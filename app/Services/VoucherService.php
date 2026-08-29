<?php

namespace App\Services;

use App\Models\GiftVoucher;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * VoucherService
 *
 * Single authoritative source of truth for all Gift Voucher business logic:
 * - Generation (idempotent, via source_order_no unique constraint)
 * - Redemption (with pessimistic row lock to prevent double-spend)
 * - Cancellation (auto-void when Order #1 is refunded)
 * - Refund Calculations (3 distinct cases)
 * - Batch Expiry Sweep (used by the vouchers:expire Artisan command)
 */
class VoucherService
{
    /**
     * Generate a Gold deferred voucher for Order #1.
     *
     * Idempotent: if a voucher already exists for this source_order_no
     * (e.g. checkout was retried), returns the existing record instead of
     * creating a duplicate or throwing an unhandled error.
     *
     * @param  int         $userId    Customer's user ID (nullable for guests)
     * @param  string|null $phone     Customer's contact number
     * @param  string      $orderNo   Order #1 reference number (source_order_no)
     * @param  int         $cardId    Membership card ID
     * @param  string      $cardName  Display name of the membership card
     * @param  int         $addedBy   Admin or system user ID
     * @return GiftVoucher
     */
    public function generateGoldVoucher(
        ?int $userId,
        ?string $phone,
        string $orderNo,
        int $cardId,
        string $cardName,
        int $addedBy = 0
    ): GiftVoucher {
        $voucherValue = (float) config('vouchers.gold_deferred_voucher_value', 2600.00);
        $validDays    = (int) config('vouchers.gold_deferred_voucher_days', 30);
        $prefix       = config('vouchers.gold_voucher_code_prefix', 'GV2600-');
        $expiresAt    = Carbon::now()->addDays($validDays);

        // ── Try to generate unique code ─────────────────────────────────────
        $code = $prefix . strtoupper(Str::random(6));
        while (GiftVoucher::where('code', $code)->exists()) {
            $code = $prefix . strtoupper(Str::random(6));
        }

        $payload = [
            'name'                  => '₹' . number_format($voucherValue, 0) . ' ' . $cardName . ' Voucher',
            'code'                  => $code,
            'voucher_value'         => $voucherValue,
            'min_cart_amount'       => 0.00,
            'start_date'            => Carbon::now()->toDateString(),
            'end_date'              => $expiresAt->toDateString(),
            'expires_at'            => $expiresAt,
            'validity_days'         => $validDays,
            'membership_scope'      => 'any_membership',
            'membership_card_id'    => $cardId,
            'allow_bogo_stacking'   => false,
            'allow_coupon_stacking' => false,
            'apply_on'              => 'all_products',
            'description'           => '₹' . number_format($voucherValue, 0) . ' Gift Voucher from Order #' . $orderNo
                . ' (' . $cardName . '). Valid for ' . $validDays . ' days.',
            'usage_limit_per_user'  => 1,
            'total_used'            => 0,
            'status'                => 'active',
            'added_by'              => $addedBy,
            'user_id'               => $userId,
            'contact_no'            => $phone,
            'source_order_no'       => $orderNo,
            'voucher_type'          => 'gold_deferred',
            'is_single_use'         => true,
        ];

        // ── Idempotency: catch unique violation on source_order_no ──────────
        try {
            return GiftVoucher::create($payload);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Duplicate — return the existing voucher for this order
            Log::info("VoucherService: Duplicate voucher creation attempt for order #{$orderNo}. Returning existing record.");
            return GiftVoucher::where('source_order_no', $orderNo)->firstOrFail();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                // MySQL unique violation fallback
                Log::info("VoucherService: Duplicate voucher (23000) for order #{$orderNo}. Returning existing.");
                return GiftVoucher::where('source_order_no', $orderNo)->firstOrFail();
            }
            throw $e;
        }
    }

    /**
     * Redeem a voucher on Order #2.
     *
     * Uses a pessimistic row lock to prevent double-spend via rapid clicks
     * or multiple concurrent requests. Re-checks status AND expiry inside
     * the lock — a voucher can cross its expiry boundary between the initial
     * read and lock acquisition.
     *
     * @param  string $code           Voucher code (GV2600-XXXXXX)
     * @param  float  $frameSubtotal  Net frame subtotal the voucher applies to
     * @param  string $orderNo        Order #2 reference number
     * @return array  { applied_discount, payable_after_discount, voucher_value, was_full_burn }
     *
     * @throws \Exception if voucher not found, already redeemed, or expired
     */
    public function redeemVoucher(string $code, float $frameSubtotal, string $orderNo): array
    {
        $code = strtoupper(trim($code));

        return DB::transaction(function () use ($code, $frameSubtotal, $orderNo) {
            // ── Lock the row for update (prevents concurrent redemptions) ───
            $voucher = GiftVoucher::where('code', $code)->lockForUpdate()->first();

            if (!$voucher) {
                throw new \Exception("Gift voucher '{$code}' not found.");
            }

            // ── Re-check status inside the lock ─────────────────────────────
            if ($voucher->status !== 'active') {
                throw new \Exception("Gift voucher '{$code}' has already been {$voucher->status}.");
            }

            // ── Re-check expiry live inside the lock ────────────────────────
            $now = Carbon::now();
            $expiresAt = $voucher->expires_at
                ? Carbon::parse($voucher->expires_at)
                : ($voucher->end_date ? Carbon::parse($voucher->end_date)->endOfDay() : null);

            if ($expiresAt && $now->greaterThan($expiresAt)) {
                // Auto-expire it now since the sweep hasn't run yet
                $voucher->update([
                    'status'     => 'expired',
                    'expired_at' => $now,
                ]);
                throw new \Exception("Gift voucher '{$code}' expired on " . $expiresAt->format('d M Y') . ".");
            }

            $voucherValue   = (float) $voucher->voucher_value;
            $appliedDiscount = min($frameSubtotal, $voucherValue);   // Burn-in-full: never carry over residual
            $payable         = max(0, $frameSubtotal - $appliedDiscount);
            $wasBurnedInFull = ($frameSubtotal <= $voucherValue);    // True: residual forfeited

            // ── Mark as redeemed ─────────────────────────────────────────────
            $voucher->update([
                'status'            => 'redeemed',
                'total_used'        => ($voucher->total_used ?? 0) + 1,
                'redeemed_order_no' => $orderNo,
                'redeemed_at'       => $now,
            ]);

            Log::info("VoucherService: Voucher {$code} redeemed on Order #{$orderNo}. Applied: ₹{$appliedDiscount}, Payable: ₹{$payable}, Burn-in-full: " . ($wasBurnedInFull ? 'YES' : 'NO'));

            return [
                'applied_discount'    => $appliedDiscount,
                'payable_after_discount' => $payable,
                'voucher_value'       => $voucherValue,
                'was_full_burn'       => $wasBurnedInFull,
                'residual_forfeited'  => max(0, $voucherValue - $appliedDiscount),
                'code'                => $code,
                'order_no'            => $orderNo,
            ];
        });
    }

    /**
     * Auto-void a deferred voucher when its source Order #1 is cancelled/refunded.
     *
     * Only voids vouchers that are still 'active' (unused).
     * If the voucher is already 'redeemed', the refund is adjusted via calculateRefund().
     *
     * @param  string $orderNo  Order #1 reference number
     * @return bool   true if a voucher was cancelled, false if none found or already used
     */
    public function cancelVoucherForOrder(string $orderNo): bool
    {
        $voucher = GiftVoucher::where('source_order_no', $orderNo)->first();

        if (!$voucher) {
            return false;
        }

        if ($voucher->status === 'active') {
            $voucher->update([
                'status'       => 'cancelled',
                'cancelled_at' => Carbon::now(),
            ]);
            Log::info("VoucherService: Voucher {$voucher->code} cancelled due to Order #{$orderNo} cancellation/refund.");
            return true;
        }

        // Already redeemed or expired — nothing to cancel, caller must adjust refund
        return false;
    }

    /**
     * Calculate the refund amount for Order #1 cancellation/return.
     *
     * Three distinct priority-ordered cases:
     *  1. Voucher is REDEEMED (used on Order #2): Refund = Paid - voucherValue
     *  2. Voucher is EXPIRED (30-day window passed, never used):
     *     - Driven by config 'vouchers.expired_voucher_refund_policy'
     *     - 'zero' → ₹0 refund (confirmed business decision)
     *     - 'full' → full refund of amount paid
     *  3. Voucher still ACTIVE (within 30-day window, unused): Full refund + void voucher
     *
     * Business logic defensively re-checks live expiry (not just status column)
     * in case the sweep job hasn't run yet for a specific voucher.
     *
     * @param  string $orderNo    Order #1 reference number
     * @param  float  $paidAmount Amount customer actually paid for Order #1
     * @param  float  $voucherValue The deferred voucher value (default ₹2,600)
     * @return array  { refund_amount, reason, policy_applied, voucher_status }
     */
    public function calculateRefund(
        string $orderNo,
        float $paidAmount,
        float $voucherValue = 2600.00
    ): array {
        $voucher = GiftVoucher::where('source_order_no', $orderNo)->first();

        if (!$voucher) {
            // No voucher was generated for this order (BOGO or instant discount path)
            return [
                'refund_amount'  => $paidAmount,
                'reason'         => 'No deferred voucher was issued for this order.',
                'policy_applied' => 'standard',
                'voucher_status' => null,
            ];
        }

        $now = Carbon::now();
        $liveStatus = $voucher->status;

        // ── Defensive live expiry re-check ──────────────────────────────────
        if ($liveStatus === 'active') {
            $expiresAt = $voucher->expires_at
                ? Carbon::parse($voucher->expires_at)
                : ($voucher->end_date ? Carbon::parse($voucher->end_date)->endOfDay() : null);

            if ($expiresAt && $now->greaterThan($expiresAt)) {
                // Voucher has expired but sweep hasn't run — treat as expired
                $liveStatus = 'expired';
                $voucher->update(['status' => 'expired', 'expired_at' => $now]);
            }
        }

        $policy = config('vouchers.expired_voucher_refund_policy', 'zero');

        // ── Case 1: Already redeemed on Order #2 ────────────────────────────
        if ($liveStatus === 'redeemed') {
            $refund = max(0, $paidAmount - $voucherValue);
            return [
                'refund_amount'   => $refund,
                'reason'          => "Voucher {$voucher->code} was redeemed on Order #{$voucher->redeemed_order_no}. Refund = Paid (₹{$paidAmount}) - Voucher Benefit (₹{$voucherValue}) = ₹{$refund}.",
                'policy_applied'  => 'redeemed_clawback',
                'voucher_status'  => 'redeemed',
            ];
        }

        // ── Case 2: Expired (30-day window lapsed, never used) ───────────────
        if ($liveStatus === 'expired') {
            if ($policy === 'zero') {
                return [
                    'refund_amount'   => 0.00,
                    'reason'          => "Voucher {$voucher->code} expired unused. Business policy: no refund on expired unused benefit window.",
                    'policy_applied'  => 'zero',
                    'voucher_status'  => 'expired',
                ];
            }
            // 'full' fallback
            return [
                'refund_amount'   => $paidAmount,
                'reason'          => "Voucher {$voucher->code} expired unused. Policy: full refund.",
                'policy_applied'  => 'full',
                'voucher_status'  => 'expired',
            ];
        }

        // ── Case 3: Active (within window, never redeemed) ───────────────────
        // Full refund + auto-cancel the voucher so it can't be used after refund
        $this->cancelVoucherForOrder($orderNo);
        return [
            'refund_amount'   => $paidAmount,
            'reason'          => "Voucher {$voucher->code} was active and unused. Full refund issued. Voucher cancelled.",
            'policy_applied'  => 'active_unused_full_refund',
            'voucher_status'  => 'cancelled',
        ];
    }

    /**
     * Calculate BOGO refund clawback amount when a customer returns the paid frame
     * but retains the free (BOGO) frame.
     *
     * Uses price snapshot at time of purchase (unit_price_at_purchase),
     * NOT live product price — the clawback must reflect what the frame
     * was worth when it was given away.
     *
     * @param  float $freeFramePriceAtPurchase  The free frame's retail price captured at order time
     * @param  float $paidFrameAmountPaid       Amount the customer actually paid for the paid frame
     * @return array { refund_amount, clawback_amount }
     */
    public function calculateBogoRefund(float $freeFramePriceAtPurchase, float $paidFrameAmountPaid): array
    {
        $clawback = $freeFramePriceAtPurchase;
        $refund   = max(0, $paidFrameAmountPaid - $clawback);

        return [
            'refund_amount'    => $refund,
            'clawback_amount'  => $clawback,
            'reason'           => "Customer retaining BOGO free frame (value ₹{$freeFramePriceAtPurchase} at purchase). Clawback applied to refund.",
        ];
    }

    /**
     * Batch expire overdue active vouchers.
     * Called by the vouchers:expire Artisan command (runs daily at 01:00).
     *
     * Queries all status='active' vouchers where expires_at < now()
     * and bulk-updates to status='expired', setting expired_at = now().
     *
     * @return int Number of vouchers expired
     */
    public function expireOverdueVouchers(): int
    {
        $now = Carbon::now();

        $count = GiftVoucher::where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNotNull('expires_at')->where('expires_at', '<', $now);
            })
            ->update([
                'status'     => 'expired',
                'expired_at' => $now,
            ]);

        if ($count > 0) {
            Log::info("VoucherService: Expired {$count} voucher(s) at {$now->toDateTimeString()}.");
        }

        return $count;
    }
}

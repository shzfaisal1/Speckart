<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class LoyaltyService
{
    /**
     * Retrieve complete dynamic loyalty points data, passbook history,
     * and VIP membership info for a given user.
     *
     * @param  User|null  $user
     * @return array
     */
    public function getUserLoyaltyData(?User $user = null): array
    {
        if (!$user) {
            $user = auth()->user();
        }

        $defaultResult = [
            'customer'        => null,
            'balance'         => 0,
            'earned'          => 0,
            'redeemed'        => 0,
            'point_value'     => $this->getPointValue(),
            'worth_in_rupees' => 0.0,
            'membership'      => null,
            'history'         => collect(),
            'orders_count'    => 0,
            'has_membership'  => false,
        ];

        if (!$user) {
            return $defaultResult;
        }

        // 1. Resolve or link customer record in tbl_customer
        $customer = $this->resolveCustomer($user);

        // 2. Point conversion rate (from tbl_loyalty id=1)
        $pointValue = $this->getPointValue();

        // 3. Reconcile & synchronize historical points if needed
        if ($customer) {
            $this->syncHistoricalSales($customer, $user);
            // Refresh customer record after potential sync
            $customer = DB::table('tbl_customer')->where('customer_id', $customer->customer_id)->first();
        }

        // 4. Calculate final balance, earned, and redeemed
        $balance  = 0;
        $earned   = 0;
        $redeemed = 0;

        if ($customer) {
            $balance  = (int) ($customer->Loyalty_Points_Bal ?? 0);
            $earned   = (int) ($customer->Loyalty_Points ?? 0);
            $redeemed = (int) ($customer->Loyalty_Points_Redeem ?? 0);
        }

        // Secondary fallback to sales if tbl_customer is still 0
        if ($balance === 0) {
            $salesSummary = $this->getSalesPointSummary($user, $customer);
            if ($salesSummary['earned'] > 0) {
                $earned   = max($earned, $salesSummary['earned']);
                $redeemed = max($redeemed, $salesSummary['used']);
                $balance  = max(0, $earned - $redeemed);
            }
        }

        // Session test override (for QA / dev preview)
        if (session()->has('test_loyalty_points')) {
            $balance = (int) session()->get('test_loyalty_points');
        }

        $worthInRupees = (float) round($balance * $pointValue, 2);

        // 5. Fetch Passbook History
        $history = collect();
        if ($customer && Schema::hasTable('tbl_loyaltyrogram_histroy')) {
            $history = $this->getPassbookHistory($customer->customer_id);
        }

        // 6. Check Active VIP Membership
        $membershipData = null;
        if ($customer && !empty($customer->membership_card_id) && !empty($customer->membership_expiry)) {
            try {
                $expiryDate = Carbon::parse($customer->membership_expiry);
                if ($expiryDate->isFuture() && Schema::hasTable('tbl_membership_card')) {
                    $card = DB::table('tbl_membership_card')->where('card_id', $customer->membership_card_id)->first();
                    $membershipData = [
                        'card_id'        => $customer->membership_card_id,
                        'name'           => $card->card_name ?? 'VIP Gold Member',
                        'expiry'         => $expiryDate->format('d M Y'),
                        'days_remaining' => (int) Carbon::now()->diffInDays($expiryDate, false),
                        'coupon_percent' => (float) ($card->coupon_percent ?? 0),
                        'enable_bogo'    => (bool) ($card->enable_bogo ?? true),
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Error parsing customer membership: ' . $e->getMessage());
            }
        }

        $ordersCount = $this->getOrdersCount($user, $customer);

        return [
            'customer'        => $customer,
            'balance'         => $balance,
            'earned'          => $earned,
            'redeemed'        => $redeemed,
            'point_value'     => $pointValue,
            'worth_in_rupees' => $worthInRupees,
            'membership'      => $membershipData,
            'history'         => $history,
            'orders_count'    => $ordersCount,
            'has_membership'  => !empty($membershipData),
        ];
    }

    /**
     * Resolve the corresponding tbl_customer record for a user
     * using phone and email matching without cross-matching foreign keys.
     *
     * @param  User  $user
     * @return object|null
     */
    public function resolveCustomer(User $user): ?object
    {
        if (!Schema::hasTable('tbl_customer')) {
            return null;
        }

        $phone = $user->phone ?? ($user->mobile ?? null);
        $cleanPhone = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;
        $last10 = $cleanPhone ? substr($cleanPhone, -10) : null;
        $email = $user->email ?? null;

        $customer = DB::table('tbl_customer')
            ->where('is_Deleted', 0)
            ->where(function ($q) use ($phone, $last10, $email) {
                $has = false;
                if ($phone) {
                    $q->orWhere('contact_no', $phone);
                    $has = true;
                }
                if ($last10 && strlen($last10) === 10) {
                    $q->orWhere('contact_no', 'LIKE', '%' . $last10);
                    $has = true;
                }
                if ($email) {
                    $q->orWhere('email_id', $email);
                    $has = true;
                }
                if (!$has) {
                    $q->whereRaw('1=0');
                }
            })
            ->orderByDesc('Loyalty_Points_Bal')
            ->orderByDesc('customer_id')
            ->first();

        // If not found, auto-provision a customer record linked to this user
        if (!$customer && ($phone || $email)) {
            try {
                $custUniqueId = 'C' . rand(100000, 999999);
                $newCustId = DB::table('tbl_customer')->insertGetId([
                    'cust_unique_id'        => $custUniqueId,
                    'cust_type'             => 'B2C',
                    'cust_name'             => $user->name ?: 'Speckart Customer',
                    'contact_no'            => $phone ?: '',
                    'email_id'              => $email ?: '',
                    'Loyalty_Points'        => 0,
                    'Loyalty_Points_Redeem' => 0,
                    'Loyalty_Points_Bal'    => 0,
                    'added_by'              => $user->id ?? 1,
                    'store_id'              => $user->store_id ?? 1,
                    'created_at'            => Carbon::now(),
                    'updated_at'            => Carbon::now(),
                ]);

                $customer = DB::table('tbl_customer')->where('customer_id', $newCustId)->first();
            } catch (\Throwable $e) {
                Log::warning('Could not auto-provision tbl_customer for user #' . $user->id . ': ' . $e->getMessage());
            }
        }

        return $customer;
    }

    /**
     * Reconcile past orders that earned loyalty points but were never credited
     * to tbl_customer or tbl_loyaltyrogram_histroy.
     *
     * @param  object  $customer
     * @param  User|null  $user
     * @return void
     */
    public function syncHistoricalSales(object $customer, ?User $user = null): void
    {
        if (!Schema::hasTable('tbl_sales') || !Schema::hasTable('tbl_loyaltyrogram_histroy')) {
            return;
        }

        $phone = $user->phone ?? ($user->mobile ?? ($customer->contact_no ?? null));
        $cleanPhone = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;
        $last10 = $cleanPhone ? substr($cleanPhone, -10) : null;
        $email = $user->email ?? ($customer->email_id ?? null);
        $custId = $customer->customer_id;

        $sales = DB::table('tbl_sales')
            ->where('is_deleted', 0)
            ->where(function ($q) use ($phone, $last10, $email, $custId, $user) {
                $has = false;
                if ($phone) {
                    $q->orWhere('contact_no', $phone);
                    $has = true;
                }
                if ($last10 && strlen($last10) === 10) {
                    $q->orWhere('contact_no', 'LIKE', '%' . $last10);
                    $has = true;
                }
                if ($email) {
                    $q->orWhere('email_id', $email);
                    $has = true;
                }
                if ($custId) {
                    $q->orWhere('cust_id', $custId);
                    $has = true;
                }
                if ($user && $user->id) {
                    $q->orWhere('user_id', $user->id);
                    $has = true;
                }
                if (!$has) {
                    $q->whereRaw('1=0');
                }
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $runningBal   = (float) ($customer->Loyalty_Points_Bal ?? 0);
        $totalEarned  = (float) ($customer->Loyalty_Points ?? 0);
        $totalRedeemed= (float) ($customer->Loyalty_Points_Redeem ?? 0);
        $needsUpdate  = false;

        foreach ($sales as $sale) {
            $pts = (float) ($sale->earnedPoints ?? 0);
            if ($pts > 0) {
                $exists = DB::table('tbl_loyaltyrogram_histroy')
                    ->where('customer_id', $custId)
                    ->where('description', 'LIKE', '%' . $sale->order_no . '%')
                    ->where('add_remove', 1)
                    ->exists();

                if (!$exists) {
                    $openPts = $runningBal;
                    $runningBal += $pts;
                    $totalEarned += $pts;

                    DB::table('tbl_loyaltyrogram_histroy')->insert([
                        'customer_id'    => $custId,
                        'opening_points' => $openPts,
                        'redeem'         => $pts,
                        'bal_point'      => $runningBal,
                        'description'    => 'Earned on Order ' . $sale->order_no,
                        'add_remove'     => 1, // Credit
                        'store_id'       => $sale->store_id ?? 1,
                        'added_by'       => $sale->added_by ?? ($user->id ?? 1),
                        'created_at'     => $sale->created_at ?: Carbon::now(),
                        'updated_at'     => Carbon::now(),
                    ]);

                    $needsUpdate = true;
                }
            }

            // Also check if loyalty points were redeemed in this order
            $usedPts = (float) ($sale->loyalty_point_amount ?? 0);
            if ($usedPts > 0) {
                $usedExists = DB::table('tbl_loyaltyrogram_histroy')
                    ->where('customer_id', $custId)
                    ->where('description', 'LIKE', '%' . $sale->order_no . '%')
                    ->where('add_remove', 2)
                    ->exists();

                if (!$usedExists) {
                    $openPts = $runningBal;
                    $runningBal = max(0, $runningBal - $usedPts);
                    $totalRedeemed += $usedPts;

                    DB::table('tbl_loyaltyrogram_histroy')->insert([
                        'customer_id'    => $custId,
                        'opening_points' => $openPts,
                        'redeem'         => $usedPts,
                        'bal_point'      => $runningBal,
                        'description'    => 'Redeemed on Order ' . $sale->order_no,
                        'add_remove'     => 2, // Debit
                        'store_id'       => $sale->store_id ?? 1,
                        'added_by'       => $sale->added_by ?? ($user->id ?? 1),
                        'created_at'     => $sale->created_at ?: Carbon::now(),
                        'updated_at'     => Carbon::now(),
                    ]);

                    $needsUpdate = true;
                }
            }
        }

        if ($needsUpdate || ($runningBal > (float)($customer->Loyalty_Points_Bal ?? 0))) {
            DB::table('tbl_customer')
                ->where('customer_id', $custId)
                ->update([
                    'Loyalty_Points'        => $totalEarned,
                    'Loyalty_Points_Redeem' => $totalRedeemed,
                    'Loyalty_Points_Bal'    => $runningBal,
                    'updated_at'            => Carbon::now(),
                ]);
        }
    }

    /**
     * Get passbook transactions for a customer.
     *
     * @param  int  $customerId
     * @param  int  $limit
     * @return \Illuminate\Support\Collection
     */
    public function getPassbookHistory(int $customerId, int $limit = 50): \Illuminate\Support\Collection
    {
        return DB::table('tbl_loyaltyrogram_histroy')
            ->where('customer_id', $customerId)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item->formatted_date = Carbon::parse($item->created_at)->format('d M Y, h:i A');
                $item->is_credit = ((int) $item->add_remove === 1);
                $item->points_display = ($item->is_credit ? '+' : '-') . number_format($item->redeem);
                return $item;
            });
    }

    /**
     * Sum earned and used points directly from tbl_sales.
     *
     * @param  User  $user
     * @param  object|null  $customer
     * @return array
     */
    protected function getSalesPointSummary(User $user, ?object $customer): array
    {
        if (!Schema::hasTable('tbl_sales')) {
            return ['earned' => 0, 'used' => 0];
        }

        $phone = $user->phone ?? ($user->mobile ?? ($customer->contact_no ?? null));
        $cleanPhone = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;
        $last10 = $cleanPhone ? substr($cleanPhone, -10) : null;
        $email = $user->email ?? ($customer->email_id ?? null);
        $custId = $customer ? $customer->customer_id : null;

        $q = DB::table('tbl_sales')
            ->where('is_deleted', 0)
            ->where(function ($sub) use ($phone, $last10, $email, $custId, $user) {
                $has = false;
                if ($phone) {
                    $sub->orWhere('contact_no', $phone);
                    $has = true;
                }
                if ($last10 && strlen($last10) === 10) {
                    $sub->orWhere('contact_no', 'LIKE', '%' . $last10);
                    $has = true;
                }
                if ($email) {
                    $sub->orWhere('email_id', $email);
                    $has = true;
                }
                if ($custId) {
                    $sub->orWhere('cust_id', $custId);
                    $has = true;
                }
                if ($user && $user->id) {
                    $sub->orWhere('user_id', $user->id);
                    $has = true;
                }
                if (!$has) {
                    $sub->whereRaw('1=0');
                }
            });

        $earned = (int) (clone $q)->sum('earnedPoints');
        $used   = (int) (clone $q)->sum('loyalty_point_amount');

        return ['earned' => $earned, 'used' => $used];
    }

    /**
     * Total count of orders placed by this user/customer.
     *
     * @param  User  $user
     * @param  object|null  $customer
     * @return int
     */
    protected function getOrdersCount(User $user, ?object $customer): int
    {
        if (!Schema::hasTable('tbl_sales')) {
            return 0;
        }

        $phone = $user->phone ?? ($user->mobile ?? ($customer->contact_no ?? null));
        $cleanPhone = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;
        $last10 = $cleanPhone ? substr($cleanPhone, -10) : null;
        $email = $user->email ?? ($customer->email_id ?? null);
        $custId = $customer ? $customer->customer_id : null;

        return DB::table('tbl_sales')
            ->where('is_deleted', 0)
            ->where(function ($sub) use ($phone, $last10, $email, $custId, $user) {
                $has = false;
                if ($phone) {
                    $sub->orWhere('contact_no', $phone);
                    $has = true;
                }
                if ($last10 && strlen($last10) === 10) {
                    $sub->orWhere('contact_no', 'LIKE', '%' . $last10);
                    $has = true;
                }
                if ($email) {
                    $sub->orWhere('email_id', $email);
                    $has = true;
                }
                if ($custId) {
                    $sub->orWhere('cust_id', $custId);
                    $has = true;
                }
                if ($user && $user->id) {
                    $sub->orWhere('user_id', $user->id);
                    $has = true;
                }
                if (!$has) {
                    $sub->whereRaw('1=0');
                }
            })
            ->count();
    }

    /**
     * Get conversion rate (e.g. 1 Point = ₹2.00).
     *
     * @return float
     */
    public function getPointValue(): float
    {
        if (!Schema::hasTable('tbl_loyalty')) {
            return 2.0;
        }

        $config = DB::table('tbl_loyalty')->where('id', 1)->first();
        if ($config && !empty($config->one_point_redem) && (float) $config->one_point_redem > 0) {
            return (float) $config->one_point_redem;
        }

        return 2.0;
    }
}


<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\sale\Sale;
use App\Models\sale\SalePayment;
use App\Models\sale\SaleProduct;
use App\Models\GiftVoucher;
use App\Services\CartService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $voucherService;

    public function __construct(CartService $cartService, VoucherService $voucherService)
    {
        $this->cartService    = $cartService;
        $this->voucherService = $voucherService;
    }

    /**
     * Complete Order Checkout
     * - Creates Sale, SaleProduct, SalePayment records
     * - Activates membership if purchased
     * - Clears all checkout session data
     */
    public function completeCheckout(Request $request)
    {
        // 1. Validate payment method
        $request->validate([
            'payment_method'  => 'required|string|in:cod,online,upi,card,netbanking',
            'customer_note'   => 'nullable|string|max:500',
        ]);

        $paymentMethod = $request->input('payment_method', 'cod');

        // 2. Get shipping address from session
        $shipping = session()->get('checkout_shipping', null);
        if (!$shipping) {
            return redirect()->route('shipping-details')->with('error', 'Please fill in your shipping address.');
        }

        // Pincode serviceability & COD validation
        $pincodeCheck = \App\Models\ShippingCharge::getChargeForPincode($shipping['pincode'] ?? null);
        if (!$pincodeCheck['is_serviceable']) {
            return redirect()->route('shipping-details')->with('error', $pincodeCheck['message']);
        }
        if (strtolower((string)$paymentMethod) === 'cod' && empty($pincodeCheck['is_cod_available'])) {
            return redirect()->route('payment')->with('error', 'Cash on Delivery is not available for your delivery pincode. Please choose an online payment method.');
        }

        // 3. Get cart calculations
        $appliedCoupon = session()->get('applied_coupon', null);
        $cartData = $this->cartService->getCartCalculations($appliedCoupon);

        if (empty($cartData['items'])) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        // 4. Get authenticated user & resolve/create customer in tbl_customer
        $user = auth()->user();

        // 5. Generate Order Number from store settings
        $store = DB::table('tbl_store')->where('id', 1)->first() ?? DB::table('tbl_store')->first();
        $storeDbId = $store->id ?? 1;

        $orderPrefix = $store->order_no_prefix ?? 'WEB';
        $nextOrderNo = (int)($store->next_order_no ?? 1);
        $orderNo = $orderPrefix . str_pad($nextOrderNo, 5, '0', STR_PAD_LEFT);

        // Increment next_order_no in store using integer primary key 'id'
        if ($store) {
            DB::table('tbl_store')
                ->where('id', $storeDbId)
                ->update(['next_order_no' => $nextOrderNo + 1]);
        }

        // Resolve or create customer in tbl_customer
        $customer = DB::table('tbl_customer')
            ->where('contact_no', $shipping['phone'])
            ->first();

        if (!$customer && $user && !empty($user->phone)) {
            $customer = DB::table('tbl_customer')
                ->where('contact_no', $user->phone)
                ->first();
        }

        $customerEmail = !empty($shipping['email']) ? $shipping['email'] : ($user->email ?? ($customer->email_id ?? ''));

        if (!$customer) {
            $custUniqueId = (string) rand(100000, 999999);
            $customerDbId = DB::table('tbl_customer')->insertGetId([
                'cust_unique_id' => $custUniqueId,
                'cust_type'      => 'B2C',
                'cust_name'      => $shipping['full_name'],
                'contact_no'     => $shipping['phone'],
                'email_id'       => $customerEmail,
                'cust_address'   => $shipping['full_address'],
                'pincode'        => $shipping['pincode'],
                'added_by'       => $user->id ?? 1,
                'store_id'       => $storeDbId,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]);
            $custId = $customerDbId;
        } else {
            $custId = $customer->customer_id ?? $customer->id;
        }

        $addedBy = $user->id ?? $custId;

        // 6. Calculate totals from CartService data
        $frameSubtotal  = (float)($cartData['frame_subtotal'] ?? 0);
        $lensSubtotal   = (float)($cartData['lens_subtotal'] ?? 0);
        $totalItemPrice = $frameSubtotal + $lensSubtotal;
        $bogoDiscount   = (float)($cartData['bogo_savings'] ?? 0) + (float)($cartData['third_item_savings'] ?? 0);
        $couponAmount   = (float)($cartData['coupon_discount'] ?? 0);
        $loyaltyAmount  = (float)($cartData['loyalty_discount'] ?? 0);
        $totalDiscount  = $bogoDiscount + $couponAmount + (float)($cartData['first_frame_free_save'] ?? 0);
        $grandTotal     = (float)($cartData['grand_total'] ?? 0);

        // Payment amounts based on method
        $payAmount     = ($paymentMethod === 'cod') ? 0 : $grandTotal;
        $pendingAmount = ($paymentMethod === 'cod') ? $grandTotal : 0;

        // Check if any cart item has prescription
        $hasAnyRx = false;
        foreach ($cartData['items'] as $chkItem) {
            if (!empty($chkItem['prescription_data'])) {
                $hasAnyRx = true;
                break;
            }
        }

        // ── 7. All DB operations wrapped in a transaction for data integrity ──
        try {
            DB::beginTransaction();

            // Create Sale record (for In-Store POS & Backwards Compatibility & B2C Order)
            $saleData = [
                'sale_date'           => Carbon::now()->toDateString(),
                'order_no'            => $orderNo,
                'cust_name'           => $shipping['full_name'],
                'contact_no'          => $shipping['phone'],
                'email_id'            => $customerEmail,
                'cust_address'        => $shipping['full_address'],
                'state_id'            => $store->state_id ?? 0,
                'city_id'             => $store->city_id ?? 0,
                'pincode'             => $shipping['pincode'],
                'total_basic_amount'  => max(0, $totalItemPrice - $totalDiscount),
                'total_gst_amount'    => 0,
                'fitting_fee'         => 0,
                'total_item_price'    => $totalItemPrice,
                'total_discount'      => $totalDiscount,
                'bogo_discount'       => $bogoDiscount,
                'shipping_fee'        => (float)($cartData['shipping_charge'] ?? 0.00),
                'coupon_amount'       => $couponAmount,
                'loyalty_point_amount'=> $loyaltyAmount,
                'loyalty_point_apply' => ($loyaltyAmount > 0) ? (int)($cartData['points_used'] ?? 0) : 0,
                'total_payable'       => $grandTotal,
                'pay_amount'          => $payAmount,
                'pending_amount'      => $pendingAmount,
                'pay_method'          => strtoupper($paymentMethod),
                'sales_type'          => 0, // 0 = B2C Website Order
                'sales_status'        => 0, // 0 = Pending Website Order
                'tax_rule'            => $store->tax_rule ?? 1,
                'store_id'            => $storeDbId,
                'cust_id'             => $custId,
                'added_by'            => $addedBy,
                'earnedPoints'        => (int)($cartData['order_reward_pts'] ?? 0),
                'deletordercomment'   => $request->input('customer_note'),
                'created_at'          => Carbon::now(),
                'updated_at'          => Carbon::now(),
            ];

            // Add coupon_id if a coupon was applied
            if ($appliedCoupon && !empty($appliedCoupon['code'])) {
                $saleData['earncoupon'] = $appliedCoupon['code'];
            }

            // Add membership_id if membership is in cart
            $membershipPurchased = session()->get('cart_membership', null);
            if ($membershipPurchased) {
                $saleData['membership_id'] = $membershipPurchased['card_id'];
            }

            $sale = Sale::create($saleData);
            $saleId = $sale->sale_id ?? $sale->id;

            // Create SaleProduct records for each cart item
            foreach ($cartData['items'] as $key => $item) {
                if (!empty($item['is_membership'])) {
                    SaleProduct::create([
                        'sale_id'          => $saleId,
                        'order_no'         => $orderNo,
                        'product_deatils'  => $item['frame_name'] ?? 'Membership Card',
                        'product_code'     => 'MEMBERSHIP',
                        'product_type'     => 'membership',
                        'qty'              => 1,
                        'retail_price'     => (float)($item['frame_price'] ?? 0),
                        'sale_price'       => (float)($item['frame_price'] ?? 0),
                        'store_id'         => $storeDbId,
                        'product_id'       => $item['frame_id'] ?? null,
                    ]);
                    continue;
                }

                $qty = (int)($item['quantity'] ?? 1);
                $framePrice = (float)($item['frame_price'] ?? 0);
                $lensPrice  = (float)($item['lens_price'] ?? 0);

                $itemDiscount = (float)($item['item_bogo_discount'] ?? 0);
                if (!empty($item['is_first_frame_free_applied'])) {
                    $itemDiscount += ($framePrice * $qty);
                }

                $totalLinePayable = max(0, ($framePrice * $qty) - $itemDiscount) + ($lensPrice * $qty);
                $salePrice = $qty > 0 ? ($totalLinePayable / $qty) : $totalLinePayable;

                $prescriptionNotes = null;
                $rx = null;
                if (!empty($item['prescription_data'])) {
                    $rx = is_string($item['prescription_data']) ? json_decode($item['prescription_data'], true) : $item['prescription_data'];
                    $prescriptionNotes = is_string($item['prescription_data']) ? $item['prescription_data'] : json_encode($item['prescription_data']);
                }

                SaleProduct::create([
                    'sale_id'            => $saleId,
                    'order_no'           => $orderNo,
                    'product_deatils'    => ($item['frame_name'] ?? 'Product') . ' + ' . ($item['lens_name'] ?? 'Basic Lens'),
                    'product_discount'   => $itemDiscount,
                    'product_code'       => $item['frame_code'] ?? null,
                    'product_type'       => $item['product_type'] ?? 'frame',
                    'qty'                => $qty,
                    'retail_price'       => $framePrice + $lensPrice,
                    'sale_price'         => $salePrice,
                    'discount_amt'       => $itemDiscount,
                    'store_id'           => $storeDbId,
                    'product_id'         => $item['frame_id'] ?? null,
                    'package_id'         => $item['lens_package_id'] ?? null,
                    'product_company'    => $item['brand'] ?? null,
                    'product_size'       => $item['size'] ?? null,
                    'prescription_notes' => $prescriptionNotes,
                    'product_typesss'    => $item['lens_name'] ?? ($item['lens_type'] ?? null),
                    'product_coating'    => $item['lens_coating'] ?? null,
                    'product_index'      => $item['lens_index'] ?? null,
                    'frame_fh'           => is_array($rx) ? ($rx['fitting_height'] ?? ($rx['fh'] ?? null)) : null,
                    'GL_EYE_RS_D'        => is_array($rx) ? ($rx['GL_EYE_RS_D'] ?? ($rx['re_sph'] ?? null)) : null,
                    'GL_EYE_RC_D'        => is_array($rx) ? ($rx['GL_EYE_RC_D'] ?? ($rx['re_cyl'] ?? null)) : null,
                    'GL_EYE_RA_D'        => is_array($rx) ? ($rx['GL_EYE_RA_D'] ?? ($rx['re_axis'] ?? null)) : null,
                    'GL_EYE_RADD'        => is_array($rx) ? ($rx['GL_EYE_RADD'] ?? ($rx['re_add'] ?? null)) : null,
                    'GL_EYE_LS_D'        => is_array($rx) ? ($rx['GL_EYE_LS_D'] ?? ($rx['le_sph'] ?? null)) : null,
                    'GL_EYE_LC_D'        => is_array($rx) ? ($rx['GL_EYE_LC_D'] ?? ($rx['le_cyl'] ?? null)) : null,
                    'GL_EYE_LA_D'        => is_array($rx) ? ($rx['GL_EYE_LA_D'] ?? ($rx['le_axis'] ?? null)) : null,
                    'GL_EYE_LADD'        => is_array($rx) ? ($rx['GL_EYE_LADD'] ?? ($rx['le_add'] ?? null)) : null,
                    'GL_EYE_totalPD'     => is_array($rx) ? ($rx['GL_EYE_totalPD'] ?? ($rx['pd'] ?? null)) : null,
                ]);
            }

            // Create SalePayment record
            SalePayment::create([
                'sale_id'     => $saleId,
                'order_no'    => $orderNo,
                'total_price' => $grandTotal,
                'pay_amount'  => $payAmount,
                'bal_amount'  => $pendingAmount,
                'pay_method'  => strtoupper($paymentMethod),
                'pay_type'    => 0, // 0 = Initial Sale Payment
                'pay_date'    => Carbon::now()->toDateString(),
                'store_id'    => $storeDbId,
                'added_by'    => $addedBy,
                'sales_type'  => 0,
            ]);

            // 10. Activate Membership if purchased
            if ($membershipPurchased && $custId) {
                $validityDays = $membershipPurchased['validity_days'] ?? 730;
                DB::table('tbl_customer')
                    ->where('customer_id', $custId)
                    ->update([
                        'membership_card_id' => $membershipPurchased['card_id'],
                        'membership_expiry'  => Carbon::now()->addDays($validityDays)->toDateString(),
                    ]);

                session()->put('active_membership', [
                    'card_id'   => $membershipPurchased['card_id'],
                    'card_name' => $membershipPurchased['card_name'],
                    'expiry'    => Carbon::now()->addDays($validityDays)->toDateString(),
                ]);
            }

            // 11. Deduct Loyalty Points if used & log passbook history
            if ($loyaltyAmount > 0 && $custId) {
                $pointsUsed = (float)($cartData['points_used'] ?? 0);
                $customerRecord = DB::table('tbl_customer')->where('customer_id', $custId)->first();
                if ($customerRecord) {
                    $openingPoints = (float)($customerRecord->Loyalty_Points_Bal ?? 0);
                    $closingPoints = max(0, $openingPoints - $pointsUsed);

                    DB::table('tbl_loyaltyrogram_histroy')->insert([
                        'customer_id'    => $custId,
                        'opening_points' => $openingPoints,
                        'redeem'         => $pointsUsed,
                        'bal_point'      => $closingPoints,
                        'description'    => 'Redeemed on Order ' . $orderNo,
                        'add_remove'     => 2, // 2 = Remove/Spent
                        'store_id'       => $storeDbId,
                        'added_by'       => $addedBy,
                        'created_at'     => Carbon::now(),
                        'updated_at'     => Carbon::now(),
                    ]);

                    DB::table('tbl_customer')
                        ->where('customer_id', $custId)
                        ->update([
                            'Loyalty_Points_Redeem' => ($customerRecord->Loyalty_Points_Redeem ?? 0) + $pointsUsed,
                            'Loyalty_Points_Bal'    => $closingPoints,
                            'updated_at'            => Carbon::now(),
                        ]);
                } else {
                    DB::table('tbl_customer')
                        ->where('customer_id', $custId)
                        ->decrement('Loyalty_Points_Bal', $pointsUsed);
                }
            }

            // 12. Redeem / Burn Applied Gift Voucher via VoucherService (lockForUpdate + live expiry check)
            $appliedVoucher = session()->get('applied_voucher', null);
            $appliedCoupon  = session()->get('applied_coupon', null);
            $voucherCodeToBurn = $appliedVoucher['code'] ?? (!empty($appliedCoupon['is_gift_voucher']) ? ($appliedCoupon['code'] ?? null) : null);

            if (!empty($voucherCodeToBurn)) {
                $cleanCode = strtoupper(trim($voucherCodeToBurn));
                $dbVoucherCheck = GiftVoucher::where('code', $cleanCode)->first();

                if ($dbVoucherCheck) {
                    // Delegate to VoucherService — handles lockForUpdate + live expiry re-check
                    $this->voucherService->redeemVoucher(
                        $cleanCode,
                        (float)($cartData['frame_subtotal'] ?? 0),
                        $orderNo
                    );
                } else {
                    // Legacy offers table voucher (no lockForUpdate needed — not customer-bound)
                    DB::table('offers')
                        ->where('offer_type', 'gift_voucher')
                        ->where('coupon_code', $cleanCode)
                        ->increment('total_used', 1);
                }
            }

            // 13. Auto-Generate Deferred Gift Voucher via VoucherService (idempotent via source_order_no unique)
            // Triggered: Gold member + >= 1 frame + no BOGO + not redeeming a deferred voucher in this same order
            $isGoldMember = !empty($membershipPurchased)
                || ($customer && !empty($customer->membership_card_id) && !empty($customer->membership_expiry) && Carbon::parse($customer->membership_expiry)->isFuture())
                || session()->has('active_membership');

            $appliedVoucherType  = $appliedVoucher['voucher_type'] ?? '';
            $redeemedGoldVoucher = ($appliedVoucherType === 'gold_deferred');

            $eligibleFrameCount = 0;
            foreach ($cartData['items'] as $chkItem) {
                if (empty($chkItem['is_membership']) && !empty($chkItem['is_bogo_eligible'])) {
                    $eligibleFrameCount += (int)($chkItem['quantity'] ?? 1);
                }
            }

            $generatedVoucher = null;
            if ($isGoldMember && $eligibleFrameCount >= 1 && $bogoDiscount <= 0 && !$redeemedGoldVoucher) {
                $cardId   = $membershipPurchased['card_id'] ?? ($customer->membership_card_id ?? 1);
                $cardDb   = DB::table('tbl_membership_card')->where('card_id', $cardId)->first();
                $cardName = $cardDb->card_name ?? 'Gold Max Benefit';

                $generatedVoucher = $this->voucherService->generateGoldVoucher(
                    userId:   $custId,
                    phone:    $shipping['phone'] ?? ($customer->contact_no ?? null),
                    orderNo:  $orderNo,
                    cardId:   $cardId,
                    cardName: $cardName,
                    addedBy:  $addedBy
                );
            }

            DB::commit();

            // 14. Completely clear cart from session AND database (inside try, after commit)
            $this->cartService->clearCart();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout failed for user ' . ($user->id ?? 'guest') . ': ' . $e->getMessage());
            return redirect()->route('cart')->with('error', 'Something went wrong while placing your order. Please try again.');
        }

        $successMessage = 'Order ' . $orderNo . ' placed successfully!';
        if ($generatedVoucher) {
            $successMessage .= ' 🎉 Your ₹' . number_format($generatedVoucher->voucher_value, 0) . ' Gold Benefit Gift Voucher (' . $generatedVoucher->code . ') is now active in My Account!';
        } elseif ($membershipPurchased) {
            $successMessage .= ' Your ' . $membershipPurchased['card_name'] . ' is now active!';
        }
        if ($paymentMethod === 'cod') {
            $successMessage .= ' Payment will be collected on delivery.';
        }

        // Clear checkout_shipping after building the success message
        // (preserved during clearCart so guest users can see their order on my-orders)
        session()->forget('checkout_shipping');

        return redirect()->route('my-orders')->with('success', $successMessage);
    }
}

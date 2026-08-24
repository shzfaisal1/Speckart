<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\sale\Sale;
use App\Models\sale\SalePayment;
use App\Models\sale\SaleProduct;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
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

        if (!$customer) {
            $custUniqueId = (string) rand(100000, 999999);
            $customerDbId = DB::table('tbl_customer')->insertGetId([
                'cust_unique_id' => $custUniqueId,
                'cust_type'      => 'B2C',
                'cust_name'      => $shipping['full_name'],
                'contact_no'     => $shipping['phone'],
                'email_id'       => $shipping['email'] ?? null,
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
                'email_id'            => $shipping['email'] ?? null,
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

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout failed for user ' . ($user->id ?? 'guest') . ': ' . $e->getMessage());
            return redirect()->route('cart')->with('error', 'Something went wrong while placing your order. Please try again.');
        }

        // 12. Clear all checkout session data
        session()->forget([
            'cart',
            'applied_coupon',
            'applied_voucher',
            'cart_membership',
            'checkout_shipping',
            'use_loyalty_points',
            'free_item_selected',
        ]);

        $successMessage = 'Order ' . $orderNo . ' placed successfully!';
        if ($membershipPurchased) {
            $successMessage .= ' Your ' . $membershipPurchased['card_name'] . ' is now active!';
        }
        if ($paymentMethod === 'cod') {
            $successMessage .= ' Payment will be collected on delivery.';
        }

        return redirect()->route('my-orders')->with('success', $successMessage);
    }
}

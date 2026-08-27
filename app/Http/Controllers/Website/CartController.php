<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display Shopping Cart
     */
    public function shopping_cart(Request $request)
    {
        $appliedCoupon = session()->get('applied_coupon', null);
        $cartData = $this->cartService->getCartCalculations($appliedCoupon);

        return view('website.cart.shopping-cart', compact('cartData'));
    }

    /**
     * Add Frame + Selected Lens Package to Cart via AJAX
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'frame_id'          => 'required',
            'lens_package_id'   => 'nullable',
            'quantity'          => 'nullable|integer|min:1',
            'size'              => 'nullable|string|max:50',
            'lens_type'         => 'nullable|string|max:50',
            'prescription_data' => 'nullable',
            'prescription_file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:10240',
        ]);

        $frameId          = $request->input('frame_id');
        $lensPackageId    = $request->input('lens_package_id');
        $quantity         = $request->input('quantity', 1);
        $size             = $request->input('size');             // FIX: capture selected frame size
        $lensType         = $request->input('lens_type');        // FIX: pass lens type context to CartService
        $prescriptionData = $request->input('prescription_data');

        if ($request->hasFile('prescription_file')) {
            $file = $request->file('prescription_file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move(public_path('uploads/prescriptions'), $fileName);
            $prescriptionData = json_encode([
                'type'      => 'upload',
                'file'      => 'uploads/prescriptions/' . $fileName,
                'file_name' => $file->getClientOriginalName(),
            ]);
        }

        $result = $this->cartService->addToCart($frameId, $lensPackageId, $quantity, $prescriptionData, $lensType, $size);

        if ($result['status']) {
            // Automatically remove item from wishlist if user is authenticated
            if (Auth::check()) {
                $product = \App\Models\product\Product::where('id', $frameId)
                    ->orWhere('product_id', $frameId)
                    ->first();

                $targetProductIds = [(string) $frameId];
                if ($product) {
                    if (!empty($product->id)) {
                        $targetProductIds[] = (string) $product->id;
                    }
                    if (!empty($product->product_id)) {
                        $targetProductIds[] = (string) $product->product_id;
                    }
                    if (!empty($product->parent_product_code)) {
                        $siblingIds = \App\Models\product\Product::where('parent_product_code', $product->parent_product_code)
                            ->pluck('product_id')
                            ->merge(\App\Models\product\Product::where('parent_product_code', $product->parent_product_code)->pluck('id'))
                            ->filter()
                            ->map(fn($id) => (string) $id)
                            ->toArray();
                        $targetProductIds = array_merge($targetProductIds, $siblingIds);
                    }
                }
                $targetProductIds = array_unique(array_filter($targetProductIds));

                \App\Models\Wishlist::where('user_id', Auth::id())
                    ->whereIn('product_id', $targetProductIds)
                    ->delete();
            }

            $wishlistCount = Auth::check() 
                ? \App\Models\Wishlist::where('user_id', Auth::id())->count() 
                : 0;

            return response()->json([
                'status'         => 'success',
                'message'        => $result['message'],
                'cart_count'     => $result['cart_count'],
                'wishlist_count' => $wishlistCount,
                'redirect'       => route('cart')
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => $result['message']
        ], 400);
    }

    /**
     * Update Quantity
     */
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer',
        ]);

        $updated = $this->cartService->updateQuantity($request->cart_key, $request->quantity);

        if ($updated) {
            $appliedCoupon = session()->get('applied_coupon', null);
            $cartData = $this->cartService->getCartCalculations($appliedCoupon);

            return response()->json([
                'status'   => 'success',
                'message'  => 'Cart updated successfully!',
                'cartData' => $cartData
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Cart item not found.'], 404);
    }

    /**
     * Remove Item
     */
    public function removeItem(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
        ]);

        $removed = $this->cartService->removeItem($request->cart_key);

        if ($removed) {
            $appliedCoupon = session()->get('applied_coupon', null);
            $cartData = $this->cartService->getCartCalculations($appliedCoupon);

            return response()->json([
                'status'   => 'success',
                'message'  => 'Item removed from cart.',
                'cartData' => $cartData
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Cart item not found.'], 404);
    }

    /**
     * Apply Coupon Code dynamically from DB or fallback
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->coupon_code));
        $now  = \Carbon\Carbon::now()->toDateString();

        // 1. Search offers table
        $offer = DB::table('offers')
            ->where('coupon_code', $code)
            ->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->first();

        $couponData = null;

        if ($offer) {
            $discType = $offer->discount_type ?? 'percentage';
            $discVal  = (float) $offer->discount_value;

            // Support gift voucher offer type dynamically
            if ($offer->offer_type === 'gift_voucher' && !empty($offer->voucher_value)) {
                $discType = 'fixed';
                $discVal  = (float) $offer->voucher_value;
            }

            $couponData = [
                'code'                  => strtoupper($offer->coupon_code),
                'discount_type'         => $discType,
                'discount_value'        => $discVal,
                'discount_percent'      => ($discType === 'fixed') ? 0 : $discVal,
                'discount_amount'       => ($discType === 'fixed') ? $discVal : 0,
                'min_cart_amount'       => (float) ($offer->min_cart_amount ?? 0),
                'max_discount'          => (float) ($offer->max_discount ?? 0),
                'description'           => $offer->description ?? $offer->name,
                'is_gift_voucher'       => ($offer->offer_type === 'gift_voucher'),
                'voucher_validity_days' => (int) ($offer->voucher_validity_days ?? 30),
            ];
        } elseif (\Illuminate\Support\Facades\Schema::hasTable('coupons')) {
            // 2. Search coupons table
            $c = DB::table('coupons')
                ->where('code', $code)
                ->where('is_active', 1)
                ->first();

            if ($c) {
                $couponData = [
                    'code'             => strtoupper($c->code),
                    'discount_type'    => $c->discount_type ?? 'percentage',
                    'discount_value'   => (float) $c->discount_value,
                    'discount_percent' => ($c->discount_type === 'fixed') ? 0 : (float) $c->discount_value,
                    'discount_amount'  => ($c->discount_type === 'fixed') ? (float) $c->discount_value : 0,
                    'min_cart_amount'  => (float) ($c->min_order_value ?? 0),
                    'max_discount'     => (float) ($c->max_discount_amount ?? 0),
                    'description'      => $c->description ?? '',
                ];
            }
        } elseif (\Illuminate\Support\Facades\Schema::hasTable('tbl_coupon')) {
            // 3. Search tbl_coupon table
            $tc = DB::table('tbl_coupon')
                ->where('coupon_code', $code)
                ->first();

            if ($tc) {
                $discPct = (float) ($tc->discount_percent ?? $tc->percent ?? 0);
                $discAmt = (float) ($tc->discount_amount ?? $tc->amount ?? 0);
                $couponData = [
                    'code'             => strtoupper($tc->coupon_code),
                    'discount_type'    => ($discPct > 0) ? 'percentage' : 'fixed',
                    'discount_value'   => ($discPct > 0) ? $discPct : $discAmt,
                    'discount_percent' => $discPct,
                    'discount_amount'  => $discAmt,
                    'min_cart_amount'  => (float) ($tc->min_cart_amount ?? 0),
                    'max_discount'     => 0,
                    'description'      => 'Promo Coupon ' . $code,
                ];
            }
        }

        // 4. Fallback SINGLE coupon
        if (!$couponData && $code === 'SINGLE') {
            $couponData = [
                'code'             => 'SINGLE',
                'discount_type'    => 'percentage',
                'discount_value'   => 25,
                'discount_percent' => 25,
                'discount_amount'  => 0,
                'min_cart_amount'  => 0,
                'max_discount'     => 0,
                'description'      => '25% off frame price',
            ];
        }

        if (!$couponData) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired coupon code.'], 400);
        }

        // Check minimum cart subtotal requirement
        $currentCalc = $this->cartService->getCartCalculations();
        $subtotal = $currentCalc['frame_subtotal'] ?? 0;
        if (!empty($couponData['min_cart_amount']) && $subtotal < $couponData['min_cart_amount']) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Minimum cart subtotal of ₹' . number_format($couponData['min_cart_amount']) . ' is required for coupon ' . $code . '.'
            ], 400);
        }

        session()->put('applied_coupon', $couponData);
        $cartData = $this->cartService->getCartCalculations($couponData);

        return response()->json([
            'status'   => 'success',
            'message'  => "Coupon '{$code}' applied successfully!",
            'cartData' => $cartData
        ]);
    }

    /**
     * Remove Applied Coupon Code
     */
    public function removeCoupon()
    {
        session()->forget('applied_coupon');
        $cartData = $this->cartService->getCartCalculations(null);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Coupon removed successfully.',
            'cartData' => $cartData
        ]);
    }

    /**
     * Add Membership Card to Cart session
     */
    public function addMembershipToCart(Request $request)
    {
        $cardId = $request->input('card_id');
        $card = null;
        if ($cardId) {
            $card = DB::table('tbl_membership_card')
                ->where('card_id', $cardId)
                ->first();
        }

        if (!$card) {
            $card = DB::table('tbl_membership_card')->where('flag', 0)->first();
        }

        if (!$card) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Membership plan not found.'], 404);
            }
            return redirect()->back()->with('error', 'Membership plan not found.');
        }

        // Store membership in session
        session()->put('cart_membership', [
            'card_id'               => $card->card_id ?? ($card->id ?? 1),
            'card_name'             => $card->card_name ?? 'Gold VIP Membership',
            'price'                 => (float) ($card->price ?? 600),
            'validity_days'         => (int) ($card->validity_days ?? 365),
            'loyalty_earn_first'    => (float) ($card->loyalty_earn_first ?? 0),
            'loyalty_earn_repeat'   => (float) ($card->loyalty_earn_repeat ?? 0),
            'loyalty_use_percent'   => (float) ($card->loyalty_use_percent ?? 0),
            'coupon_percent'        => (float) ($card->coupon_percent ?? 0),
            'voucher_validity_days' => (int) ($card->voucher_validity_days ?? 365),
            'enable_bogo'           => (int) ($card->enable_bogo ?? 1),
        ]);

        if ($request->expectsJson()) {
            $appliedCoupon = session()->get('applied_coupon', null);
            $cartData = $this->cartService->getCartCalculations($appliedCoupon);
            return response()->json([
                'status'   => 'success',
                'message'  => $card->card_name . ' added to your cart!',
                'cartData' => $cartData,
            ]);
        }

        return redirect()->route('cart')->with('success', $card->card_name . ' added to your cart!');
    }

    /**
     * Remove Membership Card from Cart session
     */
    public function removeMembershipFromCart()
    {
        session()->forget('cart_membership');
        return redirect()->route('cart')->with('success', 'Membership removed from cart.');
    }

    /**
     * Toggle Loyalty Points (Single Checkbox) on Cart
     */
    public function toggleLoyalty(Request $request)
    {
        $useLoyalty = $request->boolean('use_loyalty');

        if ($useLoyalty) {
            session()->put('use_loyalty_points', true);
        } else {
            session()->forget('use_loyalty_points');
        }

        $appliedCoupon = session()->get('applied_coupon', null);
        $cartData = $this->cartService->getCartCalculations($appliedCoupon);

        return response()->json([
            'status'   => 'success',
            'message'  => $useLoyalty ? 'Loyalty points applied.' : 'Loyalty points removed.',
            'cartData' => $cartData
        ]);
    }

    /**
     * Apply Loyalty Points to Cart
     */
    public function applyLoyalty(Request $request)
    {
        return $this->toggleLoyalty($request);
    }

    /**
     * Remove Loyalty Points from Cart
     */
    public function removeLoyalty()
    {
        session()->forget('use_loyalty_points');

        $appliedCoupon = session()->get('applied_coupon', null);
        $cartData = $this->cartService->getCartCalculations($appliedCoupon);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Loyalty points removed.',
            'cartData' => $cartData
        ]);
    }

    /**
     * Apply Gift Voucher Code
     */
    public function applyVoucher(Request $request)
    {
        $request->validate(['voucher_code' => 'required|string']);

        $code = strtoupper(trim($request->voucher_code));
        $now  = \Carbon\Carbon::now()->toDateString();

        // 1. Search in dedicated tbl_gift_vouchers table first
        $giftVoucher = \App\Models\GiftVoucher::where('code', $code)
            ->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->first();

        // Fallback to legacy offers table if not found in tbl_gift_vouchers
        $legacyOffer = null;
        if (!$giftVoucher) {
            $legacyOffer = DB::table('offers')
                ->where('offer_type', 'gift_voucher')
                ->where('coupon_code', $code)
                ->where('status', 'active')
                ->where(function ($q) use ($now) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                })
                ->first();
        }

        if (!$giftVoucher && !$legacyOffer) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired gift voucher code.'], 400);
        }

        $voucherValue     = $giftVoucher ? (float) $giftVoucher->voucher_value : (float) $legacyOffer->voucher_value;
        $minCartAmount    = $giftVoucher ? (float) ($giftVoucher->min_cart_amount ?? 0) : (float) ($legacyOffer->min_cart_amount ?? 0);
        $allowBogoStack   = $giftVoucher ? (bool) $giftVoucher->allow_bogo_stacking : false;
        $membershipScope  = $giftVoucher ? $giftVoucher->membership_scope : 'all_users';
        $requiredCardId   = $giftVoucher ? $giftVoucher->membership_card_id : null;
        $validityDays     = $giftVoucher ? (int) ($giftVoucher->validity_days ?? 30) : (int) ($legacyOffer->voucher_validity_days ?? 30);
        $voucherDesc      = $giftVoucher ? ($giftVoucher->description ?? $giftVoucher->name) : ($legacyOffer->description ?? $legacyOffer->name);

        $currentCalc = $this->cartService->getCartCalculations();

        // 2. Membership Eligibility Check
        $user = Auth::user();
        $hasActiveMembership = session()->get('membership_bogo_active', false) 
            || session()->has('cart_membership')
            || ($user && !empty($user->is_membership_active));

        $userCardId = null;
        if (session()->has('cart_membership')) {
            $userCardId = session()->get('cart_membership')['card_id'] ?? null;
        } elseif (session()->has('active_membership')) {
            $userCardId = session()->get('active_membership')['card_id'] ?? null;
        } elseif ($user && !empty($user->membership_card_id)) {
            $userCardId = $user->membership_card_id;
        }

        if ($membershipScope === 'any_membership' && !$hasActiveMembership) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This gift voucher is exclusively available for active Gold/VIP Club members.'
            ], 400);
        }

        if ($membershipScope === 'specific_membership' && $requiredCardId) {
            $cardMatched = ($hasActiveMembership && $userCardId == $requiredCardId);
            if (!$cardMatched) {
                $targetCard = DB::table('tbl_membership_card')->where('card_id', $requiredCardId)->first();
                $cardName = $targetCard ? $targetCard->card_name : 'specified membership';
                return response()->json([
                    'status'  => 'error',
                    'message' => "This voucher is exclusive to '{$cardName}' members."
                ], 400);
            }
        }

        // 3. Anti-Stacking Guard (Prevents ₹0 free carts with BOGO)
        if (!$allowBogoStack && ($currentCalc['bogo_savings'] ?? 0) > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gift vouchers cannot be combined with Buy 1 Get 1 Free frame offers.'
            ], 400);
        }

        // 4. Net Cart Amount Validation (Post-BOGO discount)
        $netSubtotal = max(0, 
            ($currentCalc['frame_subtotal'] ?? 0) 
            - ($currentCalc['bogo_savings'] ?? 0) 
            - ($currentCalc['third_item_savings'] ?? 0) 
            + ($currentCalc['lens_subtotal'] ?? 0)
        );

        if ($minCartAmount > 0 && $netSubtotal < $minCartAmount) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Minimum net order value of ₹' . number_format($minCartAmount) . ' (after frame discounts) is required to use this voucher.'
            ], 400);
        }

        // 5. Product/Category/Brand Scope Matching (for dedicated gift vouchers)
        if ($giftVoucher && $giftVoucher->apply_on !== 'all_products') {
            $hasEligibleProduct = false;
            foreach ($currentCalc['items'] as $cartItem) {
                if (empty($cartItem['is_membership']) && $giftVoucher->isProductEligible($cartItem)) {
                    $hasEligibleProduct = true;
                    break;
                }
            }
            if (!$hasEligibleProduct) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This voucher is not applicable to any products currently in your cart.'
                ], 400);
            }
        }

        $voucherData = [
            'code'              => $code,
            'voucher_value'     => $voucherValue,
            'validity_days'     => $validityDays,
            'discount_type'     => 'fixed',
            'discount_amount'   => $voucherValue,
            'amount_applied'    => $voucherValue,
            'remaining_balance' => 0,
            'description'       => $voucherDesc,
            'voucher_id'        => $giftVoucher ? $giftVoucher->id : ($legacyOffer->id ?? null),
        ];

        session()->put('applied_voucher', $voucherData);

        // Also apply as coupon for cart calculation
        $couponData = [
            'code'             => $code,
            'discount_type'    => 'fixed',
            'discount_value'   => $voucherValue,
            'discount_percent' => 0,
            'discount_amount'  => $voucherValue,
            'min_cart_amount'  => $minCartAmount,
            'max_discount'     => 0,
            'description'      => $voucherDesc,
            'is_gift_voucher'  => true,
        ];
        session()->put('applied_coupon', $couponData);

        $cartData = $this->cartService->getCartCalculations($couponData);

        return response()->json([
            'status'   => 'success',
            'message'  => "Gift Voucher '{$code}' applied! ₹" . number_format($voucherValue, 2) . " discount added.",
            'cartData' => $cartData
        ]);
    }

    /**
     * Remove Applied Gift Voucher
     */
    public function removeVoucher()
    {
        session()->forget('applied_voucher');
        session()->forget('applied_coupon');

        $cartData = $this->cartService->getCartCalculations(null);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Gift voucher removed.',
            'cartData' => $cartData
        ]);
    }
}

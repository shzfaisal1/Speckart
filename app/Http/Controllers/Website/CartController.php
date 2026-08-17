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
            'prescription_data' => 'nullable',
            'prescription_file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:10240',
        ]);

        $frameId          = $request->input('frame_id');
        $lensPackageId   = $request->input('lens_package_id');
        $quantity        = $request->input('quantity', 1);
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

        $result = $this->cartService->addToCart($frameId, $lensPackageId, $quantity, $prescriptionData);

        if ($result['status']) {
            // Automatically remove item from wishlist if user is authenticated
            if (Auth::check()) {
                \App\Models\Wishlist::where('user_id', Auth::id())
                    ->where(function($q) use ($frameId) {
                        $q->where('product_id', $frameId);
                        $product = \App\Models\product\Product::where('product_id', $frameId)
                            ->orWhere('id', $frameId)
                            ->first();
                        if ($product) {
                            $q->orWhere('product_id', $product->product_id)
                              ->orWhere('product_id', $product->id);
                        }
                    })
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
        $request->validate([
            'card_id' => 'required|integer',
        ]);

        $card = DB::table('tbl_membership_card')
            ->where('card_id', $request->card_id)
            ->first();

        if (!$card) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Membership plan not found.'], 404);
            }
            return redirect()->back()->with('error', 'Membership plan not found.');
        }

        // Store membership in session
        session()->put('cart_membership', [
            'card_id'               => $card->card_id,
            'card_name'             => $card->card_name,
            'price'                 => (float) $card->price,
            'validity_days'         => (int) $card->validity_days,
            'loyalty_earn_first'    => (float) $card->loyalty_earn_first,
            'loyalty_earn_repeat'   => (float) $card->loyalty_earn_repeat,
            'loyalty_use_percent'   => (float) $card->loyalty_use_percent,
            'coupon_percent'        => (float) $card->coupon_percent,
            'voucher_validity_days' => (int) $card->voucher_validity_days,
            'enable_bogo'           => (int) $card->enable_bogo,
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

        // Search offers table for a gift_voucher with this coupon_code
        $offer = DB::table('offers')
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

        if (!$offer || empty($offer->voucher_value)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired gift voucher code.'], 400);
        }

        // Check minimum cart amount
        $currentCalc = $this->cartService->getCartCalculations();
        $subtotal = ($currentCalc['frame_subtotal'] ?? 0) + ($currentCalc['lens_subtotal'] ?? 0);
        if (!empty($offer->min_cart_amount) && $subtotal < (float) $offer->min_cart_amount) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Minimum cart value of ₹' . number_format($offer->min_cart_amount) . ' is required to use this voucher.'
            ], 400);
        }

        $voucherData = [
            'code'           => $code,
            'voucher_value'  => (float) $offer->voucher_value,
            'validity_days'  => (int) ($offer->voucher_validity_days ?? 30),
            'discount_type'  => 'fixed',
            'discount_amount'=> (float) $offer->voucher_value,
            'amount_applied' => (float) $offer->voucher_value,
            'remaining_balance' => 0,
            'description'    => $offer->description ?? $offer->name,
            'offer_id'       => $offer->id,
        ];

        session()->put('applied_voucher', $voucherData);

        // Also apply as coupon for cart calculation
        $couponData = [
            'code'             => $code,
            'discount_type'    => 'fixed',
            'discount_value'   => (float) $offer->voucher_value,
            'discount_percent' => 0,
            'discount_amount'  => (float) $offer->voucher_value,
            'min_cart_amount'  => (float) ($offer->min_cart_amount ?? 0),
            'max_discount'     => 0,
            'description'      => $offer->description ?? $offer->name,
            'is_gift_voucher'  => true,
        ];
        session()->put('applied_coupon', $couponData);

        $cartData = $this->cartService->getCartCalculations($couponData);

        return response()->json([
            'status'   => 'success',
            'message'  => "Gift Voucher '{$code}' applied! ₹" . number_format($offer->voucher_value, 2) . " discount added.",
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

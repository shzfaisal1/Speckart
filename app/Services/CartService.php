<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\b2c\Cart;
use App\Models\b2c\CartItem;

class CartService
{
    /**
     * Add Frame + Lens Package to Cart session
     */
    public function addToCart($frameId, $lensPackageId = null, $quantity = 1, $prescriptionData = null, $lensType = null)
    {
        $frame = DB::table('tbl_product_code')->where('id', $frameId)->first();
        if (!$frame) {
            $frame = DB::table('tbl_product_code')->where('product_id', $frameId)->first();
        }

        if (!$frame) {
            return ['status' => false, 'message' => 'Frame product not found.'];
        }

        // Fetch Lens Package if selected
        $lens = null;
        if ($lensPackageId) {
            $lens = DB::table('tbl_lens_package')->where('package_id', $lensPackageId)->first();
            if (!$lens) {
                $lens = DB::table('lens_packages')->where('id', $lensPackageId)->first();
            }
        }

        $cart = session()->get('cart', []);

        // Unique cart key for frame + lens combo + prescription
        $lensIdKey = $lens ? (isset($lens->package_id) ? $lens->package_id : $lens->id) : 0;
        $rxKey = $prescriptionData ? md5(is_string($prescriptionData) ? $prescriptionData : json_encode($prescriptionData)) : '0';
        $cartKey = 'frame_' . $frame->id . '_lens_' . $lensIdKey . '_rx_' . substr($rxKey, 0, 6);

        $lensPrice = 0;
        $lensDetails = 'Standard Lenses';
        $isFreeLens = true;

        // FIX: Derive a meaningful lens_name based on lens_type context
        // when no lens package is selected (Reading, Zero Power, Contact Lens, Frame Only)
        $lensNameMap = [
            'Reading Glasses' => 'Reading Power Lenses',
            'Zero Power'      => 'Zero Power - Blue Cut',
            'Contact Lens'    => 'Contact Lens',
            'Frame Only'      => 'Frame Only',
        ];
        $lensName = $lensType && isset($lensNameMap[$lensType])
            ? $lensNameMap[$lensType]
            : 'Basic / Frame Only';

        if ($lens) {
            $isFreeLens = isset($lens->is_free_lens) ? !empty($lens->is_free_lens) : (isset($lens->lens_price) && (float) $lens->lens_price == 0);
            if (isset($lens->lens_price)) {
                $lensPrice = $isFreeLens ? 0 : ((float) $lens->lens_price + (float) ($lens->coating_price ?? 0));
                $lensName  = $lens->package_name ?? $lens->lens_type ?? 'Selected Lens Package';
                $lensDetails = $lens->package_details ?? '';
            } else {
                $lensPrice = $isFreeLens ? 0 : ((float) ($lens->current_price ?? 0));
                $lensName  = $lens->name ?? 'Selected Lens Package';
                $lensDetails = $lens->short_description ?? '';
            }
        }

        // Calculate Frame Sale Price and MRP consistently
        $p1 = (float)($frame->Retail_Price ?? 0);
        $p2 = (float)($frame->Purchase_Price ?? 0);
        $dPrice = (float)($frame->discount_price ?? 0);

        if ($dPrice > 0 && $p1 > 0 && $dPrice < $p1) {
            $frameSalePrice = $dPrice;
            $frameMrp = $p1;
        } else {
            if ($p1 > 0 && $p2 > 0) {
                $frameMrp = max($p1, $p2);
                $frameSalePrice = min($p1, $p2);
            } else {
                $frameMrp = max($p1, $p2);
                $frameSalePrice = $frameMrp;
            }
        }

        // Set Frame Price: Sale price if lens is free, MRP if lens is a paid upgrade
        if ($isFreeLens) {
            $framePrice = $frameSalePrice;
        } else {
            $framePrice = $frameMrp;
        }

        // Frame Image Path logic
        $typeLower = strtolower($frame->product_type ?: 'frame');
        $imageUrl  = asset('website/assets/img/bg/Eyeglasses7.png');
        if (!empty($frame->main_image)) {
            if (!empty($frame->parent_product_code)) {
                $path = "uploads/{$typeLower}/product/{$frame->parent_product_code}/{$frame->main_image}";
                if (file_exists(public_path($path))) {
                    $imageUrl = asset($path);
                }
            } else {
                $pathWithId = "uploads/{$typeLower}/product/{$frame->product_id}/{$frame->main_image}";
                if (file_exists(public_path($pathWithId))) {
                    $imageUrl = asset($pathWithId);
                } elseif (file_exists(public_path($frame->main_image))) {
                    $imageUrl = asset($frame->main_image);
                }
            }
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'key'               => $cartKey,
                'frame_id'          => $frame->id,
                'frame_code'        => $frame->product_code,
                'frame_name'        => $frame->product_name ?: ($frame->Company . ' ' . $frame->Type . ' Eyeglasses'),
                'brand'             => $frame->Company ?? 'Speckart',
                'frame_price'       => $framePrice,
                'frame_image'       => $imageUrl,
                'size'              => $frame->Size ?? 'Medium',
                'lens_package_id'   => $lensIdKey,
                'lens_name'         => $lensName,
                'lens_details'      => $lensDetails,
                'lens_price'        => $lensPrice,
                'quantity'          => (int) $quantity,
                'product_type'      => $frame->product_type ?: ($frame->Type ?: 'Eyeglasses'),
                'prescription_data' => $prescriptionData,
                'promotion_tag'     => $frame->promotion_tag ?? null,
                'is_first_frame_free' => isset($frame->promotion_tag) && stripos($frame->promotion_tag, 'First Frame Free') !== false,
                'is_bogo_eligible'    => (function() use ($frame) {
                    $pt = strtolower($frame->product_type ?: ($frame->Type ?: ''));
                    // Strictly exclude Contact Lenses, Solutions, Accessories, and Other from BOGO
                    if (str_contains($pt, 'contact') || str_contains($pt, 'solution') || str_contains($pt, 'accessory') || $pt === 'lens' || $pt === 'other') {
                        return false;
                    }
                    return isset($frame->promotion_tag) && (stripos($frame->promotion_tag, 'BOGO') !== false || stripos($frame->promotion_tag, 'Buy 1 Get 1') !== false);
                })(),
            ];
        }

        session()->put('cart', $cart);

        // ── Dynamic Database Sync ──────────────────────────────────────────────
        try {
            $dbCart = $this->getOrCreateDbCart();
            $this->syncDbCartItem($dbCart->id, $cart[$cartKey]);
        } catch (\Throwable $e) {
            // Silently handle DB sync error if migration not yet run
            \Illuminate\Support\Facades\Log::warning('Cart DB Sync warning: ' . $e->getMessage());
        }

        return [
            'status'     => true,
            'message'    => 'Item added to cart successfully!',
            'cart_count' => count($cart),
        ];
    }

    /**
     * Update quantity of an item
     */
    public function updateQuantity($cartKey, $quantity)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$cartKey])) {
            if ($quantity <= 0) {
                $itemToRemove = $cart[$cartKey];
                unset($cart[$cartKey]);
                session()->put('cart', $cart);

                try {
                    $dbCart = $this->getOrCreateDbCart();
                    $this->removeDbCartItem($dbCart->id, $itemToRemove);
                } catch (\Throwable $e) {}
            } else {
                $cart[$cartKey]['quantity'] = (int) $quantity;
                session()->put('cart', $cart);

                try {
                    $dbCart = $this->getOrCreateDbCart();
                    $this->syncDbCartItem($dbCart->id, $cart[$cartKey]);
                } catch (\Throwable $e) {}
            }
            return true;
        }
        return false;
    }

    /**
     * Remove item from cart
     */
    public function removeItem($cartKey)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$cartKey])) {
            $itemToRemove = $cart[$cartKey];
            unset($cart[$cartKey]);
            session()->put('cart', $cart);

            try {
                $dbCart = $this->getOrCreateDbCart();
                $this->removeDbCartItem($dbCart->id, $itemToRemove);
            } catch (\Throwable $e) {}

            return true;
        }
        return false;
    }

    // ── Database Helper Methods for Dynamic Cart Sync ──────────────────────────

    /**
     * Bind or transfer any guest cart to the logged-in user upon login
     */
    public function syncGuestCartToUser($userId, $guestSessionId = null)
    {
        if (!$userId) return;

        try {
            $currentSessionId = session()->getId();
            $targetSessionId  = $guestSessionId ?: $currentSessionId;

            // 1. Find guest cart by session_id where user_id IS NULL
            $guestCart = Cart::where('session_id', $targetSessionId)->whereNull('user_id')->first();
            if (!$guestCart && $targetSessionId !== $currentSessionId) {
                $guestCart = Cart::where('session_id', $currentSessionId)->whereNull('user_id')->first();
            }

            // 2. Find or create user cart
            $userCart = Cart::where('user_id', $userId)->first();

            if ($guestCart) {
                if (!$userCart) {
                    // Assign guest cart directly to user
                    $guestCart->user_id    = $userId;
                    $guestCart->session_id = $currentSessionId;
                    $guestCart->save();
                    $userCart = $guestCart;
                } else {
                    // Merge items from guest cart to user cart
                    $guestItems = CartItem::where('cart_id', $guestCart->id)->get();
                    foreach ($guestItems as $gItem) {
                        $existing = CartItem::where('cart_id', $userCart->id)
                            ->where('product_id', $gItem->product_id)
                            ->where('lens_package_id', $gItem->lens_package_id)
                            ->first();
                        if ($existing) {
                            $existing->qty += $gItem->qty;
                            $existing->save();
                            $gItem->delete();
                        } else {
                            $gItem->cart_id = $userCart->id;
                            $gItem->save();
                        }
                    }
                    $guestCart->delete();
                }
            }

            // 3. Re-sync any active session cart items into the user's cart in DB
            $sessionCart = session()->get('cart', []);
            if (!empty($sessionCart)) {
                if (!$userCart) {
                    $userCart = Cart::create([
                        'user_id'    => $userId,
                        'session_id' => $currentSessionId,
                    ]);
                } else {
                    if ($userCart->session_id !== $currentSessionId) {
                        $userCart->session_id = $currentSessionId;
                        $userCart->save();
                    }
                }

                foreach ($sessionCart as $item) {
                    if (isset($item['frame_id'])) {
                        $this->syncDbCartItem($userCart->id, $item);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Error syncing guest cart to user: ' . $e->getMessage());
        }
    }

    /**
     * Get or create the dynamic database Cart record for logged in user or guest session
     */
    public function getOrCreateDbCart()
    {
        $userId    = Auth::id();
        $sessionId = session()->getId();

        if ($userId) {
            $cart = Cart::where('user_id', $userId)->first();
            if (!$cart) {
                // Transfer any guest cart to this logged in user
                $cart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
                if ($cart) {
                    $cart->user_id = $userId;
                    $cart->save();
                } else {
                    $cart = Cart::create([
                        'user_id'    => $userId,
                        'session_id' => $sessionId,
                    ]);
                }
            } else {
                if ($cart->session_id !== $sessionId) {
                    $cart->session_id = $sessionId;
                    $cart->save();
                }
            }
            return $cart;
        }

        // Guest user lookup
        $cart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
        if (!$cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
            ]);
        }
        return $cart;
    }

    /**
     * Sync single line item to cart_items table dynamically
     */
    protected function syncDbCartItem($cartId, array $item)
    {
        $productId     = $item['frame_id'] ?? null;
        $lensPackageId = (!empty($item['lens_package_id']) && $item['lens_package_id'] != '0') ? $item['lens_package_id'] : null;

        $dbItem = CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->where('lens_package_id', $lensPackageId)
            ->first();

        $rxData = $item['prescription_data'] ?? null;
        $rxJson = is_array($rxData) ? json_encode($rxData) : (is_string($rxData) ? $rxData : null);

        // Parse RX powers if available
        $rsD = null; $rcD = null; $raD = null; $pd = null;
        if (is_array($rxData)) {
            $rsD = $rxData['GL_EYE_RS_D'] ?? ($rxData['rs_d'] ?? null);
            $rcD = $rxData['GL_EYE_RC_D'] ?? ($rxData['rc_d'] ?? null);
            $raD = $rxData['GL_EYE_RA_D'] ?? ($rxData['ra_d'] ?? null);
            $pd  = $rxData['GL_EYE_totalPD'] ?? ($rxData['pd'] ?? null);
        }

        if ($dbItem) {
            $dbItem->qty = (int) $item['quantity'];
            $dbItem->sale_price = (float) ($item['frame_price'] ?? 0);
            $dbItem->lens_package_price = (float) ($item['lens_price'] ?? 0);
            $dbItem->prescription_notes = $rxJson;
            $dbItem->save();
        } else {
            CartItem::create([
                'cart_id'            => $cartId,
                'product_id'         => $productId,
                'product_code'       => $item['frame_code'] ?? null,
                'product_type'       => $item['product_type'] ?? 'Eyeglasses',
                'qty'                => (int) ($item['quantity'] ?? 1),
                'unit_price'         => (float) ($item['frame_price'] ?? 0),
                'sale_price'         => (float) ($item['frame_price'] ?? 0),
                'lens_package_id'    => $lensPackageId,
                'lens_package_price' => (float) ($item['lens_price'] ?? 0),
                'GL_EYE_RS_D'        => $rsD,
                'GL_EYE_RC_D'        => $rcD,
                'GL_EYE_RA_D'        => $raD,
                'GL_EYE_totalPD'     => $pd,
                'prescription_notes' => $rxJson,
            ]);
        }
    }

    /**
     * Remove line item from cart_items table dynamically
     */
    protected function removeDbCartItem($cartId, array $item)
    {
        $productId     = $item['frame_id'] ?? null;
        $lensPackageId = (!empty($item['lens_package_id']) && $item['lens_package_id'] != '0') ? $item['lens_package_id'] : null;

        CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->where('lens_package_id', $lensPackageId)
            ->delete();
    }

    /**
     * Restore cart from database table to session if session cart is empty
     */
    public function loadCartFromDbToSession()
    {
        $userId    = Auth::id();
        $sessionId = session()->getId();
        $cart      = [];

        try {
            $dbCart = null;
            if ($userId) {
                $dbCart = Cart::where('user_id', $userId)->first();
            }
            if (!$dbCart) {
                $dbCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
            }

            if ($dbCart) {
                $dbItems = CartItem::where('cart_id', $dbCart->id)->get();
                foreach ($dbItems as $dbItem) {
                    $frame = DB::table('tbl_product_code')->where('id', $dbItem->product_id)->first();
                    if (!$frame) {
                        $frame = DB::table('tbl_product_code')->where('product_id', $dbItem->product_id)->first();
                    }

                    if ($frame) {
                        $lens = null;
                        if ($dbItem->lens_package_id) {
                            $lens = DB::table('tbl_lens_package')->where('package_id', $dbItem->lens_package_id)->first();
                            if (!$lens) {
                                $lens = DB::table('lens_packages')->where('id', $dbItem->lens_package_id)->first();
                            }
                        }

                        $lensIdKey = $lens ? (isset($lens->package_id) ? $lens->package_id : $lens->id) : 0;
                        $rxKey = $dbItem->prescription_notes ? md5($dbItem->prescription_notes) : '0';
                        $cartKey = 'frame_' . $frame->id . '_lens_' . $lensIdKey . '_rx_' . substr($rxKey, 0, 6);

                        $lensPrice = (float) $dbItem->lens_package_price;
                        $lensName  = 'Basic / Frame Only';
                        $lensDetails = 'Standard Lenses';
                        if ($lens) {
                            $lensName    = $lens->package_name ?? ($lens->lens_type ?? ($lens->name ?? 'Selected Lens Package'));
                            $lensDetails = $lens->package_details ?? ($lens->short_description ?? '');
                        }

                        $fPrice = (float) ($dbItem->sale_price ?: ($dbItem->unit_price ?: 0));

                        // Frame Image Path logic
                        $typeLower = strtolower($frame->product_type ?: 'frame');
                        $imageUrl  = asset('website/assets/img/bg/Eyeglasses7.png');
                        if (!empty($frame->main_image)) {
                            if (!empty($frame->parent_product_code)) {
                                $path = "uploads/{$typeLower}/product/{$frame->parent_product_code}/{$frame->main_image}";
                                if (file_exists(public_path($path))) {
                                    $imageUrl = asset($path);
                                }
                            } else {
                                $pathWithId = "uploads/{$typeLower}/product/{$frame->product_id}/{$frame->main_image}";
                                if (file_exists(public_path($pathWithId))) {
                                    $imageUrl = asset($pathWithId);
                                } elseif (file_exists(public_path($frame->main_image))) {
                                    $imageUrl = asset($frame->main_image);
                                }
                            }
                        }

                        $cart[$cartKey] = [
                            'key'               => $cartKey,
                            'frame_id'          => $frame->id,
                            'frame_code'        => $frame->product_code,
                            'frame_name'        => $frame->product_name ?: ($frame->Company . ' ' . $frame->Type . ' Eyeglasses'),
                            'brand'             => $frame->Company ?? 'Speckart',
                            'frame_price'       => $fPrice,
                            'frame_image'       => $imageUrl,
                            'size'              => $frame->Size ?? 'Medium',
                            'lens_package_id'   => $lensIdKey,
                            'lens_name'         => $lensName,
                            'lens_details'      => $lensDetails,
                            'lens_price'        => $lensPrice,
                            'quantity'          => (int) $dbItem->qty,
                            'product_type'      => $dbItem->product_type ?: ($frame->product_type ?: ($frame->Type ?: 'Eyeglasses')),
                            'prescription_data' => $dbItem->prescription_notes ? json_decode($dbItem->prescription_notes, true) : null,
                            'promotion_tag'     => $frame->promotion_tag ?? null,
                            'is_first_frame_free' => isset($frame->promotion_tag) && stripos($frame->promotion_tag, 'First Frame Free') !== false,
                            'is_bogo_eligible'    => (function() use ($frame, $dbItem) {
                                $pt = strtolower($dbItem->product_type ?: ($frame->product_type ?: ($frame->Type ?: '')));
                                if (str_contains($pt, 'contact') || str_contains($pt, 'solution') || str_contains($pt, 'accessory') || $pt === 'lens' || $pt === 'other') {
                                    return false;
                                }
                                return isset($frame->promotion_tag) && (stripos($frame->promotion_tag, 'BOGO') !== false || stripos($frame->promotion_tag, 'Buy 1 Get 1') !== false);
                            })(),
                        ];
                    }
                }
                if (!empty($cart)) {
                    session()->put('cart', $cart);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Error loading cart from DB: ' . $e->getMessage());
        }

        return $cart;
    }

    /**
     * Calculate cart totals including BOGO and coupon
     */
    public function getCartCalculations($appliedCoupon = null)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            $cart = $this->loadCartFromDbToSession();
        }

        $frameSubtotal = 0;
        $lensSubtotal  = 0;
        $bogoSavings   = 0;
        $firstFrameFreeSavings = 0;
        $couponDiscount = 0;
        $items         = [];
        $bogoFallbackMessage = null;
        $bogoEligibleCount = 0;

        $membershipBogoEnabled = false;
        $membershipCouponPercent = 0;
        $hasMembershipInCart = false;
        $membershipCard = null;

        // Check session-based checkout membership
        if (session()->has('cart_membership')) {
            $cartMembership = session()->get('cart_membership');
            $hasMembershipInCart = true;
            $membershipBogoEnabled = true; // Gold Membership in cart enables BOGO for frames
            if (!empty($cartMembership['coupon_percent'])) {
                $membershipCouponPercent = (float) $cartMembership['coupon_percent'];
            }
            if (!empty($cartMembership['card_id'])) {
                $membershipCard = DB::table('tbl_membership_card')->where('card_id', $cartMembership['card_id'])->first();
            }
        }

        // Check authenticated user's membership & customer profile (if logged in)
        $user = auth()->user() ?? null;
        $customerRecord = null;
        if ($user && \Illuminate\Support\Facades\Schema::hasTable('tbl_customer')) {
            $customerQuery = DB::table('tbl_customer')
                ->where(function ($q) use ($user) {
                    if (!empty($user->phone)) $q->orWhere('contact_no', $user->phone);
                    if (!empty($user->mobile)) $q->orWhere('contact_no', $user->mobile);
                    if (!empty($user->contact_no)) $q->orWhere('contact_no', $user->contact_no);
                    if (!empty($user->email)) $q->orWhere('email_id', $user->email);
                    if (!empty($user->name)) $q->orWhere('cust_name', 'LIKE', '%' . $user->name . '%');
                    $q->orWhere('customer_id', $user->id);
                    $q->orWhere('added_by', $user->id);
                });

            $customerRecord = (clone $customerQuery)
                ->orderByDesc('Loyalty_Points_Bal')
                ->orderByDesc('customer_id')
                ->first();

            if ($customerRecord && $customerRecord->membership_card_id && $customerRecord->membership_expiry && \Carbon\Carbon::parse($customerRecord->membership_expiry)->isFuture()) {
                $membershipBogoEnabled = true;
                $dbCard = DB::table('tbl_membership_card')->where('card_id', $customerRecord->membership_card_id)->first();
                if ($dbCard) {
                    $membershipCard = $dbCard;
                    if (!empty($dbCard->coupon_percent)) {
                        $membershipCouponPercent = max($membershipCouponPercent, (float) $dbCard->coupon_percent);
                    }
                }
            }
        }

        // Check active membership in session (for mock/prototype testing)
        if (!$membershipCard && session()->has('active_membership')) {
            $sessMembership = session()->get('active_membership');
            if (\Carbon\Carbon::parse($sessMembership['expiry'])->isFuture()) {
                $membershipBogoEnabled = true;
                $dbCard = DB::table('tbl_membership_card')->where('card_id', $sessMembership['card_id'])->first();
                if ($dbCard) {
                    $membershipCard = $dbCard;
                    if (!empty($dbCard->coupon_percent)) {
                        $membershipCouponPercent = max($membershipCouponPercent, (float) $dbCard->coupon_percent);
                    }
                }
            }
        }

        // Flatten cart items & calculate first frame free savings
        foreach ($cart as $key => $item) {
            $qty = (int) ($item['quantity'] ?? 1);
            $fPrice = (float) $item['frame_price'];
            $lPrice = (float) $item['lens_price'];

            // First Frame Free promo (Case 8: free frame, pay lenses)
            if (!empty($item['is_first_frame_free'])) {
                $firstFrameFreeSavings += ($fPrice * $qty);
                $item['is_first_frame_free_applied'] = true;
            }

            // When membership BOGO is active, only regular optical/sunglass frames are eligible.
            // Strictly exclude Contact Lenses, Solutions, Accessories, and other non-frame items.
            $prodTypeLower   = strtolower(trim($item['product_type'] ?? 'frame'));
            $isIneligibleCategory = str_contains($prodTypeLower, 'contact')
                                 || ($prodTypeLower === 'lens')
                                 || str_contains($prodTypeLower, 'solution')
                                 || str_contains($prodTypeLower, 'accessory')
                                 || str_contains($prodTypeLower, 'other');

            $isRegularFrame  = empty($item['is_membership']) && !$isIneligibleCategory;
            $effectivelyBogoEligible = $isRegularFrame && ($hasMembershipInCart || $membershipBogoEnabled || !empty($item['is_bogo_eligible']));
            if ($effectivelyBogoEligible) {
                $bogoEligibleCount += $qty;
                // Mark it so the expansion loop below picks it up consistently
                $item['is_bogo_eligible'] = true;
            } else {
                $item['is_bogo_eligible'] = false;
            }

            $frameSubtotal += ($fPrice * $qty);
            $lensSubtotal  += ($lPrice * $qty);

            $item['discounted_frame_price'] = $fPrice;
            $item['is_bogo_free'] = false;
            $item['is_bogo_half'] = false;
            $items[$key] = $item;
        }

        // Inject membership card into items if in cart
        if ($hasMembershipInCart) {
            $cartMembership = session()->get('cart_membership');
            $membershipKey = 'membership_' . $cartMembership['card_id'];
            $items[$membershipKey] = [
                'key'               => $membershipKey,
                'is_membership'     => true,
                'frame_id'          => null,
                'frame_code'        => 'MEMBERSHIP',
                'frame_name'        => $cartMembership['card_name'],
                'brand'             => 'Privilege Club',
                'frame_price'       => (float) $cartMembership['price'],
                'frame_image'       => asset('assets/img/icon/gold_membership_card.png'),
                'size'              => 'N/A',
                'lens_package_id'   => null,
                'lens_name'         => 'Privilege Card',
                'lens_details'      => 'Unlock BOGO & VIP privileges',
                'lens_price'        => 0.0,
                'quantity'          => 1,
                'prescription_data' => null,
            ];
            $frameSubtotal += (float) $cartMembership['price'];
        }

        // Check if there is an active BOGO offer or membership BOGO with extra 3rd item discount (cached & date-validated)
        $now = \Carbon\Carbon::now()->toDateString();
        $bogoExtraDiscount = \Illuminate\Support\Facades\Cache::remember('active_bogo_extra_discount', 300, function () use ($now) {
            $offer = DB::table('offers')
                ->where('offer_type', 'buy1get1')
                ->where('status', 'active')
                ->where(function ($q) use ($now) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                })
                ->first();
            if ($offer && !empty($offer->bogo_extra_enabled) && !empty($offer->bogo_extra_discount)) {
                return (float) $offer->bogo_extra_discount;
            }
            return 60.0;
        });

        $thirdItemSavings = 0;

        // Apply BOGO if applicable (Pairs of 2 + 3rd Item Bonus) - Only if BOGO is enabled on user's active/cart membership
        if ($bogoEligibleCount >= 2 && $membershipBogoEnabled) {
            // Check for Non-AG Bifocal exception (Case 9)
            $hasNonAgBifocal = false;
            foreach ($items as $item) {
                if (isset($item['is_bogo_eligible']) && !empty($item['is_bogo_eligible'])) {
                    $lensNameLower = strtolower($item['lens_name']);
                    if (strpos($lensNameLower, 'bifocal') !== false && 
                        strpos($lensNameLower, 'anti-glare') === false && 
                        strpos($lensNameLower, 'ar') === false) {
                        $hasNonAgBifocal = true;
                        break;
                    }
                }
            }

            // Expand items into single units to sort & apply BOGO accurately
            $frameUnits = [];
            foreach ($items as $key => $item) {
                if (isset($item['is_bogo_eligible']) && !empty($item['is_bogo_eligible'])) {
                    for ($i = 0; $i < $item['quantity']; $i++) {
                        $frameUnits[] = [
                            'key' => $key,
                            'price' => (float) $item['frame_price']
                        ];
                    }
                }
            }

            // Sort frame units by price descending
            usort($frameUnits, function($a, $b) {
                return $b['price'] <=> $a['price'];
            });

            // Initialize item-level discount tracking
            foreach ($items as $k => $item) {
                $items[$k]['item_bogo_discount'] = 0;
            }

            // Every 2nd frame in sorted list is 100% FREE (BOGO Pair - Lower Value Frame)
            // Every 3rd frame (unmatched 3rd item in a set) gets 60% OFF!
            for ($i = 1; $i < count($frameUnits); $i++) {
                $key = $frameUnits[$i]['key'];
                if ($i % 2 == 1) {
                    // 2nd item of a pair (indices 1, 3, 5...) -> Lower-priced frame in this pair
                    if ($hasNonAgBifocal) {
                        // Fall back to "Buy 2nd at 50%" rule
                        $discountAmt = ($frameUnits[$i]['price'] * 0.5);
                        $bogoSavings += $discountAmt;
                        $items[$key]['is_bogo_half'] = true;
                        $items[$key]['item_bogo_discount'] = ($items[$key]['item_bogo_discount'] ?? 0) + $discountAmt;
                        $bogoFallbackMessage = "Note: BOGO isn't valid with Non-AG bifocal lenses – second pair is 50% off instead.";
                    } else {
                        // Standard BOGO (100% free frame on the lower-value product)
                        $discountAmt = $frameUnits[$i]['price'];
                        $bogoSavings += $discountAmt;
                        $items[$key]['is_bogo_free'] = true;
                        $items[$key]['item_bogo_discount'] = ($items[$key]['item_bogo_discount'] ?? 0) + $discountAmt;
                    }
                } else {
                    // Unmatched 3rd item of a set (indices 2, 4, 6... when it is the last item in array)
                    if ($i == count($frameUnits) - 1) {
                        $thirdDiscountAmount = ($frameUnits[$i]['price'] * $bogoExtraDiscount) / 100;
                        $thirdItemSavings += $thirdDiscountAmount;
                        $items[$key]['is_bogo_third_discount'] = true;
                        $items[$key]['bogo_third_discount_percent'] = $bogoExtraDiscount;
                        $items[$key]['bogo_third_savings'] = $thirdDiscountAmount;
                        $items[$key]['item_bogo_discount'] = ($items[$key]['item_bogo_discount'] ?? 0) + $thirdDiscountAmount;
                    }
                }
            }
        }

        // Calculate Frame Subtotal after discounts
        $totalFrameDiscount = $bogoSavings + $thirdItemSavings + $firstFrameFreeSavings;
        $netFrameSubtotal   = max(0, $frameSubtotal - $totalFrameDiscount);

        // Apply Coupon/Membership Discount
        // Membership-gated BOGO allows coupon stacking (coupon applies on net frame subtotal alongside BOGO savings)
        $allowCouponStacking = ($totalFrameDiscount == 0) || $hasMembershipInCart || $membershipBogoEnabled;

        if ($allowCouponStacking) {
            if ($appliedCoupon) {
                if (isset($appliedCoupon['discount_percent']) && $appliedCoupon['discount_percent'] > 0) {
                    $couponDiscount = ($netFrameSubtotal * $appliedCoupon['discount_percent']) / 100;
                } elseif (isset($appliedCoupon['discount_amount']) && $appliedCoupon['discount_amount'] > 0) {
                    $couponDiscount = min($netFrameSubtotal, (float) $appliedCoupon['discount_amount']);
                }
            } elseif ($membershipCouponPercent > 0) {
                $couponDiscount = ($netFrameSubtotal * $membershipCouponPercent) / 100;
            }
        }

        // Calculate preliminary grand total before loyalty redemption
        $preliminaryTotal = max(0, ($netFrameSubtotal - $couponDiscount) + $lensSubtotal);

        // Determine dynamic loyalty earn percentage from membership card settings
        $cashbackPercent = 5.0; // Default base rate
        $isRepeatCustomer = false;

        if ($user) {
            $userContact = $user->contact_no ?? ($user->phone ?? null);
            $userEmail   = $user->email ?? null;

            $pastOrdersCount = DB::table('tbl_sales')
                ->where(function ($query) use ($user, $userContact, $userEmail) {
                    $query->where('cust_id', $user->id);
                    if ($userContact) {
                        $query->orWhere('contact_no', $userContact);
                    }
                    if ($userEmail) {
                        $query->orWhere('email_id', $userEmail);
                    }
                })
                ->count();

            if ($pastOrdersCount > 0) {
                $isRepeatCustomer = true;
            }
        }

        if ($membershipCard) {
            if ($isRepeatCustomer && isset($membershipCard->loyalty_earn_repeat) && (float)$membershipCard->loyalty_earn_repeat > 0) {
                $cashbackPercent = (float) $membershipCard->loyalty_earn_repeat;
            } elseif (isset($membershipCard->loyalty_earn_first) && (float)$membershipCard->loyalty_earn_first > 0) {
                $cashbackPercent = (float) $membershipCard->loyalty_earn_first;
            }
        }

        // Fetch Admin Configured Point Conversion Rate & Rules (from tbl_loyalty id=1 and id=2)
        $loyaltyConfig     = DB::table('tbl_loyalty')->where('id', 1)->first();
        $autoLoyaltyConfig = DB::table('tbl_loyalty')->where('id', 2)->first();

        $pointValue = ($loyaltyConfig && !empty($loyaltyConfig->one_point_redem)) ? (float) $loyaltyConfig->one_point_redem : 1.0;

        // Available loyalty points & single-checkbox redemption processing
        $availableLoyaltyPoints = 0;
        if (session()->has('test_loyalty_points')) {
            $availableLoyaltyPoints = (float) session()->get('test_loyalty_points');
        } elseif ($customerRecord) {
            if (isset($customerRecord->Loyalty_Points_Bal) && (float)$customerRecord->Loyalty_Points_Bal > 0) {
                $availableLoyaltyPoints = (float) $customerRecord->Loyalty_Points_Bal;
            } elseif (isset($customerRecord->Loyalty_Points) && (float)$customerRecord->Loyalty_Points > 0) {
                $availableLoyaltyPoints = max(0, (float) ($customerRecord->Loyalty_Points - ($customerRecord->Loyalty_Points_Redeem ?? 0)));
            }
        }

        if ($availableLoyaltyPoints == 0 && $user) {
            if (isset($user->loyalty_points) && (float)$user->loyalty_points > 0) {
                $availableLoyaltyPoints = (float) $user->loyalty_points;
            } elseif (\Illuminate\Support\Facades\Schema::hasTable('tbl_customer')) {
                // Secondary check across all matching contact/email/name entries in tbl_customer
                $custPts = DB::table('tbl_customer')
                    ->where(function($q) use ($user) {
                        if (!empty($user->phone)) $q->orWhere('contact_no', $user->phone);
                        if (!empty($user->mobile)) $q->orWhere('contact_no', $user->mobile);
                        if (!empty($user->email)) $q->orWhere('email_id', $user->email);
                        if (!empty($user->name)) $q->orWhere('cust_name', 'LIKE', '%' . $user->name . '%');
                    })
                    ->max('Loyalty_Points_Bal');
                if ($custPts && (float)$custPts > 0) {
                    $availableLoyaltyPoints = (float)$custPts;
                }
            }

            if ($availableLoyaltyPoints == 0 && \Illuminate\Support\Facades\Schema::hasTable('tbl_sales')) {
                $earned = (float) DB::table('tbl_sales')->where('user_id', $user->id)->where('sales_type', 0)->sum('earnedPoints');
                $used   = (float) DB::table('tbl_sales')->where('user_id', $user->id)->where('sales_type', 0)->sum('loyalty_point_amount');
                $availableLoyaltyPoints = max(0, $earned - $used);
            }
        }

        // Admin Redemption Cap % on Order Total (tbl_loyalty id=2 -> order_use_loyalty)
        // NOTE: This cap is ALWAYS enforced regardless of auto_status toggle.
        $adminCapPercent = ($autoLoyaltyConfig && isset($autoLoyaltyConfig->order_use_loyalty) && (float)$autoLoyaltyConfig->order_use_loyalty > 0)
            ? (float) $autoLoyaltyConfig->order_use_loyalty
            : 100.0;

        $maxCapRupees = ($preliminaryTotal * $adminCapPercent) / 100.0;

        // Business Rule: Cap points redemption by:
        //  1. Admin-configured order_use_loyalty % of order total
        //  2. The (preliminaryTotal - 1) hard floor so total never hits ₹0
        $availableRupees = $availableLoyaltyPoints * $pointValue;
        $maxUsableRupees = min($availableRupees, min($maxCapRupees, max(0, (float) floor($preliminaryTotal - 1))));
        $rupeesToRedeem  = $maxUsableRupees;
        $pointsToRedeem  = ($pointValue > 0) ? ($rupeesToRedeem / $pointValue) : $rupeesToRedeem;

        $useLoyalty = (bool) session()->get('use_loyalty_points', false);

        if ($useLoyalty && $pointsToRedeem > 0) {
            $loyaltyDiscount = $rupeesToRedeem;
            $pointsUsed      = $pointsToRedeem;
        } else {
            $loyaltyDiscount = 0;
            $pointsUsed      = 0;
            if ($useLoyalty && $pointsToRedeem <= 0) {
                session()->forget('use_loyalty_points');
            }
        }

        // Final Grand Total after Loyalty Points Discount
        $grandTotal = max(0, $preliminaryTotal - $loyaltyDiscount);

        // Dynamic Earning Calculation from Admin Auto-Generate Rules (tbl_loyalty id=2)
        // Only override $cashbackPercent from auto rules when auto_status = 1 (Enabled in Admin panel)
        if ($autoLoyaltyConfig && isset($autoLoyaltyConfig->auto_status) && $autoLoyaltyConfig->auto_status == 1) {
            if (isset($autoLoyaltyConfig->auto_set_loyalty_point) && $autoLoyaltyConfig->auto_set_loyalty_point == 1) {
                // Ratio Mode: (no_of_points / x_number_sale_value) earn rate
                $noOfPts = (float) ($autoLoyaltyConfig->no_of_points ?? 0);
                $xValue  = (float) ($autoLoyaltyConfig->x_number_sale_value ?? 100);
                if ($xValue > 0 && $noOfPts > 0) {
                    $cashbackPercent = ($noOfPts / $xValue) * 100;
                }
            } elseif (isset($autoLoyaltyConfig->auto_set_loyalty_point) && $autoLoyaltyConfig->auto_set_loyalty_point == 2) {
                // Fixed Percentage Mode
                if (!empty($autoLoyaltyConfig->fixed_per) && (float)$autoLoyaltyConfig->fixed_per > 0) {
                    $cashbackPercent = (float) $autoLoyaltyConfig->fixed_per;
                }
            }

            // Sales Value Basis: 1 = Gross Sales, 2 = Net Sales, 3 = Total Payable Amount
            $salesBasisOption = (int) ($autoLoyaltyConfig->sales_value ?? 3);
            if ($salesBasisOption === 1) {
                $earningBaseAmount = $frameSubtotal + $lensSubtotal;       // Gross (before discounts)
            } elseif ($salesBasisOption === 2) {
                $earningBaseAmount = $preliminaryTotal;                     // Net (after coupon, before loyalty)
            } else {
                $earningBaseAmount = $grandTotal;                           // Total Payable (after all discounts)
            }
        } else {
            // auto_status = 0 (Admin disabled auto-generate)
            // Fall back to membership card cashback rate & grand total as earn base
            $earningBaseAmount = $grandTotal;
        }

        // Pending Loyalty Points / Cashback calculation on selected sales basis amount
        $cashbackDelayDays   = 14;
        $orderRewardPts      = (int) round(($earningBaseAmount * $cashbackPercent) / 100);
        $cashbackReleaseDate = \Carbon\Carbon::now()->addDays($cashbackDelayDays)->format('d M Y');

        // ── Generic 3-State Banner State Machine ──
        $freeItemAvailable = ($bogoEligibleCount >= 2 && $membershipBogoEnabled && $bogoSavings > 0);
        session()->put('free_item_selected', $freeItemAvailable);

        if (!$hasMembershipInCart && !$membershipBogoEnabled) {
            // State 1: Membership NOT in cart
            $bannerState = [
                'state'      => 1,
                'title'      => 'Add Gold Max Membership and',
                'subtitle'   => 'Avail Buy 1 Get 1 Free + ' . (int)$bogoExtraDiscount . '% OFF on 3rd Pair + ' . (int)$cashbackPercent . '% Loyalty Points',
                'btn_text'   => 'Add Gold',
                'btn_action' => 'add_membership',
                'cta_url'    => route('website.membership'),
            ];
        } elseif (($hasMembershipInCart || $membershipBogoEnabled) && $bogoEligibleCount < 2) {
            // State 2: Membership in cart, 1 frame in cart (bogoEligibleCount < 2) -> Prompt to add 2nd pair FREE
            $bannerState = [
                'state'      => 2,
                'title'      => 'Gold Max Membership added',
                'subtitle'   => 'Add 2nd Pair for Free',
                'btn_text'   => 'Choose Now',
                'btn_action' => 'choose_bogo',
                'cta_url'    => route('products', ['bogo_eligible' => 1]),
            ];
        } elseif (($hasMembershipInCart || $membershipBogoEnabled) && $bogoEligibleCount == 2) {
            // State 2.5: BOGO applied for 2 frames! Prompt user to add 3rd pair for 60% OFF
            $bannerState = [
                'state'      => 2,
                'title'      => 'Buy 1 Get 1 Free Applied!',
                'subtitle'   => 'Add a 3rd Pair and get ' . (int)$bogoExtraDiscount . '% OFF!',
                'btn_text'   => 'Add 3rd Pair',
                'btn_action' => 'choose_bogo',
                'cta_url'    => route('products', ['bogo_eligible' => 1]),
            ];
        } else {
            // State 3: 3+ frames in cart -> 3rd pair discount applied, show plain offer details box (no CTA button)
            $bannerState = [
                'state'      => 3,
                'title'      => 'Gold Max Membership Benefit',
                'subtitle'   => 'Buy 1 Get 1 Free applied + ' . (int)$bogoExtraDiscount . '% OFF on 3rd Pair applied + ' . (int)$cashbackPercent . '% cashback (will be sent after 14 days of order delivery)',
                'btn_text'   => null,
                'btn_action' => null,
                'cta_url'    => null,
            ];
        }

        // Fetch dynamic active Gift Voucher perk for cart
        $cartTotalForVoucher = $frameSubtotal + $lensSubtotal;
        $activeVoucher = DB::table('offers')
            ->where('offer_type', 'gift_voucher')
            ->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->where(function ($q) use ($cartTotalForVoucher) {
                $q->whereNull('min_cart_amount')->orWhere('min_cart_amount', '<=', $cartTotalForVoucher);
            })
            ->first();

        $giftVoucherPerk = null;
        if ($activeVoucher) {
            $hasCode      = !empty($activeVoucher->coupon_code);
            $deliveryType = $hasCode ? 'manual' : 'auto';

            // Clean offer name — strip test/placeholder values, fallback to a professional label
            $rawName      = trim($activeVoucher->name ?? '');
            $offerLabel   = (strlen($rawName) >= 3 && !preg_match('/^(test|demo|sample|temp|xxx)/i', $rawName))
                            ? $rawName
                            : '🎁 Gift Voucher Perk';

            $giftVoucherPerk = [
                'id'            => $activeVoucher->id,
                'name'          => $offerLabel,
                'voucher_value' => (float) ($activeVoucher->voucher_value ?? 0),
                'validity_days' => (int) ($activeVoucher->voucher_validity_days ?? 30),
                'coupon_code'   => $hasCode ? strtoupper($activeVoucher->coupon_code) : null,
                'delivery_type' => $deliveryType,   // 'auto' = no code needed | 'manual' = code required
                'description'   => $activeVoucher->description ?? null,
            ];
        }

        return [
            'items'                     => $items,
            'item_count'                => count($items),
            'total_quantity'            => array_sum(array_column($cart, 'quantity')) + ($hasMembershipInCart ? 1 : 0),
            'frame_subtotal'            => $frameSubtotal,
            'lens_subtotal'             => $lensSubtotal,
            'bogo_savings'              => $bogoSavings,
            'third_item_savings'        => $thirdItemSavings,
            'bogo_extra_discount'       => $bogoExtraDiscount,
            'first_frame_free_save'     => $firstFrameFreeSavings,
            'coupon_discount'           => $couponDiscount,
            'loyalty_discount'          => $loyaltyDiscount,
            'points_used'               => $pointsUsed,
            'point_value'               => $pointValue,
            'available_rupees'          => $availableRupees,
            'use_loyalty_points'        => ($loyaltyDiscount > 0),
            'available_loyalty_points'  => $availableLoyaltyPoints,
            'order_reward_pts'          => $orderRewardPts,
            'grand_total'               => $grandTotal,
            'is_bogo_active'            => ($bogoEligibleCount >= 2 && $membershipBogoEnabled),
            'free_item_selected'        => $freeItemAvailable,
            'bogo_fallback_message'     => $bogoFallbackMessage,
            'has_membership_in_cart'    => $hasMembershipInCart,
            'membership_bogo_enabled'   => $membershipBogoEnabled,
            'membership_coupon_percent' => $membershipCouponPercent,
            'pending_cashback'          => $orderRewardPts,
            'cashback_percent'          => $cashbackPercent,
            'cashback_delay_days'       => $cashbackDelayDays,
            'cashback_release_date'     => $cashbackReleaseDate,
            'banner_state'              => $bannerState,
            'gift_voucher_perk'         => $giftVoucherPerk,
            'applied_voucher'           => session()->get('applied_voucher', null),
            'available_vouchers'        => $this->getAvailableVouchers(),
            'available_coupons'         => $this->getAvailableCoupons(),
        ];
    }

    /**
     * Get active Gift Vouchers from offers table (with a coupon_code set)
     */
    public function getAvailableVouchers()
    {
        $now = \Carbon\Carbon::now()->toDateString();

        $rows = DB::table('offers')
            ->where('offer_type', 'gift_voucher')
            ->whereNotNull('coupon_code')
            ->where('coupon_code', '!=', '')
            ->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->get();

        $vouchers = [];
        foreach ($rows as $r) {
            $vouchers[] = [
                'code'        => strtoupper($r->coupon_code),
                'balance'     => (float) ($r->voucher_value ?? 0),
                'expires_at'  => $r->end_date ? \Carbon\Carbon::parse($r->end_date)->format('d M Y') : null,
                'description' => $r->description ?? $r->name,
            ];
        }
        return $vouchers;
    }

    /**
     * Get active dynamic coupons from offers & coupons tables
     */
    public function getAvailableCoupons()
    {
        $now = \Carbon\Carbon::now()->toDateString();
        $coupons = [];

        // 1. Fetch from offers table (where coupon_code is set)
        $offerCoupons = DB::table('offers')
            ->whereNotNull('coupon_code')
            ->where('coupon_code', '!=', '')
            ->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->get();

        foreach ($offerCoupons as $o) {
            $title = ($o->discount_type === 'fixed') ? ('₹' . (int)$o->discount_value . ' OFF') : ((int)$o->discount_value . '% OFF');
            if ($o->offer_type === 'gift_voucher' && !empty($o->voucher_value)) {
                $title = '₹' . (int)$o->voucher_value . ' GIFT VOUCHER';
            }

            $coupons[] = [
                'code'            => strtoupper($o->coupon_code),
                'discount_type'   => $o->discount_type ?? 'percentage',
                'discount_value'  => (float) ($o->offer_type === 'gift_voucher' ? $o->voucher_value : $o->discount_value),
                'min_cart_amount' => (float) ($o->min_cart_amount ?? 0),
                'max_discount'    => (float) ($o->max_discount ?? 0),
                'description'     => $o->description ?? ($o->name ?? ''),
                'title'           => $title,
                'is_gift_voucher' => ($o->offer_type === 'gift_voucher'),
            ];
        }

        // 2. Fetch from coupons table
        if (\Illuminate\Support\Facades\Schema::hasTable('coupons')) {
            $dbCoupons = DB::table('coupons')
                ->where('is_active', 1)
                ->where(function ($q) use ($now) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
                })
                ->get();

            foreach ($dbCoupons as $c) {
                $code = strtoupper($c->code);
                if (!collect($coupons)->pluck('code')->contains($code)) {
                    $coupons[] = [
                        'code'            => $code,
                        'discount_type'   => $c->discount_type ?? 'percentage',
                        'discount_value'  => (float) $c->discount_value,
                        'min_cart_amount' => (float) ($c->min_order_value ?? 0),
                        'max_discount'    => (float) ($c->max_discount_amount ?? 0),
                        'description'     => $c->description ?? '',
                        'title'           => ($c->discount_type === 'fixed') ? ('₹' . (int)$c->discount_value . ' OFF') : ((int)$c->discount_value . '% OFF'),
                    ];
                }
            }
        }

        // 3. Fallback SINGLE default coupon if no DB coupons found
        if (empty($coupons)) {
            $coupons[] = [
                'code'            => 'SINGLE',
                'discount_type'   => 'percentage',
                'discount_value'  => 25,
                'min_cart_amount' => 0,
                'max_discount'    => 0,
                'description'     => '25% off frame price',
                'title'           => '25% OFF',
            ];
        }

        return $coupons;
    }
}

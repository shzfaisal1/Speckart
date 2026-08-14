<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Step 1: Show Shipping Details page (Saved addresses list + Add/Edit form)
     */
    public function add_shipping_details()
    {
        $appliedCoupon = session()->get('applied_coupon', null);
        $cartData = $this->cartService->getCartCalculations($appliedCoupon);

        // Redirect to cart if empty
        if (empty($cartData['items'])) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        // Get saved addresses
        $savedAddresses = collect();
        if (auth()->check()) {
            $savedAddresses = \App\Models\UserAddress::where('user_id', auth()->id())
                ->latest()
                ->get();
        } else {
            $sessionList = session()->get('saved_addresses', []);
            $savedAddresses = collect($sessionList);
        }

        // Selected shipping address from session
        $shippingData = session()->get('checkout_shipping', []);

        return view('website.order.add-shipping-details', compact('cartData', 'shippingData', 'savedAddresses'));
    }

    /**
     * Select a saved address by ID and proceed to payment
     */
    public function select_saved_address(Request $request)
    {
        $request->validate([
            'address_id' => 'required',
        ]);

        $addressId = $request->input('address_id');
        $address = null;

        if (auth()->check()) {
            $address = \App\Models\UserAddress::where('id', $addressId)
                ->where('user_id', auth()->id())
                ->first();
        } else {
            $sessionList = session()->get('saved_addresses', []);
            foreach ($sessionList as $item) {
                if (isset($item['id']) && $item['id'] == $addressId) {
                    $address = (object)$item;
                    break;
                }
            }
        }

        if (!$address) {
            return redirect()->route('shipping-details')->with('error', 'Selected address not found.');
        }

        // Set as checkout shipping in session
        session()->put('checkout_shipping', [
            'address_id'    => $address->id,
            'address_type'  => $address->address_type ?? 'Home',
            'full_name'     => $address->full_name,
            'phone'         => $address->phone,
            'pincode'       => $address->pincode,
            'house_no'      => $address->house_no,
            'road_area'     => $address->road_area,
            'landmark'      => $address->landmark,
            'email'         => $address->email,
            'full_address'  => $address->full_address,
        ]);

        return redirect()->route('payment');
    }

    /**
     * Step 1.5: Validate & save new shipping address to DB/session, redirect to payment
     */
    public function save_shipping_details(Request $request)
    {
        $validated = $request->validate([
            'full_name'     => 'required|string|max:255',
            'phone'         => 'required|digits:10',
            'pincode'       => 'required|digits:6',
            'house_no'      => 'required|string|max:500',
            'road_area'     => 'required|string|max:500',
            'landmark'      => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'address_type'  => 'nullable|string|max:50',
        ], [
            'phone.required' => 'Please enter your mobile number.',
            'phone.digits'   => 'Mobile number must be exactly 10 digits.',
            'pincode.required' => 'Please enter your pincode.',
            'pincode.digits' => 'Pincode must be exactly 6 digits.',
        ]);

        $addressType = $validated['address_type'] ?? 'Home';

        // Build full address string
        $fullAddress = $validated['house_no'] . ', ' . $validated['road_area'];
        if (!empty($validated['landmark'])) {
            $fullAddress .= ', Near ' . $validated['landmark'];
        }
        $fullAddress .= ' - ' . $validated['pincode'];

        $validated['full_address'] = $fullAddress;

        // Save to DB if logged in
        if (auth()->check()) {
            $savedRecord = \App\Models\UserAddress::create([
                'user_id'      => auth()->id(),
                'address_type' => $addressType,
                'full_name'    => $validated['full_name'],
                'phone'        => $validated['phone'],
                'pincode'      => $validated['pincode'],
                'house_no'     => $validated['house_no'],
                'road_area'    => $validated['road_area'],
                'landmark'     => $validated['landmark'] ?? null,
                'email'        => $validated['email'] ?? null,
                'full_address' => $fullAddress,
                'is_default'   => true,
            ]);
            $validated['address_id'] = $savedRecord->id;
        } else {
            // Save to guest session address list
            $sessionList = session()->get('saved_addresses', []);
            $tempId = 'guest_' . time();
            $validated['id'] = $tempId;
            $validated['address_id'] = $tempId;
            $sessionList[] = $validated;
            session()->put('saved_addresses', $sessionList);
        }

        session()->put('checkout_shipping', $validated);

        return redirect()->route('payment');
    }

    /**
     * Delete a saved address
     */
    public function delete_address($id)
    {
        if (auth()->check()) {
            \App\Models\UserAddress::where('id', $id)
                ->where('user_id', auth()->id())
                ->delete();
        } else {
            $sessionList = session()->get('saved_addresses', []);
            $newList = array_filter($sessionList, function ($item) use ($id) {
                return (isset($item['id']) && $item['id'] != $id);
            });
            session()->put('saved_addresses', array_values($newList));
        }

        // If current checkout shipping was this deleted address, clear session
        $shippingData = session()->get('checkout_shipping', []);
        if (isset($shippingData['address_id']) && $shippingData['address_id'] == $id) {
            session()->forget('checkout_shipping');
        }

        return redirect()->route('shipping-details')->with('success', 'Address deleted successfully.');
    }

    /**
     * Step 2: Show Payment page with dynamic cart summary
     */
    public function payment_page()
    {
        $appliedCoupon = session()->get('applied_coupon', null);
        $cartData = $this->cartService->getCartCalculations($appliedCoupon);

        // Redirect to cart if empty
        if (empty($cartData['items'])) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        // Redirect to shipping if address not yet filled
        $shippingData = session()->get('checkout_shipping', null);
        if (!$shippingData) {
            return redirect()->route('shipping-details')->with('error', 'Please fill in your shipping address first.');
        }

        return view('website.order.payment-page', compact('cartData', 'shippingData'));
    }

    /**
     * Step 3: My Orders page - fetch real customer orders from B2cOrder (Source of Truth) & tbl_sales
     */
    public function my_order()
    {
        $user = auth()->user();
        $b2cOrders = collect();

        if (!$user) {
            $shippingData = session()->get('checkout_shipping', []);
            $phone = $shippingData['phone'] ?? null;
            $email = $shippingData['email'] ?? null;

            if ($phone || $email) {
                $b2cOrders = \App\Models\b2c\B2cOrder::with(['items.lensPackage'])
                    ->where(function($q) use ($phone, $email) {
                        if ($phone) $q->orWhere('guest_phone', $phone);
                        if ($email) $q->orWhere('guest_email', $email);
                    })
                    ->latest('created_at')
                    ->get();
            }
        } else {
            $b2cOrders = \App\Models\b2c\B2cOrder::with(['items.lensPackage'])
                ->where(function($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if (!empty($user->phone)) {
                        $q->orWhere('guest_phone', $user->phone);
                    }
                    if (!empty($user->email)) {
                        $q->orWhere('guest_email', $user->email);
                    }
                })
                ->latest('created_at')
                ->get();
        }

        $orders = collect();
        $seenOrderNos = [];

        // 1. Process Dedicated B2C Orders (Source of Truth)
        foreach ($b2cOrders as $bOrder) {
            $seenOrderNos[] = $bOrder->order_number;

            // Map B2C string status to UI numeric status code
            $statusStr = strtolower((string)$bOrder->order_status);
            $statusCode = 0;
            if (in_array($statusStr, ['delivered', 'completed'])) {
                $statusCode = 2;
            } elseif (in_array($statusStr, ['shipped', 'ready_to_ship', 'processing'])) {
                $statusCode = 1;
            } elseif (in_array($statusStr, ['cancelled', 'returned'])) {
                $statusCode = 3;
            }

            // Build products collection for this B2C order
            $orderProducts = collect();
            foreach ($bOrder->items as $item) {
                $pObj = new \stdClass();
                $pObj->product_id      = $item->product_id;
                $pObj->product_company = 'Speckarts';
                $pObj->product_deatils = $item->product_name . ($item->lensPackage ? ' (' . $item->lensPackage->name . ')' : '');
                $pObj->item_price      = $item->unit_price;
                $pObj->qty             = $item->quantity;
                $pObj->image           = asset('website/assets/img/bg/Eyeglasses1.png');

                // Resolve frame image from product catalog
                if (!empty($item->product_id)) {
                    $frame = DB::table('tbl_product_code')->where('id', $item->product_id)->first();
                    if (!$frame) {
                        $frame = DB::table('tbl_product_code')->where('product_id', $item->product_id)->first();
                    }
                    if ($frame && !empty($frame->main_image)) {
                        $typeLower = strtolower($frame->product_type ?: 'frame');
                        if (!empty($frame->parent_product_code)) {
                            $path = "uploads/{$typeLower}/product/{$frame->parent_product_code}/{$frame->main_image}";
                            if (file_exists(public_path($path))) {
                                $pObj->image = asset($path);
                            }
                        } else {
                            $pathWithId = "uploads/{$typeLower}/product/{$frame->product_id}/{$frame->main_image}";
                            if (file_exists(public_path($pathWithId))) {
                                $pObj->image = asset($pathWithId);
                            } elseif (file_exists(public_path($frame->main_image))) {
                                $pObj->image = asset($frame->main_image);
                            }
                        }
                    }
                }
                $orderProducts->push($pObj);
            }

            $orderObj = new \stdClass();
            $orderObj->id            = $bOrder->id;
            $orderObj->sale_id       = $bOrder->id;
            $orderObj->order_no      = $bOrder->order_number;
            $orderObj->order_status  = $bOrder->order_status;
            $orderObj->sales_status  = $statusCode;
            $orderObj->sale_date     = $bOrder->created_at->toDateString();
            $orderObj->created_at    = $bOrder->created_at;
            $orderObj->total_payable = (float) $bOrder->grand_total;
            $orderObj->delivery_method = $bOrder->delivery_method ?? 'Standard';
            $orderObj->tracking_number = $bOrder->tracking_number;
            $orderObj->products      = $orderProducts;

            $orders->push($orderObj);
        }

        // 2. Also check in-store POS walk-in purchases from tbl_sales not already loaded
        $legacySales = collect();
        if ($user) {
            $customer = DB::table('tbl_customer')
                ->where('customer_id', $user->id)
                ->orWhere('contact_no', $user->phone)
                ->first();

            $custId = $customer->customer_id ?? ($customer->id ?? $user->id);

            $legacySales = DB::table('tbl_sales')
                ->where(function($q) use ($user, $custId) {
                    $q->where('cust_id', $user->id)
                      ->orWhere('cust_id', $custId)
                      ->orWhere('added_by', $user->id);
                    if (!empty($user->phone)) {
                        $q->orWhere('contact_no', $user->phone);
                    }
                    if (!empty($user->email)) {
                        $q->orWhere('email_id', $user->email);
                    }
                })
                ->where(function($q) {
                    $q->where('is_deleted', 0)->orWhereNull('is_deleted');
                })
                ->orderBy('sale_id', 'desc')
                ->get();
        }

        foreach ($legacySales as $legSale) {
            if (!empty($legSale->order_no) && in_array($legSale->order_no, $seenOrderNos)) {
                continue; // Already processed from B2cOrder
            }

            $saleId = $legSale->sale_id ?? $legSale->id;
            $legSale->products = DB::table('tbl_sales_product')
                ->where('sale_id', $saleId)
                ->get();

            foreach ($legSale->products as $prod) {
                $prod->image = asset('website/assets/img/bg/Eyeglasses1.png');
                if (!empty($prod->product_id)) {
                    $frame = DB::table('tbl_product_code')->where('id', $prod->product_id)->first();
                    if (!$frame) {
                        $frame = DB::table('tbl_product_code')->where('product_id', $prod->product_id)->first();
                    }
                    if ($frame && !empty($frame->main_image)) {
                        $typeLower = strtolower($frame->product_type ?: 'frame');
                        if (!empty($frame->parent_product_code)) {
                            $path = "uploads/{$typeLower}/product/{$frame->parent_product_code}/{$frame->main_image}";
                            if (file_exists(public_path($path))) {
                                $prod->image = asset($path);
                            }
                        } else {
                            $pathWithId = "uploads/{$typeLower}/product/{$frame->product_id}/{$frame->main_image}";
                            if (file_exists(public_path($pathWithId))) {
                                $prod->image = asset($pathWithId);
                            } elseif (file_exists(public_path($frame->main_image))) {
                                $prod->image = asset($frame->main_image);
                            }
                        }
                    }
                }
            }

            $orders->push($legSale);
        }

        // Sort all combined orders by newest first
        $orders = $orders->sortByDesc(function ($o) {
            return $o->created_at ?? $o->sale_date ?? '2020-01-01';
        })->values();

        return view('website.order.my-order', compact('orders'));
    }

    /**
     * Cancel an active order (Syncs both B2cOrder and tbl_sales)
     */
    public function cancel_order(Request $request, $id)
    {
        // 1. Check B2cOrder
        $b2cOrder = \App\Models\b2c\B2cOrder::where('id', $id)
            ->orWhere('order_number', $id)
            ->first();

        if ($b2cOrder) {
            $b2cOrder->order_status = 'cancelled';
            $b2cOrder->admin_note   = ($b2cOrder->admin_note ? $b2cOrder->admin_note . ' | ' : '') . 'Cancelled by customer via My Orders';
            $b2cOrder->save();

            \App\Models\b2c\B2cOrderLog::create([
                'order_id'    => $b2cOrder->id,
                'user_id'     => auth()->id(),
                'action'      => 'order_cancelled',
                'from_status' => null,
                'to_status'   => 'cancelled',
                'notes'       => 'Cancelled by customer via web',
                'created_at'  => Carbon::now(),
            ]);

            // Sync to tbl_sales
            if (DB::getSchemaBuilder()->hasTable('tbl_sales')) {
                DB::table('tbl_sales')
                    ->where('order_no', $b2cOrder->order_number)
                    ->update([
                        'sales_status' => 3,
                        'updated_at'   => Carbon::now(),
                    ]);
            }

            return back()->with('success', 'Order ' . $b2cOrder->order_number . ' has been cancelled.');
        }

        // 2. Fallback to legacy tbl_sales
        $sale = DB::table('tbl_sales')->where('sale_id', $id)->first();
        if (!$sale) {
            $sale = DB::table('tbl_sales')->where('id', $id)->first();
        }

        if (!$sale) {
            return back()->with('error', 'Order not found.');
        }

        DB::table('tbl_sales')
            ->where('sale_id', $sale->sale_id ?? $sale->id)
            ->update([
                'sales_status' => 3,
                'updated_at'   => Carbon::now(),
            ]);

        return back()->with('success', 'Order ' . ($sale->order_no ?? '') . ' has been cancelled successfully.');
    }

    /**
     * Reorder products from a previous order
     */
    public function reorder(Request $request, $id)
    {
        $cartService = app(CartService::class);
        $added = 0;

        // 1. Check B2cOrder items first
        $b2cOrder = \App\Models\b2c\B2cOrder::with('items')->where('id', $id)->orWhere('order_number', $id)->first();
        if ($b2cOrder && $b2cOrder->items->isNotEmpty()) {
            foreach ($b2cOrder->items as $item) {
                if ($item->product_id) {
                    $cartService->addToCart(
                        $item->product_id,
                        $item->lens_package_id ?? null,
                        $item->quantity ?? 1,
                        $item->prescription_data ? json_encode($item->prescription_data) : null
                    );
                    $added++;
                }
            }
            return redirect()->route('cart')->with('success', 'Item(s) from order re-added to your cart!');
        }

        // 2. Check legacy tbl_sales_product
        $products = DB::table('tbl_sales_product')->where('sale_id', $id)->get();
        if ($products->isNotEmpty()) {
            foreach ($products as $prod) {
                if ($prod->product_id) {
                    $cartService->addToCart($prod->product_id, $prod->package_id ?? null, $prod->qty ?? 1, $prod->prescription_notes ?? null);
                    $added++;
                }
            }
            return redirect()->route('cart')->with('success', 'Item(s) from order re-added to your cart!');
        }

        return back()->with('error', 'Order items not found.');
    }
}


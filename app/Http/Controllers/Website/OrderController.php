<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

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
     * Step 3: My Orders page
     */
    public function my_order()
    {
        return view('website.order.my-order');
    }
}

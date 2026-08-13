<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login.web')->with('error', 'Please log in to view your profile.');
        }

        return view('website.profile.profile', compact('user'));
    }

    public function update_profile_image(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please log in to upload profile picture.'], 401);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'image.required' => 'Please select an image file to upload.',
            'image.image'    => 'Uploaded file must be a valid image.',
            'image.mimes'    => 'Supported image formats are JPG, PNG, GIF, and WEBP.',
            'image.max'      => 'Maximum allowed file size is 5MB.',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

            $websiteUploadPath = public_path('uploads/website/profile');
            if (!file_exists($websiteUploadPath)) {
                mkdir($websiteUploadPath, 0755, true);
            }
            $file->move($websiteUploadPath, $filename);

            $legacyUploadPath = public_path('uploads/profile');
            if (!file_exists($legacyUploadPath)) {
                mkdir($legacyUploadPath, 0755, true);
            }
            @copy($websiteUploadPath . '/' . $filename, $legacyUploadPath . '/' . $filename);

            $updateData = [];
            if (Schema::hasColumn('users', 'image')) {
                $updateData['image'] = $filename;
            }
            if (Schema::hasColumn('users', 'avatar')) {
                $updateData['avatar'] = $filename;
            }
            if (Schema::hasColumn('users', 'photo')) {
                $updateData['photo'] = $filename;
            }

            if (empty($updateData)) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('image')->nullable();
                });
                $updateData['image'] = $filename;
            }

            $updateData['updated_at'] = Carbon::now();
            DB::table('users')->where('id', $user->id)->update($updateData);

            return response()->json([
                'success'   => true,
                'message'   => 'Profile picture updated successfully!',
                'image_url' => asset('uploads/website/profile/' . $filename),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
    }

    public function account_information()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login.web')->with('error', 'Please log in to view account information.');
        }

        return view('website.profile.account-information', compact('user'));
    }

    public function update_account_information(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login.web')->with('error', 'Please log in to update account information.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20',
            'gender'     => 'nullable|string|in:male,female,other',
        ]);

        $fullName = trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''));

        $updateData = [
            'name'       => $fullName,
            'email'      => $validated['email'],
            'updated_at' => Carbon::now(),
        ];

        if (!empty($validated['phone'])) {
            $updateData['phone'] = $validated['phone'];
        }
        if (!empty($validated['gender'])) {
            $updateData['gender'] = $validated['gender'];
        }

        DB::table('users')->where('id', $user->id)->update($updateData);

        // Also update tbl_customer record if exists
        DB::table('tbl_customer')->where('customer_id', $user->id)->update([
            'cust_name'  => $fullName,
            'email_id'   => $validated['email'],
            'contact_no' => $validated['phone'] ?? $user->phone,
        ]);

        return redirect()->route('profile')->with('success', 'Account information updated successfully!');
    }

    public function manage_notification()
    {
        $user = auth()->user();
        return view('website.profile.manage-notification', compact('user'));
    }

    public function my_address()
    {
        $user = auth()->user();
        if (!$user) {
            $sessionList = session()->get('saved_addresses', []);
            $addresses = collect($sessionList)->map(function ($item) {
                return (object)$item;
            });
        } else {
            $addresses = UserAddress::where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return view('website.profile.my-address', compact('addresses'));
    }

    public function new_address()
    {
        return view('website.profile.add-new-address');
    }

    public function store_address(Request $request)
    {
        $validated = $request->validate([
            'full_name'     => 'nullable|string|max:255',
            'first_name'    => 'nullable|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'phone'         => 'required|digits:10',
            'pincode'       => 'required|digits:6',
            'house_no'      => 'nullable|string|max:500',
            'address_line_1'=> 'nullable|string|max:500',
            'road_area'     => 'nullable|string|max:500',
            'address_line_2'=> 'nullable|string|max:500',
            'landmark'      => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'state'         => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'address_type'  => 'nullable|string|max:50',
            'type'          => 'nullable|string|max:50',
            'is_default'    => 'nullable|boolean',
        ]);

        $fullName = $validated['full_name'] ?? trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        if (empty($fullName)) {
            $fullName = auth()->user()->name ?? 'Customer';
        }

        $houseNo  = $validated['house_no'] ?? ($validated['address_line_1'] ?? '');
        $roadArea = $validated['road_area'] ?? ($validated['address_line_2'] ?? ($validated['city'] ?? ''));
        $addressType = $validated['address_type'] ?? ($validated['type'] ?? 'Home');

        $fullAddress = $houseNo;
        if (!empty($roadArea)) {
            $fullAddress .= ', ' . $roadArea;
        }
        if (!empty($validated['landmark'])) {
            $fullAddress .= ', Near ' . $validated['landmark'];
        }
        if (!empty($validated['city'])) {
            $fullAddress .= ', ' . $validated['city'];
        }
        if (!empty($validated['state'])) {
            $fullAddress .= ', ' . $validated['state'];
        }
        $fullAddress .= ' - ' . $validated['pincode'];

        $user = auth()->user();

        if ($user) {
            if (!empty($validated['is_default'])) {
                UserAddress::where('user_id', $user->id)->update(['is_default' => false]);
            }

            UserAddress::create([
                'user_id'      => $user->id,
                'address_type' => $addressType,
                'full_name'    => $fullName,
                'phone'        => $validated['phone'],
                'pincode'      => $validated['pincode'],
                'house_no'     => $houseNo,
                'road_area'    => $roadArea,
                'landmark'     => $validated['landmark'] ?? null,
                'email'        => $validated['email'] ?? null,
                'full_address' => $fullAddress,
                'is_default'   => !empty($validated['is_default']),
            ]);
        } else {
            $sessionList = session()->get('saved_addresses', []);
            $tempId = 'guest_' . time();
            $validated['id'] = $tempId;
            $validated['full_name'] = $fullName;
            $validated['full_address'] = $fullAddress;
            $validated['address_type'] = $addressType;
            $sessionList[] = $validated;
            session()->put('saved_addresses', $sessionList);
        }

        return redirect()->route('my-addresses')->with('success', 'Address added successfully!');
    }

    public function edit_address($id)
    {
        $user = auth()->user();
        if ($user) {
            $address = UserAddress::where('id', $id)->where('user_id', $user->id)->first();
        } else {
            $sessionList = session()->get('saved_addresses', []);
            $address = null;
            foreach ($sessionList as $item) {
                if (isset($item['id']) && $item['id'] == $id) {
                    $address = (object)$item;
                    break;
                }
            }
        }

        if (!$address) {
            return redirect()->route('my-addresses')->with('error', 'Address not found.');
        }

        return view('website.profile.edit-address', compact('address'));
    }

    public function update_address(Request $request, $id)
    {
        $validated = $request->validate([
            'full_name'     => 'nullable|string|max:255',
            'first_name'    => 'nullable|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'phone'         => 'required|digits:10',
            'pincode'       => 'required|digits:6',
            'house_no'      => 'nullable|string|max:500',
            'address_line_1'=> 'nullable|string|max:500',
            'road_area'     => 'nullable|string|max:500',
            'address_line_2'=> 'nullable|string|max:500',
            'landmark'      => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'state'         => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'address_type'  => 'nullable|string|max:50',
            'type'          => 'nullable|string|max:50',
            'is_default'    => 'nullable|boolean',
        ]);

        $fullName = $validated['full_name'] ?? trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        if (empty($fullName)) {
            $fullName = auth()->user()->name ?? 'Customer';
        }

        $houseNo  = $validated['house_no'] ?? ($validated['address_line_1'] ?? '');
        $roadArea = $validated['road_area'] ?? ($validated['address_line_2'] ?? ($validated['city'] ?? ''));
        $addressType = $validated['address_type'] ?? ($validated['type'] ?? 'Home');

        $fullAddress = $houseNo;
        if (!empty($roadArea)) {
            $fullAddress .= ', ' . $roadArea;
        }
        if (!empty($validated['landmark'])) {
            $fullAddress .= ', Near ' . $validated['landmark'];
        }
        if (!empty($validated['city'])) {
            $fullAddress .= ', ' . $validated['city'];
        }
        if (!empty($validated['state'])) {
            $fullAddress .= ', ' . $validated['state'];
        }
        $fullAddress .= ' - ' . $validated['pincode'];

        $user = auth()->user();

        if ($user) {
            $addr = UserAddress::where('id', $id)->where('user_id', $user->id)->first();
            if ($addr) {
                if (!empty($validated['is_default'])) {
                    UserAddress::where('user_id', $user->id)->update(['is_default' => false]);
                }

                $addr->update([
                    'address_type' => $addressType,
                    'full_name'    => $fullName,
                    'phone'        => $validated['phone'],
                    'pincode'      => $validated['pincode'],
                    'house_no'     => $houseNo,
                    'road_area'    => $roadArea,
                    'landmark'     => $validated['landmark'] ?? null,
                    'email'        => $validated['email'] ?? null,
                    'full_address' => $fullAddress,
                    'is_default'   => !empty($validated['is_default']),
                ]);
            }
        }

        return redirect()->route('my-addresses')->with('success', 'Address updated successfully!');
    }

    public function delete_address($id)
    {
        $user = auth()->user();

        if ($user) {
            UserAddress::where('id', $id)->where('user_id', $user->id)->delete();
        } else {
            $sessionList = session()->get('saved_addresses', []);
            $newList = array_filter($sessionList, function ($item) use ($id) {
                return (isset($item['id']) && $item['id'] != $id);
            });
            session()->put('saved_addresses', array_values($newList));
        }

        return redirect()->route('my-addresses')->with('success', 'Address deleted successfully!');
    }

    public function set_default_address($id)
    {
        $user = auth()->user();

        if ($user) {
            UserAddress::where('user_id', $user->id)->update(['is_default' => false]);
            UserAddress::where('id', $id)->where('user_id', $user->id)->update(['is_default' => true]);
        }

        return redirect()->route('my-addresses')->with('success', 'Default address updated successfully!');
    }
}

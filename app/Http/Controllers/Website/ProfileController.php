<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(){
        $user = auth()->user();
         return view('website.profile.profile',compact('user'));
    }

    public function update_profile_image(Request $request){
        return response()->json(['success' => true, 'message' => 'Profile image updated successfully!']);
    }

    public function account_information(){
         return view('website.web.profile.account-information');
    }

    public function update_account_information(Request $request){
        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    public function manage_notification(){
         return view('website.web.profile.manage-notification');
    }

    public function my_address(){
         return view('website.web.profile.my-address');
    }

    public function new_address(){
         return view('website.web.profile.add-new-address');
    }

    public function store_address(Request $request){
        return redirect()->route('my_address')->with('success', 'Address added successfully!');
    }

    public function edit_address($id){
        return view('website.web.profile.edit-address');
    }

    public function update_address(Request $request, $id){
        return redirect()->route('my_address')->with('success', 'Address updated successfully!');
    }

    public function delete_address($id){
        return redirect()->route('my_address')->with('success', 'Address deleted successfully!');
    }

    public function set_default_address($id){
        return redirect()->route('my_address')->with('success', 'Default address updated!');
    }
}

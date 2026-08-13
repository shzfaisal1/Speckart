<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function add_power(){
         return view('website.web.prescription.add-power');
    }
    public function saved_prescription(){
         return view('website.web.prescription.add-power-saved-prescription');
    }
    public function prescription_manually(){
         return view('website.web.prescription.add-power-prescription-manually');
    }
    public function my_prescription(){
         return view('website.web.prescription.my-prescription');
    }
}

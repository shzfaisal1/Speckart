<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function add_power()
    {
        return view('website.prescription.add-power');
    }

    public function saved_prescription()
    {
        return view('website.prescription.add-power-saved-prescription');
    }

    public function prescription_manually()
    {
        return view('website.prescription.add-power-prescription-manually');
    }

    public function my_prescription()
    {
        return view('website.prescription.my-prescription');
    }
}

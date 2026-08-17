<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\UserPrescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

class PrescriptionController extends Controller
{
    /**
     * Ensure user_prescriptions database table exists
     */
    protected function ensureTableExists()
    {
        if (!Schema::hasTable('user_prescriptions')) {
            Schema::create('user_prescriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('session_id')->nullable()->index();
                $table->string('prescription_name')->default('My Prescription');
                $table->string('power_type')->default('Single Vision');
                $table->string('rx_file')->nullable();
                $table->string('r_sph')->nullable();
                $table->string('r_cyl')->nullable();
                $table->string('r_axis')->nullable();
                $table->string('r_add')->nullable();
                $table->string('l_sph')->nullable();
                $table->string('l_cyl')->nullable();
                $table->string('l_axis')->nullable();
                $table->string('l_add')->nullable();
                $table->string('pd')->nullable();
                $table->text('remarks')->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }
    }

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

    /**
     * Display My Prescriptions
     */
    public function my_prescription()
    {
        $this->ensureTableExists();
        $user = auth()->user();

        if ($user) {
            $prescriptions = UserPrescription::where('user_id', $user->id)
                ->latest()
                ->get();
        } else {
            $sessId = session()->getId();
            $prescriptions = UserPrescription::where('session_id', $sessId)
                ->latest()
                ->get();
        }

        // Also fetch clinic eye tests if user is logged in
        $eyeTests = collect();
        if ($user && !empty($user->phone)) {
            $eyeTests = DB::table('tbl_eye_test')
                ->where('contact_no', $user->phone)
                ->latest('test_id')
                ->get();
        }

        return view('website.prescription.my-prescription', compact('prescriptions', 'eyeTests', 'user'));
    }

    /**
     * Upload doctor prescription slip image/PDF
     */
    public function upload_prescription(Request $request)
    {
        $this->ensureTableExists();

        $request->validate([
            'prescription_name' => 'nullable|string|max:255',
            'power_type'        => 'nullable|string|max:100',
            'rx_file'           => 'required|file|mimes:jpeg,jpg,png,pdf,webp|max:10240',
        ], [
            'rx_file.required' => 'Please select a prescription photo or PDF file.',
            'rx_file.mimes'    => 'File must be an image (JPG, PNG, WEBP) or PDF document.',
            'rx_file.max'      => 'File size cannot exceed 10MB.',
        ]);

        $file = $request->file('rx_file');
        $fileName = time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
        $uploadPath = public_path('uploads/prescriptions');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $file->move($uploadPath, $fileName);

        $user = auth()->user();
        $rxName = $request->input('prescription_name') ?: 'Prescription Slip (' . Carbon::now()->format('d M Y') . ')';

        UserPrescription::create([
            'user_id'           => $user->id ?? null,
            'session_id'        => session()->getId(),
            'prescription_name' => $rxName,
            'power_type'        => $request->input('power_type', 'Single Vision'),
            'rx_file'           => 'uploads/prescriptions/' . $fileName,
            'remarks'           => $request->input('remarks'),
        ]);

        return redirect()->route('my-prescriptions')->with('success', 'Prescription uploaded successfully!');
    }

    /**
     * Save eye power parameters manually
     */
    public function save_manual_prescription(Request $request)
    {
        $this->ensureTableExists();

        $validated = $request->validate([
            'prescription_name' => 'nullable|string|max:255',
            'power_type'        => 'required|string|max:100',
            'r_sph'             => 'nullable|string|max:20',
            'r_cyl'             => 'nullable|string|max:20',
            'r_axis'            => 'nullable|string|max:20',
            'r_add'             => 'nullable|string|max:20',
            'l_sph'             => 'nullable|string|max:20',
            'l_cyl'             => 'nullable|string|max:20',
            'l_axis'            => 'nullable|string|max:20',
            'l_add'             => 'nullable|string|max:20',
            'pd'                => 'nullable|string|max:20',
            'remarks'           => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $rxName = $validated['prescription_name'] ?: 'Power Prescription (' . Carbon::now()->format('d M Y') . ')';

        UserPrescription::create([
            'user_id'           => $user->id ?? null,
            'session_id'        => session()->getId(),
            'prescription_name' => $rxName,
            'power_type'        => $validated['power_type'],
            'r_sph'             => $validated['r_sph'] ?? null,
            'r_cyl'             => $validated['r_cyl'] ?? null,
            'r_axis'            => $validated['r_axis'] ?? null,
            'r_add'             => $validated['r_add'] ?? null,
            'l_sph'             => $validated['l_sph'] ?? null,
            'l_cyl'             => $validated['l_cyl'] ?? null,
            'l_axis'            => $validated['l_axis'] ?? null,
            'l_add'             => $validated['l_add'] ?? null,
            'pd'                => $validated['pd'] ?? null,
            'remarks'           => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('my-prescriptions')->with('success', 'Eye power prescription saved successfully!');
    }

    /**
     * Delete a saved prescription
     */
    public function delete_prescription($id)
    {
        $this->ensureTableExists();
        $user = auth()->user();

        if ($user) {
            $rx = UserPrescription::where('id', $id)->where('user_id', $user->id)->first();
        } else {
            $rx = UserPrescription::where('id', $id)->where('session_id', session()->getId())->first();
        }

        if ($rx) {
            if (!empty($rx->rx_file) && file_exists(public_path($rx->rx_file))) {
                @unlink(public_path($rx->rx_file));
            }
            $rx->delete();
            return redirect()->route('my-prescriptions')->with('success', 'Prescription deleted successfully.');
        }

        return redirect()->route('my-prescriptions')->with('error', 'Prescription not found.');
    }
}

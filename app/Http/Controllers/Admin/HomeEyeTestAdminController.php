<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeEyeTestAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeEyeTestAdminController extends Controller
{
    private function ensureTableExists()
    {
        if (!Schema::hasTable('home_eye_test_appointments')) {
            Schema::create('home_eye_test_appointments', function (Blueprint $table) {
                $table->id();
                $table->string('booking_id', 50)->unique();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name', 100);
                $table->string('phone', 20);
                $table->string('email', 100)->nullable();
                $table->string('pincode', 10);
                $table->string('city', 100);
                $table->string('state', 100)->nullable();
                $table->text('address');
                $table->string('landmark', 255)->nullable();
                $table->date('appointment_date');
                $table->string('time_slot', 50);
                $table->integer('people_count')->default(1);
                $table->decimal('fee', 10, 2)->default(99.00);
                $table->string('payment_method', 50)->default('pay_on_visit');
                $table->string('payment_status', 50)->default('pending');
                $table->string('status', 50)->default('confirmed');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Display a listing of Home Eye Test appointments in the Admin Panel.
     */
    public function index(Request $request)
    {
        $this->ensureTableExists();

        $page_title  = 'Home Eye Test Appointments';
        $breadcrumbs = [
            ['link' => route('index'), 'name' => 'Home'],
            ['name' => 'Home Eye Test Appointments'],
        ];

        // Overall stats
        $stats = [
            'total'     => HomeEyeTestAppointment::count(),
            'confirmed' => HomeEyeTestAppointment::where('status', 'confirmed')->count(),
            'completed' => HomeEyeTestAppointment::where('status', 'completed')->count(),
            'cancelled' => HomeEyeTestAppointment::where('status', 'cancelled')->count(),
            'revenue'   => HomeEyeTestAppointment::where('payment_status', 'paid')->sum('fee'),
        ];

        // Query with filters
        $query = HomeEyeTestAppointment::query()->with('user')->orderBy('appointment_date', 'desc')->orderBy('created_at', 'desc');

        // Status Filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Payment Status Filter
        if ($request->filled('payment_status') && $request->input('payment_status') !== 'all') {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->input('date'));
        }

        // Omni Search (Booking ID, Name, Phone, Email, Pincode, City)
        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('booking_id', 'like', "%{$term}%")
                  ->orWhere('name', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('pincode', 'like', "%{$term}%")
                  ->orWhere('city', 'like', "%{$term}%");
            });
        }

        $appointments = $query->paginate(15)->appends($request->all());

        return view('admin.home_eye_test.index', compact('page_title', 'breadcrumbs', 'stats', 'appointments'));
    }

    /**
     * Get JSON details of a single appointment for modal view.
     */
    public function show($id)
    {
        $appointment = HomeEyeTestAppointment::with('user')->findOrFail($id);

        return response()->json([
            'status'      => 'success',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Update appointment status and notes.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'         => 'required|string|in:confirmed,assigned,completed,cancelled',
            'payment_status' => 'nullable|string|in:pending,paid',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $appointment = HomeEyeTestAppointment::findOrFail($id);

        $updateData = [
            'status' => $request->input('status'),
            'notes'  => $request->input('notes'),
        ];

        if ($request->filled('payment_status')) {
            $updateData['payment_status'] = $request->input('payment_status');
        }

        $appointment->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Appointment #' . $appointment->booking_id . ' status updated successfully!',
            ]);
        }

        return redirect()->back()->with('success', 'Appointment #' . $appointment->booking_id . ' status updated successfully!');
    }

    /**
     * Delete an appointment.
     */
    public function destroy($id)
    {
        $appointment = HomeEyeTestAppointment::findOrFail($id);
        $bookingId   = $appointment->booking_id;
        $appointment->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment #' . $bookingId . ' deleted successfully.',
        ]);
    }
}

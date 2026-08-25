<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeEyeTestAppointment;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HomeEyeTestController extends Controller
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

    public function index()
    {
        $this->ensureTableExists();
        $user = auth()->user();
        $savedAddresses = [];
        if ($user) {
            $savedAddresses = UserAddress::where('user_id', $user->id)->get();
        }

        // Generate next 7 available dates
        $availableDates = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i);
            $availableDates[] = [
                'full_date' => $date->format('Y-m-d'),
                'day_name' => $i === 0 ? 'Today' : ($i === 1 ? 'Tomorrow' : $date->format('D')),
                'date_num' => $date->format('d M'),
                'is_available' => true,
            ];
        }

        // Available standard time slots
        $timeSlots = [
            '10:00 AM - 12:00 PM',
            '12:00 PM - 02:00 PM',
            '02:00 PM - 04:00 PM',
            '04:00 PM - 06:00 PM',
            '06:00 PM - 08:00 PM',
        ];

        return view('website.home-eye-test.index', compact('user', 'savedAddresses', 'availableDates', 'timeSlots'));
    }

    public function book(Request $request)
    {
        $this->ensureTableExists();
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'pincode' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'address' => 'required|string',
            'landmark' => 'nullable|string|max:255',
            'appointment_date' => 'required|date',
            'time_slot' => 'required|string|max:50',
            'people_count' => 'required|integer|min:1|max:5',
            'payment_method' => 'required|string|in:pay_on_visit,online',
        ]);

        $bookingId = 'HT-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $feePerPerson = 99.00;
        $totalFee = $feePerPerson * (int)$request->people_count;

        $appointment = HomeEyeTestAppointment::create([
            'booking_id' => $bookingId,
            'user_id' => auth()->id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
            'address' => $request->address,
            'landmark' => $request->landmark,
            'appointment_date' => $request->appointment_date,
            'time_slot' => $request->time_slot,
            'people_count' => $request->people_count,
            'fee' => $totalFee,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_method === 'online' ? 'paid' : 'pending',
            'status' => 'confirmed',
            'notes' => $request->notes,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment booked successfully!',
                'booking_id' => $appointment->booking_id,
                'redirect_url' => route('home-eye-test.confirmation', $appointment->booking_id),
            ]);
        }

        return redirect()->route('home-eye-test.confirmation', $appointment->booking_id)
            ->with('success', 'Your Home Eye-Test appointment has been booked successfully!');
    }

    public function confirmation($booking_id)
    {
        $appointment = HomeEyeTestAppointment::where('booking_id', $booking_id)->firstOrFail();

        return view('website.home-eye-test.confirmation', compact('appointment'));
    }

    public function myAppointments()
    {
        if (!auth()->check()) {
            return redirect()->route('login.web');
        }

        $appointments = HomeEyeTestAppointment::where('user_id', auth()->id())
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);

        return view('website.home-eye-test.my-appointments', compact('appointments'));
    }
}

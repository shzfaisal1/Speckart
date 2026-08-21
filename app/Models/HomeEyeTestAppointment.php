<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeEyeTestAppointment extends Model
{
    use HasFactory;

    protected $table = 'home_eye_test_appointments';

    protected $fillable = [
        'booking_id',
        'user_id',
        'name',
        'phone',
        'email',
        'pincode',
        'city',
        'state',
        'address',
        'landmark',
        'appointment_date',
        'time_slot',
        'people_count',
        'fee',
        'payment_method',
        'payment_status',
        'status',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

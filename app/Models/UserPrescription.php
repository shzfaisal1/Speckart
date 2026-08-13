<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPrescription extends Model
{
    use HasFactory;

    protected $table = 'user_prescriptions';

    protected $fillable = [
        'user_id',
        'session_id',
        'prescription_name',
        'power_type',
        'rx_file',
        'r_sph',
        'r_cyl',
        'r_axis',
        'r_add',
        'l_sph',
        'l_cyl',
        'l_axis',
        'l_add',
        'pd',
        'remarks',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

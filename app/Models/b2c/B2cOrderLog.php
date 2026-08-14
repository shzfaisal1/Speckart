<?php

namespace App\Models\b2c;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2cOrderLog extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'b2c_order_logs';

    protected $fillable = [
        'order_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(B2cOrder::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}

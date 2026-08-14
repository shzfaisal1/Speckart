<?php

namespace App\Models\b2c;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2cOrderNote extends Model
{
    use HasFactory;

    protected $table = 'b2c_order_notes';

    protected $fillable = [
        'order_id',
        'user_id',
        'note',
        'is_customer_visible',
    ];

    protected $casts = [
        'is_customer_visible' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(B2cOrder::class, 'order_id');
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}

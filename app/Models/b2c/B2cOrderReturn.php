<?php

namespace App\Models\b2c;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2cOrderReturn extends Model
{
    use HasFactory;

    protected $table = 'b2c_order_returns';

    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'return_type',
        'reason',
        'exchange_type',
        'status',
        'admin_notes',
        'warranty_claim',
    ];

    protected $casts = [
        'warranty_claim' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(B2cOrder::class, 'order_id');
    }

    public function item()
    {
        return $this->belongsTo(B2cOrderItem::class, 'order_item_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}

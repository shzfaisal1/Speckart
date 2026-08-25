<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCharge extends Model
{
    use HasFactory;

    protected $table = 'tbl_shipping_charges';

    protected $fillable = [
        'pincode',
        'amount',
        'is_cod_available',
        'status',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'is_cod_available' => 'integer',
        'status'           => 'integer',
    ];

    /**
     * Scope for active / enabled pincodes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Helper to get shipping charge and COD availability for a given pincode
     *
     * @param string|null $pincode
     * @return array ['is_serviceable' => bool, 'amount' => float, 'is_cod_available' => bool, 'message' => string]
     */
    public static function getChargeForPincode(?string $pincode): array
    {
        if (empty($pincode)) {
            return [
                'is_serviceable'   => true,
                'amount'           => 0.00,
                'is_cod_available' => true,
                'status'           => 1,
                'message'          => 'Free Delivery',
            ];
        }

        $cleanPincode = trim($pincode);
        $record = self::where('pincode', $cleanPincode)->first();

        if (!$record) {
            // Unlisted pincode -> default free / standard shipping with COD enabled
            return [
                'is_serviceable'   => true,
                'amount'           => 0.00,
                'is_cod_available' => true,
                'status'           => 1,
                'message'          => 'Standard Free Delivery',
            ];
        }

        if ($record->status == 0) {
            return [
                'is_serviceable'   => false,
                'amount'           => 0.00,
                'is_cod_available' => false,
                'status'           => 0,
                'message'          => "Delivery is currently unavailable to pincode {$cleanPincode}.",
            ];
        }

        $isCod = (bool) ($record->is_cod_available ?? 1);

        return [
            'is_serviceable'   => true,
            'amount'           => (float) $record->amount,
            'is_cod_available' => $isCod,
            'status'           => 1,
            'message'          => $record->amount > 0 ? ('₹' . number_format($record->amount, 2)) : 'Free Delivery',
        ];
    }
}

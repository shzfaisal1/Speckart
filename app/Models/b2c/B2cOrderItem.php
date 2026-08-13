<?php

namespace App\Models\b2c;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2cOrderItem extends Model
{
    use HasFactory;

    protected $table = 'b2c_order_items';

    protected $fillable = [
        'order_id',
        // Product snapshot
        'product_id',
        'product_code',
        'product_name',
        'product_type',
        'barcode',
        // Pricing
        'qty',
        'base_price',
        'sale_price',
        'discount_amt',
        'total_price',
        'hsn_code',
        'gst',
        'gst_amount',
        // Lens Package
        'lens_package_id',
        'lens_package_price',
        'coating_apply',
        // Right Eye - Distance
        'GL_EYE_RS_D',
        'GL_EYE_RC_D',
        'GL_EYE_RA_D',
        'GL_EYE_RP_D',
        'GL_EYE_RV_D',
        // Right Eye - Near
        'GL_EYE_RS_N',
        'GL_EYE_RC_N',
        'GL_EYE_RA_N',
        'GL_EYE_RP_N',
        'GL_EYE_RV_N',
        'GL_EYE_RADD',
        // Left Eye - Distance
        'GL_EYE_LS_D',
        'GL_EYE_LC_D',
        'GL_EYE_LA_D',
        'GL_EYE_LP_D',
        'GL_EYE_LV_D',
        // Left Eye - Near
        'GL_EYE_LS_N',
        'GL_EYE_LC_N',
        'GL_EYE_LA_N',
        'GL_EYE_LP_N',
        'GL_EYE_LV_N',
        'GL_EYE_LADD',
        // PD
        'GL_EYE_totalPD',
        'prescription_notes',
        // Contact Lens
        'wearing_type',
        'lensRightNoOfBoxes',
        'lensRightTotalPieces',
        'lensLeftNoOfBoxes',
        'lensLeftTotalPieces',
        // Status
        'item_status',
        'cancellation_reason',
    ];

    protected $casts = [
        'base_price'         => 'decimal:2',
        'sale_price'         => 'decimal:2',
        'discount_amt'       => 'decimal:2',
        'total_price'        => 'decimal:2',
        'gst'                => 'decimal:2',
        'gst_amount'         => 'decimal:2',
        'lens_package_price' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(B2cOrder::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\product\Product::class, 'product_id');
    }

    public function lensPackage()
    {
        return $this->belongsTo(\App\Models\LensPackage::class, 'lens_package_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Check if this item has a prescription associated.
     */
    public function hasPrescription(): bool
    {
        return !is_null($this->GL_EYE_RS_D) || !is_null($this->GL_EYE_LS_D);
    }
}

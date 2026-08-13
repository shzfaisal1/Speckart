<?php

namespace App\Models\b2c;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_code',
        'product_type',
        'qty',
        'unit_price',
        'sale_price',
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
    ];

    protected $casts = [
        'unit_price'         => 'decimal:2',
        'sale_price'         => 'decimal:2',
        'lens_package_price' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
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
     * Get item total price (including lens package price).
     */
    public function getItemTotalAttribute(): float
    {
        return ($this->sale_price + $this->lens_package_price) * $this->qty;
    }
}

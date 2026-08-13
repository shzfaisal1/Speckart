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
        'GL_EYE_RS_D',
        'GL_EYE_RC_D',
        'GL_EYE_RA_D',
        'GL_EYE_RP_D',
        'GL_EYE_RV_D',
        'GL_EYE_RS_N',
        'GL_EYE_RC_N',
        'GL_EYE_RA_N',
        'GL_EYE_RP_N',
        'GL_EYE_RV_N',
        'GL_EYE_RADD',
        'GL_EYE_LS_D',
        'GL_EYE_LC_D',
        'GL_EYE_LA_D',
        'GL_EYE_LP_D',
        'GL_EYE_LV_D',
        'GL_EYE_LS_N',
        'GL_EYE_LC_N',
        'GL_EYE_LA_N',
        'GL_EYE_LP_N',
        'GL_EYE_LV_N',
        'GL_EYE_LADD',
        'GL_EYE_totalPD',
        'prescription_notes',
        'wearing_type',
        'lensRightNoOfBoxes',
        'lensRightTotalPieces',
        'lensLeftNoOfBoxes',
        'lensLeftTotalPieces',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }
}

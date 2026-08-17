<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlists';

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * The user who wishlisted this product.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The product that was wishlisted.
     * Points to tbl_product_code using product_id.
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\product\Product::class, 'product_id', 'product_id');
    }
}

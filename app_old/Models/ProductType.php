<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    
    protected $table = "product_type_masters";

    protected $fillable = ['name','slug','subtitle','icon','has_power','default_powers','is_active'];

    protected $casts = [
    'default_powers' => 'array',
    ];


}

<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_tax';

    protected $fillable =
    [
        'id', 'product_type','hsn_code', 'percentage', 'description', 'set_default','status','added_by','store_id','created_at','updated_at','updated_by'
    ];
}
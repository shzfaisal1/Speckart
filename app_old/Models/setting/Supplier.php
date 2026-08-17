<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_suppliers';

    protected $fillable =
    [
        'id', 'supplier_company','supplier_id', 'contact_name', 'contact_no', 'gst_no', 'state','status','added_by','store_id','created_at','updated_at','updated_by'
    ];
}
<?php

namespace App\Models\inventory;

use Illuminate\Database\Eloquent\Model;

class BarcodeTrack extends Model
{
    protected $table = 'tbl_barcode_track_record';

    protected $fillable = [
        'id',
        'barcode_no',
        'store_id',
        'reference_type',
        'action_perform',
        'added_by',
        'created_at'
    ];

    public $timestamps = true; // set false if table has no timestamps
}
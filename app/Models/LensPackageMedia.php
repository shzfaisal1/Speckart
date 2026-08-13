<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LensPackageMedia extends Model
{
    protected $table = 'lens_package_media';

    /** This table only has a created_at column (no updated_at). */
    const UPDATED_AT = null;

    protected $fillable = [
        'lens_package_id', 'media_type', 'url', 'alt_text', 'sort_order',
    ];

    // ── Relationships ──

    public function lensPackage()
    {
        return $this->belongsTo(LensPackage::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LensPackageBadge extends Model
{
    protected $table = 'lens_package_badges';

    /** Badges are simple child records — no timestamp columns. */
    public $timestamps = false;

    protected $fillable = [
        'lens_package_id', 'label', 'bg_color', 'text_color', 'sort_order',
    ];

    // ── Relationships ──

    public function lensPackage()
    {
        return $this->belongsTo(LensPackage::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LensBenefit extends Model
{
    protected $table = 'lens_benefits';

    /** Benefits are a lookup table — no created_at / updated_at columns. */
    public $timestamps = false;

    protected $fillable = [
        'name', 'description', 'icon_emoji', 'icon_image', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ──

    public function lensPackages()
    {
        return $this->belongsToMany(
            LensPackage::class,
            'lens_package_benefits',
            'benefit_id',
            'lens_package_id'
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LensPackageTag extends Model
{
    protected $table = 'lens_package_tags';

    /** Tags are a lookup table — no created_at / updated_at columns. */
    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'icon_url', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ──

    public function lensPackages()
    {
        return $this->belongsToMany(
            LensPackage::class,
            'lens_package_tag_map',
            'tag_id',
            'lens_package_id'
        );
    }
}

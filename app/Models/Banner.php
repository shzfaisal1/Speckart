<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = [
        'title',
        'image_path',
        'link_type',
        'link_id',
        'custom_url',
        'position',
        'sort_order',
        'is_active',
        'store_id',
        'added_by',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Relationship to Offer if link_type is 'offer'
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'link_id');
    }
}

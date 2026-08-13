<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = ['name', 'slug', 'image', 'description', 'is_active', 'added_by', 'store_id', 'allowed_filters'];

    protected $casts = [
        'is_active' => 'boolean',
        'allowed_filters' => 'array',
    ];

    /**
     * A category has many subcategories.
     */
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
}

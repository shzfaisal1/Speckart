<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subcategories';

    protected $fillable = ['category_id', 'name', 'slug', 'image', 'description', 'is_active', 'added_by', 'store_id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * A subcategory belongs to a category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

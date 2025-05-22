<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sneaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'brand_id',
        'category_id',
        'description',
        'price',
        'in_stock',
        'images',
        'sizes',
        'colors',
        'is_favorite',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'in_stock' => 'boolean',
        'images' => 'array',
        'sizes' => 'array',
        'colors' => 'array',
        'is_favorite' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = [
        'name', 'duration', 'route_description', 'destinations',
        'price_usd', 'is_active', 'image_path', 'sort_order',
    ];

    protected $casts = [
        'destinations' => 'array',
        'price_usd'    => 'float',
        'is_active'    => 'boolean',
    ];

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return str_starts_with($this->image_path, 'http')
                ? $this->image_path
                : asset('storage/' . $this->image_path);
        }
        return 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=85';
    }

    public function getPriceFormattedAttribute(): string
    {
        return '$' . number_format($this->price_usd, 0) . ' USD';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

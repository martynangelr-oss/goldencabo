<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'name', 'description', 'services', 'passengers',
        'is_available', 'image_path', 'sort_order',
    ];

    protected $casts = [
        'services'     => 'array',
        'is_available' => 'boolean',
    ];

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return str_starts_with($this->image_path, 'http')
                ? $this->image_path
                : asset('storage/' . $this->image_path);
        }
        return 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800&q=85';
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}

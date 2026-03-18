<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Zone extends Model
{
    protected $fillable = [
        'number', 'name', 'area', 'round_trip_price', 'one_way_price',
        'travel_time', 'is_active', 'sort_order', 'image_path', 'image_path_secondary',
    ];

    /** Default fallback images per zone number */
    private static array $defaults = [
        1 => ['https://images.unsplash.com/photo-1512813195386-6cf811ad3542?w=900&q=85','https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&q=80'],
        2 => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?w=900&q=85','https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80'],
        3 => ['https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=900&q=85','https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&q=80'],
        4 => ['https://images.unsplash.com/photo-1501854140801-50d01698950b?w=900&q=85','https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=600&q=80'],
    ];

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return str_starts_with($this->image_path, 'http')
                ? $this->image_path
                : Storage::disk('public')->url($this->image_path);
        }
        return self::$defaults[$this->number][0]
            ?? 'https://images.unsplash.com/photo-1512813195386-6cf811ad3542?w=900&q=85';
    }

    public function getImageSecondaryUrlAttribute(): string
    {
        if ($this->image_path_secondary) {
            return str_starts_with($this->image_path_secondary, 'http')
                ? $this->image_path_secondary
                : Storage::disk('public')->url($this->image_path_secondary);
        }
        return self::$defaults[$this->number][1]
            ?? 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80';
    }

    protected $casts = [
        'round_trip_price' => 'float',
        'one_way_price'    => 'float',
        'is_active'        => 'boolean',
    ];

    public function hotels()
    {
        return $this->hasMany(Hotel::class)->orderBy('sort_order')->orderBy('name');
    }

    public function activeHotels()
    {
        return $this->hasMany(Hotel::class)->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

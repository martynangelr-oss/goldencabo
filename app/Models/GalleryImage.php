<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    protected $fillable = ['caption', 'image_path', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return 'https://images.unsplash.com/photo-1512813195386-6cf811ad3542?w=1000&q=85';
        }
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        return Storage::disk('public')->url($this->image_path);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

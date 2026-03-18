<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SectionImage extends Model
{
    protected $fillable = [
        'section', 'title', 'subtitle', 'caption',
        'image_path', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return 'https://images.unsplash.com/photo-1512813195386-6cf811ad3542?w=800&q=85';
        }
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        return Storage::disk('public')->url($this->image_path);
    }

    public function getSectionLabelAttribute(): string
    {
        return match($this->section) {
            'about'   => 'Acerca de Nosotros',
            'airport' => 'Servicio Aeropuerto',
            default   => $this->section,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

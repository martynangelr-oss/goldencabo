<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = ['zone_id', 'name', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

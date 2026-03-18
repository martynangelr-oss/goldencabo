<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Get a setting value by key, with optional default. */
    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::remember("setting_{$key}", 600, function () use ($key, $default) {
            $s = static::where('key', $key)->first();
            return $s ? $s->value : $default;
        });
    }

    /** Set (upsert) a setting value. */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    /** Return public URL for a setting that stores a file path. */
    public static function fileUrl(string $key): ?string
    {
        $val = static::get($key);
        if (!$val) return null;
        if (str_starts_with($val, 'http')) return $val;
        return Storage::disk('public')->url($val);
    }
}

<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Support\AuditLogger;

class SectionImageService
{
    public const SLOTS = [
        'about_img_main' => [
            'label' => 'Imagen Principal',
            'section' => 'about',
            'hint' => '800 × 600 px recomendado (relación 4:3)',
            'default' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=85',
        ],
        'about_img_secondary' => [
            'label' => 'Imagen Secundaria',
            'section' => 'about',
            'hint' => '600 × 450 px recomendado (relación 4:3)',
            'default' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80',
        ],
        'airport_img_main' => [
            'label' => 'Imagen Principal',
            'section' => 'airport',
            'hint' => '900 × 600 px recomendado (relación 3:2)',
            'default' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=900&q=85',
        ],
    ];

    public function slotsWithMeta(): array
    {
        $slots = [];
        foreach (self::SLOTS as $key => $meta) {
            $stored = SiteSetting::get($key);
            $slots[$key] = array_merge($meta, [
                'key' => $key,
                'url' => $stored
                    ? (str_starts_with($stored, 'http') ? $stored : asset('storage/'.$stored))
                    : $meta['default'],
                'is_custom' => (bool) $stored,
            ]);
        }

        return $slots;
    }

    public function urlFor(string $key): string
    {
        return SiteSetting::fileUrl($key) ?? self::SLOTS[$key]['default'];
    }

    public function currentPath(string $slot): ?string
    {
        return SiteSetting::get($slot);
    }

    public function updateSlot(string $slot, string $path): void
    {
        SiteSetting::set($slot, $path);
        AuditLogger::record("section-image.update:{$slot}");
    }

    public function restoreSlot(string $slot): void
    {
        SiteSetting::set($slot, null);
        AuditLogger::record("section-image.restore:{$slot}");
    }
}

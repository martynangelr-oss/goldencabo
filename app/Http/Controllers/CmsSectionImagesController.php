<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsSectionImagesController extends Controller
{
    // Slots fijos con sus metadatos
    private const SLOTS = [
        'about_img_main' => [
            'label'       => 'Imagen Principal',
            'section'     => 'about',
            'hint'        => '800 × 600 px recomendado (relación 4:3)',
            'default'     => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=85',
        ],
        'about_img_secondary' => [
            'label'       => 'Imagen Secundaria',
            'section'     => 'about',
            'hint'        => '600 × 450 px recomendado (relación 4:3)',
            'default'     => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80',
        ],
        'airport_img_main' => [
            'label'       => 'Imagen Principal',
            'section'     => 'airport',
            'hint'        => '900 × 600 px recomendado (relación 3:2)',
            'default'     => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=900&q=85',
        ],
    ];

    public function index()
    {
        $slots = [];
        foreach (self::SLOTS as $key => $meta) {
            $stored = SiteSetting::get($key);
            $slots[$key] = array_merge($meta, [
                'key'      => $key,
                'url'      => $stored
                    ? (str_starts_with($stored, 'http') ? $stored : Storage::disk('public')->url($stored))
                    : $meta['default'],
                'is_custom' => (bool) $stored,
            ]);
        }

        return view('admin.cms.section-images', compact('slots'));
    }

    public function update(Request $request, string $slot)
    {
        if (!array_key_exists($slot, self::SLOTS)) {
            abort(404);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:25600',
        ], [
            'image.required' => 'Debes seleccionar una imagen.',
            'image.image'    => 'El archivo debe ser una imagen válida.',
            'image.mimes'    => 'Solo se aceptan formatos JPG, PNG o WEBP.',
            'image.max'      => 'La imagen no debe superar 25 MB.',
        ]);

        // Borrar imagen anterior si es un archivo local
        $old = SiteSetting::get($slot);
        if ($old && !str_starts_with($old, 'http')) {
            Storage::disk('public')->delete($old);
        }

        $path = $request->file('image')->store('cms/sections', 'public');
        SiteSetting::set($slot, $path);

        return back()->with('success', 'Imagen actualizada correctamente.');
    }

    public function restore(string $slot)
    {
        if (!array_key_exists($slot, self::SLOTS)) {
            abort(404);
        }

        $old = SiteSetting::get($slot);
        if ($old && !str_starts_with($old, 'http')) {
            Storage::disk('public')->delete($old);
        }

        SiteSetting::set($slot, null);

        return back()->with('success', 'Imagen restaurada al valor predeterminado.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsGalleryController extends Controller
{
    public function index()
    {
        $images = GalleryImage::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.cms.gallery', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images'    => 'required|array|min:1',
            'images.*'  => 'image|mimes:jpg,jpeg,png,webp|max:25600',
            'caption'   => 'nullable|string|max:191',
            'sort_order'=> 'nullable|integer|min:0',
        ], [
            'images.required'  => 'Debes seleccionar al menos una imagen.',
            'images.*.image'   => 'Uno o más archivos no son imágenes válidas.',
            'images.*.mimes'   => 'Solo se aceptan formatos JPG, PNG o WEBP.',
            'images.*.max'     => 'Cada imagen no debe superar 25 MB.',
        ]);

        $base  = GalleryImage::max('sort_order') ?? 0;
        $count = 0;
        $errors = [];

        foreach ($request->file('images') as $i => $file) {
            try {
                $path = $file->store('cms/gallery', 'public');
                if (!$path) {
                    $errors[] = "No se pudo guardar '{$file->getClientOriginalName()}'. Verifica permisos de almacenamiento.";
                    continue;
                }
                GalleryImage::create([
                    'image_path' => $path,
                    'caption'    => $request->captions[$i] ?? $request->caption ?? null,
                    'sort_order' => $base + $i + 1,
                    'is_active'  => true,
                ]);
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Error al procesar '{$file->getClientOriginalName()}': " . $e->getMessage();
            }
        }

        if ($errors) {
            $msg = implode(' | ', $errors);
            return back()->withErrors(['images' => $msg])->with('gallery_upload_error', true);
        }

        return back()->with('success', "$count imagen(es) subida(s) correctamente.");
    }

    public function update(Request $request, GalleryImage $image)
    {
        $request->validate([
            'caption'    => 'nullable|string|max:191',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:25600',
        ], [
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'Solo se aceptan formatos JPG, PNG o WEBP.',
            'image.max'   => 'La imagen no debe superar 25 MB.',
        ]);

        $data = [
            'caption'    => $request->caption,
            'sort_order' => $request->sort_order ?? $image->sort_order,
            'is_active'  => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            try {
                if ($image->image_path && !str_starts_with($image->image_path, 'http')) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $path = $request->file('image')->store('cms/gallery', 'public');
                if (!$path) {
                    return back()->withErrors(['image' => 'No se pudo guardar la imagen. Verifica permisos de almacenamiento.'])->with('gallery_edit_error', $image->id);
                }
                $data['image_path'] = $path;
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Error al guardar la imagen: ' . $e->getMessage()])->with('gallery_edit_error', $image->id);
            }
        }

        $image->update($data);
        return back()->with('success', 'Imagen actualizada.');
    }

    public function destroy(GalleryImage $image)
    {
        if ($image->image_path && !str_starts_with($image->image_path, 'http')) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();
        return back()->with('success', 'Imagen eliminada.');
    }

    public function toggle(GalleryImage $image)
    {
        $image->update(['is_active' => !$image->is_active]);
        return back()->with('success', 'Visibilidad actualizada.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesFileStorage;
use App\Models\GalleryImage;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class CmsGalleryController extends Controller
{
    use HandlesFileStorage;

    public function __construct(private GalleryService $gallery) {}

    public function index()
    {
        $images = $this->gallery->listAllOrdered();

        return view('admin.cms.gallery', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:25600',
            'caption' => 'nullable|string|max:191',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'images.required' => 'Debes seleccionar al menos una imagen.',
            'images.*.image' => 'Uno o más archivos no son imágenes válidas.',
            'images.*.mimes' => 'Solo se aceptan formatos JPG, PNG o WEBP.',
            'images.*.max' => 'Cada imagen no debe superar 25 MB.',
        ]);

        $base = $this->gallery->maxSortOrder();
        $count = 0;
        $errors = [];

        foreach ($request->file('images') as $i => $file) {
            try {
                $path = $this->saveFile($file, 'cms/gallery');
                $this->gallery->create([
                    'image_path' => $path,
                    'caption' => $request->captions[$i] ?? $request->caption ?? null,
                    'sort_order' => $base + $i + 1,
                    'is_active' => true,
                ]);
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Error al procesar '{$file->getClientOriginalName()}': ".$e->getMessage();
            }
        }

        if ($errors) {
            return back()->withErrors(['images' => implode(' | ', $errors)])->with('gallery_upload_error', true);
        }

        return back()->with('success', "$count imagen(es) subida(s) correctamente.");
    }

    public function update(Request $request, GalleryImage $image)
    {
        $request->validate([
            'caption' => 'nullable|string|max:191',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:25600',
        ], [
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'Solo se aceptan formatos JPG, PNG o WEBP.',
            'image.max' => 'La imagen no debe superar 25 MB.',
        ]);

        $data = [
            'caption' => $request->caption,
            'sort_order' => $request->sort_order ?? $image->sort_order,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            try {
                $this->deleteFile($image->image_path);
                $data['image_path'] = $this->saveFile($request->file('image'), 'cms/gallery');
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Error al guardar la imagen: '.$e->getMessage()])->with('gallery_edit_error', $image->id);
            }
        }

        $this->gallery->update($image, $data);

        return back()->with('success', 'Imagen actualizada.');
    }

    public function destroy(GalleryImage $image)
    {
        $this->deleteFile($image->image_path);
        $this->gallery->delete($image);

        return back()->with('success', 'Imagen eliminada.');
    }

    public function toggle(GalleryImage $image)
    {
        $this->gallery->toggle($image);

        return back()->with('success', 'Visibilidad actualizada.');
    }
}

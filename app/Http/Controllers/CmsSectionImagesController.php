<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesFileStorage;
use App\Services\SectionImageService;
use Illuminate\Http\Request;

class CmsSectionImagesController extends Controller
{
    use HandlesFileStorage;

    public function __construct(private SectionImageService $sectionImages) {}

    public function index()
    {
        $slots = $this->sectionImages->slotsWithMeta();

        return view('admin.cms.section-images', compact('slots'));
    }

    public function update(Request $request, string $slot)
    {
        if (! array_key_exists($slot, SectionImageService::SLOTS)) {
            abort(404);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:25600',
        ], [
            'image.required' => 'Debes seleccionar una imagen.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'Solo se aceptan formatos JPG, PNG o WEBP.',
            'image.max' => 'La imagen no debe superar 25 MB.',
        ]);

        $this->deleteFile($this->sectionImages->currentPath($slot));
        $path = $this->saveFile($request->file('image'), 'cms/sections');
        $this->sectionImages->updateSlot($slot, $path);

        return back()->with('success', 'Imagen actualizada correctamente.');
    }

    public function restore(string $slot)
    {
        if (! array_key_exists($slot, SectionImageService::SLOTS)) {
            abort(404);
        }

        $this->deleteFile($this->sectionImages->currentPath($slot));
        $this->sectionImages->restoreSlot($slot);

        return back()->with('success', 'Imagen restaurada al valor predeterminado.');
    }
}

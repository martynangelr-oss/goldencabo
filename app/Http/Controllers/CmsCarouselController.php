<?php

namespace App\Http\Controllers;

use App\Models\CarouselSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsCarouselController extends Controller
{
    public function index()
    {
        $slides = CarouselSlide::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.cms.carousel', compact('slides'));
    }

    public function create()
    {
        return view('admin.cms.carousel-form', ['slide' => new CarouselSlide()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image_path'] = $this->handleImage($request);

        if (!$data['image_path']) {
            return back()->withErrors(['image' => 'La imagen es obligatoria.'])->withInput();
        }

        CarouselSlide::create($data);
        return redirect()->route('admin.cms.carousel.index')
            ->with('success', 'Diapositiva creada correctamente.');
    }

    public function edit(CarouselSlide $slide)
    {
        return view('admin.cms.carousel-form', compact('slide'));
    }

    public function update(Request $request, CarouselSlide $slide)
    {
        $data = $this->validated($request);
        $img  = $this->handleImage($request, $slide->image_path);
        if ($img !== null) $data['image_path'] = $img;

        $slide->update($data);
        return redirect()->route('admin.cms.carousel.index')
            ->with('success', 'Diapositiva actualizada correctamente.');
    }

    public function destroy(CarouselSlide $slide)
    {
        if ($slide->image_path && !str_starts_with($slide->image_path, 'http')) {
            Storage::disk('public')->delete($slide->image_path);
        }
        $slide->delete();
        return back()->with('success', 'Diapositiva eliminada.');
    }

    public function toggle(CarouselSlide $slide)
    {
        $slide->update(['is_active' => !$slide->is_active]);
        return back()->with('success', 'Estado actualizado.');
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function validated(Request $request): array
    {
        $v = $request->validate([
            'title'       => 'nullable|string|max:191',
            'subtitle'    => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:80',
            'button_url'  => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/|#|\/).*/i'],
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable',
        ]);

        $v['is_active']  = $request->boolean('is_active');
        $v['sort_order'] = $v['sort_order'] ?? 0;

        return $v;
    }

    private function handleImage(Request $request, ?string $existing = null): ?string
    {
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpg,jpeg,png,webp|max:20480',
            ], [
                'image.mimes' => 'El archivo debe ser JPG, PNG o WEBP.',
                'image.image' => 'El archivo debe ser una imagen válida.',
                'image.max'   => 'La imagen no debe superar 20 MB.',
            ]);
            if ($existing && !str_starts_with($existing, 'http')) {
                Storage::disk('public')->delete($existing);
            }
            return $request->file('image')->store('cms/carousel', 'public');
        }
        return $existing;
    }
}

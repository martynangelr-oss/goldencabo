<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesFileStorage;
use App\Models\CarouselSlide;
use App\Services\CarouselService;
use Illuminate\Http\Request;

class CmsCarouselController extends Controller
{
    use HandlesFileStorage;

    public function __construct(private CarouselService $carousel) {}

    public function index()
    {
        $slides = $this->carousel->listAllOrdered();

        return view('admin.cms.carousel', compact('slides'));
    }

    public function create()
    {
        return view('admin.cms.carousel-form', ['slide' => new CarouselSlide]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        try {
            $data['image_path'] = $this->handleImage($request);
            if (! $data['image_path']) {
                return back()->withErrors(['image' => 'La imagen es obligatoria.'])->withInput();
            }
            $this->carousel->create($data);
        } catch (\Exception $e) {
            return redirect()->route('admin.cms.carousel.index')
                ->with('error', 'Error al crear la diapositiva. Inténtalo de nuevo.');
        }

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
        try {
            $img = $this->handleImage($request, $slide->image_path);
            if ($img !== null) {
                $data['image_path'] = $img;
            }
            $this->carousel->update($slide, $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.cms.carousel.index')
                ->with('error', 'Error al actualizar la diapositiva. Inténtalo de nuevo.');
        }

        return redirect()->route('admin.cms.carousel.index')
            ->with('success', 'Diapositiva actualizada correctamente.');
    }

    public function destroy(CarouselSlide $slide)
    {
        $this->deleteFile($slide->image_path);
        $this->carousel->delete($slide);

        return back()->with('success', 'Diapositiva eliminada.');
    }

    public function toggle(CarouselSlide $slide)
    {
        $this->carousel->toggle($slide);

        return back()->with('success', 'Estado actualizado.');
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function validated(Request $request): array
    {
        $v = $request->validate([
            'title' => 'nullable|string|max:191',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:80',
            'button_url' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/|#|\/).*/i'],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $v['is_active'] = $request->boolean('is_active');
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
                'image.max' => 'La imagen no debe superar 20 MB.',
            ]);
            $this->deleteFile($existing);

            return $this->saveFile($request->file('image'), 'cms/carousel');
        }

        return $existing;
    }
}

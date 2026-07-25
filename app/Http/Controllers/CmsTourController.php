<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesFileStorage;
use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Http\Request;

class CmsTourController extends Controller
{
    use HandlesFileStorage;

    public function __construct(private TourService $tours) {}

    public function index()
    {
        $tours = $this->tours->listAllOrdered();

        return view('admin.cms.tours', compact('tours'));
    }

    public function create()
    {
        return view('admin.cms.tour-form', ['tour' => new Tour]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        try {
            $data['image_path'] = $this->handleImage($request);
            $this->tours->create($data);
        } catch (\Exception $e) {
            return redirect()->route('admin.cms.tours.index')
                ->with('error', 'Error al crear el tour. Inténtalo de nuevo.');
        }

        return redirect()->route('admin.cms.tours.index')
            ->with('success', 'Tour creado correctamente.');
    }

    public function edit(Tour $tour)
    {
        return view('admin.cms.tour-form', compact('tour'));
    }

    public function update(Request $request, Tour $tour)
    {
        $data = $this->validated($request, $tour->id);
        try {
            $img = $this->handleImage($request, $tour->image_path);
            if ($img !== null) {
                $data['image_path'] = $img;
            }
            $this->tours->update($tour, $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.cms.tours.index')
                ->with('error', 'Error al actualizar el tour. Inténtalo de nuevo.');
        }

        return redirect()->route('admin.cms.tours.index')
            ->with('success', 'Tour actualizado correctamente.');
    }

    public function destroy(Tour $tour)
    {
        $this->deleteFile($tour->image_path);
        $this->tours->delete($tour);

        return back()->with('success', 'Tour eliminado.');
    }

    public function toggle(Tour $tour)
    {
        $tour = $this->tours->toggleActive($tour);

        return response()->json(['is_active' => $tour->is_active]);
    }

    // ── Helpers ────────────────────────────────────────────────
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $v = $request->validate([
            'name' => 'required|string|max:191',
            'name_en' => 'nullable|string|max:191',
            'duration' => 'nullable|string|max:50',
            'route_description' => 'nullable|string|max:3000',
            'route_description_en' => 'nullable|string|max:3000',
            'destinations' => 'nullable|string',
            'destinations_en' => 'nullable|string',
            'price_usd' => 'required|numeric|min:0',
            'price_label' => 'nullable|in:group,person,none',
            'is_active' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $v['is_active'] = $request->boolean('is_active');
        $v['sort_order'] = $v['sort_order'] ?? 0;
        $v['price_label'] = $v['price_label'] ?? 'group';
        $v['destinations'] = $v['destinations']
            ? array_values(array_filter(array_map('trim', explode("\n", $v['destinations']))))
            : [];
        $v['destinations_en'] = $v['destinations_en']
            ? array_values(array_filter(array_map('trim', explode("\n", $v['destinations_en']))))
            : [];

        return $v;
    }

    private function handleImage(Request $request, ?string $existing = null): ?string
    {
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpg,jpeg,png,webp|max:25600',
            ], [
                'image.image' => 'El archivo debe ser una imagen válida.',
                'image.mimes' => 'Solo se aceptan formatos JPG, PNG o WEBP.',
                'image.max' => 'La imagen no debe superar 25 MB.',
            ]);
            $this->deleteFile($existing);

            return $this->saveFile($request->file('image'), 'cms/tours');
        }
        if ($request->filled('image_url')) {
            $request->validate([
                'image_url' => ['string', 'max:500', 'regex:/^https?:\/\//i'],
            ], [
                'image_url.regex' => 'La URL de imagen debe comenzar con http:// o https://',
            ]);

            return $request->input('image_url');
        }

        return $existing;
    }
}

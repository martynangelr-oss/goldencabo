<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsTourController extends Controller
{
    public function index()
    {
        $tours = Tour::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.cms.tours', compact('tours'));
    }

    public function create()
    {
        return view('admin.cms.tour-form', ['tour' => new Tour()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image_path'] = $this->handleImage($request);

        Tour::create($data);
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
        $img  = $this->handleImage($request, $tour->image_path);
        if ($img !== null) $data['image_path'] = $img;

        $tour->update($data);
        return redirect()->route('admin.cms.tours.index')
            ->with('success', 'Tour actualizado correctamente.');
    }

    public function destroy(Tour $tour)
    {
        if ($tour->image_path && !str_starts_with($tour->image_path, 'http')) {
            Storage::disk('public')->delete($tour->image_path);
        }
        $tour->delete();
        return back()->with('success', 'Tour eliminado.');
    }

    public function toggle(Tour $tour)
    {
        $tour->update(['is_active' => !$tour->is_active]);
        return response()->json(['is_active' => $tour->is_active]);
    }

    // ── Helpers ────────────────────────────────────────────────
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $v = $request->validate([
            'name'              => 'required|string|max:191',
            'duration'          => 'nullable|string|max:50',
            'route_description' => 'nullable|string|max:3000',
            'destinations'      => 'nullable|string',
            'price_usd'         => 'required|numeric|min:0',
            'is_active'         => 'nullable',
            'sort_order'        => 'nullable|integer|min:0',
        ]);

        $v['is_active']    = $request->boolean('is_active');
        $v['sort_order']   = $v['sort_order'] ?? 0;
        $v['destinations'] = $v['destinations']
            ? array_values(array_filter(array_map('trim', explode("\n", $v['destinations']))))
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
                'image.max'   => 'La imagen no debe superar 25 MB.',
            ]);
            if ($existing && !str_starts_with($existing, 'http')) {
                Storage::disk('public')->delete($existing);
            }
            return $request->file('image')->store('cms/tours', 'public');
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

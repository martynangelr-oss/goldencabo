<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsVehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.cms.vehicles', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.cms.vehicle-form', ['vehicle' => new Vehicle()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image_path'] = $this->handleImage($request);

        Vehicle::create($data);
        return redirect()->route('admin.cms.vehicles.index')
            ->with('success', 'Vehículo creado correctamente.');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.cms.vehicle-form', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $this->validated($request, $vehicle->id);
        $img  = $this->handleImage($request, $vehicle->image_path);
        if ($img !== null) $data['image_path'] = $img;

        $vehicle->update($data);
        return redirect()->route('admin.cms.vehicles.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->image_path && !str_starts_with($vehicle->image_path, 'http')) {
            Storage::disk('public')->delete($vehicle->image_path);
        }
        $vehicle->delete();
        return back()->with('success', 'Vehículo eliminado.');
    }

    public function toggle(Vehicle $vehicle)
    {
        $vehicle->update(['is_available' => !$vehicle->is_available]);
        return response()->json(['is_available' => $vehicle->is_available]);
    }

    // ── Helpers ────────────────────────────────────────────────
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $v = $request->validate([
            'name'         => 'required|string|max:191',
            'description'  => 'nullable|string|max:2000',
            'services'     => 'nullable|string',
            'passengers'   => 'required|integer|min:1|max:50',
            'is_available' => 'nullable',
            'sort_order'   => 'nullable|integer|min:0',
        ]);

        $v['is_available'] = $request->boolean('is_available');
        $v['sort_order']   = $v['sort_order'] ?? 0;
        $v['services']     = $v['services']
            ? array_values(array_filter(array_map('trim', explode(',', $v['services']))))
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
            return $request->file('image')->store('cms/vehicles', 'public');
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

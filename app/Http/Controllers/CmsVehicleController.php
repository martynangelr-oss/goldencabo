<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesFileStorage;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\Request;

class CmsVehicleController extends Controller
{
    use HandlesFileStorage;

    public function __construct(private VehicleService $vehicles) {}

    public function index()
    {
        $vehicles = $this->vehicles->listAllOrdered();

        return view('admin.cms.vehicles', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.cms.vehicle-form', ['vehicle' => new Vehicle]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        try {
            $data['image_path'] = $this->handleImage($request);
            $this->vehicles->create($data);
        } catch (\Exception $e) {
            return redirect()->route('admin.cms.vehicles.index')
                ->with('error', 'Error al crear el vehículo. Inténtalo de nuevo.');
        }

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
        try {
            $img = $this->handleImage($request, $vehicle->image_path);
            if ($img !== null) {
                $data['image_path'] = $img;
            }
            $this->vehicles->update($vehicle, $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.cms.vehicles.index')
                ->with('error', 'Error al actualizar el vehículo. Inténtalo de nuevo.');
        }

        return redirect()->route('admin.cms.vehicles.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->deleteFile($vehicle->image_path);
        $this->vehicles->delete($vehicle);

        return back()->with('success', 'Vehículo eliminado.');
    }

    public function toggle(Vehicle $vehicle)
    {
        $vehicle = $this->vehicles->toggleAvailability($vehicle);

        return response()->json(['is_available' => $vehicle->is_available]);
    }

    // ── Helpers ────────────────────────────────────────────────
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $v = $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string|max:2000',
            'services' => 'nullable|string',
            'passengers' => 'required|integer|min:1|max:50',
            'is_available' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $v['is_available'] = $request->boolean('is_available');
        $v['sort_order'] = $v['sort_order'] ?? 0;
        $v['services'] = $v['services']
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
                'image.max' => 'La imagen no debe superar 25 MB.',
            ]);
            $this->deleteFile($existing);

            return $this->saveFile($request->file('image'), 'cms/vehicles');
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

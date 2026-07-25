<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesFileStorage;
use App\Models\Hotel;
use App\Models\Zone;
use App\Services\ZoneService;
use Illuminate\Http\Request;

class CmsZoneController extends Controller
{
    use HandlesFileStorage;

    public function __construct(private ZoneService $zones) {}

    public function index()
    {
        $zones = $this->zones->listAllWithHotels();

        return view('admin.cms.zones', compact('zones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|integer|unique:zones,number',
            'name' => 'required|string|max:100',
            'area' => 'nullable|string|max:200',
            'round_trip_price' => 'required|numeric|min:0',
            'one_way_price' => 'required|numeric|min:0',
            'travel_time' => 'nullable|string|max:30',
        ]);

        $this->zones->create([
            'number' => $request->number,
            'name' => $request->name,
            'area' => $request->area,
            'round_trip_price' => $request->round_trip_price,
            'one_way_price' => $request->one_way_price,
            'travel_time' => $request->travel_time,
            'is_active' => true,
            'sort_order' => $request->number,
        ]);

        return back()->with('success', 'Zona creada correctamente.');
    }

    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'area' => 'nullable|string|max:200',
            'round_trip_price' => 'required|numeric|min:0',
            'one_way_price' => 'required|numeric|min:0',
            'travel_time' => 'nullable|string|max:30',
        ]);

        $this->zones->update($zone, [
            'name' => $request->name,
            'area' => $request->area,
            'round_trip_price' => $request->round_trip_price,
            'one_way_price' => $request->one_way_price,
            'travel_time' => $request->travel_time,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Zona actualizada.');
    }

    public function updateImages(Request $request, Zone $zone)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:30720',
            'image_secondary' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:30720',
        ], [
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'Solo se aceptan formatos JPG, PNG o WEBP.',
            'image.max' => 'La imagen no debe superar 30 MB.',
            'image_secondary.image' => 'El archivo debe ser una imagen válida.',
            'image_secondary.mimes' => 'Solo se aceptan formatos JPG, PNG o WEBP.',
            'image_secondary.max' => 'La imagen no debe superar 30 MB.',
        ]);

        $data = [];
        try {
            if ($request->hasFile('image')) {
                $this->deleteFile($zone->image_path);
                $data['image_path'] = $this->saveFile($request->file('image'), 'cms/zones');
            }
            if ($request->hasFile('image_secondary')) {
                $this->deleteFile($zone->image_path_secondary);
                $data['image_path_secondary'] = $this->saveFile($request->file('image_secondary'), 'cms/zones');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar las imágenes. Inténtalo de nuevo.');
        }

        if (! empty($data)) {
            $this->zones->updateImages($zone, $data);

            return back()->with('success', 'Imágenes de la zona actualizadas.');
        }

        return back()->with('error', 'No se seleccionó ninguna imagen.');
    }

    public function destroy(Zone $zone)
    {
        $this->zones->delete($zone);

        return back()->with('success', 'Zona eliminada.');
    }

    // ── Hotels ──────────────────────────────────────────────────
    public function storeHotel(Request $request, Zone $zone)
    {
        $request->validate(['name' => 'required|string|max:191']);

        $this->zones->addHotel($zone, $request->name);

        return back()->with('success', 'Hotel agregado.');
    }

    public function destroyHotel(Hotel $hotel)
    {
        $this->zones->deleteHotel($hotel);

        return back()->with('success', 'Hotel eliminado.');
    }

    public function toggleHotel(Hotel $hotel)
    {
        $this->zones->toggleHotel($hotel);

        return back()->with('success', 'Estado del hotel actualizado.');
    }
}

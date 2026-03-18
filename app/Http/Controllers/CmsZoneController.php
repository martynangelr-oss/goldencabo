<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::with('hotels')->orderBy('number')->get();
        return view('admin.cms.zones', compact('zones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number'          => 'required|integer|unique:zones,number',
            'name'            => 'required|string|max:100',
            'area'            => 'nullable|string|max:200',
            'round_trip_price'=> 'required|numeric|min:0',
            'one_way_price'   => 'required|numeric|min:0',
            'travel_time'     => 'nullable|string|max:30',
        ]);

        Zone::create([
            'number'           => $request->number,
            'name'             => $request->name,
            'area'             => $request->area,
            'round_trip_price' => $request->round_trip_price,
            'one_way_price'    => $request->one_way_price,
            'travel_time'      => $request->travel_time,
            'is_active'        => true,
            'sort_order'       => $request->number,
        ]);

        return back()->with('success', 'Zona creada correctamente.');
    }

    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'name'            => 'required|string|max:100',
            'area'            => 'nullable|string|max:200',
            'round_trip_price'=> 'required|numeric|min:0',
            'one_way_price'   => 'required|numeric|min:0',
            'travel_time'     => 'nullable|string|max:30',
        ]);

        $zone->update([
            'name'             => $request->name,
            'area'             => $request->area,
            'round_trip_price' => $request->round_trip_price,
            'one_way_price'    => $request->one_way_price,
            'travel_time'      => $request->travel_time,
            'is_active'        => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Zona actualizada.');
    }

    public function updateImages(Request $request, Zone $zone)
    {
        $request->validate([
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'image_secondary' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = [];

        if ($request->hasFile('image')) {
            if ($zone->image_path && !str_starts_with($zone->image_path, 'http')) {
                Storage::disk('public')->delete($zone->image_path);
            }
            $data['image_path'] = $request->file('image')->store('cms/zones', 'public');
        }

        if ($request->hasFile('image_secondary')) {
            if ($zone->image_path_secondary && !str_starts_with($zone->image_path_secondary, 'http')) {
                Storage::disk('public')->delete($zone->image_path_secondary);
            }
            $data['image_path_secondary'] = $request->file('image_secondary')->store('cms/zones', 'public');
        }

        if (!empty($data)) {
            $zone->update($data);
            return back()->with('success', 'Imágenes de la zona actualizadas.');
        }

        return back()->with('error', 'No se seleccionó ninguna imagen.');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return back()->with('success', 'Zona eliminada.');
    }

    // ── Hotels ──────────────────────────────────────────────────
    public function storeHotel(Request $request, Zone $zone)
    {
        $request->validate(['name' => 'required|string|max:191']);

        $zone->hotels()->create([
            'name'       => $request->name,
            'is_active'  => true,
            'sort_order' => $zone->hotels()->count(),
        ]);

        return back()->with('success', 'Hotel agregado.');
    }

    public function destroyHotel(Hotel $hotel)
    {
        $hotel->delete();
        return back()->with('success', 'Hotel eliminado.');
    }

    public function toggleHotel(Hotel $hotel)
    {
        $hotel->update(['is_active' => !$hotel->is_active]);
        return back()->with('success', 'Estado del hotel actualizado.');
    }
}

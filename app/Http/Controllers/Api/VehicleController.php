<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesFileStorage;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    use HandlesFileStorage;

    public function __construct(private VehicleService $vehicles) {}

    public function index()
    {
        return response()->json(['data' => $this->vehicles->listAllOrdered()]);
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json(['data' => $vehicle]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image_path'] = $this->handleImage($request);

        $vehicle = $this->vehicles->create($data);

        return response()->json(['data' => $vehicle], 201);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $this->validated($request);
        $img = $this->handleImage($request, $vehicle->image_path);
        if ($img !== null) {
            $data['image_path'] = $img;
        }

        $vehicle = $this->vehicles->update($vehicle, $data);

        return response()->json(['data' => $vehicle]);
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->deleteFile($vehicle->image_path);
        $this->vehicles->delete($vehicle);

        return response()->json(null, 204);
    }

    private function validated(Request $request): array
    {
        $v = $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string|max:2000',
            'services' => 'nullable|array',
            'passengers' => 'required|integer|min:1|max:50',
            'is_available' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $v['is_available'] = $request->boolean('is_available');
        $v['sort_order'] = $v['sort_order'] ?? 0;
        $v['services'] = $v['services'] ?? [];

        return $v;
    }

    private function handleImage(Request $request, ?string $existing = null): ?string
    {
        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|mimes:jpg,jpeg,png,webp|max:25600']);
            $this->deleteFile($existing);

            return $this->saveFile($request->file('image'), 'cms/vehicles');
        }
        if ($request->filled('image_url')) {
            $request->validate(['image_url' => ['string', 'max:500', 'regex:/^https?:\/\//i']]);

            return $request->input('image_url');
        }

        return $existing;
    }
}

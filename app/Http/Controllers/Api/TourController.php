<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesFileStorage;
use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Http\Request;

class TourController extends Controller
{
    use HandlesFileStorage;

    public function __construct(private TourService $tours) {}

    public function index()
    {
        return response()->json(['data' => $this->tours->listAllOrdered()]);
    }

    public function show(Tour $tour)
    {
        return response()->json(['data' => $tour]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image_path'] = $this->handleImage($request);

        $tour = $this->tours->create($data);

        return response()->json(['data' => $tour], 201);
    }

    public function update(Request $request, Tour $tour)
    {
        $data = $this->validated($request);
        $img = $this->handleImage($request, $tour->image_path);
        if ($img !== null) {
            $data['image_path'] = $img;
        }

        $tour = $this->tours->update($tour, $data);

        return response()->json(['data' => $tour]);
    }

    public function destroy(Tour $tour)
    {
        $this->deleteFile($tour->image_path);
        $this->tours->delete($tour);

        return response()->json(null, 204);
    }

    private function validated(Request $request): array
    {
        $v = $request->validate([
            'name' => 'required|string|max:191',
            'name_en' => 'nullable|string|max:191',
            'duration' => 'nullable|string|max:50',
            'route_description' => 'nullable|string|max:3000',
            'route_description_en' => 'nullable|string|max:3000',
            'destinations' => 'nullable|array',
            'destinations_en' => 'nullable|array',
            'price_usd' => 'required|numeric|min:0',
            'price_label' => 'nullable|in:group,person,none',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $v['is_active'] = $request->boolean('is_active');
        $v['sort_order'] = $v['sort_order'] ?? 0;
        $v['price_label'] = $v['price_label'] ?? 'group';
        $v['destinations'] = $v['destinations'] ?? [];
        $v['destinations_en'] = $v['destinations_en'] ?? [];

        return $v;
    }

    private function handleImage(Request $request, ?string $existing = null): ?string
    {
        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|mimes:jpg,jpeg,png,webp|max:25600']);
            $this->deleteFile($existing);

            return $this->saveFile($request->file('image'), 'cms/tours');
        }
        if ($request->filled('image_url')) {
            $request->validate(['image_url' => ['string', 'max:500', 'regex:/^https?:\/\//i']]);

            return $request->input('image_url');
        }

        return $existing;
    }
}

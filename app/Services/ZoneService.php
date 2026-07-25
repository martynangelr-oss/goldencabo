<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Zone;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Collection;

class ZoneService
{
    /** Fallback pricing used only if a zone number has no matching active DB row. */
    private const FALLBACK = [
        1 => ['name' => 'San José del Cabo',  'round' => 100, 'one_way' => 60],
        2 => ['name' => 'Corredor Turístico', 'round' => 120, 'one_way' => 65],
        3 => ['name' => 'Cabo San Lucas',     'round' => 140, 'one_way' => 75],
        4 => ['name' => 'Lado del Pacífico',  'round' => 180, 'one_way' => 100],
    ];

    public function listAllWithHotels(): Collection
    {
        return Zone::with('hotels')->orderBy('number')->get();
    }

    public function create(array $data): Zone
    {
        $zone = Zone::create($data);
        AuditLogger::record('zone.create', $zone);

        return $zone;
    }

    public function update(Zone $zone, array $data): Zone
    {
        $zone->update($data);
        AuditLogger::record('zone.update', $zone);

        return $zone;
    }

    public function updateImages(Zone $zone, array $paths): Zone
    {
        $zone->update($paths);
        AuditLogger::record('zone.images', $zone);

        return $zone;
    }

    public function delete(Zone $zone): void
    {
        AuditLogger::record('zone.delete', $zone);
        $zone->delete();
    }

    public function addHotel(Zone $zone, string $name): Hotel
    {
        $hotel = $zone->hotels()->create([
            'name' => $name,
            'is_active' => true,
            'sort_order' => $zone->hotels()->count(),
        ]);
        AuditLogger::record('hotel.create', $hotel);

        return $hotel;
    }

    public function deleteHotel(Hotel $hotel): void
    {
        AuditLogger::record('hotel.delete', $hotel);
        $hotel->delete();
    }

    public function toggleHotel(Hotel $hotel): Hotel
    {
        $hotel->update(['is_active' => ! $hotel->is_active]);
        AuditLogger::record('hotel.toggle', $hotel);

        return $hotel;
    }

    public function activeForHome(): Collection
    {
        return Zone::with('activeHotels')->active()->orderBy('sort_order')->get();
    }

    /**
     * Resolve the zone name + price for a booking, preferring the live DB row
     * and falling back to the original hardcoded rates if it's missing/inactive.
     */
    public function resolvePricing(int $zoneNumber, string $tripType): array
    {
        $zone = Zone::where('number', $zoneNumber)->where('is_active', true)->first();
        $fallback = self::FALLBACK[$zoneNumber] ?? null;

        $zoneName = $zone?->name ?? $fallback['name'] ?? null;
        $price = $tripType === 'round_trip'
            ? ($zone?->round_trip_price ?? $fallback['round'] ?? null)
            : ($zone?->one_way_price ?? $fallback['one_way'] ?? null);

        return ['zone_name' => $zoneName, 'price' => $price];
    }
}

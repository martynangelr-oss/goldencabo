<?php

namespace App\Services;

use App\Models\Tour;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Collection;

class TourService
{
    public function listAllOrdered(): Collection
    {
        return Tour::orderBy('sort_order')->orderBy('id')->get();
    }

    public function create(array $data): Tour
    {
        $tour = Tour::create($data);
        AuditLogger::record('tour.create', $tour);

        return $tour;
    }

    public function update(Tour $tour, array $data): Tour
    {
        $tour->update($data);
        AuditLogger::record('tour.update', $tour);

        return $tour;
    }

    public function delete(Tour $tour): void
    {
        AuditLogger::record('tour.delete', $tour);
        $tour->delete();
    }

    public function toggleActive(Tour $tour): Tour
    {
        $tour->update(['is_active' => ! $tour->is_active]);
        AuditLogger::record('tour.toggle', $tour);

        return $tour;
    }

    public function activeForHome(): Collection
    {
        return Tour::active()->orderBy('sort_order')->get();
    }
}

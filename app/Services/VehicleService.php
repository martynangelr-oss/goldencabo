<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Collection;

class VehicleService
{
    public function listAllOrdered(): Collection
    {
        return Vehicle::orderBy('sort_order')->orderBy('id')->get();
    }

    public function create(array $data): Vehicle
    {
        $vehicle = Vehicle::create($data);
        AuditLogger::record('vehicle.create', $vehicle);

        return $vehicle;
    }

    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        $vehicle->update($data);
        AuditLogger::record('vehicle.update', $vehicle);

        return $vehicle;
    }

    public function delete(Vehicle $vehicle): void
    {
        AuditLogger::record('vehicle.delete', $vehicle);
        $vehicle->delete();
    }

    public function toggleAvailability(Vehicle $vehicle): Vehicle
    {
        $vehicle->update(['is_available' => ! $vehicle->is_available]);
        AuditLogger::record('vehicle.toggle', $vehicle);

        return $vehicle;
    }

    public function availableForHome(): Collection
    {
        return Vehicle::available()->orderBy('sort_order')->get();
    }
}

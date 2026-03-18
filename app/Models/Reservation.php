<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'zone',
        'zone_name',
        'hotel',
        'first_name',
        'last_name',
        'email',
        'phone',
        'passengers',
        'direction',
        'trip_type',
        'arrival_flight',
        'departure_flight',
        'arrival_date',
        'arrival_time',
        'price',
        'status',
        'notes',
    ];

    protected $casts = [
        'arrival_date' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getDirectionLabelAttribute(): string
    {
        return $this->direction === 'air' ? 'Aeropuerto → Hotel' : 'Hotel → Aeropuerto';
    }

    public function getTripLabelAttribute(): string
    {
        return $this->trip_type === 'rnd' ? 'Ida y vuelta' : 'Solo ida';
    }

    public static function generateOrderNumber(): string
    {
        return 'GC-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}

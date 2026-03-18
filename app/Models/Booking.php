<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'zone', 'zone_name', 'trip_type', 'direction',
        'price_usd', 'first_name', 'last_name', 'email', 'phone',
        'hotel', 'pax', 'arrival_flight', 'departure_flight',
        'arrival_date', 'arrival_time', 'status', 'notes', 'voucher_sent',
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'voucher_sent' => 'boolean',
        'price_usd' => 'decimal:2',
    ];

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'GC-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDirectionLabelAttribute(): string
    {
        return $this->direction === 'airport_to_hotel'
            ? 'Aeropuerto → Hotel'
            : 'Hotel → Aeropuerto';
    }

    public function getTripLabelAttribute(): string
    {
        return $this->trip_type === 'round_trip' ? 'Ida y vuelta' : 'Solo ida';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'confirmed'  => 'bg-green-100 text-green-800',
            'pending'    => 'bg-yellow-100 text-yellow-800',
            'cancelled'  => 'bg-red-100 text-red-800',
            'completed'  => 'bg-blue-100 text-blue-800',
            default      => 'bg-gray-100 text-gray-800',
        };
    }
}

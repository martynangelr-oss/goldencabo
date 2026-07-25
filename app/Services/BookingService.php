<?php

namespace App\Services;

use App\Models\Booking;
use App\Support\AuditLogger;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BookingService
{
    public function create(array $data): Booking
    {
        $data['order_number'] = Booking::generateOrderNumber();
        $booking = Booking::create($data);
        AuditLogger::record('booking.create', $booking);

        return $booking;
    }

    public function findByOrderNumber(string $orderNumber): ?Booking
    {
        return Booking::where('order_number', $orderNumber)->first();
    }

    public function findById(int $id): Booking
    {
        return Booking::findOrFail($id);
    }

    public function markVoucherSent(Booking $booking): Booking
    {
        $booking->update(['voucher_sent' => true]);

        return $booking;
    }

    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Booking::latest();

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('hotel', 'like', "%$search%");
            });
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($zone = $filters['zone'] ?? null) {
            $query->where('zone', (int) $zone);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function updateStatus(Booking $booking, array $data): Booking
    {
        $booking->update($data);
        AuditLogger::record('booking.status_update', $booking);

        return $booking;
    }

    public function delete(Booking $booking): void
    {
        AuditLogger::record('booking.delete', $booking);
        $booking->delete();
    }

    public function countTotal(): int
    {
        return Booking::count();
    }

    public function countConfirmedToday(): int
    {
        return Booking::whereDate('created_at', today())->count();
    }

    public function recent(int $n = 5): Collection
    {
        return Booking::latest()->take($n)->get();
    }
}

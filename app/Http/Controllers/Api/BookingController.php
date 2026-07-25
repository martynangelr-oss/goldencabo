<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookings) {}

    public function index(Request $request)
    {
        $bookings = $this->bookings->paginateForAdmin([
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'zone' => $request->get('zone'),
        ]);

        return response()->json($bookings);
    }

    public function show(string $orderNumber)
    {
        $booking = $this->bookings->findByOrderNumber($orderNumber);
        abort_unless($booking, 404);

        return response()->json(['data' => $booking]);
    }
}

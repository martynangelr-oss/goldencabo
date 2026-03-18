<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Mail\ReservationConfirmation;
use App\Mail\ReservationAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservationController extends Controller
{
    private array $prices = [
        1 => ['round' => '$100 USD', 'oneway' => '$60 USD'],
        2 => ['round' => '$120 USD', 'oneway' => '$65 USD'],
        3 => ['round' => '$140 USD', 'oneway' => '$75 USD'],
        4 => ['round' => '$180 USD', 'oneway' => '$100 USD'],
    ];

    private array $zoneNames = [
        1 => 'San José del Cabo',
        2 => 'Corredor Turístico',
        3 => 'Cabo San Lucas',
        4 => 'Lado del Pacífico',
    ];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone'            => 'required|integer|between:1,4',
            'hotel'           => 'required|string|max:120',
            'first_name'      => 'required|string|max:80',
            'last_name'       => 'nullable|string|max:80',
            'email'           => 'required|email|max:150',
            'phone'           => 'required|string|min:7|max:25',
            'passengers'      => 'required|integer|between:1,10',
            'direction'       => 'required|in:air,htl',
            'trip_type'       => 'required|in:one,rnd',
            'arrival_flight'  => 'nullable|string|max:15',
            'departure_flight'=> 'nullable|string|max:15',
            'arrival_day'     => 'required|string',
            'arrival_month'   => 'required|string',
            'arrival_year'    => 'required|string',
            'arrival_hour'    => 'nullable|string',
            'arrival_minute'  => 'nullable|string',
            'terms'           => 'required|accepted',
        ]);

        $zone = (int) $validated['zone'];
        $tripType = $validated['trip_type'];
        $price = $tripType === 'rnd'
            ? $this->prices[$zone]['round']
            : $this->prices[$zone]['oneway'];

        $arrivalDate = "{$validated['arrival_year']}-{$validated['arrival_month']}-{$validated['arrival_day']}";
        $arrivalTime = sprintf('%s:%s', $validated['arrival_hour'] ?? '00', $validated['arrival_minute'] ?? '00');

        $reservation = Reservation::create([
            'order_number'    => Reservation::generateOrderNumber(),
            'zone'            => $zone,
            'zone_name'       => $this->zoneNames[$zone],
            'hotel'           => $validated['hotel'],
            'first_name'      => $validated['first_name'],
            'last_name'       => $validated['last_name'] ?? '',
            'email'           => $validated['email'],
            'phone'           => $validated['phone'],
            'passengers'      => $validated['passengers'],
            'direction'       => $validated['direction'],
            'trip_type'       => $tripType,
            'arrival_flight'  => $validated['arrival_flight'] ?? 'N/A',
            'departure_flight'=> $validated['departure_flight'] ?? 'N/A',
            'arrival_date'    => $arrivalDate,
            'arrival_time'    => $arrivalTime,
            'price'           => $price,
            'status'          => 'confirmed',
        ]);

        // Send confirmation to client
        try {
            Mail::to($reservation->email)->send(new ReservationConfirmation($reservation));
        } catch (\Exception $e) {
            \Log::warning('Client email failed: ' . $e->getMessage());
        }

        // Notify admin
        try {
            Mail::to(config('mail.admin_email', 'goldencabotransportation@gmail.com'))
                ->send(new ReservationAdmin($reservation));
        } catch (\Exception $e) {
            \Log::warning('Admin email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'order'   => $reservation->order_number,
            'id'      => $reservation->id,
        ]);
    }

    public function voucher(string $order)
    {
        $reservation = Reservation::where('order_number', $order)->firstOrFail();
        return view('voucher', compact('reservation'));
    }

    public function pdf(string $order)
    {
        $reservation = Reservation::where('order_number', $order)->firstOrFail();
        $zones = app(HomeController::class)::getZones();

        $pdf = Pdf::loadView('pdf.voucher', compact('reservation', 'zones'))
            ->setPaper('letter', 'portrait');

        return $pdf->download("GoldenCabo_Voucher_{$reservation->order_number}.pdf");
    }

    public function sendEmail(Request $request, string $order)
    {
        $reservation = Reservation::where('order_number', $order)->firstOrFail();

        try {
            Mail::to($reservation->email)->send(new ReservationConfirmation($reservation));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

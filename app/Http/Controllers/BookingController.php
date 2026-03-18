<?php

namespace App\Http\Controllers;

use App\Http\Controllers\HomeController;
use App\Models\Booking;
use App\Models\Zone;
use App\Mail\BookingConfirmation;
use App\Mail\BookingNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    private array $zones = [
        1 => ['name' => 'San José del Cabo',  'round' => 100, 'one_way' => 60],
        2 => ['name' => 'Corredor Turístico', 'round' => 120, 'one_way' => 65],
        3 => ['name' => 'Cabo San Lucas',     'round' => 140, 'one_way' => 75],
        4 => ['name' => 'Lado del Pacífico',  'round' => 180, 'one_way' => 100],
    ];

    public function store(Request $request)
    {
        // Normalizar direction: 'air' → 'airport_to_hotel', 'hotel' → 'hotel_to_airport'
        $dirMap = ['air' => 'airport_to_hotel', 'hotel' => 'hotel_to_airport'];
        if (isset($dirMap[$request->direction])) {
            $request->merge(['direction' => $dirMap[$request->direction]]);
        }

        // Normalizar trip_type: 'one' → 'one_way', 'rnd' → 'round_trip'
        $tripMap = ['one' => 'one_way', 'rnd' => 'round_trip'];
        if (isset($tripMap[$request->trip_type])) {
            $request->merge(['trip_type' => $tripMap[$request->trip_type]]);
        }

        // Build arrival_date from separate day/month/year fields if needed
        if (!$request->arrival_date && $request->arrival_day) {
            $request->merge([
                'arrival_date' => sprintf(
                    '%04d-%02d-%02d',
                    $request->arrival_year ?? date('Y'),
                    $request->arrival_month ?? date('m'),
                    $request->arrival_day
                ),
            ]);
        }

        // Build arrival_time from hour/minute if needed
        if (!$request->arrival_time && $request->arrival_hour !== null) {
            $request->merge([
                'arrival_time' => sprintf('%02d:%02d', $request->arrival_hour, $request->arrival_minute ?? 0),
            ]);
        }

        // Normalize pax field (JS sends 'passengers')
        if (!$request->pax && $request->passengers) {
            $request->merge(['pax' => $request->passengers]);
        }

        $validator = Validator::make($request->all(), [
            'zone'            => 'required|integer|in:1,2,3,4',
            'trip_type'       => 'required|in:one_way,round_trip',
            'direction'       => 'required|in:airport_to_hotel,hotel_to_airport',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'nullable|string|max:100',
            'email'           => ['required', 'string', 'max:255', 'email:rfc', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'],
            'phone'           => ['required', 'string', 'regex:/^\+\d{1,3}\d{10}$/'],
            'hotel'           => 'required|string|max:191',
            'pax'             => 'required|integer|min:1|max:20',
            'arrival_flight'  => 'nullable|string|max:20',
            'departure_flight'=> 'nullable|string|max:20',
            'arrival_date'    => 'required|date|after_or_equal:today',
            'arrival_time'    => 'nullable|string|max:10',
        ], [
            'zone.required'         => 'Seleccione una zona.',
            'zone.in'               => 'Zona no válida.',
            'first_name.required'   => 'El nombre es obligatorio.',
            'email.required'        => 'El correo electrónico es obligatorio.',
            'email.email'           => 'Introduzca un correo válido.',
            'phone.required'        => 'El teléfono es obligatorio.',
            'hotel.required'        => 'El hotel es obligatorio.',
            'arrival_date.required'         => 'La fecha de llegada es obligatoria.',
            'arrival_date.after_or_equal'   => 'La fecha de llegada no puede ser en el pasado.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Server-side rate limit: 5 booking attempts per phone per hour
        $phoneKey = 'booking:' . preg_replace('/\D/', '', $request->phone);
        if (RateLimiter::tooManyAttempts($phoneKey, 5)) {
            $seconds = RateLimiter::availableIn($phoneKey);
            return response()->json([
                'success' => false,
                'message' => 'Ha superado el límite de intentos. Intente en ' . ceil($seconds / 60) . ' minuto(s).',
            ], 429);
        }
        RateLimiter::hit($phoneKey, 3600);

        $zone      = (int) $request->zone;
        $zoneModel = Zone::where('number', $zone)->where('is_active', true)->first();
        $fallback  = $this->zones[$zone];
        $zoneName  = $zoneModel ? $zoneModel->name : $fallback['name'];
        $price     = $request->trip_type === 'round_trip'
            ? ($zoneModel ? $zoneModel->round_trip_price : $fallback['round'])
            : ($zoneModel ? $zoneModel->one_way_price    : $fallback['one_way']);

        $booking = Booking::create([
            'order_number'    => Booking::generateOrderNumber(),
            'zone'            => $zone,
            'zone_name'       => $zoneName,
            'trip_type'       => $request->trip_type,
            'direction'       => $request->direction,
            'price_usd'       => $price,
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name ?? '',
            'email'           => $request->email,
            'phone'           => $request->phone,
            'hotel'           => $request->hotel,
            'pax'             => $request->pax,
            'arrival_flight'  => $request->arrival_flight,
            'departure_flight'=> $request->departure_flight,
            'arrival_date'    => $request->arrival_date,
            'arrival_time'    => $request->arrival_time,
            'status'          => 'confirmed',
        ]);

        // Email al cliente
        try {
            Mail::to($booking->email)->send(new BookingConfirmation($booking));
            $booking->update(['voucher_sent' => true]);
        } catch (\Exception $e) {
            Log::error('Booking confirmation email failed: ' . $e->getMessage());
        }

        // Notificación al admin
        try {
            $adminEmail = config('app.admin_email', 'goldencabotransportation@gmail.com');
            Mail::to($adminEmail)->send(new BookingNotification($booking));
        } catch (\Exception $e) {
            Log::error('Admin notification email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'order'   => $booking->order_number,
            'booking' => [
                'order_number'    => $booking->order_number,
                'zone'            => $booking->zone,
                'zone_name'       => $booking->zone_name,
                'trip_type'       => $booking->trip_type,
                'direction'       => $booking->direction,
                'price_usd'       => $booking->price_usd,
                'full_name'       => $booking->full_name,
                'email'           => $booking->email,
                'phone'           => $booking->phone,
                'hotel'           => $booking->hotel,
                'pax'             => $booking->pax,
                'arrival_flight'  => $booking->arrival_flight,
                'departure_flight'=> $booking->departure_flight,
                'arrival_date'    => $booking->arrival_date?->format('d/m/Y'),
                'arrival_time'    => $booking->arrival_time,
            ],
            'message' => '¡Reservación confirmada! Se ha enviado un voucher a su correo.',
        ]);
    }

    public function show(string $orderNumber)
    {
        $booking = Booking::where('order_number', $orderNumber)->firstOrFail();
        return response()->json(['success' => true, 'booking' => $booking]);
    }

    public function downloadPdf(Booking $booking, Request $request)
    {
        $lang  = in_array($request->query('lang'), ['es', 'en']) ? $request->query('lang') : 'es';
        $zones = HomeController::getZones();
        $t     = $this->voucherTranslations($booking, $lang);

        $pdf = Pdf::loadView('pdf.booking-voucher', compact('booking', 'zones', 't', 'lang'))
            ->setPaper('letter', 'portrait');

        return $pdf->download("GoldenCabo_Voucher_{$booking->order_number}.pdf");
    }

    private function voucherTranslations(Booking $booking, string $lang): array
    {
        $strings = [
            'es' => [
                'confirmed'        => '¡Reservación Confirmada!',
                'dear'             => 'Estimado/a',
                'thank_you'        => 'Gracias por elegir Golden Cabo Transportation. Su reservación ha sido confirmada exitosamente.',
                'service_details'  => 'Detalles del Servicio',
                'zone'             => 'Zona',
                'hotel'            => 'Hotel',
                'service_type'     => 'Tipo de servicio',
                'direction'        => 'Dirección',
                'passengers'       => 'Pasajeros',
                'flight_info'      => 'Información de Vuelo y Fecha',
                'arrival_flight'   => 'Vuelo de llegada',
                'departure_flight' => 'Vuelo de salida',
                'arrival_date'     => 'Fecha de llegada',
                'arrival_time'     => 'Hora de llegada',
                'zone_rates'       => 'Tarifas de Zonas',
                'zone_col'         => 'Zona',
                'destination'      => 'Destino',
                'round_trip_col'   => 'Ida y Vuelta',
                'one_way_col'      => 'Solo Ida',
                'total_price'      => 'Precio Total del Servicio',
                'meeting_point'    => 'Punto de Encuentro',
                'meeting_text'     => 'Nuestro personal le estará esperando a la salida de la terminal, <strong>bajo la sombrilla #10</strong>, con un cartel con su nombre. Por favor preséntese tan pronto haya recogido su equipaje.',
                'tagline'          => '"Ven a Los Cabos y déjanos la conducción a nosotros"',
                'direction_label'  => $booking->direction === 'airport_to_hotel' ? 'Aeropuerto → Hotel' : 'Hotel → Aeropuerto',
                'trip_label'       => $booking->trip_type === 'round_trip' ? 'Ida y vuelta' : 'Solo ida',
            ],
            'en' => [
                'confirmed'        => 'Booking Confirmed!',
                'dear'             => 'Dear',
                'thank_you'        => 'Thank you for choosing Golden Cabo Transportation. Your reservation has been successfully confirmed.',
                'service_details'  => 'Service Details',
                'zone'             => 'Zone',
                'hotel'            => 'Hotel',
                'service_type'     => 'Service Type',
                'direction'        => 'Direction',
                'passengers'       => 'Passengers',
                'flight_info'      => 'Flight & Date Information',
                'arrival_flight'   => 'Arrival Flight',
                'departure_flight' => 'Departure Flight',
                'arrival_date'     => 'Arrival Date',
                'arrival_time'     => 'Arrival Time',
                'zone_rates'       => 'Zone Rates',
                'zone_col'         => 'Zone',
                'destination'      => 'Destination',
                'round_trip_col'   => 'Round Trip',
                'one_way_col'      => 'One Way',
                'total_price'      => 'Total Service Price',
                'meeting_point'    => 'Meeting Point',
                'meeting_text'     => 'Our staff will be waiting for you at the terminal exit, <strong>under umbrella #10</strong>, holding a sign with your name. Please approach as soon as you have collected your luggage.',
                'tagline'          => '"Come to Los Cabos and let us take care of the driving"',
                'direction_label'  => $booking->direction === 'airport_to_hotel' ? 'Airport → Hotel' : 'Hotel → Airport',
                'trip_label'       => $booking->trip_type === 'round_trip' ? 'Round Trip' : 'One Way',
            ],
        ];

        return $strings[$lang];
    }

    public function resendVoucher(string $orderNumber)
    {
        $booking = Booking::where('order_number', $orderNumber)->firstOrFail();
        try {
            Mail::to($booking->email)->send(new BookingConfirmation($booking));
            return response()->json(['success' => true, 'message' => 'Voucher reenviado correctamente.']);
        } catch (\Exception $e) {
            Log::error('Resend voucher failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'No se pudo reenviar el voucher. Intente más tarde.'], 500);
        }
    }
}

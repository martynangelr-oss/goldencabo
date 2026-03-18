<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Nueva Reservación</title>
<style>body{font-family:Arial,sans-serif;background:#f5f5f5;padding:20px}
.box{max-width:580px;margin:0 auto;background:#fff;border-radius:12px;padding:28px;border-top:4px solid #00AC97}
h2{color:#051E1C}table{width:100%;border-collapse:collapse;margin-top:16px}
td{padding:8px 12px;border-bottom:1px solid #f0f0f0;font-size:.9rem}
td:first-child{color:#888;width:160px}.price{font-size:1.5rem;font-weight:700;color:#00AC97}</style>
</head>
<body>
<div class="box">
<h2>🔔 Nueva Reservación Recibida</h2>
<p>Se ha registrado una nueva reservación en el sistema.</p>
<table>
  <tr><td>Order #</td><td><strong>{{ $booking->order_number }}</strong></td></tr>
  <tr><td>Pasajero</td><td>{{ $booking->full_name }}</td></tr>
  <tr><td>Email</td><td>{{ $booking->email }}</td></tr>
  <tr><td>Teléfono</td><td>{{ $booking->phone }}</td></tr>
  <tr><td>Hotel</td><td>{{ $booking->hotel }}</td></tr>
  <tr><td>Zona</td><td>Zona {{ $booking->zone }} — {{ $booking->zone_name }}</td></tr>
  <tr><td>Servicio</td><td>{{ $booking->trip_label }} · {{ $booking->direction_label }}</td></tr>
  <tr><td>PAX</td><td>{{ $booking->pax }}</td></tr>
  <tr><td>Vuelo Llegada</td><td>{{ $booking->arrival_flight ?? 'N/A' }}</td></tr>
  <tr><td>Fecha Llegada</td><td>{{ $booking->arrival_date?->format('d/m/Y') }} {{ $booking->arrival_time }}</td></tr>
  <tr><td>Precio</td><td class="price">${{ number_format($booking->price_usd, 0) }} USD</td></tr>
</table>
<p style="margin-top:20px;font-size:.8rem;color:#888">Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
</div>
</body>
</html>

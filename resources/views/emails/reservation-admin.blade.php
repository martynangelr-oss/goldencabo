<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Nueva Reservación</title></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px">
<div style="background:#fff;max-width:600px;margin:0 auto;border-radius:12px;overflow:hidden">
  <div style="background:linear-gradient(135deg,#005F53,#00AC97);padding:24px;color:#fff">
    <h2 style="margin:0">🚐 Nueva Reservación: {{ $reservation->order_number }}</h2>
    <p style="margin:6px 0 0;opacity:.8">{{ $reservation->full_name }} — {{ $reservation->price }}</p>
  </div>
  <div style="padding:24px">
    <table width="100%" style="border-collapse:collapse;font-size:.9rem">
      @foreach([
        ['Pasajero', $reservation->full_name],
        ['Email', $reservation->email],
        ['Teléfono', $reservation->phone],
        ['Hotel', $reservation->hotel],
        ['Zona', 'Zona '.$reservation->zone.' — '.$reservation->zone_name],
        ['PAX', $reservation->passengers],
        ['Precio', $reservation->price],
        ['Dirección', $reservation->direction_label],
        ['Tipo', $reservation->trip_label],
        ['Vuelo Llegada', $reservation->arrival_flight ?: 'N/A'],
        ['Fecha', $reservation->arrival_date ? $reservation->arrival_date->format('d/m/Y') : '—'],
        ['Hora', $reservation->arrival_time ?? '—'],
      ] as [$l,$v])
      <tr><td style="padding:9px 0;border-bottom:1px solid #eee;font-weight:700;color:#8CCBC6;width:40%;font-size:.78rem;text-transform:uppercase;letter-spacing:1px">{{$l}}</td><td style="padding:9px 0;border-bottom:1px solid #eee;color:#051E1C">{{$v}}</td></tr>
      @endforeach
    </table>
    <div style="margin-top:20px"><a href="{{ url('/admin/reservations') }}" style="background:#00AC97;color:#fff;padding:12px 24px;border-radius:100px;text-decoration:none;font-size:.8rem;font-weight:700">Ver en el Panel Admin →</a></div>
  </div>
</div>
</body>
</html>

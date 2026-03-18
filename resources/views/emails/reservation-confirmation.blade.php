<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Confirmación de Reserva</title></head>
<body style="margin:0;padding:0;background:#F2FBFA;font-family:'Helvetica Neue',Arial,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F2FBFA;padding:40px 0">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);max-width:600px;width:100%">
      <!-- Header -->
      <tr><td style="background:linear-gradient(135deg,#005F53,#00AC97);padding:32px 40px;text-align:center">
        <div style="font-size:22px;color:#fff;font-weight:700;font-family:Georgia,serif">✦ GOLDEN CABO</div>
        <div style="font-size:11px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,.6);margin-top:4px">Transportation</div>
        <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);border-radius:100px;display:inline-block;padding:6px 20px;margin-top:16px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#fff">Reservación Confirmada ✓</div>
      </td></tr>
      <!-- Body -->
      <tr><td style="padding:36px 40px">
        <h2 style="font-family:Georgia,serif;font-size:1.5rem;color:#051E1C;margin:0 0 8px">Hola, {{ $reservation->first_name }}!</h2>
        <p style="font-size:.9rem;color:#3A9C97;margin:0 0 28px;line-height:1.7">Gracias por elegir Golden Cabo Transportation. Su reservación ha sido confirmada con los siguientes detalles:</p>
        <!-- Order box -->
        <div style="background:linear-gradient(135deg,#00AC97,#007B6D);border-radius:12px;padding:20px 24px;margin-bottom:24px;color:#fff">
          <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;opacity:.7;margin-bottom:6px">Número de Orden</div>
          <div style="font-family:Georgia,serif;font-size:1.8rem;font-weight:700">{{ $reservation->order_number }}</div>
          <div style="font-size:.85rem;opacity:.8;margin-top:4px">{{ $reservation->price }}</div>
        </div>
        <!-- Details table -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
          @foreach([
            ['Zona', 'Zona '.$reservation->zone.' — '.$reservation->zone_name],
            ['Hotel', $reservation->hotel],
            ['Pasajeros', $reservation->passengers],
            ['Dirección', $reservation->direction_label],
            ['Tipo de Servicio', $reservation->trip_label],
            ['Vuelo de Llegada', $reservation->arrival_flight ?: 'N/A'],
            ['Fecha de Llegada', $reservation->arrival_date ? $reservation->arrival_date->format('d/m/Y') : '—'],
            ['Hora de Llegada', $reservation->arrival_time ?? '—'],
          ] as [$label, $value])
          <tr>
            <td style="padding:11px 0;border-bottom:1px solid #EEF8F7;font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#8CCBC6;width:45%">{{ $label }}</td>
            <td style="padding:11px 0;border-bottom:1px solid #EEF8F7;font-size:.85rem;color:#051E1C">{{ $value }}</td>
          </tr>
          @endforeach
        </table>
        <!-- Meeting point -->
        <div style="background:#EEF8F7;border-radius:12px;padding:18px 20px;margin-top:24px;margin-bottom:24px;border-left:3px solid #00AC97">
          <div style="font-size:.7rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#00AC97;margin-bottom:6px">📍 Punto de Encuentro</div>
          <p style="font-size:.85rem;color:#1A6B67;margin:0;line-height:1.7">Le esperaremos afuera de la terminal, bajo la <strong>SOMBRILLA #10</strong>. Nuestro equipo portará un letrero con su nombre.</p>
        </div>
        <p style="font-size:.82rem;color:#8CCBC6;line-height:1.8">¿Necesita ayuda? Contáctenos al <strong style="color:#051E1C">(+52) 624 121 6527</strong> o por correo a <strong style="color:#00AC97">goldencabotransportation@gmail.com</strong></p>
      </td></tr>
      <!-- Footer -->
      <tr><td style="background:#051E1C;padding:20px 40px;text-align:center">
        <p style="font-family:Georgia,serif;font-style:italic;font-size:.85rem;color:rgba(0,172,151,.6);margin:0">"Ven a Los Cabos y déjanos la conducción a nosotros"</p>
        <p style="font-size:.7rem;color:rgba(255,255,255,.2);margin:8px 0 0">© {{ date('Y') }} CEUR Transportation S. de RL. de CV.</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>

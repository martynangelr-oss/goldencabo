<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirmación de Reservación — Golden Cabo Transportation</title>
<style>
body{margin:0;padding:0;background:#F0F9F8;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;color:#051E1C}
.wrap{max-width:620px;margin:24px auto;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(0,172,151,.12)}
.header{background:linear-gradient(135deg,#005F53,#00AC97);padding:36px 36px 28px;text-align:center}
.header h1{color:#fff;font-size:1.8rem;margin:0 0 6px;font-weight:700}
.header p{color:rgba(255,255,255,.7);font-size:.85rem;margin:0;letter-spacing:2px;text-transform:uppercase}
.order-badge{background:rgba(255,255,255,.15);border:2px solid rgba(255,255,255,.3);border-radius:100px;display:inline-block;padding:8px 24px;color:#fff;font-size:1.1rem;font-weight:700;margin-top:14px;letter-spacing:2px}
.body{padding:36px}
.greeting{font-size:1rem;margin-bottom:24px;line-height:1.7;color:#1A6B67}
.section{background:#F2FBFA;border-radius:14px;padding:20px 22px;margin-bottom:18px;border:1px solid rgba(0,172,151,.12)}
.section-title{font-size:.6rem;letter-spacing:3px;text-transform:uppercase;color:#00AC97;font-weight:700;margin-bottom:14px}
.row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(0,172,151,.08);font-size:.88rem}
.row:last-child{border-bottom:none}
.row .label{color:#8CCBC6;font-size:.82rem}
.row .value{font-weight:600;color:#051E1C}
.price-box{background:linear-gradient(135deg,#00AC97,#009988);border-radius:14px;padding:20px;text-align:center;margin:20px 0}
.price-box .amount{color:#fff;font-size:2.2rem;font-weight:700;line-height:1}
.price-box .label{color:rgba(255,255,255,.75);font-size:.7rem;letter-spacing:2px;text-transform:uppercase;margin-top:4px}
.meeting{background:#051E1C;border-radius:14px;padding:20px;margin:20px 0;color:#fff}
.meeting h3{color:#00AC97;font-size:.7rem;letter-spacing:2px;text-transform:uppercase;margin-bottom:10px}
.meeting p{font-size:.85rem;line-height:1.7;color:rgba(255,255,255,.75);margin:0}
.footer{background:#EEF8F7;padding:24px;text-align:center;font-size:.78rem;color:#8CCBC6;line-height:1.8}
.footer strong{color:#007B6D}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <p>Golden Cabo Transportation</p>
    <h1>¡Reservación Confirmada!</h1>
    <div class="order-badge">{{ $booking->order_number }}</div>
  </div>
  <div class="body">
    <p class="greeting">
      Estimado/a <strong>{{ $booking->full_name }}</strong>,<br>
      Gracias por elegir Golden Cabo Transportation. Su reservación ha sido confirmada exitosamente.
      A continuación encontrará los detalles de su traslado:
    </p>

    <div class="section">
      <div class="section-title">Detalles del Servicio</div>
      <div class="row"><span class="label">Zona</span><span class="value">Zona {{ $booking->zone }} — {{ $booking->zone_name }}</span></div>
      <div class="row"><span class="label">Hotel</span><span class="value">{{ $booking->hotel }}</span></div>
      <div class="row"><span class="label">Tipo de servicio</span><span class="value">{{ $booking->trip_label }}</span></div>
      <div class="row"><span class="label">Dirección</span><span class="value">{{ $booking->direction_label }}</span></div>
      <div class="row"><span class="label">Pasajeros</span><span class="value">{{ $booking->pax }}</span></div>
    </div>

    <div class="section">
      <div class="section-title">Información de Vuelo y Fecha</div>
      <div class="row"><span class="label">Vuelo de llegada</span><span class="value">{{ $booking->arrival_flight ?? 'N/A' }}</span></div>
      @if($booking->departure_flight)
      <div class="row"><span class="label">Vuelo de salida</span><span class="value">{{ $booking->departure_flight }}</span></div>
      @endif
      <div class="row"><span class="label">Fecha de llegada</span><span class="value">{{ $booking->arrival_date?->format('d/m/Y') }}</span></div>
      @if($booking->arrival_time)
      <div class="row"><span class="label">Hora de llegada</span><span class="value">{{ $booking->arrival_time }}</span></div>
      @endif
    </div>

    <div class="price-box">
      <div class="amount">${{ number_format($booking->price_usd, 0) }} USD</div>
      <div class="label">Precio Total del Servicio</div>
    </div>

    <div class="meeting">
      <h3>📍 Punto de Encuentro</h3>
      <p>Nuestro personal le estará esperando a la salida de la terminal, <strong>bajo la sombrilla #10</strong>, con un cartel con su nombre. Por favor preséntese tan pronto haya recogido su equipaje.</p>
    </div>
  </div>
  <div class="footer">
    <strong>CEUR Transportation S. de RL. de CV.</strong><br>
    Calle Huanacastle Esq. Eucalipto, San José del Cabo, BCS<br>
    📞 (+52) 333 303 4455 · (+52) 624 121 6527<br>
    ✉️ goldencabotransportation@gmail.com<br>
    🌐 www.goldencabotransportation.com<br><br>
    <em>"Ven a Los Cabos y déjanos la conducción a nosotros"</em>
  </div>
</div>
</body>
</html>

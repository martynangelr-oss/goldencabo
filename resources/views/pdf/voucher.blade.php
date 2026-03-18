<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body{font-family:DejaVu Sans,sans-serif;margin:0;padding:0;font-size:10px;color:#051E1C}
  .header{background:#00AC97;padding:20px 24px;color:white}
  .order-band{background:#051E1C;padding:10px 24px;color:white;font-size:11px}
  .body{padding:18px 24px}
  table{width:100%;border-collapse:collapse}
  th{background:#00AC97;color:white;padding:6px 8px;font-size:8px;text-align:left}
  td{padding:8px;border-bottom:1px solid #EEF8F7;font-size:9px}
  .zone-row td{background:#E0F5F3;font-weight:bold}
  .footer{background:#051E1C;color:rgba(255,255,255,.4);text-align:center;padding:10px;font-size:8px;margin-top:16px}
  .meeting{background:#E0F5F3;border-left:3px solid #00AC97;padding:10px 14px;margin:14px 0}
  .contact-box{background:#051E1C;color:white;padding:10px 14px;border-radius:6px}
</style>
</head>
<body>
<div class="header">
  <h1 style="margin:0;font-size:16px">CEUR TRANSPORTATION</h1>
  <div style="font-size:8px;letter-spacing:2px;opacity:.7">S. DE RL. DE CV. · www.goldencabotransportation.com</div>
  <div style="background:rgba(255,255,255,.2);display:inline-block;padding:4px 14px;border-radius:100px;margin-top:8px;font-size:8px;letter-spacing:1.5px">TRANSPORTATION VOUCHER</div>
</div>
<div class="order-band">ORDER # {{ $reservation->order_number }} · {{ $reservation->price }} · {{ $reservation->passengers }} PAX</div>
<div class="body">
  <p><strong>Dear {{ $reservation->full_name }},</strong><br><span style="color:#3A9C97">Thank you for choosing us as your transportation company in Los Cabos.</span></p>
  <table>
    <thead><tr><th>Zone</th><th>Destination</th><th>Round Trip</th><th>One Way</th></tr></thead>
    <tbody>
      @foreach($zones as $num => $zone)
      <tr @if(intval($num) === intval($reservation->zone)) class="zone-row" @endif>
        <td>Zone {{ $num }}</td><td>{{ $zone['name'] }}</td><td>{{ $zone['round'] }}</td><td>{{ $zone['oneway'] }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <table style="margin-top:14px">
    <tr><td style="width:30%;font-weight:bold;color:#3A9C97">Hotel</td><td>{{ $reservation->hotel }}</td><td style="width:30%;font-weight:bold;color:#3A9C97">Direction</td><td>{{ $reservation->direction_label }}</td></tr>
    <tr><td style="font-weight:bold;color:#3A9C97">Arrival Flight</td><td>{{ $reservation->arrival_flight }}</td><td style="font-weight:bold;color:#3A9C97">Date</td><td>{{ $reservation->arrival_date ? $reservation->arrival_date->format('d/m/Y') : '-' }}</td></tr>
    <tr><td style="font-weight:bold;color:#3A9C97">Trip Type</td><td>{{ $reservation->trip_label }}</td><td style="font-weight:bold;color:#3A9C97">Price</td><td><strong style="color:#00AC97">{{ $reservation->price }}</strong></td></tr>
  </table>
  <div class="meeting"><strong style="font-size:9px;letter-spacing:1px;color:#00AC97">MEETING POINT</strong><br>Outside the terminal, under UMBRELLA #10. Staff will hold a sign with your name.</div>
  <div class="contact-box"><strong style="color:#7FFFF0">LOCAL CONTACT: CESAR URBINA</strong><br><span style="color:rgba(255,255,255,.7)">011 52 624 121 65 27 · goldencabotransportation@gmail.com</span></div>
</div>
<div class="footer">CEUR Transportation S. de RL. de CV. · www.goldencabotransportation.com<br>"Ven a Los Cabos y dejanos la conduccion a nosotros"</div>
</body>
</html>

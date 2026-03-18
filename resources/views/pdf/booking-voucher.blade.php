<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #051E1C; background: #F0F9F8; }

  .wrap { max-width: 580px; margin: 20px auto; background: #fff; border-radius: 12px; overflow: hidden; }

  /* Header */
  .header { background: #005F53; padding: 28px 32px 22px; text-align: center; }
  .header-sub { color: rgba(255,255,255,.7); font-size: 7px; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 4px; }
  .header h1 { color: #fff; font-size: 18px; font-weight: bold; margin-bottom: 12px; }
  .order-badge { background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.3); border-radius: 100px; display: inline-block; padding: 6px 22px; color: #fff; font-size: 12px; font-weight: bold; letter-spacing: 2px; }

  /* Body */
  .body { padding: 24px 28px; }
  .greeting { font-size: 10px; margin-bottom: 18px; line-height: 1.7; color: #1A6B67; }

  /* Section card */
  .section { background: #F2FBFA; border-radius: 10px; padding: 16px 18px; margin-bottom: 14px; border: 1px solid rgba(0,172,151,.12); }
  .section-title { font-size: 7px; letter-spacing: 3px; text-transform: uppercase; color: #00AC97; font-weight: bold; margin-bottom: 10px; }
  .row { padding: 5px 0; border-bottom: 1px solid rgba(0,172,151,.10); font-size: 9px; }
  .row:last-child { border-bottom: none; }
  .row table { width: 100%; }
  .row .label { color: #8CCBC6; font-size: 8.5px; width: 42%; }
  .row .value { font-weight: bold; color: #051E1C; }

  /* Zones table */
  .zones-table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
  .zones-table th { background: #00AC97; color: #fff; padding: 5px 8px; text-align: left; font-size: 7.5px; }
  .zones-table td { padding: 5px 8px; border-bottom: 1px solid #EEF8F7; }
  .zones-table tr.zone-row td { background: #E0F5F3; font-weight: bold; }

  /* Price box */
  .price-box { background: #00AC97; border-radius: 10px; padding: 16px; text-align: center; margin: 14px 0; }
  .price-box .amount { color: #fff; font-size: 22px; font-weight: bold; }
  .price-box .price-label { color: rgba(255,255,255,.75); font-size: 7px; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }

  /* Meeting point */
  .meeting { background: #051E1C; border-radius: 10px; padding: 16px 18px; margin: 14px 0; color: #fff; }
  .meeting h3 { color: #00AC97; font-size: 7px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 8px; }
  .meeting p { font-size: 9px; line-height: 1.7; color: rgba(255,255,255,.75); }
  .meeting strong { color: #fff; }

  /* Footer */
  .footer { background: #EEF8F7; padding: 16px 20px; text-align: center; font-size: 7.5px; color: #8CCBC6; line-height: 1.9; }
  .footer strong { color: #007B6D; }
  .footer em { color: #3A9C97; }
</style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
  <div class="header">
    <div class="header-sub">Golden Cabo Transportation</div>
    <h1>{{ $t['confirmed'] }}</h1>
    <div class="order-badge">{{ $booking->order_number }}</div>
  </div>

  <div class="body">

    <p class="greeting">
      {{ $t['dear'] }} <strong>{{ $booking->full_name }}</strong>,<br>
      {{ $t['thank_you'] }}
    </p>

    {{-- Service details --}}
    <div class="section">
      <div class="section-title">{{ $t['service_details'] }}</div>
      <div class="row"><table><tr><td class="label">{{ $t['zone'] }}</td><td class="value">{{ $t['zone'] }} {{ $booking->zone }} — {{ $booking->zone_name }}</td></tr></table></div>
      <div class="row"><table><tr><td class="label">{{ $t['hotel'] }}</td><td class="value">{{ $booking->hotel }}</td></tr></table></div>
      <div class="row"><table><tr><td class="label">{{ $t['service_type'] }}</td><td class="value">{{ $t['trip_label'] }}</td></tr></table></div>
      <div class="row"><table><tr><td class="label">{{ $t['direction'] }}</td><td class="value">{{ $t['direction_label'] }}</td></tr></table></div>
      <div class="row"><table><tr><td class="label">{{ $t['passengers'] }}</td><td class="value">{{ $booking->pax }}</td></tr></table></div>
    </div>

    {{-- Flight info --}}
    <div class="section">
      <div class="section-title">{{ $t['flight_info'] }}</div>
      <div class="row"><table><tr><td class="label">{{ $t['arrival_flight'] }}</td><td class="value">{{ $booking->arrival_flight ?? 'N/A' }}</td></tr></table></div>
      @if($booking->departure_flight)
      <div class="row"><table><tr><td class="label">{{ $t['departure_flight'] }}</td><td class="value">{{ $booking->departure_flight }}</td></tr></table></div>
      @endif
      <div class="row"><table><tr><td class="label">{{ $t['arrival_date'] }}</td><td class="value">{{ $booking->arrival_date?->format('d/m/Y') }}</td></tr></table></div>
      @if($booking->arrival_time)
      <div class="row"><table><tr><td class="label">{{ $t['arrival_time'] }}</td><td class="value">{{ $booking->arrival_time }}</td></tr></table></div>
      @endif
    </div>

    {{-- Zone rates table --}}
    <div class="section">
      <div class="section-title">{{ $t['zone_rates'] }}</div>
      <table class="zones-table">
        <thead>
          <tr>
            <th>{{ $t['zone_col'] }}</th>
            <th>{{ $t['destination'] }}</th>
            <th>{{ $t['round_trip_col'] }}</th>
            <th>{{ $t['one_way_col'] }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($zones as $num => $zone)
          <tr @if(intval($num) === intval($booking->zone)) class="zone-row" @endif>
            <td>{{ $t['zone_col'] }} {{ $num }}</td>
            <td>{{ $zone['name'] }}</td>
            <td>{{ $zone['round'] }}</td>
            <td>{{ $zone['oneway'] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Price --}}
    <div class="price-box">
      <div class="amount">${{ number_format($booking->price_usd, 0) }} USD</div>
      <div class="price-label">{{ $t['total_price'] }}</div>
    </div>

    {{-- Meeting point --}}
    <div class="meeting">
      <h3>{{ $t['meeting_point'] }}</h3>
      <p>{!! $t['meeting_text'] !!}</p>
    </div>

  </div>

  {{-- Footer --}}
  <div class="footer">
    <strong>CEUR Transportation S. de RL. de CV.</strong><br>
    Calle Huanacastle Esq. Eucalipto, San José del Cabo, BCS<br>
    (+52) 333 303 4455 · (+52) 624 121 6527<br>
    goldencabotransportation@gmail.com · www.goldencabotransportation.com<br><br>
    <em>{{ $t['tagline'] }}</em>
  </div>

</div>
</body>
</html>

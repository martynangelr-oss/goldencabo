@php
  use App\Models\SiteSetting;
  $logoUrl  = SiteSetting::fileUrl('logo') ?? '';
  $dd       = $reservation->arrival_date ? $reservation->arrival_date->format('d') : '--';
  $mm       = $reservation->arrival_date ? $reservation->arrival_date->format('m') : '--';
  $yy       = $reservation->arrival_date ? $reservation->arrival_date->format('Y') : '--';
  $timeParts = $reservation->arrival_time ? explode(':', $reservation->arrival_time) : ['--','--'];
  $hh       = $timeParts[0] ?? '--';
  $mn       = str_pad($timeParts[1] ?? '--', 2, '0', STR_PAD_LEFT);
  $zonePrices = [1=>['r'=>'$100 USD','o'=>'$60 USD'],2=>['r'=>'$120 USD','o'=>'$65 USD'],3=>['r'=>'$140 USD','o'=>'$75 USD'],4=>['r'=>'$180 USD','o'=>'$100 USD']];
  $zp       = $zonePrices[$reservation->zone] ?? ['r'=>$reservation->price,'o'=>$reservation->price];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reservación {{ $reservation->order_number }} — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;600;700&family=Playfair+Display:wght@700&family=Lato:wght@300;400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="admin-wrap">
<nav class="admin-nav">
  <a href="{{ route('admin.dashboard') }}" style="font-family:'Playfair Display',serif;font-size:1rem;color:#fff">✦ Golden Cabo Admin</a>
  <div style="display:flex;gap:20px">
    <a href="{{ route('admin.bookings') }}">← Volver</a>
    <form method="POST" action="{{ route('logout') }}" style="margin:0">
      @csrf <button type="submit" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.5);font-family:'Josefin Sans',sans-serif;font-size:.75rem;letter-spacing:1.5px;text-transform:uppercase">Salir</button>
    </form>
  </div>
</nav>
<div class="admin-content">
  <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;color:#051E1C;margin-bottom:24px">
    Reservación #{{ $reservation->order_number }}
    <span class="badge-status badge-{{ $reservation->status }}" style="vertical-align:middle;margin-left:12px">{{ $reservation->status }}</span>
  </h2>
  @if(session('success'))<div style="background:#d4f8d4;border:1px solid #1a7a1a;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#1a7a1a;font-family:'Josefin Sans',sans-serif;font-size:.8rem">{{ session('success') }}</div>@endif
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    <div class="admin-card">
      <h3 style="font-family:'Josefin Sans',sans-serif;font-size:.7rem;letter-spacing:2px;text-transform:uppercase;color:#3A9C97;margin-bottom:18px">Datos del Pasajero</h3>
      @foreach([['Nombre Completo', $reservation->full_name],['Email',$reservation->email],['Teléfono',$reservation->phone],['Hotel',$reservation->hotel],['Pasajeros',$reservation->passengers]] as [$l,$v])
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #EEF8F7">
        <span style="font-family:'Josefin Sans',sans-serif;font-size:.7rem;color:#8CCBC6;font-weight:700;text-transform:uppercase;letter-spacing:1px">{{$l}}</span>
        <span style="font-family:'Lato',sans-serif;font-size:.85rem;color:#051E1C">{{$v}}</span>
      </div>
      @endforeach
    </div>
    <div class="admin-card">
      <h3 style="font-family:'Josefin Sans',sans-serif;font-size:.7rem;letter-spacing:2px;text-transform:uppercase;color:#3A9C97;margin-bottom:18px">Detalles del Servicio</h3>
      @foreach([['Zona','Zona '.$reservation->zone.' — '.$reservation->zone_name],['Dirección',$reservation->direction_label],['Tipo',$reservation->trip_label],['Precio',$reservation->price],['Vuelo Llegada',$reservation->arrival_flight],['Vuelo Salida',$reservation->departure_flight],['Fecha Llegada',$reservation->arrival_date ? $reservation->arrival_date->format('d/m/Y') : '—'],['Hora',$reservation->arrival_time ?? '—']] as [$l,$v])
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #EEF8F7">
        <span style="font-family:'Josefin Sans',sans-serif;font-size:.7rem;color:#8CCBC6;font-weight:700;text-transform:uppercase;letter-spacing:1px">{{$l}}</span>
        <span style="font-family:'Lato',sans-serif;font-size:.85rem;color:#051E1C">{{$v}}</span>
      </div>
      @endforeach
    </div>
  </div>
  <div class="admin-card" style="margin-top:24px">
    <h3 style="font-family:'Josefin Sans',sans-serif;font-size:.7rem;letter-spacing:2px;text-transform:uppercase;color:#3A9C97;margin-bottom:18px">Actualizar Estado</h3>
    <form method="POST" action="{{ route('admin.booking.update', $reservation->id) }}" style="display:flex;gap:12px;align-items:center">
      @csrf @method('PUT')
      <select name="status" style="padding:10px 14px;border-radius:10px;border:1.5px solid #D4EDEB;background:#F2FBFA;font-size:.85rem;outline:none">
        @foreach(['pending','confirmed','completed','cancelled'] as $s)
          <option value="{{$s}}" @selected($reservation->status===$s)>{{ucfirst($s)}}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-teal btn-sm">Guardar</button>
    </form>
  </div>
  <div class="admin-card" style="margin-top:24px;display:flex;align-items:center;gap:16px">
    <h3 style="font-family:'Josefin Sans',sans-serif;font-size:.7rem;letter-spacing:2px;text-transform:uppercase;color:#3A9C97;margin:0">Voucher</h3>
    <button id="admin-pdf-btn" onclick="adminDownloadPDF()" class="btn btn-teal btn-sm" style="display:flex;align-items:center;gap:8px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Descargar Voucher PDF
    </button>
  </div>
</div>
<script>
window.SITE_LOGO = @json($logoUrl);
window.bk = {
  order:    @json($reservation->order_number),
  zone:     @json((string) $reservation->zone),
  zoneName: @json($reservation->zone_name),
  priceR:   @json($zp['r']),
  priceO:   @json($zp['o']),
  hotel:    @json($reservation->hotel),
  name:     @json($reservation->first_name),
  last:     @json($reservation->last_name),
  email:    @json($reservation->email),
  pax:      {{ (int) $reservation->passengers }},
  dir:      @json($reservation->direction),
  trip:     @json($reservation->trip_type),
  arrFlt:   @json($reservation->arrival_flight ?? ''),
  depFlt:   @json($reservation->departure_flight ?? ''),
  dd:       @json($dd),
  mm:       @json($mm),
  yy:       @json($yy),
  hh:       @json($hh),
  mn:       @json($mn),
};

// ── Font helpers ──
let _poppinsReg = null, _poppinsBold = null;
async function _ensurePoppins() {
  if (_poppinsReg) return true;
  try {
    const toB64 = async url => {
      const resp = await fetch(url);
      if (!resp.ok) throw new Error('Font fetch failed: ' + resp.status);
      const buf = await resp.arrayBuffer();
      const bytes = new Uint8Array(buf);
      let s = '';
      for (let i = 0; i < bytes.byteLength; i++) s += String.fromCharCode(bytes[i]);
      return btoa(s);
    };
    [_poppinsReg, _poppinsBold] = await Promise.all([
      toB64('https://fonts.gstatic.com/s/poppins/v21/pxiEyp8kv8JHgFVrJJfecg.ttf'),
      toB64('https://fonts.gstatic.com/s/poppins/v21/pxiByp8kv8JHgFVrLCz7Z1JlFd2JQEl8qg.ttf'),
    ]);
    return true;
  } catch (e) { return false; }
}
function _addPoppins(doc) {
  if (!_poppinsReg) return false;
  doc.addFileToVFS('Poppins-Regular.ttf', _poppinsReg);
  doc.addFileToVFS('Poppins-Bold.ttf',    _poppinsBold);
  doc.addFont('Poppins-Regular.ttf', 'Poppins', 'normal');
  doc.addFont('Poppins-Bold.ttf',    'Poppins', 'bold');
  return true;
}

// ── Logo helper ──
let _logoInfo = undefined;
async function _ensureLogo() {
  if (_logoInfo !== undefined) return _logoInfo;
  const url = window.SITE_LOGO;
  if (!url) { _logoInfo = null; return null; }
  try {
    const img = new Image();
    await new Promise((res, rej) => { img.onload = res; img.onerror = rej; img.src = url; });
    const cv = document.createElement('canvas');
    cv.width = img.naturalWidth; cv.height = img.naturalHeight;
    cv.getContext('2d').drawImage(img, 0, 0);
    _logoInfo = { b64: cv.toDataURL('image/png'), w: img.naturalWidth, h: img.naturalHeight };
  } catch (e) { _logoInfo = null; }
  return _logoInfo;
}

async function generatePDF() {
  await Promise.all([_ensurePoppins(), _ensureLogo()]);
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'letter' });
  let hp = false;
  try { hp = _addPoppins(doc); } catch(e) { hp = false; }
  const f  = hp ? 'Poppins' : 'helvetica';
  const b  = bk;
  const W  = 215.9, H = 279.4;

  const teal   = [0, 172, 151];
  const tealLt = [232, 248, 246];
  const dark   = [22, 32, 42];
  const mid    = [95, 110, 125];
  const light  = [246, 248, 250];
  const border = [218, 226, 232];
  const white  = [255, 255, 255];

  const dirTxt  = b.dir === 'air' ? 'Del Aeropuerto al Hotel' : 'Del Hotel al Aeropuerto';
  const tripTxt = b.trip === 'rnd' ? 'Ida y vuelta' : 'Solo ida';
  const price   = b.trip === 'rnd' ? b.priceR : b.priceO;
  const zones   = [
    { z: 'Zona 1', n: 'San José del Cabo',  r: '$100 USD', o: '$60 USD'  },
    { z: 'Zona 2', n: 'Corredor Turístico', r: '$120 USD', o: '$65 USD'  },
    { z: 'Zona 3', n: 'Cabo San Lucas',     r: '$140 USD', o: '$75 USD'  },
    { z: 'Zona 4', n: 'Lado del Pacífico',  r: '$180 USD', o: '$100 USD' },
  ];

  doc.setFillColor(...teal); doc.rect(0, 0, W, 3.5, 'F');
  doc.setFillColor(...white); doc.rect(0, 3.5, W, 54.5, 'F');

  let logoBottom = 3.5;
  if (_logoInfo) {
    const maxW = 42, maxH = 20;
    const ratio = Math.min(maxW / _logoInfo.w, maxH / _logoInfo.h);
    const lw = _logoInfo.w * ratio, lh = _logoInfo.h * ratio;
    doc.addImage(_logoInfo.b64, 'PNG', 14, 9, lw, lh);
    logoBottom = 9 + lh + 3;
  } else {
    doc.setFont(f, 'bold'); doc.setFontSize(15); doc.setTextColor(...teal);
    doc.text('GOLDEN CABO', 14, 21);
    doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
    doc.text('TRANSPORTATION', 14, 27);
    logoBottom = 31;
  }
  doc.setFont(f, 'normal'); doc.setFontSize(7); doc.setTextColor(...mid);
  doc.text('(+52) 333 303 4455  ·  (+52) 624 121 6527  ·  goldencabotransportation@gmail.com', 14, logoBottom + 3);

  doc.setFillColor(...teal); doc.roundedRect(W - 74, 8, 60, 10, 2, 2, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(7.5); doc.setTextColor(...white);
  doc.text('TRANSPORTATION VOUCHER', W - 73, 14.5);

  doc.setFont(f, 'bold'); doc.setFontSize(11); doc.setTextColor(...dark);
  doc.text('Order #' + b.order, W - 44, 28, { align: 'center' });
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  doc.text(b.pax + ' pasajero' + (b.pax > 1 ? 's' : '') + '  ·  ' + tripTxt, W - 44, 34, { align: 'center' });

  doc.setFillColor(...tealLt); doc.setDrawColor(...teal);
  doc.roundedRect(W - 60, 37.5, 46, 14, 2.5, 2.5, 'FD');
  doc.setFont(f, 'bold'); doc.setFontSize(14); doc.setTextColor(...teal);
  doc.text(price, W - 37, 47, { align: 'center' });

  doc.setFillColor(...border); doc.rect(0, 58, W, 0.5, 'F');
  doc.setFillColor(...light); doc.rect(0, 58.5, W, 22, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(11); doc.setTextColor(...dark);
  doc.text('Estimado/a ' + b.name + (b.last ? ' ' + b.last : '') + ',', 14, 69);
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  doc.text('Gracias por elegir Golden Cabo Transportation. A continuación los detalles de su servicio.', 14, 75.5, { maxWidth: W - 28 });

  let y = 88;
  doc.setFont(f, 'bold'); doc.setFontSize(7); doc.setTextColor(...teal);
  doc.text('TARIFAS POR ZONA', 14, y);
  doc.setFillColor(...border); doc.rect(14, y + 2, W - 28, 0.4, 'F');
  y += 7;

  const cw = (W - 28) / 4;
  doc.setFillColor(...dark); doc.roundedRect(14, y, W - 28, 8, 1.5, 1.5, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(6.5); doc.setTextColor(...white);
  doc.text('ZONA', 17, y + 5.2);
  doc.text('DESTINO', 17 + cw, y + 5.2);
  doc.text('IDA Y VUELTA', 17 + cw * 2, y + 5.2);
  doc.text('SOLO IDA', 17 + cw * 3, y + 5.2);
  y += 8;

  zones.forEach((z, i) => {
    const sel = String(i + 1) === String(b.zone);
    if (sel) {
      doc.setFillColor(...tealLt); doc.rect(14, y, W - 28, 9, 'F');
      doc.setFillColor(...teal);   doc.rect(14, y, 3.5, 9, 'F');
    } else {
      doc.setFillColor(i % 2 === 0 ? 255 : 250, i % 2 === 0 ? 255 : 252, i % 2 === 0 ? 255 : 251);
      doc.rect(14, y, W - 28, 9, 'F');
    }
    doc.setFont(f, sel ? 'bold' : 'normal');
    doc.setFontSize(7.5);
    doc.setTextColor(...(sel ? teal : dark));
    doc.text(z.z, sel ? 20.5 : 17, y + 6.2);
    doc.text(z.n, 17 + cw, y + 6.2);
    doc.text(z.r, 17 + cw * 2, y + 6.2);
    doc.text(z.o, 17 + cw * 3, y + 6.2);
    y += 9;
  });

  y += 8;
  doc.setFont(f, 'bold'); doc.setFontSize(7); doc.setTextColor(...teal);
  doc.text('INFORMACIÓN DEL VIAJE', 14, y);
  doc.setFillColor(...border); doc.rect(14, y + 2, W - 28, 0.4, 'F');
  y += 8;

  const bw  = (W - 28) / 2 - 4;
  const bx2 = 14 + bw + 8;
  const cardH = 46;

  doc.setFillColor(...white); doc.setDrawColor(...border);
  doc.roundedRect(14, y, bw, cardH, 2, 2, 'FD');
  doc.setFillColor(...teal); doc.roundedRect(14, y, 3.5, cardH, 1, 1, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(6.5); doc.setTextColor(...teal);
  doc.text('VUELO / LLEGADA', 21, y + 8);
  doc.setFont(f, 'bold'); doc.setFontSize(8.5); doc.setTextColor(...dark);
  doc.text(dirTxt, 21, y + 17);
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  doc.text('Vuelo:   ' + b.arrFlt, 21, y + 26);
  doc.text('Fecha:  ' + b.dd + '/' + b.mm + '/' + b.yy, 21, y + 32);
  doc.text('Hora:    ' + b.hh + ':' + b.mn, 21, y + 38);

  doc.setFillColor(...white); doc.setDrawColor(...border);
  doc.roundedRect(bx2, y, bw, cardH, 2, 2, 'FD');
  doc.setFillColor(...teal); doc.roundedRect(bx2, y, 3.5, cardH, 1, 1, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(6.5); doc.setTextColor(...teal);
  doc.text('HOTEL / SERVICIO', bx2 + 7, y + 8);
  doc.setFont(f, 'bold'); doc.setFontSize(8.5); doc.setTextColor(...dark);
  doc.text(b.hotel, bx2 + 7, y + 17, { maxWidth: bw - 10 });
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  doc.text('Tipo:         ' + tripTxt, bx2 + 7, y + 28);
  doc.text('Vuelo salida: ' + (b.trip === 'rnd' ? b.depFlt : 'N/A'), bx2 + 7, y + 34);

  y += cardH + 8;
  doc.setFillColor(...tealLt); doc.setDrawColor(...teal);
  doc.roundedRect(14, y, W - 28, 26, 2, 2, 'FD');
  doc.setFont(f, 'bold'); doc.setFontSize(7.5); doc.setTextColor(...teal);
  doc.text('PUNTO DE ENCUENTRO', 18, y + 9);
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(25, 75, 70);
  doc.text('Nos encontrará fuera de la terminal, bajo la SOMBRILLA #10.', 18, y + 17);
  doc.text('El personal le esperará con un letrero con su nombre.', 18, y + 23);

  y += 32;
  doc.setFillColor(...light); doc.setDrawColor(...border);
  doc.roundedRect(14, y, W - 28, 18, 2, 2, 'FD');
  doc.setFont(f, 'bold'); doc.setFontSize(7.5); doc.setTextColor(...dark);
  doc.text('CONTACTO LOCAL:  CESAR URBINA', 18, y + 8);
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  doc.text('Tel: 011 52 624 121 65 27  ·  goldencabotransportation@gmail.com', 18, y + 14.5);

  doc.setFillColor(...light); doc.rect(0, H - 16, W, 16, 'F');
  doc.setFillColor(...teal);  doc.rect(0, H - 16, W, 1.5, 'F');
  doc.setFont(f, 'normal'); doc.setFontSize(7); doc.setTextColor(...mid);
  doc.text('Golden Cabo Transportation  ·  www.goldencabotransportation.com', W / 2, H - 8.5, { align: 'center' });
  doc.text('"Ven a Los Cabos y déjanos la conducción a nosotros"', W / 2, H - 3.5, { align: 'center' });

  return doc;
}

async function adminDownloadPDF() {
  const btn = document.getElementById('admin-pdf-btn');
  const orig = btn ? btn.innerHTML : '';
  if (btn) { btn.disabled = true; btn.textContent = 'Generando PDF...'; }
  try {
    if (!window.jspdf) throw new Error('jsPDF no disponible');
    const doc = await generatePDF();
    doc.save('GoldenCabo_Voucher_' + bk.order + '.pdf');
  } catch(e) {
    console.error('PDF generation error:', e);
    alert('No se pudo generar el voucher. Por favor intente de nuevo.');
  } finally {
    if (btn) { btn.disabled = false; btn.innerHTML = orig; }
  }
}
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reservaciones — Admin Golden Cabo</title>
<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;600;700&family=Playfair+Display:wght@700&family=Lato:wght@300;400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-wrap">
<nav class="admin-nav">
  <a href="{{ route('admin.dashboard') }}" style="font-family:'Playfair Display',serif;font-size:1rem;color:#fff">✦ Golden Cabo Admin</a>
  <div style="display:flex;gap:20px">
    <a href="{{ route('admin.reservations') }}">Reservaciones</a>
    <a href="{{ route('admin.contacts') }}">Contactos</a>
    <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
      @csrf <button type="submit" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.5);font-family:'Josefin Sans',sans-serif;font-size:.75rem;letter-spacing:1.5px;text-transform:uppercase">Salir</button>
    </form>
  </div>
</nav>
<div class="admin-content">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:14px">
    <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;color:#051E1C">Reservaciones</h2>
    <form method="GET" style="display:flex;gap:10px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." style="padding:9px 14px;border-radius:10px;border:1.5px solid #D4EDEB;background:#F2FBFA;font-size:.85rem;outline:none">
      <button type="submit" class="btn btn-teal btn-sm">Buscar</button>
    </form>
  </div>
  <div class="admin-card">
    <table class="admin-table">
      <thead><tr><th>Order #</th><th>Pasajero</th><th>Email</th><th>Hotel</th><th>Zona</th><th>PAX</th><th>Precio</th><th>Fecha Llegada</th><th>Estado</th><th></th></tr></thead>
      <tbody>
        @forelse($reservations as $r)
        <tr>
          <td style="font-weight:600;color:#00AC97">{{ $r->order_number }}</td>
          <td>{{ $r->full_name }}</td>
          <td style="font-size:.78rem;color:#3A9C97">{{ $r->email }}</td>
          <td>{{ $r->hotel }}</td>
          <td>Zona {{ $r->zone }}</td>
          <td style="text-align:center">{{ $r->passengers }}</td>
          <td style="font-weight:700">{{ $r->price }}</td>
          <td>{{ $r->arrival_date ? $r->arrival_date->format('d/m/Y') : '—' }}</td>
          <td><span class="badge-status badge-{{ $r->status }}">{{ $r->status }}</span></td>
          <td><a href="{{ route('admin.reservations.show', $r->id) }}" class="btn btn-outline btn-sm" style="font-size:.6rem;padding:7px 14px">Ver</a></td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;padding:32px;color:#8CCBC6">No hay reservaciones.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:20px">{{ $reservations->links() }}</div>
  </div>
</div>
</body>
</html>

@extends('layouts.admin')
@section('page-title', 'Dashboard')
@section('content')

{{-- Stat Cards --}}
<div class="stats-grid">

  <div class="stat-card accent">
    <div class="stat-icon-wrap">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
      </svg>
    </div>
    <div>
      <div class="stat-num">{{ $totalReservations }}</div>
      <div class="stat-lbl">Total Reservaciones</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon-wrap">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
    </div>
    <div>
      <div class="stat-num">{{ $confirmedToday }}</div>
      <div class="stat-lbl">Reservas Hoy</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon-wrap">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
        <polyline points="22,6 12,13 2,6"/>
      </svg>
    </div>
    <div>
      <div class="stat-num">{{ $totalContacts }}</div>
      <div class="stat-lbl">Mensajes</div>
    </div>
  </div>

  @if($unreadContacts > 0)
  <div class="stat-card" style="border-left:3px solid var(--warn)">
    <div class="stat-icon-wrap" style="background:#FEF3C7">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <div>
      <div class="stat-num" style="color:#D97706">{{ $unreadContacts }}</div>
      <div class="stat-lbl">Sin Leer</div>
    </div>
  </div>
  @endif

</div>

{{-- Recent bookings --}}
<div class="card">
  <div class="card-header">
    <div class="card-title">
      <span class="ct-icon">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
        </svg>
      </span>
      Reservaciones Recientes
    </div>
    <a href="{{ route('admin.bookings') }}" class="btn btn-ghost btn-sm">
      Ver todas
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order #</th>
          <th>Pasajero</th>
          <th>Hotel</th>
          <th>Zona</th>
          <th>Precio</th>
          <th>Llegada</th>
          <th>Estado</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($recent as $r)
        <tr>
          <td><span style="font-weight:700;color:var(--teal);font-family:monospace;font-size:.8rem">{{ $r->order_number }}</span></td>
          <td style="font-weight:500">{{ $r->full_name }}</td>
          <td style="color:var(--txt2)">{{ Str::limit($r->hotel, 22) }}</td>
          <td><span class="badge badge-teal">Zona {{ $r->zone }}</span></td>
          <td style="font-weight:700">${{ number_format($r->price_usd, 0) }}</td>
          <td style="color:var(--txt2)">{{ $r->arrival_date?->format('d/m/Y') ?? '—' }}</td>
          <td>
            @switch($r->status)
              @case('confirmed') <span class="badge badge-green">Confirmada</span> @break
              @case('pending')   <span class="badge badge-yellow">Pendiente</span> @break
              @case('cancelled') <span class="badge badge-red">Cancelada</span> @break
              @case('completed') <span class="badge badge-blue">Completada</span> @break
              @default           <span class="badge badge-gray">{{ $r->status }}</span>
            @endswitch
          </td>
          <td>
            <a href="{{ route('admin.booking.show', $r->id) }}" class="btn btn-ghost btn-sm">Ver</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" style="text-align:center;padding:40px;color:var(--txt2)">
            No hay reservaciones aún.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@extends('layouts.admin')
@section('page-title', 'Reservación ' . $booking->order_number)
@section('content')

{{-- Back + header --}}
<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('admin.bookings') }}" class="btn btn-ghost btn-sm" style="padding:6px 10px">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
    </a>
    <div>
      <h1 style="display:flex;align-items:center;gap:10px">
        {{ $booking->order_number }}
        @switch($booking->status)
          @case('confirmed') <span class="badge badge-green">Confirmada</span> @break
          @case('pending')   <span class="badge badge-yellow">Pendiente</span> @break
          @case('cancelled') <span class="badge badge-red">Cancelada</span>   @break
          @case('completed') <span class="badge badge-blue">Completada</span> @break
        @endswitch
      </h1>
      <p>Creada el {{ $booking->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

  {{-- Booking details --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <span class="ct-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
        </span>
        Detalles del Servicio
      </div>
    </div>
    <div class="detail-row"><span class="detail-label">Order #</span><span class="detail-value" style="font-family:monospace;font-weight:700;color:var(--teal)">{{ $booking->order_number }}</span></div>
    <div class="detail-row"><span class="detail-label">Zona</span><span class="detail-value"><span class="badge badge-teal">Zona {{ $booking->zone }} — {{ $booking->zone_name }}</span></span></div>
    <div class="detail-row"><span class="detail-label">Tipo</span><span class="detail-value">{{ $booking->trip_label }}</span></div>
    <div class="detail-row"><span class="detail-label">Dirección</span><span class="detail-value">{{ $booking->direction_label }}</span></div>
    <div class="detail-row"><span class="detail-label">Precio</span><span class="detail-value" style="font-weight:700;font-size:.95rem;color:var(--teal)">${{ number_format($booking->price_usd, 2) }} USD</span></div>
    <div class="detail-row"><span class="detail-label">Pasajeros</span><span class="detail-value">{{ $booking->pax }}</span></div>
    <div class="detail-row"><span class="detail-label">Hotel</span><span class="detail-value">{{ $booking->hotel }}</span></div>
    <div class="detail-row"><span class="detail-label">Vuelo Llegada</span><span class="detail-value">{{ $booking->arrival_flight ?? '—' }}</span></div>
    <div class="detail-row"><span class="detail-label">Vuelo Salida</span><span class="detail-value">{{ $booking->departure_flight ?? '—' }}</span></div>
    <div class="detail-row"><span class="detail-label">Fecha Llegada</span><span class="detail-value">{{ $booking->arrival_date?->format('d/m/Y') }}</span></div>
    <div class="detail-row"><span class="detail-label">Hora Llegada</span><span class="detail-value">{{ $booking->arrival_time ?? '—' }}</span></div>
    <div class="detail-row">
      <span class="detail-label">Voucher</span>
      <span class="detail-value">
        @if($booking->voucher_sent)
          <span class="badge badge-green">Enviado</span>
        @else
          <span class="badge badge-gray">No enviado</span>
        @endif
      </span>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:20px">

    {{-- Passenger --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <span class="ct-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </span>
          Información del Pasajero
        </div>
      </div>
      <div class="detail-row"><span class="detail-label">Nombre</span><span class="detail-value" style="font-weight:600">{{ $booking->full_name }}</span></div>
      <div class="detail-row">
        <span class="detail-label">Email</span>
        <span class="detail-value">
          <a href="mailto:{{ $booking->email }}" style="color:var(--teal)">{{ $booking->email }}</a>
        </span>
      </div>
      <div class="detail-row"><span class="detail-label">Teléfono</span><span class="detail-value">{{ $booking->phone }}</span></div>
    </div>

    {{-- Update status --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <span class="ct-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </span>
          Actualizar Estado
        </div>
      </div>
      <form method="POST" action="{{ route('admin.booking.update', $booking) }}">
        @csrf @method('PUT')
        <div class="fg">
          <label>Estado</label>
          <select name="status">
            @foreach(['pending' => 'Pendiente', 'confirmed' => 'Confirmada', 'cancelled' => 'Cancelada', 'completed' => 'Completada'] as $val => $label)
              <option value="{{ $val }}" {{ $booking->status == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>Notas internas</label>
          <textarea name="notes" placeholder="Agregar notas...">{{ $booking->notes }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          Guardar Cambios
        </button>
      </form>
    </div>

    {{-- Actions --}}
    <div class="card">
      <div class="card-title" style="margin-bottom:14px">Acciones</div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
          <a href="{{ route('admin.booking.pdf', $booking) }}?lang=es" target="_blank" class="btn btn-primary" style="justify-content:center;text-decoration:none">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            PDF — ES
          </a>
          <a href="{{ route('admin.booking.pdf', $booking) }}?lang=en" target="_blank" class="btn btn-ghost" style="justify-content:center;text-decoration:none">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            PDF — EN
          </a>
        </div>
        <form method="POST" action="{{ route('admin.booking.destroy', $booking) }}"
              onsubmit="return confirm('¿Eliminar esta reservación? Esta acción no se puede deshacer.')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
            </svg>
            Eliminar Reservación
          </button>
        </form>
      </div>
    </div>

  </div>
</div>

@endsection

@extends('layouts.admin')
@section('page-title', 'Reservaciones')
@section('content')

<div class="page-header">
  <div>
    <h1>Reservaciones</h1>
    <p>Gestión de todas las reservaciones del sistema</p>
  </div>
</div>

{{-- Filters --}}
<div class="card" style="padding:16px 20px;margin-bottom:16px">
  <form method="GET" action="{{ route('admin.bookings') }}" class="search-bar">
    <div class="search-input-wrap">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--txt2)">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" name="search" value="{{ request('search') }}"
             placeholder="Buscar por nombre, email, order #..."
             style="width:100%;padding:9px 13px 9px 36px;border-radius:8px;border:1.5px solid var(--border);
                    font-family:'Inter',sans-serif;font-size:.82rem;color:var(--txt);outline:none">
    </div>
    <select name="status" style="padding:9px 13px;border-radius:8px;border:1.5px solid var(--border);
            font-family:'Inter',sans-serif;font-size:.82rem;color:var(--txt);background:var(--white);outline:none;cursor:pointer">
      <option value="">Todos los estados</option>
      <option value="confirmed"  {{ request('status')=='confirmed'  ?'selected':'' }}>Confirmada</option>
      <option value="pending"    {{ request('status')=='pending'    ?'selected':'' }}>Pendiente</option>
      <option value="cancelled"  {{ request('status')=='cancelled'  ?'selected':'' }}>Cancelada</option>
      <option value="completed"  {{ request('status')=='completed'  ?'selected':'' }}>Completada</option>
    </select>
    <select name="zone" style="padding:9px 13px;border-radius:8px;border:1.5px solid var(--border);
            font-family:'Inter',sans-serif;font-size:.82rem;color:var(--txt);background:var(--white);outline:none;cursor:pointer">
      <option value="">Todas las zonas</option>
      @foreach([1,2,3,4] as $z)
        <option value="{{ $z }}" {{ request('zone')==$z?'selected':'' }}>Zona {{ $z }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      Filtrar
    </button>
    @if(request('search') || request('status') || request('zone'))
      <a href="{{ route('admin.bookings') }}" class="btn btn-ghost btn-sm">Limpiar</a>
    @endif
  </form>
</div>

{{-- Table --}}
<div class="card" style="padding:0;overflow:hidden">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order #</th>
          <th>Pasajero</th>
          <th>Email</th>
          <th>Zona</th>
          <th>Hotel</th>
          <th>PAX</th>
          <th>Llegada</th>
          <th>Precio</th>
          <th>Estado</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($bookings as $b)
        <tr>
          <td>
            <span style="font-weight:700;color:var(--teal);font-family:monospace;font-size:.8rem">
              {{ $b->order_number }}
            </span>
          </td>
          <td style="font-weight:500">{{ $b->full_name }}</td>
          <td style="color:var(--txt2);font-size:.78rem">{{ $b->email }}</td>
          <td><span class="badge badge-teal">Zona {{ $b->zone }}</span></td>
          <td style="color:var(--txt2)">{{ Str::limit($b->hotel, 22) }}</td>
          <td style="text-align:center;font-weight:600">{{ $b->pax }}</td>
          <td style="color:var(--txt2)">{{ $b->arrival_date?->format('d/m/Y') ?? '—' }}</td>
          <td style="font-weight:700">${{ number_format($b->price_usd, 0) }}</td>
          <td>
            @switch($b->status)
              @case('confirmed') <span class="badge badge-green">Confirmada</span> @break
              @case('pending')   <span class="badge badge-yellow">Pendiente</span> @break
              @case('cancelled') <span class="badge badge-red">Cancelada</span>   @break
              @case('completed') <span class="badge badge-blue">Completada</span> @break
              @default           <span class="badge badge-gray">{{ $b->status }}</span>
            @endswitch
          </td>
          <td>
            <a href="{{ route('admin.booking.show', $b) }}" class="btn btn-ghost btn-sm">Ver</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" style="text-align:center;padding:48px;color:var(--txt2)">
            No se encontraron reservaciones.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($bookings->hasPages())
  <div style="padding:14px 20px;border-top:1px solid var(--border)">
    <div class="pagination-wrap">{{ $bookings->links() }}</div>
  </div>
  @endif
</div>

@endsection

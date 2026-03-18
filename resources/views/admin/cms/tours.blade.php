@extends('layouts.admin')
@section('page-title', 'Tours')
@section('content')

<div class="page-header">
  <div>
    <h1>Tours</h1>
    <p>Gestiona los recorridos disponibles</p>
  </div>
  <a href="{{ route('admin.cms.tours.create') }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nuevo Tour
  </a>
</div>

@if(session('success'))
  <div style="background:#D1FAE5;color:#065F46;border-radius:var(--radius-sm);padding:10px 16px;font-size:.85rem;margin-bottom:16px">{{ session('success') }}</div>
@endif

<div class="card" style="padding:0;overflow:hidden">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:60px">Imagen</th>
          <th>Nombre</th>
          <th>Duración</th>
          <th>Destinos</th>
          <th>Precio</th>
          <th style="text-align:center">Activo</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($tours as $t)
        <tr>
          <td>
            <img src="{{ $t->image_url }}" alt="{{ $t->name }}"
                 style="width:48px;height:36px;object-fit:cover;border-radius:6px">
          </td>
          <td style="font-weight:600">{{ $t->name }}</td>
          <td style="color:var(--txt2)">{{ $t->duration ?? '—' }}</td>
          <td>
            @foreach(array_slice($t->destinations ?? [], 0, 3) as $d)
              <span class="badge badge-teal" style="margin:1px 2px">{{ $d }}</span>
            @endforeach
            @if(count($t->destinations ?? []) > 3)
              <span style="font-size:.72rem;color:var(--txt2)">+{{ count($t->destinations) - 3 }}</span>
            @endif
          </td>
          <td style="font-weight:700;color:var(--teal)">${{ number_format($t->price_usd, 0) }} USD</td>
          <td style="text-align:center">
            <button onclick="toggleTour({{ $t->id }}, this)"
                    class="toggle-btn {{ $t->is_active ? 'on' : '' }}">
              <span class="toggle-dot"></span>
            </button>
          </td>
          <td style="white-space:nowrap">
            <a href="{{ route('admin.cms.tours.edit', $t) }}" class="btn btn-ghost btn-sm">Editar</a>
            <form method="POST" action="{{ route('admin.cms.tours.destroy', $t) }}"
                  style="display:inline" onsubmit="return confirm('¿Eliminar este tour?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm">Eliminar</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:48px;color:var(--txt2)">
            No hay tours registrados. <a href="{{ route('admin.cms.tours.create') }}">Crear el primero</a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<style>
.toggle-btn{width:40px;height:22px;border-radius:100px;border:none;background:#CBD5E1;cursor:pointer;position:relative;transition:background .2s;padding:0;display:inline-flex;align-items:center}
.toggle-btn.on{background:var(--teal)}
.toggle-dot{width:16px;height:16px;border-radius:50%;background:#fff;position:absolute;left:3px;transition:left .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.toggle-btn.on .toggle-dot{left:21px}
</style>
<script>
async function toggleTour(id, btn) {
  const r = await fetch(`/admin/cms/tours/${id}/toggle`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
  });
  const d = await r.json();
  btn.classList.toggle('on', d.is_active);
}
</script>

@endsection

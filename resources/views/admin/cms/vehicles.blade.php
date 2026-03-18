@extends('layouts.admin')
@section('page-title', 'Vehículos')
@section('content')

<div class="page-header">
  <div>
    <h1>Vehículos</h1>
    <p>Gestiona la flota disponible para traslados</p>
  </div>
  <a href="{{ route('admin.cms.vehicles.create') }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nuevo Vehículo
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card" style="padding:0;overflow:hidden">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:60px">Imagen</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Servicios</th>
          <th style="text-align:center">PAX</th>
          <th style="text-align:center">Disponible</th>
          <th style="text-align:center">Orden</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($vehicles as $v)
        <tr>
          <td>
            <img src="{{ $v->image_url }}" alt="{{ $v->name }}"
                 style="width:48px;height:36px;object-fit:cover;border-radius:6px;background:#f0f0f0">
          </td>
          <td style="font-weight:600">{{ $v->name }}</td>
          <td style="color:var(--txt2);font-size:.78rem;max-width:200px">
            {{ Str::limit($v->description, 60) }}
          </td>
          <td>
            @foreach(($v->services ?? []) as $s)
              <span class="badge badge-teal" style="margin:1px 2px">{{ $s }}</span>
            @endforeach
          </td>
          <td style="text-align:center;font-weight:600">{{ $v->passengers }}</td>
          <td style="text-align:center">
            <button onclick="toggleVehicle({{ $v->id }}, this)"
                    data-state="{{ $v->is_available ? '1' : '0' }}"
                    class="toggle-btn {{ $v->is_available ? 'on' : '' }}"
                    title="{{ $v->is_available ? 'Disponible' : 'No disponible' }}">
              <span class="toggle-dot"></span>
            </button>
          </td>
          <td style="text-align:center;color:var(--txt2)">{{ $v->sort_order }}</td>
          <td style="white-space:nowrap">
            <a href="{{ route('admin.cms.vehicles.edit', $v) }}" class="btn btn-ghost btn-sm">Editar</a>
            <form method="POST" action="{{ route('admin.cms.vehicles.destroy', $v) }}"
                  style="display:inline" onsubmit="return confirm('¿Eliminar este vehículo?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm">Eliminar</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" style="text-align:center;padding:48px;color:var(--txt2)">
            No hay vehículos registrados. <a href="{{ route('admin.cms.vehicles.create') }}">Crear el primero</a>
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
.alert-success{background:#D1FAE5;color:#065F46;border-radius:var(--radius-sm);padding:10px 16px;font-size:.85rem;margin-bottom:16px}
</style>
<script>
async function toggleVehicle(id, btn) {
  const r = await fetch(`/admin/cms/vehicles/${id}/toggle`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
  });
  const d = await r.json();
  btn.classList.toggle('on', d.is_available);
  btn.title = d.is_available ? 'Disponible' : 'No disponible';
}
</script>

@endsection

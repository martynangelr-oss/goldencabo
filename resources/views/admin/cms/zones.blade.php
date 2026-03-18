@extends('layouts.admin')
@section('page-title', 'Zonas & Hoteles')
@section('content')

<div class="page-header">
  <div>
    <h1>Zonas & Hoteles</h1>
    <p>Administra las zonas de traslado y los hoteles vinculados a cada una</p>
  </div>
  <button class="btn btn-primary" onclick="openNewZoneModal()">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nueva Zona
  </button>
</div>

@foreach($zones as $zone)
<div class="card" style="margin-bottom:20px">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap">

    {{-- Zone header --}}
    <div style="display:flex;align-items:center;gap:14px">
      <div style="width:48px;height:48px;border-radius:12px;background:var(--teal-lt);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;color:var(--teal);flex-shrink:0">
        {{ $zone->number }}
      </div>
      <div>
        <div style="font-size:.95rem;font-weight:700;color:var(--txt)">{{ $zone->name }}</div>
        @if($zone->area)
          <div style="font-size:.75rem;color:var(--txt2);margin-top:2px">{{ $zone->area }}</div>
        @endif
        <div style="display:flex;gap:12px;margin-top:6px">
          <span class="badge badge-teal">Redondo: ${{ number_format($zone->round_trip_price, 0) }} USD</span>
          <span class="badge badge-gray">Sencillo: ${{ number_format($zone->one_way_price, 0) }} USD</span>
          @if($zone->travel_time)
            <span class="badge badge-blue">{{ $zone->travel_time }}</span>
          @endif
          <span class="badge {{ $zone->is_active ? 'badge-green' : 'badge-red' }}">
            {{ $zone->is_active ? 'Activa' : 'Inactiva' }}
          </span>
        </div>
      </div>
    </div>

    {{-- Zone actions --}}
    <div style="display:flex;gap:8px;flex-shrink:0">
      <button class="btn btn-ghost btn-sm"
              data-id="{{ $zone->id }}"
              data-number="{{ $zone->number }}"
              data-name="{{ $zone->name }}"
              data-area="{{ $zone->area ?? '' }}"
              data-round="{{ $zone->round_trip_price }}"
              data-one="{{ $zone->one_way_price }}"
              data-time="{{ $zone->travel_time ?? '' }}"
              data-active="{{ $zone->is_active ? '1' : '0' }}"
              onclick="openEditZone(this)">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Editar Zona
      </button>
      <button class="btn btn-ghost btn-sm"
              data-id="{{ $zone->id }}"
              data-number="{{ $zone->number }}"
              data-name="{{ $zone->name }}"
              data-img-main="{{ $zone->image_url }}"
              data-img-sec="{{ $zone->image_secondary_url }}"
              onclick="openImgZone(this)">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        Imágenes
      </button>
      <form method="POST" action="{{ route('admin.cms.zones.destroy', $zone) }}"
            data-zone-name="{{ $zone->name }}"
            onsubmit="return confirm('¿Eliminar zona ' + this.dataset.zoneName + '? Se eliminarán también todos sus hoteles.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
          Eliminar
        </button>
      </form>
    </div>
  </div>

  <div style="height:1px;background:var(--border);margin:18px 0"></div>

  {{-- Hotels list --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
    <div style="font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--txt2)">
      Hoteles ({{ $zone->hotels->count() }})
    </div>
    <button class="btn btn-ghost btn-sm"
            data-zone-id="{{ $zone->id }}"
            data-zone-name="{{ $zone->name }}"
            onclick="openAddHotel(this)">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Agregar Hotel
    </button>
  </div>

  @if($zone->hotels->isEmpty())
    <div style="text-align:center;padding:24px;color:var(--txt2);font-size:.82rem">
      No hay hoteles en esta zona.
    </div>
  @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:8px">
      @foreach($zone->hotels->sortBy('sort_order') as $hotel)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--bg);border-radius:var(--radius-sm);gap:8px">
          <div style="display:flex;align-items:center;gap:8px;min-width:0">
            <div style="width:6px;height:6px;border-radius:50%;background:{{ $hotel->is_active ? 'var(--success)' : 'var(--txt2)' }};flex-shrink:0"></div>
            <span style="font-size:.82rem;color:var(--txt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $hotel->name }}</span>
          </div>
          <div style="display:flex;gap:4px;flex-shrink:0">
            <form method="POST" action="{{ route('admin.cms.hotels.toggle', $hotel) }}">
              @csrf
              <button type="submit" title="{{ $hotel->is_active ? 'Desactivar' : 'Activar' }}"
                      style="background:none;border:none;cursor:pointer;padding:4px;color:{{ $hotel->is_active ? 'var(--success)' : 'var(--txt2)' }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  @if($hotel->is_active)
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  @else
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>
                  @endif
                </svg>
              </button>
            </form>
            <form method="POST" action="{{ route('admin.cms.hotels.destroy', $hotel) }}"
                  data-hotel-name="{{ $hotel->name }}"
                  onsubmit="return confirm('¿Eliminar hotel ' + this.dataset.hotelName + '?')">
              @csrf @method('DELETE')
              <button type="submit" title="Eliminar"
                      style="background:none;border:none;cursor:pointer;padding:4px;color:var(--txt2)" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--txt2)'">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endforeach

@if($zones->isEmpty())
  <div class="card" style="text-align:center;padding:48px">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--txt2)" stroke-width="1.5" style="margin:0 auto 16px"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
    <p style="color:var(--txt2);font-size:.88rem">No hay zonas configuradas.</p>
    <button class="btn btn-primary" style="margin-top:16px" onclick="openNewZoneModal()">Crear primera zona</button>
  </div>
@endif

{{-- ── Modal: Imágenes de Zona ── --}}
<div id="zoneImgModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:28px 32px;width:100%;max-width:560px;box-shadow:var(--shadow-md);position:relative;margin:20px;max-height:90vh;overflow-y:auto">
    <button onclick="closeImgModal()" style="position:absolute;top:14px;right:16px;background:none;border:none;cursor:pointer;color:var(--txt2);font-size:18px">✕</button>
    <h2 id="zoneImgTitle" style="font-size:1rem;font-weight:700;margin-bottom:6px;color:var(--txt)">Imágenes de Zona</h2>
    <p style="font-size:.75rem;color:var(--txt2);margin-bottom:18px">Actualiza las fotos que aparecen en la tarjeta de esta zona.</p>

    <div class="alert" style="background:#EFF6FF;color:#1E40AF;border-color:#BFDBFE;font-size:.76rem;margin-bottom:18px">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
      <span>
        <strong>Imagen principal:</strong> 900 × 600 px (3:2) ·
        <strong>Imagen secundaria:</strong> 600 × 400 px (3:2) · JPG/PNG/WEBP · Máx. 4 MB c/u
      </span>
    </div>

    <form id="zoneImgForm" method="POST" action="" enctype="multipart/form-data">
      @csrf

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="fg" style="margin-bottom:0">
          <label>Imagen principal</label>
          <div style="margin-bottom:8px;border-radius:6px;overflow:hidden;aspect-ratio:3/2;background:var(--bg)">
            <img id="imgZoneMain" src="" alt="" style="width:100%;height:100%;object-fit:cover">
          </div>
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                 onchange="previewZoneImg(this,'imgZoneMain')">
          <p class="form-hint">900 × 600 px recomendado</p>
        </div>
        <div class="fg" style="margin-bottom:0">
          <label>Imagen secundaria</label>
          <div style="margin-bottom:8px;border-radius:6px;overflow:hidden;aspect-ratio:3/2;background:var(--bg)">
            <img id="imgZoneSec" src="" alt="" style="width:100%;height:100%;object-fit:cover">
          </div>
          <input type="file" name="image_secondary" accept="image/jpeg,image/png,image/webp"
                 onchange="previewZoneImg(this,'imgZoneSec')">
          <p class="form-hint">600 × 400 px recomendado</p>
        </div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px">
        <button type="button" onclick="closeImgModal()" class="btn btn-ghost">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Imágenes</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Modal: Nueva / Editar Zona ── --}}
<div id="zoneModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:28px 32px;width:100%;max-width:520px;box-shadow:var(--shadow-md);position:relative;margin:20px">
    <button onclick="closeZoneModal()" style="position:absolute;top:14px;right:16px;background:none;border:none;cursor:pointer;color:var(--txt2);font-size:18px">✕</button>
    <h2 id="zoneModalTitle" style="font-size:1rem;font-weight:700;margin-bottom:20px;color:var(--txt)">Nueva Zona</h2>
    <form id="zoneForm" method="POST" action="{{ route('admin.cms.zones.store') }}">
      @csrf
      <input type="hidden" name="_method" id="zoneMethod" value="">
      <div style="display:grid;grid-template-columns:80px 1fr;gap:14px">
        <div class="fg" style="margin-bottom:0">
          <label># Zona</label>
          <input type="number" name="number" id="z-number" min="1" max="20" required>
        </div>
        <div class="fg" style="margin-bottom:0">
          <label>Nombre <span style="color:var(--danger)">*</span></label>
          <input type="text" name="name" id="z-name" placeholder="Ej: San José del Cabo" required>
        </div>
      </div>
      <div class="fg" style="margin-top:14px">
        <label>Área / Descripción</label>
        <input type="text" name="area" id="z-area" placeholder="Ej: Corredor turístico principal">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
        <div class="fg" style="margin-bottom:0">
          <label>Precio Redondo (USD) <span style="color:var(--danger)">*</span></label>
          <input type="number" name="round_trip_price" id="z-round" step="0.01" min="0" placeholder="140" required>
        </div>
        <div class="fg" style="margin-bottom:0">
          <label>Precio Sencillo (USD) <span style="color:var(--danger)">*</span></label>
          <input type="number" name="one_way_price" id="z-one" step="0.01" min="0" placeholder="75" required>
        </div>
        <div class="fg" style="margin-bottom:0">
          <label>Tiempo aprox.</label>
          <input type="text" name="travel_time" id="z-time" placeholder="45 min">
        </div>
      </div>
      <div class="fg" style="margin-top:14px;margin-bottom:0;display:flex;align-items:center;gap:12px">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.85rem;color:var(--txt);text-transform:none;letter-spacing:0;font-weight:400">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" id="z-active" value="1" checked style="width:auto;accent-color:var(--teal)">
          Zona activa (visible en el wizard de reservas)
        </label>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:24px">
        <button type="button" onclick="closeZoneModal()" class="btn btn-ghost">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="zoneSubmitBtn">Crear Zona</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Modal: Agregar Hotel ── --}}
<div id="hotelModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:28px 32px;width:100%;max-width:420px;box-shadow:var(--shadow-md);position:relative;margin:20px">
    <button onclick="closeHotelModal()" style="position:absolute;top:14px;right:16px;background:none;border:none;cursor:pointer;color:var(--txt2);font-size:18px">✕</button>
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:6px;color:var(--txt)">Agregar Hotel</h2>
    <p id="hotelModalZoneName" style="font-size:.78rem;color:var(--txt2);margin-bottom:20px"></p>
    <form id="hotelForm" method="POST" action="">
      @csrf
      <div class="fg">
        <label>Nombre del Hotel <span style="color:var(--danger)">*</span></label>
        <input type="text" name="name" id="h-name" placeholder="Ej: Hyatt Ziva Los Cabos" required>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
        <button type="button" onclick="closeHotelModal()" class="btn btn-ghost">Cancelar</button>
        <button type="submit" class="btn btn-primary">Agregar Hotel</button>
      </div>
    </form>
  </div>
</div>

<script>
const zoneStoreUrl = '{{ route('admin.cms.zones.store') }}';
const zoneBaseUrl  = '{{ url('admin/cms/zones') }}';

function openNewZoneModal() {
  document.getElementById('zoneModalTitle').textContent = 'Nueva Zona';
  document.getElementById('zoneSubmitBtn').textContent  = 'Crear Zona';
  document.getElementById('zoneForm').action = zoneStoreUrl;
  document.getElementById('zoneMethod').value = '';
  ['z-number','z-name','z-area','z-round','z-one','z-time'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('z-active').checked = true;
  showModal('zoneModal');
}

function openEditZone(btn) {
  const d = btn.dataset;
  document.getElementById('zoneModalTitle').textContent = 'Editar Zona';
  document.getElementById('zoneSubmitBtn').textContent  = 'Guardar Cambios';
  document.getElementById('zoneForm').action = zoneBaseUrl + '/' + d.id;
  document.getElementById('zoneMethod').value = 'PUT';
  document.getElementById('z-number').value = d.number;
  document.getElementById('z-name').value   = d.name;
  document.getElementById('z-area').value   = d.area;
  document.getElementById('z-round').value  = d.round;
  document.getElementById('z-one').value    = d.one;
  document.getElementById('z-time').value   = d.time;
  document.getElementById('z-active').checked = d.active === '1';
  showModal('zoneModal');
}

function closeZoneModal() { hideModal('zoneModal'); }

function openAddHotel(btn) {
  const d = btn.dataset;
  document.getElementById('hotelModalZoneName').textContent = 'Zona: ' + d.zoneName;
  document.getElementById('hotelForm').action = zoneBaseUrl + '/' + d.zoneId + '/hotels';
  document.getElementById('h-name').value = '';
  showModal('hotelModal');
}

function closeHotelModal() { hideModal('hotelModal'); }

function showModal(id) {
  var m = document.getElementById(id);
  m.style.display = 'flex';
  setTimeout(() => m.querySelector('input[type=text],input[type=number]')?.focus(), 80);
}
function hideModal(id) { document.getElementById(id).style.display = 'none'; }

// Image zone modal
function openImgZone(btn) {
  const d = btn.dataset;
  document.getElementById('zoneImgTitle').textContent = 'Imágenes — Zona ' + d.number + ': ' + d.name;
  document.getElementById('zoneImgForm').action = zoneBaseUrl + '/' + d.id + '/images';
  document.getElementById('imgZoneMain').src = d.imgMain;
  document.getElementById('imgZoneSec').src  = d.imgSec;
  showModal('zoneImgModal');
}
function closeImgModal() { hideModal('zoneImgModal'); }

function previewZoneImg(input, targetId) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => document.getElementById(targetId).src = e.target.result;
    reader.readAsDataURL(input.files[0]);
  }
}

// Close on backdrop click
['zoneModal','hotelModal','zoneImgModal'].forEach(function(id) {
  document.getElementById(id).addEventListener('click', function(e) {
    if (e.target === this) hideModal(id);
  });
});
</script>

@endsection

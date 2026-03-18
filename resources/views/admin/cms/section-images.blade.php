@extends('layouts.admin')
@section('page-title', 'Imágenes de Secciones')
@section('content')

<div class="page-header">
  <div>
    <h1>Imágenes de Secciones</h1>
    <p>Reemplaza las imágenes de Acerca de Nosotros y Servicio Aeropuerto</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:20px">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
  </div>
@endif

@if($errors->any())
  <div class="alert alert-error" style="margin-bottom:20px">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <ul style="margin:0;padding-left:16px">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

{{-- ══ ACERCA DE NOSOTROS ══ --}}
@php $aboutSlots = array_filter($slots, fn($s) => $s['section'] === 'about'); @endphp
<div class="card" style="margin-bottom:24px">
  <div class="card-header">
    <div class="card-title">
      <span class="ct-icon">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      </span>
      Acerca de Nosotros
    </div>
    <a href="{{ route('home') }}#acerca" target="_blank" class="btn btn-ghost btn-sm">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      Ver en sitio
    </a>
  </div>
  <div class="alert" style="background:#EFF6FF;color:#1E40AF;border-color:#BFDBFE;font-size:.77rem;margin-bottom:20px">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span>Imágenes de la sección <strong>Acerca de Nosotros</strong> del sitio web.</span>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px">
    @foreach($aboutSlots as $key => $slot)
      @include('admin.cms._slot-card', ['slot' => $slot, 'key' => $key])
    @endforeach
  </div>
</div>

{{-- ══ SERVICIO AEROPUERTO ══ --}}
@php $airportSlots = array_filter($slots, fn($s) => $s['section'] === 'airport'); @endphp
<div class="card">
  <div class="card-header">
    <div class="card-title">
      <span class="ct-icon">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21.53 16.92z"/></svg>
      </span>
      Servicio Aeropuerto
    </div>
    <a href="{{ route('home') }}#exp" target="_blank" class="btn btn-ghost btn-sm">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      Ver en sitio
    </a>
  </div>
  <div class="alert" style="background:#EFF6FF;color:#1E40AF;border-color:#BFDBFE;font-size:.77rem;margin-bottom:20px">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span>Imagen de la sección <strong>Servicio Aeropuerto</strong> del sitio web.</span>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px">
    @foreach($airportSlots as $key => $slot)
      @include('admin.cms._slot-card', ['slot' => $slot, 'key' => $key])
    @endforeach
  </div>
</div>

{{-- ══ Modales de reemplazar ══ --}}
@foreach($slots as $key => $slot)
<div id="modal-{{ $key }}" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:28px 32px;width:100%;max-width:480px;box-shadow:var(--shadow-md);position:relative;margin:20px;max-height:90vh;overflow-y:auto">
    <button onclick="closeModal('{{ $key }}')" style="position:absolute;top:14px;right:16px;background:none;border:none;cursor:pointer;color:var(--txt2);font-size:18px">✕</button>
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:4px;color:var(--txt)">Reemplazar Imagen</h2>
    <p style="font-size:.78rem;color:var(--txt2);margin-bottom:18px">
      {{ $slot['label'] }} ·
      <strong>{{ $slot['section'] === 'about' ? 'Acerca de Nosotros' : 'Servicio Aeropuerto' }}</strong>
    </p>

    <div style="margin-bottom:16px;border-radius:var(--radius-sm);overflow:hidden;aspect-ratio:4/3;background:var(--bg)">
      <img id="preview-{{ $key }}" src="{{ $slot['url'] }}" alt=""
           style="width:100%;height:100%;object-fit:cover">
    </div>

    <form method="POST" action="{{ route('admin.cms.section-images.update', $key) }}" enctype="multipart/form-data"
          onsubmit="this.querySelector('[type=submit]').disabled=true;this.querySelector('[type=submit]').textContent='Guardando...'">
      @csrf

      @if($errors->has('image'))
        <div class="alert alert-error" style="font-size:.8rem;margin-bottom:12px">
          {{ $errors->first('image') }}
        </div>
      @endif

      <div id="drop-{{ $key }}"
           style="border:2px dashed var(--border);border-radius:var(--radius-sm);padding:24px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;margin-bottom:16px"
           onclick="document.getElementById('file-{{ $key }}').click()"
           ondragover="this.style.borderColor='var(--teal)';this.style.background='var(--teal-lt)';event.preventDefault()"
           ondragleave="this.style.borderColor='var(--border)';this.style.background='transparent'"
           ondrop="handleFileDrop(event,'{{ $key }}')">
        <input type="file" id="file-{{ $key }}" name="image" accept="image/jpeg,image/png,image/webp"
               style="display:none" onchange="previewFile(this,'{{ $key }}')">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--txt2)" stroke-width="1.5" style="margin:0 auto 8px;display:block"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <p style="font-size:.82rem;font-weight:600;color:var(--txt);margin-bottom:3px">Arrastra o haz clic para seleccionar</p>
        <p style="font-size:.72rem;color:var(--txt2)">{{ $slot['hint'] }} · JPG, PNG, WEBP · Máx. 25 MB</p>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" onclick="closeModal('{{ $key }}')" class="btn btn-ghost">Cancelar</button>
        <button type="submit" class="btn btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Guardar Imagen
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Form restaurar fuera del modal --}}
@if($slot['is_custom'])
<form id="restore-{{ $key }}" method="POST" action="{{ route('admin.cms.section-images.restore', $key) }}" style="display:none">
  @csrf
</form>
@endif
@endforeach

<script>
function openModal(key) {
  document.getElementById('modal-' + key).style.display = 'flex';
}
function closeModal(key) {
  document.getElementById('modal-' + key).style.display = 'none';
}
function restoreSlot(key) {
  if (confirm('¿Restaurar la imagen predeterminada?')) {
    document.getElementById('restore-' + key).submit();
  }
}
function previewFile(input, key) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => { document.getElementById('preview-' + key).src = e.target.result; };
  reader.readAsDataURL(input.files[0]);
}
function handleFileDrop(e, key) {
  e.preventDefault();
  e.currentTarget.style.borderColor = 'var(--border)';
  e.currentTarget.style.background  = 'transparent';
  const file = e.dataTransfer.files[0];
  if (file && file.type.startsWith('image/')) {
    const input = document.getElementById('file-' + key);
    const dt = new DataTransfer(); dt.items.add(file);
    input.files = dt.files;
    previewFile(input, key);
  }
}
document.querySelectorAll('[id^="modal-"]').forEach(el => {
  el.addEventListener('click', function(e) {
    if (e.target === this) closeModal(this.id.replace('modal-', ''));
  });
});
</script>

@endsection

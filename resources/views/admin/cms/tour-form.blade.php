@extends('layouts.admin')
@section('page-title', isset($tour->id) ? 'Editar Tour' : 'Nuevo Tour')
@section('content')

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('admin.cms.tours.index') }}" class="btn btn-ghost btn-sm" style="padding:6px 10px">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
      <h1>{{ isset($tour->id) ? 'Editar Tour' : 'Nuevo Tour' }}</h1>
      <p>{{ isset($tour->id) ? $tour->name : 'Completa los datos del tour' }}</p>
    </div>
  </div>
</div>

@if($errors->any())
  <div style="background:#FEE2E2;color:#991B1B;border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:16px;font-size:.85rem">
    @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
  </div>
@endif

<form method="POST"
      action="{{ isset($tour->id) ? route('admin.cms.tours.update', $tour) : route('admin.cms.tours.store') }}"
      enctype="multipart/form-data">
  @csrf
  @if(isset($tour->id)) @method('PUT') @endif

  <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    <div style="display:flex;flex-direction:column;gap:16px">

      {{-- Info principal --}}
      <div class="card">
        <div class="form-section-title" style="margin-bottom:20px;font-weight:600;font-size:.85rem;color:var(--teal);text-transform:uppercase;letter-spacing:.5px">
          Información del Tour
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div class="fg">
            <label>Nombre <span style="color:var(--danger)">*</span></label>
            <input type="text" name="name" value="{{ old('name', $tour->name) }}"
                   placeholder="Ej: Recorrido a La Paz" required>
          </div>
          <div class="fg">
            <label>Duración</label>
            <input type="text" name="duration" value="{{ old('duration', $tour->duration) }}"
                   placeholder="Ej: 10 horas">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div class="fg">
            <label>Precio (USD) <span style="color:var(--danger)">*</span></label>
            <input type="number" name="price_usd" value="{{ old('price_usd', $tour->price_usd ?? '') }}"
                   step="0.01" min="0" placeholder="420" required>
          </div>
          <div class="fg">
            <label>Orden de aparición</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $tour->sort_order ?? 0) }}" min="0">
          </div>
        </div>

        <div class="fg">
          <label>Descripción de Ruta</label>
          <textarea name="route_description" rows="5"
                    placeholder="Describe el recorrido, puntos de interés, lo que incluye...">{{ old('route_description', $tour->route_description) }}</textarea>
        </div>

        <div class="fg" style="margin-bottom:0;display:flex;align-items:center;gap:12px">
          <label class="switch-label">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $tour->is_active ?? true) ? 'checked' : '' }}>
            <span class="switch-track"><span class="switch-thumb"></span></span>
            Tour activo (visible en el sitio)
          </label>
        </div>
      </div>

      {{-- Destinos --}}
      <div class="card">
        <div class="form-section-title" style="margin-bottom:16px;font-weight:600;font-size:.85rem;color:var(--teal);text-transform:uppercase;letter-spacing:.5px">
          Lista de Destinos / Puntos del Recorrido
        </div>

        <div id="destList" style="display:flex;flex-direction:column;gap:6px;margin-bottom:12px">
          @foreach(($tour->destinations ?? []) as $dest)
            <div class="dest-item">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/></svg>
              <span>{{ $dest }}</span>
              <button type="button" onclick="removeDest(this)" style="margin-left:auto">✕</button>
            </div>
          @endforeach
        </div>

        <div style="display:flex;gap:8px">
          <input type="text" id="destInput" placeholder="Agregar destino (Enter para confirmar)"
                 style="flex:1;padding:9px 12px;border-radius:var(--radius-sm);border:1.5px solid var(--border);font-size:.85rem;outline:none"
                 onkeydown="if(event.key==='Enter'){event.preventDefault();addDest()}"
                 onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='var(--border)'">
          <button type="button" onclick="addDest()" class="btn btn-primary btn-sm">+ Agregar</button>
        </div>
        <textarea name="destinations" id="destHidden" style="display:none">{{ old('destinations', implode("\n", $tour->destinations ?? [])) }}</textarea>
      </div>

    </div>

    {{-- Sidebar: imagen + acciones --}}
    <div style="display:flex;flex-direction:column;gap:16px">
      <div class="card">
        <div class="form-section-title" style="margin-bottom:16px;font-weight:600;font-size:.85rem;color:var(--teal);text-transform:uppercase;letter-spacing:.5px">
          Imagen del Tour
        </div>
        <div class="img-drop" id="imgDrop">
          <input type="file" name="image" id="imgFile" accept="image/jpeg,image/png,image/webp"
                 onchange="previewImg(this)">
          <div id="imgDropInner" style="text-align:center">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--txt2)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <p style="font-size:.82rem;color:var(--txt2);margin-top:10px">
              Arrastra o <span style="color:var(--teal);font-weight:600;cursor:pointer"
                onclick="document.getElementById('imgFile').click()">selecciona</span>
            </p>
            <p style="font-size:.7rem;color:var(--txt2);margin-top:4px">JPG, PNG, WebP — máx. 3 MB</p>
          </div>
          <div id="imgPreview" style="display:none;position:relative">
            <img id="imgPreviewImg" src="" alt=""
                 style="width:100%;height:160px;object-fit:cover;border-radius:8px">
            <button type="button" onclick="clearImg()"
                    style="position:absolute;top:6px;right:6px;width:26px;height:26px;border-radius:50%;border:none;background:rgba(0,0,0,.5);color:#fff;cursor:pointer;font-size:14px">✕</button>
          </div>
        </div>
        @if(isset($tour->id) && $tour->image_path)
          <div style="margin-top:12px;font-size:.75rem;color:var(--txt2)">
            Imagen actual:
            <img src="{{ $tour->image_url }}" alt=""
                 style="width:100%;height:80px;object-fit:cover;border-radius:6px;margin-top:6px">
          </div>
        @endif
        <div class="fg" style="margin-top:14px;margin-bottom:0">
          <label>O pegar URL de imagen</label>
          <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://...">
        </div>
      </div>

      <div class="card" style="display:flex;flex-direction:column;gap:10px">
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          {{ isset($tour->id) ? 'Guardar Cambios' : 'Crear Tour' }}
        </button>
        <a href="{{ route('admin.cms.tours.index') }}" class="btn btn-ghost"
           style="width:100%;justify-content:center">Cancelar</a>
      </div>
    </div>

  </div>
</form>

<style>
.fg{margin-bottom:16px}
.fg label{display:block;font-size:.72rem;letter-spacing:.5px;text-transform:uppercase;color:var(--txt2);font-weight:600;margin-bottom:6px}
.fg input,.fg textarea,.fg select{width:100%;padding:10px 13px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:#fff;font-family:'Inter',sans-serif;font-size:.88rem;color:var(--txt);outline:none;transition:border-color .2s;resize:vertical}
.fg input:focus,.fg textarea:focus{border-color:var(--teal);box-shadow:0 0 0 3px rgba(0,153,136,.1)}
.img-drop{border:2px dashed var(--border);border-radius:var(--radius);padding:24px;cursor:pointer;transition:border-color .2s;position:relative;background:#fafafa}
.img-drop.drag{border-color:var(--teal);background:var(--teal-lt)}
.img-drop input[type=file]{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;z-index:2}
.dest-item{display:flex;align-items:center;gap:8px;padding:8px 12px;background:var(--bg);border-radius:var(--radius-sm);font-size:.85rem;color:var(--txt)}
.dest-item svg{color:var(--teal);flex-shrink:0}
.dest-item button{background:none;border:none;cursor:pointer;color:var(--txt2);font-size:12px;padding:0;line-height:1}
.dest-item button:hover{color:var(--danger)}
.switch-label{display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.85rem;color:var(--txt)}
.switch-label input[type=checkbox]{display:none}
.switch-track{width:40px;height:22px;border-radius:100px;background:#CBD5E1;position:relative;transition:background .2s;flex-shrink:0}
.switch-label input:checked + .switch-track{background:var(--teal)}
.switch-thumb{width:16px;height:16px;border-radius:50%;background:#fff;position:absolute;top:3px;left:3px;transition:left .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.switch-label input:checked + .switch-track .switch-thumb{left:21px}
</style>

<script>
// Destinations
function updateDestHidden() {
  const items = document.querySelectorAll('#destList .dest-item span');
  document.getElementById('destHidden').value = Array.from(items).map(i => i.textContent).join('\n');
}
function addDest() {
  const input = document.getElementById('destInput');
  const val = input.value.trim();
  if (!val) return;
  const div = document.createElement('div');
  div.className = 'dest-item';
  div.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/></svg><span>${val}</span><button type="button" onclick="removeDest(this)" style="margin-left:auto">✕</button>`;
  document.getElementById('destList').appendChild(div);
  input.value = '';
  updateDestHidden();
}
function removeDest(btn) {
  btn.closest('.dest-item').remove();
  updateDestHidden();
}
updateDestHidden();

// Image
const drop = document.getElementById('imgDrop');
drop.addEventListener('dragover', e => { e.preventDefault(); drop.classList.add('drag'); });
drop.addEventListener('dragleave', () => drop.classList.remove('drag'));
drop.addEventListener('drop', e => {
  e.preventDefault(); drop.classList.remove('drag');
  const file = e.dataTransfer.files[0];
  if (file && file.type.startsWith('image/')) {
    const dt = new DataTransfer(); dt.items.add(file);
    document.getElementById('imgFile').files = dt.files;
    previewImg(document.getElementById('imgFile'));
  }
});
function previewImg(input) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('imgPreviewImg').src = e.target.result;
    document.getElementById('imgDropInner').style.display = 'none';
    document.getElementById('imgPreview').style.display = 'block';
    document.querySelector('#imgDrop input[type=file]').style.display = 'none';
  };
  reader.readAsDataURL(input.files[0]);
}
function clearImg() {
  document.getElementById('imgFile').value = '';
  document.getElementById('imgPreview').style.display = 'none';
  document.getElementById('imgDropInner').style.display = 'block';
  document.querySelector('#imgDrop input[type=file]').style.display = '';
}
</script>

@endsection

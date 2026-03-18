@extends('layouts.admin')
@section('page-title', isset($vehicle->id) ? 'Editar Vehículo' : 'Nuevo Vehículo')
@section('content')

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('admin.cms.vehicles.index') }}" class="btn btn-ghost btn-sm" style="padding:6px 10px">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
      <h1>{{ isset($vehicle->id) ? 'Editar Vehículo' : 'Nuevo Vehículo' }}</h1>
      <p>{{ isset($vehicle->id) ? $vehicle->name : 'Completa los datos del vehículo' }}</p>
    </div>
  </div>
</div>

@if($errors->any())
  <div style="background:#FEE2E2;color:#991B1B;border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:16px;font-size:.85rem">
    @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
  </div>
@endif

<form method="POST"
      action="{{ isset($vehicle->id) ? route('admin.cms.vehicles.update', $vehicle) : route('admin.cms.vehicles.store') }}"
      enctype="multipart/form-data">
  @csrf
  @if(isset($vehicle->id)) @method('PUT') @endif

  <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    {{-- Main fields --}}
    <div class="card">
      <div class="form-section-title" style="margin-bottom:20px;font-weight:600;font-size:.85rem;color:var(--teal);text-transform:uppercase;letter-spacing:.5px">
        Información del Vehículo
      </div>

      <div class="fg">
        <label>Nombre <span style="color:var(--danger)">*</span></label>
        <input type="text" name="name" value="{{ old('name', $vehicle->name) }}"
               placeholder="Ej: Chevrolet Suburban LTZ" required>
      </div>

      <div class="fg">
        <label>Descripción</label>
        <textarea name="description" rows="4"
                  placeholder="Descripción detallada del vehículo...">{{ old('description', $vehicle->description) }}</textarea>
      </div>

      <div class="fg">
        <label>Servicios / Características</label>
        <div class="tag-wrap" id="tagWrap">
          @foreach(($vehicle->services ?? []) as $s)
            <span class="tag-chip">{{ $s }}<button type="button" onclick="removeTag(this)">✕</button></span>
          @endforeach
          <input type="text" id="tagInput" placeholder="Agregar característica y Enter..."
                 onkeydown="addTag(event)">
        </div>
        <input type="hidden" name="services" id="servicesHidden"
               value="{{ old('services', implode(',', $vehicle->services ?? [])) }}">
        <div style="font-size:.72rem;color:var(--txt2);margin-top:5px">Presiona Enter para agregar. Ej: A/C Premium, WiFi, USB</div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="fg">
          <label>Número de Pasajeros <span style="color:var(--danger)">*</span></label>
          <input type="number" name="passengers" value="{{ old('passengers', $vehicle->passengers ?? 1) }}"
                 min="1" max="50" required>
        </div>
        <div class="fg">
          <label>Orden de aparición</label>
          <input type="number" name="sort_order" value="{{ old('sort_order', $vehicle->sort_order ?? 0) }}"
                 min="0">
        </div>
      </div>

      <div class="fg" style="display:flex;align-items:center;gap:12px;margin-bottom:0">
        <label class="switch-label">
          <input type="hidden" name="is_available" value="0">
          <input type="checkbox" name="is_available" value="1" id="isAvailable"
                 {{ old('is_available', $vehicle->is_available ?? true) ? 'checked' : '' }}>
          <span class="switch-track"><span class="switch-thumb"></span></span>
          Disponible para reservas
        </label>
      </div>
    </div>

    {{-- Image upload --}}
    <div style="display:flex;flex-direction:column;gap:16px">
      <div class="card">
        <div class="form-section-title" style="margin-bottom:16px;font-weight:600;font-size:.85rem;color:var(--teal);text-transform:uppercase;letter-spacing:.5px">
          Imagen del Vehículo
        </div>

        <div class="img-drop" id="imgDrop">
          <input type="file" name="image" id="imgFile" accept="image/jpeg,image/png,image/webp"
                 onchange="previewImg(this)">
          <div class="img-drop-inner" id="imgDropInner">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--txt2)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <p style="font-size:.82rem;color:var(--txt2);margin-top:10px;text-align:center">
              Arrastra una imagen o <span style="color:var(--teal);font-weight:600;cursor:pointer"
                onclick="document.getElementById('imgFile').click()">selecciona</span>
            </p>
            <p style="font-size:.7rem;color:var(--txt2);margin-top:4px">JPG, PNG, WebP — máx. 3 MB</p>
          </div>
          <div id="imgPreview" style="display:none;position:relative">
            <img id="imgPreviewImg" src="" alt=""
                 style="width:100%;height:160px;object-fit:cover;border-radius:8px">
            <button type="button" onclick="clearImg()"
                    style="position:absolute;top:6px;right:6px;width:26px;height:26px;border-radius:50%;border:none;background:rgba(0,0,0,.5);color:#fff;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center">✕</button>
          </div>
        </div>

        {{-- Current image --}}
        @if(isset($vehicle->id) && $vehicle->image_path)
          <div style="margin-top:12px;font-size:.75rem;color:var(--txt2)">
            Imagen actual:
            <img src="{{ $vehicle->image_url }}" alt=""
                 style="width:100%;height:80px;object-fit:cover;border-radius:6px;margin-top:6px">
          </div>
        @endif

        {{-- Or URL --}}
        <div class="fg" style="margin-top:14px;margin-bottom:0">
          <label>O pegar URL de imagen</label>
          <input type="url" name="image_url" value="{{ old('image_url') }}"
                 placeholder="https://...">
        </div>
      </div>

      <div class="card" style="display:flex;flex-direction:column;gap:10px">
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          {{ isset($vehicle->id) ? 'Guardar Cambios' : 'Crear Vehículo' }}
        </button>
        <a href="{{ route('admin.cms.vehicles.index') }}" class="btn btn-ghost"
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

/* Tag input */
.tag-wrap{display:flex;flex-wrap:wrap;gap:6px;align-items:center;min-height:44px;padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:#fff;cursor:text}
.tag-wrap:focus-within{border-color:var(--teal);box-shadow:0 0 0 3px rgba(0,153,136,.1)}
.tag-chip{display:inline-flex;align-items:center;gap:4px;background:var(--teal-lt);color:var(--teal-dk);padding:3px 8px;border-radius:100px;font-size:.75rem;font-weight:500}
.tag-chip button{background:none;border:none;cursor:pointer;color:inherit;font-size:11px;padding:0;line-height:1;opacity:.7}
.tag-chip button:hover{opacity:1}
#tagInput{border:none;outline:none;font-size:.85rem;padding:2px 0;min-width:150px;flex:1;background:transparent}

/* Image drop */
.img-drop{border:2px dashed var(--border);border-radius:var(--radius);padding:24px;text-align:center;cursor:pointer;transition:border-color .2s;position:relative;background:#fafafa}
.img-drop.drag{border-color:var(--teal);background:var(--teal-lt)}
.img-drop input[type=file]{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;z-index:2}
#imgPreview input[type=file]{display:none}

/* Switch */
.switch-label{display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.85rem;color:var(--txt)}
.switch-label input[type=checkbox]{display:none}
.switch-track{width:40px;height:22px;border-radius:100px;background:#CBD5E1;position:relative;transition:background .2s;flex-shrink:0}
.switch-label input:checked + .switch-track{background:var(--teal)}
.switch-thumb{width:16px;height:16px;border-radius:50%;background:#fff;position:absolute;top:3px;left:3px;transition:left .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.switch-label input:checked + .switch-track .switch-thumb{left:21px}
</style>

<script>
// ── Tag input ──────────────────────────────────────
function updateHidden() {
  const chips = document.querySelectorAll('#tagWrap .tag-chip');
  document.getElementById('servicesHidden').value =
    Array.from(chips).map(c => c.firstChild.textContent.trim()).join(',');
}
function addTag(e) {
  if (e.key !== 'Enter' && e.key !== ',') return;
  e.preventDefault();
  const val = e.target.value.trim().replace(/,$/, '');
  if (!val) return;
  const chip = document.createElement('span');
  chip.className = 'tag-chip';
  chip.innerHTML = `${val}<button type="button" onclick="removeTag(this)">✕</button>`;
  document.getElementById('tagWrap').insertBefore(chip, e.target);
  e.target.value = '';
  updateHidden();
}
function removeTag(btn) {
  btn.closest('.tag-chip').remove();
  updateHidden();
}
updateHidden();

// ── Image drag & drop ──────────────────────────────
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

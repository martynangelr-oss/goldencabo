@extends('layouts.admin')
@section('page-title', 'Galería de Viajes')
@section('content')

<div class="page-header">
  <div>
    <h1>Galería de Viajes</h1>
    <p>Imágenes de la sección galería del sitio web</p>
  </div>
  <button class="btn btn-primary" onclick="openUploadModal()">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Subir Imágenes
  </button>
</div>

{{-- Dimension hint --}}
<div class="alert" style="background:#EFF6FF;color:#1E40AF;border-color:#BFDBFE;margin-bottom:20px">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  <span>
    Dimensiones recomendadas:
    <strong>Imagen grande (1a): 1000 × 700 px</strong> ·
    <strong>Imágenes pequeñas: 700 × 500 px</strong>
    · Relación 3:2 · Formatos: JPG, PNG, WEBP · Peso máximo: 25 MB por imagen.
  </span>
</div>

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
  </div>
@endif

@if($errors->any())
  <div class="alert alert-error" style="margin-bottom:16px">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <ul style="margin:0;padding-left:16px">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

@if($images->isEmpty())
  <div class="card" style="text-align:center;padding:56px">
    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--txt2)" stroke-width="1.5" style="margin:0 auto 16px"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
    <p style="color:var(--txt2);font-size:.88rem">La galería está vacía. Sube las primeras imágenes.</p>
    <button class="btn btn-primary" style="margin-top:16px" onclick="openUploadModal()">Subir Imágenes</button>
  </div>
@else
  {{-- Grid --}}
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:24px">
    @foreach($images as $img)
    <div class="card" style="padding:0;overflow:hidden;border-radius:var(--radius)">
      {{-- Thumbnail --}}
      <div style="position:relative;aspect-ratio:3/2;background:var(--bg);overflow:hidden">
        <img src="{{ $img->image_url }}" alt="{{ $img->caption }}"
             style="width:100%;height:100%;object-fit:cover;transition:transform .2s"
             onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
        <div style="position:absolute;top:8px;right:8px;display:flex;gap:4px">
          <form method="POST" action="{{ route('admin.cms.gallery.toggle', $img) }}">
            @csrf
            <button type="submit"
                    title="{{ $img->is_active ? 'Ocultar' : 'Mostrar' }}"
                    style="width:24px;height:24px;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;background:{{ $img->is_active ? '#10B981' : '#9CA3AF' }};color:#fff">
              @if($img->is_active)
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              @else
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              @endif
            </button>
          </form>
        </div>
        @if(!$img->is_active)
          <div style="position:absolute;inset:0;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center">
            <span style="color:#fff;font-size:.68rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase">Oculta</span>
          </div>
        @endif
      </div>

      {{-- Footer --}}
      <div style="padding:10px 12px">
        <div style="font-size:.78rem;color:var(--txt);font-weight:500;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          {{ $img->caption ?: 'Sin pie de foto' }}
        </div>
        <div style="display:flex;gap:6px">
          <button class="btn btn-ghost btn-sm" style="flex:1;justify-content:center"
                  onclick="openEditModal({{ $img->id }}, '{{ addslashes($img->caption ?? '') }}', {{ $img->sort_order }}, '{{ $img->image_url }}')">
            Editar
          </button>
          <form method="POST" action="{{ route('admin.cms.gallery.destroy', $img) }}"
                onsubmit="return confirm('¿Eliminar imagen?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Eliminar">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
            </button>
          </form>
        </div>
      </div>
    </div>
    @endforeach
  </div>
@endif

{{-- ── Modal: Subir imágenes ── --}}
<div id="uploadModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:28px 32px;width:100%;max-width:560px;box-shadow:var(--shadow-md);position:relative;margin:20px;max-height:90vh;overflow-y:auto">
    <button onclick="closeUploadModal()" style="position:absolute;top:14px;right:16px;background:none;border:none;cursor:pointer;color:var(--txt2);font-size:18px">✕</button>

    <h2 style="font-size:1rem;font-weight:700;margin-bottom:6px;color:var(--txt)">Subir Imágenes a la Galería</h2>
    <p style="font-size:.78rem;color:var(--txt2);margin-bottom:20px">Puedes seleccionar múltiples imágenes a la vez.</p>

    <div class="alert" style="background:#EFF6FF;color:#1E40AF;border-color:#BFDBFE;font-size:.77rem;margin-bottom:18px">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span>
        <strong>Imagen grande (1ª posición):</strong> 1000 × 700 px (3:2)<br>
        <strong>Imágenes regulares:</strong> 700 × 500 px (3:2)<br>
        Formatos: JPG, PNG, WEBP · Máx. 25 MB c/u
      </span>
    </div>

    <form method="POST" action="{{ route('admin.cms.gallery.store') }}" enctype="multipart/form-data"
          onsubmit="document.getElementById('uploadBtn').disabled=true;document.getElementById('uploadBtn').textContent='Subiendo...'">
      @csrf

      @if($errors->has('images'))
        <div class="alert alert-error" style="margin-bottom:16px;font-size:.8rem">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ $errors->first('images') }}
        </div>
      @endif

      {{-- Drop zone --}}
      <div id="dropZone"
           style="border:2px dashed var(--border);border-radius:var(--radius);padding:32px;text-align:center;
                  cursor:pointer;transition:border-color .2s,background .2s;margin-bottom:16px"
           onclick="document.getElementById('galleryFiles').click()"
           ondragover="this.style.borderColor='var(--teal)';this.style.background='var(--teal-lt)';event.preventDefault()"
           ondragleave="this.style.borderColor='var(--border)';this.style.background='transparent'"
           ondrop="handleDrop(event)">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--txt2)" stroke-width="1.5" style="margin:0 auto 10px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <p style="font-size:.85rem;font-weight:600;color:var(--txt);margin-bottom:4px">Arrastra imágenes aquí</p>
        <p style="font-size:.75rem;color:var(--txt2)">o haz clic para seleccionar</p>
        <input type="file" id="galleryFiles" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
               style="display:none" onchange="previewGallery(this)">
      </div>

      <div id="galleryPreviews" style="display:none;display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px;margin-bottom:16px"></div>

      <div class="fg">
        <label>Pie de foto común (opcional)</label>
        <input type="text" name="caption" placeholder="Ej: Los Cabos" maxlength="191">
        <p class="form-hint">Se aplicará a todas las imágenes subidas en este lote.</p>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
        <button type="button" onclick="closeUploadModal()" class="btn btn-ghost">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="uploadBtn">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Subir Imágenes
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ── Modal: Editar imagen ── --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:28px 32px;width:100%;max-width:440px;box-shadow:var(--shadow-md);position:relative;margin:20px">
    <button onclick="closeEditModal()" style="position:absolute;top:14px;right:16px;background:none;border:none;cursor:pointer;color:var(--txt2);font-size:18px">✕</button>
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:20px;color:var(--txt)">Editar Imagen</h2>

    <div style="margin-bottom:16px;border-radius:var(--radius-sm);overflow:hidden;aspect-ratio:3/2;background:var(--bg)">
      <img id="editPreviewImg" src="" alt="" style="width:100%;height:100%;object-fit:cover">
    </div>

    <form id="editForm" method="POST" action="" enctype="multipart/form-data">
      @csrf
      <div class="fg">
        <label>Reemplazar imagen (opcional)</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
               onchange="updateEditPreview(this)">
        <p class="form-hint">JPG, PNG o WEBP. Máx. 25 MB.</p>
      </div>
      <div class="fg">
        <label>Pie de foto</label>
        <input type="text" name="caption" id="editCaption" placeholder="Ej: Playa Los Cabos" maxlength="191">
      </div>
      <div class="fg" style="margin-bottom:0">
        <label>Orden</label>
        <input type="number" name="sort_order" id="editOrder" min="0">
      </div>
      <div class="fg" style="margin-top:12px">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.85rem;color:var(--txt);text-transform:none;letter-spacing:0;font-weight:400">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" id="editActive" value="1" style="width:auto;accent-color:var(--teal)">
          Visible en la galería
        </label>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px">
        <button type="button" onclick="closeEditModal()" class="btn btn-ghost">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<script>
const galleryBaseUrl = '{{ url('admin/cms/gallery') }}';

function openUploadModal() { document.getElementById('uploadModal').style.display = 'flex'; }
function closeUploadModal() { document.getElementById('uploadModal').style.display = 'none'; }

// Reabrir modal automáticamente si hay errores de subida
@if($errors->has('images') || session('gallery_upload_error'))
document.addEventListener('DOMContentLoaded', () => openUploadModal());
@endif

function openEditModal(id, caption, order, imgUrl) {
  document.getElementById('editForm').action = galleryBaseUrl + '/' + id;
  document.getElementById('editCaption').value = caption;
  document.getElementById('editOrder').value   = order;
  document.getElementById('editPreviewImg').src = imgUrl;
  document.getElementById('editActive').checked = true;
  document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }

function previewGallery(input) {
  const wrap = document.getElementById('galleryPreviews');
  wrap.innerHTML = '';
  if (input.files && input.files.length) {
    wrap.style.display = 'grid';
    Array.from(input.files).forEach(file => {
      const reader = new FileReader();
      reader.onload = e => {
        const div = document.createElement('div');
        div.style.cssText = 'aspect-ratio:3/2;border-radius:6px;overflow:hidden;background:var(--bg)';
        const img = document.createElement('img');
        img.src = e.target.result;
        img.style.cssText = 'width:100%;height:100%;object-fit:cover';
        div.appendChild(img);
        wrap.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  }
}

function handleDrop(e) {
  e.preventDefault();
  const input = document.getElementById('galleryFiles');
  const dt    = e.dataTransfer;
  if (dt.files.length) {
    input.files = dt.files;
    previewGallery(input);
    e.target.style.borderColor = 'var(--border)';
    e.target.style.background  = 'transparent';
  }
}

function updateEditPreview(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => document.getElementById('editPreviewImg').src = e.target.result;
    reader.readAsDataURL(input.files[0]);
  }
}

// Close modals on backdrop click
['uploadModal','editModal'].forEach(id => {
  document.getElementById(id).addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
  });
});
</script>

@endsection

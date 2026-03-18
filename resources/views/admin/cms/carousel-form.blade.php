@extends('layouts.admin')
@section('page-title', isset($slide->id) ? 'Editar Diapositiva' : 'Nueva Diapositiva')
@section('content')

<div class="page-header">
  <div>
    <h1>{{ isset($slide->id) ? 'Editar Diapositiva' : 'Nueva Diapositiva' }}</h1>
    <p>Carrusel hero de la página de inicio</p>
  </div>
  <a href="{{ route('admin.cms.carousel.index') }}" class="btn btn-ghost">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-error">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <ul style="margin:0;padding-left:16px">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start">

  {{-- Main form --}}
  <div class="card">
    <form method="POST"
          action="{{ isset($slide->id) ? route('admin.cms.carousel.update', $slide) : route('admin.cms.carousel.store') }}"
          enctype="multipart/form-data"
          onsubmit="const btn=this.querySelector('[type=submit]');btn.disabled=true;btn.innerHTML='<svg width=\'14\' height=\'14\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'20 6 9 17 4 12\'/></svg> Guardando...'">
      @csrf

      {{-- Image upload --}}
      <div class="form-section">
        <div class="form-section-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          Imagen de Fondo
        </div>

        <div class="alert" style="background:#F0FDF4;color:#166534;border-color:#BBF7D0;font-size:.78rem;margin-bottom:14px">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 11 12 14 22 4"/></svg>
          <span>Puedes subir imágenes de <strong>cualquier tamaño</strong>. El sistema las adapta automáticamente al carrusel. · Formatos: JPG, PNG, WEBP · Máx. <strong>20 MB</strong></span>
        </div>

        @if(isset($slide->id) && $slide->image_path)
          <div style="margin-bottom:12px">
            <div style="font-size:.68rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--txt2);margin-bottom:6px">Imagen actual</div>
            <div style="border-radius:var(--radius-sm);overflow:hidden;max-height:160px;background:var(--bg)">
              <img src="{{ $slide->image_url }}" alt="current" style="width:100%;max-height:160px;object-fit:cover">
            </div>
          </div>
        @endif

        <div class="fg" style="margin-bottom:0">
          <label>{{ isset($slide->id) ? 'Reemplazar imagen (opcional)' : 'Imagen *' }}</label>
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                 id="imgInput" onchange="previewImg(this)"
                 {{ !isset($slide->id) ? 'required' : '' }}>
          <div id="imgPreviewWrap" style="display:none;margin-top:10px;border-radius:var(--radius-sm);overflow:hidden;max-height:160px;background:var(--bg)">
            <img id="imgPreview" src="" alt="preview" style="width:100%;max-height:160px;object-fit:cover">
          </div>
          <p class="form-hint">JPG, PNG o WEBP. Máximo 20 MB.</p>
        </div>
      </div>

      {{-- Text content --}}
      <div class="form-section">
        <div class="form-section-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Contenido de Texto
        </div>

        <div class="fg">
          <label>Título principal</label>
          <input type="text" name="title" value="{{ old('title', $slide->title ?? '') }}"
                 placeholder="Ej: Traslados privados de lujo" maxlength="191">
          <p class="form-hint">Se mostrará como encabezado grande en el hero.</p>
        </div>

        <div class="fg">
          <label>Subtítulo / Descripción</label>
          <input type="text" name="subtitle" value="{{ old('subtitle', $slide->subtitle ?? '') }}"
                 placeholder="Ej: Servicio profesional desde el aeropuerto" maxlength="255">
        </div>
      </div>

      {{-- Call to Action --}}
      <div class="form-section">
        <div class="form-section-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Botón (opcional)
        </div>

        <div class="fg-row">
          <div class="fg">
            <label>Texto del botón</label>
            <input type="text" name="button_text" value="{{ old('button_text', $slide->button_text ?? '') }}"
                   placeholder="Ej: Reservar Ahora" maxlength="80">
          </div>
          <div class="fg">
            <label>URL / Ancla de destino</label>
            <input type="text" name="button_url" value="{{ old('button_url', $slide->button_url ?? '') }}"
                   placeholder="Ej: #zones-section o https://..." maxlength="255">
          </div>
        </div>
      </div>

      {{-- Settings --}}
      <div class="form-section" style="margin-bottom:0">
        <div class="form-section-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
          Publicación
        </div>

        <div class="fg-row" style="align-items:center">
          <div class="fg" style="margin-bottom:0">
            <label>Orden de aparición</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $slide->sort_order ?? 0) }}" min="0" max="99">
            <p class="form-hint">Menor número → aparece primero.</p>
          </div>
          <div class="fg" style="margin-bottom:0;margin-top:20px">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.85rem;color:var(--txt);text-transform:none;letter-spacing:0;font-weight:400">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" value="1"
                     {{ old('is_active', $slide->is_active ?? true) ? 'checked' : '' }}
                     style="width:auto;accent-color:var(--teal)">
              Diapositiva activa (visible)
            </label>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:24px;padding-top:20px;border-top:1px solid var(--border)">
        <a href="{{ route('admin.cms.carousel.index') }}" class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          {{ isset($slide->id) ? 'Guardar Cambios' : 'Crear Diapositiva' }}
        </button>
      </div>

    </form>
  </div>

  {{-- Sidebar info --}}
  <div>
    <div class="card" style="background:var(--teal-lt);border-color:var(--teal-md)">
      <div class="card-title" style="margin-bottom:14px">
        <span class="ct-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span>
        Guía de imágenes
      </div>
      <ul style="font-size:.78rem;color:var(--txt2);line-height:2;padding-left:16px">
        <li>Cualquier resolución es aceptada</li>
        <li>El carrusel adapta la imagen automáticamente</li>
        <li>Relación de aspecto ideal: <strong>16:5</strong></li>
        <li>Formato de entrada: JPG, PNG, WEBP</li>
        <li>Peso máximo: <strong>20 MB</strong></li>
        <li>Sujeto centrado = mejor resultado</li>
      </ul>
    </div>
    <div class="card" style="margin-top:0">
      <div class="card-title" style="margin-bottom:14px">
        <span class="ct-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/></svg></span>
        Cómo funciona
      </div>
      <p style="font-size:.78rem;color:var(--txt2);line-height:1.7">
        Las diapositivas activas se muestran como fondo del <em>hero</em> de la página de inicio,
        rotando automáticamente cada 6 segundos.
        El título y subtítulo se superponen sobre la imagen.
      </p>
    </div>
  </div>

</div>

<script>
function previewImg(input) {
  const wrap = document.getElementById('imgPreviewWrap');
  const img  = document.getElementById('imgPreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      img.src = e.target.result;
      wrap.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

@endsection

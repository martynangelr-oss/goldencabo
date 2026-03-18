@extends('layouts.admin')
@section('page-title', 'Carrusel Inicio')
@section('content')

<div class="page-header">
  <div>
    <h1>Carrusel de Inicio</h1>
    <p>Administra las diapositivas del hero carousel del sitio web</p>
  </div>
  <a href="{{ route('admin.cms.carousel.create') }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nueva Diapositiva
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
  </div>
@endif

{{-- Dimension hint --}}
<div class="alert" style="background:#EFF6FF;color:#1E40AF;border-color:#BFDBFE;margin-bottom:20px">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  <span>Sube imágenes de <strong>cualquier tamaño</strong> — el sistema las adapta automáticamente a <strong>1920 × 900 px</strong> · Formatos: JPG, PNG, WEBP · Peso máximo: <strong>20 MB</strong>.</span>
</div>

@if($slides->isEmpty())
  <div class="card" style="text-align:center;padding:48px">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--txt2)" stroke-width="1.5" style="margin:0 auto 16px"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3h-8"/></svg>
    <p style="color:var(--txt2);font-size:.88rem">No hay diapositivas. Crea la primera.</p>
    <a href="{{ route('admin.cms.carousel.create') }}" class="btn btn-primary" style="margin-top:16px">Crear Diapositiva</a>
  </div>
@else
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px">
    @foreach($slides as $slide)
    <div class="card" style="padding:0;overflow:hidden;border:2px solid {{ $slide->is_active ? 'var(--teal)' : 'var(--border)' }};transition:border-color .2s">

      {{-- Thumbnail --}}
      <div style="position:relative;aspect-ratio:16/5;background:var(--bg);overflow:hidden">
        <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}"
             style="width:100%;height:100%;object-fit:cover;{{ $slide->is_active ? '' : 'filter:grayscale(60%) opacity(.6)' }}">

        {{-- Orden badge --}}
        <div style="position:absolute;top:8px;left:8px;background:rgba(0,0,0,.55);color:#fff;font-size:.65rem;font-weight:700;padding:3px 8px;border-radius:20px">
          #{{ $slide->sort_order }}
        </div>

        {{-- Estado overlay cuando está oculta --}}
        @if(!$slide->is_active)
          <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none">
            <span style="background:rgba(0,0,0,.5);color:#fff;font-size:.7rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 12px;border-radius:20px">OCULTA</span>
          </div>
        @endif
      </div>

      {{-- Info --}}
      <div style="padding:14px 16px">
        <div style="font-weight:700;font-size:.88rem;color:var(--txt);margin-bottom:2px">
          {{ $slide->title ?: '(sin título)' }}
        </div>
        @if($slide->subtitle)
          <div style="font-size:.75rem;color:var(--txt2);margin-bottom:8px">{{ Str::limit($slide->subtitle, 70) }}</div>
        @else
          <div style="font-size:.75rem;color:var(--txt2);margin-bottom:8px">Sin subtítulo</div>
        @endif

        @if($slide->button_text)
          <div style="font-size:.72rem;color:var(--teal);margin-bottom:10px">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            {{ $slide->button_text }}
          </div>
        @endif

        {{-- Actions row --}}
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding-top:10px;border-top:1px solid var(--border)">

          {{-- Toggle activar/desactivar --}}
          <form method="POST" action="{{ route('admin.cms.carousel.toggle', $slide) }}" style="flex-shrink:0">
            @csrf
            <button type="submit"
                    style="display:flex;align-items:center;gap:7px;padding:6px 12px;border-radius:20px;border:none;cursor:pointer;font-size:.72rem;font-weight:700;letter-spacing:.5px;transition:background .2s,color .2s;
                           {{ $slide->is_active
                              ? 'background:var(--teal-lt);color:var(--teal)'
                              : 'background:#F3F4F6;color:var(--txt2)' }}">
              {{-- Toggle track --}}
              <span style="width:28px;height:16px;border-radius:8px;display:inline-block;position:relative;flex-shrink:0;transition:background .2s;
                           {{ $slide->is_active ? 'background:var(--teal)' : 'background:#D1D5DB' }}">
                <span style="position:absolute;top:2px;width:12px;height:12px;border-radius:50%;background:#fff;transition:left .2s;
                              {{ $slide->is_active ? 'left:14px' : 'left:2px' }}"></span>
              </span>
              {{ $slide->is_active ? 'Activa' : 'Oculta' }}
            </button>
          </form>

          {{-- Edit + Delete --}}
          <div style="display:flex;gap:6px">
            <a href="{{ route('admin.cms.carousel.edit', $slide) }}"
               class="btn btn-ghost btn-sm btn-icon" title="Editar">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
            <form method="POST" action="{{ route('admin.cms.carousel.destroy', $slide) }}"
                  onsubmit="return confirm('¿Eliminar esta diapositiva?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Eliminar">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
              </button>
            </form>
          </div>

        </div>
      </div>
    </div>
    @endforeach
  </div>
@endif

@endsection

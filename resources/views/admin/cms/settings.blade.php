@extends('layouts.admin')
@section('page-title', 'Configuración Global')
@section('content')

@php
  use App\Models\SiteSetting;
  $logoUrl = SiteSetting::fileUrl('logo');
@endphp

<div class="page-header">
  <div>
    <h1>Configuración Global</h1>
    <p>Logo del sitio, datos de contacto y textos generales</p>
  </div>
</div>

<form method="POST" action="{{ route('admin.cms.settings.update') }}" enctype="multipart/form-data">
  @csrf

  <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    <div>
      {{-- Logo --}}
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span class="ct-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></span>
            Logo del Sitio
          </div>
        </div>

        <div style="display:grid;grid-template-columns:auto 1fr;gap:24px;align-items:center">

          {{-- Preview --}}
          <div style="width:120px;height:80px;border:1.5px solid var(--border);border-radius:var(--radius-sm);
                      display:flex;align-items:center;justify-content:center;background:var(--bg);overflow:hidden">
            @if($logoUrl)
              <img id="logoPreview" src="{{ $logoUrl }}" alt="Logo actual"
                   style="max-width:100%;max-height:100%;object-fit:contain">
            @else
              <div id="logoPreview" style="text-align:center">
                <div style="font-size:.6rem;color:var(--txt2);text-transform:uppercase;letter-spacing:1px">Sin logo<br>personalizado</div>
              </div>
            @endif
          </div>

          <div>
            <div class="fg" style="margin-bottom:8px">
              <label>Subir nuevo logo</label>
              <input type="file" name="logo" id="logoInput" accept="image/png,image/jpg,image/jpeg,image/svg+xml,image/webp"
                     onchange="previewLogo(this)">
              <p class="form-hint">PNG con fondo transparente recomendado. SVG, JPG o WEBP también aceptados. Máx. 2 MB.</p>
            </div>
            <div class="alert" style="background:#FEF3C7;color:#92400E;border-color:#FDE68A;font-size:.76rem;margin-bottom:0">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              Dimensiones recomendadas: <strong>240 × 80 px</strong> · Fondo transparente (PNG) para mejor resultado.
            </div>
          </div>
        </div>

        @if($logoUrl)
          <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
            <button type="button" class="btn btn-danger btn-sm"
                    onclick="if(confirm('¿Eliminar logo personalizado y volver al logo de texto?')) document.getElementById('_remove_logo_form').submit()">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
              Eliminar logo personalizado
            </button>
          </div>
        @endif
      </div>

      {{-- Contact info --}}
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span class="ct-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
            Datos de Contacto
          </div>
        </div>

        <div class="fg-row">
          <div class="fg">
            <label>Teléfono principal</label>
            <input type="text" name="phone_primary"
                   value="{{ old('phone_primary', SiteSetting::get('phone_primary', '(+52) 333 303 4455')) }}"
                   placeholder="(+52) 333 303 4455" maxlength="30">
          </div>
          <div class="fg">
            <label>Teléfono secundario</label>
            <input type="text" name="phone_secondary"
                   value="{{ old('phone_secondary', SiteSetting::get('phone_secondary', '(+52) 624 121 6527')) }}"
                   placeholder="(+52) 624 121 6527" maxlength="30">
          </div>
        </div>

        <div class="fg">
          <label>Correo de contacto</label>
          <input type="email" name="email_contact"
                 value="{{ old('email_contact', SiteSetting::get('email_contact', 'goldencabotransportation@gmail.com')) }}"
                 placeholder="correo@empresa.com" maxlength="191">
        </div>

        <div class="fg" style="margin-bottom:0">
          <label>Dirección</label>
          <textarea name="address" rows="2" maxlength="500"
                    placeholder="Calle Huanacastle Esq. Eucalipto...">{{ old('address', SiteSetting::get('address', 'Calle Huanacastle Esq. Eucalipto Mza 70 lte 1, Col. Las Veredas, CP 23436, San José del Cabo, BCS')) }}</textarea>
        </div>
      </div>

      {{-- Chat integrations --}}
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span class="ct-icon">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </span>
            Integraciones de Chat
          </div>
        </div>

        <div class="alert" style="background:#F0FDF4;color:#14532D;border-color:#BBF7D0;font-size:.77rem;margin-bottom:18px">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span>Los botones de chat flotante aparecen en la esquina inferior izquierda del sitio cuando hay datos configurados.</span>
        </div>

        {{-- WhatsApp --}}
        <div style="display:flex;align-items:flex-start;gap:14px;padding:14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);margin-bottom:14px;background:#F9FEFF">
          <div style="width:38px;height:38px;border-radius:50%;background:#25D366;flex-shrink:0;display:flex;align-items:center;justify-content:center">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
          </div>
          <div style="flex:1">
            <div style="font-size:.8rem;font-weight:700;color:var(--txt);margin-bottom:4px">WhatsApp</div>
            <div class="fg" style="margin-bottom:0">
              <input type="text" name="whatsapp"
                     value="{{ old('whatsapp', SiteSetting::get('whatsapp', '')) }}"
                     placeholder="+523331234567"
                     maxlength="30">
              <p class="form-hint">Número con código de país sin espacios. Ej: <strong>+523331234567</strong></p>
            </div>
          </div>
        </div>

        {{-- Messenger --}}
        <div style="display:flex;align-items:flex-start;gap:14px;padding:14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:#F5F0FF">
          <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#0099FF,#A033FF);flex-shrink:0;display:flex;align-items:center;justify-content:center">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.477 2 2 6.145 2 11.243c0 2.908 1.444 5.503 3.71 7.21V22l3.38-1.86c.904.25 1.862.385 2.91.385 5.523 0 10-4.144 10-9.282C22 6.145 17.523 2 12 2zm1.006 12.5-2.548-2.72-4.97 2.72 5.467-5.804 2.61 2.72 4.907-2.72-5.466 5.804z"/></svg>
          </div>
          <div style="flex:1">
            <div style="font-size:.8rem;font-weight:700;color:var(--txt);margin-bottom:4px">Messenger</div>
            <div class="fg" style="margin-bottom:0">
              <input type="text" name="messenger_url"
                     value="{{ old('messenger_url', SiteSetting::get('messenger_url', '')) }}"
                     placeholder="m.me/111697618213600">
              <p class="form-hint">Acepta con o sin https://. Ej: <strong>m.me/111697618213600</strong></p>
            </div>
          </div>
        </div>
      </div>

      {{-- Site identity --}}
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span class="ct-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg></span>
            Identidad del Sitio
          </div>
        </div>

        <div class="fg">
          <label>Nombre del sitio</label>
          <input type="text" name="site_name"
                 value="{{ old('site_name', SiteSetting::get('site_name', 'Golden Cabo Transportation')) }}"
                 placeholder="Golden Cabo Transportation" maxlength="100">
        </div>

        <div class="fg" style="margin-bottom:0">
          <label>Tagline / Slogan</label>
          <input type="text" name="site_tagline"
                 value="{{ old('site_tagline', SiteSetting::get('site_tagline', 'Traslados privados de lujo en Los Cabos')) }}"
                 placeholder="Traslados privados de lujo en Los Cabos" maxlength="191">
        </div>
      </div>
    </div>

    {{-- Sidebar --}}
    <div>
      <div class="card" style="position:sticky;top:20px">
        <div class="card-title" style="margin-bottom:16px">
          <span class="ct-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
          Guardar cambios
        </div>
        <p style="font-size:.78rem;color:var(--txt2);margin-bottom:20px;line-height:1.6">
          Los cambios se reflejarán inmediatamente en el sitio web una vez guardados.
        </p>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Guardar Configuración
        </button>

        <div class="sb-divider" style="margin:16px 0"></div>

        <div style="font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--txt2);margin-bottom:10px">Guía de logo</div>
        <ul style="font-size:.75rem;color:var(--txt2);line-height:2;padding-left:16px">
          <li>Ancho ideal: <strong>240 px</strong></li>
          <li>Alto ideal: <strong>80 px</strong></li>
          <li>Formato: <strong>PNG transparente</strong></li>
          <li>También acepta: SVG, JPG, WEBP</li>
          <li>Peso máximo: <strong>2 MB</strong></li>
        </ul>
      </div>
    </div>

  </div>
</form>

{{-- Form de eliminar logo fuera del form principal para evitar forms anidados --}}
@if($logoUrl)
<form id="_remove_logo_form" method="POST" action="{{ route('admin.cms.settings.logo.remove') }}" style="display:none">
  @csrf @method('DELETE')
</form>
@endif

<script>
function previewLogo(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      const wrap = document.getElementById('logoPreview');
      if (wrap.tagName === 'IMG') {
        wrap.src = e.target.result;
      } else {
        const img = document.createElement('img');
        img.id    = 'logoPreview';
        img.src   = e.target.result;
        img.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain';
        wrap.parentNode.replaceChild(img, wrap);
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

@endsection

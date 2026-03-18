@extends('layouts.app')
@section('title','Golden Cabo Transportation — Traslados Privados Los Cabos')
@section('content')

{{-- FAB --}}
<button class="fab" onclick="openW(event)">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
  <span data-i18n="fab.book">Reservar</span>
</button>

{{-- Chat bubbles (WhatsApp & Messenger) --}}
@php
  $waNumber = preg_replace('/[^0-9]/', '', $siteSettings['whatsapp'] ?? '');
  $msRaw    = trim($siteSettings['messenger_url'] ?? '');
  $msUrl    = $msRaw ? (preg_match('#^https?://#i', $msRaw) ? $msRaw : 'https://' . $msRaw) : '';
@endphp
@if($waNumber || $msUrl)
<div class="chat-bubbles">
  @if($msUrl)
  <a href="{{ $msUrl }}" target="_blank" rel="noopener" class="chat-bubble chat-bubble--ms" title="Messenger">
    <svg viewBox="0 0 24 24" fill="currentColor" style="color:#fff;width:26px;height:26px">
      <path d="M12 2C6.477 2 2 6.145 2 11.243c0 2.908 1.444 5.503 3.71 7.21V22l3.38-1.86c.904.25 1.862.385 2.91.385 5.523 0 10-4.144 10-9.282C22 6.145 17.523 2 12 2zm1.006 12.5-2.548-2.72-4.97 2.72 5.467-5.804 2.61 2.72 4.907-2.72-5.466 5.804z"/>
    </svg>
    <span class="chat-bubble-tooltip">Messenger</span>
  </a>
  @endif
  @if($waNumber)
  <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="chat-bubble chat-bubble--wa" title="WhatsApp">
    <svg viewBox="0 0 24 24" fill="currentColor" style="color:#fff;width:26px;height:26px">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
    </svg>
    <span class="chat-bubble-tooltip">WhatsApp</span>
  </a>
  @endif
</div>
@endif

{{-- NAVBAR --}}
<nav id="nav">
  <a href="{{ route('home') }}" class="logo">
    @if(!empty($siteSettings['logo']))
      <img src="{{ $siteSettings['logo'] }}" alt="{{ $siteSettings['name'] }}"
           style="height:44px;width:auto;object-fit:contain;display:block">
    @else
      <div class="logo-mark">✦</div>
      <div class="logo-text">
        <div class="ln"><span>Golden</span> Cabo</div>
        <div class="ls">Transportation</div>
      </div>
    @endif
  </a>
  <ul class="nav-links" id="navLinks">
    <li><a href="#hero" data-i18n="nav.home">Inicio</a></li>
    <li><a href="#recorridos" data-i18n="nav.tours">Recorridos</a></li>
    <li><a href="#flota" data-i18n="nav.transport">Transporte</a></li>
    <li><a href="#galeria" data-i18n="nav.gallery">Galería</a></li>
    <li><a href="#acerca" data-i18n="nav.about">Acerca</a></li>
    <li><a href="#contacto" data-i18n="nav.contact">Contacto</a></li>
    <li><a href="#" class="nav-cta" onclick="openW(event)" data-i18n="nav.book">Reservar</a></li>
    <li class="gc-lang-switch">
      <button id="btn-lang-es" class="gc-lang-btn active" onclick="gcSetLang('es')">ES</button>
      <span class="gc-lang-div">|</span>
      <button id="btn-lang-en" class="gc-lang-btn" onclick="gcSetLang('en')">EN</button>
    </li>
  </ul>
  <div class="ham" id="ham" onclick="toggleNav()"><span></span><span></span><span></span></div>
</nav>
<div class="nav-drawer" id="navDrawer">
  <button class="drawer-x" onclick="closeNav()">✕</button>
  <ul class="nav-links" onclick="closeNav()">
    <li><a href="#hero" data-i18n="nav.home">Inicio</a></li>
    <li><a href="#recorridos" data-i18n="nav.tours">Recorridos</a></li>
    <li><a href="#flota" data-i18n="nav.transport">Transporte</a></li>
    <li><a href="#galeria" data-i18n="nav.gallery">Galería</a></li>
    <li><a href="#acerca" data-i18n="nav.about">Acerca</a></li>
    <li><a href="#contacto" data-i18n="nav.contact">Contacto</a></li>
    <li><a href="#" class="nav-cta" onclick="openW(event)" data-i18n="nav.book_now">Reservar Ahora</a></li>
    <li class="gc-lang-switch gc-lang-switch--drawer">
      <button id="btn-lang-es-d" class="gc-lang-btn active" onclick="gcSetLang('es');closeNav()">ES</button>
      <span class="gc-lang-div">|</span>
      <button id="btn-lang-en-d" class="gc-lang-btn" onclick="gcSetLang('en');closeNav()">EN</button>
    </li>
  </ul>
</div>

<style>
.gc-lang-switch { display:flex; align-items:center; gap:4px; margin-left:4px; }
.gc-lang-switch--drawer { justify-content:center; margin:8px 0 0; }
.gc-lang-btn {
  background:none; border:none; cursor:pointer; padding:3px 7px;
  font-family:'Josefin Sans',sans-serif; font-size:.65rem; font-weight:700;
  letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,.65);
  border-radius:4px; transition:color .2s, background .2s;
}
.gc-lang-btn:hover { color:#fff; }
.gc-lang-btn.active { color:#fff; background:rgba(255,255,255,.15); }
nav.solid .gc-lang-btn { color:rgba(5,30,28,.55); }
nav.solid .gc-lang-btn:hover { color:#009988; }
nav.solid .gc-lang-btn.active { color:#009988; background:rgba(0,153,136,.1); }
.gc-lang-div { color:rgba(255,255,255,.3); font-size:.65rem; }
nav.solid .gc-lang-div { color:rgba(5,30,28,.25); }
</style>

{{-- HERO --}}
<section id="hero">
  {{-- Dynamic slides or fallback static slides --}}
  @if(!empty($slides))
    @foreach($slides as $i => $slide)
      <div class="hs s{{ $i+1 }} {{ $i===0?'on':'' }}"
           style="background-image:url('{{ $slide['image'] }}')"
           data-title="{{ $slide['title'] ?? '' }}"
           data-subtitle="{{ $slide['subtitle'] ?? '' }}"
           data-btn-text="{{ $slide['button_text'] ?? '' }}"
           data-btn-url="{{ $slide['button_url'] ?? '' }}"></div>
    @endforeach
  @else
    <div class="hs s1 on" style="background-image:url('https://images.unsplash.com/photo-1512813195386-6cf811ad3542?w=1900&q=85')"></div>
    <div class="hs s2" style="background-image:url('https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=1900&q=85')"></div>
    <div class="hs s3" style="background-image:url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1900&q=85')"></div>
    <div class="hs s4" style="background-image:url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1900&q=85')"></div>
  @endif
  <div class="hero-orb"></div>
  <div class="hero-wave">
    <svg viewBox="0 0 1440 72" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,72 C360,0 1080,0 1440,72 L1440,72 L0,72 Z" fill="#ffffff"/>
    </svg>
  </div>
  {{-- First slide content (or default) --}}
  @php $firstSlide = $slides[0] ?? null; @endphp
  <div class="hero-content">
    <div class="hero-badge"><span class="badge-dot"></span>Los Cabos, Baja California Sur</div>
    @if($firstSlide && $firstSlide['title'])
      <h1 class="hero-h" id="heroTitle">{!! nl2br(e($firstSlide['title'])) !!}</h1>
    @else
      <h1 class="hero-h" id="heroTitle" data-i18n-html="hero.title_html">Traslados<br><em>privados</em><br>de lujo</h1>
    @endif
    @if($firstSlide && $firstSlide['subtitle'])
      <p class="hero-sub" id="heroSub">{{ $firstSlide['subtitle'] }}</p>
    @else
      <p class="hero-sub" id="heroSub" data-i18n="hero.sub">Servicio profesional de transporte y tours exclusivos desde y hacia el Aeropuerto Internacional de Los Cabos.</p>
    @endif
    <div class="hero-actions">
      @if($firstSlide && $firstSlide['button_text'] && $firstSlide['button_url'])
        <a href="{{ $firstSlide['button_url'] }}" class="btn btn-teal" id="heroBtn">{{ $firstSlide['button_text'] }}</a>
      @else
        <button class="btn btn-teal" onclick="openW(event)" id="heroBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="16" height="16"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <span data-i18n="hero.btn">Reservar Ahora</span>
        </button>
      @endif
      <a href="#zones-section" class="btn btn-glass" data-i18n="hero.rates">Ver Tarifas</a>
    </div>
  </div>
  <div class="hero-dots" id="heroDots"></div>
  <div class="hcnt" id="hcnt">01 / {{ count($slides) ?: 4 }}</div>
</section>

{{-- STATS --}}
<div class="stats">
  <div class="stat"><div class="stat-n">10+</div><div class="stat-l" data-i18n="stats.experience">Años de experiencia</div></div>
  <div class="stat"><div class="stat-n">4</div><div class="stat-l" data-i18n="stats.zones">Zonas cubiertas</div></div>
  <div class="stat"><div class="stat-n">500+</div><div class="stat-l" data-i18n="stats.clients">Clientes felices</div></div>
  <div class="stat"><div class="stat-n">24/7</div><div class="stat-l" data-i18n="stats.availability">Disponibilidad</div></div>
</div>

{{-- ZONAS --}}
<section class="section" id="zones-section">
  <div class="container">
    <div class="tc rv">
      <div class="chip" data-i18n="zones.chip">Cobertura Regional</div>
      <h2 class="display" data-i18n-html="zones.title_html">Zonas de Transporte <em>Golden Cabo</em></h2>
      <p class="lead" data-i18n="zones.lead">Seleccione su zona de destino para conocer precios y la lista de hoteles disponibles.</p>
      <div class="zone-tabs">
        @foreach($zones as $num => $zone)
          <button class="ztab {{ $num===1?'on':'' }}" onclick="showZone({{$num}},this)"><span data-i18n="zones.zone">Zona</span> {{$num}}</button>
        @endforeach
      </div>
    </div>
    <div class="zpanels">
      @foreach($zones as $num => $zone)
      <div class="zpanel {{ $num===1?'on':'' }} rv" id="zp{{$num}}">
        <div class="zcard">
          <div class="zcard-c1"></div><div class="zcard-c2"></div><div class="zcwm">{{$num}}</div>
          <div class="zbadge"><span data-i18n="zones.zone">Zona</span> {{$num}}</div>
          <div class="zname">{{ $zone['name'] }}</div>
          <div class="zpgrid">
            <div class="zpbox"><div class="zpbox-l" data-i18n="zones.round_trip">Ida y Vuelta</div><div class="zpbox-p">{{ $zone['round'] }}</div></div>
            <div class="zpbox"><div class="zpbox-l" data-i18n="zones.one_way">Solo Ida</div><div class="zpbox-p">{{ $zone['oneway'] }}</div></div>
          </div>
          <button class="zbtn" onclick="showHotels({{$num}})">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span data-i18n="zones.hotel_btn">Ver Lista de Hoteles</span>
          </button>
        </div>
        <div class="zstack">
          <div class="zimg-a"><img src="{{ $zone['img_main'] ?? 'https://images.unsplash.com/photo-1512813195386-6cf811ad3542?w=900&q=85' }}" alt="{{ $zone['name'] }}"></div>
          <div class="zimg-b"><img src="{{ $zone['img_sec'] ?? 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80' }}" alt="Transporte"></div>
          <div class="zfloat"><div class="zf-n">{{ $zone['time'] }}</div><div class="zf-l" data-i18n="zones.from_airport">del aeropuerto</div></div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- FLEET --}}
<section class="section s-alt" id="flota">
  <div class="container">
    <div class="tc rv" style="margin-bottom:52px">
      <div class="chip" data-i18n="fleet.chip">Nuestra Flota</div>
      <h2 class="display" data-i18n-html="fleet.title_html">Vehículos de <em>Primera Clase</em></h2>
      <p class="lead" data-i18n="fleet.lead">Flota moderna, equipada y climatizada para su máximo confort en cada traslado.</p>
    </div>
    <div class="rv">
      <div class="fleet-outer">
        <div class="fleet-track" id="fleetTrack">
          @foreach($fleet as $v)
          <div class="fcard">
            <div class="fcard-img">
              <img src="{{ $v['image'] }}" alt="{{ $v['model'] }}">
              <div class="fbadge" @if($v['badge_color']==='gold') style="background:linear-gradient(135deg,#B8860B,#DAA520)" @endif>{{ $v['badge'] }}</div>
            </div>
            <div class="fcard-body">
              <div class="fbrand">{{ $v['brand'] }}</div>
              <div class="fmodel">{{ $v['model'] }}</div>
              <div class="ffeats">
                @foreach($v['features'] as $f)
                  @php
                    $emoji = mb_substr($f, 0, 2);
                    $text  = trim(mb_substr($f, 2));
                  @endphp
                  <div class="ff">
                    <div class="ff-i">{{ $emoji }}</div>
                    <div class="ff-txt">
                      <span class="ff-label">{{ $text }}</span>
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="ffoot">
                <div class="fpax">
                  <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                  <span data-i18n="fleet.pax_tpl" data-i18n-n="{{ $v['pax'] }}">Hasta {{ $v['pax'] }} pasajeros</span>
                </div>
                <button class="btn btn-teal btn-sm" onclick="openW(event)" data-i18n="fleet.book">Reservar</button>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      <div class="ctrls">
        <button class="cbtn" onclick="moveFleet(-1)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg></button>
        <div class="cdots" id="fleetDots"></div>
        <button class="cbtn" onclick="moveFleet(1)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg></button>
      </div>
    </div>
  </div>
</section>

{{-- ABOUT --}}
<section class="section" id="acerca">
  <div class="container">
    <div class="about-grid">
      <div class="about-vis rv">
        <div class="av-a"><img src="{{ $sectionImages['about_main'] }}" alt="Los Cabos"></div>
        <div class="av-b"><img src="{{ $sectionImages['about_secondary'] }}" alt="Transporte"></div>
        <div class="av-badge"><div class="av-badge-n">10+</div><div class="av-badge-l" data-i18n="about.years">Años en servicio</div></div>
      </div>
      <div class="rv d2">
        <div class="chip" data-i18n="about.chip">Acerca de Nosotros</div>
        <h2 class="display" data-i18n-html="about.title_html">Traslados con <em>pasión</em> y profesionalismo</h2>
        <p class="lead" data-i18n="about.lead">Somos una empresa nacida de la pasión por el turismo en Los Cabos. Nos esforzamos por brindarle las mejores experiencias durante sus vacaciones.</p>
        <div class="feats">
          <div class="feat"><div class="feat-i">🛡️</div><div><h4 data-i18n="about.feat1_title">Seguridad Garantizada</h4><p data-i18n="about.feat1_desc">Conductores certificados y vehículos en perfectas condiciones.</p></div></div>
          <div class="feat"><div class="feat-i">⭐</div><div><h4 data-i18n="about.feat2_title">Experiencia Inolvidable</h4><p data-i18n="about.feat2_desc">Servicio personalizado para que cada traslado sea especial.</p></div></div>
          <div class="feat"><div class="feat-i">🚗</div><div><h4 data-i18n="about.feat3_title">Flota Moderna</h4><p data-i18n="about.feat3_desc">Vehículos nuevos, cómodos y climatizados para su máximo placer.</p></div></div>
        </div>
        <button class="btn btn-teal" onclick="openW(event)" data-i18n="about.btn">Reservar Ahora</button>
      </div>
    </div>
  </div>
</section>

{{-- TOURS --}}
<section class="section s-alt" id="recorridos">
  <div class="container">
    <div class="tc rv" style="margin-bottom:52px">
      <div class="chip" data-i18n="tours.chip">Excursiones</div>
      <h2 class="display" data-i18n-html="tours.title_html">Recorridos por <em>la Ciudad</em></h2>
      <p class="lead" data-i18n="tours.lead">Tours en grupos pequeños para una experiencia más personalizada.</p>
    </div>
    <div class="tours-grid">
      @foreach($tours as $i => $tour)
      <div class="tcard rv {{ $i>0?'d'.($i*2):'' }}">
        <div class="timg">
          <img src="{{ $tour['image'] }}" alt="{{ $tour['name'] }}">
          <div class="tprice">{{ $tour['price'] }}</div>
          <div class="tdur">⏱ {{ $tour['duration'] }}</div>
        </div>
        <div class="tbody">
          <div class="tname">{{ $tour['name'] }}</div>
          <p class="tdesc">{{ $tour['description'] }}</p>
          <div class="ttags">
            @foreach($tour['tags'] as $tag)
              <span class="ttag">{{ $tag }}</span>
            @endforeach
          </div>
          <div class="tfoot">
            <div class="tpriceb">{{ $tour['price'] }} <small data-i18n="tours.per_group">/ grupo</small></div>
            <button class="btn btn-teal btn-sm" onclick="openTourModal('{{ addslashes($tour['name']) }}')" data-i18n="tours.book">Reservar</button>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- GALLERY --}}
<section class="section" id="galeria">
  <div class="container">
    <div class="tc rv" style="margin-bottom:52px">
      <div class="chip" data-i18n="gallery.chip">Galería de Viajes</div>
      <h2 class="display" data-i18n-html="gallery.title_html">Los Cabos en <em>Imágenes</em></h2>
      <p class="lead" data-i18n="gallery.lead">Un enclave que ofrece el equilibrio perfecto entre cultura, biodiversidad y modernidad.</p>
    </div>
    <div class="gallery rv">
      @if(!empty($galleryImages))
        @foreach($galleryImages as $gi)
          <div class="gi" onclick="openLb(this)">
            <img src="{{ $gi['url'] }}" alt="{{ $gi['caption'] }}">
            <div class="gi-ov"><span class="gi-cap">{{ $gi['caption'] }}</span></div>
          </div>
        @endforeach
      @else
        <div class="gi" onclick="openLb(this)"><img src="https://images.unsplash.com/photo-1512813195386-6cf811ad3542?w=1000&q=85" alt="Cabo"><div class="gi-ov"><span class="gi-cap">Los Cabos</span></div></div>
        <div class="gi" onclick="openLb(this)"><img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=700&q=85" alt="Traslado"><div class="gi-ov"><span class="gi-cap">Traslados Privados</span></div></div>
        <div class="gi" onclick="openLb(this)"><img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=700&q=85" alt="Auto"><div class="gi-ov"><span class="gi-cap">Flota de Lujo</span></div></div>
        <div class="gi" onclick="openLb(this)"><img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=700&q=85" alt="Playa"><div class="gi-ov"><span class="gi-cap">Paisajes Únicos</span></div></div>
        <div class="gi" onclick="openLb(this)"><img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1000&q=85" alt="Hotel"><div class="gi-ov"><span class="gi-cap">Destinos Premium</span></div></div>
      @endif
    </div>
  </div>
</section>

{{-- EXPERIENCE --}}
<section id="exp" class="s-dark">
  <div class="exp-img rv">
    <img src="{{ $sectionImages['airport_main'] }}" alt="Experiencia">
  </div>
  <div class="rv d2">
    <div class="chip inv" data-i18n="exp.chip">Servicio Aeropuerto</div>
    <h2 class="display inv" data-i18n-html="exp.title_html">Del aeropuerto<br>a su <em>hotel</em></h2>
    <p class="lead inv" data-i18n="exp.lead">Desde el primer minuto de su llegada, nuestro equipo estará esperándole con un letrero con su nombre.</p>
    <div class="exp-q" data-i18n="exp.quote">"Ven a Los Cabos y déjanos la conducción a nosotros"</div>
    <button class="btn btn-white" onclick="openW(event)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="16" height="16"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <span data-i18n="exp.btn">Reservar Traslado</span>
    </button>
  </div>
</section>

{{-- CONTACT --}}
<section class="section s-alt" id="contacto">
  <div class="container">
    <div class="tc rv" style="margin-bottom:52px">
      <div class="chip" data-i18n="contact.chip">Contáctenos</div>
      <h2 class="display" data-i18n-html="contact.title_html">Estamos aquí <em>para usted</em></h2>
    </div>
    <div class="contact-grid">
      <div class="rv">
        <div class="ci"><div class="ci-i">📍</div><div><div class="ci-l" data-i18n="contact.address">Dirección</div><div class="ci-v">{!! nl2br(e($siteSettings['address'] ?? 'Calle Huanacastle Esq. Eucalipto Mza 70 lte 1, Col. Las Veredas, CP 23436, San José del Cabo, BCS')) !!}</div></div></div>
        <div class="ci"><div class="ci-i">📞</div><div><div class="ci-l" data-i18n="contact.phone">Teléfono</div><div class="ci-v">{{ $siteSettings['phone_primary'] ?? '(+52) 333 303 4455' }}@if(!empty($siteSettings['phone_secondary']))<br>{{ $siteSettings['phone_secondary'] }}@endif</div></div></div>
        <div class="ci"><div class="ci-i">✉️</div><div><div class="ci-l" data-i18n="contact.email">Email</div><div class="ci-v">{{ $siteSettings['email'] ?? 'goldencabotransportation@gmail.com' }}</div></div></div>
        <div class="ci"><div class="ci-i">🌐</div><div><div class="ci-l" data-i18n="contact.website">Sitio Web</div><div class="ci-v">www.goldencabotransportation.com</div></div></div>
      </div>
      <div class="cf rv d2">
        <div class="cf-title" data-i18n="contact.form_title">Solicitar Información</div>
        <form id="contactForm">
          @csrf
          <div class="fg-row">
            <div class="fg"><label data-i18n="contact.first_name">Nombre</label><input type="text" name="first_name" placeholder="Su nombre" data-i18n-placeholder="contact.ph_first" required></div>
            <div class="fg"><label data-i18n="contact.last_name">Apellido</label><input type="text" name="last_name" placeholder="Su apellido" data-i18n-placeholder="contact.ph_last"></div>
          </div>
          <div class="fg"><label><span data-i18n="contact.email_label">Correo Electrónico</span> <span style="color:#E5414A">*</span></label><input type="email" name="email" placeholder="correo@ejemplo.com" data-i18n-placeholder="contact.ph_email" required maxlength="255"></div>
          <div class="fg">
            <label><span data-i18n="contact.phone_label">Teléfono</span> <span style="color:#E5414A">*</span></label>
            <div class="gc-pw" id="cf-phone-wrap">
              <select class="gc-lada" id="cf-lada" onchange="gcFmtPhone('cf-phone-wrap')">
                <option value="+52">MX +52</option>
                <option value="+1">US +1</option>
                <option value="+44">UK +44</option>
                <option value="+34">ES +34</option>
                <option value="+57">CO +57</option>
                <option value="+54">AR +54</option>
                <option value="+56">CL +56</option>
              </select>
              <input type="tel" class="gc-pvis" id="cf-phone-vis" placeholder="(___) ___ ____"
                     oninput="gcFmtPhone('cf-phone-wrap')" maxlength="14" autocomplete="tel-national">
              <input type="hidden" class="gc-phid" id="cf-phone-val" name="phone">
            </div>
            <div class="gc-perr" id="cf-phone-err" data-i18n="contact.phone_err">Ingrese un número válido de 10 dígitos.</div>
          </div>
          <div class="fg">
            <label data-i18n="contact.service">Servicio de Interés</label>
            <select name="service">
              <option value="" data-i18n="contact.svc_ph">Seleccione un servicio...</option>
              <option data-i18n="contact.svc_1">Traslado Aeropuerto → Hotel</option>
              <option data-i18n="contact.svc_2">Recorrido a La Paz</option>
              <option data-i18n="contact.svc_3">Recorrido a Todos Santos</option>
              <option data-i18n="contact.svc_4">Renta de Vehículo</option>
              <option data-i18n="contact.svc_5">Otro</option>
            </select>
          </div>
          <div class="fg"><label data-i18n="contact.message">Mensaje</label><textarea name="message" placeholder="Cuéntenos sobre su viaje..." data-i18n-placeholder="contact.ph_message" required></textarea></div>
          <button type="button" class="btn btn-teal" style="width:100%;justify-content:center" onclick="submitContact()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <span data-i18n="contact.send">Enviar Mensaje</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

{{-- FOOTER --}}
<footer>
  <div class="container">
    <div class="fg-grid">
      <div>
        <div class="logo">
          @if(!empty($siteSettings['logo']))
            <img src="{{ $siteSettings['logo'] }}" alt="{{ $siteSettings['name'] ?? 'Golden Cabo' }}"
                 style="height:40px;width:auto;object-fit:contain">
          @else
            <div class="logo-mark">✦</div>
            <div class="logo-text">
              <div class="ln"><span>Golden</span> Cabo</div>
              <div class="ls">Transportation</div>
            </div>
          @endif
        </div>
        <p class="ft-desc" data-i18n="footer.desc">Empresa de traslados privados y excursiones en Los Cabos, dedicada a brindarle la mejor experiencia desde 2014.</p>
      </div>
      <div class="fc"><h5 data-i18n="footer.nav">Navegación</h5><ul>
        <li><a href="#hero" data-i18n="footer.home">Inicio</a></li><li><a href="#acerca" data-i18n="footer.about">Acerca de</a></li>
        <li><a href="#recorridos" data-i18n="footer.tours">Recorridos</a></li><li><a href="#flota" data-i18n="footer.transport">Transporte</a></li>
        <li><a href="#galeria" data-i18n="footer.gallery">Galería</a></li><li><a href="#contacto" data-i18n="footer.contact">Contacto</a></li>
      </ul></div>
      <div class="fc"><h5 data-i18n="footer.services">Servicios</h5><ul>
        <li><a href="#" data-i18n="footer.svc_airport">Traslado Aeropuerto</a></li><li><a href="#" data-i18n="footer.svc_lapaz">Tour La Paz</a></li>
        <li><a href="#" data-i18n="footer.svc_todos">Tour Todos Santos</a></li><li><a href="#">Toyota Hiace</a></li>
        <li><a href="#">Chevrolet Suburban</a></li><li><a href="#">Sprinter VIP</a></li>
      </ul></div>
      <div class="fc"><h5 data-i18n="footer.zones">Zonas</h5><ul>
        <li><a href="#" data-i18n="footer.zone1">Zona 1 – San José del Cabo</a></li>
        <li><a href="#" data-i18n="footer.zone2">Zona 2 – Corredor Turístico</a></li>
        <li><a href="#" data-i18n="footer.zone3">Zona 3 – Cabo San Lucas</a></li>
        <li><a href="#" data-i18n="footer.zone4">Zona 4 – Lado del Pacífico</a></li>
      </ul></div>
    </div>
    <div class="fbot">
      <span class="fcopy" data-i18n="footer.copyright" data-i18n-year="{{ date('Y') }}">© {{ date('Y') }} CEUR Transportation S. de RL. de CV. · Todos los derechos reservados.</span>
      <span class="fquote" data-i18n="footer.quote">"Ven a Los Cabos y déjanos la conducción a nosotros"</span>
    </div>
  </div>
</footer>

{{-- BOOKING WIZARD --}}
@include('partials.wizard')

{{-- HOTELS MODAL --}}
<div class="hm-ov" id="hmOv">
  <div class="hm-box">
    <button class="hm-x" onclick="closeH()">✕</button>
    <div class="hm-title" id="hmT"></div>
    <div class="hm-zone" id="hmZ"></div>
    <ul class="h-list" id="hmL"></ul>
    <button class="btn btn-teal" style="width:100%;justify-content:center;margin-top:22px" onclick="closeH();openW(event)" data-i18n="hotels.book_btn">Reservar Traslado</button>
  </div>
</div>

{{-- LIGHTBOX --}}
<div class="lb" id="lb" onclick="closeLb()">
  <button class="lb-x">✕</button>
  <img id="lb-img" src="" alt="">
</div>

{{-- API config for JS --}}
<script>
window.CSRF_TOKEN  = '{{ csrf_token() }}';
window.BOOKING_API = '{{ route("bookings.store") }}';
window.CONTACT_API = '{{ route("contact.store") }}';
window.HOTELS_DB   = @json($hotels);
window.SERVER_NOW  = { hour: {{ now()->hour }}, minute: {{ now()->minute }} };
window.SITE_LOGO   = '{{ $siteSettings["logo"] ?? "" }}';
</script>

{{-- ═══ TOUR CONTACT MODAL ═══ --}}
<div id="tourModal" style="display:none;position:fixed;inset:0;z-index:9000;align-items:center;justify-content:center;padding:16px">
  {{-- Overlay --}}
  <div id="tourModalOv"
       onclick="closeTourModal()"
       style="position:absolute;inset:0;background:rgba(5,30,28,.55);backdrop-filter:blur(4px)">
  </div>

  {{-- Panel --}}
  <div id="tourModalBox"
       style="position:relative;background:#fff;border-radius:20px;width:100%;max-width:520px;
              max-height:90vh;overflow-y:auto;box-shadow:0 32px 80px rgba(0,0,0,.25);
              transform:translateY(32px) scale(.97);opacity:0;transition:all .3s cubic-bezier(.4,0,.2,1)">

    {{-- Header --}}
    <div style="padding:24px 28px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
      <div>
        <div style="font-size:.62rem;letter-spacing:2.5px;text-transform:uppercase;color:#009988;font-weight:700;margin-bottom:4px" data-i18n="tourmodal.header">Reservar Tour</div>
        <h3 id="tourModalTitle" style="font-family:'Playfair Display',serif;font-size:1.25rem;color:#051E1C;line-height:1.3"></h3>
      </div>
      <button onclick="closeTourModal()"
              style="width:32px;height:32px;border-radius:50%;border:none;background:#F3F4F6;
                     color:#6B7280;cursor:pointer;display:flex;align-items:center;justify-content:center;
                     flex-shrink:0;font-size:18px;transition:background .2s"
              onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
        ✕
      </button>
    </div>

    {{-- Form --}}
    <form id="tourContactForm" style="padding:20px 28px 28px">

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:.62rem;letter-spacing:1.5px;text-transform:uppercase;color:#1A6B67;font-weight:700" data-i18n="tourmodal.first_name">Nombre</label>
          <input type="text" name="first_name" placeholder="Su nombre" data-i18n-placeholder="tourmodal.ph_first" required
                 style="padding:10px 13px;border-radius:10px;border:1.5px solid #D4EDEB;background:#F2FBFA;
                        font-family:'Lato',sans-serif;font-size:.88rem;color:#051E1C;outline:none;
                        transition:border-color .2s"
                 onfocus="this.style.borderColor='#009988'" onblur="this.style.borderColor='#D4EDEB'">
        </div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:.62rem;letter-spacing:1.5px;text-transform:uppercase;color:#1A6B67;font-weight:700" data-i18n="tourmodal.last_name">Apellido</label>
          <input type="text" name="last_name" placeholder="Su apellido" data-i18n-placeholder="tourmodal.ph_last"
                 style="padding:10px 13px;border-radius:10px;border:1.5px solid #D4EDEB;background:#F2FBFA;
                        font-family:'Lato',sans-serif;font-size:.88rem;color:#051E1C;outline:none;
                        transition:border-color .2s"
                 onfocus="this.style.borderColor='#009988'" onblur="this.style.borderColor='#D4EDEB'">
        </div>
      </div>

      <div style="margin-top:14px;display:flex;flex-direction:column;gap:5px">
        <label style="font-size:.62rem;letter-spacing:1.5px;text-transform:uppercase;color:#1A6B67;font-weight:700"><span data-i18n="tourmodal.email">Correo Electrónico</span> <span style="color:#E5414A">*</span></label>
        <input type="email" id="tm-email" name="email" placeholder="correo@ejemplo.com" data-i18n-placeholder="tourmodal.ph_email" required maxlength="255"
               style="padding:10px 13px;border-radius:10px;border:1.5px solid #D4EDEB;background:#F2FBFA;
                      font-family:'Lato',sans-serif;font-size:.88rem;color:#051E1C;outline:none;
                      transition:border-color .2s"
               onfocus="this.style.borderColor='#009988'" onblur="this.style.borderColor='#D4EDEB'">
      </div>

      <div style="margin-top:14px;display:flex;flex-direction:column;gap:5px">
        <label style="font-size:.62rem;letter-spacing:1.5px;text-transform:uppercase;color:#1A6B67;font-weight:700"><span data-i18n="tourmodal.phone">Teléfono</span> <span style="color:#E5414A">*</span></label>
        <div class="gc-pw" id="tm-phone-wrap" style="border-color:#D4EDEB;background:#F2FBFA;border-radius:10px">
          <select class="gc-lada" id="tm-lada" onchange="gcFmtPhone('tm-phone-wrap')"
                  style="font-family:'Lato',sans-serif;border-right-color:#D4EDEB">
            <option value="+52">MX +52</option>
            <option value="+1">US +1</option>
            <option value="+44">UK +44</option>
            <option value="+34">ES +34</option>
            <option value="+57">CO +57</option>
            <option value="+54">AR +54</option>
            <option value="+56">CL +56</option>
          </select>
          <input type="tel" class="gc-pvis" id="tm-phone-vis" placeholder="(___) ___ ____"
                 oninput="gcFmtPhone('tm-phone-wrap')" maxlength="14"
                 style="font-family:'Lato',sans-serif;padding:10px 13px">
          <input type="hidden" class="gc-phid" id="tm-phone-val" name="phone">
        </div>
        <div id="tm-phone-err" style="display:none;font-size:.72rem;color:#E5414A;margin-top:4px" data-i18n="tourmodal.phone_err">Ingrese un número válido de 10 dígitos.</div>
      </div>

      <div style="margin-top:14px;display:flex;flex-direction:column;gap:5px">
        <label style="font-size:.62rem;letter-spacing:1.5px;text-transform:uppercase;color:#1A6B67;font-weight:700" data-i18n="tourmodal.service">Servicio de Interés</label>
        <select name="service" id="tourModalService"
                style="padding:10px 13px;border-radius:10px;border:1.5px solid #D4EDEB;background:#F2FBFA;
                       font-family:'Lato',sans-serif;font-size:.88rem;color:#051E1C;outline:none;
                       transition:border-color .2s;cursor:pointer"
                onfocus="this.style.borderColor='#009988'" onblur="this.style.borderColor='#D4EDEB'">
          <option value="" data-i18n="tourmodal.svc_ph">Seleccione un servicio...</option>
          <option data-i18n="tourmodal.svc_1">Traslado Aeropuerto → Hotel</option>
          <option data-i18n="tourmodal.svc_2">Recorrido a La Paz</option>
          <option data-i18n="tourmodal.svc_3">Recorrido a Todos Santos</option>
          <option data-i18n="tourmodal.svc_4">Renta de Vehículo</option>
          <option data-i18n="tourmodal.svc_5">Otro</option>
        </select>
      </div>

      <div style="margin-top:14px;display:flex;flex-direction:column;gap:5px">
        <label style="font-size:.62rem;letter-spacing:1.5px;text-transform:uppercase;color:#1A6B67;font-weight:700" data-i18n="tourmodal.message">Mensaje</label>
        <textarea name="message" placeholder="Cuéntenos sobre su viaje, fechas, número de personas..." data-i18n-placeholder="tourmodal.message_ph"
                  rows="3"
                  style="padding:10px 13px;border-radius:10px;border:1.5px solid #D4EDEB;background:#F2FBFA;
                         font-family:'Lato',sans-serif;font-size:.88rem;color:#051E1C;outline:none;
                         resize:vertical;transition:border-color .2s"
                  onfocus="this.style.borderColor='#009988'" onblur="this.style.borderColor='#D4EDEB'"></textarea>
      </div>

      {{-- Success message (hidden by default) --}}
      <div id="tourModalSuccess"
           style="display:none;margin-top:16px;padding:14px 16px;background:#D1FAE5;border-radius:10px;
                  color:#065F46;font-size:.85rem;font-family:'Josefin Sans',sans-serif;
                  display:none;align-items:center;gap:8px">
        <span data-i18n="tourmodal.success">✅ ¡Gracias! Nos comunicaremos con usted pronto.</span>
      </div>

      <button type="button" id="tourModalBtn" onclick="submitTourContact()"
              style="margin-top:20px;width:100%;padding:13px;border-radius:100px;border:none;
                     background:linear-gradient(135deg,#00AC97,#009988);color:#fff;
                     font-family:'Josefin Sans',sans-serif;font-size:.76rem;font-weight:700;
                     letter-spacing:2px;text-transform:uppercase;cursor:pointer;
                     display:flex;align-items:center;justify-content:center;gap:8px;
                     transition:opacity .2s">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
        <span data-i18n="tourmodal.send">Enviar Solicitud</span>
      </button>

    </form>
  </div>
</div>

@push('scripts')
<script>
/* ── Tour Modal ── */
function openTourModal(tourName) {
  const modal = document.getElementById('tourModal');
  const box   = document.getElementById('tourModalBox');
  const title = document.getElementById('tourModalTitle');
  const srv   = document.getElementById('tourModalService');

  title.textContent = tourName;

  // Pre-select the matching service option if found
  Array.from(srv.options).forEach(opt => {
    opt.selected = opt.text.toLowerCase().includes(tourName.toLowerCase().split(' ')[0]);
  });

  // Reset form & hide success
  document.getElementById('tourContactForm').reset();
  document.getElementById('tourModalSuccess').style.display = 'none';
  document.getElementById('tourModalBtn').style.display     = 'flex';
  document.getElementById('tourModalBtn').style.opacity     = '1';
  document.getElementById('tourModalBtn').disabled          = false;
  document.getElementById('tourModalBtn').querySelector('span[data-i18n]') ?
    document.getElementById('tourModalBtn').querySelector('span[data-i18n]').textContent = (window.GC_T && window.GC_T['tourmodal.send']) || 'Enviar Solicitud' :
    (document.getElementById('tourModalBtn').textContent = (window.GC_T && window.GC_T['tourmodal.send']) || 'Enviar Solicitud');
  var tmWrap = document.getElementById('tm-phone-wrap');
  var tmVis  = document.getElementById('tm-phone-vis');
  var tmHid  = document.getElementById('tm-phone-val');
  var tmErr  = document.getElementById('tm-phone-err');
  if (tmWrap) tmWrap.classList.remove('err');
  if (tmVis)  tmVis.value = '';
  if (tmHid)  tmHid.value = '';
  if (tmErr)  tmErr.style.display = 'none';
  title.textContent = tourName; // re-set after reset

  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';

  requestAnimationFrame(() => {
    box.style.opacity   = '1';
    box.style.transform = 'translateY(0) scale(1)';
  });
}

function closeTourModal() {
  const modal = document.getElementById('tourModal');
  const box   = document.getElementById('tourModalBox');
  box.style.opacity   = '0';
  box.style.transform = 'translateY(32px) scale(.97)';
  setTimeout(() => {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }, 280);
}

async function submitTourContact() {
  const form = document.getElementById('tourContactForm');
  const btn  = document.getElementById('tourModalBtn');

  const firstNameEl = form.querySelector('[name="first_name"]');
  const emailEl     = document.getElementById('tm-email');
  const tmWrap      = document.getElementById('tm-phone-wrap');
  const tmVis       = document.getElementById('tm-phone-vis');
  const tmHid       = document.getElementById('tm-phone-val');
  const tmErr       = document.getElementById('tm-phone-err');

  const firstName = firstNameEl.value.trim();
  const email     = emailEl ? emailEl.value.trim() : form.querySelector('[name="email"]').value.trim();

  let valid = true;

  // Name
  firstNameEl.style.borderColor = firstName ? '#D4EDEB' : '#EF4444';
  if (!firstName) valid = false;

  // Email
  if (!gcValidEmail(email)) {
    if (emailEl) emailEl.style.borderColor = '#EF4444';
    valid = false;
  } else {
    if (emailEl) emailEl.style.borderColor = '#D4EDEB';
  }

  // Phone
  const phoneDigits = tmVis ? tmVis.value.replace(/\D/g, '') : '';
  if (phoneDigits.length !== 10) {
    if (tmWrap) tmWrap.classList.add('err');
    if (tmErr)  tmErr.style.display = 'block';
    valid = false;
  } else {
    if (tmWrap) tmWrap.classList.remove('err');
    if (tmErr)  tmErr.style.display = 'none';
  }

  if (!valid) return;

  // Rate limit
  const cleanPhone = tmHid ? tmHid.value : phoneDigits;
  if (!gcCheckRateLimit(cleanPhone)) {
    alert((window.GC_T && window.GC_T['tourmodal.rate_limit']) || 'Ha superado el límite de 5 intentos por hora para este número. Por favor intente más tarde.');
    return;
  }

  btn.textContent  = (window.GC_T && window.GC_T['tourmodal.sending']) || 'Enviando...';
  btn.style.opacity = '.7';
  btn.disabled     = true;

  const payload = {
    first_name: firstName,
    last_name:  form.querySelector('[name="last_name"]').value.trim(),
    email,
    phone:      tmHid ? tmHid.value : phoneDigits,
    service:    form.querySelector('[name="service"]').value,
    message:    form.querySelector('[name="message"]').value.trim(),
  };

  try {
    const resp = await fetch(window.CONTACT_API, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': window.CSRF_TOKEN,
      },
      body: JSON.stringify(payload),
    });
    const data = await resp.json();
    if (data.success) {
      btn.style.display = 'none';
      const success = document.getElementById('tourModalSuccess');
      success.style.display = 'flex';
      setTimeout(() => closeTourModal(), 3200);
    } else {
      btn.textContent  = (window.GC_T && window.GC_T['tourmodal.send']) || 'Enviar Solicitud';
      btn.style.opacity = '1';
      btn.disabled     = false;
      alert((window.GC_T && window.GC_T['tourmodal.error']) || 'Error al enviar. Por favor intente de nuevo.');
    }
  } catch (e) {
    btn.textContent  = (window.GC_T && window.GC_T['tourmodal.send']) || 'Enviar Solicitud';
    btn.style.opacity = '1';
    btn.disabled     = false;
    alert((window.GC_T && window.GC_T['tourmodal.conn_err']) || 'Error de conexión. Verifique su internet.');
  }
}

// Close on ESC key
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeTourModal();
});
</script>
@endpush

@endsection

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="session-timeout" content="{{ max(5, min(480, (int) \App\Models\SiteSetting::get('session_timeout_minutes', '15'))) }}">
<title>@yield('page-title', 'Dashboard') — Golden Cabo Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
<style>
/* ── Reset & Base ────────────────────────────── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --teal:    #009988;
  --teal-dk: #007B6D;
  --teal-lt: #E0F5F3;
  --teal-md: #C2EDE9;
  --dark:    #0D2B29;
  --dark2:   #163330;
  --txt:     #1A2E2D;
  --txt2:    #5A7372;
  --border:  #E4ECEB;
  --bg:      #F3F7F7;
  --white:   #ffffff;
  --danger:  #EF4444;
  --warn:    #F59E0B;
  --success: #10B981;
  --info:    #3B82F6;
  --radius:  12px;
  --radius-sm:8px;
  --shadow:  0 1px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
  --shadow-md:0 4px 24px rgba(0,0,0,.09);
  --sb-w:    256px;
}
html,body{height:100%}
body{
  font-family:'Inter',sans-serif;
  background:var(--bg);
  color:var(--txt);
  display:flex;
  min-height:100vh;
  font-size:14px;
  line-height:1.5;
}
a{color:inherit;text-decoration:none}
button{font-family:inherit}

/* ── Sidebar ─────────────────────────────────── */
.sidebar{
  width:var(--sb-w);
  min-height:100vh;
  background:var(--dark);
  display:flex;
  flex-direction:column;
  flex-shrink:0;
  position:sticky;
  top:0;
  height:100vh;
  overflow-y:auto;
  scrollbar-width:none;
}
.sidebar::-webkit-scrollbar{display:none}

/* Brand */
.sb-brand{
  padding:24px 20px 20px;
  border-bottom:1px solid rgba(255,255,255,.07);
  display:flex;
  align-items:center;
  gap:12px;
}
.sb-logo{
  width:40px;height:40px;border-radius:11px;
  background:linear-gradient(135deg,var(--teal),var(--teal-dk));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:18px;font-weight:700;flex-shrink:0;
  box-shadow:0 4px 12px rgba(0,153,136,.35);
}
.sb-brand-name{
  font-family:'Playfair Display',serif;
  color:#fff;font-size:1rem;line-height:1.2;
}
.sb-brand-sub{
  font-size:.6rem;letter-spacing:2.5px;text-transform:uppercase;
  color:rgba(255,255,255,.28);margin-top:2px;
}

/* Nav sections */
.sb-nav{flex:1;padding:16px 0 8px}
.sb-section-label{
  font-size:.58rem;letter-spacing:2.5px;text-transform:uppercase;
  color:rgba(255,255,255,.22);font-weight:600;
  padding:12px 20px 6px;
}
.sb-item{
  display:flex;align-items:center;gap:11px;
  padding:9px 20px;
  color:rgba(255,255,255,.48);
  font-size:.8rem;font-weight:500;
  transition:all .18s;
  border-left:3px solid transparent;
  cursor:pointer;position:relative;
}
.sb-item:hover{
  color:rgba(255,255,255,.85);
  background:rgba(255,255,255,.05);
}
.sb-item.active{
  color:#fff;
  background:rgba(0,153,136,.18);
  border-left-color:var(--teal);
}
.sb-item.active .sb-icon{color:var(--teal)}
.sb-icon{
  width:32px;height:32px;border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  background:rgba(255,255,255,.06);
  flex-shrink:0;
  transition:background .18s;
}
.sb-item:hover .sb-icon,.sb-item.active .sb-icon{background:rgba(0,153,136,.2)}
.sb-badge{
  margin-left:auto;
  background:var(--danger);color:#fff;
  font-size:.58rem;font-weight:700;letter-spacing:.5px;
  padding:2px 7px;border-radius:100px;min-width:20px;text-align:center;
}
.sb-divider{height:1px;background:rgba(255,255,255,.06);margin:8px 16px}

/* Profile footer */
.sb-footer{
  padding:14px 16px;
  border-top:1px solid rgba(255,255,255,.07);
  margin-top:auto;
}
.sb-profile-btn{
  display:flex;align-items:center;gap:10px;
  padding:10px 12px;border-radius:10px;
  transition:background .18s;cursor:pointer;
}
.sb-profile-btn:hover{background:rgba(255,255,255,.06)}
.sb-avatar{
  width:34px;height:34px;border-radius:50%;
  background:linear-gradient(135deg,var(--teal),var(--teal-dk));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.8rem;font-weight:700;flex-shrink:0;
  text-transform:uppercase;
}
.sb-profile-info{flex:1;min-width:0}
.sb-profile-name{color:#fff;font-size:.8rem;font-weight:600;truncate}
.sb-profile-role{color:rgba(255,255,255,.32);font-size:.62rem;margin-top:1px}
.sb-profile-btn svg{color:rgba(255,255,255,.3);flex-shrink:0}
.sb-logout{
  display:flex;align-items:center;gap:10px;
  padding:8px 12px;border-radius:8px;
  color:rgba(255,255,255,.32);font-size:.75rem;
  margin-top:4px;cursor:pointer;background:none;border:none;
  width:100%;transition:all .18s;
}
.sb-logout:hover{color:var(--danger);background:rgba(239,68,68,.08)}

/* ── Main ─────────────────────────────────────── */
.main{
  flex:1;
  display:flex;
  flex-direction:column;
  overflow:hidden;
  min-width:0;
}

/* Topbar */
.topbar{
  background:var(--white);
  padding:0 28px;
  height:60px;
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  flex-shrink:0;
  box-shadow:0 1px 0 rgba(0,0,0,.04);
}
.topbar-left{display:flex;align-items:center;gap:8px}
.topbar-breadcrumb{
  font-size:.72rem;color:var(--txt2);
  display:flex;align-items:center;gap:6px;
}
.topbar-breadcrumb span{color:var(--txt2)}
.topbar-title{
  font-size:1rem;font-weight:600;color:var(--txt);
}
.topbar-right{display:flex;align-items:center;gap:10px}
.topbar-user{
  display:flex;align-items:center;gap:8px;
  padding:6px 10px;border-radius:8px;
  background:var(--bg);border:1px solid var(--border);
}
.topbar-user-avatar{
  width:26px;height:26px;border-radius:50%;
  background:linear-gradient(135deg,var(--teal),var(--teal-dk));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.6rem;font-weight:700;text-transform:uppercase;
}
.topbar-user-name{font-size:.75rem;font-weight:500;color:var(--txt)}

/* Page body */
.page-body{
  flex:1;
  padding:24px 28px;
  overflow-y:auto;
}
.page-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:20px;flex-wrap:wrap;gap:12px;
}
.page-header h1{
  font-size:1.3rem;font-weight:700;color:var(--txt);
}
.page-header p{
  font-size:.8rem;color:var(--txt2);margin-top:2px;
}

/* ── Cards ───────────────────────────────────── */
.card{
  background:var(--white);
  border-radius:var(--radius);
  padding:22px 24px;
  box-shadow:var(--shadow);
  border:1px solid var(--border);
  margin-bottom:20px;
}
.card-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:18px;
}
.card-title{
  font-size:.85rem;font-weight:700;color:var(--txt);
  display:flex;align-items:center;gap:8px;
}
.card-title .ct-icon{
  width:28px;height:28px;border-radius:7px;
  background:var(--teal-lt);
  display:flex;align-items:center;justify-content:center;
}
.card-title .ct-icon svg{color:var(--teal)}

/* ── Stat Cards ──────────────────────────────── */
.stats-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
  gap:16px;
  margin-bottom:22px;
}
.stat-card{
  background:var(--white);
  border-radius:var(--radius);
  padding:20px 22px;
  box-shadow:var(--shadow);
  border:1px solid var(--border);
  display:flex;
  align-items:center;
  gap:16px;
}
.stat-card.accent{background:linear-gradient(135deg,var(--teal),var(--teal-dk));border-color:transparent}
.stat-card.accent .stat-num,.stat-card.accent .stat-lbl{color:#fff}
.stat-card.accent .stat-icon-wrap{background:rgba(255,255,255,.18)}
.stat-icon-wrap{
  width:46px;height:46px;border-radius:12px;
  background:var(--teal-lt);
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
}
.stat-icon-wrap svg{color:var(--teal)}
.stat-num{font-size:1.75rem;font-weight:700;color:var(--txt);line-height:1}
.stat-lbl{font-size:.68rem;color:var(--txt2);margin-top:3px;text-transform:uppercase;letter-spacing:.8px;font-weight:500}

/* ── Table ───────────────────────────────────── */
.table-wrap{overflow-x:auto;margin:-1px}
table{width:100%;border-collapse:collapse;font-size:.82rem}
thead th{
  background:#FAFCFC;
  padding:10px 14px;
  text-align:left;
  font-size:.65rem;font-weight:700;
  letter-spacing:1.5px;text-transform:uppercase;
  color:var(--txt2);
  border-bottom:1px solid var(--border);
  white-space:nowrap;
}
thead th:first-child{border-radius:var(--radius-sm) 0 0 0}
thead th:last-child{border-radius:0 var(--radius-sm) 0 0}
tbody td{
  padding:12px 14px;
  border-bottom:1px solid #F0F4F4;
  color:var(--txt);
  vertical-align:middle;
}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover td{background:#FAFCFC}

/* ── Badges ──────────────────────────────────── */
.badge{
  display:inline-flex;align-items:center;gap:4px;
  padding:3px 10px;border-radius:100px;
  font-size:.65rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;
  white-space:nowrap;
}
.badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;opacity:.6}
.badge-green{background:#D1FAE5;color:#065F46}
.badge-yellow{background:#FEF3C7;color:#92400E}
.badge-red{background:#FEE2E2;color:#991B1B}
.badge-blue{background:#DBEAFE;color:#1E40AF}
.badge-gray{background:#F3F4F6;color:#4B5563}
.badge-teal{background:var(--teal-lt);color:var(--teal-dk)}

/* ── Buttons ─────────────────────────────────── */
.btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:8px 16px;border-radius:8px;
  font-size:.75rem;font-weight:600;
  cursor:pointer;border:none;
  text-decoration:none;transition:all .18s;
  white-space:nowrap;
}
.btn svg{flex-shrink:0}
.btn-primary{background:var(--teal);color:#fff}
.btn-primary:hover{background:var(--teal-dk)}
.btn-ghost{background:transparent;border:1.5px solid var(--border);color:var(--txt2)}
.btn-ghost:hover{border-color:var(--teal);color:var(--teal)}
.btn-danger{background:#FEE2E2;color:#991B1B;border:none}
.btn-danger:hover{background:#FECACA}
.btn-sm{padding:5px 12px;font-size:.7rem}
.btn-icon{padding:7px;border-radius:7px}

/* ── Forms ───────────────────────────────────── */
.fg{margin-bottom:16px}
.fg label{
  display:block;
  font-size:.68rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;
  color:var(--txt2);margin-bottom:6px;
}
.fg input,.fg select,.fg textarea{
  width:100%;padding:9px 13px;
  border-radius:var(--radius-sm);
  border:1.5px solid var(--border);
  background:var(--white);
  font-family:'Inter',sans-serif;font-size:.85rem;
  color:var(--txt);outline:none;
  transition:border-color .18s,box-shadow .18s;
}
.fg input:focus,.fg select:focus,.fg textarea:focus{
  border-color:var(--teal);
  box-shadow:0 0 0 3px rgba(0,153,136,.1);
}
.fg textarea{height:88px;resize:vertical}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-hint{font-size:.72rem;color:var(--txt2);margin-top:4px}
.form-section{margin-bottom:28px}
.form-section-title{
  font-size:.8rem;font-weight:700;color:var(--txt);
  padding-bottom:10px;border-bottom:1px solid var(--border);
  margin-bottom:18px;
  display:flex;align-items:center;gap:8px;
}
.form-section-title svg{color:var(--teal)}

/* ── Alerts ──────────────────────────────────── */
.alert{
  padding:12px 16px;border-radius:var(--radius-sm);
  margin-bottom:18px;font-size:.82rem;
  display:flex;align-items:center;gap:10px;
}
.alert-success{background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0}
.alert-error{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA}

/* ── Search bar ──────────────────────────────── */
.search-bar{
  display:flex;align-items:center;gap:8px;
  flex-wrap:wrap;
}
.search-input-wrap{
  position:relative;flex:1;min-width:220px;
}
.search-input-wrap svg{
  position:absolute;left:11px;top:50%;transform:translateY(-50%);
  color:var(--txt2);pointer-events:none;
}
.search-input-wrap input{
  padding-left:36px !important;
  width:100%;
}

/* ── Pagination ──────────────────────────────── */
.pagination-wrap{
  display:flex;justify-content:flex-end;
  gap:5px;margin-top:16px;align-items:center;
}
.pagination-wrap a,.pagination-wrap span{
  display:inline-flex;align-items:center;justify-content:center;
  min-width:32px;height:32px;padding:0 8px;
  border-radius:7px;font-size:.73rem;font-weight:600;
  text-decoration:none;color:var(--txt2);
  background:var(--white);border:1.5px solid var(--border);
  transition:all .18s;
}
.pagination-wrap .active span{
  background:var(--teal);color:#fff;border-color:var(--teal);
}
.pagination-wrap a:hover{border-color:var(--teal);color:var(--teal)}

/* ── Contact message cards ───────────────────── */
.msg-card{
  background:var(--white);
  border-radius:var(--radius);
  padding:18px 22px;
  box-shadow:var(--shadow);
  border:1px solid var(--border);
  border-left:4px solid var(--border);
  margin-bottom:12px;
  transition:border-color .18s;
}
.msg-card.unread{border-left-color:var(--teal)}
.msg-card-header{
  display:flex;justify-content:space-between;align-items:flex-start;
  margin-bottom:10px;flex-wrap:wrap;gap:8px;
}
.msg-sender{font-size:.88rem;font-weight:600;color:var(--txt)}
.msg-email{font-size:.78rem;color:var(--teal);margin-top:2px}
.msg-phone{font-size:.78rem;color:var(--txt2);margin-top:1px}
.msg-time{font-size:.68rem;color:var(--txt2)}
.msg-service{
  display:inline-block;font-size:.62rem;font-weight:700;letter-spacing:1.5px;
  text-transform:uppercase;color:var(--teal);
  background:var(--teal-lt);padding:3px 9px;border-radius:100px;margin-bottom:10px;
}
.msg-text{font-size:.85rem;color:var(--txt2);line-height:1.7}

/* ── Profile ─────────────────────────────────── */
.profile-avatar-lg{
  width:80px;height:80px;border-radius:50%;
  background:linear-gradient(135deg,var(--teal),var(--teal-dk));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:2rem;font-weight:700;text-transform:uppercase;
  box-shadow:0 4px 20px rgba(0,153,136,.3);
  flex-shrink:0;
}
.profile-header{
  display:flex;align-items:center;gap:20px;
  padding:20px 24px;
  border-bottom:1px solid var(--border);
  margin-bottom:24px;
}
.profile-header-info h2{font-size:1.1rem;font-weight:700;color:var(--txt)}
.profile-header-info p{font-size:.78rem;color:var(--txt2);margin-top:3px}
.security-icon{
  width:42px;height:42px;border-radius:10px;
  background:var(--teal-lt);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}

/* ── Detail rows ─────────────────────────────── */
.detail-row{
  display:flex;padding:10px 0;
  border-bottom:1px solid #F0F4F4;
  align-items:baseline;
}
.detail-row:last-child{border-bottom:none}
.detail-label{
  width:150px;flex-shrink:0;
  font-size:.68rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
  color:var(--txt2);
}
.detail-value{font-size:.85rem;color:var(--txt);flex:1}

/* ── Action chips ────────────────────────────── */
.chip{
  display:inline-flex;align-items:center;gap:5px;
  padding:4px 12px;border-radius:100px;font-size:.7rem;font-weight:600;
  border:1.5px solid var(--border);color:var(--txt2);
  background:var(--white);transition:all .18s;cursor:pointer;
}
.chip:hover{border-color:var(--teal);color:var(--teal)}
</style>
</head>
<body>

{{-- ════════════════ SIDEBAR ════════════════ --}}
<aside class="sidebar">

  {{-- Brand --}}
  <div class="sb-brand">
    <div class="sb-logo">✦</div>
    <div>
      <div class="sb-brand-name">Golden Cabo</div>
      <div class="sb-brand-sub">Administración</div>
    </div>
  </div>

  {{-- Navigation --}}
  <nav class="sb-nav">

    {{-- Principal --}}
    <div class="sb-section-label">Principal</div>
    <a href="{{ route('admin.dashboard') }}"
       class="sb-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
          <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
      </span>
      Dashboard
    </a>

    {{-- Gestión --}}
    @if(Auth::user()->hasPermission('bookings') || Auth::user()->hasPermission('contacts'))
    <div class="sb-section-label" style="margin-top:8px">Gestión</div>
    @endif

    @if(Auth::user()->hasPermission('bookings'))
    <a href="{{ route('admin.bookings') }}"
       class="sb-item {{ request()->routeIs('admin.booking*') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
        </svg>
      </span>
      Reservaciones
    </a>
    @endif

    @if(Auth::user()->hasPermission('contacts'))
    <a href="{{ route('admin.contacts') }}"
       class="sb-item {{ request()->routeIs('admin.contacts') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
      </span>
      Mensajes
      @php $unread = \App\Models\Contact::where('read', false)->count(); @endphp
      @if($unread > 0)
        <span class="sb-badge">{{ $unread }}</span>
      @endif
    </a>
    @endif

    {{-- Usuarios (solo admins) --}}
    @if(Auth::user()->isAdmin())
    <a href="{{ route('admin.users.index') }}"
       class="sb-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </span>
      Usuarios
    </a>
    @endif

    <div class="sb-divider"></div>

    {{-- CMS --}}
    @if(Auth::user()->hasPermission('cms'))
    <div class="sb-section-label" style="margin-top:8px">CMS</div>
    @if(Auth::user()->hasPermission('vehicles'))
    <a href="{{ route('admin.cms.vehicles.index') }}"
       class="sb-item {{ request()->routeIs('admin.cms.vehicles*') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
      </span>
      Vehículos
    </a>

    @endif
    @if(Auth::user()->hasPermission('tours'))
    <a href="{{ route('admin.cms.tours.index') }}"
       class="sb-item {{ request()->routeIs('admin.cms.tours*') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
        </svg>
      </span>
      Tours
    </a>
    @endif

    @if(Auth::user()->hasPermission('zones'))
    <a href="{{ route('admin.cms.zones.index') }}"
       class="sb-item {{ request()->routeIs('admin.cms.zones*') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </span>
      Zonas & Hoteles
    </a>
    @endif

    @if(Auth::user()->hasPermission('carousel'))
    <a href="{{ route('admin.cms.carousel.index') }}"
       class="sb-item {{ request()->routeIs('admin.cms.carousel*') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3h-8"/><path d="M8 3l-4 4"/><path d="M16 3l4 4"/>
        </svg>
      </span>
      Carrusel Inicio
    </a>
    @endif

    @if(Auth::user()->hasPermission('gallery'))
    <a href="{{ route('admin.cms.gallery.index') }}"
       class="sb-item {{ request()->routeIs('admin.cms.gallery*') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
        </svg>
      </span>
      Galería de Viajes
    </a>
    @endif

    @if(Auth::user()->hasPermission('sections'))
    <a href="{{ route('admin.cms.section-images.index') }}"
       class="sb-item {{ request()->routeIs('admin.cms.section-images*') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="2" width="9" height="9" rx="1"/><rect x="13" y="2" width="9" height="9" rx="1"/>
          <rect x="2" y="13" width="9" height="9" rx="1"/><rect x="13" y="13" width="9" height="9" rx="1"/>
        </svg>
      </span>
      Imágenes Secciones
    </a>
    @endif
    @endif {{-- end hasPermission('cms') --}}

    <div class="sb-divider"></div>

    @if(Auth::user()->hasPermission('settings'))
    <div class="sb-section-label" style="margin-top:8px">Configuración</div>
    <a href="{{ route('admin.cms.settings.index') }}"
       class="sb-item {{ request()->routeIs('admin.cms.settings*') ? 'active' : '' }}">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
      </span>
      Config. Global
    </a>
    @endif

    <div class="sb-divider"></div>

    {{-- Externo --}}
    <a href="{{ route('home') }}" target="_blank" class="sb-item">
      <span class="sb-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/>
          <line x1="2" y1="12" x2="22" y2="12"/>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
      </span>
      Ver Sitio
      <span style="margin-left:auto;opacity:.35">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      </span>
    </a>

  </nav>


</aside>

{{-- ════════════════ MAIN ════════════════ --}}
<div class="main">

  {{-- Topbar --}}
  <div class="topbar">
    <div class="topbar-left">
      <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
    </div>
    <div class="topbar-right">
      <a href="{{ route('admin.profile') }}"
         style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;background:var(--bg);border:1px solid var(--border);text-decoration:none;transition:background .18s"
         onmouseover="this.style.background='var(--teal-lt)'" onmouseout="this.style.background='var(--bg)'">
        <div class="topbar-user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
        <div style="line-height:1.2">
          <div class="topbar-user-name">{{ Auth::user()->name }}</div>
          <div style="font-size:.62rem;color:var(--txt2)">{{ Auth::user()->isAdmin() ? 'Administrador' : 'Editor' }}</div>
        </div>
      </a>
      <button type="button"
              onclick="document.getElementById('_logout_form').submit()"
              style="display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--txt2);font-size:.75rem;font-weight:500;cursor:pointer;transition:all .18s"
              onmouseover="this.style.background='#FEE2E2';this.style.color='var(--danger)';this.style.borderColor='#FECACA'" onmouseout="this.style.background='var(--bg)';this.style.color='var(--txt2)';this.style.borderColor='var(--border)'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Salir
      </button>
    </div>
  </div>

  {{-- Alerts --}}
  <div class="page-body">
    @if(session('success'))
      <div class="alert alert-success">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
      </div>
    @endif
    @if($errors->any() && !$errors->hasBag('login'))
      <div class="alert alert-error" style="flex-direction:column;align-items:flex-start;gap:4px">
        <div style="display:flex;align-items:center;gap:8px;font-weight:600">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Corrige los siguientes errores:
        </div>
        <ul style="margin:4px 0 0 24px;font-size:.8rem">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @yield('content')
  </div>

</div>

{{-- Logout form (fuera del aside para evitar conflictos de DOM) --}}
<form id="_logout_form" method="POST" action="{{ route('logout') }}" style="display:none">
  @csrf
</form>

{{-- Session timeout warning modal --}}
<div id="_session_warn" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:32px;max-width:380px;width:calc(100% - 40px);box-shadow:var(--shadow-md);text-align:center">
    <div style="width:56px;height:56px;border-radius:50%;background:#FEF3C7;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <h3 style="font-size:1rem;font-weight:700;color:var(--txt);margin-bottom:8px">Sesión por expirar</h3>
    <p style="font-size:.85rem;color:var(--txt2);margin-bottom:6px">Tu sesión cerrará por inactividad en:</p>
    <div id="_session_countdown" style="font-size:2.5rem;font-weight:700;color:var(--warn);margin:12px 0">2:00</div>
    <p style="font-size:.78rem;color:var(--txt2);margin-bottom:20px">Haz clic en "Continuar" para mantener la sesión activa.</p>
    <div style="display:flex;gap:10px;justify-content:center">
      <button onclick="document.getElementById('_logout_form').submit()" class="btn btn-ghost">Cerrar sesión</button>
      <button onclick="keepAlive()" class="btn btn-primary">Continuar sesión</button>
    </div>
  </div>
</div>

<script>
(function () {
  const metaEl  = document.querySelector('meta[name="session-timeout"]');
  const minutes = metaEl ? parseInt(metaEl.content, 10) : 15;
  const totalMs = minutes * 60 * 1000;
  const warnMs  = Math.min(2 * 60 * 1000, totalMs * 0.2); // warn at 2 min or 20% remaining

  let warnTimer, logoutTimer, countdownInterval;

  function startTimers() {
    clearTimeout(warnTimer);
    clearTimeout(logoutTimer);
    clearInterval(countdownInterval);

    warnTimer = setTimeout(showWarning, totalMs - warnMs);
    logoutTimer = setTimeout(function () {
      document.getElementById('_logout_form').submit();
    }, totalMs);
  }

  function showWarning() {
    const modal = document.getElementById('_session_warn');
    modal.style.display = 'flex';
    let secs = Math.round(warnMs / 1000);
    updateCountdown(secs);
    countdownInterval = setInterval(function () {
      secs--;
      updateCountdown(Math.max(0, secs));
    }, 1000);
  }

  function updateCountdown(secs) {
    const m = String(Math.floor(secs / 60)).padStart(1, '0');
    const s = String(secs % 60).padStart(2, '0');
    document.getElementById('_session_countdown').textContent = m + ':' + s;
  }

  window.keepAlive = function () {
    document.getElementById('_session_warn').style.display = 'none';
    clearInterval(countdownInterval);
    // Ping server to reset last_activity
    fetch(window.location.href, { method: 'HEAD', credentials: 'same-origin' })
      .catch(function () {});
    startTimers();
  };

  startTimers();
})();
</script>

</body>
</html>

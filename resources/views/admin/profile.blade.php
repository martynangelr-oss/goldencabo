@extends('layouts.admin')
@section('page-title', 'Mi Perfil')
@section('content')

<div class="page-header">
  <div>
    <h1>Mi Perfil</h1>
    <p>Administra tu información personal y credenciales de acceso</p>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

  {{-- ── INFORMACIÓN PERSONAL ── --}}
  <div class="card">

    {{-- Avatar header --}}
    <div class="profile-header">
      <div class="profile-avatar-lg">{{ substr(Auth::user()->name, 0, 1) }}</div>
      <div class="profile-header-info">
        <h2>{{ Auth::user()->name }}</h2>
        <p>{{ Auth::user()->email }}</p>
        <span class="badge badge-teal" style="margin-top:6px">Administrador</span>
      </div>
    </div>

    <div class="form-section">
      <div class="form-section-title">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        Información Personal
      </div>

      <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf @method('PUT')

        <div class="fg">
          <label>Nombre completo</label>
          <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                 placeholder="Tu nombre" required>
          @error('name')
            <div class="form-hint" style="color:var(--danger)">{{ $message }}</div>
          @enderror
        </div>

        <div class="fg">
          <label>Correo electrónico</label>
          <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                 placeholder="tu@correo.com" required>
          @error('email')
            <div class="form-hint" style="color:var(--danger)">{{ $message }}</div>
          @enderror
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:4px">
          <button type="submit" class="btn btn-primary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>

  </div>

  {{-- ── SEGURIDAD ── --}}
  <div class="card">

    <div style="display:flex;align-items:center;gap:14px;padding-bottom:20px;border-bottom:1px solid var(--border);margin-bottom:24px">
      <div class="security-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
      </div>
      <div>
        <div style="font-size:.9rem;font-weight:700;color:var(--txt)">Seguridad</div>
        <div style="font-size:.75rem;color:var(--txt2);margin-top:2px">Actualiza tu contraseña de acceso</div>
      </div>
    </div>

    <form method="POST" action="{{ route('admin.profile.password') }}">
      @csrf @method('PUT')

      <div class="fg">
        <label>Contraseña actual</label>
        <div style="position:relative">
          <input type="password" name="current_password" id="pwd_current"
                 placeholder="••••••••" required autocomplete="current-password">
          <button type="button" onclick="togglePwd('pwd_current', this)"
                  style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--txt2);padding:4px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        @error('current_password')
          <div class="form-hint" style="color:var(--danger)">{{ $message }}</div>
        @enderror
      </div>

      <div class="fg">
        <label>Nueva contraseña</label>
        <div style="position:relative">
          <input type="password" name="password" id="pwd_new"
                 placeholder="Mínimo 8 caracteres" required autocomplete="new-password">
          <button type="button" onclick="togglePwd('pwd_new', this)"
                  style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--txt2);padding:4px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        @error('password')
          <div class="form-hint" style="color:var(--danger)">{{ $message }}</div>
        @enderror
      </div>

      <div class="fg">
        <label>Confirmar nueva contraseña</label>
        <div style="position:relative">
          <input type="password" name="password_confirmation" id="pwd_confirm"
                 placeholder="Repite la contraseña" required autocomplete="new-password">
          <button type="button" onclick="togglePwd('pwd_confirm', this)"
                  style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--txt2);padding:4px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        @error('password_confirmation')
          <div class="form-hint" style="color:var(--danger)">{{ $message }}</div>
        @enderror
      </div>

      {{-- Password strength hint --}}
      <div style="background:var(--bg);border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:.72rem;color:var(--txt2);line-height:1.6">
        <div style="font-weight:600;color:var(--txt);margin-bottom:4px">Recomendaciones:</div>
        <div>· Mínimo 8 caracteres</div>
        <div>· Combina letras, números y símbolos</div>
        <div>· No reutilices contraseñas anteriores</div>
      </div>

      <div style="display:flex;justify-content:flex-end">
        <button type="submit" class="btn btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          Cambiar Contraseña
        </button>
      </div>
    </form>

  </div>

</div>

<script>
function togglePwd(id, btn) {
  var input = document.getElementById(id);
  var isText = input.type === 'text';
  input.type = isText ? 'password' : 'text';
  btn.style.color = isText ? 'var(--txt2)' : 'var(--teal)';
}
</script>

@endsection

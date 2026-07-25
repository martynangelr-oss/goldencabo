@extends('layouts.admin')
@section('page-title', 'Usuarios')
@section('content')

<div class="page-header">
  <div>
    <h1>Usuarios</h1>
    <p>Gestiona los usuarios del panel y sus permisos de acceso</p>
  </div>
  <button class="btn btn-primary" onclick="openNewUserModal()">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nuevo Usuario
  </button>
</div>

<div class="card" style="padding:0;overflow:hidden">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Rol</th>
          <th>Permisos CMS</th>
          <th>Estado</th>
          <th style="text-align:right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--teal),var(--teal-dk));display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700;flex-shrink:0;text-transform:uppercase">
                {{ substr($user->name, 0, 1) }}
              </div>
              <div>
                <div style="font-weight:600;font-size:.85rem;color:var(--txt)">
                  {{ $user->name }}
                  @if($user->id === Auth::id())
                    <span style="font-size:.6rem;background:var(--teal-lt);color:var(--teal-dk);padding:1px 7px;border-radius:100px;font-weight:700;margin-left:4px">TÚ</span>
                  @endif
                </div>
                <div style="font-size:.75rem;color:var(--txt2)">{{ $user->email }}</div>
              </div>
            </div>
          </td>
          <td>
            @if($user->isAdmin())
              <span class="badge badge-teal">Admin</span>
            @else
              <span class="badge badge-blue">Editor</span>
            @endif
          </td>
          <td>
            @if($user->isAdmin())
              <span style="font-size:.75rem;color:var(--txt2)">Acceso completo</span>
            @else
              <div style="display:flex;flex-wrap:wrap;gap:4px">
                @php
                  $labels = ['bookings'=>'Reservaciones','contacts'=>'Mensajes','cms'=>'CMS','vehicles'=>'Vehículos','tours'=>'Tours','zones'=>'Zonas','carousel'=>'Carrusel','gallery'=>'Galería','sections'=>'Secciones','settings'=>'Config'];
                @endphp
                @foreach($labels as $key => $label)
                  @if($user->hasPermission($key))
                    <span class="badge badge-green" style="font-size:.58rem">{{ $label }}</span>
                  @endif
                @endforeach
                @if(empty(array_filter($user->permissions ?? [])))
                  <span style="font-size:.75rem;color:var(--txt2)">Sin permisos</span>
                @endif
              </div>
            @endif
          </td>
          <td>
            <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">
              {{ $user->is_active ? 'Activo' : 'Inactivo' }}
            </span>
          </td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              @if($user->id !== Auth::id())
                <button class="btn btn-ghost btn-sm"
                        data-id="{{ $user->id }}"
                        data-name="{{ $user->name }}"
                        data-email="{{ $user->email }}"
                        data-role="{{ $user->role }}"
                        data-active="{{ $user->is_active ? '1' : '0' }}"
                        data-perms="{{ json_encode($user->permissions ?? []) }}"
                        onclick="openEditUserModal(this)">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Editar
                </button>
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                  @csrf
                  <button type="submit" class="btn btn-ghost btn-sm" title="{{ $user->is_active ? 'Desactivar' : 'Activar' }}">
                    @if($user->is_active)
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    @else
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    @endif
                    {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                  </button>
                </form>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      data-name="{{ $user->name }}"
                      onsubmit="return confirm('¿Eliminar al usuario ' + this.dataset.name + '?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    Eliminar
                  </button>
                </form>
              @else
                <span style="font-size:.75rem;color:var(--txt2)">Tu cuenta</span>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- ── Modal: Nuevo / Editar Usuario ── --}}
<div id="userModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:28px 32px;width:100%;max-width:580px;box-shadow:var(--shadow-md);position:relative;margin:20px;max-height:90vh;overflow-y:auto">
    <button onclick="closeUserModal()" style="position:absolute;top:14px;right:16px;background:none;border:none;cursor:pointer;color:var(--txt2);font-size:18px">✕</button>
    <h2 id="userModalTitle" style="font-size:1rem;font-weight:700;margin-bottom:20px;color:var(--txt)">Nuevo Usuario</h2>

    <form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <input type="hidden" name="_method" id="userMethod" value="">

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="fg">
          <label>Nombre <span style="color:var(--danger)">*</span></label>
          <input type="text" name="name" id="u-name" placeholder="Nombre completo" required>
        </div>
        <div class="fg">
          <label>Email <span style="color:var(--danger)">*</span></label>
          <input type="email" name="email" id="u-email" placeholder="correo@ejemplo.com" required>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="fg">
          <label>Contraseña <span id="u-pass-required" style="color:var(--danger)">*</span></label>
          <input type="password" name="password" id="u-password" placeholder="Mínimo 8 caracteres">
          <p class="form-hint" id="u-pass-hint">Mínimo 8 caracteres</p>
        </div>
        <div class="fg">
          <label>Rol <span style="color:var(--danger)">*</span></label>
          <select name="role" id="u-role" onchange="togglePermissions()" required>
            <option value="admin">Admin — acceso completo</option>
            <option value="editor">Editor — permisos personalizados</option>
          </select>
        </div>
      </div>

      {{-- Permisos (solo para editores) --}}
      <div id="permsSection" style="display:none;border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;margin-bottom:14px">
        <div style="font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--txt2);margin-bottom:14px">Permisos de Acceso</div>

        @php
          $permGroups = [
            'Gestión' => [
              'bookings' => ['Reservaciones', 'Ver y gestionar reservaciones'],
              'contacts' => ['Mensajes', 'Ver y gestionar mensajes de contacto'],
            ],
            'CMS' => [
              'cms'      => ['CMS (acceso general)', 'Habilita la sección CMS en el menú'],
              'vehicles' => ['Vehículos', 'Gestión de la flota'],
              'tours'    => ['Tours', 'Gestión de tours'],
              'zones'    => ['Zonas & Hoteles', 'Zonas de traslado y hoteles'],
              'carousel' => ['Carrusel Inicio', 'Diapositivas del carrusel'],
              'gallery'  => ['Galería de Viajes', 'Imágenes de la galería'],
              'sections' => ['Imágenes Secciones', 'Fotos de secciones del sitio'],
            ],
            'Configuración' => [
              'settings' => ['Config. Global', 'Configuración general del sitio'],
            ],
          ];
        @endphp

        @foreach($permGroups as $groupName => $perms)
        <div style="margin-bottom:14px">
          <div style="font-size:.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--teal);margin-bottom:8px">{{ $groupName }}</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
            @foreach($perms as $key => [$label, $hint])
            <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;padding:10px;border-radius:6px;border:1px solid var(--border);transition:background .15s" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
              <input type="checkbox" name="perm_{{ $key }}" id="perm_{{ $key }}" value="1" style="margin-top:2px;accent-color:var(--teal);flex-shrink:0">
              <div>
                <div style="font-size:.82rem;font-weight:600;color:var(--txt)">{{ $label }}</div>
                <div style="font-size:.7rem;color:var(--txt2)">{{ $hint }}</div>
              </div>
            </label>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>

      {{-- Estado (solo al editar) --}}
      <div id="activeSection" style="display:none;margin-bottom:14px">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.85rem;color:var(--txt);font-weight:400;text-transform:none;letter-spacing:0">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" id="u-active" value="1" style="width:auto;accent-color:var(--teal)">
          Usuario activo (puede iniciar sesión)
        </label>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:4px">
        <button type="button" onclick="closeUserModal()" class="btn btn-ghost">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="userSubmitBtn">Crear Usuario</button>
      </div>
    </form>
  </div>
</div>

<script>
const usersBaseUrl = '{{ url('admin/users') }}';

function openNewUserModal() {
  document.getElementById('userModalTitle').textContent = 'Nuevo Usuario';
  document.getElementById('userSubmitBtn').textContent  = 'Crear Usuario';
  document.getElementById('userForm').action = usersBaseUrl;
  document.getElementById('userMethod').value = '';
  document.getElementById('u-name').value = '';
  document.getElementById('u-email').value = '';
  document.getElementById('u-password').value = '';
  document.getElementById('u-password').required = true;
  document.getElementById('u-pass-required').style.display = '';
  document.getElementById('u-pass-hint').textContent = 'Mínimo 8 caracteres';
  document.getElementById('u-role').value = 'editor';
  document.getElementById('activeSection').style.display = 'none';
  // Reset checkboxes
  document.querySelectorAll('[id^=perm_]').forEach(cb => cb.checked = false);
  togglePermissions();
  showModal('userModal');
}

function openEditUserModal(btn) {
  const d  = btn.dataset;
  const id = d.id;
  document.getElementById('userModalTitle').textContent = 'Editar Usuario';
  document.getElementById('userSubmitBtn').textContent  = 'Guardar Cambios';
  document.getElementById('userForm').action = usersBaseUrl + '/' + id;
  document.getElementById('userMethod').value = 'PUT';
  document.getElementById('u-name').value  = d.name;
  document.getElementById('u-email').value = d.email;
  document.getElementById('u-password').value = '';
  document.getElementById('u-password').required = false;
  document.getElementById('u-pass-required').style.display = 'none';
  document.getElementById('u-pass-hint').textContent = 'Dejar vacío para no cambiar';
  document.getElementById('u-role').value = d.role;
  document.getElementById('u-active').checked = d.active === '1';
  document.getElementById('activeSection').style.display = '';

  // Set permissions
  const perms = JSON.parse(d.perms || '{}');
  document.querySelectorAll('[id^=perm_]').forEach(cb => {
    const key = cb.id.replace('perm_', '');
    cb.checked = !!perms[key];
  });

  togglePermissions();
  showModal('userModal');
}

function togglePermissions() {
  const isEditor = document.getElementById('u-role').value === 'editor';
  document.getElementById('permsSection').style.display = isEditor ? '' : 'none';
}

function closeUserModal() { hideModal('userModal'); }

function showModal(id) {
  var m = document.getElementById(id);
  m.style.display = 'flex';
  setTimeout(() => m.querySelector('input[type=text],input[type=email]')?.focus(), 80);
}
function hideModal(id) { document.getElementById(id).style.display = 'none'; }

document.getElementById('userModal').addEventListener('click', function(e) {
  if (e.target === this) closeUserModal();
});
</script>

@endsection

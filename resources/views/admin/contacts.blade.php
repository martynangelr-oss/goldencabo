@extends('layouts.admin')
@section('page-title', 'Mensajes')
@section('content')

<div class="page-header">
  <div>
    <h1>Mensajes de Contacto</h1>
    <p>Solicitudes e inquietudes recibidas desde el sitio</p>
  </div>
</div>

@forelse($contacts as $c)
<div class="msg-card {{ $c->read ? '' : 'unread' }}">
  <div class="msg-card-header">
    <div>
      <div class="msg-sender" style="display:flex;align-items:center;gap:8px">
        {{ $c->first_name }} {{ $c->last_name }}
        @if(!$c->read)
          <span class="badge badge-teal" style="font-size:.58rem">Nuevo</span>
        @endif
      </div>
      <div class="msg-email">{{ $c->email }}</div>
      @if($c->phone)
        <div class="msg-phone">📞 {{ $c->phone }}</div>
      @endif
    </div>
    <div style="text-align:right">
      <div class="msg-time">{{ $c->created_at->format('d/m/Y') }}</div>
      <div class="msg-time" style="margin-top:2px">{{ $c->created_at->format('H:i') }}</div>
    </div>
  </div>

  @if($c->service)
    <span class="msg-service">{{ $c->service }}</span>
  @endif

  <p class="msg-text">{{ $c->message }}</p>

  <div style="margin-top:12px;display:flex;gap:8px">
    <a href="mailto:{{ $c->email }}" class="btn btn-primary btn-sm">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
        <polyline points="22,6 12,13 2,6"/>
      </svg>
      Responder
    </a>
    <form method="POST" action="{{ route('admin.contact.destroy', $c) }}"
          onsubmit="return confirm('¿Eliminar este mensaje?')" style="display:inline">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
        </svg>
        Eliminar
      </button>
    </form>
  </div>
</div>
@empty
<div class="card" style="text-align:center;padding:60px 24px;color:var(--txt2)">
  <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
       style="margin:0 auto 12px;display:block;opacity:.3" stroke-linecap="round" stroke-linejoin="round">
    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
    <polyline points="22,6 12,13 2,6"/>
  </svg>
  <p style="font-size:.85rem">No hay mensajes de contacto.</p>
</div>
@endforelse

<div class="pagination-wrap">{{ $contacts->links() }}</div>

@endsection

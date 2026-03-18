<div style="border:1.5px solid var(--border);border-radius:var(--radius);overflow:hidden;background:var(--white)">
  {{-- Imagen --}}
  <div style="position:relative;aspect-ratio:4/3;background:var(--bg);overflow:hidden">
    <img src="{{ $slot['url'] }}" alt="{{ $slot['label'] }}"
         style="width:100%;height:100%;object-fit:cover;transition:transform .2s"
         onmouseover="this.style.transform='scale(1.03)'"
         onmouseout="this.style.transform='scale(1)'">
    @if($slot['is_custom'])
      <div style="position:absolute;top:8px;left:8px">
        <span style="background:var(--teal);color:#fff;font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:3px 8px;border-radius:100px">
          Personalizada
        </span>
      </div>
    @else
      <div style="position:absolute;top:8px;left:8px">
        <span style="background:rgba(0,0,0,.45);color:#fff;font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:3px 8px;border-radius:100px">
          Predeterminada
        </span>
      </div>
    @endif
  </div>

  {{-- Footer --}}
  <div style="padding:14px 16px">
    <div style="font-size:.82rem;font-weight:600;color:var(--txt);margin-bottom:2px">{{ $slot['label'] }}</div>
    <div style="font-size:.72rem;color:var(--txt2);margin-bottom:12px">{{ $slot['hint'] }}</div>
    <div style="display:flex;gap:8px">
      <button class="btn btn-primary btn-sm" style="flex:1;justify-content:center" onclick="openModal('{{ $key }}')">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Reemplazar
      </button>
      @if($slot['is_custom'])
        <button class="btn btn-ghost btn-sm btn-icon" title="Restaurar predeterminada"
                onclick="restoreSlot('{{ $key }}')">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5.25"/></svg>
        </button>
      @endif
    </div>
  </div>
</div>

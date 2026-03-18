<div class="wov" id="wov">
  <div class="wbox">
    <button class="wx" onclick="closeW()">✕</button>
    <div class="whead">
      <div class="wtitle" data-i18n="wizard.title">Reservar Traslado</div>
      <div class="wsteps">
        <div class="wstep on" id="ws1"><div class="wcirc">1</div><div class="wlabel" data-i18n="wizard.step1">Zona</div></div>
        <div class="wline" id="wl12"></div>
        <div class="wstep" id="ws2"><div class="wcirc">2</div><div class="wlabel" data-i18n="wizard.step2">Personal</div></div>
        <div class="wline" id="wl23"></div>
        <div class="wstep" id="ws3"><div class="wcirc">3</div><div class="wlabel" data-i18n="wizard.step3">Llegada</div></div>
        <div class="wline" id="wl34"></div>
        <div class="wstep" id="ws4"><div class="wcirc">✓</div><div class="wlabel" data-i18n="wizard.step4">Voucher</div></div>
      </div>
    </div>
    <div class="wbody">
      {{-- Step 1 --}}
      <div class="wpanel on" id="wp1">
        <p style="font-family:var(--fl);font-size:.85rem;color:var(--G500);margin-bottom:20px;font-weight:300" data-i18n="wizard.zone_prompt">Seleccione su zona de destino:</p>
        <div class="wzones">
          <div class="wzc" data-zone="1" data-name="San José del Cabo" data-r="$100 USD" data-o="$60 USD" onclick="selZ(this)">
            <div class="wchk">✓</div><div class="wznum"><span data-i18n="wizard.zone">Zona</span> 1</div><div class="wzname">San José del Cabo</div>
            <div class="wzprices"><div class="wzpr"><span class="wzpl" data-i18n="wizard.round_trip">Ida y vuelta</span><span class="wzpv">$100 USD</span></div><div class="wzpr"><span class="wzpl" data-i18n="wizard.one_way">Solo ida</span><span class="wzpv">$60 USD</span></div></div>
          </div>
          <div class="wzc" data-zone="2" data-name="Corredor Turístico" data-r="$120 USD" data-o="$65 USD" onclick="selZ(this)">
            <div class="wchk">✓</div><div class="wznum"><span data-i18n="wizard.zone">Zona</span> 2</div><div class="wzname">Corredor Turístico</div>
            <div class="wzprices"><div class="wzpr"><span class="wzpl" data-i18n="wizard.round_trip">Ida y vuelta</span><span class="wzpv">$120 USD</span></div><div class="wzpr"><span class="wzpl" data-i18n="wizard.one_way">Solo ida</span><span class="wzpv">$65 USD</span></div></div>
          </div>
          <div class="wzc" data-zone="3" data-name="Cabo San Lucas" data-r="$140 USD" data-o="$75 USD" onclick="selZ(this)">
            <div class="wchk">✓</div><div class="wznum"><span data-i18n="wizard.zone">Zona</span> 3</div><div class="wzname">Cabo San Lucas</div>
            <div class="wzprices"><div class="wzpr"><span class="wzpl" data-i18n="wizard.round_trip">Ida y vuelta</span><span class="wzpv">$140 USD</span></div><div class="wzpr"><span class="wzpl" data-i18n="wizard.one_way">Solo ida</span><span class="wzpv">$75 USD</span></div></div>
          </div>
          <div class="wzc" data-zone="4" data-name="Lado del Pacífico" data-r="$180 USD" data-o="$100 USD" onclick="selZ(this)">
            <div class="wchk">✓</div><div class="wznum"><span data-i18n="wizard.zone">Zona</span> 4</div><div class="wzname">Lado del Pacífico</div>
            <div class="wzprices"><div class="wzpr"><span class="wzpl" data-i18n="wizard.round_trip">Ida y vuelta</span><span class="wzpv">$180 USD</span></div><div class="wzpr"><span class="wzpl" data-i18n="wizard.one_way">Solo ida</span><span class="wzpv">$100 USD</span></div></div>
          </div>
        </div>
        <p id="zerr" style="font-family:var(--fj);color:#E5414A;font-size:.7rem;letter-spacing:1px;display:none" data-i18n="wizard.zone_err">Por favor seleccione una zona.</p>
      </div>
      {{-- Step 2 --}}
      <div class="wpanel" id="wp2">
        <div class="wf">
          <label class="wl"><span data-i18n="wizard.hotel">Hotel</span> <span class="r">*</span></label>
          <div style="position:relative">
            <input class="wi" id="f-hotel" type="text" placeholder="Escriba para buscar su hotel..." data-i18n-placeholder="wizard.hotel_ph" autocomplete="off"
                   oninput="hotelSearch()" onfocus="hotelSearch()" onblur="setTimeout(hotelHide,160)">
            <div id="f-hotel-list" class="hotel-dd"></div>
          </div>
          <div class="werr" data-i18n="wizard.required">Este campo es obligatorio.</div>
        </div>
        <div class="wrow">
          <div class="wf"><label class="wl"><span data-i18n="wizard.first_name">Nombre</span> <span class="r">*</span></label><input class="wi" id="f-name" type="text" placeholder="Su nombre" data-i18n-placeholder="wizard.first_name_ph"><div class="werr" data-i18n="wizard.invalid_name">Introduzca su nombre.</div></div>
          <div class="wf"><label class="wl" data-i18n="wizard.last_name">Apellido</label><input class="wi" id="f-last" type="text" placeholder="Su apellido" data-i18n-placeholder="wizard.last_name_ph"></div>
        </div>
        <div class="wf"><label class="wl"><span data-i18n="wizard.email">Correo Electrónico</span> <span class="r">*</span></label><input class="wi" id="f-email" type="email" placeholder="correo@ejemplo.com" data-i18n-placeholder="wizard.email_ph"><div class="werr" data-i18n="wizard.invalid_email">Introduzca un correo válido.</div></div>
        <div class="wf">
          <label class="wl"><span data-i18n="wizard.phone">Teléfono</span> <span class="r">*</span></label>
          <div class="gc-pw" id="f-phone-wrap">
            <select class="gc-lada" id="f-lada" onchange="gcFmtPhone('f-phone-wrap')">
              <option value="+52">MX +52</option>
              <option value="+1">US +1</option>
              <option value="+44">UK +44</option>
              <option value="+34">ES +34</option>
              <option value="+57">CO +57</option>
              <option value="+54">AR +54</option>
              <option value="+56">CL +56</option>
            </select>
            <input type="tel" class="gc-pvis" id="f-phone" placeholder="(___) ___ ____"
                   oninput="gcFmtPhone('f-phone-wrap')" maxlength="14" autocomplete="tel-national">
            <input type="hidden" class="gc-phid" id="f-phone-val" name="phone">
          </div>
          <div class="werr" data-i18n="wizard.phone_err">Ingrese un número de teléfono válido (10 dígitos).</div>
        </div>
      </div>
      {{-- Step 3 --}}
      <div class="wpanel" id="wp3">
        <label class="wl" style="margin-bottom:10px" data-i18n="wizard.depart_from">Salida desde</label>
        <div class="toggle">
          <button class="tgl on" id="td-air" onclick="setDir('air')" data-i18n="wizard.air_to_hotel">Del aeropuerto al hotel</button>
          <button class="tgl" id="td-htl" onclick="setDir('htl')" data-i18n="wizard.hotel_to_air">Del hotel al aeropuerto</button>
        </div>
        <div id="trip-toggle-wrap">
          <label class="wl" style="margin-bottom:10px" data-i18n="wizard.svc_type">Tipo de servicio</label>
          <div class="toggle">
            <button class="tgl on" id="tt-one" onclick="setTrip('one')" data-i18n="wizard.one_way_btn">Solo ida</button>
            <button class="tgl" id="tt-rnd" onclick="setTrip('rnd')" data-i18n="wizard.rnd_trip_btn">Ida y vuelta</button>
          </div>
        </div>
        <div class="wrow">
          <div class="wf" id="aflt-wrap"><label class="wl" data-i18n="wizard.arr_flight">Vuelo de Llegada</label><input class="wi" id="f-aflt" type="text" placeholder="Ej: AA1234" data-i18n-placeholder="wizard.arr_flight_ph" maxlength="10"></div>
          <div class="wf" id="dflt-wrap" style="display:none"><label class="wl" data-i18n="wizard.dep_flight">Vuelo de Salida</label><input class="wi" id="f-dflt" type="text" placeholder="Ej: AM5678" data-i18n-placeholder="wizard.dep_flight_ph" maxlength="10"></div>
        </div>
        <label class="wl" id="f-date-label">Fecha y Hora <span class="r">*</span></label>
        {{-- Hidden selects — read by existing wizard JS without changes --}}
        <select id="f-dd" style="display:none"><option value="">Día</option></select>
        <select id="f-mm" style="display:none"><option value="">Mes</option><option value="01">Enero</option><option value="02">Febrero</option><option value="03">Marzo</option><option value="04">Abril</option><option value="05">Mayo</option><option value="06">Junio</option><option value="07">Julio</option><option value="08">Agosto</option><option value="09">Sept.</option><option value="10">Oct.</option><option value="11">Nov.</option><option value="12">Dic.</option></select>
        <select id="f-yy" style="display:none"><option value="">Año</option></select>
        <select id="f-hh" style="display:none"></select>
        <select id="f-mn" style="display:none"></select>
        {{-- Visual date/time picker --}}
        <div class="gc-dt-wrap" style="margin-top:10px">
          <div class="gc-cal">
            <div class="gc-cal-nav">
              <button type="button" class="gc-cal-arr" onclick="calPrev()">&#8249;</button>
              <span id="gc-cal-title"></span>
              <button type="button" class="gc-cal-arr" onclick="calNext()">&#8250;</button>
            </div>
            <div id="gc-cal-grid" class="gc-cal-grid"></div>
          </div>
          <div class="gc-time">
            <div class="gc-time-label" data-i18n="wizard.arr_time">Hora de llegada</div>
            <div class="gc-time-controls">
              <div class="gc-time-col">
                <button type="button" class="gc-time-btn" onclick="timeAdj('hh',1)">▲</button>
                <div class="gc-time-val" id="gc-hh">00</div>
                <button type="button" class="gc-time-btn" onclick="timeAdj('hh',-1)">▼</button>
              </div>
              <div class="gc-time-sep">:</div>
              <div class="gc-time-col">
                <button type="button" class="gc-time-btn" onclick="timeAdj('mn',1)">▲</button>
                <div class="gc-time-val" id="gc-mn">00</div>
                <button type="button" class="gc-time-btn" onclick="timeAdj('mn',-1)">▼</button>
              </div>
            </div>
            <div class="gc-time-hint" data-i18n="wizard.time_hint">Hora del servidor (24 h)</div>
          </div>
        </div>
        <p id="derr" style="font-family:var(--fj);color:#E5414A;font-size:.68rem;letter-spacing:1px;display:none;margin-bottom:16px" data-i18n="wizard.date_err">Complete la fecha de llegada.</p>
        <div class="wf" style="margin-top:4px">
          <label class="wl" data-i18n="wizard.passengers">Número de Pasajeros</label>
          <div class="pax-row">
            <button class="pbtn2" onclick="chgPax(-1)">−</button>
            <span class="pnum" id="pnum">1</span>
            <span class="plbl" data-i18n="wizard.pax_label">pasajero(s) (máx. 10)</span>
            <button class="pbtn2" onclick="chgPax(1)">+</button>
          </div>
        </div>
        <div class="wf" style="margin-top:14px">
          <label class="terms-row">
            <input type="checkbox" id="f-terms">
            <span data-i18n-html="wizard.terms_html">Sí, acepto la <a href="#" onclick="return false">política de privacidad</a> y los <a href="#" onclick="return false">términos y condiciones</a>.</span>
          </label>
          <p id="termerr" style="font-family:var(--fj);color:#E5414A;font-size:.68rem;letter-spacing:1px;display:none;margin-top:6px" data-i18n="wizard.terms_err">Debe aceptar los términos.</p>
        </div>
      </div>
      {{-- Step 4 --}}
      <div class="wpanel" id="wp4">
        <div id="sview" class="wsuccess">
          <div class="sicon">🎉</div>
          <h3 class="stitle" data-i18n="wizard.confirmed">¡Reservación Confirmada!</h3>
          <p class="sdesc" data-i18n="wizard.confirmed_desc">Su voucher ha sido generado. Descárguelo o recíbalo por correo. Preséntelo impreso o en formato electrónico.</p>
          <div class="sref" id="sref"></div>
          <div class="pdf-btns">
            <button class="pdlbtn dl" onclick="downloadPDF()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span data-i18n="wizard.dl_pdf">Descargar Voucher PDF</span>
            </button>
            <button class="pdlbtn em" onclick="sendEmail()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <span data-i18n="wizard.send_email">Enviar por Correo Electrónico</span>
            </button>
            <button class="pdlbtn cl" onclick="closeW()" data-i18n="wizard.close">Cerrar</button>
          </div>
        </div>
        <div class="eprog" id="eprog">
          <div class="espin"></div>
          <p class="epmsg" data-i18n="wizard.sending_email">Enviando voucher por correo...</p>
          <div class="epdone" id="epdone">
            <div class="epdone-ico">✅</div>
            <div class="epdone-title" data-i18n="wizard.sent">¡Enviado con éxito!</div>
            <div class="epdone-sub"><span data-i18n="wizard.sent_to">El voucher fue enviado a</span><br><strong id="emailTo"></strong></div>
          </div>
        </div>
      </div>
    </div>
    <div class="wfoot" id="wfoot">
      <button class="wback" id="wback" style="display:none" onclick="wizBack()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
        <span data-i18n="wizard.prev">Anterior</span>
      </button>
      <div></div>
      <button class="wnext" id="wnext" onclick="wizNext()">
        <span data-i18n="wizard.next">Siguiente</span> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
    </div>
  </div>
</div>

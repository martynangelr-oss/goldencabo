/* Golden Cabo Transportation — Main JS */

/* ═══ PHONE / EMAIL UTILS ═══ */
function gcFmtPhone(wrapId) {
  var wrap = document.getElementById(wrapId);
  if (!wrap) return;
  var vis  = wrap.querySelector('.gc-pvis');
  var hid  = wrap.querySelector('.gc-phid');
  var lada = wrap.querySelector('.gc-lada');
  if (!vis) return;
  var raw = vis.value.replace(/\D/g, '').substring(0, 10);
  var fmt = '';
  if (raw.length > 0) fmt = '(' + raw.substring(0, 3);
  if (raw.length >= 3) fmt += ') ' + raw.substring(3, 6);
  else if (raw.length > 0) fmt += raw.substring(3);
  if (raw.length >= 6) fmt += ' ' + raw.substring(6, 10);
  else if (raw.length > 3) fmt += raw.substring(6);
  vis.value = fmt;
  if (hid) hid.value = raw.length === 10 ? ((lada ? lada.value : '+52') + raw) : '';
}

function gcValidEmail(email) {
  return /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(email)
      && email.length <= 255
      && !/\s/.test(email);
}

function gcCheckRateLimit(phone) {
  var key = 'gc_rl_' + phone.replace(/\D/g, '');
  var now = Date.now();
  var hour = 3600000;
  var arr = JSON.parse(sessionStorage.getItem(key) || '[]');
  arr = arr.filter(function(t){ return now - t < hour; });
  if (arr.length >= 5) return false;
  arr.push(now);
  sessionStorage.setItem(key, JSON.stringify(arr));
  return true;
}

/* ═══ API CONFIG (fallback si no viene del blade) ═══ */
if (!window.BOOKING_API) window.BOOKING_API = '/api/bookings';
if (!window.CONTACT_API) window.CONTACT_API = '/api/contact';
if (!window.CSRF_TOKEN)  window.CSRF_TOKEN  = document.querySelector('meta[name="csrf-token"]')?.content || '';

/* ═══ NAVBAR ═══ */
window.addEventListener('scroll', () =>
  document.getElementById('nav').classList.toggle('solid', scrollY > 50)
);
function toggleNav() {
  document.getElementById('ham').classList.toggle('open');
  document.getElementById('navDrawer').classList.toggle('open');
}
function closeNav() {
  document.getElementById('ham').classList.remove('open');
  document.getElementById('navDrawer').classList.remove('open');
}

/* ═══ HERO SLIDER ═══ */
const slides = [...document.querySelectorAll('.hs')];
let hi = 0, ht;
const _heroDefaults = {
  title:    document.getElementById('heroTitle')?.innerHTML || '',
  subtitle: document.getElementById('heroSub')?.textContent || '',
  btnText:  document.getElementById('heroBtn')?.textContent?.trim() || '',
  btnUrl:   document.getElementById('heroBtn')?.getAttribute('href') || '',
};
function goS(n) {
  slides[hi].classList.remove('on');
  hi = n;
  slides[hi].classList.add('on');
  document.getElementById('hcnt').textContent = String(hi + 1).padStart(2, '0') + ' / ' + String(slides.length).padStart(2, '0');
  document.querySelectorAll('.hdot').forEach((d, i) => d.classList.toggle('on', i === hi));
  // Sync hero text with current slide data
  const s = slides[hi];
  const titleEl    = document.getElementById('heroTitle');
  const subEl      = document.getElementById('heroSub');
  const btnEl      = document.getElementById('heroBtn');
  if (titleEl) titleEl.innerHTML = s.dataset.title ? s.dataset.title.replace(/\n/g, '<br>') : _heroDefaults.title;
  if (subEl)   subEl.textContent = s.dataset.subtitle || _heroDefaults.subtitle;
  if (btnEl && s.dataset.btnText) {
    btnEl.textContent = s.dataset.btnText;
    if (s.dataset.btnUrl) btnEl.setAttribute('href', s.dataset.btnUrl);
  } else if (btnEl) {
    btnEl.innerHTML = _heroDefaults.btnText;
  }
  clearInterval(ht);
  ht = setInterval(() => goS((hi + 1) % slides.length), 6000);
}
const dd = document.getElementById('heroDots');
slides.forEach((_, i) => {
  const d = document.createElement('div');
  d.className = 'hdot' + (i === 0 ? ' on' : '');
  d.onclick = () => goS(i);
  dd.appendChild(d);
});
ht = setInterval(() => goS((hi + 1) % slides.length), 6000);

/* ═══ SCROLL REVEAL ═══ */
const obs = new IntersectionObserver(e => e.forEach(x => {
  if (x.isIntersecting) x.target.classList.add('vis');
}), { threshold: .1 });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));

/* ═══ ZONE TABS ═══ */
function showZone(n, btn) {
  document.querySelectorAll('.ztab').forEach(t => t.classList.remove('on'));
  btn.classList.add('on');
  document.querySelectorAll('.zpanel').forEach(p => p.classList.remove('on'));
  const p = document.getElementById('zp' + n);
  p.classList.add('on');
  p.classList.remove('vis');
  setTimeout(() => p.classList.add('vis'), 10);
}

/* ═══ FLEET CAROUSEL ═══ */
let fi = 0;
function iw() { return window.innerWidth < 1024 ? 1 : 2; }
function buildDots() {
  const total = Math.ceil(document.querySelectorAll('.fcard').length / iw());
  const c = document.getElementById('fleetDots');
  c.innerHTML = '';
  for (let i = 0; i < total; i++) {
    const d = document.createElement('div');
    d.className = 'cdot' + (i === 0 ? ' on' : '');
    d.onclick = () => goF(i);
    c.appendChild(d);
  }
}
function goF(n) {
  const total = Math.ceil(document.querySelectorAll('.fcard').length / iw());
  fi = Math.max(0, Math.min(n, total - 1));
  const w = document.querySelector('.fcard').offsetWidth + 24;
  document.getElementById('fleetTrack').style.transform = `translateX(-${fi * iw() * w}px)`;
  document.querySelectorAll('.cdot').forEach((d, i) => d.classList.toggle('on', i === fi));
}
function moveFleet(dir) { goF(fi + dir); }
buildDots();
window.addEventListener('resize', buildDots);

/* ═══ HOTELS MODAL ═══ */
function showHotels(z) {
  const db = window.HOTELS_DB;
  const d = db[z];
  document.getElementById('hmT').textContent = ((window.GC_T && window.GC_T['js.hotels_prefix']) || 'Hoteles — ') + d.zone;
  document.getElementById('hmZ').textContent = d.area;
  document.getElementById('hmL').innerHTML = d.hotels.map(h => `<li>${h}</li>`).join('');
  document.getElementById('hmOv').classList.add('on');
  document.body.style.overflow = 'hidden';
}
function closeH() {
  document.getElementById('hmOv').classList.remove('on');
  document.body.style.overflow = '';
}
document.getElementById('hmOv').addEventListener('click', e => {
  if (e.target === document.getElementById('hmOv')) closeH();
});

/* ═══ LIGHTBOX ═══ */
function openLb(el) {
  document.getElementById('lb-img').src = el.querySelector('img').src;
  document.getElementById('lb').classList.add('on');
  document.body.style.overflow = 'hidden';
}
function closeLb() {
  document.getElementById('lb').classList.remove('on');
  document.body.style.overflow = '';
}

/* ═══ INIT DATE SELECTS ═══ */
(function () {
  const ddEl = document.getElementById('f-dd');
  if (!ddEl) return;
  for (let i = 1; i <= 31; i++) {
    const o = document.createElement('option');
    o.value = String(i).padStart(2, '0');
    o.textContent = i;
    ddEl.appendChild(o);
  }
  const yyEl = document.getElementById('f-yy');
  const cy = new Date().getFullYear();
  for (let i = cy; i <= cy + 3; i++) {
    const o = document.createElement('option');
    o.value = String(i);
    o.textContent = i;
    yyEl.appendChild(o);
  }
  const hhEl = document.getElementById('f-hh');
  for (let i = 0; i <= 23; i++) {
    const o = document.createElement('option');
    o.value = String(i).padStart(2, '0');
    o.textContent = String(i).padStart(2, '0');
    hhEl.appendChild(o);
  }
})();

/* ═══ BOOKING WIZARD ═══ */
let wStep = 1, zEl = null, pax = 1, dir = 'air', trip = 'one';
let bk = {}, reservationOrder = null;

function openW(e) {
  if (e && e.preventDefault) e.preventDefault();
  document.getElementById('wov').classList.add('on');
  document.body.style.overflow = 'hidden';
}
function closeW() {
  document.getElementById('wov').classList.remove('on');
  document.body.style.overflow = '';
  if (wStep === 4) setTimeout(resetW, 600);
}
document.getElementById('wov').addEventListener('click', e => {
  if (e.target === document.getElementById('wov')) closeW();
});

function resetW() {
  wStep = 1; zEl = null; pax = 1; dir = 'air'; trip = 'one';
  document.querySelectorAll('.wzc').forEach(c => c.classList.remove('sel'));
  document.querySelectorAll('.wi,.ws,.gc-pvis').forEach(f => { f.value = ''; f.classList.remove('err'); });
  document.querySelectorAll('.gc-phid').forEach(f => { f.value = ''; });
  document.querySelectorAll('.gc-pw').forEach(f => f.classList.remove('err'));
  document.getElementById('pnum').textContent = '1';
  document.getElementById('f-terms').checked = false;
  document.getElementById('eprog').classList.remove('on');
  document.getElementById('epdone').classList.remove('on');
  document.getElementById('sview').style.display = 'block';
  document.getElementById('eprog').style.display = 'none';
  setDir('air'); setTrip('one');
  goW(1);
}

function selZ(c) {
  document.querySelectorAll('.wzc').forEach(x => x.classList.remove('sel'));
  c.classList.add('sel'); zEl = c;
  document.getElementById('zerr').style.display = 'none';
  // Reset hotel field when zone changes
  const hi = document.getElementById('f-hotel');
  if (hi) { hi.value = ''; hi.classList.remove('err'); }
  hotelHide();
}

/* Hotel autocomplete */
function _hotelList() {
  const list = document.getElementById('f-hotel-list');
  const input = document.getElementById('f-hotel');
  if (!list || !input) return;
  const zone = zEl ? zEl.dataset.zone : null;
  const db = window.HOTELS_DB || {};
  const hotels = zone && db[zone] ? db[zone].hotels : [];
  const q = input.value.trim().toLowerCase();
  const filtered = q ? hotels.filter(h => h.toLowerCase().includes(q)) : hotels;
  if (!filtered.length) {
    list.innerHTML = zone
      ? '<div class="hotel-dd-empty">' + ((window.GC_T && window.GC_T['js.hotel_no_results']) || 'Sin resultados para esta zona.') + '</div>'
      : '<div class="hotel-dd-empty">' + ((window.GC_T && window.GC_T['js.hotel_no_zone']) || 'Primero seleccione una zona.') + '</div>';
  } else {
    list.innerHTML = filtered.map(h =>
      `<div class="hotel-dd-item" onmousedown="hotelPick(event)">${h}</div>`
    ).join('');
  }
  list.classList.add('open');
}
function hotelSearch() { _hotelList(); }
function hotelHide() {
  const list = document.getElementById('f-hotel-list');
  if (list) list.classList.remove('open');
}
function hotelPick(e) {
  e.preventDefault();
  const input = document.getElementById('f-hotel');
  if (input) { input.value = e.currentTarget.textContent; input.classList.remove('err'); }
  hotelHide();
}

/* ═══ DATE / TIME PICKER ═══ */
const _CAL_MONTHS_ES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const _CAL_DAYS_ES   = ['Lu','Ma','Mi','Ju','Vi','Sá','Do'];
function _calMonths() { return (window.GC_T && Array.isArray(window.GC_T['js.cal_months'])) ? window.GC_T['js.cal_months'] : _CAL_MONTHS_ES; }
function _calDays()   { return (window.GC_T && Array.isArray(window.GC_T['js.cal_days']))   ? window.GC_T['js.cal_days']   : _CAL_DAYS_ES; }
const _MN_STEPS   = [0, 15, 30, 45];
let _calYear = 0, _calMonth = 0, _calDay = 0, _calHH = 0, _calMN = 0;

function initDatePicker() {
  const now  = window.SERVER_NOW || {};
  const today = new Date();
  _calYear  = today.getFullYear();
  _calMonth = today.getMonth();      // 0-indexed
  _calDay   = 0;                     // no day selected yet
  // Round server minute to nearest 15
  _calHH = now.hour   !== undefined ? now.hour  : today.getHours();
  const rawMn = now.minute !== undefined ? now.minute : today.getMinutes();
  _calMN = _MN_STEPS.reduce((best, v) => Math.abs(v - rawMn) < Math.abs(best - rawMn) ? v : best, 0);
  document.getElementById('gc-hh').textContent = String(_calHH).padStart(2, '0');
  document.getElementById('gc-mn').textContent = String(_calMN).padStart(2, '0');
  _syncTimeSelects();
  calRender();
}

function _calLimits() {
  const t = new Date();
  const minDate = new Date(t.getFullYear(), t.getMonth(), t.getDate());
  const maxDate = new Date(t.getFullYear() + 1, t.getMonth(), t.getDate() - 1);
  return { today: t, minDate, maxDate };
}

function calRender() {
  const title = document.getElementById('gc-cal-title');
  const grid  = document.getElementById('gc-cal-grid');
  if (!title || !grid) return;
  const { today, minDate, maxDate } = _calLimits();
  title.textContent = _calMonths()[_calMonth] + ' ' + _calYear;
  // Update nav arrow states
  const canPrev = !(_calYear === minDate.getFullYear() && _calMonth === minDate.getMonth());
  const canNext = !(_calYear === maxDate.getFullYear() && _calMonth === maxDate.getMonth());
  const prevBtn = document.querySelector('.gc-cal-arr:first-child');
  const nextBtn = document.querySelector('.gc-cal-arr:last-child');
  if (prevBtn) { prevBtn.style.opacity = canPrev ? '' : '.3'; prevBtn.style.pointerEvents = canPrev ? '' : 'none'; }
  if (nextBtn) { nextBtn.style.opacity = canNext ? '' : '.3'; nextBtn.style.pointerEvents = canNext ? '' : 'none'; }
  const firstDow  = new Date(_calYear, _calMonth, 1).getDay();
  const offset    = firstDow === 0 ? 6 : firstDow - 1;
  const daysInMon = new Date(_calYear, _calMonth + 1, 0).getDate();
  let html = _calDays().map(d => `<div class="gc-cal-dh">${d}</div>`).join('');
  for (let i = 0; i < offset; i++) html += '<div></div>';
  const isCurrentMonth = _calYear === today.getFullYear() && _calMonth === today.getMonth();
  for (let d = 1; d <= daysInMon; d++) {
    const date    = new Date(_calYear, _calMonth, d);
    const blocked = date < minDate || date > maxDate;
    let cls = 'gc-cal-day';
    if (blocked)                                 cls += ' past';
    if (isCurrentMonth && d === today.getDate()) cls += ' today';
    if (d === _calDay)                           cls += ' sel';
    html += `<div class="${cls}"${blocked ? '' : ` onclick="calPick(${d})"`}>${d}</div>`;
  }
  grid.innerHTML = html;
  _syncDateSelects();
}

function calPrev() {
  const { minDate } = _calLimits();
  if (_calYear === minDate.getFullYear() && _calMonth === minDate.getMonth()) return;
  if (--_calMonth < 0) { _calMonth = 11; _calYear--; }
  _calDay = 0; calRender();
}
function calNext() {
  const { maxDate } = _calLimits();
  if (_calYear === maxDate.getFullYear() && _calMonth === maxDate.getMonth()) return;
  if (++_calMonth > 11) { _calMonth = 0; _calYear++; }
  _calDay = 0; calRender();
}
function calPick(d) {
  _calDay = d; calRender();
}

function timeAdj(field, delta) {
  if (field === 'hh') {
    _calHH = (_calHH + delta + 24) % 24;
    document.getElementById('gc-hh').textContent = String(_calHH).padStart(2, '0');
  } else {
    const idx = _MN_STEPS.indexOf(_calMN);
    _calMN = _MN_STEPS[((idx < 0 ? 0 : idx) + delta + _MN_STEPS.length) % _MN_STEPS.length];
    document.getElementById('gc-mn').textContent = String(_calMN).padStart(2, '0');
  }
  _syncTimeSelects();
}

function _syncDateSelects() {
  _setSelVal('f-dd', _calDay ? String(_calDay).padStart(2, '0') : '');
  _setSelVal('f-mm', _calDay ? String(_calMonth + 1).padStart(2, '0') : '');
  _setSelVal('f-yy', _calDay ? String(_calYear) : '');
}
function _syncTimeSelects() {
  _setSelVal('f-hh', String(_calHH).padStart(2, '0'));
  _setSelVal('f-mn', String(_calMN).padStart(2, '0'));
}
function _setSelVal(id, val) {
  const el = document.getElementById(id);
  if (!el) return;
  if (!el.querySelector(`option[value="${val}"]`)) {
    const o = document.createElement('option');
    o.value = o.textContent = val;
    el.appendChild(o);
  }
  el.value = val;
}

function _updateDateLabel() {
  const el = document.getElementById('f-date-label');
  if (!el) return;
  el.innerHTML = (window.GC_T && window.GC_T['js.date_label_html']) || 'Fecha y Hora <span class="r">*</span>';
}

function setDir(d) {
  dir = d;
  document.getElementById('td-air').classList.toggle('on', d === 'air');
  document.getElementById('td-htl').classList.toggle('on', d === 'htl');
  const tripWrap = document.getElementById('trip-toggle-wrap');
  const afltWrap = document.getElementById('aflt-wrap');
  const dfltWrap = document.getElementById('dflt-wrap');
  if (d === 'htl') {
    // Hotel → Aeropuerto: solo ida forzado, solo Vuelo de Salida (ancho completo)
    setTrip('one');
    if (tripWrap) tripWrap.style.display = 'none';
    if (afltWrap) { afltWrap.style.display = 'none'; afltWrap.style.gridColumn = ''; }
    if (dfltWrap) { dfltWrap.style.display = 'block'; dfltWrap.style.gridColumn = '1 / -1'; }
  } else {
    // Aeropuerto → Hotel: mostrar toggle y Vuelo de Llegada
    if (tripWrap) tripWrap.style.display = 'block';
    if (afltWrap) { afltWrap.style.display = 'block'; afltWrap.style.gridColumn = trip === 'rnd' ? '' : '1 / -1'; }
    if (dfltWrap) { dfltWrap.style.display = trip === 'rnd' ? 'block' : 'none'; dfltWrap.style.gridColumn = ''; }
  }
  _updateDateLabel();
}
function setTrip(t) {
  trip = t;
  document.getElementById('tt-one').classList.toggle('on', t === 'one');
  document.getElementById('tt-rnd').classList.toggle('on', t === 'rnd');
  if (dir !== 'htl') {
    const afltWrap = document.getElementById('aflt-wrap');
    const dfltWrap = document.getElementById('dflt-wrap');
    // Solo ida: Vuelo de Llegada ancho completo; Ida y vuelta: ambos a 1fr
    if (afltWrap) afltWrap.style.gridColumn = t === 'rnd' ? '' : '1 / -1';
    if (dfltWrap) { dfltWrap.style.display = t === 'rnd' ? 'block' : 'none'; dfltWrap.style.gridColumn = ''; }
  }
  _updateDateLabel();
}
function chgPax(d) {
  pax = Math.max(1, Math.min(10, pax + d));
  document.getElementById('pnum').textContent = pax;
}

function goW(n) {
  document.querySelectorAll('.wpanel').forEach((p, i) => p.classList.toggle('on', i === n - 1));
  [1, 2, 3, 4].forEach(i => {
    const s = document.getElementById('ws' + i);
    s.classList.remove('on', 'done');
    if (i === n) s.classList.add('on');
    else if (i < n) s.classList.add('done');
  });
  document.getElementById('wl12').classList.toggle('done', n > 2);
  document.getElementById('wl23').classList.toggle('done', n > 3);
  document.getElementById('wl34').classList.toggle('done', n > 4);
  wStep = n;
  const back = document.getElementById('wback'),
        next = document.getElementById('wnext'),
        foot = document.getElementById('wfoot');
  if (n === 4) { foot.style.display = 'none'; return; }
  foot.style.display = 'flex';
  back.style.display = n > 1 ? 'flex' : 'none';
  const arrowSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>';
  const _nextTxt = n === 3
    ? ((window.GC_T && window.GC_T['js.confirm_btn']) || 'Confirmar')
    : ((window.GC_T && window.GC_T['js.next_btn'])    || 'Siguiente');
  next.innerHTML = _nextTxt + ' ' + arrowSvg;
  if (n === 3) initDatePicker();
}

async function wizNext() {
  if (wStep === 1) {
    if (!zEl) { document.getElementById('zerr').style.display = 'block'; return; }
    goW(2);
  } else if (wStep === 2) {
    let ok = true;
    const h = document.getElementById('f-hotel'), n = document.getElementById('f-name');
    const e = document.getElementById('f-email');
    const p = document.getElementById('f-phone'); // visible input
    const pWrap = document.getElementById('f-phone-wrap');
    [h, n, e].forEach(f => f.classList.remove('err'));
    if (pWrap) pWrap.classList.remove('err'); else if (p) p.classList.remove('err');
    if (!h.value.trim()) { h.classList.add('err'); ok = false; }
    if (!n.value.trim()) { n.classList.add('err'); ok = false; }
    if (!gcValidEmail(e.value)) { e.classList.add('err'); ok = false; }
    const phoneDigits = p ? p.value.replace(/\D/g, '') : '';
    if (phoneDigits.length !== 10) {
      if (pWrap) pWrap.classList.add('err'); else if (p) p.classList.add('err');
      ok = false;
    }
    if (ok) goW(3);
  } else if (wStep === 3) {
    let ok = true;
    const ddV = document.getElementById('f-dd').value,
          mmV = document.getElementById('f-mm').value,
          yyV = document.getElementById('f-yy').value;
    document.getElementById('derr').style.display = (!ddV || !mmV || !yyV) ? 'block' : 'none';
    if (!ddV || !mmV || !yyV) ok = false;
    if (!document.getElementById('f-terms').checked) {
      document.getElementById('termerr').style.display = 'block'; ok = false;
    } else { document.getElementById('termerr').style.display = 'none'; }
    if (ok) await submitReservation();
  }
}
function wizBack() { if (wStep > 1) goW(wStep - 1); }

async function submitReservation() {
  const btn = document.getElementById('wnext');
  btn.disabled = true;
  btn.textContent = (window.GC_T && window.GC_T['js.sending']) || 'Enviando...';

  // Rate limit check by phone
  const cleanPhone = (document.getElementById('f-phone-val') || {}).value
                  || document.getElementById('f-phone').value;
  if (!gcCheckRateLimit(cleanPhone)) {
    alert((window.GC_T && window.GC_T['js.rate_limit']) || 'Ha superado el límite de 5 intentos por hora para este número. Por favor intente más tarde.');
    btn.disabled = false;
    const arrowSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>';
    btn.innerHTML = ((window.GC_T && window.GC_T['js.confirm_btn']) || 'Confirmar') + ' ' + arrowSvg;
    return;
  }

  try {
    const payload = {
      zone: zEl.dataset.zone,
      hotel: document.getElementById('f-hotel').value,
      first_name: document.getElementById('f-name').value,
      last_name: document.getElementById('f-last').value,
      email: document.getElementById('f-email').value,
      phone: (document.getElementById('f-phone-val') || {}).value || document.getElementById('f-phone').value,
      passengers: pax,
      direction: dir,
      trip_type: trip,
      arrival_flight: document.getElementById('f-aflt').value,
      departure_flight: document.getElementById('f-dflt').value,
      arrival_day: document.getElementById('f-dd').value,
      arrival_month: document.getElementById('f-mm').value,
      arrival_year: document.getElementById('f-yy').value,
      arrival_hour: document.getElementById('f-hh').value,
      arrival_minute: document.getElementById('f-mn').value,
      terms: '1',
    };
    const resp = await fetch(window.BOOKING_API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN },
      body: JSON.stringify(payload),
    });
    const data = await resp.json();
    if (data.success) {
      reservationOrder = data.order;
      bk = {
        order: data.order,
        zone: zEl.dataset.zone, zoneName: zEl.dataset.name,
        priceR: zEl.dataset.r, priceO: zEl.dataset.o,
        hotel: document.getElementById('f-hotel').value,
        name: document.getElementById('f-name').value,
        last: document.getElementById('f-last').value,
        email: document.getElementById('f-email').value,
        phone: (document.getElementById('f-phone-val') || {}).value || document.getElementById('f-phone').value,
        pax, dir, trip,
        arrFlt: document.getElementById('f-aflt').value || 'N/A',
        depFlt: document.getElementById('f-dflt').value || 'N/A',
        dd: document.getElementById('f-dd').value,
        mm: document.getElementById('f-mm').value,
        yy: document.getElementById('f-yy').value,
        hh: document.getElementById('f-hh').value || '00',
        mn: document.getElementById('f-mn').value || '00',
      };
      buildSuccess();
    } else {
      alert((window.GC_T && window.GC_T['js.error_generic']) || 'Ocurrió un error. Por favor intente de nuevo.');
    }
  } catch (err) {
    alert((window.GC_T && window.GC_T['js.error_connection']) || 'Error de conexión. Por favor intente de nuevo.');
  } finally {
    btn.disabled = false;
  }
}

function buildSuccess() {
  const b = bk;
  const _t = window.GC_T || {};
  const dirTxt  = b.dir  === 'air' ? (_t['js.dir_air']   || 'Aeropuerto → Hotel') : (_t['js.dir_hotel']  || 'Hotel → Aeropuerto');
  const tripTxt = b.trip === 'rnd' ? (_t['js.trip_round'] || 'Ida y vuelta')       : (_t['js.trip_one']   || 'Solo ida');
  const price = b.trip === 'rnd' ? b.priceR : b.priceO;
  const _lbl = (k, fb) => _t[k] || fb;
  document.getElementById('sref').innerHTML =
    `<strong>Order #:</strong> ${b.order} &nbsp;|&nbsp; <strong>${_lbl('js.lbl_zone','Zona')} ${b.zone}</strong> – ${b.zoneName}<br>` +
    `<strong>${_lbl('js.lbl_passenger','Pasajero')}:</strong> ${b.name} ${b.last} &nbsp;|&nbsp; <strong>PAX:</strong> ${b.pax}<br>` +
    `<strong>${_lbl('js.lbl_hotel','Hotel')}:</strong> ${b.hotel} &nbsp;|&nbsp; <strong>${_lbl('js.lbl_direction','Dirección')}:</strong> ${dirTxt}<br>` +
    `<strong>${_lbl('js.lbl_type','Tipo')}:</strong> ${tripTxt} &nbsp;|&nbsp; <strong>${_lbl('js.lbl_price','Precio')}:</strong> ${price}<br>` +
    `<strong>${_lbl('js.lbl_arr_flight','Vuelo Llegada')}:</strong> ${b.arrFlt} &nbsp;|&nbsp; <strong>${_lbl('js.lbl_date','Fecha')}:</strong> ${b.dd}/${b.mm}/${b.yy} ${b.hh}:${b.mn}`;
  goW(4);
}

/* ═══ PDF GENERATION (client-side jsPDF) ═══ */

// ── Font helpers ──
let _poppinsReg = null, _poppinsBold = null;
async function _ensurePoppins() {
  if (_poppinsReg) return true;
  try {
    const toB64 = async url => {
      const resp = await fetch(url);
      if (!resp.ok) throw new Error('Font fetch failed: ' + resp.status);
      const buf = await resp.arrayBuffer();
      const bytes = new Uint8Array(buf);
      let s = '';
      for (let i = 0; i < bytes.byteLength; i++) s += String.fromCharCode(bytes[i]);
      return btoa(s);
    };
    [_poppinsReg, _poppinsBold] = await Promise.all([
      toB64('https://fonts.gstatic.com/s/poppins/v21/pxiEyp8kv8JHgFVrJJfecg.ttf'),
      toB64('https://fonts.gstatic.com/s/poppins/v21/pxiByp8kv8JHgFVrLCz7Z1JlFd2JQEl8qg.ttf'),
    ]);
    return true;
  } catch (e) { return false; }
}
function _addPoppins(doc) {
  if (!_poppinsReg) return false;
  doc.addFileToVFS('Poppins-Regular.ttf', _poppinsReg);
  doc.addFileToVFS('Poppins-Bold.ttf',    _poppinsBold);
  doc.addFont('Poppins-Regular.ttf', 'Poppins', 'normal');
  doc.addFont('Poppins-Bold.ttf',    'Poppins', 'bold');
  return true;
}

// ── Logo helper ──
let _logoInfo = undefined;
async function _ensureLogo() {
  if (_logoInfo !== undefined) return _logoInfo;
  const url = window.SITE_LOGO;
  if (!url) { _logoInfo = null; return null; }
  try {
    const img = new Image();
    await new Promise((res, rej) => { img.onload = res; img.onerror = rej; img.src = url; });
    const cv = document.createElement('canvas');
    cv.width = img.naturalWidth; cv.height = img.naturalHeight;
    cv.getContext('2d').drawImage(img, 0, 0);
    _logoInfo = { b64: cv.toDataURL('image/png'), w: img.naturalWidth, h: img.naturalHeight };
  } catch (e) { _logoInfo = null; }
  return _logoInfo;
}

async function generatePDF() {
  await Promise.all([_ensurePoppins(), _ensureLogo()]);
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'letter' });
  let hp = false;
  try { hp = _addPoppins(doc); } catch(e) { hp = false; }
  const f  = hp ? 'Poppins' : 'helvetica';
  const b  = bk;
  const W  = 215.9, H = 279.4;

  // Palette — white dominant, teal as accent
  const teal   = [0, 172, 151];
  const tealLt = [232, 248, 246];
  const dark   = [22, 32, 42];
  const mid    = [95, 110, 125];
  const light  = [246, 248, 250];
  const border = [218, 226, 232];
  const white  = [255, 255, 255];

  const _tp = window.GC_T || {};
  const dirTxt  = b.dir  === 'air' ? (_tp['js.dir_air']   || 'Del Aeropuerto al Hotel') : (_tp['js.dir_hotel']  || 'Del Hotel al Aeropuerto');
  const tripTxt = b.trip === 'rnd' ? (_tp['js.trip_round'] || 'Ida y vuelta')            : (_tp['js.trip_one']   || 'Solo ida');
  const price   = b.trip === 'rnd' ? b.priceR : b.priceO;
  const zones   = [
    { z: 'Zona 1', n: 'San José del Cabo',  r: '$100 USD', o: '$60 USD'  },
    { z: 'Zona 2', n: 'Corredor Turístico', r: '$120 USD', o: '$65 USD'  },
    { z: 'Zona 3', n: 'Cabo San Lucas',     r: '$140 USD', o: '$75 USD'  },
    { z: 'Zona 4', n: 'Lado del Pacífico',  r: '$180 USD', o: '$100 USD' },
  ];

  // ── TOP ACCENT BAR ──
  doc.setFillColor(...teal); doc.rect(0, 0, W, 3.5, 'F');

  // ── HEADER (white, y:3.5–58) ──
  doc.setFillColor(...white); doc.rect(0, 3.5, W, 54.5, 'F');

  // Logo
  let logoBottom = 3.5;
  if (_logoInfo) {
    const maxW = 42, maxH = 20;
    const ratio = Math.min(maxW / _logoInfo.w, maxH / _logoInfo.h);
    const lw = _logoInfo.w * ratio, lh = _logoInfo.h * ratio;
    doc.addImage(_logoInfo.b64, 'PNG', 14, 9, lw, lh);
    logoBottom = 9 + lh + 3;
  } else {
    doc.setFont(f, 'bold'); doc.setFontSize(15); doc.setTextColor(...teal);
    doc.text('GOLDEN CABO', 14, 21);
    doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
    doc.text('TRANSPORTATION', 14, 27);
    logoBottom = 31;
  }
  // Contact line below logo
  doc.setFont(f, 'normal'); doc.setFontSize(7); doc.setTextColor(...mid);
  doc.text('(+52) 333 303 4455  ·  (+52) 624 121 6527  ·  goldencabotransportation@gmail.com', 14, logoBottom + 3);

  // Right — Voucher badge + Order
  doc.setFillColor(...teal); doc.roundedRect(W - 74, 8, 60, 10, 2, 2, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(7.5); doc.setTextColor(...white);
  doc.text('TRANSPORTATION VOUCHER', W - 73, 14.5);

  doc.setFont(f, 'bold'); doc.setFontSize(11); doc.setTextColor(...dark);
  doc.text('Order #' + b.order, W - 44, 28, { align: 'center' });
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  const _psfx = (_tp['js.passengers_sfx'] || 'pasajero');
  doc.text(b.pax + ' ' + _psfx + (b.pax > 1 ? 's' : '') + '  ·  ' + tripTxt, W - 44, 34, { align: 'center' });

  // Price chip
  doc.setFillColor(...tealLt); doc.setDrawColor(...teal);
  doc.roundedRect(W - 60, 37.5, 46, 14, 2.5, 2.5, 'FD');
  doc.setFont(f, 'bold'); doc.setFontSize(14); doc.setTextColor(...teal);
  doc.text(price, W - 37, 47, { align: 'center' });

  // ── SEPARATOR ──
  doc.setFillColor(...border); doc.rect(0, 58, W, 0.5, 'F');

  // ── CUSTOMER BAND (y:58.5–80) ──
  doc.setFillColor(...light); doc.rect(0, 58.5, W, 22, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(11); doc.setTextColor(...dark);
  doc.text('Estimado/a ' + b.name + (b.last ? ' ' + b.last : '') + ',', 14, 69);
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  doc.text('Gracias por elegir Golden Cabo Transportation. A continuación los detalles de su servicio.', 14, 75.5, { maxWidth: W - 28 });

  // ── ZONE TABLE ──
  let y = 88;
  doc.setFont(f, 'bold'); doc.setFontSize(7); doc.setTextColor(...teal);
  doc.text('TARIFAS POR ZONA', 14, y);
  doc.setFillColor(...border); doc.rect(14, y + 2, W - 28, 0.4, 'F');
  y += 7;

  const cw = (W - 28) / 4;
  doc.setFillColor(...dark); doc.roundedRect(14, y, W - 28, 8, 1.5, 1.5, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(6.5); doc.setTextColor(...white);
  doc.text('ZONA', 17, y + 5.2);
  doc.text('DESTINO', 17 + cw, y + 5.2);
  doc.text('IDA Y VUELTA', 17 + cw * 2, y + 5.2);
  doc.text('SOLO IDA', 17 + cw * 3, y + 5.2);
  y += 8;

  zones.forEach((z, i) => {
    const sel = String(i + 1) === String(b.zone);
    if (sel) {
      doc.setFillColor(...tealLt); doc.rect(14, y, W - 28, 9, 'F');
      doc.setFillColor(...teal);   doc.rect(14, y, 3.5, 9, 'F');
    } else {
      doc.setFillColor(i % 2 === 0 ? 255 : 250, i % 2 === 0 ? 255 : 252, i % 2 === 0 ? 255 : 251);
      doc.rect(14, y, W - 28, 9, 'F');
    }
    doc.setFont(f, sel ? 'bold' : 'normal');
    doc.setFontSize(7.5);
    doc.setTextColor(...(sel ? teal : dark));
    doc.text(z.z, sel ? 20.5 : 17, y + 6.2);
    doc.text(z.n, 17 + cw, y + 6.2);
    doc.text(z.r, 17 + cw * 2, y + 6.2);
    doc.text(z.o, 17 + cw * 3, y + 6.2);
    y += 9;
  });

  // ── TRAVEL INFO ──
  y += 8;
  doc.setFont(f, 'bold'); doc.setFontSize(7); doc.setTextColor(...teal);
  doc.text('INFORMACIÓN DEL VIAJE', 14, y);
  doc.setFillColor(...border); doc.rect(14, y + 2, W - 28, 0.4, 'F');
  y += 8;

  const bw  = (W - 28) / 2 - 4;
  const bx2 = 14 + bw + 8;
  const cardH = 46;

  // Left card
  doc.setFillColor(...white); doc.setDrawColor(...border);
  doc.roundedRect(14, y, bw, cardH, 2, 2, 'FD');
  doc.setFillColor(...teal); doc.roundedRect(14, y, 3.5, cardH, 1, 1, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(6.5); doc.setTextColor(...teal);
  doc.text('VUELO / LLEGADA', 21, y + 8);
  doc.setFont(f, 'bold'); doc.setFontSize(8.5); doc.setTextColor(...dark);
  doc.text(dirTxt, 21, y + 17);
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  doc.text('Vuelo:   ' + b.arrFlt, 21, y + 26);
  doc.text('Fecha:  ' + b.dd + '/' + b.mm + '/' + b.yy, 21, y + 32);
  doc.text('Hora:    ' + b.hh + ':' + b.mn, 21, y + 38);

  // Right card
  doc.setFillColor(...white); doc.setDrawColor(...border);
  doc.roundedRect(bx2, y, bw, cardH, 2, 2, 'FD');
  doc.setFillColor(...teal); doc.roundedRect(bx2, y, 3.5, cardH, 1, 1, 'F');
  doc.setFont(f, 'bold'); doc.setFontSize(6.5); doc.setTextColor(...teal);
  doc.text('HOTEL / SERVICIO', bx2 + 7, y + 8);
  doc.setFont(f, 'bold'); doc.setFontSize(8.5); doc.setTextColor(...dark);
  doc.text(b.hotel, bx2 + 7, y + 17, { maxWidth: bw - 10 });
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  doc.text('Tipo:         ' + tripTxt, bx2 + 7, y + 28);
  doc.text('Vuelo salida: ' + (b.trip === 'rnd' ? b.depFlt : 'N/A'), bx2 + 7, y + 34);

  // ── MEETING POINT ──
  y += cardH + 8;
  doc.setFillColor(...tealLt); doc.setDrawColor(...teal);
  doc.roundedRect(14, y, W - 28, 26, 2, 2, 'FD');
  doc.setFont(f, 'bold'); doc.setFontSize(7.5); doc.setTextColor(...teal);
  doc.text('PUNTO DE ENCUENTRO', 18, y + 9);
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(25, 75, 70);
  doc.text('Nos encontrará fuera de la terminal, bajo la SOMBRILLA #10.', 18, y + 17);
  doc.text('El personal le esperará con un letrero con su nombre.', 18, y + 23);

  // ── CONTACT ──
  y += 32;
  doc.setFillColor(...light); doc.setDrawColor(...border);
  doc.roundedRect(14, y, W - 28, 18, 2, 2, 'FD');
  doc.setFont(f, 'bold'); doc.setFontSize(7.5); doc.setTextColor(...dark);
  doc.text('CONTACTO LOCAL:  CESAR URBINA', 18, y + 8);
  doc.setFont(f, 'normal'); doc.setFontSize(7.5); doc.setTextColor(...mid);
  doc.text('Tel: 011 52 624 121 65 27  ·  goldencabotransportation@gmail.com', 18, y + 14.5);

  // ── FOOTER ──
  doc.setFillColor(...light); doc.rect(0, H - 16, W, 16, 'F');
  doc.setFillColor(...teal);  doc.rect(0, H - 16, W, 1.5, 'F');
  doc.setFont(f, 'normal'); doc.setFontSize(7); doc.setTextColor(...mid);
  doc.text('Golden Cabo Transportation  ·  www.goldencabotransportation.com', W / 2, H - 8.5, { align: 'center' });
  doc.text('"Ven a Los Cabos y déjanos la conducción a nosotros"', W / 2, H - 3.5, { align: 'center' });

  return doc;
}

async function downloadPDF() {
  const btn = document.querySelector('.pdlbtn.dl');
  const orig = btn ? btn.innerHTML : '';
  if (btn) { btn.disabled = true; btn.textContent = 'Generando PDF...'; }
  try {
    if (!window.jspdf) throw new Error('jsPDF no disponible');
    const doc = await generatePDF();
    doc.save('GoldenCabo_Voucher_' + bk.order + '.pdf');
  } catch(e) {
    console.error('PDF generation error:', e);
    alert('No se pudo generar el voucher. Por favor intente de nuevo.');
  } finally {
    if (btn) { btn.disabled = false; btn.innerHTML = orig; }
  }
}

async function sendEmail() {
  document.getElementById('sview').style.display = 'none';
  const ep = document.getElementById('eprog');
  ep.style.display = 'flex'; setTimeout(() => ep.classList.add('on'), 10);
  if (reservationOrder) {
    try {
      await fetch(window.BOOKING_API.replace('/api/bookings', '/api/bookings/') + reservationOrder + '/resend', {
        method: 'POST', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }
      });
    } catch (e) { }
  }
  setTimeout(() => {
    document.querySelector('.espin').style.display = 'none';
    document.querySelector('.epmsg').style.display = 'none';
    document.getElementById('emailTo').textContent = bk.email;
    document.getElementById('epdone').classList.add('on');
    setTimeout(async () => { const doc = await generatePDF(); doc.save('GoldenCabo_Voucher_' + bk.order + '.pdf'); }, 700);
  }, 2400);
}

/* ═══ CONTACT FORM ═══ */
async function submitContact() {
  const form = document.getElementById('contactForm');
  if (!form) return;

  // Validate email
  const emailEl = form.querySelector('[name="email"]');
  if (emailEl && !gcValidEmail(emailEl.value)) {
    emailEl.style.borderColor = '#E5414A';
    emailEl.focus();
    return;
  }
  if (emailEl) emailEl.style.borderColor = '';

  // Validate phone (10 digits required)
  const cfWrap = document.getElementById('cf-phone-wrap');
  const cfVis  = document.getElementById('cf-phone-vis');
  const cfHid  = document.getElementById('cf-phone-val');
  const cfErr  = document.getElementById('cf-phone-err');
  const phoneDigits = cfVis ? cfVis.value.replace(/\D/g, '') : '';
  if (phoneDigits.length !== 10) {
    if (cfWrap) cfWrap.classList.add('err');
    if (cfErr) cfErr.style.display = 'block';
    return;
  }
  if (cfWrap) cfWrap.classList.remove('err');
  if (cfErr) cfErr.style.display = 'none';

  // Rate limit
  const cleanPhone = cfHid ? cfHid.value : phoneDigits;
  if (!gcCheckRateLimit(cleanPhone)) {
    alert((window.GC_T && window.GC_T['js.rate_limit']) || 'Ha superado el límite de 5 intentos por hora para este número. Por favor intente más tarde.');
    return;
  }

  const formData = new FormData(form);
  const payload = Object.fromEntries(formData.entries());
  try {
    const resp = await fetch(window.CONTACT_API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN },
      body: JSON.stringify(payload),
    });
    const data = await resp.json();
    if (data.success) {
      gcShowContactToast();
      form.reset();
      if (cfWrap) cfWrap.classList.remove('err');
      if (cfVis) cfVis.value = '';
      if (cfHid) cfHid.value = '';
    } else { alert((window.GC_T && window.GC_T['js.error_generic']) || 'Error al enviar. Por favor intente de nuevo.'); }
  } catch (e) { alert((window.GC_T && window.GC_T['js.error_connection']) || 'Error de conexión.'); }
}

function gcShowContactToast() {
  let toast = document.getElementById('gc-contact-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'gc-contact-toast';
    toast.className = 'gc-toast';
    toast.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span>¡Gracias! Nos comunicaremos con usted pronto.</span>';
    document.body.appendChild(toast);
  }
  clearTimeout(toast._hideTimer);
  toast.classList.add('gc-toast--show');
  toast._hideTimer = setTimeout(() => toast.classList.remove('gc-toast--show'), 4000);
}

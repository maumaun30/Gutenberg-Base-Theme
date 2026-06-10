/**
 * Register / Sign Up popup modal.
 *
 * Markup lives in header.php (#fm-reg-modal). Any element with the class
 * `fm-open-register` (or the legacy id #fm-register-trigger) opens it.
 *
 * On submit: validates the terms checkbox + PH phone number, reuses the
 * attribution that attribution.js persisted to localStorage (fm_attr), and
 * redirects to the registration URL with `mobile=<number>` + attribution.
 */
document.addEventListener('DOMContentLoaded', function () {

  const OPEN_CLASS = 'fm-open-register';
  const modal    = document.getElementById('fm-reg-modal');
  const closeBtn = document.getElementById('fm-reg-close');
  const phoneIn  = document.getElementById('fm-reg-phone');
  const termsCk  = document.getElementById('fm-reg-terms');
  const submit   = document.getElementById('fm-reg-submit');
  if (!modal) return;

  function openModal() {
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('funalo-drawer-open');
    setTimeout(function () { if (phoneIn) phoneIn.focus(); }, 150);
  }
  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('funalo-drawer-open');
  }

  /* Any element with .fm-open-register (or #fm-register-trigger) opens the
     modal. Delegated so dynamically-added triggers work too. */
  document.addEventListener('click', function (e) {
    const trigger = e.target.closest('.' + OPEN_CLASS + ', #fm-register-trigger');
    if (!trigger) return;
    e.preventDefault();
    openModal();
  });
  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
  });

  /* Live-format the phone toward a local 09XXXXXXXXX form */
  if (phoneIn) {
    phoneIn.addEventListener('input', function () {
      let v = this.value.replace(/\D/g, '');
      if (v.startsWith('639')) v = '0' + v.slice(2);
      else if (v.startsWith('63')) v = '0' + v.slice(2);
      else if (v.startsWith('9')) v = '0' + v;
      this.value = v;
    });
  }

  /* Read attribution that attribution.js persisted on page load */
  function getAttribution() {
    const stored = localStorage.getItem('fm_attr') || localStorage.getItem('fm_attribution');
    try { return stored ? JSON.parse(stored) : {}; } catch (err) { return {}; }
  }

  function submitPhone() {
    if (!termsCk || !termsCk.checked) {
      alert('You must agree to the Terms');
      return;
    }
    let v = (phoneIn ? phoneIn.value : '').trim();
    if (!v) { alert('Enter phone number'); if (phoneIn) phoneIn.focus(); return; }

    v = v.replace(/\D/g, '');
    if (v.startsWith('639') && v.length === 12) v = '0' + v.slice(2);
    else if (v.startsWith('63') && v.length === 12) v = '0' + v.slice(2);
    else if (v.startsWith('9') && v.length === 10) v = '0' + v;
    else if (v.startsWith('09') && v.length === 11) { /* already valid */ }
    else { alert('Invalid PH number'); if (phoneIn) phoneIn.focus(); return; }

    const attr = getAttribution();
    const query = new URLSearchParams();
    query.set('mobile', v);
    Object.keys(attr).forEach(function (key) {
      if (attr[key]) query.set(key, attr[key]);
    });

    window.location.href = 'https://funalomax.com/en?' + query.toString();
  }

  if (submit) submit.addEventListener('click', submitPhone);
  if (phoneIn) phoneIn.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); submitPhone(); }
  });

});

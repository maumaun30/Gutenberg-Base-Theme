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

  /* The +63 prefix is shown next to the field, so the input holds only the
     10-digit local number starting with 9 (9XXXXXXXXX). Strip any country
     code or leading 0 the user types/pastes. */
  function toLocal(value) {
    let v = (value || '').replace(/\D/g, '');
    if (v.startsWith('63')) v = v.slice(2);
    if (v.startsWith('0')) v = v.slice(1);
    return v.slice(0, 10);
  }

  if (phoneIn) {
    phoneIn.addEventListener('input', function () {
      this.value = toLocal(this.value);
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
    const local = toLocal(phoneIn ? phoneIn.value : '');
    if (!local) { alert('Enter phone number'); if (phoneIn) phoneIn.focus(); return; }

    // Must be a 10-digit PH mobile starting with 9 (the +63 is implied)
    if (!/^9\d{9}$/.test(local)) {
      alert('Invalid PH number');
      if (phoneIn) phoneIn.focus();
      return;
    }

    // Send in the 09XXXXXXXXX form the registration flow expects
    const mobile = '0' + local;

    const attr = getAttribution();
    const query = new URLSearchParams();
    query.set('mobile', mobile);
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

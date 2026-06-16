/**
 * Register / Sign Up popup modal.
 *
 * Markup lives in header.php (#fm-reg-modal). Any element with the class
 * `fm-open-register` (or the legacy id #fm-register-trigger) opens it.
 *
 * The same submit flow also powers the New User Welcome Bonus modal
 * (#fm-welcome-modal, markup in functions.php): its phone field + CTA reuse
 * the helpers below instead of opening the register modal.
 *
 * On submit: validates the terms checkbox (when present) + PH phone number,
 * reuses the attribution that attribution.js persisted to localStorage
 * (fm_attr), and redirects to the registration URL with `mobile=<number>` +
 * attribution.
 */
document.addEventListener('DOMContentLoaded', function () {

  const OPEN_CLASS = 'fm-open-register';
  const modal    = document.getElementById('fm-reg-modal');
  const closeBtn = document.getElementById('fm-reg-close');

  /* The +63 prefix is shown next to the field, so the input holds only the
     10-digit local number starting with 9 (9XXXXXXXXX). Strip any country
     code or leading 0 the user types/pastes. */
  function toLocal(value) {
    let v = (value || '').replace(/\D/g, '');
    if (v.startsWith('63')) v = v.slice(2);
    if (v.startsWith('0')) v = v.slice(1);
    return v.slice(0, 10);
  }

  /* Read attribution that attribution.js persisted on page load */
  function getAttribution() {
    const stored = localStorage.getItem('fm_attr') || localStorage.getItem('fm_attribution');
    try { return stored ? JSON.parse(stored) : {}; } catch (err) { return {}; }
  }

  function submitPhone(phoneIn, termsCk) {
    if (termsCk && !termsCk.checked) {
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

    // Always include the core UTM params (fall back to defaults when missing)
    query.set('utm_source', attr.utm_source || 'seo');
    query.set('utm_medium', attr.utm_medium || 'ggo');
    query.set('utm_campaign', attr.utm_campaign ||
      '2026_q2_fam_own_lfc_org_seo_ggo_fam-games-sub-seo-reg-bonus');

    // Optional UTM params — only when present
    if (attr.utm_term) query.set('utm_term', attr.utm_term);
    if (attr.utm_content) query.set('utm_content', attr.utm_content);
    if (attr.affiliate_id) query.set('affiliate_id', attr.affiliate_id);

    // Ad-click tracking — only when a click id was captured
    if (attr.click_field && attr.click_id) {
      query.set('click_field', attr.click_field);
      query.set('click_id', attr.click_id);
      query.set('click_platform', attr.click_platform);
    }

    window.location.href =
      'https://funalomax.com/en/profile/wallet?tab=deposit&' + query.toString();
  }

  /* Wire a phone input + submit button (+ optional terms checkbox) to the
     shared submit flow. Used by both the register and welcome bonus modals. */
  function wirePhoneForm(phoneIn, submit, termsCk) {
    if (phoneIn) {
      phoneIn.addEventListener('input', function () {
        this.value = toLocal(this.value);
      });
      phoneIn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); submitPhone(phoneIn, termsCk); }
      });
    }
    if (submit) submit.addEventListener('click', function () { submitPhone(phoneIn, termsCk); });
  }

  /* ── Register modal ── */
  if (modal) {
    function openModal() {
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('funalo-drawer-open');
      const phoneIn = document.getElementById('fm-reg-phone');
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

    wirePhoneForm(
      document.getElementById('fm-reg-phone'),
      document.getElementById('fm-reg-submit'),
      document.getElementById('fm-reg-terms')
    );
  }

  /* ── Welcome bonus modal (no terms checkbox) ── */
  wirePhoneForm(
    document.getElementById('fm-welcome-phone'),
    document.getElementById('fm-welcome-submit'),
    null
  );

});

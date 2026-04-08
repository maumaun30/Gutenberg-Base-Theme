document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('mobile-menu-toggle');
    const nav = document.getElementById('site-navigation');

    if (!toggle || !nav) {
        return;
    }

    toggle.addEventListener('click', function () {
        const isActive = nav.classList.toggle('is-active');
        toggle.setAttribute('aria-expanded', isActive ? 'true' : 'false');
    });
});
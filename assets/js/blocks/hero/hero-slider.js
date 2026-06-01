/**
 * Hero Slider — hero-slider.js
 * Enqueued via functions.php. Initialises all .hero-slider instances on the page.
 * No dependencies. Supports autoplay, dot nav, touch/swipe, pause on hover.
 */
(function () {
    'use strict';

    function initSlider(root) {
        var slides  = root.querySelectorAll('.hero-slider__slide');
        var dots    = root.querySelectorAll('.hero-slider__dot');
        var total   = slides.length;
        if (total < 2) return;

        var current = 0;
        var timer   = null;
        var delay   = parseInt(root.dataset.autoplay, 10) || 5000;

        function goTo(index) {
            slides[current].classList.remove('is-active');
            slides[current].setAttribute('aria-hidden', 'true');
            if (dots[current]) {
                dots[current].classList.remove('is-active');
                dots[current].setAttribute('aria-selected', 'false');
            }

            current = (index + total) % total;

            slides[current].classList.add('is-active');
            slides[current].setAttribute('aria-hidden', 'false');
            if (dots[current]) {
                dots[current].classList.add('is-active');
                dots[current].setAttribute('aria-selected', 'true');
            }
        }

        function start() {
            clearInterval(timer);
            timer = setInterval(function () { goTo(current + 1); }, delay);
        }

        function stop() { clearInterval(timer); }

        // Dot clicks
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                goTo(parseInt(dot.dataset.index, 10));
                start();
            });
        });

        // Pause on hover / focus
        root.addEventListener('mouseenter', stop);
        root.addEventListener('focusin',    stop);
        root.addEventListener('mouseleave', start);
        root.addEventListener('focusout',   start);

        // Touch / swipe
        var touchStartX = 0;
        root.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        root.addEventListener('touchend', function (e) {
            var diff = touchStartX - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 40) {
                goTo(diff > 0 ? current + 1 : current - 1);
                start();
            }
        }, { passive: true });

        start();
    }

    function initAll() {
        document.querySelectorAll('.hero-slider').forEach(function (el) {
            initSlider(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
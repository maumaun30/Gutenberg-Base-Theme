/**
 * Counter block — counts each value up from zero the first time it scrolls
 * into view. Only the numeric part is animated; prefixes/suffixes such as
 * "M+", "/7" or "★" are rendered as separate spans by render.php.
 */
(function () {
  var targets = document.querySelectorAll('.mytheme-counter__number[data-counter-target]');

  if (!targets.length) {
    return;
  }

  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function format(value, decimals) {
    return decimals > 0 ? value.toFixed(decimals) : String(Math.round(value));
  }

  function run(el) {
    if (el.dataset.counterDone === '1') {
      return;
    }
    el.dataset.counterDone = '1';

    var target = parseFloat(el.dataset.counterTarget);
    var decimals = parseInt(el.dataset.counterDecimals, 10) || 0;
    var duration = parseInt(el.dataset.counterDuration, 10) || 2000;

    if (isNaN(target)) {
      return;
    }

    if (prefersReducedMotion) {
      el.textContent = format(target, decimals);
      return;
    }

    var start = null;

    function step(timestamp) {
      if (start === null) {
        start = timestamp;
      }
      var progress = Math.min((timestamp - start) / duration, 1);
      // easeOutCubic, so the number decelerates into its final value
      var eased = 1 - Math.pow(1 - progress, 3);

      el.textContent = format(target * eased, decimals);

      if (progress < 1) {
        window.requestAnimationFrame(step);
      } else {
        el.textContent = format(target, decimals);
      }
    }

    el.textContent = format(0, decimals);
    window.requestAnimationFrame(step);
  }

  if (!('IntersectionObserver' in window)) {
    Array.prototype.forEach.call(targets, run);
    return;
  }

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          run(entry.target);
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.35 }
  );

  Array.prototype.forEach.call(targets, function (el) {
    observer.observe(el);
  });
})();

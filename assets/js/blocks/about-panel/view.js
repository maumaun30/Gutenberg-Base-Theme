/**
 * About Panel — Read More toggle.
 *
 * Scoped per block instance so several panels can sit on one page. The collapse
 * pins the current rendered height first and shrinks on the next two frames,
 * which gives the browser a clear start point so the transition runs at the
 * same speed in both directions (same approach as single-game.php).
 */
(function () {
  var panels = document.querySelectorAll('.mytheme-about');

  panels.forEach(function (panel) {
    var body = panel.querySelector('.mytheme-about__content.is-collapsible');
    var btn = panel.querySelector('.mytheme-about__toggle');

    if (!body || !btn) {
      return;
    }

    /* Collapsed height lives in the inline --about-collapsed custom property;
       resolve it once so the collapse animates back to the same value. */
    var collapsed =
      (body.style.getPropertyValue('--about-collapsed') || '').trim() || '8.5em';

    /* Content already fits — no need for the toggle at all */
    if (body.scrollHeight <= body.clientHeight + 4) {
      btn.style.display = 'none';
      body.style.maxHeight = 'none';
      return;
    }

    var isOpen = false;

    btn.addEventListener('click', function () {
      if (!isOpen) {
        body.style.maxHeight = body.scrollHeight + 'px';
        btn.textContent = btn.dataset.less;
        isOpen = true;
      } else {
        body.style.maxHeight = body.scrollHeight + 'px';
        requestAnimationFrame(function () {
          requestAnimationFrame(function () {
            body.style.maxHeight = collapsed;
          });
        });
        btn.textContent = btn.dataset.more;
        isOpen = false;
      }
    });
  });
})();

/**
 * Portrait Slider — initialises every slider on the page from the config
 * render.php puts in `data-portrait-slider`.
 *
 * Swiper is enqueued as a dependency (see mytheme_enqueue_portrait_slider_assets
 * in functions.php), so it is guaranteed to be defined by the time this runs.
 */
(function () {
  /**
   * Duplicates the slides until there are at least `minimum` of them, so loop
   * mode has enough to work with. The clones are hidden from assistive tech —
   * they are the same content already announced once.
   */
  function padSlidesForLoop(el, minimum) {
    var wrapper = el.querySelector('.swiper-wrapper');

    if (!wrapper) {
      return;
    }

    var originals = Array.prototype.slice.call(
      wrapper.querySelectorAll('.swiper-slide')
    );

    if (!originals.length || originals.length >= minimum) {
      return false;
    }

    while (wrapper.querySelectorAll('.swiper-slide').length < minimum) {
      originals.forEach(function (slide) {
        var clone = slide.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        wrapper.appendChild(clone);
      });
    }

    return true;
  }

  /**
   * How wide the travelling window of dots is, for sliders with more images
   * than DOT_CAP. Mirrors the slides in view so the dots read as the same
   * pattern — 5 on desktop, 3 below, where a phone showing one slide at a time
   * would say nothing with a single dot.
   *
   * Odd numbers only: the active dot is the middle one, which needs an equal
   * number either side.
   *
   * Keyed off the same 1024 breakpoint as the slides below.
   */
  function dotCountFor() {
    return window.innerWidth >= 1024 ? 5 : 3;
  }

  var DOT_TRANSITION = 350;

  /** Shortest signed step from `from` to `to` around a cycle of `total`. */
  function shortestStep(from, to, total) {
    var step = ((to - from + total) % total + total) % total;

    return step > total / 2 ? step - total : step;
  }

  /**
   * Past this many slides, one dot each stops being readable and the dots
   * switch to a fixed window that travels with the slider.
   */
  var DOT_CAP = 7;

  /**
   * Drives the pagination dots.
   *
   * Swiper's own pagination is not used: its `dynamicBullets` renders the main
   * bullets *plus* a shrinking one at each edge and lets the active bullet
   * drift off centre, and neither of the modes below is expressible with it.
   *
   * Up to DOT_CAP slides — the ordinary case — there is one dot per image and
   * the active pill travels along the row, so the dots answer "where am I, and
   * how much is left".
   *
   * Beyond that the row would be an unreadable smear, so it becomes a window of
   * dotCountFor() dots with the active one held in the middle. There the row is
   * built with a spare dot either side and slides by exactly one position per
   * change, the pill handing over to its neighbour as it moves; once the
   * movement settles the row is rebuilt at its resting offset, which looks
   * identical, ready for the next step.
   *
   * `total` is the number of images the editor added. Swiper's `realIndex` runs
   * over the padded set, so it is taken modulo `total` throughout.
   */
  function createDots(host, swiper, total, continuous) {
    var windowed = total > DOT_CAP;
    var track = document.createElement('div');
    var count;
    var middle;
    var pitch;
    var index;
    var settle;

    track.className =
      'mytheme-portrait-slider__dots' + (windowed ? ' is-windowed' : '');
    host.appendChild(track);

    /** Which image is on screen, whichever copy of it Swiper is showing. */
    function activeImage() {
      return swiper.realIndex % total;
    }

    function dotMarkup(target, isActive) {
      return (
        '<span class="mytheme-portrait-slider__dot-slot">' +
        '<button type="button" class="mytheme-portrait-slider__dot' +
        (isActive ? ' is-active' : '') +
        '" data-slide="' + target + '"' +
        (isActive ? ' aria-current="true"' : '') +
        ' aria-label="Go to slide ' + (target + 1) + '"></button>' +
        '</span>'
      );
    }

    /** Positions the row; `slot` 1 is the resting offset. Windowed mode only. */
    function offsetTo(slot, animate) {
      track.classList.toggle('is-animating', !!animate);
      track.style.transform = 'translateX(' + -slot * pitch + 'px)';
    }

    function build() {
      var html = '';
      var i;

      index = activeImage();

      if (!windowed) {
        for (i = 0; i < total; i += 1) {
          html += dotMarkup(i, i === index);
        }

        track.innerHTML = html;
        return;
      }

      count = dotCountFor();
      middle = Math.floor(count / 2);

      // One spare dot at each end, so sliding by a position never opens a gap.
      for (i = -1; i <= count; i += 1) {
        html += dotMarkup(
          (((index + (i - middle)) % total) + total) % total,
          i === middle
        );
      }

      track.innerHTML = html;

      // Measured rather than hard-coded, so the spacing stays in the stylesheet.
      pitch = track.children[1].offsetLeft - track.children[0].offsetLeft;
      host.style.width = count * pitch + 'px';
      offsetTo(1, false);
    }

    function setActive(position) {
      var slots = track.children;

      Array.prototype.forEach.call(slots, function (slot) {
        slot.firstChild.classList.remove('is-active');
        slot.firstChild.removeAttribute('aria-current');
      });

      slots[position].firstChild.classList.add('is-active');
      slots[position].firstChild.setAttribute('aria-current', 'true');
    }

    function slideChanged() {
      var next = activeImage();
      // Only a loop can step from the last slide to the first; without one the
      // distance is just the difference.
      var step = continuous
        ? shortestStep(index, next, total)
        : next - index;

      if (step === 0) {
        return;
      }

      // One dot per image: the pill simply moves to the dot for the new image,
      // and the width and colour transitions carry it there.
      if (!windowed) {
        index = next;
        setActive(index);
        return;
      }

      // Anything but a single step (a dot click, a dragged flick) has no
      // one-position movement to show, so it snaps to the new window instead.
      if (Math.abs(step) !== 1 || settle) {
        clearTimeout(settle);
        settle = null;
        build();
        return;
      }

      index = next;
      setActive(1 + middle + step);
      offsetTo(1 + step, true);

      // Rebuilding puts the row back at its resting offset with the new window
      // already in place — visually identical to where the movement ended.
      settle = setTimeout(function () {
        settle = null;
        build();
      }, DOT_TRANSITION);
    }

    host.addEventListener('click', function (event) {
      var dot = event.target.closest('[data-slide]');

      if (!dot) {
        return;
      }

      var target = Number(dot.getAttribute('data-slide'));

      // slideToLoop addresses a slide by its real index among the copies; with
      // no loop there are no copies and the plain index is the one to use.
      if (continuous) {
        swiper.slideToLoop(target);
      } else {
        swiper.slideTo(target);
      }
    });

    // `realIndexChange` rather than `slideChange`: while looping, Swiper
    // silently teleports between the real slides and their copies, which fires
    // slideChange with an index the dots should not follow.
    swiper.on('realIndexChange', slideChanged);

    // A backstop once everything has come to rest. Dragging can settle
    // somewhere the change events did not describe as a clean step — a flick
    // that crosses several slides, or one released mid-transition — and this
    // catches the dots up rather than leaving them out of sync until the next
    // move.
    swiper.on('transitionEnd', function () {
      if (!settle && index !== activeImage()) {
        build();
      }
    });

    // Only the windowed mode's count depends on the slider's width.
    if (windowed) {
      var lastCount = null;

      swiper.on('resize', function () {
        if (dotCountFor() !== lastCount) {
          lastCount = dotCountFor();
          build();
        }
      });

      build();
      lastCount = count;

      return;
    }

    build();
  }

  /**
   * Runs autoplay only while the slider is actually on screen.
   *
   * A slider further down the page would otherwise have cycled through several
   * slides before the visitor ever reaches it, so they arrive part-way through
   * with no idea it started at the first image. Stopping it again on the way
   * out also keeps a long page from running timers for sliders nobody is
   * looking at.
   *
   * Where IntersectionObserver is missing the slider simply keeps Swiper's own
   * behaviour and plays from load.
   */
  function watchInView(el, swiper) {
    if (typeof window.IntersectionObserver !== 'function') {
      return;
    }

    swiper.autoplay.stop();

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            swiper.autoplay.start();
          } else {
            swiper.autoplay.stop();
          }
        });
      },
      // A quarter of the slider showing is enough to count as arrived — waiting
      // for all of it would never fire for one taller than the viewport.
      { threshold: 0.25 }
    );

    observer.observe(el);
  }

  function init() {
    var sliders = document.querySelectorAll('[data-portrait-slider]');

    Array.prototype.forEach.call(sliders, function (el) {
      var config = {};

      try {
        config = JSON.parse(el.getAttribute('data-portrait-slider')) || {};
      } catch (e) {
        config = {};
      }

      // The images the editor actually added, counted before anything is
      // cloned. This — not the padded total, and not Swiper's own `slides`
      // collection once it is looping — is what the dots represent.
      var slideCount = el.querySelectorAll('.swiper-slide').length;

      var continuous = config.continuous !== false;

      // Loop mode needs materially more slides than are on screen at once —
      // with 5 slides shown 5-up Swiper turns looping off, which is what makes
      // the strip stop part-way instead of running continuously. Cloning the
      // set until there is enough to loop keeps it seamless at every
      // breakpoint (5 desktop / 3 tablet / 1 phone). The clones repeat the
      // originals in order, so slide n always shows image n % slideCount.
      //
      // Only when looping: without it the clones would show as extra slides to
      // scroll past on the way to an end that never arrives.
      if (continuous) {
        padSlidesForLoop(el, 5 * 2 + 2);
      }

      var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      var options = {
        // 1 up on phones, 3 on tablet, 5 on desktop. On a phone the slide takes
        // the full container width — a fractional value there only shrinks the
        // portrait art and leaves it looking off-centre.
        slidesPerView: 1,
        spaceBetween: 16,
        centeredSlides: config.centered !== false,
        loop: continuous,
        loopAdditionalSlides: 2,
        grabCursor: true,
        speed: config.speed || 700,
        watchSlidesProgress: true,
        // Recalculate if the slider changes size (fonts loading, an ancestor
        // animating in) rather than only on window resize.
        observer: true,
        // Deliberately off. The dots are re-rendered on every slide change and
        // live in the same section, so observing ancestors made each render
        // trigger a Swiper update — which during a loop can move the slider
        // under the dots and leave the two disagreeing.
        observeParents: false,
        // Viewport width, not the slider's own: the slider is held to a 1280
        // container, so at a 1024 viewport it measures ~976 and a
        // container-based 1024 breakpoint would never fire. These match the
        // media queries in the stylesheet.
        breakpoints: {
          768: {
            slidesPerView: 3,
            spaceBetween: 20,
          },
          1024: {
            slidesPerView: 5,
            spaceBetween: 24,
          },
        },
      };

      if (config.autoplay && !reduceMotion) {
        options.autoplay = {
          delay: config.delay || 3000,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
          // Without a loop, coming to rest at the end is the point — otherwise
          // autoplay would snap back to the first slide and keep going, which
          // is the looping behaviour by another name.
          stopOnLastSlide: !continuous,
        };
      }

      // The bullets sit outside the .swiper element (see render.php), so they
      // are looked up by the id render.php derives from the slider's own — a
      // page with several sliders must not have them share one element.
      var pagination = el.id
        ? document.getElementById(el.id + '-pagination')
        : null;

      // Keyboard and screen-reader support come from Swiper's own modules.
      options.keyboard = { enabled: true, onlyInViewport: true };
      options.a11y = { enabled: true };

      var swiper = new window.Swiper(el, options);

      if (config.pagination && pagination) {
        createDots(pagination, swiper, slideCount, continuous);
      }

      if (options.autoplay) {
        watchInView(el, swiper);
      }

      // Reveals the slider; until now the markup is held in its pre-init state
      // so the raw, full-size images never flash on screen.
      el.classList.add('is-ready');
    });
  }

  /**
   * Swiper is loaded from a CDN and may not have executed yet — on a first,
   * uncached visit, or when a plugin defers scripts. Waiting for it here is
   * what stops the slider rendering as a row of unsized images.
   */
  function whenSwiperReady(callback) {
    if (typeof window.Swiper !== 'undefined') {
      callback();
      return;
    }

    var waited = 0;
    var timer = setInterval(function () {
      waited += 50;

      if (typeof window.Swiper !== 'undefined') {
        clearInterval(timer);
        callback();
      } else if (waited >= 10000) {
        // Give up gracefully: show the slides as a plain scrollable strip
        // rather than leaving them hidden.
        clearInterval(timer);
        Array.prototype.forEach.call(
          document.querySelectorAll('[data-portrait-slider]'),
          function (el) {
            el.classList.add('is-ready', 'is-fallback');
          }
        );
      }
    }, 50);
  }

  function start() {
    whenSwiperReady(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();

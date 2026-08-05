/**
 * FAQ block — smooth open/close.
 *
 * A native <details> jumps straight to its final height, so the panel is
 * animated here with the Web Animations API: the element's height is tweened
 * between the summary height and the full height, and `open` is only flipped at
 * the point that keeps the content visible for the whole animation.
 *
 * With JavaScript off, the accordion still works — it just snaps, as a plain
 * <details> does.
 */
(function () {
  var items = document.querySelectorAll('.mytheme-faq__item');

  if (!items.length || typeof Element.prototype.animate !== 'function') {
    return;
  }

  var DURATION = 320;
  var EASING = 'cubic-bezier(0.33, 0.0, 0.2, 1)'; // quick out, soft landing

  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  function setup(details) {
    var summary = details.querySelector('.mytheme-faq__question');

    if (!summary) {
      return;
    }

    var animation = null;
    var isClosing = false;
    var isExpanding = false;

    function onFinish(open) {
      details.open = open;
      details.classList.remove('is-closing');
      animation = null;
      isClosing = false;
      isExpanding = false;
      // Hand height control back to the stylesheet.
      details.style.height = '';
      details.style.overflow = '';
    }

    function animateHeight(from, to, open) {
      details.style.overflow = 'hidden';

      if (animation) {
        animation.cancel();
      }

      animation = details.animate(
        { height: [from + 'px', to + 'px'] },
        { duration: DURATION, easing: EASING }
      );

      animation.onfinish = function () {
        onFinish(open);
      };
      animation.oncancel = function () {
        details.classList.remove('is-closing');
        isClosing = false;
        isExpanding = false;
      };
    }

    function expand() {
      isExpanding = true;
      // Cancels a close that was still running, so the icon follows the
      // direction the panel is actually going.
      details.classList.remove('is-closing');
      var start = details.offsetHeight;
      // `open` has to be set before measuring, otherwise the panel has no height.
      details.open = true;
      var end = summary.offsetHeight + getPanelHeight();

      animateHeight(start, end, true);
    }

    function collapse() {
      isClosing = true;
      // `open` has to stay set until the panel has finished shrinking, or the
      // content would vanish mid-animation. This marks the intent right away so
      // the icon can return to a plus with the panel rather than after it.
      details.classList.add('is-closing');
      animateHeight(details.offsetHeight, summary.offsetHeight, false);
    }

    function getPanelHeight() {
      var panel = details.querySelector('.mytheme-faq__answer');
      if (!panel) {
        return 0;
      }
      var styles = window.getComputedStyle(panel);
      return (
        panel.offsetHeight +
        parseFloat(styles.marginTop || 0) +
        parseFloat(styles.marginBottom || 0)
      );
    }

    // Closes any sibling the browser would otherwise snap shut instantly when
    // the group uses the exclusive `name` attribute.
    function closeGroupSiblings() {
      var name = details.getAttribute('name');
      if (!name) {
        return;
      }

      var siblings = document.querySelectorAll(
        '.mytheme-faq__item[name="' + name + '"]'
      );

      Array.prototype.forEach.call(siblings, function (other) {
        if (other !== details && other.open && other.__faqCollapse) {
          other.__faqCollapse();
        }
      });
    }

    // Exposed so a sibling opening can animate this one shut.
    details.__faqCollapse = collapse;

    summary.addEventListener('click', function (event) {
      if (prefersReducedMotion()) {
        return; // let the browser toggle it instantly
      }

      event.preventDefault();

      if (isClosing || !details.open) {
        closeGroupSiblings();
        expand();
      } else if (isExpanding || details.open) {
        collapse();
      }
    });
  }

  Array.prototype.forEach.call(items, setup);
})();

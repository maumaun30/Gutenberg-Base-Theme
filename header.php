<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>


  <nav class="funalo-nav" role="navigation" aria-label="Main navigation">

    <div class="funalo-nav__inner">

      <!-- Logo -->
      <div class="funalo-nav__logo">
        <a href="<?php echo esc_url(home_url('/')); ?>">
          <?php if (has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
          <?php else : ?>
            <span class="funalo-nav__logo-text"><?php bloginfo('name'); ?></span>
          <?php endif; ?>
        </a>
      </div>

      <!-- Primary Nav (desktop) -->
      <div style="flex:1; display:flex; align-items:center;">
        <?php
        wp_nav_menu([
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => 'funalo-nav__primary',
          'fallback_cb'    => function () {
            echo '<ul class="funalo-nav__primary"><li><a href="/">Home</a></li></ul>';
          },
        ]);
        ?>
      </div>

      <!-- Right: Secondary nav + CTA (desktop) -->
      <div class="funalo-nav__right">

        <?php
        wp_nav_menu([
          'theme_location' => 'secondary',
          'container'      => false,
          'menu_class'     => 'funalo-nav__secondary',
          'fallback_cb'    => false,
        ]);
        ?>

        <div class="funalo-nav__cta" id="fm-register-trigger">
          <a href="<?php echo esc_url('https://funalomax.com/'); ?>" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e('Login / Register', 'luxe'); ?>
          </a>
          <svg aria-hidden="true" class="funalo-nav__cta-shape" viewBox="0 0 124 32" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#_R_mcnuliivb_)">
              <path d="M124 20.4 L112.4 32 H0 V7 L7 0 H124 V20.4 Z" fill="currentColor"></path>
              <path d="M124 24 V32 H116 L124 24 Z" fill="var(--decoration, currentColor)"></path>
            </g>
            <defs>
              <clipPath id="_R_mcnuliivb_">
                <rect width="124" height="32" fill="white"></rect>
              </clipPath>
            </defs>
          </svg>
        </div>

        <!-- Search Icon Button -->
        <button class="funalo-nav__search-btn" id="funalo-search-btn" aria-label="Open search">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <svg aria-hidden="true" class="funalo-nav__cta-shape" viewBox="0 0 32 32" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#_R_ucnuliivb_)">
              <path d="M32 20.4 L20.4 32 H0 V7 L7 0 H32 V20.4 Z" fill="currentColor"></path>
              <path d="M32 24 V32 H24 L32 24 Z" fill="var(--decoration, currentColor)"></path>
            </g>
            <defs>
              <clipPath id="_R_ucnuliivb_">
                <rect width="32" height="32" fill="white"></rect>
              </clipPath>
            </defs>
          </svg>
        </button>

        <!-- Hamburger (mobile) -->
        <button class="funalo-nav__hamburger" id="funalo-hamburger"
          aria-label="Toggle mobile menu" aria-expanded="false">
          <!-- Shape background (same as search button) -->
          <svg aria-hidden="true" class="funalo-nav__cta-shape" viewBox="0 0 32 32" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#_R_hcnuliivb_)">
              <path d="M32 20.4 L20.4 32 H0 V7 L7 0 H32 V20.4 Z" fill="currentColor" />
              <path d="M32 24 V32 H24 L32 24 Z" fill="var(--decoration, currentColor)" />
            </g>
            <defs>
              <clipPath id="_R_hcnuliivb_">
                <rect width="32" height="32" fill="white" />
              </clipPath>
            </defs>
          </svg>
          <!-- Bars icon -->
          <svg class="funalo-nav__hamburger-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
          </svg>
          <!-- Close icon -->
          <svg class="funalo-nav__hamburger-close" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>

      </div>

    </div><!-- /.funalo-nav__inner -->

  </nav>

  <!-- Backdrop -->
  <div class="funalo-drawer-backdrop" id="funalo-backdrop" aria-hidden="true"></div>

  <!-- Mobile Drawer (slides in from right) -->
  <div class="funalo-nav__mobile" id="funalo-mobile-menu"
    role="dialog" aria-modal="true" aria-label="Mobile navigation" aria-hidden="true">

    <!-- Drawer Header: Logo + Close -->
    <div class="funalo-drawer__header">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="drawer-logo">
        <?php if (has_custom_logo()) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <span class="drawer-logo-text"><?php bloginfo('name'); ?></span>
        <?php endif; ?>
      </a>
      <button class="funalo-drawer__close" id="funalo-drawer-close" aria-label="Close menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>

    <!-- Drawer Nav Links -->
    <nav class="funalo-drawer__nav" aria-label="Mobile menu">
      <?php
      wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'fallback_cb'    => function () {
          echo '<ul><li><a href="/">Home</a></li></ul>';
        },
      ]);

      wp_nav_menu([
        'theme_location' => 'secondary',
        'container'      => false,
        'fallback_cb'    => false,
      ]);
      ?>
    </nav>



  </div>

  <!-- ── Full-Screen Search Overlay ── -->
  <div class="funalo-search-overlay" id="funalo-search-overlay" aria-hidden="true" role="dialog" aria-label="Search">
    <div class="funalo-search-overlay__inner">

      <button class="funalo-search-overlay__close" id="funalo-search-close" aria-label="Close search">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>

      <div class="funalo-search-overlay__content">
        <p class="funalo-search-overlay__label"><?php esc_html_e('What are you looking for?', 'luxe'); ?></p>
        <form role="search" method="get" class="funalo-search-overlay__form" action="<?php echo esc_url(home_url('/')); ?>">
          <label class="screen-reader-text" for="funalo-search-field"><?php esc_html_e('Search for:', 'luxe'); ?></label>
          <input
            type="search"
            id="funalo-search-field"
            class="funalo-search-overlay__field"
            name="s"
            value="<?php echo esc_attr(get_search_query()); ?>"
            placeholder="<?php esc_attr_e('', 'luxe'); ?>"
            autocomplete="off" />
          <button type="submit" class="funalo-search-overlay__submit" aria-label="<?php esc_attr_e('Search', 'luxe'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
          </button>
        </form>
      </div>

    </div>
  </div>

  <!-- ── Register / Sign Up Modal ── -->
  <div class="fm-reg-modal" id="fm-reg-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Register">
    <div class="fm-reg-modal__card" role="document">

      <!-- Close (chamfered red button) -->
      <button class="fm-reg-modal__close" id="fm-reg-close" type="button" aria-label="Close">
        <svg aria-hidden="true" class="fm-reg-modal__close-shape" viewBox="0 0 32 32" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g clip-path="url(#fm-reg-close-shape)">
            <path d="M32 20.4 L20.4 32 H0 V7 L7 0 H32 V20.4 Z" fill="currentColor"></path>
            <path d="M32 24 V32 H24 L32 24 Z" fill="var(--decoration, #ffffff)"></path>
          </g>
          <defs><clipPath id="fm-reg-close-shape"><rect width="32" height="32" fill="white"></rect></clipPath></defs>
        </svg>
        <svg class="fm-reg-modal__close-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>

      <!-- Brand -->
      <div class="fm-reg-modal__brand">
        <?php if (has_custom_logo()) : the_custom_logo(); else : ?>
          <span class="fm-reg-modal__brand-text"><?php bloginfo('name'); ?></span>
        <?php endif; ?>
      </div>

      <h3 class="fm-reg-modal__title">
        <?php esc_html_e('JOIN THE ACTION', 'luxe'); ?>
        <span class="fm-reg-modal__title-accent"><?php esc_html_e('TODAY', 'luxe'); ?></span>
      </h3>
      <p class="fm-reg-modal__sub"><?php esc_html_e('Get instant access to exciting casino games anytime, anywhere.', 'luxe'); ?></p>

      <!-- Phone field: icon + +63 prefix + local number -->
      <div class="fm-reg-modal__field">
        <svg class="fm-reg-modal__field-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" />
        </svg>
        <span class="fm-reg-modal__cc">+63</span>
        <input type="tel" id="fm-reg-phone" class="fm-reg-modal__input"
          placeholder="09XX XXX XXXX" inputmode="numeric" autocomplete="tel">
      </div>

      <!-- Submit (chamfered red button) -->
      <button type="button" id="fm-reg-submit" class="fm-reg-modal__submit">
        <svg aria-hidden="true" class="fm-reg-modal__submit-shape" viewBox="0 0 148 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g clip-path="url(#fm-reg-submit-shape)">
            <path d="M148 30.4 L136.4 42 H0 V7 L7 0 H148 V30.4 Z" fill="currentColor"></path>
            <path d="M148 34 V42 H140 L148 34 Z" fill="var(--decoration, #ffffff)"></path>
          </g>
          <defs><clipPath id="fm-reg-submit-shape"><rect width="148" height="42" fill="white"></rect></clipPath></defs>
        </svg>
        <span class="fm-reg-modal__submit-label"><?php esc_html_e('Login / Register', 'luxe'); ?></span>
      </button>

      <label class="fm-reg-modal__terms" for="fm-reg-terms">
        <input type="checkbox" id="fm-reg-terms" checked>
        <span>
          <?php esc_html_e('I Agree To The', 'luxe'); ?>
          <a href="<?php echo esc_url('https://funalomax.com/en'); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Terms Of Use', 'luxe'); ?></a>
          <?php esc_html_e('And', 'luxe'); ?>
          <a href="<?php echo esc_url('https://funalomax.com/en'); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Privacy Policy', 'luxe'); ?></a>
        </span>
      </label>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {

      /* ── Mobile Drawer ── */
      const hamburger = document.getElementById('funalo-hamburger');
      const drawer = document.getElementById('funalo-mobile-menu');
      const backdrop = document.getElementById('funalo-backdrop');
      const closeBtn = document.getElementById('funalo-drawer-close');

      function openDrawer() {
        drawer.classList.add('is-open');
        if (backdrop) backdrop.classList.add('is-open');
        hamburger.classList.add('is-open');
        hamburger.setAttribute('aria-expanded', 'true');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.classList.add('funalo-drawer-open');
      }

      function closeDrawer() {
        drawer.classList.remove('is-open');
        if (backdrop) backdrop.classList.remove('is-open');
        hamburger.classList.remove('is-open');
        hamburger.setAttribute('aria-expanded', 'false');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('funalo-drawer-open');
      }

      if (hamburger && drawer) {
        hamburger.addEventListener('click', function() {
          drawer.classList.contains('is-open') ? closeDrawer() : openDrawer();
        });
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
        if (backdrop) backdrop.addEventListener('click', closeDrawer);
      }

      /* ── Search Overlay ── */
      const searchBtn = document.getElementById('funalo-search-btn');
      const searchOverlay = document.getElementById('funalo-search-overlay');
      const searchClose = document.getElementById('funalo-search-close');
      const searchInput = searchOverlay ? searchOverlay.querySelector('input[type="search"], input[name="s"]') : null;

      function openSearch() {
        searchOverlay.classList.add('is-open');
        searchOverlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('funalo-drawer-open');
        if (searchInput) setTimeout(() => searchInput.focus(), 200);
      }

      function closeSearch() {
        searchOverlay.classList.remove('is-open');
        searchOverlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('funalo-drawer-open');
      }

      if (searchBtn) searchBtn.addEventListener('click', openSearch);
      if (searchClose) searchClose.addEventListener('click', closeSearch);

      /* ── Escape key closes either ── */
      document.addEventListener('keydown', function(e) {
        if (e.key !== 'Escape') return;
        if (drawer && drawer.classList.contains('is-open')) closeDrawer();
        if (searchOverlay && searchOverlay.classList.contains('is-open')) closeSearch();
      });

    });
  </script>
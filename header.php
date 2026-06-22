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
        <a href="https://funalomax.com/en?utm_source=seo&utm_medium=ggo&utm_campaign=2026_q2_fam_own_lfc_org_seo_ggo_fam-games-sub-seo">
          <?php if (has_custom_logo()) : ?>
            <?php // Output the logo image only — the_custom_logo() would wrap it in its own
                  // <a> pointing to the homepage, overriding the link above. ?>
            <?php echo wp_get_attachment_image(get_theme_mod('custom_logo'), 'full', false, ['class' => 'custom-logo']); ?>
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

        <div class="funalo-nav__cta" id="fm-register-trigger" role="button" tabindex="0">
          <a>
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
      <a href="https://funalomax.com/en?utm_source=seo&utm_medium=ggo&utm_campaign=2026_q2_fam_own_lfc_org_seo_ggo_fam-games-sub-seo" class="drawer-logo">
        <?php if (has_custom_logo()) : ?>
          <?php // Output the logo image only — the_custom_logo() would wrap it in its own
                // <a> pointing to the homepage, overriding the link above. ?>
          <?php echo wp_get_attachment_image(get_theme_mod('custom_logo'), 'full', false, ['class' => 'custom-logo']); ?>
        <?php else : ?>
          <span class="drawer-logo-text"><?php bloginfo('name'); ?></span>
        <?php endif; ?>
      </a>
      <button class="funalo-drawer__close" id="funalo-drawer-close" aria-label="Close menu">
        <!-- Chamfered shape background (same as header buttons) -->
        <svg aria-hidden="true" class="funalo-nav__cta-shape" viewBox="0 0 32 32" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g clip-path="url(#_R_drawerclose_)">
            <path d="M32 20.4 L20.4 32 H0 V7 L7 0 H32 V20.4 Z" fill="currentColor" />
            <path d="M32 24 V32 H24 L32 24 Z" fill="var(--decoration, currentColor)" />
          </g>
          <defs>
            <clipPath id="_R_drawerclose_">
              <rect width="32" height="32" fill="white" />
            </clipPath>
          </defs>
        </svg>
        <svg class="funalo-drawer__close-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
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

    <!-- Drawer CTA: Login / Register (same chamfered shape as header CTA) -->
    <div class="funalo-drawer__cta" id="fm-drawer-register-trigger">
      <a href="<?php echo esc_url('https://funalomax.com/'); ?>" class="fm-open-register" target="_blank" rel="noopener noreferrer">
        <?php esc_html_e('Login / Register', 'luxe'); ?>
      </a>
      <svg aria-hidden="true" class="funalo-nav__cta-shape" viewBox="0 0 408 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#_R_drawercta_)">
                <path d="M408 30.4 L396.4 42 H0 V7 L7 0 H408 V30.4 Z" fill="currentColor"></path>
                <path d="M408 34 V42 H400 L408 34 Z" fill="var(--decoration, #ffffff)"></path>
            </g>
            <defs><clipPath id="_R_drawercta_"><rect width="408" height="42" fill="white"></rect></clipPath></defs>
        </svg>
      
      <!--<svg aria-hidden="true" class="funalo-nav__cta-shape" viewBox="0 0 124 32" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g clip-path="url(#_R_drawercta_)">
          <path d="M124 20.4 L112.4 32 H0 V7 L7 0 H124 V20.4 Z" fill="currentColor"></path>
          <path d="M124 24 V32 H116 L124 24 Z" fill="var(--decoration, currentColor)"></path>
        </g>
        <defs>
          <clipPath id="_R_drawercta_">
            <rect width="124" height="32" fill="white"></rect>
          </clipPath>
        </defs>
      </svg>-->
    </div>

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
        // Close the drawer when the in-drawer Login/Register opens the modal
        const drawerCta = document.getElementById('fm-drawer-register-trigger');
        if (drawerCta) drawerCta.addEventListener('click', closeDrawer);
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
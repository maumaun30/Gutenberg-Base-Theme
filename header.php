<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ── Responsible Gaming Popup ── -->
<div class="fnlmx-rg-popup" id="fnlmx-rg-popup" aria-modal="true" role="dialog" aria-label="Responsible Gaming Guidelines" aria-hidden="true">
  <div class="fnlmx-rg-popup__backdrop"></div>

  <div class="fnlmx-rg-popup__panel">

    <h2 class="fnlmx-rg-popup__title"><?php esc_html_e('Responsible Gaming Guidelines', 'luxe'); ?></h2>

    <div class="fnlmx-rg-popup__body">
      <ul class="fnlmx-rg-popup__list">
        <li><?php esc_html_e('I am over 21 years of age.', 'luxe'); ?></li>
        <li><?php esc_html_e('I am not a government official, or employee connected directly with the operation of the government or any of its agencies.', 'luxe'); ?></li>
        <li><?php esc_html_e('I am not a member of the Armed Forces of the Philippines, including the Army, Navy, Air Force, or the Philippine National Police.', 'luxe'); ?></li>
        <li><?php esc_html_e('I am not a holder of a Gaming Employment License (GEL).', 'luxe'); ?></li>
        <li><?php esc_html_e('Playing in open and public places is prohibited.', 'luxe'); ?></li>
        <li><a href="<?php echo esc_url( home_url('/self-exclude/') ); ?>"><?php esc_html_e('To Self-Exclude (Self-Ban)', 'luxe'); ?></a></li>
        <li><a href="https://www.pagcor.ph/responsible-gaming.php" target="_blank" rel="noopener noreferrer"><?php esc_html_e("To Know More About PAGCOR's Responsible Gaming Program", 'luxe'); ?></a></li>
        <li><a href="<?php echo esc_url( home_url('/gaming-support/') ); ?>"><?php esc_html_e('For Gaming Support Helplines', 'luxe'); ?></a></li>
      </ul>

      <!-- PAGCOR + 21 badges -->
      <div class="fnlmx-rg-popup__badges">
        <div class="fnlmx-rg-popup__badge-item">
          <?php
            $pagcor_img = function_exists('get_field') ? get_field('fnlmx_pagcor_badge', 'option') : null;
            if ( $pagcor_img && is_array($pagcor_img) ) :
          ?>
            <img src="<?php echo esc_url($pagcor_img['url']); ?>" alt="PAGCOR" loading="lazy">
          <?php else : ?>
            <span class="fnlmx-rg-popup__badge-fallback">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="#F71DC2"><circle cx="12" cy="12" r="10"/></svg>
              PAGCOR
            </span>
          <?php endif; ?>
        </div>
        <div class="fnlmx-rg-popup__badge-divider"></div>
        <div class="fnlmx-rg-popup__badge-item">
          <?php
            $badge21_img = function_exists('get_field') ? get_field('fnlmx_21_badge', 'option') : null;
            if ( $badge21_img && is_array($badge21_img) ) :
          ?>
            <img src="<?php echo esc_url($badge21_img['url']); ?>" alt="21+ Know When To Stop" loading="lazy">
          <?php else : ?>
            <span class="fnlmx-rg-popup__badge-fallback fnlmx-rg-popup__badge-fallback--21">21+</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Disclaimer -->
      <p class="fnlmx-rg-popup__disclaimer">
        <?php esc_html_e('Funds or credits in the account of player who is found ineligible to play shall mean forfeiture of said funds/credits in favor of the Government.', 'luxe'); ?>
      </p>
    </div>

    <!-- Actions -->
    <div class="fnlmx-rg-popup__actions">
      <button class="fnlmx-rg-popup__btn fnlmx-rg-popup__btn--exit" id="fnlmx-rg-exit">
        <?php esc_html_e('Exit', 'luxe'); ?>
      </button>
      <button class="fnlmx-rg-popup__btn fnlmx-rg-popup__btn--proceed" id="fnlmx-rg-proceed">
        <?php esc_html_e('Proceed', 'luxe'); ?>
      </button>
    </div>

  </div>
</div>


<nav class="funalo-nav" role="navigation" aria-label="Main navigation">

  <div class="funalo-nav__inner">

    <!-- Logo -->
    <div class="funalo-nav__logo">
      <a href="<?php echo esc_url( home_url('/') ); ?>">
        <?php if ( has_custom_logo() ) : ?>
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
          'fallback_cb'    => function() {
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

      <div class="funalo-nav__cta">
        <a href="<?php echo esc_url( home_url('/join-now/') ); ?>">
          <?php esc_html_e('Login / Register', 'luxe'); ?>
        </a>
      </div>

      <!-- Search Icon Button -->
      <button class="funalo-nav__search-btn" id="funalo-search-btn" aria-label="Open search">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
      </button>

    </div>

    <!-- Hamburger (mobile) -->
    <button class="funalo-nav__hamburger" id="funalo-hamburger"
            aria-label="Toggle mobile menu" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
    </button>

  </div><!-- /.funalo-nav__inner -->

</nav>

<!-- Backdrop -->
<div class="funalo-drawer-backdrop" id="funalo-backdrop" aria-hidden="true"></div>

<!-- Mobile Drawer (slides in from right) -->
<div class="funalo-nav__mobile" id="funalo-mobile-menu"
     role="dialog" aria-modal="true" aria-label="Mobile navigation" aria-hidden="true">

  <!-- Drawer Header: Logo + Close -->
  <div class="funalo-drawer__header">
    <a href="<?php echo esc_url( home_url('/') ); ?>" class="drawer-logo">
      <?php if ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
      <?php else : ?>
        <span class="drawer-logo-text"><?php bloginfo('name'); ?></span>
      <?php endif; ?>
    </a>
    <button class="funalo-drawer__close" id="funalo-drawer-close" aria-label="Close menu">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>

  <!-- Drawer Nav Links -->
  <nav class="funalo-drawer__nav" aria-label="Mobile menu">
    <?php
      wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'fallback_cb'    => function() {
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

  <!-- Drawer CTA -->
  <div class="funalo-drawer__cta">
    <a href="<?php echo esc_url( home_url('/join-now/') ); ?>">
      <?php esc_html_e('Login / Register', 'luxe'); ?>
    </a>
  </div>

</div>

<!-- ── Full-Screen Search Overlay ── -->
<div class="funalo-search-overlay" id="funalo-search-overlay" aria-hidden="true" role="dialog" aria-label="Search">
  <div class="funalo-search-overlay__inner">

    <button class="funalo-search-overlay__close" id="funalo-search-close" aria-label="Close search">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>

    <div class="fnlmx-search-overlay__content">
      <p class="funalo-search-overlay__label"><?php esc_html_e('What are you looking for?', 'luxe'); ?></p>
      <?php get_search_form(); ?>
    </div>

  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {

    /* ══════════════════════════════════════
       RESPONSIBLE GAMING POPUP
    ══════════════════════════════════════ */
    const rgPopup   = document.getElementById('fnlmx-rg-popup');
    const rgProceed = document.getElementById('fnlmx-rg-proceed');
    const rgExit    = document.getElementById('fnlmx-rg-exit');

    if (rgPopup && !sessionStorage.getItem('fnlmx_rg_accepted')) {
      // Small delay so page renders first
      setTimeout(function () {
        rgPopup.classList.add('is-open');
        rgPopup.setAttribute('aria-hidden', 'false');
        document.body.classList.add('funalo-drawer-open');
      }, 300);
    }

    if (rgProceed) {
      rgProceed.addEventListener('click', function () {
        sessionStorage.setItem('fnlmx_rg_accepted', '1');
        rgPopup.classList.remove('is-open');
        rgPopup.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('funalo-drawer-open');
      });
    }

    if (rgExit) {
      rgExit.addEventListener('click', function () {
        if (document.referrer && document.referrer !== window.location.href) {
          window.location.href = document.referrer;
        } else {
          // No referrer — try close tab, fallback to blank
          window.close();
          setTimeout(function () {
            window.location.replace('about:blank');
          }, 300);
        }
      });
    }

    /* ══════════════════════════════════════
       MOBILE DRAWER
    ══════════════════════════════════════ */
    const hamburger = document.getElementById('funalo-hamburger');
    const drawer    = document.getElementById('funalo-mobile-menu');
    const backdrop  = document.getElementById('funalo-backdrop');
    const closeBtn  = document.getElementById('funalo-drawer-close');

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
      hamburger.addEventListener('click', function () {
        drawer.classList.contains('is-open') ? closeDrawer() : openDrawer();
      });
      if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
      if (backdrop) backdrop.addEventListener('click', closeDrawer);
    }

    /* ══════════════════════════════════════
       SEARCH OVERLAY
    ══════════════════════════════════════ */
    const searchBtn     = document.getElementById('funalo-search-btn');
    const searchOverlay = document.getElementById('funalo-search-overlay');
    const searchClose   = document.getElementById('funalo-search-close');
    const searchInput   = searchOverlay ? searchOverlay.querySelector('input[type="search"], input[name="s"]') : null;

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

    if (searchBtn)   searchBtn.addEventListener('click', openSearch);
    if (searchClose) searchClose.addEventListener('click', closeSearch);

    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      if (drawer && drawer.classList.contains('is-open')) closeDrawer();
      if (searchOverlay && searchOverlay.classList.contains('is-open')) closeSearch();
    });

  });
</script>
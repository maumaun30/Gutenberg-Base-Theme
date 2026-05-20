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
        <a href="<?php echo esc_url( 'https://funalomax.com/' ); ?>" target="_blank" rel="noopener noreferrer">
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

    <div class="funalo-search-overlay__content">
      <p class="funalo-search-overlay__label"><?php esc_html_e('What are you looking for?', 'luxe'); ?></p>
      <?php get_search_form(); ?>
    </div>

  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {

    /* ── Mobile Drawer ── */
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

    /* ── Search Overlay ── */
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

    /* ── Escape key closes either ── */
    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      if (drawer && drawer.classList.contains('is-open')) closeDrawer();
      if (searchOverlay && searchOverlay.classList.contains('is-open')) closeSearch();
    });

  });
</script>
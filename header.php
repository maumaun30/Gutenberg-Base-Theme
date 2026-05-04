<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<style>
  /* ── FUNaloMAX Nav Variables ── */
  :root {
    --nav-bg:            #0F0D1E;
    --nav-border:        rgba(255, 45, 120, 0.18);
    --color-primary:     #FF2D78;
    --color-primary-hover: #ff508e;
    --nav-text:          #ffffff;
    --nav-text-muted:    rgba(255,255,255,0.65);
    --nav-active-underline: #FF2D78;
    --nav-height:        72px;
  }

  /* ── Base Nav Shell ── */
  .funalo-nav {
    background-color: var(--nav-bg);
    border-bottom: 1px solid var(--nav-border);
    position: relative;
    z-index: 100;
  }

  .funalo-nav__inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
    height: var(--nav-height);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 32px;
  }

  /* ── Logo ── */
  .funalo-nav__logo a {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
  }

  .funalo-nav__logo img,
  .funalo-nav__logo .custom-logo {
    height: 52px;
    width: auto;
  }

  .funalo-nav__logo-text {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: 28px;
    color: #fff;
    letter-spacing: 1px;
    line-height: 1;
  }

  /* ── Primary Nav Links ── */
  .funalo-nav__primary {
    display: flex;
    align-items: center;
    gap: 32px;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .funalo-nav__primary li {
    position: relative;
  }

  .funalo-nav__primary li a {
    font-family: 'Barlow', 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--nav-text-muted);
    text-decoration: none;
    padding: 4px 0;
    position: relative;
    transition: color 0.2s ease;
  }

  .funalo-nav__primary li a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--nav-active-underline);
    border-radius: 2px;
    transition: width 0.25s ease;
  }

  .funalo-nav__primary li a:hover,
  .funalo-nav__primary li.current-menu-item > a,
  .funalo-nav__primary li.current-page-ancestor > a {
    color: var(--nav-text);
  }

  .funalo-nav__primary li a:hover::after,
  .funalo-nav__primary li.current-menu-item > a::after,
  .funalo-nav__primary li.current-page-ancestor > a::after {
    width: 100%;
  }

  /* Active item — solid pink underline by default */
  .funalo-nav__primary li.current-menu-item > a {
    color: #fff;
  }
  .funalo-nav__primary li.current-menu-item > a::after {
    width: 100%;
  }

  /* ── Dropdown ── */
  .funalo-nav__primary li ul.sub-menu {
    display: none;
    position: absolute;
    top: calc(100% + 16px);
    left: 0;
    background: #1a172e;
    border: 1px solid var(--nav-border);
    border-radius: 8px;
    padding: 8px 0;
    min-width: 180px;
    list-style: none;
    margin: 0;
    box-shadow: 0 12px 32px rgba(0,0,0,0.5);
  }

  .funalo-nav__primary li:hover > ul.sub-menu {
    display: block;
  }

  .funalo-nav__primary li ul.sub-menu li a {
    display: block;
    padding: 10px 18px;
    font-size: 12px;
    color: var(--nav-text-muted);
  }

  .funalo-nav__primary li ul.sub-menu li a:hover {
    color: #fff;
    background: rgba(255, 45, 120, 0.1);
  }

  .funalo-nav__primary li ul.sub-menu li a::after {
    display: none;
  }

  /* ── Right Side: Secondary + CTA ── */
  .funalo-nav__right {
    display: flex;
    align-items: center;
    gap: 24px;
  }

  .funalo-nav__secondary {
    display: flex;
    align-items: center;
    gap: 20px;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .funalo-nav__secondary li a {
    font-family: 'Barlow', 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 12px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--nav-text-muted);
    text-decoration: none;
    transition: color 0.2s ease;
  }

  .funalo-nav__secondary li a:hover {
    color: var(--nav-text);
  }

  /* ── Play Now CTA ── */
  .funalo-nav__cta a {
    display: inline-block;
    padding: 11px 26px;
    border-radius: 6px;
    font-family: 'Barlow', 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 13px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    text-decoration: none;
    color: #ffffff;
    background-color: var(--color-primary);
    transition: background-color 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
    box-shadow: 0 0 18px rgba(255, 45, 120, 0.45);
    white-space: nowrap;
  }

  .funalo-nav__cta a:hover {
    background-color: var(--color-primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 0 28px rgba(255, 45, 120, 0.65);
  }

  /* ── Hamburger ── */
  .funalo-nav__hamburger {
    display: none;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    width: 36px;
    height: 36px;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
  }

  .funalo-nav__hamburger span {
    display: block;
    height: 2px;
    width: 100%;
    background: #fff;
    border-radius: 2px;
    transition: transform 0.3s ease, opacity 0.3s ease;
  }

  /* Hamburger open state */
  .funalo-nav__hamburger.is-open span:nth-child(1) {
    transform: translateY(7px) rotate(45deg);
  }
  .funalo-nav__hamburger.is-open span:nth-child(2) {
    opacity: 0;
  }
  .funalo-nav__hamburger.is-open span:nth-child(3) {
    transform: translateY(-7px) rotate(-45deg);
  }

  /* ── Mobile Drawer Overlay ── */
  .funalo-drawer-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 998;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.35s ease;
  }

  .funalo-drawer-backdrop.is-open {
    opacity: 1;
    pointer-events: all;
  }

  /* ── Mobile Drawer Panel (slides in from right) ── */
  .funalo-nav__mobile {
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    max-width: 420px;
    height: 100vh;
    background: #0F0D1E;
    z-index: 999;
    transform: translateX(110%);
    transition: transform 0.38s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    visibility: hidden;
  }

  .funalo-nav__mobile.is-open {
    transform: translateX(0);
    visibility: visible;
  }

  /* Drawer header row: logo + close btn */
  .funalo-drawer__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid rgba(255, 45, 120, 0.14);
    flex-shrink: 0;
  }

  .funalo-drawer__header .drawer-logo img,
  .funalo-drawer__header .drawer-logo .custom-logo {
    height: 44px;
    width: auto;
  }

  .funalo-drawer__header .drawer-logo-text {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: 24px;
    color: #fff;
    letter-spacing: 1px;
    text-decoration: none;
  }

  /* Close (×) button */
  .funalo-drawer__close {
    background: none;
    border: none;
    cursor: pointer;
    color: #fff;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.75;
    transition: opacity 0.2s ease, transform 0.2s ease;
  }

  .funalo-drawer__close:hover {
    opacity: 1;
    transform: rotate(90deg);
  }

  .funalo-drawer__close svg {
    width: 28px;
    height: 28px;
  }

  /* Drawer nav links */
  .funalo-drawer__nav {
    flex: 1;
    padding: 12px 0;
  }

  .funalo-nav__mobile ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .funalo-nav__mobile ul li a {
    display: block;
    font-family: 'Barlow', 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.72);
    text-decoration: none;
    padding: 17px 28px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    transition: color 0.2s ease, background 0.2s ease, padding-left 0.2s ease;
  }

  .funalo-nav__mobile ul li a:hover,
  .funalo-nav__mobile ul li.current-menu-item > a {
    color: #fff;
    background: rgba(255, 45, 120, 0.07);
    padding-left: 36px;
  }

  .funalo-nav__mobile ul li:first-child a {
    border-top: 1px solid rgba(255,255,255,0.06);
  }

  /* Drawer CTA */
  .funalo-drawer__cta {
    padding: 24px 24px 36px;
    flex-shrink: 0;
  }

  .funalo-drawer__cta a {
    display: block;
    text-align: center;
    padding: 16px;
    border-radius: 8px;
    font-family: 'Barlow', 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 15px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    text-decoration: none;
    color: #ffffff;
    background-color: var(--color-primary);
    box-shadow: 0 0 24px rgba(255, 45, 120, 0.5);
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
  }

  .funalo-drawer__cta a:hover {
    background-color: var(--color-primary-hover);
    box-shadow: 0 0 36px rgba(255, 45, 120, 0.7);
  }

  /* ── Responsive Breakpoint ── */
  @media (max-width: 1023px) {
    .funalo-nav__primary,
    .funalo-nav__right {
      display: none;
    }

    .funalo-nav__hamburger {
      display: flex;
    }
  }

  /* Prevent body scroll when drawer open */
  body.funalo-drawer-open {
    overflow: hidden;
  }
</style>

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
          <?php esc_html_e('Play Now', 'luxe'); ?>
        </a>
      </div>

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
      <?php esc_html_e('Play Now', 'luxe'); ?>
    </a>
  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('funalo-hamburger');
    const drawer    = document.getElementById('funalo-mobile-menu');
    const backdrop  = document.getElementById('funalo-backdrop');
    const closeBtn  = document.getElementById('funalo-drawer-close');
    if (!hamburger || !drawer) return;

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

    hamburger.addEventListener('click', function () {
      drawer.classList.contains('is-open') ? closeDrawer() : openDrawer();
    });

    if (closeBtn)  closeBtn.addEventListener('click', closeDrawer);
    if (backdrop)  backdrop.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && drawer.classList.contains('is-open')) closeDrawer();
    });
  });
</script>
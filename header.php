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
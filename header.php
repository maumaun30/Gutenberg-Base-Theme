<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
 
 
<nav class="fixed top-0 left-0 right-0 z-50 border-b"
     style="background-color: var(--bg-dark-2); border-color: var(--border);">
 
  <div class="max-w-7xl mx-auto px-6 py-4">
    <div class="flex items-center justify-between">
 
      <!-- Left: Logo & Main Menu -->
      <div class="flex items-center gap-12">
 
        <!-- Logo -->
        <a href="<?php echo esc_url( home_url('/') ); ?>" class="flex items-center gap-3 no-underline">
          <div class="w-10 h-10 rounded-lg flex items-center justify-center"
               style="background-color: var(--color-primary); box-shadow: var(--shadow-glow);">
            <span class="text-2xl font-bold" style="color: var(--bg-dark-1);">
              <?php echo esc_html( mb_substr( get_bloginfo('name'), 0, 1 ) ); ?>
            </span>
          </div>
          <span class="text-2xl font-bold tracking-tight"
                style="font-family: 'Bebas Neue', sans-serif; color: white;">
            <?php bloginfo('name'); ?>
          </span>
        </a>
 
        <!-- Primary Nav -->
        <div class="hidden lg:flex items-center gap-8">
          <?php
            wp_nav_menu([
              'theme_location' => 'primary',
              'container'      => false,
              'items_wrap'     => '%3$s',
              'walker'         => new Luxe_Nav_Walker(),
              'fallback_cb'    => 'luxe_fallback_nav',
            ]);
          ?>
        </div>
 
      </div><!-- /Left -->
 
      <!-- Right: Secondary Menu & Buttons -->
      <div class="hidden lg:flex items-center gap-6">
 
        <?php
          wp_nav_menu([
            'theme_location' => 'secondary',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'walker'         => new Luxe_Nav_Walker(),
            'fallback_cb'    => false,
          ]);
        ?>
 
        <div class="flex items-center gap-3">
          <?php if ( is_user_logged_in() ) : ?>
            <a href="<?php echo esc_url( wp_logout_url( home_url('/') ) ); ?>"
               class="px-5 py-2 rounded-lg text-white font-medium transition-all hover:bg-white/5">
              <?php esc_html_e('Logout', 'luxe'); ?>
            </a>
            <a href="<?php echo esc_url( get_dashboard_url() ); ?>"
               class="px-5 py-2 rounded-lg font-medium transition-all"
               style="background-color: var(--color-primary); color: var(--bg-dark-1); box-shadow: var(--shadow-glow);"
               onmouseenter="this.style.backgroundColor='var(--color-primary-hover)'"
               onmouseleave="this.style.backgroundColor='var(--color-primary)'">
              <?php esc_html_e('Dashboard', 'luxe'); ?>
            </a>
          <?php else : ?>
            <a href="<?php echo esc_url( wp_login_url() ); ?>"
               class="px-5 py-2 rounded-lg text-white font-medium transition-all hover:bg-white/5">
              <?php esc_html_e('Login', 'luxe'); ?>
            </a>
            <a href="<?php echo esc_url( wp_registration_url() ); ?>"
               class="px-5 py-2 rounded-lg font-medium transition-all"
               style="background-color: var(--color-primary); color: var(--bg-dark-1); box-shadow: var(--shadow-glow);"
               onmouseenter="this.style.backgroundColor='var(--color-primary-hover)'"
               onmouseleave="this.style.backgroundColor='var(--color-primary)'">
              <?php esc_html_e('Register', 'luxe'); ?>
            </a>
          <?php endif; ?>
        </div>
 
      </div><!-- /Right -->
 
      <!-- Mobile hamburger (CSS-only toggle) -->
      <label for="mobile-menu-toggle" class="lg:hidden p-2 cursor-pointer">
        <input type="checkbox" id="mobile-menu-toggle" class="sr-only">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </label>
 
    </div><!-- /flex justify-between -->
 
    <!-- Mobile Menu -->
    <div id="mobile-menu"
         class="lg:hidden hidden flex-col gap-4 mt-4 pb-4 border-t pt-4"
         style="border-color: var(--border);">
 
      <?php
        wp_nav_menu([
          'theme_location' => 'primary',
          'container'      => false,
          'items_wrap'     => '%3$s',
          'walker'         => new Luxe_Mobile_Nav_Walker(),
          'fallback_cb'    => 'luxe_mobile_fallback_nav',
        ]);
 
        wp_nav_menu([
          'theme_location' => 'secondary',
          'container'      => false,
          'items_wrap'     => '%3$s',
          'walker'         => new Luxe_Mobile_Nav_Walker(),
          'fallback_cb'    => false,
        ]);
      ?>
 
      <div class="flex gap-3 mt-2">
        <?php if ( is_user_logged_in() ) : ?>
          <a href="<?php echo esc_url( wp_logout_url( home_url('/') ) ); ?>"
             class="flex-1 px-5 py-2 rounded-lg text-white font-medium text-center border border-white/20">
            <?php esc_html_e('Logout', 'luxe'); ?>
          </a>
        <?php else : ?>
          <a href="<?php echo esc_url( wp_login_url() ); ?>"
             class="flex-1 px-5 py-2 rounded-lg text-white font-medium text-center border border-white/20">
            <?php esc_html_e('Login', 'luxe'); ?>
          </a>
          <a href="<?php echo esc_url( wp_registration_url() ); ?>"
             class="flex-1 px-5 py-2 rounded-lg font-medium text-center"
             style="background-color: var(--color-primary); color: var(--bg-dark-1);">
            <?php esc_html_e('Register', 'luxe'); ?>
          </a>
        <?php endif; ?>
      </div>
 
    </div><!-- /mobile menu -->
 
  </div><!-- /max-w-7xl -->
</nav>
 
<!-- Spacer to push content below fixed nav -->
<div class="h-[73px]"></div>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
 
 
<nav class="border-b"
     style="background-color: var(--bg-dark-2); border-color: var(--border);">
 
  <div class="max-w-7xl mx-auto px-6 py-4">
    <div class="flex items-center justify-between">
 
      <!-- Left: Logo & Main Menu -->
      <div class="flex items-center gap-12">
 
        <!-- Logo -->
        <a href="<?php echo esc_url( home_url('/') ); ?>" class="flex items-center gap-3 no-underline">
        <?php if ( has_custom_logo() ) : ?>
            <div class="site-logo">
            <?php the_custom_logo(); ?>
            </div>
        <?php else : ?>
            <span class="text-2xl font-bold tracking-tight"
                style="font-family: 'Bebas Neue', sans-serif; color: white;">
            <?php bloginfo('name'); ?>
            </span>
        <?php endif; ?>
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
 
      <!-- Right: Secondary Menu & Button -->
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
 
        <!-- Join Now Button -->
        <div class="flex items-center gap-3">
          <a href="<?php echo esc_url( home_url('/join-now/') ); ?>"
             class="px-5 py-2 rounded-lg font-medium transition-all"
             style="background-color: var(--color-primary); color: var(--bg-dark-1); box-shadow: var(--shadow-glow);"
             onmouseenter="this.style.backgroundColor='var(--color-primary-hover)'"
             onmouseleave="this.style.backgroundColor='var(--color-primary)'">
            <?php esc_html_e('Play Now', 'luxe'); ?>
          </a>
        </div>
 
      </div><!-- /Right -->
 
      <!-- Mobile hamburger -->
      <label for="mobile-menu-toggle" class="lg:hidden p-2 cursor-pointer">
        <input type="checkbox" id="mobile-menu-toggle" class="sr-only">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </label>
 
    </div>
 
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
 
      <!-- Mobile Join Now -->
      <div class="flex gap-3 mt-2">
        <a href="<?php echo esc_url( home_url('/join-now/') ); ?>"
           class="flex-1 px-5 py-2 rounded-lg font-medium text-center"
           style="background-color: var(--color-primary); color: var(--bg-dark-1);">
          <?php esc_html_e('Play Now', 'luxe'); ?>
        </a>
      </div>
 
    </div>
 
  </div>
</nav>
 
<!-- Spacer
<div class="h-[73px]"></div> -->
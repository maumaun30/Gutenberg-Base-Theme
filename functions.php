<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    if (!class_exists('ACF')) {
        include_once get_stylesheet_directory() . '/acf/acf.php';
    }
});

add_filter('acf/settings/path', function ($path) {
    return get_stylesheet_directory() . '/acf/';
});

add_filter('acf/settings/dir', function ($dir) {
    return get_stylesheet_directory_uri() . '/acf/';
});

function mytheme_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('custom-logo');

    register_nav_menus([
        'primary' => __('Primary Menu', 'mytheme'),
    ]);
}
add_action('after_setup_theme', 'mytheme_setup');

function mytheme_enqueue_assets()
{
    $critical_css = get_theme_file_path('/assets/css/critical.min.css');
    $main_css     = get_theme_file_path('/assets/css/main.min.css');
    $critical_js  = get_theme_file_path('/assets/js/critical.js');

    if (file_exists($critical_css)) {
        wp_enqueue_style(
            'mytheme-critical',
            get_theme_file_uri('/assets/css/critical.min.css'),
            filemtime($critical_css)
        );
    }

    if (file_exists($main_css)) {
        wp_enqueue_style(
            'mytheme-main',
            get_theme_file_uri('/assets/css/main.min.css'),
            ['mytheme-critical'],
            filemtime($main_css)
        );
    }

    wp_enqueue_style(
        'mytheme-style',
        get_stylesheet_uri(),
        ['mytheme-main'],
        filemtime(get_theme_file_path('/style.css'))
    );

    if (file_exists($critical_js)) {
        wp_enqueue_script(
            'mytheme-critical',
            get_theme_file_uri('/assets/js/critical.js'),
            [],
            filemtime($critical_js),
            false
        );
    }

    $attribution_js = get_theme_file_path('/assets/js/attribution.js');
    if (file_exists($attribution_js)) {
        wp_enqueue_script(
            'mytheme-attribution',
            get_theme_file_uri('/assets/js/attribution.js'),
            [],
            filemtime($attribution_js),
            true
        );
    }

    $register_modal_js = get_theme_file_path('/assets/js/register-modal.js');
    if (file_exists($register_modal_js)) {
        wp_enqueue_script(
            'mytheme-register-modal',
            get_theme_file_uri('/assets/js/register-modal.js'),
            ['mytheme-attribution'],
            filemtime($register_modal_js),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_assets');

function mytheme_editor_styles()
{
    if (file_exists(get_theme_file_path('/assets/css/main.min.css'))) {
        add_editor_style('assets/css/main.min.css');
    }
}
add_action('after_setup_theme', 'mytheme_editor_styles');

function mytheme_register_blocks()
{
    $blocks_dir = get_theme_file_path('/assets/js/blocks');

    if (!is_dir($blocks_dir)) {
        return;
    }

    $folders = scandir($blocks_dir);

    foreach ($folders as $folder) {
        if ($folder === '.' || $folder === '..') {
            continue;
        }

        $block_path = $blocks_dir . '/' . $folder;

        if (is_dir($block_path) && file_exists($block_path . '/block.json')) {
            register_block_type($block_path);
        }
    }
}
add_action('init', 'mytheme_register_blocks');

/**
 * Cache-bust theme block stylesheets registered via block.json.
 *
 * Block "style" files (e.g. assets/js/blocks/<block>/style.css) are enqueued by
 * register_block_type() with WordPress's own version string (?ver=6.9.4), which
 * never changes when you edit the CSS. Browsers then cache the file indefinitely
 * and keep serving a stale copy. Append the file's modification time so every
 * edit produces a fresh URL.
 */
add_filter('style_loader_src', function ($src) {
    if (strpos($src, '/assets/js/blocks/') === false) {
        return $src;
    }
    $clean = strtok($src, '?');
    $path  = str_replace(
        trailingslashit(get_template_directory_uri()),
        trailingslashit(get_template_directory()),
        $clean
    );
    if (is_file($path)) {
        $src = add_query_arg('ver', filemtime($path), $clean);
    }
    return $src;
});

/**
 * Enqueue Swiper.js + carousel frontend script.
 *
 * Add this to your theme's functions.php (or a dedicated assets loader file).
 * Adjust the version strings as needed.
 */
function mytheme_enqueue_carousel_assets() {
    // Only load on pages that actually have the carousel block
    if ( ! has_block( 'mytheme/carousel' ) ) {
        return;
    }
 
    // Swiper CSS (CDN — swap for local if preferred)
    wp_enqueue_style(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [],
        '11'
    );
 
    // Swiper JS (CDN)
    wp_enqueue_script(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [],
        '11',
        true // load in footer
    );
 
    // Our carousel initializer
    wp_enqueue_script(
        'mytheme-carousel',
        get_template_directory_uri() . '/assets/js/mytheme-carousel.js',
        [ 'swiper' ],
        wp_get_theme()->get( 'Version' ),
        true
    );

}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_carousel_assets' );

/**
 * Enqueue Swiper + the Featured Games initializer on single blog posts.
 *
 * mytheme_enqueue_carousel_assets() above short-circuits unless the page has
 * the mytheme/carousel block, so on single.php Swiper is otherwise missing.
 * This handler runs independently and only on single posts.
 */
function fnlmx_enqueue_single_post_slider() {
    if ( ! is_singular( array( 'post', 'promo' ) ) ) {
        return;
    }

    wp_enqueue_style(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [],
        '11'
    );
    wp_enqueue_script(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [],
        '11',
        true
    );

    $fg_js = get_template_directory() . '/assets/js/single-featured-games.js';
    wp_enqueue_script(
        'sp-featured-games',
        get_template_directory_uri() . '/assets/js/single-featured-games.js',
        [ 'swiper' ],
        file_exists( $fg_js ) ? filemtime( $fg_js ) : wp_get_theme()->get( 'Version' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'fnlmx_enqueue_single_post_slider' );

// Responsible Gaming Popup
function fnlmx_responsible_gaming_popup() {
  if ( ! is_front_page() ) return;

  // ACF Fields (Options Page)
  $guidelines_content = function_exists('get_field') ? get_field('fnlmx_gaming_guidelines_contents', 'option') : '';
  $images_wrapper     = function_exists('get_field') ? get_field('fnlmx_gaming_guidelines_img_wrapper', 'option') : [];
  $subparagraph       = function_exists('get_field') ? get_field('fnlmx_gaming_guidelines_subparagraph', 'option') : '';
  $exit_url = 'https://www.google.com';

  // Hero image for the New User Welcome Bonus modal (shown after Proceed).
  // Prefer the ACF options field; otherwise fall back to a theme asset so the
  // image can be supplied simply by dropping a file into /assets/images/.
  $welcome_img     = function_exists('get_field') ? get_field('fnlmx_welcome_bonus_image', 'option') : null;
  $welcome_img_url = '';
  $welcome_img_alt = 'New User Welcome Bonus';
  if ( $welcome_img && is_array( $welcome_img ) && ! empty( $welcome_img['url'] ) ) {
    $welcome_img_url = $welcome_img['url'];
    $welcome_img_alt = $welcome_img['alt'] ?? $welcome_img_alt;
  } else {
    foreach ( [ 'welcome-bonus-hero.png', 'welcome-bonus-hero.webp', 'welcome-bonus-hero.jpg' ] as $welcome_fallback ) {
      if ( file_exists( get_theme_file_path( '/assets/images/' . $welcome_fallback ) ) ) {
        $welcome_img_url = get_theme_file_uri( '/assets/images/' . $welcome_fallback );
        break;
      }
    }
  }
  ?>

  <div class="fnlmx-rg-popup" id="fnlmx-rg-popup" aria-modal="true" role="dialog" aria-label="Responsible Gaming Guidelines" aria-hidden="true">
    <div class="fnlmx-rg-popup__backdrop"></div>

    <div class="fnlmx-rg-popup__panel">

      <h2 class="fnlmx-rg-popup__title"><?php esc_html_e('Responsible Gaming Guidelines', 'luxe'); ?></h2>

      <div class="fnlmx-rg-popup__body">

        <?php if ( $guidelines_content ) : ?>
          <div class="fnlmx-rg-popup__wysiwyg">
            <?php echo wp_kses_post( $guidelines_content ); ?>
          </div>
        <?php endif; ?>

        <div class="fnlmx-rg-popup__badges">
          <?php if ( ! empty( $images_wrapper ) ) : ?>
            <div class="fnlmx-rg-popup__badges-row">
              <?php foreach ( $images_wrapper as $index => $row ) :
                $img = $row['fnlmx_gaming_guidelines_img'] ?? null;
                if ( $index > 0 ) : ?>
                  <div class="fnlmx-rg-popup__badge-divider"></div>
                <?php endif; ?>
                <div class="fnlmx-rg-popup__badge-item">
                  <?php if ( $img && is_array($img) ) : ?>
                    <img
                      src="<?php echo esc_url( $img['url'] ); ?>"
                      alt="<?php echo esc_attr( $img['alt'] ?? '' ); ?>"
                      width="<?php echo esc_attr( $img['width'] ?? '' ); ?>"
                      height="<?php echo esc_attr( $img['height'] ?? '' ); ?>"
                      loading="lazy"
                    >
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else : ?>
            <!-- Fallback badges when no ACF images are set -->
            <div class="fnlmx-rg-popup__badges-row">
              <div class="fnlmx-rg-popup__badge-item">
                <span class="fnlmx-rg-popup__badge-fallback">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="#F71DC2"><circle cx="12" cy="12" r="10"/></svg>
                  PAGCOR
                </span>
              </div>
              <div class="fnlmx-rg-popup__badge-divider"></div>
              <div class="fnlmx-rg-popup__badge-item">
                <span class="fnlmx-rg-popup__badge-fallback fnlmx-rg-popup__badge-fallback--21">21+</span>
              </div>
            </div>
          <?php endif; ?>

          <?php if ( $subparagraph ) : ?>
            <p class="fnlmx-rg-popup__disclaimer">
              <?php echo esc_html( $subparagraph ); ?>
            </p>
          <?php endif; ?>
        </div>

      </div>

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

  <!-- ── New User Welcome Bonus Modal (opens after Proceed) ── -->
  <div class="fm-welcome-modal" id="fm-welcome-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="New User Welcome Bonus">
    <div class="fm-welcome-modal__card" role="document">

      <!-- Close (chamfered button, matches register modal) -->
      <button class="fm-welcome-modal__close" id="fm-welcome-close" type="button" aria-label="Close">
        <svg aria-hidden="true" class="fm-welcome-modal__close-shape" viewBox="0 0 32 32" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g clip-path="url(#fm-welcome-close-shape)">
            <path d="M32 20.4 L20.4 32 H0 V7 L7 0 H32 V20.4 Z" fill="currentColor"></path>
            <path d="M32 24 V32 H24 L32 24 Z" fill="var(--decoration, #ffffff)"></path>
          </g>
          <defs><clipPath id="fm-welcome-close-shape"><rect width="32" height="32" fill="white"></rect></clipPath></defs>
        </svg>
        <svg class="fm-welcome-modal__close-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>

      <?php if ( $welcome_img_url ) : ?>
        <div class="fm-welcome-modal__hero">
          <img src="<?php echo esc_url( $welcome_img_url ); ?>" alt="<?php echo esc_attr( $welcome_img_alt ); ?>" loading="lazy" decoding="async">
        </div>
      <?php endif; ?>

      <h3 class="fm-welcome-modal__title"><?php esc_html_e( 'New User', 'luxe' ); ?><span class="fm-welcome-modal__title-accent"><?php esc_html_e( 'Welcome Bonus', 'luxe' ); ?></span></h3>

      <div class="fm-welcome-modal__coin" aria-hidden="true">
        <span class="fm-welcome-modal__coin-amount">&#8369;5</span>
      </div>

      <p class="fm-welcome-modal__sub"><?php esc_html_e( 'Register now and get', 'luxe' ); ?> <strong>&#8369;5</strong> <?php esc_html_e( 'instantly.', 'luxe' ); ?></p>

      <!-- Phone field: icon + +63 prefix + local number (mirrors the register modal) -->
      <div class="fm-welcome-modal__field">
        <svg class="fm-welcome-modal__field-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" />
        </svg>
        <span class="fm-welcome-modal__cc">+63</span>
        <input type="tel" id="fm-welcome-phone" class="fm-welcome-modal__input"
          placeholder="9XX XXX XXXX" inputmode="numeric" autocomplete="tel" maxlength="10">
      </div>

      <!-- CTA — submits the phone via register-modal.js (same flow as the register modal) -->
      <button type="button" id="fm-welcome-submit" class="fm-welcome-modal__cta">
        <svg aria-hidden="true" class="fm-welcome-modal__cta-shape" viewBox="0 0 148 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g clip-path="url(#fm-welcome-cta-shape)">
            <path d="M148 30.4 L136.4 42 H0 V7 L7 0 H148 V30.4 Z" fill="currentColor"></path>
            <path d="M148 34 V42 H140 L148 34 Z" fill="var(--decoration, #ffffff)"></path>
          </g>
          <defs><clipPath id="fm-welcome-cta-shape"><rect width="148" height="42" fill="white"></rect></clipPath></defs>
        </svg>
        <span class="fm-welcome-modal__cta-label"><?php esc_html_e( 'Register & Get', 'luxe' ); ?> &#8369;5</span>
      </button>

    </div>
  </div>

  <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rgPopup   = document.getElementById('fnlmx-rg-popup');
            const rgProceed = document.getElementById('fnlmx-rg-proceed');
            const rgExit    = document.getElementById('fnlmx-rg-exit');
            const welcomeModal = document.getElementById('fm-welcome-modal');
            const welcomeClose = document.getElementById('fm-welcome-close');

            function openWelcome() {
                if (!welcomeModal) return;
                welcomeModal.classList.add('is-open');
                welcomeModal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('funalo-drawer-open');
            }
            function closeWelcome() {
                if (!welcomeModal) return;
                welcomeModal.classList.remove('is-open');
                welcomeModal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('funalo-drawer-open');
            }
            if (welcomeClose) welcomeClose.addEventListener('click', closeWelcome);
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && welcomeModal && welcomeModal.classList.contains('is-open')) closeWelcome();
            });

            if (rgPopup && !sessionStorage.getItem('fnlmx_rg_accepted')) {
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
                // Show the New User Welcome Bonus modal next; keep body locked
                // while it's open, otherwise release the scroll lock.
                if (welcomeModal) {
                    openWelcome();
                } else {
                    document.body.classList.remove('funalo-drawer-open');
                }
            });
            }

            if (rgExit) {
            rgExit.addEventListener('click', function () {
                var fallback = <?php echo json_encode( esc_url( $exit_url ) ); ?>;
                window.location.href = document.referrer && document.referrer !== window.location.href
                ? document.referrer
                : fallback;
            });
            }
        });
    </script>

  <?php
}
add_action( 'wp_body_open', 'fnlmx_responsible_gaming_popup' );

/**
 * Back to Top button.
 *
 * Fixed button on the lower-right of the page. Hidden until the visitor
 * scrolls down, then smooth-scrolls back to the top of the page on click.
 */
function fnlmx_back_to_top_button() {
  ?>
  <button class="fnlmx-back-to-top" id="fnlmx-back-to-top" type="button" aria-label="<?php esc_attr_e( 'Back to top', 'luxe' ); ?>">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
      <path d="M12 19V5M12 5l-7 7M12 5l7 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </button>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const backToTop = document.getElementById('fnlmx-back-to-top');
      if (!backToTop) return;

      const toggleBackToTop = function () {
        if (window.pageYOffset > 300) {
          backToTop.classList.add('is-visible');
        } else {
          backToTop.classList.remove('is-visible');
        }
      };

      toggleBackToTop();
      window.addEventListener('scroll', toggleBackToTop, { passive: true });

      backToTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });
  </script>
  <?php
}
add_action( 'wp_footer', 'fnlmx_back_to_top_button' );

// CTA SECTION

function fnlmx_cta_section() {
    $cta_title       = get_field( 'fnlmx_cta_title', 'option' );
    $cta_description = get_field( 'fnlmx_cta_description', 'option' );
    $cta_buttons     = get_field( 'fnlmx_cta_button_wrapper', 'option' );
    $cta_desktop_bg  = get_field( 'fnlmx_cta_desktop_bg', 'option' );
    $cta_mobile_bg   = get_field( 'fnlmx_cta_mobile_bg', 'option' );
    $cta_image       = get_field( 'fnlmx_cta_image', 'option' );

    if ( ! $cta_title && ! $cta_description ) {
        return '';
    }

    $desktop_bg_url = ! empty( $cta_desktop_bg['url'] ) ? esc_url( $cta_desktop_bg['url'] ) : '';
    $desktop_bg_alt = ! empty( $cta_desktop_bg['alt'] ) ? esc_attr( $cta_desktop_bg['alt'] ) : '';
    $mobile_bg_url  = ! empty( $cta_mobile_bg['url'] )  ? esc_url( $cta_mobile_bg['url'] )  : '';
    $mobile_bg_alt  = ! empty( $cta_mobile_bg['alt'] )  ? esc_attr( $cta_mobile_bg['alt'] )  : '';
    $image_url      = ! empty( $cta_image['url'] )       ? esc_url( $cta_image['url'] )       : '';
    $image_alt      = ! empty( $cta_image['alt'] )       ? esc_attr( $cta_image['alt'] )      : '';

    ob_start(); ?>

    <?php /*
        .fnlmx-cta-wrap  — full viewport width, holds the bg images (position: relative)
        .fnlmx-cta       — max-width 1280px centred, holds the split content (position: relative z-index:1)
        The bg images are absolutely positioned inside .fnlmx-cta-wrap so they
        always cover the full width regardless of the inner container's max-width.
    */ ?>
    <div class="fnlmx-cta-wrap">

        <?php /* Desktop bg — covers full wrap width */ ?>
        <?php if ( $desktop_bg_url ) : ?>
            <img
                src="<?php echo $desktop_bg_url; ?>"
                alt="<?php echo $desktop_bg_alt; ?>"
                class="fnlmx-cta__bg fnlmx-cta__bg--desktop"
                aria-hidden="true"
                loading="lazy"
            >
        <?php endif; ?>

        <?php /* Mobile bg — covers full wrap width, swaps in on mobile */ ?>
        <?php if ( $mobile_bg_url ) : ?>
            <img
                src="<?php echo $mobile_bg_url; ?>"
                alt="<?php echo $mobile_bg_alt; ?>"
                class="fnlmx-cta__bg fnlmx-cta__bg--mobile"
                aria-hidden="true"
                loading="lazy"
            >
        <?php endif; ?>

        <?php /* Constrained content container */ ?>
        <section class="fnlmx-cta">
            <div class="fnlmx-cta__inner">

                <?php /* Left / Top — content pane (transparent) */ ?>
                <div class="fnlmx-cta__content-pane">
                    <div class="fnlmx-cta__content">

                        <?php if ( $cta_title ) : ?>
                            <h2 class="fnlmx-cta__title"><?php echo esc_html( $cta_title ); ?></h2>
                        <?php endif; ?>

                        <?php if ( $cta_description ) : ?>
                            <div class="fnlmx-cta__description"><?php echo wp_kses_post( $cta_description ); ?></div>
                        <?php endif; ?>

                        <?php if ( ! empty( $cta_buttons ) ) : ?>
                            <div class="fnlmx-cta__buttons">
                                <?php foreach ( $cta_buttons as $index => $button ) :
                                    $label = ! empty( $button['fnlmx_cta_button_label'] ) ? $button['fnlmx_cta_button_label'] : '';
                                    $link  = ! empty( $button['fnlmx_cta_button_link'] )  ? $button['fnlmx_cta_button_link']  : '';

                                    if ( ! $label ) continue;

                                    $is_external = $link && ! preg_match( '/^(tel:|mailto:)/', $link );
                                    $target      = $is_external ? ' target="_blank" rel="noopener noreferrer"' : '';
                                    // Primary button opens registration via attribution.js (fm-register-btn);
                                    // its click is intercepted, so the href just acts as a fallback.
                                    $btn_class   = $index === 0 ? 'fnlmx-cta__btn fnlmx-cta__btn--primary fm-register-btn' : 'fnlmx-cta__btn fnlmx-cta__btn--secondary';
                                    $btn_uid     = uniqid();
                                    // Angled SVG shape used as the button background.
                                    $btn_shape = '<svg aria-hidden="true" class="fnlmx-cta__btn-shape" viewBox="0 0 148 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">'
                                        . '<g clip-path="url(#fnlmx-cta-btn-' . esc_attr( $btn_uid ) . ')">'
                                        . '<path d="M148 30.4 L136.4 42 H0 V7 L7 0 H148 V30.4 Z" fill="currentColor"></path>'
                                        . '<path d="M148 34 V42 H140 L148 34 Z" fill="var(--decoration, currentColor)"></path>'
                                        . '</g><defs><clipPath id="fnlmx-cta-btn-' . esc_attr( $btn_uid ) . '">'
                                        . '<rect width="148" height="42" fill="white"></rect></clipPath></defs></svg>';
                                ?>
                                    <?php if ( $link ) : ?>
                                        <a href="<?php echo esc_url( $link ); ?>" class="<?php echo esc_attr( $btn_class ); ?>"<?php echo $target; ?>>
                                            <?php echo $btn_shape; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                            <span class="fnlmx-cta__btn-label"><?php echo esc_html( $label ); ?></span>
                                        </a>
                                    <?php else : ?>
                                        <span class="<?php echo esc_attr( $btn_class ); ?>">
                                            <?php echo $btn_shape; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                            <span class="fnlmx-cta__btn-label"><?php echo esc_html( $label ); ?></span>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <?php /* Right / Bottom — CTA image (transparent, no bg) */ ?>
                <div class="fnlmx-cta__image-pane">
                    <?php if ( $image_url ) : ?>
                        <img
                            src="<?php echo $image_url; ?>"
                            alt="<?php echo $image_alt; ?>"
                            class="fnlmx-cta__image"
                            loading="lazy"
                        >
                    <?php endif; ?>
                </div>

            </div>
        </section>

    </div><!-- /.fnlmx-cta-wrap -->

    <?php
    return ob_get_clean();
}
add_shortcode( 'fnlmx_cta', 'fnlmx_cta_section' );
 
/**
 * Luxe Theme — Nav Walker & Menu Registration
 * Add this to your functions.php
 *
 * @package Luxe
 */
 
// ─── Register Nav Locations ──────────────────────────────────────────────────
 
add_action( 'after_setup_theme', function () {
    register_nav_menus([
        'primary'   => __( 'Primary Menu',   'luxe' ),
        'secondary' => __( 'Secondary Menu', 'luxe' ),
    ]);
});
 
 
// ─── Desktop Nav Walker ──────────────────────────────────────────────────────
 
class Luxe_Nav_Walker extends Walker_Nav_Menu {
 
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = implode( ' ', (array) $item->classes );
        $is_current = in_array( 'current-menu-item', (array) $item->classes );
 
        $color = $is_current ? 'color: white;' : 'color: rgba(255,255,255,0.8);';
 
        $output .= sprintf(
            '<a href="%s" class="font-medium transition-colors hover:text-white" style="%s">%s</a>',
            esc_url( $item->url ),
            esc_attr( $color ),
            esc_html( $item->title )
        );
    }
}
 
 
// ─── Mobile Nav Walker ───────────────────────────────────────────────────────
 
class Luxe_Mobile_Nav_Walker extends Walker_Nav_Menu {
 
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $is_current = in_array( 'current-menu-item', (array) $item->classes );
        $color = $is_current ? 'color: white;' : 'color: rgba(255,255,255,0.8);';
 
        $output .= sprintf(
            '<a href="%s" class="font-medium transition-colors hover:text-white" style="%s">%s</a>',
            esc_url( $item->url ),
            esc_attr( $color ),
            esc_html( $item->title )
        );
    }
}
 
 
// ─── Fallback Nav (if no menu assigned) ─────────────────────────────────────
 
function luxe_fallback_nav() {
    $links = [
        home_url('/')          => __( 'Home',     'luxe' ),
        home_url('/services')  => __( 'Services', 'luxe' ),
        home_url('/about')     => __( 'About',    'luxe' ),
        home_url('/contact')   => __( 'Contact',  'luxe' ),
    ];
 
    foreach ( $links as $url => $label ) {
        printf(
            '<a href="%s" class="font-medium transition-colors text-white/80 hover:text-white">%s</a>',
            esc_url( $url ),
            esc_html( $label )
        );
    }
}
 
function luxe_mobile_fallback_nav() {
    luxe_fallback_nav();
}

/**
 * Luxe Theme — Footer Nav Walker, Menu Registration & Social Customizer
 * Add this to your functions.php (append to existing Luxe functions)
 *
 * @package Luxe
 */
 
// ─── Register Footer Nav Locations ──────────────────────────────────────────
// (Merge with the existing register_nav_menus call if you already have one)
 
add_action( 'after_setup_theme', function () {
    register_nav_menus([
        // Header (already registered)
        'primary'      => __( 'Primary Menu',   'luxe' ),
        'secondary'    => __( 'Secondary Menu', 'luxe' ),
        // Footer
        'footer-links' => __( 'Footer: Quick Links',    'luxe' ),
        'footer-help'  => __( 'Footer: Help & Support', 'luxe' ),
        'footer-legal' => __( 'Footer: Legal',          'luxe' ),
    ]);
}, 0 );
 
 
// ─── Footer Nav Walker ───────────────────────────────────────────────────────
 
class Luxe_Footer_Walker extends Walker_Nav_Menu {
 
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $output .= sprintf(
            '<li><a href="%s" class="text-white/60 hover:text-white transition-colors">%s</a></li>',
            esc_url( $item->url ),
            esc_html( $item->title )
        );
    }
}
 
// ─── Social Links via Customizer ─────────────────────────────────────────────
 
add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
 
    $wp_customize->add_section( 'luxe_social', [
        'title'    => __( 'Social Links', 'luxe' ),
        'priority' => 120,
    ]);
 
    $socials = [
        'twitter'   => __( 'Twitter / X URL',  'luxe' ),
        'linkedin'  => __( 'LinkedIn URL',      'luxe' ),
        'github'    => __( 'GitHub URL',        'luxe' ),
        'instagram' => __( 'Instagram URL',     'luxe' ),
    ];
 
    foreach ( $socials as $key => $label ) {
        $wp_customize->add_setting( 'luxe_social_' . $key, [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
 
        $wp_customize->add_control( 'luxe_social_' . $key, [
            'label'   => $label,
            'section' => 'luxe_social',
            'type'    => 'url',
        ]);
    }
});

// Allow SVG
add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

  global $wp_version;
  if ( $wp_version !== '4.7.1' ) {
     return $data;
  }

  $filetype = wp_check_filetype( $filename, $mimes );

  return [
      'ext'             => $filetype['ext'],
      'type'            => $filetype['type'],
      'proper_filename' => $data['proper_filename']
  ];

}, 10, 4 );

function cc_mime_types( $mimes ){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

function fix_svg() {
  echo '<style type="text/css">
        .attachment-266x266, .thumbnail img {
             width: 100% !important;
             height: auto !important;
        }
        </style>';
}
add_action( 'admin_head', 'fix_svg' );

/**
 * On single 'game' page, mark the menu item linking to the game's primary
 * game_category (parent game category) as current.
 */
add_filter( 'nav_menu_css_class', function ( $classes, $item ) {
    if ( ! is_singular( 'game' ) ) {
        return $classes;
    }
    if ( empty( $item->object ) || $item->object !== 'game_category' ) {
        return $classes;
    }

    $terms = get_the_terms( get_queried_object_id(), 'game_category' );
    if ( ! $terms || is_wp_error( $terms ) ) {
        return $classes;
    }
    $primary = $terms[0];

    $matched_ids = array_merge(
        [ (int) $primary->term_id ],
        array_map( 'intval', get_ancestors( $primary->term_id, 'game_category', 'taxonomy' ) )
    );

    $menu_term_id = (int) $item->object_id;
    if ( in_array( $menu_term_id, $matched_ids, true ) ) {
        $classes[] = 'current-menu-item';
        $classes[] = 'current_page_item';
        if ( $menu_term_id !== (int) $primary->term_id ) {
            $classes[] = 'current-menu-ancestor';
            $classes[] = 'current-menu-parent';
        }
    }
    return array_unique( $classes );
}, 10, 2 );

/**
 * On a single 'promo' page, mark the parent "Promos" menu item as current.
 * WordPress doesn't auto-highlight a CPT archive (or its Page/custom-link) on a
 * CPT single, so match the menu item URL against the known Promos destinations.
 */
add_filter( 'nav_menu_css_class', function ( $classes, $item ) {
    if ( ! is_singular( 'promo' ) ) {
        return $classes;
    }

    // URLs the "Promos" menu item could point at (archive, Page, or custom link).
    $targets = array_filter( [
        get_post_type_archive_link( 'promo' ),
        home_url( '/promos/' ),
    ] );

    $item_path = trailingslashit( (string) wp_parse_url( $item->url, PHP_URL_PATH ) );

    foreach ( $targets as $target ) {
        $target_path = trailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) );
        if ( strlen( $item_path ) > 1 && $item_path === $target_path ) {
            $classes[] = 'current-menu-item';
            $classes[] = 'current_page_item';
            $classes[] = 'current-page-ancestor';
            break;
        }
    }

    return array_unique( $classes );
}, 10, 2 );

/**
 * On a single blog post (single.php), mark the parent "Blogs" menu item as
 * current. Mirrors the promo handling above and matches the menu item URL
 * against the configured posts page or the /blog/ link.
 */
add_filter( 'nav_menu_css_class', function ( $classes, $item ) {
    if ( ! is_singular( 'post' ) ) {
        return $classes;
    }

    // URLs the "Blogs" menu item could point at (posts page, Page, or custom link).
    $posts_page = (int) get_option( 'page_for_posts' );
    $targets = array_filter( [
        $posts_page ? get_permalink( $posts_page ) : '',
        home_url( '/blogs/' ),
    ] );

    $item_path = trailingslashit( (string) wp_parse_url( $item->url, PHP_URL_PATH ) );

    foreach ( $targets as $target ) {
        $target_path = trailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) );
        if ( strlen( $item_path ) > 1 && $item_path === $target_path ) {
            $classes[] = 'current-menu-item';
            $classes[] = 'current_page_item';
            $classes[] = 'current-page-ancestor';
            break;
        }
    }

    return array_unique( $classes );
}, 10, 2 );

// Post Archive Block helpers (AJAX handler + card template + asset enqueue)
require_once get_template_directory() . '/assets/js/blocks/post-archive/functions-helpers.php';

//Hero Slider
function mytheme_enqueue_hero_slider() {
    $js_path = get_theme_file_path( '/assets/js/blocks/hero/hero-slider.js' );

    if ( ! file_exists( $js_path ) ) {
        return;
    }

    wp_enqueue_script(
        'mytheme-hero-slider',
        get_theme_file_uri( '/assets/js/blocks/hero/hero-slider.js' ),
        [],
        filemtime( $js_path ),
        [ 'strategy' => 'defer', 'in_footer' => true ]
    );
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_hero_slider' );

/* ──────────────────────────────────────────────────────────────
   Game Category archive — "Load More" support
   Shared card template (used by taxonomy-game_category.php + AJAX)
   ────────────────────────────────────────────────────────────── */
/**
 * Site (custom) logo URL — used as the fallback art on game tiles that have no
 * featured image. Resolved once per request.
 */
if ( ! function_exists( 'fnlmx_site_logo_url' ) ) {
    function fnlmx_site_logo_url(): string {
        static $url = null;
        if ( $url !== null ) {
            return $url;
        }
        $logo_id = (int) get_theme_mod( 'custom_logo' );
        $url = $logo_id ? ( wp_get_attachment_image_url( $logo_id, 'medium' ) ?: '' ) : '';
        return $url;
    }
}

/**
 * Centered site-logo markup for a game tile that lacks a featured image.
 * Returns an empty string when no custom logo is set (callers can then fall
 * back to their own placeholder).
 */
if ( ! function_exists( 'fnlmx_tile_fallback_logo' ) ) {
    function fnlmx_tile_fallback_logo(): string {
        $logo = fnlmx_site_logo_url();
        if ( ! $logo ) {
            return '';
        }
        return '<img src="' . esc_url( $logo ) . '" alt="" class="fnlmx-fallback-logo" loading="lazy">';
    }
}

if ( ! function_exists( 'fnlmx_game_card_template' ) ) {
    function fnlmx_game_card_template( int $post_id ): void {
        $thumb  = get_the_post_thumbnail_url( $post_id, 'medium_large' );
        $title  = get_the_title( $post_id );
        $is_hot = has_term( 'hot', 'game-tag', $post_id );
        ?>
        <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="fm-card" aria-label="<?php echo esc_attr( $title ); ?>">

            <?php if ( $thumb ) : ?>

                <div
                    class="fm-card__blur"
                    style="background-image:url('<?php echo esc_url( $thumb ); ?>');">
                </div>

                <img
                    src="<?php echo esc_url( $thumb ); ?>"
                    alt="<?php echo esc_attr( $title ); ?>"
                    class="fm-card__img"
                    loading="lazy">

            <?php else : ?>

                <div class="fm-card__fallback">
                    <?php
                    $fallback_logo = fnlmx_tile_fallback_logo();
                    echo $fallback_logo ?: esc_html( mb_substr( $title, 0, 1 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    ?>
                </div>

            <?php endif; ?>

            <?php if ( $is_hot ) : ?>
                <span class="fm-card__badge">HOT</span>
            <?php endif; ?>

        </a>
        <?php
    }
}

if ( ! function_exists( 'fnlmx_ajax_load_more_games' ) ) {
    function fnlmx_ajax_load_more_games(): void {
        check_ajax_referer( 'fnlmx_load_more', 'nonce' );

        $term_id = absint( $_POST['term_id'] ?? 0 );
        $page    = max( 1, (int) ( $_POST['page'] ?? 2 ) );

        if ( ! $term_id ) {
            wp_send_json_error( [ 'message' => 'Missing term' ], 400 );
        }

        $query = new WP_Query( [
            'post_type'      => 'game',
            'post_status'    => 'publish',
            'tax_query'      => [ [
                'taxonomy'         => 'game_category',
                'field'            => 'term_id',
                'terms'            => $term_id,
                'include_children' => true,
            ] ],
            'posts_per_page' => 12,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ] );

        if ( ! $query->have_posts() ) {
            wp_send_json_success( [ 'html' => '', 'has_more' => false ] );
        }

        ob_start();
        while ( $query->have_posts() ) {
            $query->the_post();
            fnlmx_game_card_template( get_the_ID() );
        }
        wp_reset_postdata();
        $html = ob_get_clean();

        $has_more = $page < (int) $query->max_num_pages;

        wp_send_json_success( compact( 'html', 'has_more' ) );
    }

    add_action( 'wp_ajax_fnlmx_load_more_games',        'fnlmx_ajax_load_more_games' );
    add_action( 'wp_ajax_nopriv_fnlmx_load_more_games', 'fnlmx_ajax_load_more_games' );
}
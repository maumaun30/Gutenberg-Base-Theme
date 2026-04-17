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
 
 
// ─── Fallback links (shown if no menu is assigned) ───────────────────────────
 
function luxe_footer_links_fallback() {
    $items = [
        home_url('/')          => __( 'Home',     'luxe' ),
        home_url('/about')     => __( 'About Us', 'luxe' ),
        home_url('/services')  => __( 'Services', 'luxe' ),
        home_url('/pricing')   => __( 'Pricing',  'luxe' ),
        home_url('/contact')   => __( 'Contact',  'luxe' ),
    ];
    luxe_render_footer_fallback( $items );
}
 
function luxe_footer_help_fallback() {
    $items = [
        home_url('/support')       => __( 'Support Center', 'luxe' ),
        home_url('/docs')          => __( 'Documentation',  'luxe' ),
        home_url('/faq')           => __( 'FAQ',            'luxe' ),
        home_url('/community')     => __( 'Community',      'luxe' ),
        home_url('/status')        => __( 'Status',         'luxe' ),
    ];
    luxe_render_footer_fallback( $items );
}
 
function luxe_footer_legal_fallback() {
    $items = [
        home_url('/privacy')  => __( 'Privacy Policy',   'luxe' ),
        home_url('/terms')    => __( 'Terms of Service', 'luxe' ),
        home_url('/cookies')  => __( 'Cookie Policy',    'luxe' ),
        home_url('/licenses') => __( 'Licenses',         'luxe' ),
        home_url('/security') => __( 'Security',         'luxe' ),
    ];
    luxe_render_footer_fallback( $items );
}
 
function luxe_render_footer_fallback( array $items ) {
    foreach ( $items as $url => $label ) {
        printf(
            '<li><a href="%s" class="text-white/60 hover:text-white transition-colors">%s</a></li>',
            esc_url( $url ),
            esc_html( $label )
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
 
 
// ─── Social Icon Hover (CSS) — enqueue in wp_head ────────────────────────────
// Since Tailwind can't do arbitrary CSS variable swaps on hover without JIT,
// we add a tiny <style> block for the social icon hover effect.
 
add_action( 'wp_head', function () { ?>
<style>
  .luxe-social-icon:hover {
    background-color: var(--color-primary) !important;
    color: var(--bg-dark-1) !important;
    border-color: var(--color-primary) !important;
  }
</style>
<?php });

// ── CPT: game ──────────────────────────────────────────────
function mytheme_register_game_cpt() {
    register_post_type( 'game', [
        'labels' => [
            'name'               => 'Games',
            'singular_name'      => 'Game',
            'add_new_item'       => 'Add New Game',
            'edit_item'          => 'Edit Game',
            'new_item'           => 'New Game',
            'view_item'          => 'View Game',
            'search_items'       => 'Search Games',
            'not_found'          => 'No games found',
            'not_found_in_trash' => 'No games found in trash',
        ],
        'public'       => true,
        'show_in_rest' => true,
        'supports'     => [ 'title', 'thumbnail', 'custom-fields' ],
        'menu_icon'    => 'dashicons-games',
        'has_archive'  => false,
        'rewrite'      => [ 'slug' => 'games' ],
    ] );
}
add_action( 'init', 'mytheme_register_game_cpt' );

// ── Taxonomy: game_category ────────────────────────────────
function mytheme_register_game_category_taxonomy() {
    register_taxonomy( 'game_category', 'game', [
        'labels' => [
            'name'              => 'Game Categories',
            'singular_name'     => 'Game Category',
            'search_items'      => 'Search Categories',
            'all_items'         => 'All Categories',
            'edit_item'         => 'Edit Category',
            'update_item'       => 'Update Category',
            'add_new_item'      => 'Add New Category',
            'new_item_name'     => 'New Category Name',
            'menu_name'         => 'Categories',
        ],
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'game-category' ],
    ] );
}
add_action( 'init', 'mytheme_register_game_category_taxonomy' );

// ── Meta fields: price + button URL ───────────────────────
function mytheme_register_game_meta() {
    register_post_meta( 'game', 'game_price', [
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'string',
        'auth_callback' => fn() => current_user_can( 'edit_posts' ),
    ] );
    register_post_meta( 'game', 'game_button_url', [
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'string',
        'auth_callback' => fn() => current_user_can( 'edit_posts' ),
    ] );
    register_post_meta( 'game', 'game_button_label', [
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'string',
        'auth_callback' => fn() => current_user_can( 'edit_posts' ),
    ] );
}
add_action( 'init', 'mytheme_register_game_meta' );

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

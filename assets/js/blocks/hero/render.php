<?php
/**
 * Hero Slider Block – render.php
 * Full-width image slider — featured images from the selected post type.
 * Breadcrumb only (Home › Page Title), dot nav only, no arrows.
 * JS is enqueued via functions.php (hero-slider.js).
 */

$post_type       = $attributes['selectedPostType'] ?? 'post';
$slide_count     = intval( $attributes['slideCount']     ?? 5 );
$breadcrumb_lbl  = $attributes['breadcrumbLabel']        ?? '';
$autoplay_delay  = intval( $attributes['autoplayDelay']  ?? 5000 );
$slider_height   = esc_attr( $attributes['sliderHeight'] ?? '420px' );
$overlay_opacity = intval( $attributes['overlayOpacity'] ?? 35 ) / 100;

// ── Dynamic breadcrumb label ──────────────────────────────────────────────────
if ( empty( $breadcrumb_lbl ) ) {
    $queried = get_queried_object();
    if ( $queried instanceof WP_Post ) {
        $breadcrumb_lbl = get_the_title( $queried );
    } elseif ( $queried instanceof WP_Term ) {
        $breadcrumb_lbl = $queried->name;
    } elseif ( $queried instanceof WP_Post_Type ) {
        $breadcrumb_lbl = $queried->labels->name;
    } elseif ( is_home() || is_front_page() ) {
        $breadcrumb_lbl = get_bloginfo( 'name' );
    } else {
        $breadcrumb_lbl = get_the_archive_title();
    }
}
$breadcrumb_lbl = wp_strip_all_tags( $breadcrumb_lbl );

// ── Fetch slides ──────────────────────────────────────────────────────────────
$slides = [];
if ( $post_type && get_post_type_object( $post_type ) ) {
    $q = new WP_Query( [
        'post_type'              => $post_type,
        'posts_per_page'         => $slide_count,
        'post_status'            => 'publish',
        'has_post_thumbnail'     => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'orderby'                => 'date',
        'order'                  => 'DESC',
    ] );
    foreach ( $q->posts as $p ) {
        $img = get_the_post_thumbnail_url( $p->ID, 'full' );
        if ( $img ) {
            $slides[] = [
                'url'   => $img,
                'title' => get_the_title( $p ),
            ];
        }
    }
    wp_reset_postdata();
}

if ( empty( $slides ) ) {
    echo '<div class="wp-block-mytheme-hero hero-slider hero-slider--empty">'
       . '<p style="color:#fff;padding:2rem;text-align:center;opacity:.6;">No published posts with a featured image found for post type: <strong>'
       . esc_html( $post_type ) . '</strong></p></div>';
    return;
}

$wrapper_attrs = get_block_wrapper_attributes( [
    'class'                => 'hero-slider',
    'data-autoplay'        => $autoplay_delay,
    'aria-roledescription' => 'carousel',
    'aria-label'           => esc_attr( $breadcrumb_lbl . ' slideshow' ),
    'style'                => "--slider-height:{$slider_height};--overlay-opacity:{$overlay_opacity};",
] );
?>

<div <?php echo $wrapper_attrs; ?>>

    <?php /* ── Slides ─────────────────────────────────────────── */ ?>
    <div class="hero-slider__track" aria-live="off">
        <?php foreach ( $slides as $i => $slide ) : ?>
        <div
            class="hero-slider__slide<?php echo $i === 0 ? ' is-active' : ''; ?>"
            aria-hidden="<?php echo $i === 0 ? 'false' : 'true'; ?>"
            role="group"
            aria-roledescription="slide"
            aria-label="<?php echo esc_attr( ( $i + 1 ) . ' of ' . count( $slides ) ); ?>"
        >
            <img
                class="hero-slider__img"
                src="<?php echo esc_url( $slide['url'] ); ?>"
                alt="<?php echo esc_attr( $slide['title'] ); ?>"
                <?php echo $i === 0 ? '' : 'loading="lazy"'; ?>
                decoding="async"
            />
            <div class="hero-slider__overlay" aria-hidden="true"></div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php /* ── Breadcrumb — inside nav-width container ──────── */ ?>
    <div class="hero-slider__nav-container">
        <nav class="hero-slider__breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'mytheme' ); ?>">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumb-home">
                <?php esc_html_e( 'Home', 'mytheme' ); ?>
            </a>
            <span class="breadcrumb-sep" aria-hidden="true"> &rsaquo; </span>
            <span class="breadcrumb-current"><?php echo esc_html( $breadcrumb_lbl ); ?></span>
        </nav>
    </div>

    <?php /* ── Dot navigation only — no arrows ──────────────── */ ?>
    <?php if ( count( $slides ) > 1 ) : ?>
    <div class="hero-slider__dots" role="tablist" aria-label="<?php esc_attr_e( 'Slide navigation', 'mytheme' ); ?>">
        <?php foreach ( $slides as $i => $slide ) : ?>
        <button
            class="hero-slider__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"
            role="tab"
            aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
            aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'mytheme' ), $i + 1 ) ); ?>"
            data-index="<?php echo $i; ?>"
        ></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
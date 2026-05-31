<?php
/**
 * Hero Block – render.php
 *
 * Dynamic attributes available via $attributes[].
 * Breadcrumb: "Home > <page title>" resolved at render time.
 * Post images: pulled from the chosen post type, respecting $attributes['imageCount'].
 */

// ── Attributes ────────────────────────────────────────────────────────────────
$title            = $attributes['title']           ?? '';
$subtitle         = $attributes['subtitle']        ?? '';
$breadcrumb_label = $attributes['breadcrumbLabel'] ?? '';
$cta_text         = $attributes['ctaText']         ?? 'Register Now';
$cta_url          = $attributes['ctaUrl']          ?? '#';
$post_type        = $attributes['selectedPostType'] ?? 'post';
$image_count      = intval( $attributes['imageCount']      ?? 3 );
$bg_color         = esc_attr( $attributes['backgroundColor'] ?? '#7B1FA2' );
$accent_color     = esc_attr( $attributes['accentColor']     ?? '#F5C518' );
$show_breadcrumb  = (bool) ( $attributes['showBreadcrumb']  ?? true );
$show_cta         = (bool) ( $attributes['showCta']         ?? true );
$show_images      = (bool) ( $attributes['showPostImages']  ?? true );

// ── Dynamic Breadcrumb ────────────────────────────────────────────────────────
// Priority: explicit label → queried object title → fallback
if ( empty( $breadcrumb_label ) ) {
    $queried = get_queried_object();
    if ( $queried instanceof WP_Post ) {
        $breadcrumb_label = get_the_title( $queried );
    } elseif ( $queried instanceof WP_Term ) {
        $breadcrumb_label = $queried->name;
    } elseif ( $queried instanceof WP_Post_Type ) {
        $breadcrumb_label = $queried->labels->name;
    } else {
        $breadcrumb_label = $title;
    }
}
$breadcrumb_label = wp_strip_all_tags( $breadcrumb_label );

// ── Dynamic Post Images ───────────────────────────────────────────────────────
$posts = [];
if ( $show_images && ! empty( $post_type ) ) {
    // Resolve the REST base for the post type to its WP query post_type arg
    $pt_object = get_post_type_object( $post_type );
    if ( $pt_object ) {
        $query_args = [
            'post_type'              => $post_type,
            'posts_per_page'         => $image_count,
            'post_status'            => 'publish',
            'has_post_thumbnail'     => true,
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ];
        $query = new WP_Query( $query_args );
        $posts = $query->posts;
        wp_reset_postdata();
    }
}

// ── Wrapper attributes ────────────────────────────────────────────────────────
$wrapper_attrs = get_block_wrapper_attributes([
    'class' => 'hero-block',
    'style' => "--hero-bg:{$bg_color};--hero-accent:{$accent_color};",
]);
?>

<section <?php echo $wrapper_attrs; ?>>

    <?php if ( $show_breadcrumb ) : ?>
    <nav class="hero-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'mytheme' ); ?>">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumb-home">
            <?php esc_html_e( 'Home', 'mytheme' ); ?>
        </a>
        <span class="breadcrumb-sep" aria-hidden="true"> &rsaquo; </span>
        <span class="breadcrumb-current"><?php echo esc_html( $breadcrumb_label ); ?></span>
    </nav>
    <?php endif; ?>

    <div class="hero-inner">

        <div class="hero-content">
            <?php if ( $title ) : ?>
                <h1 class="hero-title"><?php echo wp_kses_post( $title ); ?></h1>
            <?php endif; ?>

            <?php if ( $subtitle ) : ?>
                <p class="hero-subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
            <?php endif; ?>

            <?php if ( $show_cta && $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ); ?>" class="hero-cta">
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            <?php endif; ?>
        </div>

        <?php if ( $show_images && ! empty( $posts ) ) : ?>
        <div class="hero-images" aria-hidden="true">
            <?php foreach ( $posts as $post ) :
                $thumb_url = get_the_post_thumbnail_url( $post->ID, 'medium' );
                if ( ! $thumb_url ) continue;
            ?>
                <div class="hero-image-item">
                    <img
                        src="<?php echo esc_url( $thumb_url ); ?>"
                        alt="<?php echo esc_attr( get_the_title( $post ) ); ?>"
                        loading="lazy"
                        decoding="async"
                    />
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div><!-- .hero-inner -->

</section>
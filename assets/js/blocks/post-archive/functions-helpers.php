<?php
/**
 * ──────────────────────────────────────────────────────────────
 *  Post Archive Block — Helper Functions
 *  Paste / require this file from your theme's functions.php:
 *    require_once get_template_directory() . '/assets/js/blocks/post-archive/functions-helpers.php';
 * ──────────────────────────────────────────────────────────────
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ──────────────────────────────────────────────────────────────
   1. Resolve a REST-base slug (e.g. "posts") → WP post type slug (e.g. "post")
   ────────────────────────────────────────────────────────────── */
if ( ! function_exists( 'fnlmx_resolve_post_type' ) ) {
    function fnlmx_resolve_post_type( string $rest_or_slug ): string {
        // Direct slug match  e.g. "post", "page", "my_cpt"
        if ( get_post_type_object( $rest_or_slug ) ) {
            return $rest_or_slug;
        }

        // REST-base lookup  e.g. "posts" → "post"
        foreach ( get_post_types( [ 'show_in_rest' => true ], 'objects' ) as $type ) {
            $rest_base = ! empty( $type->rest_base ) ? $type->rest_base : $type->name;
            if ( $rest_base === $rest_or_slug ) {
                return $type->name;
            }
        }

        // Fallback
        return 'post';
    }
}

/* ──────────────────────────────────────────────────────────────
   2. Single card template (reused by render.php + AJAX handler)
   ────────────────────────────────────────────────────────────── */
if ( ! function_exists( 'fnlmx_archive_card_template' ) ) {
    function fnlmx_archive_card_template( int $post_id ): void {
        $post        = get_post( $post_id );
        $thumb_url   = get_the_post_thumbnail_url( $post_id, 'large' );
        $title       = get_the_title( $post_id );
        $excerpt     = wp_trim_words( get_the_excerpt( $post_id ), 18, '…' );
        $link        = get_permalink( $post_id );
        ?>
        <article class="fnlmx-archive-card">
            <div class="fnlmx-archive-card__thumb">
                <?php if ( $thumb_url ) : ?>
                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                <?php else : ?>
                    <div class="fnlmx-archive-card__thumb-placeholder"></div>
                <?php endif; ?>
                <div class="fnlmx-archive-card__overlay"></div>
            </div>

            <div class="fnlmx-archive-card__content">
                <h3 class="fnlmx-archive-card__title"><?php echo esc_html( $title ); ?></h3>
                <?php if ( $excerpt ) : ?>
                    <p class="fnlmx-archive-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
                <?php endif; ?>
                <?php $btn_uid = uniqid(); ?>
                <a href="<?php echo esc_url( $link ); ?>" class="fnlmx-archive-card__btn">
                    <svg aria-hidden="true" class="fnlmx-archive-card__btn-shape" viewBox="0 0 148 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#fnlmx-archive-btn-<?php echo esc_attr( $btn_uid ); ?>)">
                            <path d="M148 30.4 L136.4 42 H0 V7 L7 0 H148 V30.4 Z" fill="currentColor"></path>
                            <path d="M148 34 V42 H140 L148 34 Z" fill="var(--decoration, currentColor)"></path>
                        </g>
                        <defs><clipPath id="fnlmx-archive-btn-<?php echo esc_attr( $btn_uid ); ?>"><rect width="148" height="42" fill="white"></rect></clipPath></defs>
                    </svg>
                    <span class="fnlmx-archive-card__btn-label"><?php esc_html_e( 'Read More', 'mytheme' ); ?></span>
                </a>
            </div>
        </article>
        <?php
    }
}

/* ──────────────────────────────────────────────────────────────
   3. AJAX: wp_ajax_fnlmx_load_more_posts
   ────────────────────────────────────────────────────────────── */
if ( ! function_exists( 'fnlmx_ajax_load_more_posts' ) ) {
    function fnlmx_ajax_load_more_posts(): void {
        check_ajax_referer( 'fnlmx_load_more', 'nonce' );

        $post_type   = sanitize_key( $_POST['post_type']    ?? 'post' );
        $per_page    = min( 48, max( 1, (int) ( $_POST['per_page'] ?? 4 ) ) );
        $page        = max( 1, (int) ( $_POST['page'] ?? 2 ) );

        $post_type   = fnlmx_resolve_post_type( $post_type );

        $query = new WP_Query( [
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
        ] );

        if ( ! $query->have_posts() ) {
            wp_send_json_success( [ 'html' => '', 'has_more' => false ] );
        }

        ob_start();
        while ( $query->have_posts() ) {
            $query->the_post();
            fnlmx_archive_card_template( get_the_ID() );
        }
        wp_reset_postdata();
        $html = ob_get_clean();

        $has_more = $page < (int) $query->max_num_pages;

        wp_send_json_success( compact( 'html', 'has_more' ) );
    }

    add_action( 'wp_ajax_fnlmx_load_more_posts',        'fnlmx_ajax_load_more_posts' );
    add_action( 'wp_ajax_nopriv_fnlmx_load_more_posts', 'fnlmx_ajax_load_more_posts' );
}

/* ──────────────────────────────────────────────────────────────
   4. Enqueue front-end JS for Load More
   ────────────────────────────────────────────────────────────── */
if ( ! function_exists( 'fnlmx_enqueue_post_archive_assets' ) ) {
    function fnlmx_enqueue_post_archive_assets(): void {
        if ( ! has_block( 'mytheme/post-archive' ) ) return;

        wp_enqueue_script(
            'fnlmx-post-archive',
            get_template_directory_uri() . '/assets/js/blocks/post-archive/load-more.js',
            [],
            filemtime( get_template_directory() . '/assets/js/blocks/post-archive/load-more.js' ),
            true
        );

        wp_localize_script( 'fnlmx-post-archive', 'fnlmxArchive', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'action'  => 'fnlmx_load_more_posts',
        ] );
    }

    add_action( 'wp_enqueue_scripts', 'fnlmx_enqueue_post_archive_assets' );
}
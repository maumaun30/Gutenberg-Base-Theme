<?php
/**
 * Post Archive Block — render.php
 *
 * Attributes:
 *   sectionLabel  (string)
 *   postType      (string)  REST base or slug
 *   postsPerPage  (int)
 *   showLoadMore  (bool)
 *   columns       (int)
 */

$section_label  = $attributes['sectionLabel']  ?? 'All Blogs';
$post_type_raw  = $attributes['postType']       ?? 'posts';
$posts_per_page = (int) ( $attributes['postsPerPage'] ?? 4 );
$show_load_more = (bool) ( $attributes['showLoadMore'] ?? true );
$columns        = (int) ( $attributes['columns'] ?? 2 );

// Resolve REST base → actual post type slug
$resolved_type = fnlmx_resolve_post_type( $post_type_raw );

$query_args = [
    'post_type'      => $resolved_type,
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'paged'          => 1,
];

$query      = new WP_Query( $query_args );
$total      = (int) $query->found_posts;
$has_more   = $total > $posts_per_page;

$block_id   = 'fnlmx-archive-' . wp_unique_id();
$wrapper    = get_block_wrapper_attributes( [
    'class'            => 'fnlmx-post-archive',
    'data-block-id'    => $block_id,
    'data-post-type'   => esc_attr( $resolved_type ),
    'data-per-page'    => $posts_per_page,
    'data-columns'     => $columns,
    'data-current-page'=> '1',
] );
?>

<div <?php echo $wrapper; ?>>
  <div class="container" style="max-width:1280px; margin:0 auto; padding:0 24px;">

    <?php if ( $section_label ) : ?>
        <p class="fnlmx-post-archive__label"><?php echo esc_html( $section_label ); ?></p>
    <?php endif; ?>

    <div
        class="fnlmx-post-archive__grid"
        style="--archive-cols: <?php echo $columns; ?>;"
        id="<?php echo esc_attr( $block_id . '-grid' ); ?>"
    >
        <?php
        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) :
                $query->the_post();
                fnlmx_archive_card_template( get_the_ID() );
            endwhile;
            wp_reset_postdata();
        else :
            echo '<p class="fnlmx-post-archive__empty">' . esc_html__( 'No posts found.', 'mytheme' ) . '</p>';
        endif;
        ?>
    </div>

    <?php if ( $show_load_more && $has_more ) : ?>
        <div class="fnlmx-post-archive__load-more-wrap">
            <button
                class="fnlmx-post-archive__load-more"
                data-grid="<?php echo esc_attr( $block_id . '-grid' ); ?>"
                data-block="<?php echo esc_attr( $block_id ); ?>"
                data-nonce="<?php echo wp_create_nonce( 'fnlmx_load_more' ); ?>"
            >
                <?php esc_html_e( 'Load More', 'mytheme' ); ?>
            </button>
        </div>
    <?php endif; ?>

  </div><!-- /.container -->
</div>
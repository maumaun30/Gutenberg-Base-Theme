<?php
$section_label       = $attributes['sectionLabel']        ?? '';
$heading             = $attributes['heading']             ?? '';
$subheading          = $attributes['subheading']          ?? '';
$posts_per_category  = (int) ( $attributes['postsPerCategory'] ?? 4 );
$show_view_all       = (bool) ( $attributes['showViewAll']     ?? true );
$selected_categories = $attributes['selectedCategories']  ?? [];

// Get PARENT terms only
$term_args = [
    'taxonomy'   => 'game_category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
    'parent'     => 0, // only parent categories
];

if ( ! empty( $selected_categories ) ) {
    $term_args['include'] = array_map( 'absint', $selected_categories );
}

$categories = get_terms( $term_args );

if ( is_wp_error( $categories ) || empty( $categories ) ) {
    return;
}
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => 'games-listing bg-dark-2 section' ] ); ?>>

  <div class="games-listing__glow" aria-hidden="true"></div>

  <div class="games-listing__container">

    <?php if ( $section_label || $heading || $subheading ) : ?>
      <div class="games-listing__header">
        <!-- Game Listing Badge Layout Here -->
        <?php if ( $heading ) : ?>
          <h2 class="games-listing__heading"><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>
        <?php if ( $subheading ) : ?>
          <p class="games-listing__subheading"><?php echo esc_html( $subheading ); ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="games-listing__categories">
      <?php foreach ( $categories as $cat ) :

        // Get all child category IDs under this parent
        $child_terms = get_terms( [
            'taxonomy'   => 'game_category',
            'child_of'   => $cat->term_id,
            'hide_empty' => false,
            'fields'     => 'ids',
        ] );

        // Combine parent + children IDs
        $all_term_ids = array_merge(
            [ $cat->term_id ],
            is_wp_error( $child_terms ) ? [] : $child_terms
        );

        $games = new WP_Query( [
            'post_type'      => 'game',
            'posts_per_page' => $posts_per_category,
            'post_status'    => 'publish',
            'tax_query'      => [ [
                'taxonomy'         => 'game_category',
                'field'            => 'term_id',
                'terms'            => $all_term_ids,
                'include_children' => false, // we handle children manually
            ] ],
        ] );

        if ( ! $games->have_posts() ) continue;
      ?>

        <div class="games-listing__category">

          <div class="games-listing__cat-header">
            <div class="games-listing__cat-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="7" width="20" height="14" rx="2"/>
                <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                <line x1="12" y1="12" x2="12" y2="16"/>
                <line x1="10" y1="14" x2="14" y2="14"/>
              </svg>
            </div>
            <div>
              <h3 class="games-listing__cat-name"><?php echo esc_html( $cat->name ); ?></h3>
              <div class="games-listing__cat-divider"></div>
            </div>
          </div>

          <div class="games-listing__grid">
            <?php while ( $games->have_posts() ) : $games->the_post();
              $price        = get_post_meta( get_the_ID(), 'game_price', true );
              $button_url   = get_post_meta( get_the_ID(), 'game_button_url', true )   ?: get_permalink();
              $button_label = get_post_meta( get_the_ID(), 'game_button_label', true ) ?: 'Play Now';
              $thumb        = get_the_post_thumbnail_url( get_the_ID(), 'large' );
            ?>
              <div class="game-card">

                <div class="game-card__image-wrap">
                  <?php if ( $thumb ) : ?>
                    <img
                      src="<?php echo esc_url( $thumb ); ?>"
                      alt="<?php the_title_attribute(); ?>"
                      class="game-card__image"
                      loading="lazy"
                    />
                  <?php else : ?>
                    <div class="game-card__image-placeholder">No image</div>
                  <?php endif; ?>
                  <div class="game-card__overlay" aria-hidden="true"></div>
                  <?php if ( $price ) : ?>
                    <span class="game-card__price"><?php echo esc_html( $price ); ?></span>
                  <?php endif; ?>
                </div>

                <div class="game-card__body">
                  <h4 class="game-card__title"><?php the_title(); ?></h4>
                  <a href="<?php echo esc_url( $button_url ); ?>" class="game-card__btn">
                    <?php echo esc_html( $button_label ); ?>
                  </a>
                </div>

                <div class="game-card__glow" aria-hidden="true"></div>
              </div>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>

          <?php if ( $show_view_all ) :
            $term_link = get_term_link( $cat );
          ?>
            <div class="games-listing__view-all">
              <a href="<?php echo esc_url( is_wp_error( $term_link ) ? '#' : $term_link ); ?>"
                class="games-listing__view-btn">View All <?php echo esc_html( $cat->name ); ?> Games</a>
            </div>
          <?php endif; ?>

        </div>

      <?php endforeach; ?>
    </div>

  </div>
</section>
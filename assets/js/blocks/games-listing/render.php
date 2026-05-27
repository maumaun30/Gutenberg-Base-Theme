<?php
$posts_per_category  = (int) ( $attributes['postsPerCategory'] ?? 6 );
$show_view_all       = (bool) ( $attributes['showViewAll']     ?? true );
$selected_categories = $attributes['selectedCategories']  ?? [];
$category_order      = $attributes['categoryOrder']       ?? [];

// Build term args — parent terms only
$term_args = [
    'taxonomy'   => 'game_category',
    'hide_empty' => true,
    'parent'     => 0,
];

if ( ! empty( $selected_categories ) ) {
    $term_args['include'] = array_map( 'absint', $selected_categories );
}

$categories = get_terms( $term_args );

if ( is_wp_error( $categories ) || empty( $categories ) ) {
    return;
}

// Apply saved category order if present
if ( ! empty( $category_order ) ) {
    $order_map = array_flip( array_map( 'intval', $category_order ) );
    usort( $categories, function( $a, $b ) use ( $order_map ) {
        $pos_a = isset( $order_map[ $a->term_id ] ) ? $order_map[ $a->term_id ] : 9999;
        $pos_b = isset( $order_map[ $b->term_id ] ) ? $order_map[ $b->term_id ] : 9999;
        return $pos_a - $pos_b;
    } );
}
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => 'games-listing bg-dark-3 section' ] ); ?>>

  <div class="games-listing__glow" aria-hidden="true"></div>

  <div class="games-listing__container">

    <!-- Category Tabs — HOT = show all, others filter by slug -->
    <nav class="games-listing__tabs" aria-label="Game categories">
      <button class="games-listing__tab is-hot is-active" data-filter="all">
        <svg class="games-listing__tab-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M13.5 0.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/>
        </svg>
        HOT
      </button>
      <?php foreach ( $categories as $cat ) :
        $cat_icon = get_field( 'fnlmx_game_ctg_icon', 'game_category_' . $cat->term_id );
      ?>
        <button class="games-listing__tab" data-filter="<?php echo esc_attr( $cat->slug ); ?>">
          <?php if ( $cat_icon ) : ?>
            <img src="<?php echo esc_url( $cat_icon ); ?>" alt="" class="games-listing__tab-icon" loading="lazy">
          <?php endif; ?>
          <?php echo esc_html( $cat->name ); ?>
        </button>
      <?php endforeach; ?>
    </nav>

    <!-- Game rows by category -->
    <div class="games-listing__categories">
      <?php foreach ( $categories as $cat ) :

        $child_terms = get_terms( [
            'taxonomy'   => 'game_category',
            'child_of'   => $cat->term_id,
            'hide_empty' => false,
            'fields'     => 'ids',
        ] );

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
                'include_children' => false,
            ] ],
        ] );

        if ( ! $games->have_posts() ) continue;

        $cat_link = get_term_link( $cat );
        $cat_id   = 'gl-cat-' . esc_attr( $cat->slug );
      ?>

        <div class="games-listing__category" data-category="<?php echo esc_attr( $cat->slug ); ?>">

          <!-- Header: category name (left) + ALL link & nav (right) -->
          <div class="games-listing__cat-header">
            <div class="games-listing__cat-header-left">
              <h3 class="games-listing__cat-name"><?php echo esc_html( $cat->name ); ?></h3>
            </div>

            <?php if ( $show_view_all ) : ?>
              <div class="games-listing__cat-header-right">
                <a href="<?php echo esc_url( is_wp_error( $cat_link ) ? '#' : $cat_link ); ?>"
                   class="games-listing__view-all-link">ALL</a>
                <div class="games-listing__nav-btns">
                  <button class="games-listing__nav-btn"
                          aria-label="Scroll left"
                          data-scroll-target="<?php echo $cat_id; ?>"
                          data-scroll-dir="-1">
                    <svg viewBox="0 0 8 12" xmlns="http://www.w3.org/2000/svg"><polygon points="8,0 8,12 0,6"/></svg>
                  </button>
                  <button class="games-listing__nav-btn"
                          aria-label="Scroll right"
                          data-scroll-target="<?php echo $cat_id; ?>"
                          data-scroll-dir="1">
                    <svg viewBox="0 0 8 12" xmlns="http://www.w3.org/2000/svg"><polygon points="0,0 0,12 8,6"/></svg>
                  </button>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <!-- Thin separator line -->
          <div class="games-listing__cat-divider"></div>

          <!-- Horizontal scroll grid -->
          <div class="games-listing__scroll-wrap">
          <div class="games-listing__grid" id="<?php echo $cat_id; ?>">
            <?php while ( $games->have_posts() ) : $games->the_post();
              $price      = get_post_meta( get_the_ID(), 'game_price', true );
              $button_url = get_post_meta( get_the_ID(), 'game_button_url', true ) ?: get_permalink();
              $thumb      = get_the_post_thumbnail_url( get_the_ID(), 'large' );
            ?>
              <a href="<?php echo esc_url( $button_url ); ?>"
                 class="game-card"
                 title="<?php the_title_attribute(); ?>">
                <div class="game-card__image-wrap">
                  <?php if ( $thumb ) : ?>
                    <img
                      src="<?php echo esc_url( $thumb ); ?>"
                      alt="<?php the_title_attribute(); ?>"
                      class="game-card__image"
                      loading="lazy"
                    />
                  <?php else : ?>
                    <div class="game-card__image-placeholder">
                      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                        fill="none" stroke="rgba(255,255,255,.12)" stroke-width="1.5">
                        <rect x="10" y="3" width="10" height="12" rx="2"/>
                        <rect x="3" y="8" width="12" height="12" rx="2"/>
                        <circle cx="6.5" cy="11.5" r="0.7"/>
                        <circle cx="11.5" cy="11.5" r="0.7"/>
                        <circle cx="6.5" cy="16.5" r="0.7"/>
                        <circle cx="11.5" cy="16.5" r="0.7"/>
                        <circle cx="9" cy="14" r="0.7"/>
                      </svg>
                    </div>
                  <?php endif; ?>
                  <!-- Permanent overlay matching Figma: #141124 95% → 0% -->
                  <div class="game-card__overlay" aria-hidden="true"></div>
                  <?php if ( $price ) : ?>
                    <span class="game-card__price"><?php echo esc_html( $price ); ?></span>
                  <?php endif; ?>
                </div>
              </a>
            <?php endwhile; wp_reset_postdata(); ?>
          </div><!-- /.games-listing__grid -->
          </div><!-- /.games-listing__scroll-wrap -->

        </div>

      <?php endforeach; ?>
    </div>

  </div>

</section>

<script>
(function () {

  /* ── Scroll buttons ── */
  document.querySelectorAll('.games-listing__nav-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var grid    = document.getElementById( btn.getAttribute('data-scroll-target') );
      if ( ! grid ) return;
      var wrap    = grid.closest('.games-listing__scroll-wrap') || grid;
      var cardWidth = ( grid.querySelector('.game-card')?.offsetWidth ?? 160 ) + 10;
      wrap.scrollBy({ left: parseInt( btn.getAttribute('data-scroll-dir'), 10 ) * cardWidth * 3, behavior: 'smooth' });
    });
  });

  /* ── Category tab filtering ── */
  var tabs       = document.querySelectorAll('.games-listing__tab');
  var categories = document.querySelectorAll('.games-listing__category');

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {

      // Update active tab
      tabs.forEach(function (t) {
        t.classList.remove('is-active');
        // keep HOT always styled with is-hot but remove active from others
      });
      tab.classList.add('is-active');

      var filter = tab.getAttribute('data-filter');

      categories.forEach(function (cat) {
        if ( filter === 'all' ) {
          cat.classList.remove('is-hidden');
        } else {
          if ( cat.getAttribute('data-category') === filter ) {
            cat.classList.remove('is-hidden');
          } else {
            cat.classList.add('is-hidden');
          }
        }
      });
    });
  });

})();
</script>
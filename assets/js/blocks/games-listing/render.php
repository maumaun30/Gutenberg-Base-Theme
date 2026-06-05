<?php
$posts_per_category  = (int)  ($attributes['postsPerCategory']   ?? 6);
$show_view_all       = (bool) ($attributes['showViewAll']         ?? true);
$selected_categories = $attributes['selectedCategories']           ?? [];
$category_order      = $attributes['categoryOrder']                ?? [];
$show_recommended    = (bool) ($attributes['showRecommended']     ?? true);
$recommended_per_page = (int) ($attributes['recommendedPerPage']  ?? 12);

/* ── Helper: render a single game card ── */
if (! function_exists('gl_game_card')) :
  function gl_game_card($button_url, $thumb, $title_attr, $price)
  { ?>
    <a href="<?php echo esc_url($button_url); ?>"
      class="game-card"
      title="<?php echo esc_attr($title_attr); ?>">
      <div class="game-card__image-wrap">
        <?php if ($thumb) : ?>

          <img src="<?php echo esc_url($thumb); ?>"
            alt=""
            aria-hidden="true"
            class="game-card__image-bg"
            loading="lazy" />

          <img src="<?php echo esc_url($thumb); ?>"
            alt="<?php echo esc_attr($title_attr); ?>"
            class="game-card__image"
            loading="lazy" />

        <?php else : ?>
          <div class="game-card__image-placeholder">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
              fill="none" stroke="rgba(255,255,255,.12)" stroke-width="1.5">
              <rect x="10" y="3" width="10" height="12" rx="2" />
              <rect x="3" y="8" width="12" height="12" rx="2" />
              <circle cx="6.5" cy="11.5" r="0.7" />
              <circle cx="11.5" cy="11.5" r="0.7" />
              <circle cx="6.5" cy="16.5" r="0.7" />
              <circle cx="11.5" cy="16.5" r="0.7" />
              <circle cx="9" cy="14" r="0.7" />
            </svg>
          </div>
        <?php endif; ?>
        <div class="game-card__overlay" aria-hidden="true"></div>
        <?php if ($price) : ?>
          <span class="game-card__price"><?php echo esc_html($price); ?></span>
        <?php endif; ?>
      </div>
    </a>
  <?php }
endif;

/* ── Helper: render nav buttons ── */
if (! function_exists('gl_nav_btns')) :
  function gl_nav_btns($target_id, $label = '')
  { ?>
    <div class="games-listing__nav-btns">
      <button class="games-listing__nav-btn"
        aria-label="Scroll <?php echo esc_attr($label); ?> left"
        data-scroll-target="<?php echo esc_attr($target_id); ?>"
        data-scroll-dir="-1">
        <svg viewBox="0 0 8 12" xmlns="http://www.w3.org/2000/svg">
          <polygon points="8,0 8,12 0,6" />
        </svg>
      </button>
      <button class="games-listing__nav-btn"
        aria-label="Scroll <?php echo esc_attr($label); ?> right"
        data-scroll-target="<?php echo esc_attr($target_id); ?>"
        data-scroll-dir="1">
        <svg viewBox="0 0 8 12" xmlns="http://www.w3.org/2000/svg">
          <polygon points="0,0 0,12 8,6" />
        </svg>
      </button>
    </div>
<?php }
endif;

/* ── Build category list ── */
// Only show categories explicitly checked in the block settings.
// If none are checked, render nothing — no fallback to all categories.
if (empty($selected_categories)) {
  return;
}

$term_args = [
  'taxonomy'   => 'game_category',
  'hide_empty' => true,
  'parent'     => 0,
  'include'    => array_map('absint', $selected_categories),
];

$categories = get_terms($term_args);

if (is_wp_error($categories) || empty($categories)) {
  return;
}

/* Apply saved category order */
if (! empty($category_order)) {
  $order_map = array_flip(array_map('intval', $category_order));
  usort($categories, function ($a, $b) use ($order_map) {
    $pa = $order_map[$a->term_id] ?? 9999;
    $pb = $order_map[$b->term_id] ?? 9999;
    return $pa - $pb;
  });
}

?>

<section <?php echo get_block_wrapper_attributes(['class' => 'games-listing bg-dark-3 section']); ?>>

  <div class="games-listing__glow" aria-hidden="true"></div>

  <div class="games-listing__container">

    <!-- ── Category Tabs ── -->
    <nav class="games-listing__tabs" aria-label="Game categories">
      <?php
      /* ── Helper: angled shape background shared by every tab ── */
      if (! function_exists('gl_tab_shape')) :
        function gl_tab_shape($uid)
        { ?>
          <svg aria-hidden="true" class="games-listing__tab-shape" viewBox="0 0 148 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#gl-tab-<?php echo esc_attr($uid); ?>)">
              <path d="M148 30.4 L136.4 42 H0 V7 L7 0 H148 V30.4 Z" fill="currentColor"></path>
              <path d="M148 34 V42 H140 L148 34 Z" fill="var(--decoration, currentColor)"></path>
            </g>
            <defs>
              <clipPath id="gl-tab-<?php echo esc_attr($uid); ?>">
                <rect width="148" height="42" fill="white"></rect>
              </clipPath>
            </defs>
          </svg>
      <?php }
      endif; ?>

      <button class="games-listing__tab is-hot is-active" data-filter="all">
        <?php gl_tab_shape('hot'); ?>
        <span class="games-listing__tab-content">
          <img src="<?php echo esc_url(get_theme_file_uri('assets/images/category-template/Latest hot icon.svg')); ?>" alt=""
            class="games-listing__tab-icon games-listing__tab-icon--default" loading="lazy">
          <img src="<?php echo esc_url(get_theme_file_uri('assets/images/category-template/Hot active icon.svg')); ?>" alt=""
            class="games-listing__tab-icon games-listing__tab-icon--active" loading="lazy">
          HOT
        </span>
      </button>
      <?php foreach ($categories as $cat) :
        $cat_icon        = get_field('fnlmx_game_ctg_icon', 'game_category_' . $cat->term_id);
        $cat_icon_active = get_field('fnlmx_game_category_active_icon', 'game_category_' . $cat->term_id);
        $cat_icon_active = is_array($cat_icon_active) ? ($cat_icon_active['url'] ?? '') : $cat_icon_active;
      ?>
        <button class="games-listing__tab" data-filter="<?php echo esc_attr($cat->slug); ?>">
          <?php gl_tab_shape($cat->slug); ?>
          <span class="games-listing__tab-content">
            <?php if ($cat_icon) : ?>
              <img src="<?php echo esc_url($cat_icon); ?>" alt=""
                class="games-listing__tab-icon games-listing__tab-icon--default" loading="lazy">
            <?php endif; ?>
            <?php if ($cat_icon_active) : ?>
              <img src="<?php echo esc_url($cat_icon_active); ?>" alt=""
                class="games-listing__tab-icon games-listing__tab-icon--active" loading="lazy">
            <?php endif; ?>
            <?php echo esc_html($cat->name); ?>
          </span>
        </button>
      <?php endforeach; ?>
    </nav>

    <!-- ── Game rows by category ── -->
    <div class="games-listing__categories">
      <?php foreach ($categories as $cat) :

        $child_ids = get_terms([
          'taxonomy'   => 'game_category',
          'child_of'   => $cat->term_id,
          'hide_empty' => false,
          'fields'     => 'ids',
        ]);
        $all_term_ids = array_merge(
          [$cat->term_id],
          is_wp_error($child_ids) ? [] : $child_ids
        );

        $games = new WP_Query([
          'post_type'      => 'game',
          'posts_per_page' => $posts_per_category,
          'post_status'    => 'publish',
          'tax_query'      => [[
            'taxonomy'         => 'game_category',
            'field'            => 'term_id',
            'terms'            => $all_term_ids,
            'include_children' => false,
          ]],
        ]);

        if (! $games->have_posts()) continue;

        $cat_link = get_term_link($cat);
        $cat_id   = 'gl-cat-' . esc_attr($cat->slug);
      ?>

        <div class="games-listing__category" data-category="<?php echo esc_attr($cat->slug); ?>">

          <div class="games-listing__cat-header">
            <div class="games-listing__cat-header-left">
              <h3 class="games-listing__cat-name"><?php echo esc_html($cat->name); ?></h3>
            </div>
            <?php if ($show_view_all) : ?>
              <div class="games-listing__cat-header-right">
                <a href="<?php echo esc_url(is_wp_error($cat_link) ? '#' : $cat_link); ?>"
                  class="games-listing__view-all-link">
                  <span class="games-listing__view-all-label">ALL</span>
                  <svg aria-hidden="true" class="games-listing__view-all-shape" viewBox="0 0 48 24" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#gl-allbtn-<?php echo esc_attr($cat->slug); ?>)">
                      <path d="M48 12.4 L36.4 24 H0 V7 L7 0 H48 V12.4 Z" fill="currentColor"></path>
                      <path d="M48 16 V24 H40 L48 16 Z" fill="var(--decoration, currentColor)"></path>
                    </g>
                    <defs>
                      <clipPath id="gl-allbtn-<?php echo esc_attr($cat->slug); ?>">
                        <rect width="48" height="24" fill="white"></rect>
                      </clipPath>
                    </defs>
                  </svg>
                </a>
                <?php gl_nav_btns($cat_id, $cat->name); ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="games-listing__cat-divider"></div>

          <div class="games-listing__scroll-wrap">
            <div class="games-listing__grid" id="<?php echo $cat_id; ?>">
              <?php while ($games->have_posts()) : $games->the_post();
                gl_game_card(
                  get_post_meta(get_the_ID(), 'game_button_url', true) ?: get_permalink(),
                  get_the_post_thumbnail_url(get_the_ID(), 'large'),
                  get_the_title(),
                  get_post_meta(get_the_ID(), 'game_price', true)
                );
              endwhile;
              wp_reset_postdata(); ?>
            </div>
          </div>

        </div>

      <?php endforeach; ?>
    </div>

    <!-- ── Recommended Games — auto-queried via game-tag: recommended-games ── -->
    <?php if ($show_recommended) :
      $rec_tag = get_term_by('slug', 'recommended-games', 'game-tag');
      if ($rec_tag) :
        $rec_query = new WP_Query([
          'post_type'      => 'game',
          'post_status'    => 'publish',
          'posts_per_page' => $recommended_per_page,
          'tax_query'      => [[
            'taxonomy' => 'game-tag',
            'field'    => 'term_id',
            'terms'    => $rec_tag->term_id,
          ]],
        ]);
        if ($rec_query->have_posts()) :
          $rec_id = 'gl-recommended';
    ?>
          <div class="games-listing__category games-listing__recommended" data-category="__recommended__">
            <div class="games-listing__cat-header">
              <div class="games-listing__cat-header-left">
                <h3 class="games-listing__cat-name">Recommended Games</h3>
              </div>
              <div class="games-listing__cat-header-right">
                <?php gl_nav_btns($rec_id, 'recommended'); ?>
              </div>
            </div>
            <div class="games-listing__cat-divider"></div>
            <div class="games-listing__scroll-wrap">
              <div class="games-listing__grid" id="<?php echo $rec_id; ?>">
                <?php while ($rec_query->have_posts()) : $rec_query->the_post();
                  gl_game_card(
                    get_post_meta(get_the_ID(), 'game_button_url', true) ?: get_permalink(),
                    get_the_post_thumbnail_url(get_the_ID(), 'large'),
                    get_the_title(),
                    get_post_meta(get_the_ID(), 'game_price', true)
                  );
                endwhile;
                wp_reset_postdata(); ?>
              </div>
            </div>
          </div>
    <?php endif;
      endif;
    endif; ?>

  </div><!-- /.games-listing__container -->

</section>

<script>
  (function() {

    /* ── Scroll buttons ── */
    document.querySelectorAll('.games-listing__nav-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var grid = document.getElementById(btn.getAttribute('data-scroll-target'));
        if (!grid) return;
        var wrap = grid.closest('.games-listing__scroll-wrap') || grid;
        var cardWidth = (grid.querySelector('.game-card')?.offsetWidth ?? 160) + 10;
        wrap.scrollBy({
          left: parseInt(btn.getAttribute('data-scroll-dir'), 10) * cardWidth * 1,
          behavior: 'smooth'
        }); /*3*/
      });
    });

    /* ── Category tab filtering ── */
    var tabs = document.querySelectorAll('.games-listing__tab');
    var catRows = document.querySelectorAll('.games-listing__category[data-category]');

    tabs.forEach(function(tab) {
      tab.addEventListener('click', function() {

        tabs.forEach(function(t) {
          t.classList.remove('is-active');
        });
        tab.classList.add('is-active');

        var filter = tab.getAttribute('data-filter');

        catRows.forEach(function(row) {
          var rowCat = row.getAttribute('data-category');

          if (filter === 'all') {
            // HOT tab — show all category rows AND recommended
            row.classList.remove('is-hidden');
          } else {
            // Specific category tab — show only that category, hide recommended
            if (rowCat === filter) {
              row.classList.remove('is-hidden');
            } else {
              row.classList.add('is-hidden');
            }
          }
        });
      });
    });

  })();
</script>
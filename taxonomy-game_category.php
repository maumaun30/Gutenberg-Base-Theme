<?php

/**
 * Template: taxonomy-game_category.php
 * CPT      : game
 * Taxonomy : game_category  (hierarchical)
 *
 * Sub-category pills  → in-page JS filter (no page reload / navigation)
 * Each collection row → horizontal drag + arrow slider
 */

get_header();

$current_term   = get_queried_object();
$term_id        = $current_term->term_id;
$term_name      = $current_term->name;
$term_desc      = $current_term->description;
$parent_term_id = $current_term->parent;

/* Direct child terms */
$child_terms = get_terms([
  'taxonomy'   => 'game_category',
  'parent'     => $term_id,
  'hide_empty' => false,
  'orderby'    => 'name',
  'order'      => 'ASC',
]);
$has_children = (! empty($child_terms) && ! is_wp_error($child_terms));

/* Breadcrumb ancestors */
$ancestors = array_reverse(get_ancestors($term_id, 'game_category', 'taxonomy'));

/* Total game count */
$total_q = new WP_Query([
  'post_type'      => 'game',
  'tax_query'      => [[
    'taxonomy' => 'game_category',
    'field' => 'term_id',
    'terms' => $term_id,
    'include_children' => true
  ]],
  'posts_per_page' => 1,
  'fields' => 'ids',
]);
$game_count = $total_q->found_posts;
wp_reset_postdata();

/*
 * Pre-fetch all games per child term.
 * All data is rendered in HTML; JS shows/hides collections — no AJAX.
 */
$collections = [];
if ($has_children) {
  foreach ($child_terms as $ct) {
    $q = new WP_Query([
      'post_type'      => 'game',
      'tax_query'      => [[
        'taxonomy' => 'game_category',
        'field' => 'term_id',
        'terms' => $ct->term_id,
        'include_children' => true
      ]],
      'posts_per_page' => 20,
      'orderby'        => 'date',
      'order'          => 'DESC',
    ]);
    $games = [];
    if ($q->have_posts()) {
      while ($q->have_posts()) {
        $q->the_post();
        $gcats = get_the_terms(get_the_ID(), 'game_category');
        $games[] = [
          'id'        => get_the_ID(),
          'title'     => get_the_title(),
          'permalink' => get_permalink(),
          'thumb'     => get_the_post_thumbnail_url(get_the_ID(), 'large'),
          'badge'     => ($gcats && ! is_wp_error($gcats)) ? $gcats[0]->name : '',
        ];
      }
      wp_reset_postdata();
    }
    if (! empty($games)) {
      $collections[$ct->term_id] = ['term' => $ct, 'games' => $games];
    }
  }
}
?>
<style>
  /* ---- Page --------------------------------------------------------- */
  .gc-page {
    background: var(--bg-dark-3);
    min-height: 100vh;
    color: #fff;
  }

  /* ---- Hero --------------------------------------------------------- */
  .gc-hero {
    background: var(--bg-dark-1);
    padding: calc(var(--section-py) + 60px) 0 var(--section-py);
    position: relative;
    overflow: hidden;
  }

  .gc-orb {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    filter: blur(120px);
    opacity: .2;
  }

  .gc-orb--1 {
    top: 0;
    left: 25%;
    width: 24rem;
    height: 24rem;
    background: radial-gradient(circle, var(--color-primary), transparent);
  }

  .gc-orb--2 {
    bottom: 0;
    right: 25%;
    width: 24rem;
    height: 24rem;
    background: radial-gradient(circle, var(--color-secondary), transparent);
  }

  .gc-hero__inner {
    position: relative;
    z-index: 10;
    text-align: center;
    max-width: 60rem;
    margin: 0 auto;
    padding: 0 1.5rem;
  }

  /* Breadcrumb */
  .gc-bc {
    display: flex;
    align-items: center;
    gap: .5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
    font-family: 'Outfit', sans-serif;
    font-size: .8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .1em;
  }

  .gc-bc a {
    color: rgba(255, 255, 255, .5);
    text-decoration: none;
    transition: color .2s;
  }

  .gc-bc a:hover {
    color: var(--color-primary);
  }

  .gc-bc__sep {
    color: rgba(255, 255, 255, .25);
  }

  .gc-bc__cur {
    color: var(--color-primary);
  }

  /* Badge pill */
  .gc-badge {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .75rem 1.5rem;
    border-radius: 9999px;
    margin-bottom: 2rem;
    background: rgba(247, 29, 194, .1);
    border: 1px solid rgba(247, 29, 194, .3);
    font-family: 'Outfit', sans-serif;
    font-size: .875rem;
    font-weight: 600;
    color: var(--color-primary);
    text-transform: uppercase;
    letter-spacing: .1em;
  }

  /* Heading */
  .gc-hero h1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: var(--text-h1);
    color: #fff;
    letter-spacing: .05em;
    line-height: 1.1;
    margin-bottom: 1.25rem;
  }

  .gc-hero h1 span {
    color: var(--color-primary);
  }

  .gc-hero__desc {
    font-family: 'Lexend', sans-serif;
    font-size: var(--text-lead);
    color: rgba(255, 255, 255, .7);
    line-height: 1.7;
    /*max-width: 42rem;*/
    margin: 0 auto 2.5rem;
  }

  /* Stats */
  .gc-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    max-width: 48rem;
    margin: 0 auto;
  }

  @media(min-width:640px) {
    .gc-stats {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  .gc-stat {
    padding: 1rem;
    border-radius: .75rem;
    background: var(--bg-dark-4);
    border: 1px solid var(--border);
    text-align: center;
    animation: fadeUp .5s ease both;
  }

  .gc-stat svg {
    width: 1.5rem;
    height: 1.5rem;
    margin: 0 auto;
    color: var(--color-primary);
  }

  .gc-stat__val {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.5rem;
    color: #fff;
    letter-spacing: .05em;
    margin: .5rem 0 .25rem;
  }

  .gc-stat__lbl {
    font-family: 'Lexend', sans-serif;
    font-size: .8rem;
    color: rgba(255, 255, 255, .6);
  }

  /* ------------------------------------------------------------------ */
  /*  STICKY FILTER PILLS                                                */
  /* ------------------------------------------------------------------ */
  .gc-filters {
    background: var(--bg-dark-2);
    padding: 1.75rem 0;
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    z-index: 50;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
  }

  .gc-filters__inner {
    max-width: 80rem;
    margin: 0 auto;
    padding: 0 1.5rem;
  }

  .gc-filters__lbl {
    font-family: 'Outfit', sans-serif;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: rgba(255, 255, 255, .35);
    margin-bottom: .875rem;
  }

  .gc-filters__row {
    display: flex;
    flex-wrap: wrap;
    gap: .625rem;
  }

  .gc-pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem 1.125rem;
    border-radius: 9999px;
    cursor: pointer;
    background: var(--bg-dark-4);
    border: 1px solid var(--border);
    font-family: 'Outfit', sans-serif;
    font-size: .8rem;
    font-weight: 600;
    color: rgba(255, 255, 255, .7);
    transition: all .22s ease;
    user-select: none;
  }

  .gc-pill:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
  }

  .gc-pill.is-active {
    background: var(--color-primary);
    color: #000;
    border-color: var(--color-primary);
    box-shadow: var(--shadow-glow);
  }

  .gc-pill__count {
    background: rgba(0, 0, 0, .22);
    border-radius: 9999px;
    padding: .1rem .45rem;
    font-size: .65rem;
    font-weight: 700;
  }

  .gc-pill.is-active .gc-pill__count {
    background: rgba(0, 0, 0, .25);
  }

  /* ------------------------------------------------------------------ */
  /*  MAIN SECTION                                                       */
  /* ------------------------------------------------------------------ */
  .gc-section {
    background: var(--bg-dark-2);
    padding: var(--section-py) 0;
  }

  .gc-section-contents {
    background: var(--bg-dark-2);
    padding: 0 0 40px;
  }

  .gc-container {
    max-width: 80rem;
    margin: 0 auto;
    padding: 0 1.5rem;
  }

  /* Collection block */
  .gc-collection {
    margin-bottom: 4rem;
    animation: fadeUp .4s ease both;
  }

  .gc-collection.is-hidden {
    display: none;
  }

  .gc-collection:last-child {
    margin-bottom: 0;
  }

  /* Collection header */
  .gc-col-hd {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  @media(min-width:768px) {
    .gc-col-hd {
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
    }
  }

  .gc-col-hd__left {
    display: flex;
    align-items: center;
    gap: .875rem;
  }

  .gc-col-icon {
    width: 3rem;
    height: 3rem;
    border-radius: .75rem;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .gc-col-icon svg {
    width: 1.375rem;
    height: 1.375rem;
    color: #000;
  }

  .gc-col-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: var(--text-h3);
    color: #fff;
    letter-spacing: .05em;
    margin: 0 0 .15rem;
  }

  .gc-col-desc {
    font-family: 'Lexend', sans-serif;
    font-size: .875rem;
    color: rgba(255, 255, 255, .55);
    margin: 0;
  }

  /* Arrow buttons */
  .gc-slider-nav {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-shrink: 0;
  }

  .gc-arr {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 50%;
    cursor: pointer;
    background: var(--bg-dark-4);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, .7);
    transition: all .22s;
  }

  .gc-arr:hover {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: #000;
    box-shadow: var(--shadow-glow);
  }

  .gc-arr:disabled {
    opacity: .3;
    pointer-events: none;
  }

  .gc-arr svg {
    width: 1rem;
    height: 1rem;
  }

  /* Slider */
  .gc-slider-wrap {
    position: relative;
  }

  .gc-slider-wrap::after {
    content: '';
    pointer-events: none;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 5rem;
    z-index: 2;
    background: linear-gradient(to right, transparent, var(--bg-dark-2));
  }

  .gc-slider {
    display: flex;
    gap: 1.125rem;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: .5rem;
    cursor: grab;
  }

  .gc-slider:active {
    cursor: grabbing;
  }

  .gc-slider::-webkit-scrollbar {
    display: none;
  }

  /* Card */
  .gc-card {
    scroll-snap-align: start;
    flex: 0 0 calc(50% - .5rem);
    position: relative;
    border-radius: 1rem;
    overflow: hidden;
    background: var(--bg-dark-4);
    border: 1px solid var(--border);
    text-decoration: none;
    display: block;
    transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
  }

  @media(min-width:480px) {
    .gc-card {
      flex: 0 0 calc(33.333% - .75rem);
    }
  }

  @media(min-width:768px) {
    .gc-card {
      flex: 0 0 calc(25% - .85rem);
    }
  }

  @media(min-width:1280px) {
    .gc-card {
      flex: 0 0 calc(20% - .9rem);
    }
  }

  .gc-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg);
    border-color: var(--border-strong);
  }

  .gc-card:hover .gc-card__img {
    transform: scale(1.07);
  }

  .gc-card:hover .gc-card__name {
    color: var(--color-primary);
  }

  .gc-card:hover .gc-card__glow {
    opacity: 1;
  }

  .gc-card__img-wrap {
    position: relative;
    aspect-ratio: 3/4;
    overflow: hidden;
  }

  .gc-card__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .6s ease;
    display: block;
  }

  .gc-card__fallback {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--bg-dark-4), var(--bg-gray-4));
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .gc-card__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, .92) 0%, rgba(0, 0, 0, .25) 55%, transparent 100%);
  }

  .gc-card__badge {
    position: absolute;
    top: .625rem;
    left: .625rem;
    padding: .2rem .65rem;
    border-radius: 9999px;
    background: rgba(247, 29, 194, .2);
    border: 1px solid rgba(247, 29, 194, .45);
    color: var(--color-primary);
    font-family: 'Outfit', sans-serif;
    font-size: .6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    backdrop-filter: blur(6px);
  }

  .gc-card__info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
  }

  .gc-card__name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: var(--text-h4);
    color: #fff;
    letter-spacing: .04em;
    margin: 0;
    line-height: 1.1;
    transition: color .3s;
  }

  .gc-card__glow {
    position: absolute;
    inset: 0;
    pointer-events: none;
    opacity: 0;
    background: radial-gradient(circle at 50% 0%, rgba(247, 29, 194, .18), transparent 70%);
    transition: opacity .5s;
  }

  /* Empty */
  .gc-empty {
    text-align: center;
    padding: 5rem 1rem;
    font-family: 'Lexend', sans-serif;
    color: rgba(255, 255, 255, .4);
  }

  /* Keyframes */
  @keyframes fadeUp {
    from {
      opacity: 0;
      transform: translateY(18px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Description card */
.sg-desc-card {
  background:var(--bg-dark-4); border:1px solid var(--border);
  border-radius:1.25rem;; padding:2rem;
}
.sg-desc-card h2 {
  font-family:'Bebas Neue',sans-serif; font-size:var(--text-h3);
  color:#fff; letter-spacing:.05em; margin:0 0 1.25rem;
  padding-bottom:.875rem; border-bottom:1px solid var(--border);
}
.sg-desc-card .sg-content {
  font-family:'Lexend',sans-serif; font-size:.95rem; line-height:1.8;
  color:rgba(255,255,255,.75);
}
.sg-desc-card .sg-content p  { margin-bottom:1rem; }
.sg-desc-card .sg-content p:last-child { margin-bottom:0; }
.sg-desc-card .sg-content h3 {
  font-family:'Bebas Neue',sans-serif; font-size:1.25rem;
  color:var(--color-primary); letter-spacing:.05em; margin:1.5rem 0 .5rem;
}
.sg-desc-card .sg-content ul { list-style:none; padding:0; }
.sg-desc-card .sg-content ul li {
  padding:.35rem 0 .35rem 1.5rem; position:relative;
}
.sg-desc-card .sg-content ul li::before {
  content:''; position:absolute; left:0; top:.8rem;
  width:.45rem; height:.45rem; border-radius:50%;
  background:var(--color-primary);
}
</style>

<div class="gc-page">

  <!-- ════════════════════════ HERO ══════════════════════════════════ -->
  <?php
  set_query_var( 'term',           $current_term );
  set_query_var( 'term_name',      $term_name );
  set_query_var( 'term_desc',      $term_desc );
  set_query_var( 'parent_term_id', $parent_term_id );
  set_query_var( 'ancestors',      $ancestors );
  set_query_var( 'game_count',     $game_count );
  get_template_part( 'template-parts/game-category-hero' );
  ?>
  <!-- /hero -->


  <?php if ($has_children && ! empty($collections)) : ?>

    <!-- ════════════════ STICKY FILTER PILLS ═══════════════════════════ -->
    <div class="gc-filters" id="gc-filter-bar">
      <div class="gc-filters__inner">
        <p class="gc-filters__lbl">Explore More <?php echo esc_html($term_name); ?> Games</p>
        <div class="gc-filters__row" id="gc-pill-row">

          <button class="gc-pill is-active" data-filter="all">
            All
            <span class="gc-pill__count"><?php echo array_sum(array_map(fn($c) => count($c['games']), $collections)); ?></span>
          </button>

          <?php foreach ($collections as $cid => $col) : ?>
            <button class="gc-pill" data-filter="col-<?php echo esc_attr($cid); ?>">
              <?php echo esc_html($col['term']->name); ?>
              <span class="gc-pill__count"><?php echo count($col['games']); ?></span>
            </button>
          <?php endforeach; ?>

        </div>
      </div>
    </div>


    <!-- ════════════════ COLLECTION SLIDERS ════════════════════════════ -->
    <section class="gc-section">
      <div class="gc-container" id="gc-collections">

        <?php foreach ($collections as $cid => $col) :
          $ct = $col['term'];
        ?>
          <div class="gc-collection"
            id="col-<?php echo esc_attr($cid); ?>"
            data-collection="col-<?php echo esc_attr($cid); ?>">

            <!-- Header row -->
            <div class="gc-col-hd">
              <div class="gc-col-hd__left">
                <div class="gc-col-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02
                                 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                  </svg>
                </div>
                <div>
                  <h2 class="gc-col-title"><?php echo esc_html($ct->name); ?></h2>
                  <p class="gc-col-desc">
                    <?php echo $ct->description
                      ? esc_html($ct->description)
                      : count($col['games']) . ' games in this category'; ?>
                  </p>
                </div>
              </div>

              <!-- Prev / Next arrows -->
              <div class="gc-slider-nav">
                <button class="gc-arr gc-arr--prev"
                  data-target="slider-<?php echo esc_attr($cid); ?>"
                  aria-label="Previous" disabled>
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6" />
                  </svg>
                </button>
                <button class="gc-arr gc-arr--next"
                  data-target="slider-<?php echo esc_attr($cid); ?>"
                  aria-label="Next">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Horizontal slider -->
            <div class="gc-slider-wrap">
              <div class="gc-slider" id="slider-<?php echo esc_attr($cid); ?>">
                <?php foreach ($col['games'] as $g) : ?>
                  <a href="<?php echo esc_url($g['permalink']); ?>" class="gc-card">
                    <div class="gc-card__img-wrap">
                      <?php if ($g['thumb']) : ?>
                        <img src="<?php echo esc_url($g['thumb']); ?>"
                          alt="<?php echo esc_attr($g['title']); ?>"
                          class="gc-card__img" loading="lazy">
                      <?php else : ?>
                        <div class="gc-card__fallback">
                          <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24"
                            fill="none" stroke="rgba(255,255,255,.15)" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">

                            <!-- card (back) -->
                            <rect x="10" y="3" width="10" height="12" rx="2"/>

                            <!-- card symbol -->
                            <path d="M15 7c-1 .8-1.6 1.3-1.6 2 0 .6.5 1 1 1 .5 0 .8-.2 1.1-.6.3.4.6.6 1.1.6.5 0 1-.4 1-1 0-.7-.6-1.2-1.6-2l-.5-.4-.5.4z"/>
                            <line x1="15" y1="10" x2="15" y2="12"/>

                            <!-- dice (front) -->
                            <rect x="3" y="8" width="12" height="12" rx="2"/>

                            <!-- dice dots -->
                            <circle cx="6.5" cy="11.5" r="0.7"/>
                            <circle cx="11.5" cy="11.5" r="0.7"/>
                            <circle cx="6.5" cy="16.5" r="0.7"/>
                            <circle cx="11.5" cy="16.5" r="0.7"/>
                            <circle cx="9" cy="14" r="0.7"/>
                        </svg>
                        </div>
                      <?php endif; ?>
                      <div class="gc-card__overlay"></div>
                      <?php if ($g['badge']) : ?>
                        <span class="gc-card__badge"><?php echo esc_html($g['badge']); ?></span>
                      <?php endif; ?>
                      <div class="gc-card__info">
                        <h3 class="gc-card__name"><?php echo esc_html($g['title']); ?></h3>
                      </div>
                    </div>
                    <div class="gc-card__glow"></div>
                  </a>
                <?php endforeach; ?>
              </div>
            </div><!-- /.gc-slider-wrap -->

          </div><!-- /.gc-collection -->
        <?php endforeach; ?>

      </div><!-- /#gc-collections -->
    </section>


  <?php else : ?>
    <!-- ════════════════ FLAT SLIDER (leaf term, no children) ══════════ -->
    <section class="gc-section">
      <div class="gc-container">
        <?php if (have_posts()) : ?>
          <div class="gc-collection">
            <div class="gc-col-hd">
              <div class="gc-col-hd__left">
                <div class="gc-col-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="6" y1="11" x2="10" y2="11" />
                    <line x1="8" y1="9" x2="8" y2="13" />
                    <line x1="15" y1="12" x2="15.01" y2="12" />
                    <line x1="18" y1="10" x2="18.01" y2="10" />
                    <path d="M17.32 5H6.68a4 4 0 0 0-3.978 3.59C2.604 9.416 2 14.456 2 16
                         a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.414-1.414A2 2 0 0 1 9.828 16
                         h4.344a2 2 0 0 1 1.414.586L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3
                         c0-1.545-.604-6.584-.685-7.258A4 4 0 0 0 17.32 5z" />
                  </svg>
                </div>
                <div>
                  <h2 class="gc-col-title"><?php echo esc_html($term_name); ?></h2>
                  <p class="gc-col-desc"><?php echo number_format($game_count); ?> games available</p>
                </div>
              </div>
              <div class="gc-slider-nav">
                <button class="gc-arr gc-arr--prev" data-target="slider-flat" aria-label="Prev" disabled>
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6" />
                  </svg>
                </button>
                <button class="gc-arr gc-arr--next" data-target="slider-flat" aria-label="Next">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6" />
                  </svg>
                </button>
              </div>
            </div>

            <div class="gc-slider-wrap">
              <div class="gc-slider" id="slider-flat">
                <?php while (have_posts()) : the_post();
                  $thumb  = get_the_post_thumbnail_url(get_the_ID(), 'large');
                  $gcats  = get_the_terms(get_the_ID(), 'game_category');
                  $badge  = ($gcats && ! is_wp_error($gcats)) ? $gcats[0]->name : '';
                ?>
                  <a href="<?php the_permalink(); ?>" class="gc-card">
                    <div class="gc-card__img-wrap">
                      <?php if ($thumb) : ?>
                        <img src="<?php echo esc_url($thumb); ?>"
                          alt="<?php the_title_attribute(); ?>"
                          class="gc-card__img" loading="lazy">
                      <?php else : ?>
                        <div class="gc-card__fallback">
                          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                            fill="none" stroke="rgba(255,255,255,.15)" stroke-width="1.5">
                            <rect x="2" y="3" width="20" height="14" rx="2" />
                            <line x1="8" y1="21" x2="16" y2="21" />
                            <line x1="12" y1="17" x2="12" y2="21" />
                          </svg>
                        </div>
                      <?php endif; ?>
                      <div class="gc-card__overlay"></div>
                      <?php if ($badge) : ?>
                        <span class="gc-card__badge"><?php echo esc_html($badge); ?></span>
                      <?php endif; ?>
                      <div class="gc-card__info">
                        <h3 class="gc-card__name"><?php the_title(); ?></h3>
                      </div>
                    </div>
                    <div class="gc-card__glow"></div>
                  </a>
                <?php endwhile; ?>
              </div>
            </div>
          </div>

          <?php the_posts_pagination([
            'mid_size'  => 2,
            'prev_text' => '&larr; Prev',
            'next_text' => 'Next &rarr;',
          ]); ?>

        <?php else : ?>
          <div class="gc-empty">
            <p>No games found in this category.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

</div><!-- /.gc-page -->

<!-- ════════════════ GAME CATEGORY CONTENTS ══════════ -->
    <?php
// Get ACF field for current taxonomy term
$term_contents = get_field('fnlmx_game_ctg_contents', 'game_category_' . $term_id);

if ( $term_contents ) : ?>
  <section class="gc-section-contents">
    <div class="gc-container">
      <div class="gc-term-contents">
        <div class="sg-desc-card sg-fadein">
          <?php echo wp_kses_post( $term_contents ); ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- ════════════════ GAME CATEGORY QUICK GUIDE ══════════ -->
    <?php
// Get ACF field for current taxonomy term
$term_contents = get_field('fnlmx_game_ctg_guide', 'game_category_' . $term_id);

if ( $term_contents ) : ?>
  <section class="gc-section-contents">
    <div class="gc-container">
      <div class="gc-term-contents">
        <div class="sg-desc-card sg-fadein">
          <?php echo wp_kses_post( $term_contents ); ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>


<!-- ════════════════════════ SCRIPTS ═══════════════════════════════ -->
<script>
  (function() {
    'use strict';

    /* ── 1. FILTER PILLS ─────────────────────────────────────────── */
    var pillRow = document.getElementById('gc-pill-row');
    var collections = document.querySelectorAll('.gc-collection[data-collection]');

    if (pillRow) {
      pillRow.addEventListener('click', function(e) {
        var pill = e.target.closest('.gc-pill');
        if (!pill) return;

        /* Swap active pill */
        pillRow.querySelectorAll('.gc-pill').forEach(function(p) {
          p.classList.remove('is-active');
        });
        pill.classList.add('is-active');

        var filter = pill.dataset.filter; /* 'all'  |  'col-{id}' */

        collections.forEach(function(col) {
          if (filter === 'all' || col.dataset.collection === filter) {
            col.classList.remove('is-hidden');
          } else {
            col.classList.add('is-hidden');
          }
        });

        /* Scroll smoothly to just below the sticky bar */
        var bar = document.getElementById('gc-filter-bar');
        var section = document.getElementById('gc-collections');
        if (section) {
          var offset = bar ? bar.offsetHeight : 0;
          var top = section.getBoundingClientRect().top + window.pageYOffset - offset - 16;
          window.scrollTo({
            top: top,
            behavior: 'smooth'
          });
        }
      });
    }


    /* ── 2. ARROW BUTTONS ────────────────────────────────────────── */
    function cardScrollAmount(slider) {
      var card = slider.querySelector('.gc-card');
      return card ? (card.offsetWidth + 18) * 2 : 320;
    }

    document.querySelectorAll('.gc-arr').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var slider = document.getElementById(this.dataset.target);
        if (!slider) return;
        var dir = this.classList.contains('gc-arr--prev') ? -1 : 1;
        slider.scrollBy({
          left: dir * cardScrollAmount(slider),
          behavior: 'smooth'
        });
      });
    });


    /* ── 3. MOUSE DRAG ───────────────────────────────────────────── */
    document.querySelectorAll('.gc-slider').forEach(function(slider) {
      var isDown = false,
        startX, scrollLeft, moved = false;

      slider.addEventListener('mousedown', function(e) {
        isDown = true;
        moved = false;
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
        slider.style.cursor = 'grabbing';
      });
      document.addEventListener('mouseup', function() {
        if (!isDown) return;
        isDown = false;
        slider.style.cursor = 'grab';
      });
      slider.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        e.preventDefault();
        moved = true;
        var x = e.pageX - slider.offsetLeft;
        slider.scrollLeft = scrollLeft - (x - startX) * 1.5;
      });
      /* Block card link clicks after a drag */
      slider.addEventListener('click', function(e) {
        if (moved) {
          e.preventDefault();
          moved = false;
        }
      }, true);
    });


    /* ── 4. ARROW DISABLED STATE ─────────────────────────────────── */
    function syncArrows(slider) {
      var id = slider.id;
      var prev = document.querySelector('.gc-arr--prev[data-target="' + id + '"]');
      var next = document.querySelector('.gc-arr--next[data-target="' + id + '"]');
      if (!prev || !next) return;
      prev.disabled = slider.scrollLeft <= 2;
      next.disabled = slider.scrollLeft >= slider.scrollWidth - slider.clientWidth - 2;
    }

    document.querySelectorAll('.gc-slider').forEach(function(slider) {
      syncArrows(slider);
      slider.addEventListener('scroll', function() {
        syncArrows(slider);
      }, {
        passive: true
      });
    });

  })();
</script>

<?php get_footer(); ?>
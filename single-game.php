<?php

/**
 * Template:  single-game.php
 * CPT:       game
 *
 * Layout (matches Single Game Page mockup):
 *  1. Hero       — blurred backdrop + thumbnail, title, short description, Play Now + Try Demo
 *  2. Related    — "More {Provider} Games" horizontal slider with "View All" link
 *  3. Two-col    — About the Game (with Read More) | Game Rules (ACF repeater)
 *  4. CTA        — handled via shortcode inside post content
 */

get_header();

if (! have_posts()) {
  echo '<p style="color:#fff;text-align:center;padding:4rem;">Game not found.</p>';
  get_footer();
  exit;
}
the_post();

$post_id  = get_the_ID();
$title    = get_the_title();
$content  = get_the_content();
$excerpt  = get_the_excerpt();
$thumb_lg = get_the_post_thumbnail_url($post_id, 'large');
$thumb_xl = get_the_post_thumbnail_url($post_id, 'full');

function gc_acf($key, $id)
{
  return function_exists('get_field') ? get_field($key, $id) : get_post_meta($id, $key, true);
}

$game_url     = gc_acf('game_url',      $post_id);
$demo_url     = gc_acf('demo_url',      $post_id);
$provider     = gc_acf('provider',      $post_id);
$short_desc   = gc_acf('short_description', $post_id);
$game_rules   = gc_acf('game_rules',    $post_id); // ACF repeater: rule_title, rule_desc

/* Hero subtitle: prefer short_description ACF, then excerpt, then trimmed content */
$hero_desc = gc_acf('fnlmx_game_short_description', $post_id);;

/* Taxonomy */
$game_cats   = get_the_terms($post_id, 'game_category');
$primary_cat = ($game_cats && ! is_wp_error($game_cats)) ? $game_cats[0] : null;

/* Breadcrumb: only the top-level parent category (skip sub-cats), then game title */
$ancestors = [];
if ($primary_cat) {
  $chain = get_ancestors($primary_cat->term_id, 'game_category', 'taxonomy');
  $top_cat_id = ! empty($chain) ? end($chain) : $primary_cat->term_id;
  $ancestors[] = $top_cat_id;
}
$term_name = $title;

/* Related games — pull from the TOP-LEVEL parent category (includes all sub-cats) */
$related_games = [];
if ($primary_cat) {
  $cat_chain  = get_ancestors($primary_cat->term_id, 'game_category', 'taxonomy');
  $parent_id  = ! empty($cat_chain) ? end($cat_chain) : $primary_cat->term_id;

  $rq = new WP_Query([
    'post_type'      => 'game',
    'tax_query'      => [[
      'taxonomy'         => 'game_category',
      'field'            => 'term_id',
      'terms'            => $parent_id,
      'include_children' => true,
    ]],
    'posts_per_page' => 12,
    'post__not_in'   => [$post_id],
    'orderby'        => 'rand',
  ]);
  if ($rq->have_posts()) {
    while ($rq->have_posts()) {
      $rq->the_post();
      $related_games[] = [
        'id'        => get_the_ID(),
        'title'     => get_the_title(),
        'permalink' => get_permalink(),
        'thumb'     => get_the_post_thumbnail_url(get_the_ID(), 'large'),
      ];
    }
    wp_reset_postdata();
  }
}

/* Heading label for related strip — top-level parent of the primary game_category */
$related_label = 'Similar';
if ( $primary_cat ) {
  $cat_ancestors = get_ancestors( $primary_cat->term_id, 'game_category', 'taxonomy' );
  $top_id        = ! empty( $cat_ancestors ) ? end( $cat_ancestors ) : $primary_cat->term_id;
  $top_term      = get_term( $top_id, 'game_category' );
  if ( $top_term && ! is_wp_error( $top_term ) ) {
    $related_label = $top_term->name;
  }
}
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap');
  :root {
    --color-primary: #f71dc2;
    --color-primary-hover: #fb11c1;
    --color-secondary: #d63d4a;
    --color-amber: #f5a623;
    --bg-dark-1: #0a0a0b;
    --bg-dark-2: #111114;
    --bg-dark-3: #100E1B;
    --bg-dark-4: #1f1f25;
    --bg-dark-5: #26262e;
    --border: rgba(255, 255, 255, .08);
    --border-strong: rgba(255, 255, 255, .15);
    --shadow-glow: 0 0 32px rgba(247, 29, 194, .35);
    --radius-lg: 1.25rem;
    --radius-md: .875rem;
    --section-py: clamp(2rem, 5vw, 4rem);
    --text-h1: clamp(1.75rem, 4vw, 3rem);
    --text-h3: clamp(1.25rem, 2.5vw, 1.75rem);
  }

  .sg-page {
    background: var(--bg-dark-3);
    min-height: 100vh;
    color: #fff;
  }

  /* ============ BREADCRUMB ============ */
  .fm-bc {
    max-width: 80rem;
    margin: 0 auto;
    padding: 1.25rem 1.5rem .5rem;
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
    font-family: 'Montserrat', sans-serif;
    font-size: .78rem;
    font-weight: 600;
    letter-spacing: .04em;
  }

  .fm-bc a {
    color: #FFFFFF;
    text-decoration: none;
    transition: color .2s;
  }

  .fm-bc a:hover {
    color: var(--color-primary);
  }

  .fm-bc svg {
    color: #FFFFFF;
    flex-shrink: 0;
  }

  .fm-bc__cur {
    color: var(--color-primary);
  }

  /* ============ HERO ============ */
  .sg-hero {
    position: relative;
    max-width: 80rem;
    margin: .5rem auto 0;
    padding: 0 1.5rem;
  }

  .sg-hero__inner {
    position: relative;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    border-radius: var(--radius-lg);
    background:
      linear-gradient(var(--bg-dark-2), var(--bg-dark-2)) padding-box,
      linear-gradient(135deg, var(--color-primary) 0%, var(--color-amber) 100%) border-box;
    border: 1px solid transparent;
  }

  @media(min-width:900px) {
    .sg-hero__inner {
      flex-direction: row;
      align-items: center;
      gap: 2rem;
      padding: 1.75rem 2rem;
    }
  }

  .sg-thumb-wrap {
    flex-shrink: 0;
    align-self: center;
    width: 230px;
    height: 230px;
    position: relative;
  }

  @media(min-width:900px) {
    .sg-thumb-wrap {
      width: 200px;
    }
  }

  .sg-thumb {
    width: 230px;
    height: 230px;
    border-radius: var(--radius-md);
    display: block;
    /*object-fit: cover;*/
    box-shadow: 0 24px 60px rgba(0, 0, 0, .7), 0 0 0 1px var(--border-strong);
  }

  .sg-thumb-fallback {
    width: 100%;
    aspect-ratio: 3/4;
    border-radius: var(--radius-md);
    background: linear-gradient(135deg, var(--bg-dark-4), #42424f);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border-strong);
  }

  .sg-hero__info {
    flex: 1;
    min-width: 0;
  }

  .sg-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 40px;
    font-weight: 700;
    color: #E5E2E1;
    line-height: 24px;
    margin: 0 0 1rem;
  }

  .sg-hero__desc {
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    line-height: 24px;
    color: #DCBED4;
    margin: 0 0 1.5rem;
    max-width: 842px;
  }

  .sg-cta {
    display: flex;
    flex-wrap: wrap;
    gap: .875rem;
  }

  .sg-btn-play {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: 16px 32px;
    border-radius: 8px;
    background: var(--color-funalomax-gradient);
    color: #fff;
    font-family: 'Montserrat', sans-serif;
    font-size: .95rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    box-shadow: var(--shadow-glow);
    transition: all .25s;
    letter-spacing: -0.6px;
    text-transform: uppercase;
  }

  .sg-btn-play:hover {
    transform: translateY(-2px);
    filter: brightness(1.1);
  }

  .sg-btn-demo {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: 16px 32px;
    border-radius: 8px;
    background: var(--color-amber-gradient);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #FFFFFF;
    font-family: 'Montserrat', sans-serif;
    font-size: .95rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all .25s;
    letter-spacing: -0.6px;
    text-transform: uppercase;
  }

  .sg-btn-demo:hover {
    transform: translateY(-2px);
    filter: brightness(1.08);
  }

  /* ============ RELATED SLIDER ============ */
  .sg-related {
    max-width: 80rem;
    margin: 0 auto;
    padding: var(--section-py) 1.5rem 0;
  }

  .sg-related-hd {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    gap: 1rem;
  }

  .sg-related-hd h2 {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.25rem;
    color: #fff;
    letter-spacing: .08em;
    margin: 0;
    text-transform: uppercase;
  }

  .sg-viewall {
    font-family: 'Montserrat', sans-serif;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--color-primary);
    text-decoration: none;
  }

  .sg-viewall:hover {
    color: #fff;
  }

  .sg-slider-wrap {
    position: relative;
  }

  .sg-slider {
    display: flex;
    gap: 1rem;
    /*overflow-x: auto;*/
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: .5rem;
    cursor: grab;
  }

  .sg-slider:active {
    cursor: grabbing;
  }

  .sg-slider::-webkit-scrollbar {
    display: none;
  }

  .sg-rcard {
    scroll-snap-align: start;
    flex: 0 0 calc(50% - .5rem);
    border-radius: var(--radius-md);
    overflow: hidden;
    background: var(--bg-dark-4);
    border: 1px solid var(--border);
    text-decoration: none;
    display: block;
    position: relative;
    transition: transform .35s, box-shadow .35s, border-color .35s;
  }

  @media(min-width:480px) {
    .sg-rcard {
      flex: 0 0 calc(33.333% - .75rem);
    }
  }

  @media(min-width:768px) {
    .sg-rcard {
      flex: 0 0 calc(20% - .8rem);
    }
  }

  @media(min-width:1280px) {
    .sg-rcard {
      flex: 0 0 calc(14.2857% - .858rem);
    }
  }

  .sg-rcard:hover {
    transform: translateY(-5px);
    /*box-shadow: 0 10px 30px rgba(247, 29, 194, .18);*/
  }

  .sg-rcard__img-wrap {
    position: relative;
  }

  .sg-rcard__img {
    width: 190px;
    height: 190px;
    display: block;
  }

  /* ============ TWO-COLUMN: ABOUT + RULES ============ */
  .sg-main {
    max-width: 80rem;
    margin: 0 auto;
    padding: var(--section-py) 1.5rem;
    display: grid;
    gap: 1.5rem;
  }

  @media(min-width:900px) {
    .sg-main {
      grid-template-columns: 1.4fr 1fr;
    }
  }

  .sg-panel {
    background: var(--bg-dark-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1.75rem;
  }

  .sg-panel__hd {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding-bottom: 1rem;
    margin-bottom: 1.25rem;
    border-bottom: 1px solid var(--border);
  }

  .sg-panel__hd svg {
    width: 1.15rem;
    height: 1.15rem;
    color: var(--color-primary);
    flex-shrink: 0;
  }

  .sg-panel__hd h2 {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.05rem;
    letter-spacing: .1em;
    color: #fff;
    margin: 0;
    text-transform: uppercase;
  }

  /* About: collapsible content */
  .sg-about__content {
    font-family: 'Montserrat', sans-serif;
    font-size: .9rem;
    line-height: 1.75;
    color: rgba(255, 255, 255, .7);
    overflow: hidden;
    max-height: 7em;
    transition: max-height .4s ease;
  }

  .sg-about__content.is-open {
    max-height: 200em;
  }

  .sg-about__content p {
    margin: 0 0 1rem;
  }

  .sg-about__content p:last-child {
    margin-bottom: 0;
  }

  .sg-about__toggle {
    margin-top: 1rem;
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--color-primary);
    font-family: 'Montserrat', sans-serif;
    font-size: .78rem;
    font-weight: 700;
    padding: 0;
    text-transform: uppercase;
    letter-spacing: .08em;
  }

  .sg-about__toggle:hover {
    color: #fff;
  }

  /* Rules list */
  .sg-rules {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
  }

  .sg-rule {
    display: flex;
    gap: .85rem;
    align-items: flex-start;
  }

  .sg-rule__dot {
    flex-shrink: 0;
    width: .55rem;
    height: .55rem;
    border-radius: 50%;
    background: var(--color-primary);
    margin-top: .55rem;
    box-shadow: 0 0 12px rgba(247, 29, 194, .6);
  }

  .sg-rule__body {
    flex: 1;
    min-width: 0;
  }

  .sg-rule__title {
    font-family: 'Montserrat', sans-serif;
    font-size: .85rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 .25rem;
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  .sg-rule__desc {
    font-family: 'Montserrat', sans-serif;
    font-size: .82rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, .55);
    margin: 0;
  }

  /* ============ IFRAME MODAL ============ */
  .sg-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, .88);
    backdrop-filter: blur(8px);
    padding: 1rem;
  }

  .sg-modal.is-open {
    display: flex;
  }

  .sg-modal__box {
    position: relative;
    width: 100%;
    max-width: 1100px;
    background: var(--bg-dark-1);
    border: 1px solid var(--border-strong);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: 0 40px 100px rgba(0, 0, 0, .8);
  }

  .sg-modal__topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .875rem 1.25rem;
    background: var(--bg-dark-2);
    border-bottom: 1px solid var(--border);
  }

  .sg-modal__game-name {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.25rem;
    color: #fff;
    letter-spacing: .05em;
    margin: 0;
  }

  .sg-modal__close {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    cursor: pointer;
    background: var(--bg-dark-4);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, .7);
  }

  .sg-modal__close:hover {
    background: var(--color-primary);
    color: #000;
  }

  .sg-modal__close svg {
    width: .875rem;
    height: .875rem;
  }

  .sg-modal__iframe-wrap {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
  }

  .sg-modal__iframe-wrap iframe {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: none;
  }

  .sg-modal__loading {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    background: var(--bg-dark-1);
    font-family: 'Montserrat', sans-serif;
    font-size: .875rem;
    color: rgba(255, 255, 255, .5);
  }

  .sg-spinner {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    border: 3px solid var(--border);
    border-top-color: var(--color-primary);
    animation: spin .8s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  .sg-main {
    padding-bottom: 60px;
  }
</style>


<div class="sg-page">

  <!-- ════════════ BREADCRUMB ════════════ -->
  <nav class="fm-bc" aria-label="Breadcrumb">
    <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
    <svg viewBox="0 0 6 10" width="6" height="10" aria-hidden="true">
      <path d="M3.818 5L0 1.111 1.091 0 6 5l-4.909 5L0 8.889 3.818 5z" fill="currentColor" />
    </svg>
    <?php foreach ((array) $ancestors as $aid) :
      $at = get_term($aid, 'game_category');
      if (! $at || is_wp_error($at)) continue; ?>
      <a href="<?php echo esc_url(get_term_link($at)); ?>"><?php echo esc_html($at->name); ?></a>
      <svg viewBox="0 0 6 10" width="6" height="10" aria-hidden="true">
        <path d="M3.818 5L0 1.111 1.091 0 6 5l-4.909 5L0 8.889 3.818 5z" fill="currentColor" />
      </svg>
    <?php endforeach; ?>
    <span class="fm-bc__cur"><?php echo esc_html($term_name); ?></span>
  </nav>

  <!-- ════════════ HERO ════════════ -->
  <section class="sg-hero">
    <div class="sg-hero__inner">
      <!-- Thumbnail -->
      <div class="sg-thumb-wrap">
        <?php if ($thumb_lg) : ?>
          <img src="<?php echo esc_url($thumb_lg); ?>"
            alt="<?php echo esc_attr($title); ?>" class="sg-thumb">
        <?php else : ?>
          <div class="sg-thumb-fallback">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24"
              fill="none" stroke="rgba(255,255,255,.15)" stroke-width="1.5"
              stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="8" width="12" height="12" rx="2" />
              <rect x="10" y="3" width="10" height="12" rx="2" />
            </svg>
          </div>
        <?php endif; ?>
      </div>

      <!-- Info -->
      <div class="sg-hero__info">
        <h1 class="sg-title"><?php echo esc_html($title); ?></h1>

        <?php if ($hero_desc) : ?>
          <p class="sg-hero__desc"><?php echo esc_html($hero_desc); ?></p>
        <?php endif; ?>

        <div class="sg-cta">
          <?php if ($game_url) : ?>
            <button class="sg-btn-play js-open-modal"
              data-url="<?php echo esc_url($game_url); ?>"
              data-title="<?php echo esc_attr($title); ?>">
              Play For Real
            </button>
          <?php endif; ?>
          <?php if ($demo_url) : ?>
            <button class="sg-btn-demo js-open-modal"
              data-url="<?php echo esc_url($demo_url); ?>"
              data-title="<?php echo esc_attr($title); ?> — Demo">
              Try Demo
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════════ RELATED SLIDER ════════════ -->
  <?php if (! empty($related_games)) : ?>
    <section class="sg-related">
      <div class="sg-related-hd">
        <h2>More <?php echo esc_html($related_label); ?> Games</h2>
        <?php if ($primary_cat) : ?>
          <a href="<?php echo esc_url(get_term_link($primary_cat)); ?>" class="sg-viewall">
            View All →
          </a>
        <?php endif; ?>
      </div>

      <div class="sg-slider-wrap">
        <div class="sg-slider" id="sg-rel-slider">
          <?php foreach ($related_games as $rg) : ?>
            <a href="<?php echo esc_url($rg['permalink']); ?>" class="sg-rcard">
              <div class="sg-rcard__img-wrap">
                <?php if ($rg['thumb']) : ?>
                  <img src="<?php echo esc_url($rg['thumb']); ?>"
                    alt="<?php echo esc_attr($rg['title']); ?>"
                    class="sg-rcard__img" loading="lazy">
                <?php endif; ?>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- ════════════ ABOUT + RULES ════════════ -->
  <div class="sg-main">

    <!-- About -->
    <div class="sg-panel">
      <div class="sg-panel__hd">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" />
          <polyline points="12 6 12 12 16 14" />
        </svg>
        <h2>About the Game</h2>
      </div>

      <?php if ($content) : ?>
        <div class="sg-about__content" id="sg-about-body">
          <?php echo apply_filters('the_content', $content); ?>
        </div>
        <button type="button" class="sg-about__toggle" id="sg-about-toggle"
          data-more="Read More" data-less="Read Less">
          Read More
        </button>
      <?php else : ?>
        <div class="sg-about__content is-open">
          <p style="color:rgba(255,255,255,.35);font-style:italic;">
            No description has been added for this game yet.
          </p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Rules -->
    <div class="sg-panel">
      <div class="sg-panel__hd">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="16" rx="2" />
          <line x1="8" y1="9" x2="16" y2="9" />
          <line x1="8" y1="13" x2="16" y2="13" />
          <line x1="8" y1="17" x2="12" y2="17" />
        </svg>
        <h2>Game Rules</h2>
      </div>

      <div class="sg-rules">
        <?php if (is_array($game_rules) && ! empty($game_rules)) : ?>
          <?php foreach ($game_rules as $rule) :
            $r_title = isset($rule['rule_title']) ? $rule['rule_title'] : '';
            $r_desc  = isset($rule['rule_desc'])  ? $rule['rule_desc']  : '';
            if (! $r_title && ! $r_desc) continue; ?>
            <div class="sg-rule">
              <span class="sg-rule__dot"></span>
              <div class="sg-rule__body">
                <?php if ($r_title) : ?>
                  <h3 class="sg-rule__title"><?php echo esc_html($r_title); ?></h3>
                <?php endif; ?>
                <?php if ($r_desc) : ?>
                  <p class="sg-rule__desc"><?php echo esc_html($r_desc); ?></p>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <p style="color:rgba(255,255,255,.35);font-style:italic;font-family: 'Montserrat', sans-serif;font-size:.85rem;">
            No rules have been added for this game yet.
          </p>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- /.sg-main -->

  <!-- CTA SECTION -->
  <?php echo do_shortcode('[fnlmx_cta]'); ?>

</div><!-- /.sg-page -->


<!-- ════════════ IFRAME MODAL ════════════ -->
<div class="sg-modal" id="sg-modal" role="dialog" aria-modal="true">
  <div class="sg-modal__box">
    <div class="sg-modal__topbar">
      <h2 class="sg-modal__game-name" id="sg-modal-title"></h2>
      <button class="sg-modal__close" id="sg-modal-close" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <div class="sg-modal__iframe-wrap">
      <div class="sg-modal__loading" id="sg-modal-loading">
        <div class="sg-spinner"></div>
        <span>Loading game…</span>
      </div>
      <iframe id="sg-modal-iframe" src="" allowfullscreen
        allow="autoplay; fullscreen" title="Game"></iframe>
    </div>
  </div>
</div>


<script>
  (function() {
    'use strict';

    /* Iframe modal */
    var modal = document.getElementById('sg-modal');
    var modalIframe = document.getElementById('sg-modal-iframe');
    var modalTitle = document.getElementById('sg-modal-title');
    var modalLoad = document.getElementById('sg-modal-loading');
    var modalClose = document.getElementById('sg-modal-close');

    function openModal(url, title) {
      modalTitle.textContent = title;
      modalIframe.src = '';
      modalLoad.style.display = 'flex';
      modal.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      modalIframe.onload = function() {
        modalLoad.style.display = 'none';
      };
      modalIframe.src = url;
    }

    function closeModal() {
      modal.classList.remove('is-open');
      modalIframe.src = '';
      document.body.style.overflow = '';
    }
    document.querySelectorAll('.js-open-modal').forEach(function(btn) {
      btn.addEventListener('click', function() {
        openModal(this.dataset.url, this.dataset.title);
      });
    });
    if (modalClose) modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
      if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeModal();
    });

    /* About: Read More toggle */
    var aboutBody = document.getElementById('sg-about-body');
    var aboutBtn = document.getElementById('sg-about-toggle');
    if (aboutBody && aboutBtn) {
      // Hide toggle if content is short enough to fit
      if (aboutBody.scrollHeight <= aboutBody.clientHeight + 4) {
        aboutBtn.style.display = 'none';
        aboutBody.classList.add('is-open');
      }
      aboutBtn.addEventListener('click', function() {
        var open = aboutBody.classList.toggle('is-open');
        aboutBtn.textContent = open ? aboutBtn.dataset.less : aboutBtn.dataset.more;
      });
    }

    /* Slider drag */
    document.querySelectorAll('.sg-slider').forEach(function(slider) {
      var isDown = false,
        startX, scrollLeft, moved = false;
      slider.addEventListener('mousedown', function(e) {
        isDown = true;
        moved = false;
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
      });
      document.addEventListener('mouseup', function() {
        isDown = false;
      });
      slider.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        e.preventDefault();
        moved = true;
        slider.scrollLeft = scrollLeft - (e.pageX - slider.offsetLeft - startX) * 1.5;
      });
      slider.addEventListener('click', function(e) {
        if (moved) {
          e.preventDefault();
          moved = false;
        }
      }, true);
    });
  })();
</script>

<?php get_footer(); ?>
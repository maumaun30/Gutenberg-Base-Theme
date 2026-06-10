<?php

/**
 * Template:  single-game.php
 * CPT:       game
 *
 * Layout:
 *  1. Hero       — thumbnail, title, short description, RTP/Volatility/Provider stats bar, Play Now + Try Demo
 *  2. Related    — "More {Category} Games" static grid (6 desktop / 3 mobile) with "View All" link
 *  3. About + Rules — conditional: both cols | about-only full-width | rules-only full-width
 *  4. CTA        — shortcode
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
$thumb_lg = get_the_post_thumbnail_url($post_id, 'large');

function gc_acf($key, $id)
{
  return function_exists('get_field') ? get_field($key, $id) : get_post_meta($id, $key, true);
}

$game_url   = gc_acf('game_url',                    $post_id);
$hero_desc  = gc_acf('fnlmx_game_short_description', $post_id);
$game_rules = gc_acf('fnlmx_game_rules',             $post_id);
$game_code = get_field('fnlmx_game_code', $post_id);

/* Demo embed. Desktop opens it in the modal; mobile opens the game's iframe URL
   in a new tab (handled client-side — see the modal script below).
   wp_is_mobile() reads the User-Agent server-side just to set the shortcode device. */
$device     = wp_is_mobile() ? 'MOBILE' : 'DESKTOP';
$game_embed = $game_code
  ? do_shortcode('[st8_game game="' . esc_attr($game_code) . '" fun_mode="true" device="' . $device . '"]')
  : '';

/* Stats — build array only for non-empty fields */
$fnlmx_rtp        = gc_acf('fnlmx_rtp',        $post_id);
$fnlmx_volatility = gc_acf('fnlmx_volatility',  $post_id);
$fnlmx_provider   = gc_acf('fnlmx_provider',    $post_id);

$stats = [];
if (! empty($fnlmx_rtp)) {
  $stats[] = [
    'label' => 'RTP',
    'value' => esc_html($fnlmx_rtp) . ' %',
    'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
  ];
}
if (! empty($fnlmx_volatility) && $fnlmx_volatility !== 'Select Volatility') {
  $stats[] = [
    'label' => 'Volatility',
    'value' => esc_html($fnlmx_volatility),
    'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
  ];
}
if (! empty($fnlmx_provider)) {
  $stats[] = [
    'label' => 'Provider',
    'value' => esc_html($fnlmx_provider),
    'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4l1.5 1.5M4 2l1.5 1.5M12 2c-5.523 0-10 4.477-10 10s4.477 10 10 10 10-4.477 10-10c0-1.821-.487-3.53-1.338-5M17 3a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/><circle cx="12" cy="12" r="3"/></svg>',
  ];
}
$has_stats = ! empty($stats);

/* Taxonomy */
$game_cats   = get_the_terms($post_id, 'game_category');
$primary_cat = ($game_cats && ! is_wp_error($game_cats)) ? $game_cats[0] : null;

$ancestors = [];
if ($primary_cat) {
  $chain = get_ancestors($primary_cat->term_id, 'game_category', 'taxonomy');
  $ancestors[] = ! empty($chain) ? end($chain) : $primary_cat->term_id;
}
$term_name = $title;

/* Related games */
$related_games = [];
if ($primary_cat) {
  $cat_chain = get_ancestors($primary_cat->term_id, 'game_category', 'taxonomy');
  $parent_id = ! empty($cat_chain) ? end($cat_chain) : $primary_cat->term_id;

  $rq = new WP_Query([
    'post_type'      => 'game',
    'tax_query'      => [[
      'taxonomy'         => 'game_category',
      'field'            => 'term_id',
      'terms'            => $parent_id,
      'include_children' => true,
    ]],
    'posts_per_page' => 6,
    'post__not_in'   => [$post_id],
    'orderby'        => 'date',
    'order'          => 'DESC',
  ]);
  if ($rq->have_posts()) {
    while ($rq->have_posts()) {
      $rq->the_post();
      $related_games[] = [
        'title'     => get_the_title(),
        'permalink' => get_permalink(),
        'thumb'     => get_the_post_thumbnail_url(get_the_ID(), 'large'),
      ];
    }
    wp_reset_postdata();
  }
}

$related_label = 'Similar';
$square_cards  = false; // Slot & E-Games use square (1/1) tiles; others keep 111/140
if ($primary_cat) {
  $cat_anc  = get_ancestors($primary_cat->term_id, 'game_category', 'taxonomy');
  $top_id   = ! empty($cat_anc) ? end($cat_anc) : $primary_cat->term_id;
  $top_term = get_term($top_id, 'game_category');
  if ($top_term && ! is_wp_error($top_term)) {
    $related_label = $top_term->name;
    $square_cards  = in_array($top_term->slug, ['slot', 'e-game'], true);
  }
}

/* About / Rules visibility */
$has_about = ! empty(trim(strip_tags($content)));

$has_rules = false;
if (is_array($game_rules) && ! empty($game_rules)) {
  foreach ($game_rules as $rule) {
    $t = isset($rule['fnlmx_game_rules_title'])      ? trim($rule['fnlmx_game_rules_title'])      : '';
    $d = isset($rule['fnlmx_game_rules_desription']) ? trim($rule['fnlmx_game_rules_desription']) : '';
    if ($t || $d) {
      $has_rules = true;
      break;
    }
  }
}

$show_main = $has_about || $has_rules;
/* Layout mode: 'both' | 'about-only' | 'rules-only' */
$main_layout = 'both';
if ($has_about && ! $has_rules) $main_layout = 'about-only';
if ($has_rules && ! $has_about) $main_layout = 'rules-only';
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap');

  :root {
    --color-primary: #BA001D;
    --color-amber: #f5a623;
    --bg-dark-1: #0a0a0b;
    --bg-dark-2: #1E1E1E;
    --bg-dark-3: #000000;
    --bg-dark-4: rgb(30 30 30 / 50%);
    --border: rgba(255, 255, 255, .08);
    --border-strong: rgba(255, 255, 255, .15);
    --radius-lg: 1.25rem;
    --radius-md: .875rem;
    --section-py: clamp(2rem, 5vw, 4rem);
  }

  .sg-page {
    background: var(--bg-dark-3);
    min-height: 100vh;
    color: #fff;
  }

  /* ── BREADCRUMB ── */
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
    color: #fff;
    text-decoration: none;
    transition: color .2s;
    font-weight: 400;
  }

  .fm-bc a:hover {
    color: var(--color-primary);
  }

  .fm-bc svg {
    color: #fff;
    flex-shrink: 0;
  }

  .fm-bc__cur {
    color: var(--color-primary);
  }

  /* ── HERO ── */
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
    background: linear-gradient(var(--bg-dark-2), var(--bg-dark-2)) padding-box, var(--color-primary) border-box;
    /*background:
      linear-gradient(var(--bg-dark-2), var(--bg-dark-2)) padding-box,
      linear-gradient(135deg, var(--color-primary) 0%, var(--color-amber) 100%) border-box;*/
    border: 1px solid transparent;
  }

  .sg-thumb-wrap {
    flex-shrink: 0;
    align-self: center;
    width: 230px;
    aspect-ratio: 111 / 140;
  }

  /* Square thumbnail for Slot & E-Games */
  .sg-thumb-wrap--square {
    aspect-ratio: 1 / 1;
  }

  .sg-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--radius-md);
    display: block;
    box-shadow: 0 24px 60px rgba(0, 0, 0, .7), 0 0 0 1px var(--border-strong);
  }

  .sg-thumb-fallback {
    width: 100%;
    height: 100%;
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
    line-height: 1.15;
    margin: 0 0 .75rem;
    text-transform: uppercase;
  }

  .sg-hero__desc {
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    line-height: 24px;
    color: #FFFFFF;
    margin: 0 0 1.25rem;
    max-width: 842px;
  }

  /* ── STATS BAR ── */
  .sg-stats {
    display: inline-flex;
    align-items: center;
    gap: 0;
    background: #111;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255, 255, 255, .08);
  }

  .sg-stat {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .15rem;
    padding: .85rem 1.5rem;
    position: relative;
  }

  /* vertical divider between stats */
  .sg-stat+.sg-stat::before {
    content: '';
    position: absolute;
    left: 0;
    top: 20%;
    height: 60%;
    width: 1px;
    background: rgba(255, 255, 255, .12);
  }

  /* Each stat: icon on the left (vertically centered to the label+value stack),
     label + value stacked on the right */
  .sg-stat {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: .65rem;
    padding: .85rem 1.5rem;
    position: relative;
  }

  .sg-stat__icon {
    flex-shrink: 0;
    width: 1.4rem;
    height: 1.4rem;
    color: var(--color-primary);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .sg-stat__icon svg {
    width: 1.4rem;
    height: 1.4rem;
  }

  .sg-stat__top {
    display: flex;
    flex-direction: column;
    gap: .1rem;
  }

  .sg-stat__label {
    font-family: 'Montserrat', sans-serif;
    font-size: .72rem;
    font-weight: 400;
    color: rgba(255, 255, 255, .5);
    white-space: nowrap;
    line-height: 1;
  }

  .sg-stat__value {
    font-family: 'Montserrat', sans-serif;
    font-size: .95rem;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: .02em;
    white-space: nowrap;
    line-height: 1.2;
  }

  /* ── CTA BUTTONS ── */
  .sg-cta {
    display: flex;
    flex-wrap: wrap;
    gap: .875rem;
  }

  /* The SVG (.sg-btn-shape) is the button background; .sg-btn-label sits on top.
     Play fills via `color` (currentColor); Demo fills with an SVG gradient. */
  .sg-btn-play,
  .sg-btn-demo {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: none;
    padding: 0;
    cursor: pointer;
    text-decoration: none;
    isolation: isolate;
    transition: transform .25s;
  }

  .sg-btn-shape {
    position: absolute;
    inset: 0;
    z-index: 0;
    display: block;
    width: 100%;
    height: 100%;
    pointer-events: none;
  }

  .sg-btn-label {
    position: relative;
    z-index: 1; /* above the shape */
    padding: 16px 32px;
    font-family: 'Montserrat', sans-serif;
    font-size: .95rem;
    font-weight: 600;
    color: #fff;
    letter-spacing: -0.6px;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .sg-btn-play {
    color: var(--color-primary); /* fills the SVG shape */
    --decoration: #ffffff; /* bottom-right accent triangle */
  }

  .sg-btn-demo {
    color: #e9c349; /* decoration triangle base; main path uses the gradient */
    --decoration: #e9c349;
  }

  .sg-btn-play:hover {
    transform: translateY(-4px);
  }

  a.sg-btn-play:hover {
    color: var(--color-primary);
  }

  .sg-btn-demo:hover {
    transform: translateY(-2px);
  }

  /* ── RELATED GRID ── */
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

  .sg-related-hd span {
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    color: #fff;
    letter-spacing: -0.4px;
    margin: 0;
    text-transform: uppercase;
    font-weight: 400;
  }

  .sg-viewall {
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    font-weight: 400;
    text-transform: uppercase;
    color: var(--color-primary);
    text-decoration: none;
  }

  .sg-viewall:hover {
    color: #fff;
  }

  .sg-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
  }

  @media(min-width:768px) {
    .sg-grid {
      grid-template-columns: repeat(6, 1fr);
    }
  }

  .sg-rcard {
    position: relative;
    border-radius: var(--radius-md);
    aspect-ratio: 111 / 140;
    overflow: hidden;
    background: var(--bg-dark-4);
    border: 1px solid var(--border);
    text-decoration: none;
    display: block;
    transition: transform .35s, box-shadow .35s, border-color .35s;
  }

  /* Square tiles for Slot & E-Games categories */
  .sg-grid--square .sg-rcard {
    aspect-ratio: 1 / 1;
  }

  .sg-rcard:hover {
    transform: translateY(-4px);
  }

  .sg-rcard__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    position: relative;
    z-index: 2;
  }

  .sg-rcard__bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;

    filter: blur(20px);
    /*transform: scale(1.2);*/
    opacity: .7;

    z-index: 1;
}

  /* Play pill — bottom-right corner */
  .sg-rcard__play {
    position: absolute;
    right: 0;
    bottom: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .4rem .9rem;
    border-radius: 8px 0 8px 0;
    background: var(--color-primary);
    color: #fff;
    font-family: 'Montserrat', sans-serif;
    font-size: .8rem;
    font-weight: 700;
    letter-spacing: .02em;
    pointer-events: none;
    z-index: 3;
  }

  @media (max-width: 480px) {
    .sg-rcard__play {
      font-size: .7rem;
      padding: .35rem .7rem;
    }
  }

  /* ── ABOUT + RULES SECTION ── */
  .sg-main {
    max-width: 80rem;
    margin: 0 auto;
    padding: var(--section-py) 1.5rem;
  }

  /* Shared container wrapping both panels */
  .sg-main__wrap {
    background: var(--bg-dark-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    display: grid;
    gap: 0;
  }

  /* Two columns: vertical divider on first panel's right edge */
  .sg-main__wrap--both {
    grid-template-columns: 1.4fr 1fr;
  }

  /* Single column: one panel fills everything */
  .sg-main__wrap--single {
    grid-template-columns: 1fr;
  }

  @media(max-width:899px) {
    .sg-main__wrap--both {
      grid-template-columns: 1fr;
    }
  }

  /* Panels are transparent — bg/border come from the parent wrap */
  .sg-panel {
    padding: 1.75rem 2rem;
  }

  .sg-panel__hd {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid var(--border);
  }

  .sg-panel__hd svg {
    width: 1.1rem;
    height: 1.1rem;
    color: var(--color-primary);
    flex-shrink: 0;
  }

  .sg-panel__hd h2 {
    font-family: 'Montserrat', sans-serif;
    font-size: .8rem;
    font-weight: 700;
    color: #fff;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: .1em;
  }

  /* About content */
  .sg-about__content {
    font-family: 'Montserrat', sans-serif;
    font-size: .9rem;
    line-height: 1.8;
    color: rgba(255, 255, 255, .7);
    overflow: hidden;
    max-height: 8.5em;
    transition: max-height .45s ease;
  }

  .sg-about__content p {
    margin: 0 0 1rem;
  }

  .sg-about__content p:last-child {
    margin-bottom: 0;
  }

  .sg-about__toggle {
    margin-top: 1.25rem;
    background: transparent;
    border: none;
    cursor: pointer;
    color: rgba(255, 255, 255, .6);
    font-family: 'Montserrat', sans-serif;
    font-size: .82rem;
    font-weight: 600;
    padding: 0;
    letter-spacing: .04em;
    text-transform: uppercase;
    transition: color .2s;
  }

  .sg-about__toggle:hover {
    color: #fff;
  }

  /* Rules list */
  .sg-rules {
    display: flex;
    flex-direction: column;
    gap: 1.35rem;
    counter-reset: rule-counter;
  }

  .sg-rule {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    counter-increment: rule-counter;
  }

  .sg-rule__num {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #111;
    border: 1.5px solid var(--color-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Montserrat', sans-serif;
    font-size: .85rem;
    font-weight: 700;
    color: #fff;
    margin-top: .1rem;
  }

  .sg-rule__num::before {
    content: counter(rule-counter);
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
    line-height: 1.4;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin: 0 0 .3rem;
  }

  .sg-rule__desc {
    font-family: 'Montserrat', sans-serif;
    font-size: .82rem;
    line-height: 1.65;
    color: rgba(255, 255, 255, .5);
    margin: 0;
  }

  /* ── RESPONSIVE ── */
  @media(min-width:900px) {
    .sg-hero__inner {
      flex-direction: row;
      align-items: center;
      gap: 2rem;
      padding: 1.75rem 2rem;
    }

    .sg-thumb-wrap {
      width: 200px;
    }
  }

  @media(max-width:768px) {
    .sg-title {
      text-align: center;
    }

    .sg-hero__desc {
      text-align: center;
    }

    .sg-stats {
      align-self: center;
      display: flex;
      width: fit-content;
      margin-left: auto;
      margin-right: auto;
    }

    .sg-cta {
      justify-content: center;
    }
  }

  @media(max-width:600px) {
    .sg-thumb-wrap {
      width: 180px;
    }

    .sg-title {
      font-size: 20px;
    }

    .sg-hero__desc {
      font-size: 12px;
      line-height: 14px;
    }

    .sg-cta {
      flex-wrap: nowrap;
    }

    .sg-btn-play,
    .sg-btn-demo {
      flex: 1 1 0;
      min-width: 0;
      width: auto;
      justify-content: center;
    }

    .sg-btn-play .sg-btn-label,
    .sg-btn-demo .sg-btn-label {
      font-size: 12px;
      padding: 15px 20px;
    }

    .sg-related-hd span,
    .sg-viewall {
      font-size: 14px;
    }

    .sg-stats {
      width: 100%;
      border-radius: 8px;
      flex-wrap: wrap;
    }

    .sg-stat {
      width: calc(100% / 3 - ((3 - 1) * 15px) / 3);
      padding: .7rem 1rem;
      gap: 5px;
    }

    .sg-stat__label {
      font-size: 9px;
    }

    .sg-stat__value {
      font-size: 10px;
    }

    .sg-stat__icon svg {
      width: 15px;
      height: 15px;
    }
  }

  @media(max-width:399px) {
    .sg-stats {
      justify-content: center;
    }

    .sg-stat {
      width: calc(100% / 2 - ((2 - 1) * 15px) / 2);
    }

    .sg-stat+.sg-stat::before {
      background: unset;
    }
  }

  /* ── IFRAME MODAL ── */
  .sg-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9997;
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
    margin: 0;
  }

  .sg-modal__close {
    width: 2rem;
    height: 2rem;
    padding: 0;
    cursor: pointer;
    background: transparent;
    border: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .15s ease, opacity .15s ease;
  }

  .sg-modal__close:hover {
    opacity: .85;
  }

  .sg-modal__close svg {
    width: 1.75rem;
    height: 1.75rem;
  }

  .sg-modal__iframe-wrap {
    position: relative;
    width: 100%;
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

  .cta-global {
    margin-top: 60px;
  }
</style>


<div class="sg-page">

  <!-- BREADCRUMB -->
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

  <!-- HERO -->
  <section class="sg-hero">
    <div class="sg-hero__inner">

      <div class="sg-thumb-wrap<?php echo $square_cards ? ' sg-thumb-wrap--square' : ''; ?>">
        <?php if ($thumb_lg) : ?>
          <img src="<?php echo esc_url($thumb_lg); ?>" alt="<?php echo esc_attr($title); ?>" class="sg-thumb">
        <?php else : ?>
          <div class="sg-thumb-fallback">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24"
              fill="none" stroke="rgba(255,255,255,.15)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="8" width="12" height="12" rx="2" />
              <rect x="10" y="3" width="10" height="12" rx="2" />
            </svg>
          </div>
        <?php endif; ?>
      </div>

      <div class="sg-hero__info">
        <h1 class="sg-title"><?php echo esc_html($title); ?></h1>

        <?php if ($hero_desc) : ?>
          <p class="sg-hero__desc"><?php echo esc_html($hero_desc); ?></p>
        <?php endif; ?>

        <?php if ($has_stats) : ?>
          <div class="sg-stats">
            <?php foreach ($stats as $stat) : ?>
              <div class="sg-stat">
                <span class="sg-stat__icon" aria-hidden="true"><?php echo $stat['icon']; ?></span>
                <div class="sg-stat__top">
                  <span class="sg-stat__label"><?php echo $stat['label']; ?></span>
                  <span class="sg-stat__value"><?php echo $stat['value']; ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="sg-cta">
          <a class="sg-btn-play fm-open-register" href="https://funalomax.com/en">
            <svg aria-hidden="true" class="sg-btn-shape" viewBox="0 0 148 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#sg-btn-play-shape)">
                <path d="M148 30.4 L136.4 42 H0 V7 L7 0 H148 V30.4 Z" fill="currentColor"></path>
                <path d="M148 34 V42 H140 L148 34 Z" fill="var(--decoration, currentColor)"></path>
              </g>
              <defs><clipPath id="sg-btn-play-shape"><rect width="148" height="42" fill="white"></rect></clipPath></defs>
            </svg>
            <span class="sg-btn-label">Play For Real</span>
          </a>
          <?php if ($game_code) : ?>
            <button class="sg-btn-demo js-open-modal"
              data-title="<?php echo esc_attr($title); ?> — Demo">
              <svg aria-hidden="true" class="sg-btn-shape" viewBox="0 0 148 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#sg-btn-demo-shape)">
                  <path d="M148 30.4 L136.4 42 H0 V7 L7 0 H148 V30.4 Z" fill="url(#sg-btn-demo-grad)"></path>
                  <path d="M148 34 V42 H140 L148 34 Z" fill="var(--decoration, currentColor)"></path>
                </g>
                <defs>
                  <linearGradient id="sg-btn-demo-grad" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#936000"></stop>
                    <stop offset="100%" stop-color="#e9c349"></stop>
                  </linearGradient>
                  <clipPath id="sg-btn-demo-shape"><rect width="148" height="42" fill="white"></rect></clipPath>
                </defs>
              </svg>
              <span class="sg-btn-label">Try Demo</span>
            </button>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </section>

  <!-- RELATED GRID -->
  <?php if (! empty($related_games)) : ?>
    <section class="sg-related">
      <div class="sg-related-hd">
        <span>More <?php echo esc_html($related_label); ?> Games</span>
        <?php if ($primary_cat) : ?>
          <a href="<?php echo esc_url(get_term_link($primary_cat)); ?>" class="sg-viewall">View All →</a>
        <?php endif; ?>
      </div>
      <div class="sg-grid<?php echo $square_cards ? ' sg-grid--square' : ''; ?>">
        <?php foreach ($related_games as $rg) : ?>
          <a href="<?php echo esc_url($rg['permalink']); ?>" class="sg-rcard">
    <?php if ($rg['thumb']) : ?>

        <img
            src="<?php echo esc_url($rg['thumb']); ?>"
            alt=""
            class="sg-rcard__bg"
            aria-hidden="true"
        >

        <img
            src="<?php echo esc_url($rg['thumb']); ?>"
            alt="<?php echo esc_attr($rg['title']); ?>"
            class="sg-rcard__img"
            loading="lazy"
        >

    <?php endif; ?>

    <span class="sg-rcard__play">Play</span>
</a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- ABOUT + RULES
       Layout classes:
         sg-main--both       → 2-column grid (about left, rules right)
         sg-main--single     → 1-column (whichever panel is present stretches full width)
  -->
  <?php if ($show_main) : ?>
    <div class="sg-main">
      <div class="sg-main__wrap <?php echo ($main_layout === 'both') ? 'sg-main__wrap--both' : 'sg-main__wrap--single'; ?>">

        <?php if ($has_about) : ?>
          <div class="sg-panel">
            <div class="sg-panel__hd">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
              </svg>
              <h2>About the Game</h2>
            </div>
            <div class="sg-about__content" id="sg-about-body">
              <?php echo apply_filters('the_content', $content); ?>
            </div>
            <button type="button" class="sg-about__toggle" id="sg-about-toggle"
              data-more="Read More" data-less="Read Less">Read More</button>
          </div>
        <?php endif; ?>

        <?php if ($has_rules) : ?>
          <div class="sg-panel">
            <div class="sg-panel__hd">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
              </svg>
              <h2>Game Rules</h2>
            </div>
            <div class="sg-rules">
              <?php foreach ($game_rules as $rule) :
                $r_title = isset($rule['fnlmx_game_rules_title'])      ? $rule['fnlmx_game_rules_title']      : '';
                $r_desc  = isset($rule['fnlmx_game_rules_desription']) ? $rule['fnlmx_game_rules_desription'] : '';
                if (! $r_title && ! $r_desc) continue; ?>
                <div class="sg-rule">
                  <span class="sg-rule__num"></span>
                  <div class="sg-rule__body">
                    <?php if ($r_title) : ?><h3 class="sg-rule__title"><?php echo esc_html($r_title); ?></h3><?php endif; ?>
                    <?php if ($r_desc) : ?><p class="sg-rule__desc"><?php echo esc_html($r_desc);  ?></p><?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

      </div><!-- /.sg-main__wrap -->
    </div><!-- /.sg-main -->
  <?php endif; ?>

  <!-- CTA -->
  <div class="cta-global"><?php echo do_shortcode('[fnlmx_cta]'); ?></div>

</div><!-- /.sg-page -->


<!-- IFRAME MODAL -->
<div class="sg-modal" id="sg-modal" role="dialog" aria-modal="true">
  <div class="sg-modal__box">
    <div class="sg-modal__topbar">
      <h2 class="sg-modal__game-name" id="sg-modal-title"></h2>
      <button class="sg-modal__close" id="sg-modal-close" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" height="1em" fill="none" viewBox="0 0 24 24" class="size-6" aria-hidden="true"><path fill="#BA001D" d="M0 0h12v24H0zM12 0h12v12H12z"></path><path fill="#424242" d="M24 18v6h-6z"></path><path fill="#BA001D" d="M24 24h-.001v-8.571L15.428 24H12V12h12z"></path><g fill="#fff" filter="url(#close-button_svg__a)"><path d="M11.892 11.057v.002l1.02 1.02v-.001l3.908 3.907-.418.418-.418.418L12 12.836 8.015 16.82l-.836-.836L11.163 12 7.178 8.016l.837-.837zM16.403 7.597l.418.419-3.21 3.207-.836-.836 3.21-3.208z"></path></g><defs><filter id="close-button_svg__a" width="16.714" height="16.714" x="3.964" y="3.964" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood><feColorMatrix in="SourceAlpha" result="hardAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"></feColorMatrix><feOffset dx="0.321" dy="0.321"></feOffset><feGaussianBlur stdDeviation="0.321"></feGaussianBlur><feComposite in2="hardAlpha" operator="out"></feComposite><feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.2 0"></feColorMatrix><feBlend in2="BackgroundImageFix" result="effect1_dropShadow_264_5500"></feBlend><feBlend in="SourceGraphic" in2="effect1_dropShadow_264_5500" result="shape"></feBlend></filter></defs></svg>
      </button>
    </div>
    <div class="sg-modal__iframe-wrap">
      <div class="sg-modal__loading" id="sg-modal-loading">
        <div class="sg-spinner"></div>
        <span>Loading game…</span>
      </div>
      <?php echo $game_embed ?: 'No Game Found.'; ?>
    </div>
  </div>
</div>


<script>
  (function() {
    'use strict';

    /* Modal — the game is embedded via the [st8_game] shortcode inside the modal */
    var modal = document.getElementById('sg-modal');
    var modalTitle = document.getElementById('sg-modal-title');
    var modalLoad = document.getElementById('sg-modal-loading');
    var modalClose = document.getElementById('sg-modal-close');

    if (modal) {
      var gameFrame = modal.querySelector('.sg-modal__iframe-wrap iframe');
      var isMobile = /Mobi|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

      function hideLoading() {
        if (modalLoad) modalLoad.style.display = 'none';
      }

      // The embedded game loads in the background on page load. Hide the
      // spinner once its iframe is ready; if there's no iframe, hide it too.
      if (gameFrame) {
        gameFrame.addEventListener('load', hideLoading);
      } else {
        hideLoading();
      }

      function openModal(title) {
        if (modalTitle) modalTitle.textContent = title;
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        // By the time the user opens the modal the game has usually finished
        // loading, so clear the spinner as a fallback.
        hideLoading();
      }

      function closeModal() {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
      }

      document.querySelectorAll('.js-open-modal').forEach(function(btn) {
        btn.addEventListener('click', function() {
          // On mobile, open the game in a new tab instead of the cramped modal.
          if (isMobile) {
            var frame = modal.querySelector('.sg-modal__iframe-wrap iframe');
            var src = frame ? frame.src : '';
            if (src && src !== 'about:blank') {
              window.open(src, '_blank', 'noopener');
              return;
            }
          }
          openModal(this.dataset.title);
        });
      });
      if (modalClose) modalClose.addEventListener('click', closeModal);
      modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
      });
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
      });
    }

    /* Read More toggle — animates at the same speed in both directions
       by always transitioning between two explicit px values               */
    var aboutBody = document.getElementById('sg-about-body');
    var aboutBtn = document.getElementById('sg-about-toggle');
    if (aboutBody && aboutBtn) {
      /* Content fits without truncation — hide button, show everything */
      if (aboutBody.scrollHeight <= aboutBody.clientHeight + 4) {
        aboutBtn.style.display = 'none';
        aboutBody.style.maxHeight = 'none';
      } else {
        var isOpen = false;

        aboutBtn.addEventListener('click', function() {
          if (!isOpen) {
            /* EXPAND: transition from collapsed height to full content height */
            aboutBody.style.maxHeight = aboutBody.scrollHeight + 'px';
            aboutBtn.textContent = aboutBtn.dataset.less;
            isOpen = true;
          } else {
            /* COLLAPSE: pin to current rendered height first, then on the
               next two frames shrink — this gives the browser a clear
               start-point so the transition fires cleanly */
            aboutBody.style.maxHeight = aboutBody.scrollHeight + 'px';
            requestAnimationFrame(function() {
              requestAnimationFrame(function() {
                aboutBody.style.maxHeight = '8.5em';
              });
            });
            aboutBtn.textContent = aboutBtn.dataset.more;
            isOpen = false;
          }
        });
      }
    }
  })();
</script>

<?php get_footer(); ?>
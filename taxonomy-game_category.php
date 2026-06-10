<?php

/**
 * Template: taxonomy-game_category.php  (FunaloMAX redesign)
 * CPT      : game
 * Taxonomy : game_category
 */

get_header();

$current_term   = get_queried_object();
$term_id        = $current_term->term_id;
$term_name      = $current_term->name;
$term_desc      = $current_term->description;
$parent_term_id = $current_term->parent;
$ancestors      = array_reverse(get_ancestors($term_id, 'game_category', 'taxonomy'));

/* Root parent category drives the card aspect ratio: Slot & E-Games use square
   (1/1) tiles, everything else keeps the default 111/140. */
$root_term_id = $ancestors[0] ?? $term_id;
$root_term    = get_term($root_term_id, 'game_category');
$root_slug    = ($root_term && ! is_wp_error($root_term)) ? $root_term->slug : '';
$square_cards = in_array($root_slug, ['slot', 'e-games'], true);

/* Total game count (this term + descendants) */
$total_q = new WP_Query([
  'post_type'      => 'game',
  'tax_query'      => [[
    'taxonomy'         => 'game_category',
    'field'            => 'term_id',
    'terms'            => $term_id,
    'include_children' => true,
  ]],
  'posts_per_page' => 1,
  'fields'         => 'ids',
]);
$game_count = $total_q->found_posts;
wp_reset_postdata();

/* First 12 games for the grid */
$grid_q = new WP_Query([
  'post_type'      => 'game',
  'tax_query'      => [[
    'taxonomy'         => 'game_category',
    'field'            => 'term_id',
    'terms'            => $term_id,
    'include_children' => true,
  ]],
  'posts_per_page' => 12,
  'orderby'        => 'date',
  'order'          => 'DESC',
]);

/* ACF fields */
$sub_para  = function_exists('get_field') ? get_field('fnlmx_game_category_contents_subparagraph', $current_term) : '';
$icon_rows = function_exists('get_field') ? (get_field('fnlmx_game_category_icon_details_wrapper', $current_term) ?: []) : [];
$faq_rows  = function_exists('get_field') ? (get_field('fnlmx_game_category_faq_wrapper', $current_term) ?: []) : [];

$has_left  = !empty($icon_rows);
$has_right = !empty($faq_rows);

/* Pass to hero partial */
set_query_var('term',           $current_term);
set_query_var('term_name',      $term_name);
set_query_var('term_desc',      $term_desc);
set_query_var('parent_term_id', $parent_term_id);
set_query_var('ancestors',      $ancestors);
set_query_var('game_count',     $game_count);
?>
<style>
  :root {
    --fm-bg: rgb(16, 14, 27);
    --fm-bg-2: rgb(4, 1, 19);
    --fm-card: rgba(26, 26, 26, .7);
    --fm-card-br: rgba(255, 255, 255, .1);
    --fm-text: #fff;
    --fm-muted: rgb(161, 161, 170);
    --fm-muted-2: rgb(126, 121, 132);
    --fm-pink:
      /*rgb(247, 29, 194)*/
      #ba001d;
    --fm-pink-2: rgb(214, 61, 74);
    --fm-hot: rgb(147, 0, 10);
    --fm-red: #ba001d;
  }

  .fm-page {
    background:
      /*var(--fm-bg)*/
      var(--bg-dark-3);
    color: var(--fm-text);
    font-family: 'Montserrat', system-ui, sans-serif;
  }

  .fm-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
  }

  /* HERO */
  .fm-hero {
    position: relative;
    min-height: 300px;
    overflow: hidden;
  }

  .fm-hero__bg {
    position: absolute;
    inset: 0 0 auto 0;
    height: 100%;
    background-size: cover;
    background-position: center;
  }

  .fm-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, #0E0E0E 0%, rgba(14, 14, 14, 0.4) 50%, rgba(14, 14, 14, 0) 100%);
  }

  .fm-hero__inner {
    position: relative;
    z-index: 2;
    max-width: 1280px;
    margin: 0 auto;
    padding: 25px 44px 60px;
  }

  .fm-bc {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #fff;
    font-size: 12px;
  }

  .fm-bc a {
    color: #fff;
    text-decoration: none;
    opacity: .85;
  }

  .fm-bc a:hover {
    color: var(--color-primary);
    opacity: 1;
  }

  .fm-bc__cur {
    font-weight: 700;
    color: var(--color-primary);
  }

  .fm-hero__title {
    font-weight: 700;
    font-size: clamp(32px, 4vw, 48px);
    line-height: 1.1;
    letter-spacing: -.02em;
    color: #fff;
    margin: 22px 0 38px;
    max-width: 672px;
  }

  .fm-hero__desc {
    font-size: 18px;
    line-height: 1.6;
    color: #FFFFFF;
    max-width: 576px;
    margin: 0;
  }

  /* GAMES GRID */
  .fm-games {
    padding: 56px 0 80px;
  }

  .fm-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
  }

  @media (max-width: 1024px) {
    .fm-grid {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  @media (max-width: 640px) {
    .fm-grid {
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
    }

    .fm-bc {
      justify-content: center;
    }

    .fm-hero__title {
      text-align: center;
    }

    .fm-hero__desc {
      text-align: center;
      font-size: 12px;
    }

    .fm-info__title,
    .fm-info__sub,
    .fm-faq-title {
      text-align: center;
    }

    /* Smaller Load More button on mobile */
    .fm-loadmore__label {
      font-size: 10px;
      padding: 10px 20px;
    }

    /* Smaller HOT badge on mobile */
    .fm-card__badge {
      top: 8px;
      left: 8px;
      padding: 2px 7px;
      font-size: 8px;
      line-height: 12px;
    }
  }

  .fm-card {
    position: relative;
    aspect-ratio: 111 / 140;
    border-radius: 12px;
    overflow: hidden;
    background: var(--fm-card);
    border: 1px solid var(--fm-card-br);
    text-decoration: none;
    display: block;
    transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
  }

  /* Square tiles for Slot & E-Games categories */
  .fm-grid--square .fm-card {
    aspect-ratio: 1 / 1;
  }

  .fm-card:hover {
    transform: translateY(-4px);
  }

  .fm-card__img {
    position: relative;
    z-index: 2;
    inset: 0;
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    object-position: center;
  }

  .fm-card__blur {
    position: absolute;
    inset: 0;

    background-size: cover;
    background-position: center;

    filter: blur(30px);

    opacity: .7;

    z-index: 1;
  }

  .fm-card__fallback {
    position: absolute;
    inset: 1px;
    background: linear-gradient(135deg, #1a1729, #0c0a1e);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, .2);
    font-size: 32px;
    font-weight: 700;
  }

  .fm-card__badge {
    position: absolute;
    top: 13px;
    left: 13px;
    z-index: 3;
    /* above .fm-card__img (z-index 2), otherwise the image paints over it */
    padding: 4px 10px;
    border-radius: 4px;
    background: var(--color-primary);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    line-height: 15px;
    letter-spacing: .04em;
  }

  .fm-card__title {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 28px 12px 10px;
    background: linear-gradient(to top, rgba(0, 0, 0, .85), transparent);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    margin: 0;
  }

  .fm-viewall {
    display: flex;
    justify-content: center;
    margin-top: 48px;
  }

  /* The SVG (.fm-loadmore__shape) is the button background, filled via `color`
     (fill: currentColor); .fm-loadmore__label sits on top. */
  .fm-viewall .fm-loadmore {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: none;
    padding: 0;
    color: var(--color-primary); /* fills the SVG shape */
    --decoration: #ffffff; /* bottom-right accent triangle */
    font-family: inherit;
    text-decoration: none;
    cursor: pointer;
    isolation: isolate;
    transition: transform .2s ease, opacity .25s;
  }

  .fm-loadmore__shape {
    position: absolute;
    inset: 0;
    z-index: 0;
    display: block;
    width: 100%;
    height: 100%;
    pointer-events: none;
  }

  .fm-loadmore__label {
    position: relative;
    z-index: 1; /* above the shape */
    padding: 16px 32px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #fff;
    white-space: nowrap;
  }

  .fm-viewall .fm-loadmore:hover {
    transform: translateY(-5px);
  }

  .fm-viewall .fm-loadmore[disabled] {
    opacity: .6;
    cursor: default;
    transform: none;
  }

  /* WHY / QUICK GUIDE */
  .fm-info {
    background: rgb(30 30 30 / 50%);
    padding: 80px 0;
  }

  .fm-info__grid {
    display: grid;
    grid-template-columns: 1fr 1px 1fr;
    gap: 60px;
    align-items: start;
  }

  /* one side only → single column full width */
  .fm-info__grid--full {
    grid-template-columns: 1fr;
  }

  .fm-info__grid--full .fm-info__divider {
    display: none;
  }

  @media (max-width: 900px) {
    .fm-info__grid {
      grid-template-columns: 1fr;
      gap: 48px;
    }

    .fm-info__divider {
      display: none;
    }
  }

  .fm-info__divider {
    width: 1px;
    background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, .12), transparent);
    align-self: stretch;
  }

  .fm-info h2 {
    font-weight: 700;
    font-size: clamp(28px, 3.2vw, 40px);
    line-height: 1.15;
    letter-spacing: -.012em;
    margin: 0 0 16px;
    color: #fff;
    text-transform: uppercase;
  }

  .fm-info h2 .fm-pink {
    color: var(--fm-pink);
  }

  .fm-info__sub {
    color: var(--fm-muted-2);
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 15px;
    line-height: 1.55;
    margin: 0 0 32px;
  }

  .fm-feat {
    display: flex;
    flex-direction: column;
    gap: 28px;
  }

  .fm-feat__row {
    display: flex;
    align-items: center;
    gap: 24px;
  }

  .fm-feat__icon {
    width: 71px;
    height: 73px;
    flex-shrink: 0;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
  }

  .fm-feat__txt h3 {
    font-family: 'Inter', system-ui, sans-serif;
    font-weight: 700;
    font-size: 17px;
    color: rgb(183, 183, 184);
    margin: 0 0 8px;
    text-transform: uppercase;
    letter-spacing: .02em;
  }

  .fm-feat__txt p {
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 15px;
    color: var(--fm-muted-2);
    margin: 0;
  }

  /* FAQ */
  .fm-faq {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: 24px;
  }

  .fm-faq details {
    background: #1E1E1E;
    border: 1px solid rgb(28, 16, 43);
    border-radius: 8px;
    overflow: hidden;
    transition: border-color .2s;
  }

  .fm-faq details[open] {
    border-color: #ba001d5e;
    border-radius: 13px 10px 6px 7px;
  }

  .fm-faq details:hover {
    border-color: #ba001d5e;
  }

  .fm-faq summary {
    list-style: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 22px 24px;
    font-family: 'Inter', system-ui, sans-serif;
    font-weight: 700;
    font-size: 19px;
    color: rgb(202, 202, 203);
    user-select: none;
  }

  .fm-faq summary::-webkit-details-marker {
    display: none;
  }

  .fm-faq summary .fm-faq__ico {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    color: rgb(186, 85, 211);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .fm-faq summary .fm-faq__chev {
    margin-left: auto;
    transition: transform .3s ease;
    color: var(--fm-muted-2);
  }

  .fm-faq details[open] summary .fm-faq__chev {
    transform: rotate(90deg);
  }

  .fm-faq__body {
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 15px;
    line-height: 1.55;
    color: var(--fm-muted-2);
    padding: 0 24px 0 74px;
    /* smooth open/close via grid trick */
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows .3s ease, padding-bottom .3s ease;
  }

  .fm-faq__body-inner {
    overflow: hidden;
  }

  details[open] .fm-faq__body {
    grid-template-rows: 1fr;
    padding-bottom: 22px;
  }

  /* NEED HELP */
  .fm-help {
    position: relative;
    min-height: 377px;
    overflow: hidden;
  }

  .fm-help__bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
  }

  .fm-help__inner {
    position: relative;
    z-index: 2;
    max-width: 1280px;
    margin: 0 auto;
    padding: 64px 76px;
  }

  .fm-help h2 {
    font-weight: 700;
    font-size: 24px;
    line-height: 1.2;
    text-transform: uppercase;
    margin: 0 0 32px;
    color: #fff;
    max-width: 600px;
  }

  .fm-help p {
    font-size: 16px;
    line-height: 1.5;
    color: rgb(203, 213, 225);
    max-width: 600px;
    margin: 0 0 40px;
  }

  .fm-help__actions {
    display: flex;
    gap: 22px;
    flex-wrap: wrap;
  }

  .fm-btn-pink {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 20px;
    height: 44px;
    min-width: 154px;
    border-radius: 8px;
    background: linear-gradient(var(--fm-pink-2) 0%, var(--fm-pink) 100%);
    color: #fff;
    font-weight: 700;
    font-size: 16px;
    text-decoration: none;
    letter-spacing: -.02em;
    transition: transform .2s, box-shadow .2s;
  }

  .fm-btn-pink:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(247, 29, 194, .4);
  }

  .fm-btn-ghost {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 20px;
    height: 44px;
    min-width: 154px;
    border-radius: 8px;
    border: 1px solid rgb(51, 65, 85);
    color: #fff;
    font-weight: 700;
    font-size: 12px;
    text-decoration: none;
    transition: background .2s;
  }

  .fm-btn-ghost:hover {
    background: rgba(255, 255, 255, .05);
  }

  /* ACF content sections (kept from original) */
  .fm-content-block {
    padding: 60px 0;
    background: var(--fm-bg);
  }

  .fm-content-card {
    background: rgba(255, 255, 255, .03);
    border: 1px solid var(--fm-card-br);
    border-radius: 20px;
    padding: 32px;
    font-family: 'Inter', system-ui, sans-serif;
    color: rgba(255, 255, 255, .75);
    line-height: 1.7;
  }

  .fm-content-card h2,
  .fm-content-card h3 {
    color: #fff;
    font-family: 'Montserrat', sans-serif;
  }
</style>

<div class="fm-page">

  <?php get_template_part('template-parts/game-category-hero'); ?>

  <!-- GAMES GRID -->
  <section class="fm-games">
    <div class="fm-container">
      <?php if ($grid_q->have_posts()) : ?>
        <div class="fm-grid<?php echo $square_cards ? ' fm-grid--square' : ''; ?>">
          <?php while ($grid_q->have_posts()) : $grid_q->the_post();
            fnlmx_game_card_template(get_the_ID());
          endwhile;
          wp_reset_postdata(); ?>
        </div>

        <?php if ($game_count > 12) : ?>
          <div class="fm-viewall">
            <button
              type="button"
              class="fm-loadmore"
              data-term="<?php echo esc_attr($term_id); ?>"
              data-page="1"
              data-ajax="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
              data-nonce="<?php echo esc_attr(wp_create_nonce('fnlmx_load_more')); ?>">
              <svg aria-hidden="true" class="fm-loadmore__shape" viewBox="0 0 148 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#fm-loadmore-shape)">
                  <path d="M148 30.4 L136.4 42 H0 V7 L7 0 H148 V30.4 Z" fill="currentColor"></path>
                  <path d="M148 34 V42 H140 L148 34 Z" fill="var(--decoration, currentColor)"></path>
                </g>
                <defs><clipPath id="fm-loadmore-shape"><rect width="148" height="42" fill="white"></rect></clipPath></defs>
              </svg>
              <span class="fm-loadmore__label">Load More <?php echo esc_html($term_name); ?> Games</span>
            </button>
          </div>
        <?php endif; ?>
      <?php else : ?>
        <p style="text-align:center;color:rgba(255,255,255,.5);padding:60px 0;">No <?php echo esc_html($term_name); ?> games available yet.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- WHY PLAY / QUICK GUIDE -->
  <?php if ($has_left || $has_right) : ?>
    <section class="fm-info">
      <div class="fm-container">

        <?php
        $grid_class = 'fm-info__grid';
        if ($has_left xor $has_right) $grid_class .= ' fm-info__grid--full';
        ?>
        <div class="<?php echo esc_attr($grid_class); ?>">

          <!-- LEFT: Why Play -->
          <?php if ($has_left) : ?>
            <div>
              <h2 class="fm-info__title">
                Why Play <?php echo esc_html(strtoupper($term_name)); ?><br>
                On <span class="fm-pink">FunaloMAX?</span>
              </h2>

              <?php if ($sub_para) : ?>
                <p class="fm-info__sub"><?php echo esc_html($sub_para); ?></p>
              <?php endif; ?>

              <div class="fm-feat">
                <?php foreach ($icon_rows as $row) :
                  $icon_img = $row['fnlmx_game_category_content_icon'] ?? [];
                  $icon_url = $icon_img['url'] ?? '';
                  $subtitle = $row['fnlmx_game_category_content_subtitle'] ?? '';
                  $supara   = $row['fnlmx_game_category_content_suparagraph'] ?? '';
                ?>
                  <div class="fm-feat__row">
                    <div class="fm-feat__icon"
                      <?php if ($icon_url) : ?>
                      style="background-image:url('<?php echo esc_url($icon_url); ?>');"
                      <?php endif; ?>>
                    </div>
                    <div class="fm-feat__txt">
                      <?php if ($subtitle) : ?><h3><?php echo esc_html($subtitle); ?></h3><?php endif; ?>
                      <?php if ($supara) : ?><p><?php echo esc_html($supara); ?></p><?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($has_left && $has_right) : ?>
            <div class="fm-info__divider"></div>
          <?php endif; ?>

          <!-- RIGHT: Quick Guide FAQ -->
          <?php if ($has_right) : ?>
            <div>
              <h2 class="fm-faq-title">Quick Guide To<br><span class="fm-pink"><?php echo esc_html(strtoupper($term_name)); ?> GAMES</span></h2>

              <div class="fm-faq">
                <?php foreach ($faq_rows as $idx => $faq) :
                  $faq_icon_img = $faq['fnlmx_game_category_faq_icon'] ?? [];
                  $faq_icon_url = $faq_icon_img['url'] ?? '';
                  $question     = $faq['fnlmx_game_category_faq_question'] ?? '';
                  $answer       = $faq['fnlmx_game_category_faq_answer'] ?? '';
                ?>
                  <details <?php echo $idx === 0 ? 'open' : ''; ?>>
                    <summary>
                      <span class="fm-faq__ico"
                        <?php if ($faq_icon_url) : ?>
                        style="background-image:url('<?php echo esc_url($faq_icon_url); ?>');"
                        <?php endif; ?>>
                        <?php if (!$faq_icon_url) : ?>
                          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 8v4m0 4h.01" />
                          </svg>
                        <?php endif; ?>
                      </span>
                      <?php echo esc_html($question); ?>
                      <svg class="fm-faq__chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6" />
                      </svg>
                    </summary>
                    <div class="fm-faq__body">
                      <div class="fm-faq__body-inner">
                        <?php echo wp_kses_post($answer); ?>
                      </div>
                    </div>
                  </details>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- CTA SECTION -->
  <section class="fm-help">
    <?php echo do_shortcode('[fnlmx_cta]'); ?>
  </section>

</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Load More games
    const loadMoreBtn = document.querySelector('.fm-loadmore');
    if (loadMoreBtn) {
      const grid = document.querySelector('.fm-games .fm-grid');
      const label = loadMoreBtn.querySelector('.fm-loadmore__label') || loadMoreBtn;
      const original = label.textContent.trim();

      loadMoreBtn.addEventListener('click', async () => {
        const nextPage = parseInt(loadMoreBtn.dataset.page, 10) + 1;

        loadMoreBtn.disabled = true;
        label.textContent = 'Loading…';

        const body = new URLSearchParams({
          action: 'fnlmx_load_more_games',
          nonce: loadMoreBtn.dataset.nonce,
          term_id: loadMoreBtn.dataset.term,
          page: nextPage,
        });

        try {
          const res = await fetch(loadMoreBtn.dataset.ajax, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString(),
          });
          const json = await res.json();

          if (json.success && json.data.html) {
            grid.insertAdjacentHTML('beforeend', json.data.html);
            loadMoreBtn.dataset.page = nextPage;
          }

          if (!json.success || !json.data.has_more) {
            loadMoreBtn.closest('.fm-viewall').remove();
          } else {
            loadMoreBtn.disabled = false;
            label.textContent = original;
          }
        } catch (err) {
          loadMoreBtn.disabled = false;
          label.textContent = original;
        }
      });
    }

    document.querySelectorAll('.fm-faq').forEach(faq => {
      const items = faq.querySelectorAll('details');

      items.forEach(detail => {
        detail.querySelector('summary').addEventListener('click', e => {
          e.preventDefault();

          const isOpen = detail.hasAttribute('open');

          // Close all siblings first
          items.forEach(d => d.removeAttribute('open'));

          // If it wasn't open, open it
          if (!isOpen) detail.setAttribute('open', '');
        });
      });
    });
  });
</script>

<?php get_footer(); ?>
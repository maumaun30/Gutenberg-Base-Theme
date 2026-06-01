<?php
/**
 * Template: search.php  (FunaloMAX redesign)
 * Search results — games only.
 */

get_header();

$search_query = get_search_query();
$paged        = max( 1, (int) get_query_var( 'paged' ) );
$per_page     = 18;

$games_q = new WP_Query([
  'post_type'      => 'game',
  's'              => $search_query,
  'posts_per_page' => $per_page,
  'paged'          => $paged,
]);

$total_games = (int) $games_q->found_posts;
?>
<style>
  :root {
    --fm-bg:      rgb(16,14,27);
    --fm-bg-2:    rgb(4,1,19);
    --fm-card:    rgba(26,26,26,.7);
    --fm-card-br: rgba(255,255,255,.1);
    --fm-text:    #fff;
    --fm-muted:   rgb(161,161,170);
    --fm-muted-2: rgb(126,121,132);
    --fm-pink:    rgb(247,29,194);
    --fm-pink-2:  rgb(214,61,74);
    --fm-hot:     rgb(147,0,10);
  }

  .fm-page { background: var(--fm-bg); color: var(--fm-text); font-family: 'Montserrat', system-ui, sans-serif; }
  .fm-container { max-width: 1280px; margin: 0 auto; padding: 0 24px; }

  /* HERO */
  .fm-search-hero {
    position: relative;
    padding: 70px 0 50px;
    /*background:
      radial-gradient(ellipse at 20% 0%, rgba(247,29,194,.18), transparent 55%),
      radial-gradient(ellipse at 80% 100%, rgba(214,61,74,.14), transparent 60%),
      var(--fm-bg);*/
    background: radial-gradient(ellipse at 20% 0%, #ba001d52, transparent 55%), radial-gradient(ellipse at 80% 100%, rgba(214, 61, 74, .14), transparent 60%), #ba001d00;
  }

  .fm-search-hero__eyebrow {
    text-transform: uppercase; letter-spacing: .15em;
    font-size: 12px; color: #FFFFFF; font-weight: 700;
    margin-bottom: 12px;
  }
  .fm-search-hero h1 {
    font-weight: 700; font-size: clamp(28px, 4vw, 44px); line-height: 1.15;
    letter-spacing: -.02em; margin: 0 0 8px; color: #fff;
  }
  .fm-search-hero h1 .fm-q { color: var(--color-primary); }

  /* RESULTS */
  .fm-results { padding: 56px 0 80px; background: #000000;}

  .fm-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
  }
  @media (max-width: 1024px) { .fm-grid { grid-template-columns: repeat(4, 1fr); } }
  @media (max-width: 640px)  { .fm-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; } }

  .fm-card {
    position: relative; aspect-ratio: 1 / 1;
    border-radius: 12px; overflow: hidden;
    background: var(--fm-card);
    border: 1px solid var(--fm-card-br);
    text-decoration: none; display: block;
    transition: transform .3s, box-shadow .3s, border-color .3s;
  }
  .fm-card:hover { transform: translateY(-4px); border-color: rgba(247,29,194,.5); box-shadow: 0 10px 30px rgba(247,29,194,.18); }
  .fm-card__img { position: absolute; inset: 1px; width: calc(100% - 2px); height: calc(100% - 2px); object-fit: cover; display: block; }
  .fm-card__fallback { position: absolute; inset: 1px; background: linear-gradient(135deg, #1a1729, #0c0a1e); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,.2); font-size: 32px; font-weight: 700; }
  .fm-card__badge {
    position: absolute; top: 13px; left: 13px;
    padding: 4px 10px; border-radius: 4px;
    background: var(--fm-hot); color: #fff;
    font-size: 10px; font-weight: 700; line-height: 15px; letter-spacing: .04em;
  }
  .fm-card__title {
    position: absolute; left: 0; right: 0; bottom: 0;
    padding: 28px 12px 10px;
    background: linear-gradient(to top, rgba(0,0,0,.85), transparent);
    color: #fff; font-size: 13px; font-weight: 600; margin: 0;
  }

  /* PAGINATION */
  .fm-pagination {
    display: flex; justify-content: center; flex-wrap: wrap; gap: 8px;
    margin-top: 48px;
  }
  .fm-pagination a, .fm-pagination span {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 40px; height: 40px; padding: 0 12px;
    border-radius: 8px; border: 1px solid var(--fm-card-br);
    color: #fff; text-decoration: none; font-size: 14px; font-weight: 600;
    background: rgba(11,4,24,.6);
    transition: border-color .2s, background .2s, color .2s;
  }
  .fm-pagination a:hover { border-color: var(--fm-pink); color: var(--fm-pink); }
  .fm-pagination .current {
    background: linear-gradient(var(--fm-pink-2) 0%, var(--fm-pink) 100%);
    border-color: transparent; color: #fff;
  }
  .fm-pagination .dots { border-color: transparent; background: transparent; color: var(--fm-muted-2); }

  /* EMPTY */
  .fm-empty {
    text-align: center; padding: 80px 24px;
    border: 1px dashed var(--fm-card-br);
    border-radius: 16px;
    background: #1E1E1E;
    color: var(--fm-muted);
  }
  .fm-empty h3 { font-size: 22px; margin: 0 0 8px; color: #fff; }
  .fm-empty p { margin: 0; }
</style>

<div class="fm-page">

  <section class="fm-search-hero">
    <div class="fm-container">

      <div class="fm-search-hero__eyebrow">Search Results</div>
      <h1>
        <?php if ( $search_query ) : ?>
          Results for <span class="fm-q">&ldquo;<?php echo esc_html( $search_query ); ?>&rdquo;</span>
        <?php else : ?>
          Search FunaloMAX Games
        <?php endif; ?>
      </h1>
    </div>
  </section>

  <section class="fm-results">
    <div class="fm-container">

      <?php if ( $games_q->have_posts() ) : ?>

        <div class="fm-grid">
          <?php while ( $games_q->have_posts() ) : $games_q->the_post();
            $thumb  = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
            $is_hot = function_exists( 'get_field' ) ? (bool) get_field( 'is_hot' ) : false;
          ?>
            <a href="<?php the_permalink(); ?>" class="fm-card" aria-label="<?php the_title_attribute(); ?>">
              <?php if ( $thumb ) : ?>
                <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" class="fm-card__img" loading="lazy">
              <?php else : ?>
                <div class="fm-card__fallback"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></div>
              <?php endif; ?>
              <?php if ( $is_hot ) : ?><span class="fm-card__badge">HOT</span><?php endif; ?>
              <h3 class="fm-card__title"><?php the_title(); ?></h3>
            </a>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php
          $big   = 999999999;
          $links = paginate_links([
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => $paged,
            'total'     => $games_q->max_num_pages,
            'prev_text' => '‹ Prev',
            'next_text' => 'Next ›',
            'type'      => 'array',
            'end_size'  => 1,
            'mid_size'  => 2,
          ]);
          if ( $links ) : ?>
            <nav class="fm-pagination" aria-label="Search pagination">
              <?php foreach ( $links as $link ) {
                $cls = ( strpos( $link, 'dots' ) !== false ) ? ' class="dots"' : '';
                echo $link;
              } ?>
            </nav>
        <?php endif; ?>

      <?php else : ?>

        <div class="fm-empty">
          <h3>No words match <?php echo $search_query ? '&ldquo;' . esc_html( $search_query ) . '&rdquo;' : 'your search'; ?></h3>
          <p>Try a different keyword or check the spelling.</p>
        </div>

      <?php endif; ?>

    </div>
  </section>

</div>

<?php get_footer(); ?>
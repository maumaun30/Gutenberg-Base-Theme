<?php
/**
 * Template:  single-game.php
 * CPT:       game
 * Fields:    ACF (see companion acf-fields-game.php for registration)
 *
 * Layout:
 *  1. Hero  — blurred backdrop + centered thumbnail, title, category badges,
 *             Play Now button (opens iframe modal) + Demo button
 *  2. Info strip — RTP, Volatility, Min Bet, Max Bet, Provider, Release Date
 *  3. Body  — description / content
 *  4. Tags  — game_category breadcrumb pills
 *  5. Related games slider (same primary category)
 */

get_header();

/* ── Safety check ─────────────────────────────────────── */
if ( ! have_posts() ) {
    echo '<p style="color:#fff;text-align:center;padding:4rem;">Game not found.</p>';
    get_footer(); exit;
}
the_post();

$post_id  = get_the_ID();
$title    = get_the_title();
$content  = get_the_content();
$thumb_lg = get_the_post_thumbnail_url( $post_id, 'large' );
$thumb_xl = get_the_post_thumbnail_url( $post_id, 'full' );

/* ── ACF fields (graceful fallback if ACF not active) ─── */
function gc_acf( $key, $id ) {
    return function_exists( 'get_field' ) ? get_field( $key, $id ) : get_post_meta( $id, $key, true );
}

$game_url    = gc_acf( 'game_url',      $post_id );   // Play URL / iframe src
$demo_url    = gc_acf( 'demo_url',      $post_id );   // Demo / free-play URL
$provider    = gc_acf( 'provider',      $post_id );   // Text
$rtp         = gc_acf( 'rtp',           $post_id );   // Number  e.g. 96.5
$volatility  = gc_acf( 'volatility',    $post_id );   // Select: low | medium | high
$min_bet     = gc_acf( 'min_bet',       $post_id );   // Number
$max_bet     = gc_acf( 'max_bet',       $post_id );   // Number
$release     = gc_acf( 'release_date',  $post_id );   // Date string

/* ── Taxonomy ─────────────────────────────────────────── */
$game_cats   = get_the_terms( $post_id, 'game_category' );
$primary_cat = ( $game_cats && ! is_wp_error( $game_cats ) ) ? $game_cats[0] : null;

/* Volatility colour map */
$vol_color = [
    'low'    => '#22c55e',
    'medium' => '#f59e0b',
    'high'   => '#ef4444',
];
$vol_label = [
    'low'    => 'Low',
    'medium' => 'Medium',
    'high'   => 'High',
];
$vol_c = isset( $vol_color[ strtolower( $volatility ) ] ) ? $vol_color[ strtolower( $volatility ) ] : '#f59e0b';
$vol_l = isset( $vol_label[ strtolower( $volatility ) ] ) ? $vol_label[ strtolower( $volatility ) ] : ucfirst( $volatility );

/* ── Related games (same primary category, exclude self) ─ */
$related_games = [];
if ( $primary_cat ) {
    $rq = new WP_Query( [
        'post_type'      => 'game',
        'tax_query'      => [ [ 'taxonomy' => 'game_category', 'field' => 'term_id',
                                 'terms'   => $primary_cat->term_id ] ],
        'posts_per_page' => 12,
        'post__not_in'   => [ $post_id ],
        'orderby'        => 'rand',
    ] );
    if ( $rq->have_posts() ) {
        while ( $rq->have_posts() ) {
            $rq->the_post();
            $rcats = get_the_terms( get_the_ID(), 'game_category' );
            $related_games[] = [
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'permalink' => get_permalink(),
                'thumb'     => get_the_post_thumbnail_url( get_the_ID(), 'large' ),
                'badge'     => ( $rcats && ! is_wp_error( $rcats ) ) ? $rcats[0]->name : '',
            ];
        }
        wp_reset_postdata();
    }
}
?>
<style>
/* ------------------------------------------------------------------ */
/* CSS VARIABLES — remove block if main.css already declares these     */
/* ------------------------------------------------------------------ */
:root {
  --color-primary:       #f71dc2;
  --color-primary-hover: #fb11c1;
  --color-primary-light: #fb64d6;
  --color-secondary:     #d63d4a;
  --bg-dark-1:  #0a0a0b;
  --bg-dark-2:  #111114;
  --bg-dark-3:  #18181c;
  --bg-dark-4:  #1f1f25;
  --bg-dark-5:  #26262e;
  --bg-gray-4:  #42424f;
  --border:        rgba(255,255,255,.08);
  --border-strong: rgba(255,255,255,.15);
  --shadow-md:  0 4px 20px rgba(0,0,0,.5);
  --shadow-lg:  0 8px 40px rgba(0,0,0,.65);
  --shadow-glow:0 0 32px rgba(247,29,194,.35);
  --text-h1:  clamp(1.75rem,4vw,3rem);
  --text-h2:  clamp(1.5rem, 3vw,2.5rem);
  --text-h3:  clamp(1.25rem,2.5vw,2rem);
  --text-h4:  clamp(1.1rem, 2vw, 1.75rem);
  --text-lead:1.125rem;
  --section-py: clamp(2rem,5vw,5rem);
  --radius-lg: 1.25rem;
  --radius-md: .875rem;
}

/* ---- Page --------------------------------------------------------- */
.sg-page { background:var(--bg-dark-3); min-height:100vh; color:#fff; }

/* ================================================================== */
/*  HERO                                                               */
/* ================================================================== */
.sg-hero {
  position:relative; overflow:hidden;
  min-height:520px;
  display:flex; align-items:flex-end;
}

/* Blurred full-bleed background */
.sg-hero__backdrop {
  position:absolute; inset:0; z-index:0;
  background-size:cover; background-position:center;
  filter:blur(28px) brightness(.35) saturate(1.4);
  transform:scale(1.08);
}
.sg-hero__backdrop::after {
  content:''; position:absolute; inset:0;
  background:linear-gradient(to bottom,
    rgba(10,10,11,.3) 0%,
    rgba(10,10,11,.6) 60%,
    var(--bg-dark-3)  100%);
}

/* Pink top-left glow orb */
.sg-hero__orb {
  position:absolute; top:-4rem; left:-4rem;
  width:32rem; height:32rem; border-radius:50%;
  background:radial-gradient(circle,rgba(247,29,194,.25),transparent 70%);
  pointer-events:none; z-index:1;
}

.sg-hero__inner {
  position:relative; z-index:2;
  width:100%; max-width:80rem; margin:0 auto;
  padding: calc(var(--section-py) + 70px) 1.5rem var(--section-py);
  display:flex; flex-direction:column; gap:2.5rem;
}
@media(min-width:900px){
  .sg-hero__inner { flex-direction:row; align-items:flex-end; gap:3rem; }
}

/* Thumbnail card */
.sg-thumb-wrap {
  flex-shrink:0; align-self:center;
  width:220px; position:relative;
}
@media(min-width:900px){ .sg-thumb-wrap { width:260px; } }

.sg-thumb {
  width:100%; border-radius:var(--radius-lg);
  box-shadow:0 24px 60px rgba(0,0,0,.7), 0 0 0 1px var(--border-strong);
  display:block; object-fit:cover; aspect-ratio:3/4;
}
.sg-thumb-fallback {
  width:100%; aspect-ratio:3/4; border-radius:var(--radius-lg);
  background:linear-gradient(135deg,var(--bg-dark-4),var(--bg-gray-4));
  display:flex; align-items:center; justify-content:center;
  box-shadow:0 24px 60px rgba(0,0,0,.7); border:1px solid var(--border-strong);
}

/* Provider ribbon on thumb */
.sg-provider-ribbon {
  position:absolute; bottom:-.75rem; left:50%; transform:translateX(-50%);
  white-space:nowrap;
  background:var(--bg-dark-4); border:1px solid var(--border-strong);
  padding:.35rem 1rem; border-radius:9999px;
  font-family:'Outfit',sans-serif; font-size:.75rem; font-weight:700;
  color:rgba(255,255,255,.8); letter-spacing:.04em;
}

/* Info side */
.sg-hero__info { flex:1; min-width:0; }

/* Breadcrumb */
.sg-bc {
  display:flex; align-items:center; gap:.4rem; flex-wrap:wrap;
  font-family:'Outfit',sans-serif; font-size:.75rem; font-weight:600;
  text-transform:uppercase; letter-spacing:.1em; margin-bottom:1rem;
}
.sg-bc a { color:rgba(255,255,255,.45); text-decoration:none; transition:color .2s; }
.sg-bc a:hover { color:var(--color-primary); }
.sg-bc__sep { color:rgba(255,255,255,.2); }
.sg-bc__cur { color:var(--color-primary); }

/* Title */
.sg-title {
  font-family:'Bebas Neue',sans-serif;
  font-size:var(--text-h1); color:#fff;
  letter-spacing:.04em; line-height:1.05;
  margin:0 0 1rem;
}

/* Category pills */
.sg-cats { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1.5rem; }
.sg-cat-pill {
  display:inline-flex; align-items:center;
  padding:.3rem .875rem; border-radius:9999px;
  background:rgba(247,29,194,.12); border:1px solid rgba(247,29,194,.35);
  font-family:'Outfit',sans-serif; font-size:.72rem; font-weight:700;
  color:var(--color-primary); text-transform:uppercase; letter-spacing:.07em;
  text-decoration:none; transition:all .2s;
}
.sg-cat-pill:hover { background:var(--color-primary); color:#000; }

/* CTA buttons */
.sg-cta { display:flex; flex-wrap:wrap; gap:.875rem; }

.sg-btn-play {
  display:inline-flex; align-items:center; gap:.6rem;
  padding:.875rem 2rem; border-radius:var(--radius-md);
  background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));
  color:#fff; font-family:'Outfit',sans-serif; font-size:1rem; font-weight:700;
  border:none; cursor:pointer; text-decoration:none;
  box-shadow:var(--shadow-glow); transition:all .25s;
  letter-spacing:.03em;
}
.sg-btn-play:hover { transform:translateY(-2px); box-shadow:0 0 48px rgba(247,29,194,.5); filter:brightness(1.1); }
.sg-btn-play:active { transform:scale(.97); }
.sg-btn-play svg { width:1.25rem; height:1.25rem; }

.sg-btn-demo {
  display:inline-flex; align-items:center; gap:.6rem;
  padding:.875rem 1.75rem; border-radius:var(--radius-md);
  background:transparent; border:2px solid var(--border-strong);
  color:rgba(255,255,255,.8); font-family:'Outfit',sans-serif;
  font-size:1rem; font-weight:600; cursor:pointer; text-decoration:none;
  transition:all .25s; letter-spacing:.03em;
}
.sg-btn-demo:hover {
  border-color:var(--color-primary); color:var(--color-primary);
  box-shadow:var(--shadow-glow);
}
.sg-btn-demo svg { width:1.125rem; height:1.125rem; }

/* ================================================================== */
/*  INFO STRIP                                                         */
/* ================================================================== */
.sg-strip {
  background:var(--bg-dark-1);
  border-top:1px solid var(--border);
  border-bottom:1px solid var(--border);
  padding:1.5rem 0;
}
.sg-strip__inner {
  max-width:80rem; margin:0 auto; padding:0 1.5rem;
  display:flex; flex-wrap:wrap; gap:0;
}
.sg-stat {
  flex:1 1 140px; padding:1rem 1.25rem;
  display:flex; flex-direction:column; align-items:center;
  border-right:1px solid var(--border); text-align:center;
}
.sg-stat:last-child { border-right:none; }
.sg-stat__icon { width:1.625rem; height:1.625rem; color:var(--color-primary); margin-bottom:.5rem; }
.sg-stat__val {
  font-family:'Bebas Neue',sans-serif; font-size:1.375rem;
  color:#fff; letter-spacing:.04em; line-height:1;
}
.sg-stat__lbl {
  font-family:'Outfit',sans-serif; font-size:.7rem; font-weight:600;
  text-transform:uppercase; letter-spacing:.09em;
  color:rgba(255,255,255,.4); margin-top:.3rem;
}
/* Volatility colour dot */
.sg-vol-dot {
  display:inline-block; width:.55rem; height:.55rem;
  border-radius:50%; margin-right:.35rem; vertical-align:middle;
}

/* ================================================================== */
/*  MAIN CONTENT                                                       */
/* ================================================================== */
.sg-main {
  max-width:80rem; margin:0 auto;
  padding:var(--section-py) 1.5rem;
  display:grid; gap:2.5rem;
}
@media(min-width:900px){
  .sg-main { grid-template-columns:1fr 320px; }
}

/* Description card */
.sg-desc-card {
  background:var(--bg-dark-4); border:1px solid var(--border);
  border-radius:var(--radius-lg); padding:2rem;
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

/* Sidebar */
.sg-sidebar { display:flex; flex-direction:column; gap:1.5rem; }

/* Detail card (sidebar) */
.sg-detail-card {
  background:var(--bg-dark-4); border:1px solid var(--border);
  border-radius:var(--radius-lg); padding:1.5rem;
}
.sg-detail-card__title {
  font-family:'Bebas Neue',sans-serif; font-size:1.125rem;
  color:#fff; letter-spacing:.06em; margin:0 0 1.25rem;
  padding-bottom:.75rem; border-bottom:1px solid var(--border);
}
.sg-detail-row {
  display:flex; align-items:center; justify-content:space-between;
  padding:.625rem 0; border-bottom:1px solid var(--border);
  gap:.75rem;
}
.sg-detail-row:last-child { border-bottom:none; padding-bottom:0; }
.sg-detail-row__lbl {
  font-family:'Outfit',sans-serif; font-size:.78rem; font-weight:600;
  text-transform:uppercase; letter-spacing:.08em;
  color:rgba(255,255,255,.4); white-space:nowrap;
}
.sg-detail-row__val {
  font-family:'Lexend',sans-serif; font-size:.875rem; font-weight:600;
  color:#fff; text-align:right;
}

/* Play button card (sidebar) */
.sg-play-card {
  background:linear-gradient(135deg,rgba(247,29,194,.12),rgba(214,61,74,.08));
  border:1px solid rgba(247,29,194,.25);
  border-radius:var(--radius-lg); padding:1.5rem; text-align:center;
}
.sg-play-card p {
  font-family:'Lexend',sans-serif; font-size:.8rem;
  color:rgba(255,255,255,.5); margin:.75rem 0 0;
}
.sg-play-card .sg-btn-play,
.sg-play-card .sg-btn-demo { width:100%; justify-content:center; }
.sg-play-card .sg-btn-demo { margin-top:.625rem; }

/* Categories card */
.sg-cats-card {
  background:var(--bg-dark-4); border:1px solid var(--border);
  border-radius:var(--radius-lg); padding:1.5rem;
}
.sg-cats-card__title {
  font-family:'Bebas Neue',sans-serif; font-size:1.125rem;
  color:#fff; letter-spacing:.06em; margin:0 0 1rem;
}
.sg-cats-pills { display:flex; flex-wrap:wrap; gap:.5rem; }

/* ================================================================== */
/*  RELATED GAMES SLIDER                                               */
/* ================================================================== */
.sg-related {
  background:var(--bg-dark-2);
  padding:var(--section-py) 0;
  border-top:1px solid var(--border);
}
.sg-related__inner { max-width:80rem; margin:0 auto; padding:0 1.5rem; }

/* Section heading */
.sg-related-hd {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:1.5rem; gap:1rem;
}
.sg-related-hd__left { display:flex; align-items:center; gap:.875rem; }
.sg-rel-icon {
  width:2.75rem; height:2.75rem; border-radius:.75rem; flex-shrink:0;
  background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));
  display:flex; align-items:center; justify-content:center;
}
.sg-rel-icon svg { width:1.25rem; height:1.25rem; color:#000; }
.sg-related-hd h2 {
  font-family:'Bebas Neue',sans-serif; font-size:var(--text-h3);
  color:#fff; letter-spacing:.05em; margin:0;
}
.sg-related-hd p {
  font-family:'Lexend',sans-serif; font-size:.85rem;
  color:rgba(255,255,255,.5); margin:.1rem 0 0;
}

/* Arrows */
.sg-arr-group { display:flex; gap:.5rem; flex-shrink:0; }
.sg-arr {
  width:2.25rem; height:2.25rem; border-radius:50%; cursor:pointer;
  background:var(--bg-dark-4); border:1px solid var(--border);
  display:flex; align-items:center; justify-content:center;
  color:rgba(255,255,255,.7); transition:all .22s;
}
.sg-arr:hover {
  background:var(--color-primary); border-color:var(--color-primary);
  color:#000; box-shadow:var(--shadow-glow);
}
.sg-arr:disabled { opacity:.3; pointer-events:none; }
.sg-arr svg { width:1rem; height:1rem; }

/* Slider */
.sg-slider-wrap { position:relative; }
.sg-slider-wrap::after {
  content:''; pointer-events:none;
  position:absolute; top:0; right:0; bottom:0; width:5rem; z-index:2;
  background:linear-gradient(to right,transparent,var(--bg-dark-2));
}
.sg-slider {
  display:flex; gap:1.125rem;
  overflow-x:auto; scroll-snap-type:x mandatory;
  -webkit-overflow-scrolling:touch;
  scrollbar-width:none; padding-bottom:.5rem;
  cursor:grab;
}
.sg-slider:active { cursor:grabbing; }
.sg-slider::-webkit-scrollbar { display:none; }

/* Related card */
.sg-rcard {
  scroll-snap-align:start;
  flex: 0 0 calc(50% - .5rem);
  position:relative; border-radius:var(--radius-md); overflow:hidden;
  background:var(--bg-dark-4); border:1px solid var(--border);
  text-decoration:none; display:block;
  transition:transform .35s, box-shadow .35s, border-color .35s;
}
@media(min-width:480px)  { .sg-rcard { flex: 0 0 calc(33.333% - .75rem); } }
@media(min-width:768px)  { .sg-rcard { flex: 0 0 calc(25% - .85rem); } }
@media(min-width:1280px) { .sg-rcard { flex: 0 0 calc(20% - .9rem); } }

.sg-rcard:hover { transform:translateY(-6px); box-shadow:var(--shadow-lg); border-color:var(--border-strong); }
.sg-rcard:hover .sg-rcard__img  { transform:scale(1.07); }
.sg-rcard:hover .sg-rcard__name { color:var(--color-primary); }
.sg-rcard:hover .sg-rcard__glow { opacity:1; }

.sg-rcard__img-wrap { position:relative; aspect-ratio:3/4; overflow:hidden; }
.sg-rcard__img { width:100%; height:100%; object-fit:cover; transition:transform .6s; display:block; }
.sg-rcard__fallback {
  width:100%; height:100%;
  background:linear-gradient(135deg,var(--bg-dark-4),var(--bg-gray-4));
  display:flex; align-items:center; justify-content:center;
}
.sg-rcard__overlay {
  position:absolute; inset:0;
  background:linear-gradient(to top,rgba(0,0,0,.92) 0%,rgba(0,0,0,.2) 55%,transparent 100%);
}
.sg-rcard__badge {
  position:absolute; top:.6rem; left:.6rem;
  padding:.18rem .6rem; border-radius:9999px;
  background:rgba(247,29,194,.2); border:1px solid rgba(247,29,194,.4);
  color:var(--color-primary); font-family:'Outfit',sans-serif;
  font-size:.6rem; font-weight:700; text-transform:uppercase;
  backdrop-filter:blur(6px);
}
.sg-rcard__info { position:absolute; bottom:0; left:0; right:0; padding:.875rem; }
.sg-rcard__name {
  font-family:'Bebas Neue',sans-serif; font-size:1.25rem;
  color:#fff; letter-spacing:.04em; margin:0; transition:color .3s;
}
.sg-rcard__glow {
  position:absolute; inset:0; pointer-events:none; opacity:0;
  background:radial-gradient(circle at 50% 0%,rgba(247,29,194,.18),transparent 70%);
  transition:opacity .5s;
}

/* ================================================================== */
/*  PLAY MODAL  (iframe lightbox)                                      */
/* ================================================================== */
.sg-modal {
  display:none; position:fixed; inset:0; z-index:9999;
  align-items:center; justify-content:center;
  background:rgba(0,0,0,.88);
  backdrop-filter:blur(8px);
  padding:1rem;
}
.sg-modal.is-open { display:flex; }
.sg-modal__box {
  position:relative; width:100%; max-width:1100px;
  background:var(--bg-dark-1); border:1px solid var(--border-strong);
  border-radius:var(--radius-lg); overflow:hidden;
  box-shadow:0 40px 100px rgba(0,0,0,.8);
  animation:modalIn .3s ease both;
}
@keyframes modalIn {
  from { opacity:0; transform:scale(.93) translateY(20px); }
  to   { opacity:1; transform:scale(1)   translateY(0); }
}
.sg-modal__topbar {
  display:flex; align-items:center; justify-content:space-between;
  padding:.875rem 1.25rem;
  background:var(--bg-dark-2); border-bottom:1px solid var(--border);
}
.sg-modal__game-name {
  font-family:'Bebas Neue',sans-serif; font-size:1.25rem;
  color:#fff; letter-spacing:.05em; margin:0;
}
.sg-modal__close {
  width:2rem; height:2rem; border-radius:50%; cursor:pointer;
  background:var(--bg-dark-4); border:1px solid var(--border);
  display:flex; align-items:center; justify-content:center;
  color:rgba(255,255,255,.7); transition:all .2s;
}
.sg-modal__close:hover { background:var(--color-primary); color:#000; border-color:var(--color-primary); }
.sg-modal__close svg { width:.875rem; height:.875rem; }
.sg-modal__iframe-wrap {
  position:relative; width:100%; padding-bottom:56.25%;
}
.sg-modal__iframe-wrap iframe {
  position:absolute; inset:0; width:100%; height:100%; border:none;
}
.sg-modal__loading {
  position:absolute; inset:0; display:flex; flex-direction:column;
  align-items:center; justify-content:center; gap:1rem;
  background:var(--bg-dark-1);
  font-family:'Outfit',sans-serif; font-size:.875rem;
  color:rgba(255,255,255,.5);
}
.sg-modal__loading .sg-spinner {
  width:2.5rem; height:2.5rem; border-radius:50%;
  border:3px solid var(--border); border-top-color:var(--color-primary);
  animation:spin .8s linear infinite;
}
@keyframes spin { to { transform:rotate(360deg); } }

/* ================================================================== */
/*  MISC UTILITIES                                                     */
/* ================================================================== */
@keyframes fadeUp {
  from { opacity:0; transform:translateY(16px); }
  to   { opacity:1; transform:translateY(0); }
}
.sg-fadein { animation:fadeUp .5s ease both; }
</style>


<div class="sg-page">

<!-- ════════════════════════ HERO ══════════════════════════════════ -->
<section class="sg-hero">

  <!-- Blurred backdrop -->
  <div class="sg-hero__backdrop"
       style="background-image:url('<?php echo esc_url( $thumb_xl ?: $thumb_lg ); ?>')">
  </div>
  <div class="sg-hero__orb"></div>

  <div class="sg-hero__inner sg-fadein">

    <!-- Thumbnail -->
    <div class="sg-thumb-wrap">
      <?php if ( $thumb_lg ) : ?>
        <img src="<?php echo esc_url( $thumb_lg ); ?>"
             alt="<?php echo esc_attr( $title ); ?>"
             class="sg-thumb">
      <?php else : ?>
        <div class="sg-thumb-fallback">
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
      
    </div>

    <!-- Info -->
    <div class="sg-hero__info">

      <!-- Breadcrumb -->
      <nav class="sg-bc">
        
        <?php if ( $game_cats && ! is_wp_error( $game_cats ) ) :
          /* Walk the ancestry of the primary cat */
          $ancs = array_reverse( get_ancestors( $primary_cat->term_id, 'game_category', 'taxonomy' ) );
          foreach ( $ancs as $aid ) :
            $at = get_term( $aid, 'game_category' ); ?>
            
            
          <?php endforeach; ?>
          
          <a href="<?php echo esc_url( get_term_link( $primary_cat ) ); ?>"><?php echo esc_html( $primary_cat->name ); ?></a>
        <?php endif; ?>
        <span class="sg-bc__sep">›</span>
        <span class="sg-bc__cur"><?php echo esc_html( $title ); ?></span>
      </nav>

      <!-- Title -->
      <h1 class="sg-title"><?php echo esc_html( $title ); ?></h1>


      <!-- CTA -->
      <div class="sg-cta">
        <?php if ( $game_url ) : ?>
          <button class="sg-btn-play js-open-modal"
                  data-url="<?php echo esc_url( $game_url ); ?>"
                  data-title="<?php echo esc_attr( $title ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                 fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            Play Now
          </button>
        <?php endif; ?>
        <?php if ( $demo_url ) : ?>
          <button class="sg-btn-demo js-open-modal"
                  data-url="<?php echo esc_url( $demo_url ); ?>"
                  data-title="<?php echo esc_attr( $title ); ?> — Demo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <polygon points="10 8 16 12 10 16 10 8" fill="currentColor" stroke="none"/>
            </svg>
            Try Demo
          </button>
        <?php endif; ?>
      </div>

    </div><!-- /.sg-hero__info -->
  </div><!-- /.sg-hero__inner -->
</section><!-- /hero -->


<!-- ════════════════════════ INFO STRIP ════════════════════════════ -->
<!-- /strip -->


<!-- ════════════════════════ MAIN CONTENT ══════════════════════════ -->
<div class="sg-main">

  <!-- Left: Description -->
  <div class="sg-desc-card sg-fadein">
    <h2>About <?php echo esc_html( $title ); ?></h2>
    <?php if ( $content ) : ?>
      <div class="sg-content"><?php echo apply_filters( 'the_content', $content ); ?></div>
    <?php else : ?>
      <div class="sg-content">
        <p style="color:rgba(255,255,255,.35);font-style:italic;">
          No description has been added for this game yet.
        </p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Right: Sidebar -->
  <aside class="sg-sidebar sg-fadein">

    <!-- Play buttons card -->
    

    <!-- Game details card -->
    <div class="sg-detail-card">
      <h3 class="sg-detail-card__title">Game Details</h3>

      <?php if ( $provider ) : ?>
        <div class="sg-detail-row">
          <span class="sg-detail-row__lbl">Developer</span>
          <span class="sg-detail-row__val"><?php echo esc_html( $provider ); ?></span>
        </div>
      <?php endif; ?>

      <?php if ( $rtp ) : ?>
        <div class="sg-detail-row">
          <span class="sg-detail-row__lbl">RTP</span>
          <span class="sg-detail-row__val"><?php echo esc_html( $rtp ); ?>%</span>
        </div>
      <?php endif; ?>

      <?php if ( $volatility ) : ?>
        <div class="sg-detail-row">
          <span class="sg-detail-row__lbl">Volatility</span>
          <span class="sg-detail-row__val" style="color:<?php echo esc_attr( $vol_c ); ?>">
            <span class="sg-vol-dot" style="background:<?php echo esc_attr( $vol_c ); ?>"></span>
            <?php echo esc_html( $vol_l ); ?>
          </span>
        </div>
      <?php endif; ?>

      <?php if ( $min_bet ) : ?>
        <div class="sg-detail-row">
          <span class="sg-detail-row__lbl">Min Bet</span>
          <span class="sg-detail-row__val">₱<?php echo esc_html( $min_bet ); ?></span>
        </div>
      <?php endif; ?>

      <?php if ( $max_bet ) : ?>
        <div class="sg-detail-row">
          <span class="sg-detail-row__lbl">Max Bet</span>
          <span class="sg-detail-row__val">₱<?php echo esc_html( $max_bet ); ?></span>
        </div>
      <?php endif; ?>

      <?php if ( $release ) : ?>
        <div class="sg-detail-row">
          <span class="sg-detail-row__lbl">Released</span>
          <span class="sg-detail-row__val">
            <?php echo esc_html( date( 'Y', strtotime( $release ) ) ); ?>
          </span>
        </div>
      <?php endif; ?>

    </div><!-- /.sg-detail-card -->

    <!-- Categories card -->
    <?php if ( $game_cats && ! is_wp_error( $game_cats ) ) : ?>
      <div class="sg-cats-card">
        <h3 class="sg-cats-card__title">Categories</h3>
        <div class="sg-cats-pills">
          <?php foreach ( $game_cats as $cat ) : ?>
            <p
               class="sg-cat-pill"><?php echo esc_html( $cat->name ); ?></p>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </aside>
</div><!-- /.sg-main -->


<!-- ════════════════════════ RELATED GAMES ═════════════════════════ -->
<?php if ( ! empty( $related_games ) ) : ?>
  <section class="sg-related">
    <div class="sg-related__inner">

      <div class="sg-related-hd">
        <div class="sg-related-hd__left">
          <div class="sg-rel-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02
                               12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div>
            <h2>More Like This</h2>
            <p>More games from
              <?php if ( $primary_cat ) : ?>
                <a href="<?php echo esc_url( get_term_link( $primary_cat ) ); ?>"
                   style="color:var(--color-primary);text-decoration:none;">
                  <?php echo esc_html( $primary_cat->name ); ?>
                </a>
              <?php endif; ?>
            </p>
          </div>
        </div>

        <div class="sg-arr-group">
          <button class="sg-arr sg-arr--prev" data-target="sg-rel-slider" aria-label="Prev" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <button class="sg-arr sg-arr--next" data-target="sg-rel-slider" aria-label="Next">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
      </div>

      <div class="sg-slider-wrap">
        <div class="sg-slider" id="sg-rel-slider">
          <?php foreach ( $related_games as $rg ) : ?>
            <a href="<?php echo esc_url( $rg['permalink'] ); ?>" class="sg-rcard">
              <div class="sg-rcard__img-wrap">
                <?php if ( $rg['thumb'] ) : ?>
                  <img src="<?php echo esc_url( $rg['thumb'] ); ?>"
                       alt="<?php echo esc_attr( $rg['title'] ); ?>"
                       class="sg-rcard__img" loading="lazy">
                <?php else : ?>
                  <div class="sg-rcard__fallback">
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
                <div class="sg-rcard__overlay"></div>
                <?php if ( $rg['badge'] ) : ?>
                  <span class="sg-rcard__badge"><?php echo esc_html( $rg['badge'] ); ?></span>
                <?php endif; ?>
                <div class="sg-rcard__info">
                  <h3 class="sg-rcard__name"><?php echo esc_html( $rg['title'] ); ?></h3>
                </div>
              </div>
              <div class="sg-rcard__glow"></div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </section>
<?php endif; ?>

</div><!-- /.sg-page -->


<!-- ════════════════════════ IFRAME MODAL ══════════════════════════ -->
<div class="sg-modal" id="sg-modal" role="dialog" aria-modal="true">
  <div class="sg-modal__box">
    <div class="sg-modal__topbar">
      <h2 class="sg-modal__game-name" id="sg-modal-title"></h2>
      <button class="sg-modal__close" id="sg-modal-close" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6"  y1="6" x2="18" y2="18"/>
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


<!-- ════════════════════════ SCRIPTS ═══════════════════════════════ -->
<script>
(function () {
  'use strict';

  /* ── 1. IFRAME MODAL ─────────────────────────────────────────── */
  var modal       = document.getElementById('sg-modal');
  var modalIframe = document.getElementById('sg-modal-iframe');
  var modalTitle  = document.getElementById('sg-modal-title');
  var modalLoad   = document.getElementById('sg-modal-loading');
  var modalClose  = document.getElementById('sg-modal-close');

  function openModal(url, title) {
    modalTitle.textContent    = title;
    modalIframe.src           = '';          // reset first
    modalLoad.style.display   = 'flex';
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';

    /* Show iframe once loaded */
    modalIframe.onload = function () {
      modalLoad.style.display = 'none';
    };
    modalIframe.src = url;
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modalIframe.src = '';
    document.body.style.overflow = '';
  }

  /* Open on any .js-open-modal button */
  document.querySelectorAll('.js-open-modal').forEach(function (btn) {
    btn.addEventListener('click', function () {
      openModal(this.dataset.url, this.dataset.title);
    });
  });

  /* Close buttons */
  if (modalClose) modalClose.addEventListener('click', closeModal);

  /* Click outside box */
  modal.addEventListener('click', function (e) {
    if (e.target === modal) closeModal();
  });

  /* Escape key */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
  });


  /* ── 2. RELATED SLIDER — ARROW BUTTONS ──────────────────────── */
  document.querySelectorAll('.sg-arr').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var slider = document.getElementById(this.dataset.target);
      if (!slider) return;
      var card   = slider.querySelector('.sg-rcard');
      var amount = card ? (card.offsetWidth + 18) * 2 : 320;
      slider.scrollBy({
        left: this.classList.contains('sg-arr--prev') ? -amount : amount,
        behavior: 'smooth'
      });
    });
  });

  /* ── 3. SLIDER — MOUSE DRAG ──────────────────────────────────── */
  document.querySelectorAll('.sg-slider').forEach(function (slider) {
    var isDown = false, startX, scrollLeft, moved = false;

    slider.addEventListener('mousedown', function (e) {
      isDown = true; moved = false;
      startX = e.pageX - slider.offsetLeft;
      scrollLeft = slider.scrollLeft;
      slider.style.cursor = 'grabbing';
    });
    document.addEventListener('mouseup', function () {
      if (!isDown) return;
      isDown = false; slider.style.cursor = 'grab';
    });
    slider.addEventListener('mousemove', function (e) {
      if (!isDown) return;
      e.preventDefault(); moved = true;
      slider.scrollLeft = scrollLeft - (e.pageX - slider.offsetLeft - startX) * 1.5;
    });
    slider.addEventListener('click', function (e) {
      if (moved) { e.preventDefault(); moved = false; }
    }, true);
  });

  /* ── 4. ARROW DISABLED STATE ─────────────────────────────────── */
  function syncArrows(slider) {
    var id   = slider.id;
    var prev = document.querySelector('.sg-arr--prev[data-target="' + id + '"]');
    var next = document.querySelector('.sg-arr--next[data-target="' + id + '"]');
    if (!prev || !next) return;
    prev.disabled = slider.scrollLeft <= 2;
    next.disabled = slider.scrollLeft >= slider.scrollWidth - slider.clientWidth - 2;
  }

  document.querySelectorAll('.sg-slider').forEach(function (s) {
    syncArrows(s);
    s.addEventListener('scroll', function () { syncArrows(s); }, { passive: true });
  });

})();
</script>

<?php get_footer(); ?>
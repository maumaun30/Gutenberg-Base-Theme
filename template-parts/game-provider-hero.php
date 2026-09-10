<?php
/**
 * Template Part: Game Provider Hero (FunaloMAX redesign)
 *
 * Mirrors game-category-hero.php. `provider` is non-hierarchical, so the
 * breadcrumb is a flat Home > {Provider} — there are no ancestors to walk.
 *
 * ACF lookups pass the WP_Term object rather than a "{taxonomy}_{id}" string,
 * which resolves correctly on any taxonomy.
 *
 * @var WP_Term $term
 * @var string  $term_name
 * @var string  $term_desc
 * @var int     $game_count
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$term      = get_query_var( 'term' );
$term_name = get_query_var( 'term_name' );
$term_desc = get_query_var( 'term_desc' );

$assets_url = get_template_directory_uri() . '/assets/images/category-template';
$hero_bg    = $assets_url . '/Image of a carnival.webp'; // fallback

$hero_bg_mobile = ''; // only set when the term has a dedicated mobile crop

/* Prefer ACF term image: fnlmx_game_category_featured_image */
if ( function_exists( 'get_field' ) && $term instanceof WP_Term ) {
    $acf_img = get_field( 'fnlmx_game_category_featured_image', $term );
    if ( is_array( $acf_img ) ) {
        $hero_bg = ! empty( $acf_img['sizes']['large'] )
            ? $acf_img['sizes']['large']
            : ( ! empty( $acf_img['url'] ) ? $acf_img['url'] : $hero_bg );
    }

    /* Optional portrait crop: fnlmx_game_category_featured_image_mobile.
       Left empty when unset so the desktop image above keeps serving mobile. */
    $acf_img_m = get_field( 'fnlmx_game_category_featured_image_mobile', $term );
    if ( is_array( $acf_img_m ) ) {
        $hero_bg_mobile = ! empty( $acf_img_m['sizes']['large'] )
            ? $acf_img_m['sizes']['large']
            : ( ! empty( $acf_img_m['url'] ) ? $acf_img_m['url'] : '' );
    }
}

/* Prefer ACF term title group: fnlmx_game_category_main_title */
$title_highlighted = '';
$title_white       = '';

if ( function_exists( 'get_field' ) && $term instanceof WP_Term ) {
    $acf_title = get_field( 'fnlmx_game_category_main_title', $term );
    if ( is_array( $acf_title ) ) {
        $title_highlighted = trim( (string) ( $acf_title['fnlmx_game_category_highlighted_title'] ?? '' ) );
        $title_white       = trim( (string) ( $acf_title['fnlmx_game_category_white_title'] ?? '' ) );
    }
}

/* Fallback to the previous hardcoded pattern */
if ( '' === $title_highlighted && '' === $title_white ) {
    $title_highlighted = sprintf( 'Play %s Games', $term_name );
    $title_white       = 'in the Philippines';
}
?>
<section class="fm-hero">
  <div class="fm-hero__bg" style="background-image:url('<?php echo esc_url( $hero_bg ); ?>');"></div>
  <?php if ( $hero_bg_mobile ) : ?>
    <div class="fm-hero__bg fm-hero__bg--mobile" style="background-image:url('<?php echo esc_url( $hero_bg_mobile ); ?>');"></div>
    <style>
      /* Swap in the portrait crop below the hero's mobile breakpoint. */
      .fm-hero__bg--mobile { display: none; }
      @media (max-width: 640px) {
        .fm-hero > .fm-hero__bg:not(.fm-hero__bg--mobile) { display: none; }
        .fm-hero__bg--mobile { display: block; }
      }
    </style>
  <?php endif; ?>
  <div class="fm-hero__overlay"></div>

  <div class="fm-hero__inner">
    <nav class="fm-bc" aria-label="Breadcrumb">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
      <svg viewBox="0 0 6 10" width="6" height="10" aria-hidden="true"><path d="M3.818 5L0 1.111 1.091 0 6 5l-4.909 5L0 8.889 3.818 5z" fill="currentColor"/></svg>
      <span class="fm-bc__cur"><?php echo esc_html( $term_name ); ?></span>
    </nav>

    <h1 class="fm-hero__title">
      <?php if ( $title_highlighted ) : ?><span style="color:#ba001d;"><?php echo esc_html( $title_highlighted ); ?></span><?php endif; ?>
      <?php if ( $title_highlighted && $title_white ) : ?><?php endif; ?>
      <?php echo esc_html( $title_white ); ?>
    </h1>

    <p class="fm-hero__desc">
      <?php echo $term_desc
        ? wp_kses_post( $term_desc )
        : 'Discover the full lineup of games from ' . esc_html( $term_name ) . '. Play everything from iconic classics to fast-paced live action.'; ?>
    </p>
  </div>
</section>

<?php
/**
 * Template Part: Game Category Hero (FunaloMAX redesign)
 *
 * @var WP_Term $term
 * @var string  $term_name
 * @var string  $term_desc
 * @var int     $parent_term_id
 * @var array   $ancestors
 * @var int     $game_count
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$term           = get_query_var( 'term' );
$term_name      = get_query_var( 'term_name' );
$term_desc      = get_query_var( 'term_desc' );
$parent_term_id = get_query_var( 'parent_term_id' );
$ancestors      = get_query_var( 'ancestors' );

$assets_url = get_template_directory_uri() . '/assets/images/category-template';
$hero_bg    = $assets_url . '/ce74fb3646e3.png'; // fallback

$hero_bg_mobile = ''; // only set when the term has a dedicated mobile crop

/* Prefer ACF term image: fnlmx_game_category_featured_image */
if ( function_exists( 'get_field' ) && $term && ! empty( $term->term_id ) ) {
    $acf_img = get_field( 'fnlmx_game_category_featured_image', 'game_category_' . $term->term_id );
    if ( is_array( $acf_img ) ) {
        $hero_bg = ! empty( $acf_img['sizes']['large'] )
            ? $acf_img['sizes']['large']
            : ( ! empty( $acf_img['url'] ) ? $acf_img['url'] : $hero_bg );
    }

    /* Optional portrait crop: fnlmx_game_category_featured_image_mobile.
       Left empty when unset so the desktop image above keeps serving mobile. */
    $acf_img_m = get_field( 'fnlmx_game_category_featured_image_mobile', 'game_category_' . $term->term_id );
    if ( is_array( $acf_img_m ) ) {
        $hero_bg_mobile = ! empty( $acf_img_m['sizes']['large'] )
            ? $acf_img_m['sizes']['large']
            : ( ! empty( $acf_img_m['url'] ) ? $acf_img_m['url'] : '' );
    }
}

/* Prefer ACF term title group: fnlmx_game_category_main_title */
$title_highlighted = '';
$title_white       = '';

if ( function_exists( 'get_field' ) && $term && ! empty( $term->term_id ) ) {
    $acf_title = get_field( 'fnlmx_game_category_main_title', 'game_category_' . $term->term_id );
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
      <?php foreach ( (array) $ancestors as $aid ) :
        $at = get_term( $aid, 'game_category' ); ?>
        <a href="<?php echo esc_url( get_term_link( $at ) ); ?>"><?php echo esc_html( $at->name ); ?></a>
        <svg viewBox="0 0 6 10" width="6" height="10" aria-hidden="true"><path d="M3.818 5L0 1.111 1.091 0 6 5l-4.909 5L0 8.889 3.818 5z" fill="currentColor"/></svg>
      <?php endforeach; ?>
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
        : 'Step into the excitement of ' . esc_html( $term_name ) . ' games online. Play everything from iconic classics to fast-paced live action.'; ?>
    </p>
  </div>
</section>

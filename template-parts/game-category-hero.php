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

/* Prefer ACF term image: fnlmx_game_category_featured_image */
if ( function_exists( 'get_field' ) && $term && ! empty( $term->term_id ) ) {
    $acf_img = get_field( 'fnlmx_game_category_featured_image', 'game_category_' . $term->term_id );
    if ( is_array( $acf_img ) ) {
        $hero_bg = ! empty( $acf_img['sizes']['large'] )
            ? $acf_img['sizes']['large']
            : ( ! empty( $acf_img['url'] ) ? $acf_img['url'] : $hero_bg );
    }
}
?>
<section class="fm-hero">
  <div class="fm-hero__bg" style="background-image:url('<?php echo esc_url( $hero_bg ); ?>');"></div>
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
      Play <?php echo esc_html( $term_name ); ?> Games on<br><span style="color:#ba001d;">FUNaloMAX</span>
    </h1>

    <p class="fm-hero__desc">
      <?php echo $term_desc
        ? wp_kses_post( $term_desc )
        : 'Step into the excitement of ' . esc_html( $term_name ) . ' games online. Play everything from iconic classics to fast-paced live action.'; ?>
    </p>
  </div>
</section>

<?php
/**
 * Template Part: Game Category Hero
 *
 * @package YourTheme
 *
 * @var WP_Term       $term             Current term object
 * @var string        $term_name        Term display name
 * @var string        $term_desc        Term description
 * @var int           $parent_term_id   Parent term ID (0 if top-level)
 * @var array         $ancestors        Array of ancestor term IDs
 * @var int           $game_count       Total game count
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Pull in variables passed from the taxonomy template
$term           = get_query_var( 'term' );
$term_name      = get_query_var( 'term_name' );
$term_desc      = get_query_var( 'term_desc' );
$parent_term_id = get_query_var( 'parent_term_id' );
$ancestors      = get_query_var( 'ancestors' );
$game_count     = get_query_var( 'game_count' );
?>

<section class="gc-hero">
  <div class="gc-orb gc-orb--1"></div>
  <div class="gc-orb gc-orb--2"></div>
  <div class="gc-hero__inner">

    <?php if ( ! empty( $ancestors ) ) : ?>
      <nav class="gc-bc">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'game' ) ); ?>">All Games</a>
        <?php foreach ( $ancestors as $aid ) :
          $at = get_term( $aid, 'game_category' ); ?>
          <span class="gc-bc__sep">›</span>
          <a href="<?php echo esc_url( get_term_link( $at ) ); ?>"><?php echo esc_html( $at->name ); ?></a>
        <?php endforeach; ?>
        <span class="gc-bc__sep">›</span>
        <span class="gc-bc__cur"><?php echo esc_html( $term_name ); ?></span>
      </nav>
    <?php endif; ?>

    <div class="gc-badge">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="6" y1="11" x2="10" y2="11" />
        <line x1="8" y1="9" x2="8" y2="13" />
        <line x1="15" y1="12" x2="15.01" y2="12" />
        <line x1="18" y1="10" x2="18.01" y2="10" />
        <path d="M17.32 5H6.68a4 4 0 0 0-3.978 3.59C2.604 9.416 2 14.456 2 16
               a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.414-1.414A2 2 0 0 1 9.828 16
               h4.344a2 2 0 0 1 1.414.586L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3
               c0-1.545-.604-6.584-.685-7.258A4 4 0 0 0 17.32 5z" />
      </svg>
      <?php echo esc_html( $term_name ); ?>
    </div>

    <h1>
      <?php if ( $parent_term_id === 0 ) : ?>
        Explore More<br><span><?php echo esc_html( $term_name ); ?> Games</span>
      <?php else :
        $pt = get_term( $parent_term_id, 'game_category' ); ?>
        <?php echo esc_html( $pt->name ); ?><br>
        <span><?php echo esc_html( $term_name ); ?></span>
      <?php endif; ?>
    </h1>

    <p class="gc-hero__desc">
      <?php echo $term_desc
        ? wp_kses_post( $term_desc )
        : 'Discover the best ' . esc_html( $term_name ) . ' games. Find your next gaming obsession.'; ?>
    </p>

  </div>
</section>
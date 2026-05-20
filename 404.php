<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="error-wrapper" id="error-404-wrapper">

  <div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

    <div class="error-row">

      <div class="col-md-12 content-area" id="primary">

        <main class="site-main" id="main">

          <section class="error-404 not-found text-center">

            <header class="page-header-404">

              <h1 class="page-title-404"><?php esc_html_e( 'Oops!', 'understrap' ); ?></h1>

            </header><!-- .page-header -->

            <div class="page-content">

              <h4 class="page-subtitle-404">404 - PAGE NOT FOUND</h4>

              <p class="page-description-404">
                <?php esc_html_e( 'The page you are looking for might have been removed had its name changed or is temporary unavailable.', 'understrap' ); ?>
              </p>

              <a class="btn btn-primary-gradient" href="/">
                <?php esc_html_e( 'Go back to homepage', 'understrap' ); ?>
              </a>


            </div><!-- .page-content -->

          </section><!-- .error-404 -->

        </main>

      </div><!-- #primary -->

    </div><!-- .row -->

  </div><!-- #content -->

</div><!-- #error-404-wrapper -->

<?php
get_footer();
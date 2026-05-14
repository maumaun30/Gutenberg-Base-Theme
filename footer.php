<?php
/**
 * footer.php
 *
 * Dynamic footer powered by ACF Options Page.
 *
 * ACF field keys used (all from options page):
 *   fnlmx_footer_description       — text
 *   fnlmx_footer_solaire           — image (array)
 *   fnlmx_responsible_gaming       — repeater → fnlmx_responsible_gaming_image (image array)
 *   fnlmx_footer_payments          — repeater → fnlmx_payment_method_icon      (image array)
 *   fnlmx_footer_social            — repeater → fnlmx_social_media_icon         (image array)
 *                                             → fnlmx_social_media_url          (url)
 *
 * Nav menus registered: footer-links | footer-legal
 */

/* ── ACF options helper ────────────────────────────────────────────── */
function fnlmx_opt( $key ) {
    return function_exists( 'get_field' ) ? get_field( $key, 'option' ) : null;
}

$footer_desc     = fnlmx_opt( 'fnlmx_footer_description' );
$solaire_img     = fnlmx_opt( 'fnlmx_footer_solaire' );
$rg_rows         = fnlmx_opt( 'fnlmx_responsible_gaming' );
$payment_rows    = fnlmx_opt( 'fnlmx_footer_payments' );
$social_rows     = fnlmx_opt( 'fnlmx_footer_social' );

/* Image url helper */
function fnlmx_img_url( $img, $size = 'full' ) {
    if ( ! $img ) return '';
    if ( is_array( $img ) ) return $img['sizes'][ $size ] ?? $img['url'] ?? '';
    return $img;
}
function fnlmx_img_alt( $img, $fallback = '' ) {
    if ( is_array( $img ) ) return $img['alt'] ?? $fallback;
    return $fallback;
}
?>

<footer class="fnlmx-footer">
  <div class="fnlmx-footer__container">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- ROW 1: Brand left  |  Solaire + RG badges right           -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="fnlmx-footer__top-row">

      <!-- Brand: logo + description -->
      <div class="fnlmx-footer__brand">
        <?php if ( has_custom_logo() ) :
          the_custom_logo();
        else : ?>
          <a href="<?php echo esc_url( home_url('/') ); ?>" class="fnlmx-footer__site-name">
            <?php bloginfo('name'); ?>
          </a>
        <?php endif; ?>

        <?php if ( $footer_desc ) : ?>
          <p class="fnlmx-footer__desc"><?php echo wp_kses_post( $footer_desc ); ?></p>
        <?php endif; ?>
      </div>

      <!-- Right side: Solaire pill + RG badges pill (two separate containers) -->
      <div class="fnlmx-footer__top-right">

        <!-- Solaire — standalone wide pill -->
        <?php if ( $solaire_img ) :
          $sol_url = fnlmx_img_url( $solaire_img, 'medium' );
          $sol_alt = fnlmx_img_alt( $solaire_img, 'A Product of Solaire' );
        ?>
          <div class="fnlmx-footer__solaire">
            <img src="<?php echo esc_url( $sol_url ); ?>"
                 alt="<?php echo esc_attr( $sol_alt ); ?>"
                 loading="lazy">
          </div>
        <?php endif; ?>

        <!-- RG badges — grouped together in their own pill -->
        <?php if ( ! empty( $rg_rows ) ) : ?>
          <div class="fnlmx-footer__rg-group">
            <?php foreach ( $rg_rows as $row ) :
              $img     = $row['fnlmx_responsible_gaming_image'] ?? null;
              $img_url = fnlmx_img_url( $img, 'medium' );
              $img_alt = fnlmx_img_alt( $img, 'Responsible Gaming' );
              if ( ! $img_url ) continue;
            ?>
              <div class="fnlmx-rg-badge">
                <img src="<?php echo esc_url( $img_url ); ?>"
                     alt="<?php echo esc_attr( $img_alt ); ?>"
                     loading="lazy">
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
    </div><!-- /top-row -->

    <div class="fnlmx-footer__divider"></div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- ROW 2: Support | Legal | Payments + Social                 -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="fnlmx-footer__bottom-row">
      <div class="fnlmx-footer__navigation">
      <!-- Support nav -->
      <nav class="fnlmx-footer__nav-col">
        <h4 class="fnlmx-footer__nav-heading">
          <?php esc_html_e( 'Support', 'luxe' ); ?>
        </h4>
        <ul class="fnlmx-footer__nav-list">
          <?php wp_nav_menu([
            'theme_location' => 'footer-links',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'walker'         => new Luxe_Footer_Walker(),
            'fallback_cb'    => 'luxe_footer_links_fallback',
          ]); ?>
        </ul>
      </nav>

      <!-- Legal nav -->
      <nav class="fnlmx-footer__nav-col">
        <h4 class="fnlmx-footer__nav-heading">
          <?php esc_html_e( 'Legal', 'luxe' ); ?>
        </h4>
        <ul class="fnlmx-footer__nav-list">
          <?php wp_nav_menu([
            'theme_location' => 'footer-legal',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'walker'         => new Luxe_Footer_Walker(),
            'fallback_cb'    => 'luxe_footer_legal_fallback',
          ]); ?>
        </ul>
      </nav>
      </div>

      <!-- Payments + Social stacked -->
      <div class="fnlmx-footer__trust-col">

        <?php if ( ! empty( $payment_rows ) ) : ?>
          <div class="fnlmx-trust-block">
            <h4 class="fnlmx-footer__nav-heading">
              <?php esc_html_e( 'Secure Payments', 'luxe' ); ?>
            </h4>
            <div class="fnlmx-payment-list">
              <?php foreach ( $payment_rows as $row ) :
                $icon     = $row['fnlmx_payment_method_icon'] ?? null;
                $icon_url = fnlmx_img_url( $icon, 'medium' );
                $icon_alt = fnlmx_img_alt( $icon, 'Payment method' );
                if ( ! $icon_url ) continue;
              ?>
                <div class="fnlmx-payment-badge">
                  <img src="<?php echo esc_url( $icon_url ); ?>"
                       alt="<?php echo esc_attr( $icon_alt ); ?>"
                       loading="lazy">
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if ( ! empty( $social_rows ) ) : ?>
          <div class="fnlmx-trust-block">
            <h4 class="fnlmx-footer__nav-heading">
              <?php esc_html_e( 'Online Support & Communities', 'luxe' ); ?>
            </h4>
            <div class="fnlmx-social-list">
              <?php foreach ( $social_rows as $row ) :
                $icon      = $row['fnlmx_social_media_icon'] ?? null;
                $icon_url  = fnlmx_img_url( $icon, 'thumbnail' );
                $icon_alt  = fnlmx_img_alt( $icon, 'Social media' );
                $icon_link = $row['fnlmx_social_media_url'] ?? '#';
                if ( ! $icon_url ) continue;
              ?>
                <a href="<?php echo esc_url( $icon_link ); ?>"
                   class="fnlmx-social-icon"
                   target="_blank" rel="noopener noreferrer"
                   aria-label="<?php echo esc_attr( $icon_alt ); ?>">
                  <img src="<?php echo esc_url( $icon_url ); ?>"
                       alt="<?php echo esc_attr( $icon_alt ); ?>"
                       loading="lazy">
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

      </div><!-- /trust-col -->

    </div><!-- /bottom-row -->

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- COPYRIGHT                                                   -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="fnlmx-footer__divider"></div>
    <div class="fnlmx-footer__copyright">
      <p class="fnlmx-footer__copy">
        Copyright &copy; <?php echo esc_html( date('Y') ); ?>
        <?php bloginfo('name'); ?><br>
        <?php esc_html_e( 'All Rights Reserved.', 'luxe' ); ?>
      </p>
    </div>

  </div><!-- /container -->
</footer>

<?php wp_footer(); ?>
</body>
</html>
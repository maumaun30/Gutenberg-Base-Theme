<?php
/**
 * footer.php
 *
 * Dynamic footer powered by ACF Options Page.
 *
 * ACF field keys used (all from options page):
 *   fnlmx_footer_description       — text
 *   fnlmx_solaire_footer           — image (array)
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
$solaire_img     = fnlmx_opt( 'fnlmx_solaire_footer' );
$rg_rows         = fnlmx_opt( 'fnlmx_responsible_gaming' );    // repeater
$payment_rows    = fnlmx_opt( 'fnlmx_footer_payments' );       // repeater
$social_rows     = fnlmx_opt( 'fnlmx_footer_social' );         // repeater

/* Image url helper — handles both array (ACF) and plain string */
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

  <!-- ═══════════════════════ MAIN FOOTER ═══════════════════════ -->
  <div class="fnlmx-footer__main">
    <div class="fnlmx-footer__container fnlmx-footer__main-inner">

      <!-- ── LEFT: Brand column ──────────────────────────────── -->
      <div class="fnlmx-footer__brand">

        <!-- Site logo -->
        <?php if ( has_custom_logo() ) :
          the_custom_logo();
        else : ?>
          <a href="<?php echo esc_url( home_url('/') ); ?>" class="fnlmx-footer__site-name">
            <?php bloginfo('name'); ?>
          </a>
        <?php endif; ?>

        <!-- ACF description -->
        <?php if ( $footer_desc ) : ?>
          <p class="fnlmx-footer__desc"><?php echo wp_kses_post( $footer_desc ); ?></p>
        <?php endif; ?>

        <!-- Solaire partner image -->
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

        <!-- Responsible Gaming badges -->
        <?php if ( ! empty( $rg_rows ) ) : ?>
          <div class="fnlmx-footer__rg-inline">
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

      <!-- ── MIDDLE: Nav columns ────────────────────────────── -->
      <div class="fnlmx-footer__nav-group">

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

      <!-- ── RIGHT: Payments + Social ──────────────────────── -->
      <div class="fnlmx-footer__trust-col">

        <!-- Secure Payments -->
        <?php if ( ! empty( $payment_rows ) ) : ?>
          <div class="fnlmx-trust-block">
            <span class="fnlmx-trust-block__label">
              <?php esc_html_e( 'Secure Payments', 'luxe' ); ?>
            </span>
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

        <!-- Online Support & Communities (Social) -->
        <?php if ( ! empty( $social_rows ) ) : ?>
          <div class="fnlmx-trust-block">
            <span class="fnlmx-trust-block__label">
              <?php esc_html_e( 'Online Support & Communities', 'luxe' ); ?>
            </span>
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

      </div>

    </div>
  </div><!-- /main footer -->


  <!-- ═══════════════════════ BOTTOM BAR ════════════════════════ -->
  <div class="fnlmx-footer__bottom">
    <div class="fnlmx-footer__container fnlmx-footer__bottom-inner">
      <p class="fnlmx-footer__copy">
        Copyright &copy; <?php echo esc_html( date('Y') ); ?>
        <?php bloginfo('name'); ?><br>
        <?php esc_html_e( 'All Rights Reserved.', 'luxe' ); ?>
      </p>
    </div>
  </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
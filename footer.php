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
 *
 * Nav menus registered: footer-links | footer-help | footer-legal
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
    <div class="fnlmx-footer__container">

      <!-- ── Logo + description column ──────────────────────── -->
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

      </div>

      <!-- ── Nav columns ────────────────────────────────────── -->
      <nav class="fnlmx-footer__nav-col">
        <h4 class="fnlmx-footer__nav-heading">
          <?php esc_html_e( 'Quick Links', 'luxe' ); ?>
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
          <?php esc_html_e( 'Help & Support', 'luxe' ); ?>
        </h4>
        <ul class="fnlmx-footer__nav-list">
          <?php wp_nav_menu([
            'theme_location' => 'footer-help',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'walker'         => new Luxe_Footer_Walker(),
            'fallback_cb'    => 'luxe_footer_help_fallback',
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
  </div><!-- /main footer -->


  <!-- ═══════════════════════ TRUST STRIP ═══════════════════════ -->
  <div class="fnlmx-footer__trust">
    <div class="fnlmx-footer__container fnlmx-footer__trust-inner">

      <!-- Payment Methods -->
      <?php if ( ! empty( $payment_rows ) ) : ?>
        <div class="fnlmx-trust-block">
          <span class="fnlmx-trust-block__label">
            <?php esc_html_e( 'Payment Methods', 'luxe' ); ?>
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

      <!-- Responsible Gaming -->
      <?php if ( ! empty( $rg_rows ) ) : ?>
        <div class="fnlmx-trust-block">
          <span class="fnlmx-trust-block__label">
            <?php esc_html_e( 'Responsible Gambling', 'luxe' ); ?>
          </span>
          <div class="fnlmx-rg-list">
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
        </div>
      <?php endif; ?>

      <!-- Follow Us / Social -->
      <?php if ( ! empty( $social_rows ) ) : ?>
        <div class="fnlmx-trust-block">
          <span class="fnlmx-trust-block__label">
            <?php esc_html_e( 'Follow Us', 'luxe' ); ?>
          </span>
          <div class="fnlmx-social-list">
            <?php foreach ( $social_rows as $row ) :
              $icon     = $row['fnlmx_social_media_icon'] ?? null;
              $icon_url = fnlmx_img_url( $icon, 'thumbnail' );
              $icon_alt = fnlmx_img_alt( $icon, 'Social media' );
              /*
               * Optional: if your ACF repeater also has a URL sub-field,
               * swap the <div> below for an <a href="...">.
               * Add: fnlmx_social_media_url (URL sub-field) to the repeater.
               */
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
  </div><!-- /trust strip -->


  <!-- ═══════════════════════ BOTTOM BAR ════════════════════════ -->
  <div class="fnlmx-footer__bottom">
    <div class="fnlmx-footer__container fnlmx-footer__bottom-inner">
      <p class="fnlmx-footer__copy">
        &copy; <?php echo esc_html( date('Y') ); ?>
        <?php bloginfo('name'); ?> &ndash;
        <?php esc_html_e( 'All rights reserved', 'luxe' ); ?>
      </p>
    </div>
  </div>

</footer>

<!-- ═══════════════════════ FOOTER STYLES ══════════════════════ -->
<style>
/* ---- Root (safe fallback if main.css not loaded) --------------- */
:root {
  --color-primary:  #f71dc2;
  --color-secondary:#d63d4a;
  --bg-dark-1:      #0a0a0b;
  --bg-dark-2:      #111114;
  --bg-dark-3:      #18181c;
  --bg-dark-4:      #1f1f25;
  --border:         rgba(255,255,255,.08);
  --border-strong:  rgba(255,255,255,.14);
  --shadow-glow:    0 0 24px rgba(247,29,194,.3);
}

/* ---- Footer shell --------------------------------------------- */
.fnlmx-footer {
  background-color: var(--bg-dark-1);
  color: #fff;
  font-family: 'Lexend', sans-serif;
}

/* ---- Shared container ----------------------------------------- */
.fnlmx-footer__container {
  max-width: 80rem;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* ================================================================ */
/*  MAIN FOOTER (logo + nav columns)                                */
/* ================================================================ */
.fnlmx-footer__main {
  padding: 4rem 0 3rem;
  border-bottom: 1px solid var(--border);
}
.fnlmx-footer__main .fnlmx-footer__container {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2.5rem;
}
@media (min-width: 640px) {
  .fnlmx-footer__main .fnlmx-footer__container {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (min-width: 1024px) {
  .fnlmx-footer__main .fnlmx-footer__container {
    grid-template-columns: 1.6fr 1fr 1fr 1fr;
    gap: 3rem;
  }
}

/* Brand column */
.fnlmx-footer__brand {}

.fnlmx-footer__brand .custom-logo-link img,
.fnlmx-footer__brand .custom-logo-link {
  display: block;
  max-width: 160px;
  margin-bottom: 1.25rem;
}
.fnlmx-footer__site-name {
  display: inline-block;
  font-family: 'Bebas Neue', sans-serif;
  font-size: 1.75rem;
  letter-spacing: .06em;
  color: #fff;
  text-decoration: none;
  margin-bottom: 1.25rem;
}

.fnlmx-footer__desc {
  font-size: .875rem;
  line-height: 1.75;
  color: rgba(255,255,255,.55);
  margin-bottom: 1.5rem;
  max-width: 28rem;
}

/* Solaire image */
.fnlmx-footer__solaire {
  display: inline-block;
  padding: .625rem 1.25rem;
  background: rgba(255,255,255,.04);
  border: 1px solid var(--border-strong);
  border-radius: .625rem;
}
.fnlmx-footer__solaire img {
  max-width: 200px;
  height: auto;
  display: block;
  filter: brightness(.9);
  transition: filter .25s;
}
.fnlmx-footer__solaire:hover img { filter: brightness(1.05); }

/* Nav columns */
.fnlmx-footer__nav-heading {
  font-family: 'Outfit', sans-serif;
  font-size: .7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .14em;
  color: var(--color-primary);
  margin: 0 0 1.25rem;
}
.fnlmx-footer__nav-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: .625rem;
}
/* Walker outputs <li> with <a> inside */
.fnlmx-footer__nav-list li { margin: 0; }
.fnlmx-footer__nav-list li a {
  font-size: .875rem;
  color: rgba(255,255,255,.55);
  text-decoration: none;
  transition: color .2s, padding-left .2s;
  display: inline-flex;
  align-items: center;
  gap: .375rem;
}
.fnlmx-footer__nav-list li a::before {
  content: '';
  display: inline-block;
  width: .3rem;
  height: .3rem;
  border-radius: 50%;
  background: var(--color-primary);
  opacity: 0;
  transition: opacity .2s;
  flex-shrink: 0;
}
.fnlmx-footer__nav-list li a:hover {
  color: #fff;
  padding-left: .25rem;
}
.fnlmx-footer__nav-list li a:hover::before { opacity: 1; }

/* ================================================================ */
/*  TRUST STRIP (payments / responsible gaming / social)            */
/* ================================================================ */
.fnlmx-footer__trust {
  background: var(--bg-dark-2);
  border-bottom: 1px solid var(--border);
  padding: 2.5rem 0;
}
.fnlmx-footer__trust-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2.5rem;
}
@media (min-width: 768px) {
  .fnlmx-footer__trust-inner {
    flex-direction: row;
    justify-content: center;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 3rem 4rem;
  }
}

/* Individual trust block */
.fnlmx-trust-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}
.fnlmx-trust-block__label {
  font-family: 'Outfit', sans-serif;
  font-size: .7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .14em;
  color: rgba(255,255,255,.4);
}

/* ── Payment badges ──────────────────────────────────────────── */
.fnlmx-payment-list {
  display: flex;
  flex-wrap: wrap;
  gap: .625rem;
  justify-content: center;
}
.fnlmx-payment-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: .5rem 1rem;
  background: rgba(255,255,255,.05);
  border: 1px solid var(--border-strong);
  border-radius: .625rem;
  transition: border-color .22s, background .22s, transform .22s;
  min-width: 88px;
  height: 48px;
}
.fnlmx-payment-badge:hover {
  border-color: rgba(255,255,255,.3);
  background: rgba(255,255,255,.09);
  transform: translateY(-2px);
}
.fnlmx-payment-badge img {
  max-height: 28px;
  max-width: 100px;
  width: auto;
  display: block;
  object-fit: contain;
  filter: grayscale(15%);
  transition: filter .22s;
}
.fnlmx-payment-badge:hover img { filter: grayscale(0%); }

/* ── Responsible Gaming badges ───────────────────────────────── */
.fnlmx-rg-list {
  display: flex;
  flex-wrap: wrap;
  gap: .75rem;
  justify-content: center;
}
.fnlmx-rg-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: .5rem 1.125rem;
  background: rgba(255,255,255,.04);
  border: 1px solid var(--border);
  border-radius: .625rem;
  transition: border-color .22s, transform .22s;
}
.fnlmx-rg-badge:hover {
  border-color: rgba(255,255,255,.2);
  transform: translateY(-2px);
}
.fnlmx-rg-badge img {
  max-height: 40px;
  max-width: 160px;
  width: auto;
  display: block;
  object-fit: contain;
}

/* ── Social icons ────────────────────────────────────────────── */
.fnlmx-social-list {
  display: flex;
  gap: .75rem;
  flex-wrap: wrap;
  justify-content: center;
}
.fnlmx-social-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 50%;
  background: rgba(255,255,255,.06);
  border: 1px solid var(--border-strong);
  transition: all .25s;
  overflow: hidden;
  flex-shrink: 0;
}
.fnlmx-social-icon:hover {
  background: var(--color-primary);
  border-color: var(--color-primary);
  box-shadow: var(--shadow-glow);
  transform: translateY(-3px) scale(1.08);
}
.fnlmx-social-icon img {
  width: 1.25rem;
  height: 1.25rem;
  object-fit: contain;
  display: block;
  /* Keep icons white-ish on dark background */
  filter: brightness(0) invert(1);
  opacity: .75;
  transition: opacity .22s, filter .22s;
}
.fnlmx-social-icon:hover img {
  filter: brightness(0) invert(1);
  opacity: 1;
}

/* ================================================================ */
/*  BOTTOM BAR                                                      */
/* ================================================================ */
.fnlmx-footer__bottom {
  background: var(--bg-dark-1);
  padding: 1.25rem 0;
}
.fnlmx-footer__bottom-inner {
  display: flex;
  align-items: center;
  justify-content: center;
}
.fnlmx-footer__copy {
  font-family: 'Outfit', sans-serif;
  font-size: .8rem;
  color: rgba(255,255,255,.35);
  margin: 0;
  text-align: center;
  letter-spacing: .03em;
}
</style>

<?php wp_footer(); ?>
</body>
</html>
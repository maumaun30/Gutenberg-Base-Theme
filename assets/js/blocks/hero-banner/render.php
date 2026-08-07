<?php
$image_url          = $attributes['imageUrl'] ?? '';
$image_id           = $attributes['imageId'] ?? 0;
$image_alt          = $attributes['imageAlt'] ?? '';
$title              = $attributes['title'] ?? '';
$title_highlight    = $attributes['titleHighlight'] ?? '';
$subtitle           = $attributes['subtitle'] ?? '';
$features           = $attributes['features'] ?? [];
$download_buttons   = $attributes['downloadButtons'] ?? [];
$primary_btn_text   = $attributes['primaryButtonText'] ?? '';
$show_primary_btn   = $attributes['showPrimaryButton'] ?? false;
$secondary_btn_text = $attributes['secondaryButtonText'] ?? '';
$secondary_btn_url  = $attributes['secondaryButtonUrl'] ?? '';
$is_page_title      = $attributes['isPageTitle'] ?? false;
$overlay_opacity    = $attributes['overlayOpacity'] ?? 85;

// SEO: this banner is only an <h1> when explicitly marked as the page title,
// so a page carrying both a carousel and a banner never has two H1s.
$title_tag = $is_page_title ? 'h1' : 'h2';

// Secondary button: fall back to scrolling to the games listing section when
// no URL (or just the placeholder "#") is set in the block settings.
if ( '' === trim( (string) $secondary_btn_url ) || '#' === $secondary_btn_url ) {
    $secondary_btn_url = '#games-listing';
}

$overlay_full  = round( $overlay_opacity / 100, 2 );
$overlay_light = round( $overlay_full * 0.6, 2 );

$banner_uid = uniqid();

// Reuses the carousel slide's button shape when that block is on the page;
// otherwise defines an identical helper here.
if ( ! function_exists( 'mytheme_hero_banner_btn_shape' ) ) {
    function mytheme_hero_banner_btn_shape( $uid ) { ?>
      <svg aria-hidden="true" class="mytheme-hero-banner__btn-shape" viewBox="0 0 148 42" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g clip-path="url(#mytheme-hero-banner-btn-<?php echo esc_attr( $uid ); ?>)">
          <path d="M148 30.4 L136.4 42 H0 V7 L7 0 H148 V30.4 Z" fill="currentColor"></path>
          <path d="M148 34 V42 H140 L148 34 Z" fill="var(--decoration, currentColor)"></path>
        </g>
        <defs>
          <clipPath id="mytheme-hero-banner-btn-<?php echo esc_attr( $uid ); ?>">
            <rect width="148" height="42" fill="white"></rect>
          </clipPath>
        </defs>
      </svg>
    <?php }
}
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => 'mytheme-hero-banner' ] ); ?>>

  <div
    class="mytheme-hero-banner__overlay"
    style="background: linear-gradient(to right, rgba(10,10,11,<?php echo $overlay_full; ?>), rgba(10,10,11,<?php echo $overlay_light; ?>));"
    aria-hidden="true"
  ></div>

  <div class="mytheme-hero-banner__content">

    <div class="mytheme-hero-banner__inner">

      <?php if ( $title || $title_highlight ) : ?>
        <<?php echo $title_tag; ?> class="mytheme-hero-banner__title">
          <?php if ( $title ) : ?>
            <span><?php echo wp_kses_post( $title ); ?></span>
          <?php endif; ?>

          <?php if ( $title_highlight ) : ?>
            <span class="highlight-text"><?php echo wp_kses_post( $title_highlight ); ?></span>
          <?php endif; ?>
        </<?php echo $title_tag; ?>>
      <?php endif; ?>

      <?php if ( $subtitle ) : ?>
        <p class="mytheme-hero-banner__subtitle">
          <?php echo wp_kses_post( $subtitle ); ?>
        </p>
      <?php endif; ?>

      <?php if ( ( $show_primary_btn && $primary_btn_text ) || $secondary_btn_text ) : ?>
        <div class="mytheme-hero-banner__buttons">
          <?php if ( $show_primary_btn && $primary_btn_text ) : ?>
            <button
              type="button"
              class="mytheme-hero-banner__btn mytheme-hero-banner__btn--primary"
              id="fnlmx-rg-proceed"
            >
              <?php mytheme_hero_banner_btn_shape( 'primary-' . $banner_uid ); ?>
              <span class="mytheme-hero-banner__btn-label"><?php echo esc_html( $primary_btn_text ); ?></span>
            </button>
          <?php endif; ?>

          <?php if ( $secondary_btn_text ) : ?>
            <a
              href="<?php echo esc_url( $secondary_btn_url ); ?>"
              class="mytheme-hero-banner__btn mytheme-hero-banner__btn--secondary"
            >
              <?php mytheme_hero_banner_btn_shape( 'secondary-' . $banner_uid ); ?>
              <span class="mytheme-hero-banner__btn-label"><?php echo esc_html( $secondary_btn_text ); ?></span>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php
      $features = array_filter( (array) $features, static function ( $item ) {
          return '' !== trim( (string) $item );
      } );
      ?>
      <?php if ( $features ) : ?>
        <ul class="mytheme-hero-banner__features">
          <?php foreach ( $features as $feature ) : ?>
            <li class="mytheme-hero-banner__feature">
              <span class="mytheme-hero-banner__check" aria-hidden="true">&#10003;</span>
              <span><?php echo esc_html( $feature ); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php if ( ! empty( $download_buttons ) ) : ?>
        <div class="mytheme-hero-banner__download-buttons">
          <?php foreach ( $download_buttons as $button ) : ?>
            <?php
            $btn_icon   = $button['iconUrl'] ?? '';
            $btn_alt    = $button['iconAlt'] ?? '';
            $btn_small  = $button['smallText'] ?? '';
            $btn_main   = $button['mainText'] ?? '';
            $btn_action = $button['action'] ?? 'link';
            $new_tab    = ! empty( $button['newTab'] );

            // A download button points at the uploaded file and carries the
            // `download` attribute so browsers save it instead of navigating;
            // a link button just goes to its URL.
            if ( 'file' === $btn_action ) {
                $href      = $button['fileUrl'] ?? '';
                $file_name = $button['fileName'] ?? '';
                $extra     = ' download' . ( $file_name ? '="' . esc_attr( $file_name ) . '"' : '' );
            } else {
                $href  = $button['url'] ?? '';
                $extra = $new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
            }

            // Skip buttons that have nothing to point at yet.
            if ( '' === trim( (string) $href ) ) {
                continue;
            }
            ?>

            <a class="mytheme-hero-banner__download-btn" href="<?php echo esc_url( $href ); ?>"<?php echo $extra; ?>>

              <?php if ( $btn_icon ) : ?>
                <span class="mytheme-hero-banner__download-btn-icon">
                  <img src="<?php echo esc_url( $btn_icon ); ?>" alt="<?php echo esc_attr( $btn_alt ); ?>" loading="lazy" />
                </span>
              <?php endif; ?>

              <span class="mytheme-hero-banner__download-btn-text">
                <?php if ( '' !== trim( (string) $btn_small ) ) : ?>
                  <span class="mytheme-hero-banner__download-btn-small"><?php echo esc_html( $btn_small ); ?></span>
                <?php endif; ?>

                <?php if ( '' !== trim( (string) $btn_main ) ) : ?>
                  <span class="mytheme-hero-banner__download-btn-main"><?php echo esc_html( $btn_main ); ?></span>
                <?php endif; ?>
              </span>

            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>

    <?php if ( $image_url ) : ?>
      <div class="mytheme-hero-banner__media">
        <?php if ( $image_id ) : ?>
          <?php
          echo wp_get_attachment_image(
              $image_id,
              'full',
              false,
              [
                  'class'   => 'mytheme-hero-banner__image',
                  'alt'     => $image_alt,
                  'loading' => 'lazy',
              ]
          );
          ?>
        <?php else : ?>
          <img
            class="mytheme-hero-banner__image"
            src="<?php echo esc_url( $image_url ); ?>"
            alt="<?php echo esc_attr( $image_alt ); ?>"
            loading="lazy"
          />
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>

</section>

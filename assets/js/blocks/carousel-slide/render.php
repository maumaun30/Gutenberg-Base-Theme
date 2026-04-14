<?php
$image_url            = $attributes['imageUrl'] ?? '';
$image_alt            = $attributes['imageAlt'] ?? '';
$title                = $attributes['title'] ?? '';
$subtitle             = $attributes['subtitle'] ?? '';
$primary_btn_text     = $attributes['primaryButtonText'] ?? '';
$primary_btn_url      = $attributes['primaryButtonUrl'] ?? '#';
$secondary_btn_text   = $attributes['secondaryButtonText'] ?? '';
$secondary_btn_url    = $attributes['secondaryButtonUrl'] ?? '#';
$overlay_opacity      = $attributes['overlayOpacity'] ?? 85;

$overlay_full  = round( $overlay_opacity / 100, 2 );
$overlay_light = round( $overlay_full * 0.6, 2 );
?>

<div <?php echo get_block_wrapper_attributes(['class' => 'swiper-slide mytheme-carousel-slide']); ?>>

  <?php if ( $image_url ) : ?>
    <div
      class="mytheme-carousel-slide__bg"
      style="background-image: url('<?php echo esc_url( $image_url ); ?>');"
      role="img"
      aria-label="<?php echo esc_attr( $image_alt ); ?>"
    ></div>
  <?php endif; ?>

  <div
    class="mytheme-carousel-slide__overlay"
    style="background: linear-gradient(to right, rgba(10,10,11,<?php echo $overlay_full; ?>), rgba(10,10,11,<?php echo $overlay_light; ?>));"
    aria-hidden="true"
  ></div>

  <div class="mytheme-carousel-slide__content">
    <div class="mytheme-carousel-slide__inner">

      <?php if ( $title ) : ?>
        <h1 class="mytheme-carousel-slide__title">
          <?php echo wp_kses_post( $title ); ?>
        </h1>
      <?php endif; ?>

      <?php if ( $subtitle ) : ?>
        <p class="mytheme-carousel-slide__subtitle">
          <?php echo wp_kses_post( $subtitle ); ?>
        </p>
      <?php endif; ?>

      <?php if ( $primary_btn_text || $secondary_btn_text ) : ?>
        <div class="mytheme-carousel-slide__buttons">
          <?php if ( $primary_btn_text ) : ?>
            <a
              href="<?php echo esc_url( $primary_btn_url ); ?>"
              class="mytheme-carousel-slide__btn mytheme-carousel-slide__btn--primary"
            >
              <?php echo esc_html( $primary_btn_text ); ?>
            </a>
          <?php endif; ?>

          <?php if ( $secondary_btn_text ) : ?>
            <a
              href="<?php echo esc_url( $secondary_btn_url ); ?>"
              class="mytheme-carousel-slide__btn mytheme-carousel-slide__btn--secondary"
            >
              <?php echo esc_html( $secondary_btn_text ); ?>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>

</div>
<?php
$title           = $attributes['title'] ?? '';
$title_highlight = $attributes['titleHighlight'] ?? '';
$subtitle        = trim( (string) ( $attributes['subtitle'] ?? '' ) );
$is_page_title   = $attributes['isPageTitle'] ?? false;
$slides          = $attributes['slides'] ?? [];
$autoplay        = $attributes['autoplay'] ?? true;
$autoplay_delay  = (int) ( $attributes['autoplayDelay'] ?? 3000 );
$speed           = (int) ( $attributes['speed'] ?? 700 );
$continuous      = $attributes['continuous'] ?? true;
$centered        = $attributes['centeredSlides'] ?? true;
$show_pagination = $attributes['showPagination'] ?? true;

// SEO: this section is only an <h1> when explicitly marked as the page title.
$title_tag = $is_page_title ? 'h1' : 'h2';

// Unique id so several sliders on one page don't share a pagination element.
$slider_uid = 'portrait-slider-' . uniqid();

// Swiper reads its setup from this attribute (see portrait-slider.js).
$config = wp_json_encode(
    [
        'autoplay'   => (bool) $autoplay,
        'delay'      => max( 500, $autoplay_delay ),
        'speed'      => max( 100, $speed ),
        'continuous' => (bool) $continuous,
        'centered'   => (bool) $centered,
        'pagination' => (bool) $show_pagination,
    ]
);
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => 'mytheme-portrait-slider' ] ); ?>>

  <div class="mytheme-portrait-slider__container">

    <?php if ( $title || $title_highlight || '' !== $subtitle ) : ?>
      <header class="mytheme-portrait-slider__header">

        <?php if ( $title || $title_highlight ) : ?>
          <<?php echo $title_tag; ?> class="mytheme-portrait-slider__title">
            <?php if ( $title ) : ?>
              <span><?php echo wp_kses_post( $title ); ?></span>
            <?php endif; ?>

            <?php if ( $title_highlight ) : ?>
              <span class="highlight-text"><?php echo wp_kses_post( $title_highlight ); ?></span>
            <?php endif; ?>
          </<?php echo $title_tag; ?>>
        <?php endif; ?>

        <?php // Optional field: nothing is output at all when it is left empty. ?>
        <?php if ( '' !== $subtitle ) : ?>
          <p class="mytheme-portrait-slider__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
        <?php endif; ?>

      </header>
    <?php endif; ?>

  </div>

  <?php if ( ! empty( $slides ) ) : ?>
    <div
      class="swiper mytheme-portrait-slider__swiper"
      id="<?php echo esc_attr( $slider_uid ); ?>"
      data-portrait-slider="<?php echo esc_attr( $config ); ?>"
    >
      <div class="swiper-wrapper">

        <?php foreach ( $slides as $slide ) : ?>
          <?php
          $image_url = $slide['imageUrl'] ?? '';
          $image_id  = (int) ( $slide['imageId'] ?? 0 );
          $image_alt = $slide['imageAlt'] ?? '';
          $link_url  = trim( (string) ( $slide['linkUrl'] ?? '' ) );

          if ( '' === $image_url ) {
              continue;
          }

          if ( $image_id ) {
              $img_html = wp_get_attachment_image(
                  $image_id,
                  'large',
                  false,
                  [
                      'class'   => 'mytheme-portrait-slider__image',
                      'alt'     => $image_alt,
                      'loading' => 'lazy',
                  ]
              );
          } else {
              $img_html = '<img class="mytheme-portrait-slider__image" src="' . esc_url( $image_url ) .
                  '" alt="' . esc_attr( $image_alt ) . '" loading="lazy" />';
          }
          ?>

          <div class="swiper-slide mytheme-portrait-slider__slide">
            <?php if ( '' !== $link_url ) : ?>
              <a href="<?php echo esc_url( $link_url ); ?>" class="mytheme-portrait-slider__link">
                <?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
              </a>
            <?php else : ?>
              <?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>

      </div>
    </div>

    <?php
    // Kept outside the .swiper element on purpose: as a direct child it would
    // sit inside a box that clips its overflow. The dots are built and driven
    // by portrait-slider.js rather than by Swiper's pagination module, so it
    // deliberately carries none of Swiper's own classes.
    ?>
    <?php if ( $show_pagination ) : ?>
      <div
        class="mytheme-portrait-slider__pagination"
        id="<?php echo esc_attr( $slider_uid ); ?>-pagination"
      ></div>
    <?php endif; ?>
  <?php endif; ?>

</section>

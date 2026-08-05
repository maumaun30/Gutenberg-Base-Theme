<?php
$title           = $attributes['title'] ?? '';
$title_highlight = $attributes['titleHighlight'] ?? '';
$subtitle        = trim( (string) ( $attributes['subtitle'] ?? '' ) );
$is_page_title   = $attributes['isPageTitle'] ?? false;
$items           = $attributes['items'] ?? [];
$speed           = max( 1, (int) ( $attributes['speed'] ?? 5 ) );
$direction       = ( 'right' === ( $attributes['direction'] ?? 'left' ) ) ? 'right' : 'left';
$pause_on_hover  = $attributes['pauseOnHover'] ?? true;

// SEO: this section is only an <h1> when explicitly marked as the page title.
$title_tag = $is_page_title ? 'h1' : 'h2';

$classes = 'mytheme-icon-slider mytheme-icon-slider--' . $direction;
if ( $pause_on_hover ) {
    $classes .= ' mytheme-icon-slider--pause-hover';
}
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => $classes ] ); ?>>
  <div class="mytheme-icon-slider__container">

    <?php if ( $title || $title_highlight || '' !== $subtitle ) : ?>
      <header class="mytheme-icon-slider__header">

        <?php if ( $title || $title_highlight ) : ?>
          <<?php echo $title_tag; ?> class="mytheme-icon-slider__title">
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
          <p class="mytheme-icon-slider__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
        <?php endif; ?>

      </header>
    <?php endif; ?>

  </div>

  <?php if ( ! empty( $items ) ) : ?>
    <?php
    // Build the pill markup once, then output the track twice so the CSS
    // marquee can loop seamlessly (the duplicate is hidden from AT).
    ob_start();
    foreach ( $items as $item ) :
        $icon_url = $item['iconUrl'] ?? '';
        $icon_id  = (int) ( $item['iconId'] ?? 0 );
        $icon_alt = $item['iconAlt'] ?? '';
        $label    = $item['label'] ?? '';

        if ( '' === trim( (string) $label ) && '' === $icon_url ) {
            continue;
        }
        ?>
        <span class="mytheme-icon-slider__pill">

          <?php if ( $icon_url ) : ?>
            <span class="mytheme-icon-slider__icon">
              <?php if ( $icon_id ) : ?>
                <?php
                echo wp_get_attachment_image(
                    $icon_id,
                    'full',
                    false,
                    [
                        'class'   => 'mytheme-icon-slider__icon-img',
                        'alt'     => $icon_alt,
                        'loading' => 'lazy',
                    ]
                );
                ?>
              <?php else : ?>
                <img
                  class="mytheme-icon-slider__icon-img"
                  src="<?php echo esc_url( $icon_url ); ?>"
                  alt="<?php echo esc_attr( $icon_alt ); ?>"
                  loading="lazy"
                />
              <?php endif; ?>
            </span>
          <?php endif; ?>

          <?php if ( '' !== trim( (string) $label ) ) : ?>
            <span class="mytheme-icon-slider__label"><?php echo esc_html( $label ); ?></span>
          <?php endif; ?>

        </span>
        <?php
    endforeach;
    $pills_html = ob_get_clean();

    // Keep a constant scroll speed regardless of item count.
    $duration = max( 10, count( $items ) * $speed );
    ?>

    <div class="mytheme-icon-slider__marquee">
      <div class="mytheme-icon-slider__track" style="--icon-slider-duration: <?php echo esc_attr( $duration ); ?>s;">
        <?php echo $pills_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php
        // Aria-hidden duplicate keeps the loop seamless without exposing the
        // items twice to screen readers.
        ?>
        <span class="mytheme-icon-slider__track-dupe" aria-hidden="true">
          <?php echo $pills_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </span>
      </div>
    </div>
  <?php endif; ?>

</section>

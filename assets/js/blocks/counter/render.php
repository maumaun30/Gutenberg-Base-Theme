<?php
$items       = $attributes['items'] ?? [];
$animate     = $attributes['animate'] ?? true;
$duration    = (int) ( $attributes['duration'] ?? 2000 );
$columns     = (int) ( $attributes['columns'] ?? 4 );
$show_border = $attributes['showBorder'] ?? true;

if ( empty( $items ) ) {
    return;
}

$classes = 'mytheme-counter';
if ( $show_border ) {
    $classes .= ' mytheme-counter--bordered';
}
?>

<div
  <?php
  echo get_block_wrapper_attributes(
      [
          'class' => $classes,
          'style' => '--counter-columns:' . max( 1, $columns ) . ';',
      ]
  );
  ?>
>
  <div class="mytheme-counter__grid">

    <?php foreach ( $items as $item ) : ?>
      <?php
      $icon_url = $item['iconUrl'] ?? '';
      $icon_id  = (int) ( $item['iconId'] ?? 0 );
      $icon_alt = $item['iconAlt'] ?? '';
      $value    = (string) ( $item['value'] ?? '' );
      $label    = $item['label'] ?? '';

      // Per-item opt-out. Items saved before this toggle existed have no
      // `animate` key, so a missing value has to read as "on".
      $item_animates = $animate && ( ! isset( $item['animate'] ) || false !== $item['animate'] );

      // Split the value into prefix + number + suffix so the count-up animates
      // only the numeric part and "1M+", "24/7" or "4.8★" keep their wrapping.
      $number = '';
      $prefix = $value;
      $suffix = '';
      if ( preg_match( '/\d+(?:[.,]\d+)?/', $value, $match, PREG_OFFSET_CAPTURE ) ) {
          $number = $match[0][0];
          $offset = $match[0][1];
          $prefix = substr( $value, 0, $offset );
          $suffix = substr( $value, $offset + strlen( $number ) );
      }

      // Decimals in the source value are preserved while counting (4.8, not 5).
      $decimals = 0;
      if ( '' !== $number && preg_match( '/[.,](\d+)$/', $number, $dec ) ) {
          $decimals = strlen( $dec[1] );
      }
      ?>

      <div class="mytheme-counter__item">

        <?php if ( $icon_url ) : ?>
          <div class="mytheme-counter__icon">
            <?php if ( $icon_id ) : ?>
              <?php
              echo wp_get_attachment_image(
                  $icon_id,
                  'full',
                  false,
                  [
                      'class'   => 'mytheme-counter__icon-img',
                      'alt'     => $icon_alt,
                      'loading' => 'lazy',
                  ]
              );
              ?>
            <?php else : ?>
              <img
                class="mytheme-counter__icon-img"
                src="<?php echo esc_url( $icon_url ); ?>"
                alt="<?php echo esc_attr( $icon_alt ); ?>"
                loading="lazy"
              />
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if ( '' !== $value ) : ?>
          <div class="mytheme-counter__value">
            <?php if ( '' !== $prefix ) : ?>
              <span class="mytheme-counter__affix"><?php echo esc_html( $prefix ); ?></span>
            <?php endif; ?>

            <?php if ( '' !== $number ) : ?>
              <span
                class="mytheme-counter__number"
                <?php if ( $item_animates ) : ?>
                  data-counter-target="<?php echo esc_attr( str_replace( ',', '.', $number ) ); ?>"
                  data-counter-decimals="<?php echo esc_attr( $decimals ); ?>"
                  data-counter-duration="<?php echo esc_attr( $duration ); ?>"
                <?php endif; ?>
              ><?php echo esc_html( $number ); ?></span>
            <?php endif; ?>

            <?php if ( '' !== $suffix ) : ?>
              <span class="mytheme-counter__affix"><?php echo esc_html( $suffix ); ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if ( $label ) : ?>
          <div class="mytheme-counter__label"><?php echo esc_html( $label ); ?></div>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>

  </div>
</div>

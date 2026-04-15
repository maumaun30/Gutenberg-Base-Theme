<?php
$image_id             = intval( $attributes['imageId'] ?? 0 );
$image_url            = esc_url( $attributes['imageUrl'] ?? '' );
$image_alt            = esc_attr( $attributes['imageAlt'] ?? '' );
$title                = $attributes['title'] ?? '';
$body                 = $attributes['body'] ?? '';
$bullet_points        = $attributes['bulletPoints'] ?? [];
$primary_btn_text     = esc_html( $attributes['primaryButtonText'] ?? '' );
$primary_btn_url      = esc_url( $attributes['primaryButtonUrl'] ?? '#' );
$secondary_btn_text   = esc_html( $attributes['secondaryButtonText'] ?? '' );
$secondary_btn_url    = esc_url( $attributes['secondaryButtonUrl'] ?? '#' );

// Prefer WordPress-generated responsive image markup when we have an ID
$image_html = '';
if ( $image_id ) {
    $image_html = wp_get_attachment_image( $image_id, 'large', false, [
        'class' => 'mytheme-cta__img',
        'alt'   => $image_alt,
    ] );
} elseif ( $image_url ) {
    $image_html = '<img src="' . $image_url . '" alt="' . $image_alt . '" class="mytheme-cta__img" loading="lazy" />';
}
?>

<section <?php echo get_block_wrapper_attributes(['class' => 'mytheme-cta']); ?>>
  <div class="mytheme-cta__container">
    <div class="mytheme-cta__grid">

      <!-- Left: Image -->
      <?php if ( $image_html ) : ?>
        <div class="mytheme-cta__image-col">
          <div class="mytheme-cta__image-wrap">
            <?php echo $image_html; ?>
          </div>
          <div class="mytheme-cta__accent" aria-hidden="true"></div>
        </div>
      <?php endif; ?>

      <!-- Right: Content -->
      <div class="mytheme-cta__content-col">

        <?php if ( $title ) : ?>
          <h2 class="mytheme-cta__title"><?php echo wp_kses_post( $title ); ?></h2>
        <?php endif; ?>

        <?php if ( $body ) : ?>
          <p class="mytheme-cta__body"><?php echo wp_kses_post( $body ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $bullet_points ) ) : ?>
          <ul class="mytheme-cta__bullets">
            <?php foreach ( $bullet_points as $bullet ) : ?>
              <li class="mytheme-cta__bullet">
                <span class="mytheme-cta__bullet-icon" aria-hidden="true">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7" />
                  </svg>
                </span>
                <span><?php echo esc_html( $bullet ); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if ( $primary_btn_text || $secondary_btn_text ) : ?>
          <div class="mytheme-cta__buttons">
            <?php if ( $primary_btn_text ) : ?>
              <a href="<?php echo $primary_btn_url; ?>" class="mytheme-cta__btn mytheme-cta__btn--primary">
                <?php echo $primary_btn_text; ?>
              </a>
            <?php endif; ?>
            <?php if ( $secondary_btn_text ) : ?>
              <a href="<?php echo $secondary_btn_url; ?>" class="mytheme-cta__btn mytheme-cta__btn--secondary">
                <?php echo $secondary_btn_text; ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>
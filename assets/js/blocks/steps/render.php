<?php
$section_title    = $attributes['sectionTitle'] ?? '';
$section_subtitle = $attributes['sectionSubtitle'] ?? '';
$footer_text      = $attributes['footerText'] ?? '';
$steps            = $attributes['steps'] ?? [];
$total            = count( $steps );
?>

<section <?php echo get_block_wrapper_attributes(['class' => 'mytheme-steps']); ?>>
  <div class="mytheme-steps__container">

    <?php if ( $section_title || $section_subtitle ) : ?>
      <div class="mytheme-steps__heading">
        <?php if ( $section_title ) : ?>
          <h2 class="mytheme-steps__title">
            <?php echo wp_kses_post( $section_title ); ?>
          </h2>
        <?php endif; ?>
        <?php if ( $section_subtitle ) : ?>
          <p class="mytheme-steps__subtitle">
            <?php echo wp_kses_post( $section_subtitle ); ?>
          </p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="mytheme-steps__grid">
      <?php foreach ( $steps as $index => $step ) :
        $number   = esc_html( $step['number'] ?? ( $index + 1 ) );
        $title    = esc_html( $step['title'] ?? '' );
        $desc     = esc_html( $step['description'] ?? '' );
        $icon_url = esc_url( $step['iconUrl'] ?? '' );
        $icon_alt = esc_attr( $step['iconAlt'] ?? '' );
      ?>
        <div class="mytheme-steps__item">

          <!-- Badge: absolutely positioned over top-left of card -->
          <div class="mytheme-steps__number" aria-label="Step <?php echo esc_attr( $number ); ?>">
            <?php echo esc_html( $number ); ?>
          </div>

          <div class="mytheme-steps__card">

            <?php if ( $icon_url ) : ?>
              <div class="mytheme-steps__icon">
                <img src="<?php echo $icon_url; ?>" alt="<?php echo $icon_alt; ?>" />
              </div>
            <?php endif; ?>

            <h3 class="mytheme-steps__card-title"><?php echo $title; ?></h3>
            <p class="mytheme-steps__card-desc"><?php echo $desc; ?></p>

            <div class="mytheme-steps__glow" aria-hidden="true"></div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

    <?php if ( $footer_text ) : ?>
      <div class="steps-description">
        <p><?php echo wp_kses_post( $footer_text ); ?></p>
      </div>
    <?php endif; ?>

  </div>
</section>
<?php
$section_title    = $attributes['sectionTitle'] ?? '';
$section_subtitle = $attributes['sectionSubtitle'] ?? '';
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
        $number      = esc_html( $step['number'] ?? '' );
        $title       = esc_html( $step['title'] ?? '' );
        $description = esc_html( $step['description'] ?? '' );
        $is_last     = $index === $total - 1;
      ?>
        <div class="mytheme-steps__card">

          <?php if ( ! $is_last ) : ?>
            <div class="mytheme-steps__connector" aria-hidden="true"></div>
          <?php endif; ?>

          <div class="mytheme-steps__number"><?php echo $number; ?></div>

          <h3 class="mytheme-steps__card-title"><?php echo $title; ?></h3>

          <p class="mytheme-steps__card-desc"><?php echo $description; ?></p>

          <div class="mytheme-steps__glow" aria-hidden="true"></div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>
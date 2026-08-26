<?php
$title           = $attributes['title'] ?? '';
$title_highlight = $attributes['titleHighlight'] ?? '';
$subtitle        = trim( (string) ( $attributes['subtitle'] ?? '' ) );
$is_page_title   = $attributes['isPageTitle'] ?? false;
$steps           = $attributes['steps'] ?? [];
$pad_numbers     = $attributes['padNumbers'] ?? true;
$show_line       = $attributes['showLine'] ?? true;

// SEO: this section is only an <h1> when explicitly marked as the page title.
$title_tag = $is_page_title ? 'h1' : 'h2';

$classes = 'mytheme-line-steps';
if ( ! $show_line ) {
    $classes .= ' mytheme-line-steps--no-line';
}

// HowTo describes this section's ordered instructions; helper lives in inc/schema.php.
$howto_schema = function_exists( 'fnlmx_howto_schema' )
    ? fnlmx_howto_schema( trim( $title . ' ' . $title_highlight ), $subtitle, $steps )
    : '';
?>
<?php echo $howto_schema; ?>

<section
  <?php
  echo get_block_wrapper_attributes(
      [
          'class' => $classes,
          'style' => '--line-steps-count:' . max( 1, count( $steps ) ) . ';',
      ]
  );
  ?>
>
  <div class="mytheme-line-steps__container">

    <?php if ( $title || $title_highlight || '' !== $subtitle ) : ?>
      <header class="mytheme-line-steps__header">

        <?php if ( $title || $title_highlight ) : ?>
          <<?php echo $title_tag; ?> class="mytheme-line-steps__title">
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
          <p class="mytheme-line-steps__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
        <?php endif; ?>

      </header>
    <?php endif; ?>

    <?php if ( ! empty( $steps ) ) : ?>
      <ol class="mytheme-line-steps__list">

        <?php foreach ( $steps as $index => $step ) : ?>
          <?php
          $step_title = $step['title'] ?? '';
          $step_text  = trim( (string) ( $step['description'] ?? '' ) );

          // Numbers follow the step's position unless the step overrides it.
          $number = trim( (string) ( $step['number'] ?? '' ) );
          if ( '' === $number ) {
              $position = $index + 1;
              $number   = ( $pad_numbers && $position < 10 ) ? '0' . $position : (string) $position;
          }
          ?>

          <li class="mytheme-line-steps__step">

            <span class="mytheme-line-steps__marker">
              <span class="mytheme-line-steps__number"><?php echo esc_html( $number ); ?></span>
            </span>

            <?php if ( '' !== trim( (string) $step_title ) ) : ?>
              <h3 class="mytheme-line-steps__step-title"><?php echo esc_html( $step_title ); ?></h3>
            <?php endif; ?>

            <?php if ( '' !== $step_text ) : ?>
              <p class="mytheme-line-steps__step-text"><?php echo wp_kses_post( $step_text ); ?></p>
            <?php endif; ?>

          </li>
        <?php endforeach; ?>

      </ol>
    <?php endif; ?>

  </div>
</section>

<?php
$title           = $attributes['title'] ?? '';
$title_highlight = $attributes['titleHighlight'] ?? '';
$subtitle        = trim( (string) ( $attributes['subtitle'] ?? '' ) );
$is_page_title   = $attributes['isPageTitle'] ?? false;
$cards           = $attributes['cards'] ?? [];
$columns         = (int) ( $attributes['columns'] ?? 3 );

// SEO: this section is only an <h1> when explicitly marked as the page title,
// so a page carrying a hero plus this section never has two H1s.
$title_tag = $is_page_title ? 'h1' : 'h2';
?>

<section
  <?php
  echo get_block_wrapper_attributes(
      [
          'class' => 'mytheme-grid-card',
          'style' => '--grid-card-columns:' . max( 1, $columns ) . ';',
      ]
  );
  ?>
>
  <div class="mytheme-grid-card__container">

    <?php if ( $title || $title_highlight || $subtitle ) : ?>
      <header class="mytheme-grid-card__header">

        <?php if ( $title || $title_highlight ) : ?>
          <<?php echo $title_tag; ?> class="mytheme-grid-card__title">
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
          <p class="mytheme-grid-card__subtitle">
            <?php echo wp_kses_post( $subtitle ); ?>
          </p>
        <?php endif; ?>

      </header>
    <?php endif; ?>

    <?php if ( ! empty( $cards ) ) : ?>
      <div class="mytheme-grid-card__grid">

        <?php foreach ( $cards as $card ) : ?>
          <?php
          $icon_url    = $card['iconUrl'] ?? '';
          $icon_id     = (int) ( $card['iconId'] ?? 0 );
          $icon_alt    = $card['iconAlt'] ?? '';
          $card_title  = $card['title'] ?? '';
          $description = trim( (string) ( $card['description'] ?? '' ) );
          ?>

          <div class="mytheme-grid-card__card">

            <?php if ( $icon_url ) : ?>
              <div class="mytheme-grid-card__icon">
                <?php if ( $icon_id ) : ?>
                  <?php
                  echo wp_get_attachment_image(
                      $icon_id,
                      'full',
                      false,
                      [
                          'class'   => 'mytheme-grid-card__icon-img',
                          'alt'     => $icon_alt,
                          'loading' => 'lazy',
                      ]
                  );
                  ?>
                <?php else : ?>
                  <img
                    class="mytheme-grid-card__icon-img"
                    src="<?php echo esc_url( $icon_url ); ?>"
                    alt="<?php echo esc_attr( $icon_alt ); ?>"
                    loading="lazy"
                  />
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php if ( $card_title ) : ?>
              <h3 class="mytheme-grid-card__card-title"><?php echo esc_html( $card_title ); ?></h3>
            <?php endif; ?>

            <?php if ( '' !== $description ) : ?>
              <p class="mytheme-grid-card__card-text"><?php echo wp_kses_post( $description ); ?></p>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>

      </div>
    <?php endif; ?>

  </div>
</section>

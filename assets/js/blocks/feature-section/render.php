<?php
$title           = $attributes['title'] ?? '';
$title_highlight = $attributes['titleHighlight'] ?? '';
$subtitle        = trim( (string) ( $attributes['subtitle'] ?? '' ) );
$is_page_title   = $attributes['isPageTitle'] ?? false;
$image_url       = $attributes['imageUrl'] ?? '';
$image_id        = (int) ( $attributes['imageId'] ?? 0 );
$image_alt       = $attributes['imageAlt'] ?? '';
$items           = $attributes['items'] ?? [];
$buttons         = $attributes['buttons'] ?? [];
$media_position  = ( 'left' === ( $attributes['mediaPosition'] ?? 'right' ) ) ? 'left' : 'right';

// SEO: this section is only an <h1> when explicitly marked as the page title.
$title_tag = $is_page_title ? 'h1' : 'h2';

$classes = 'mytheme-feature-section mytheme-feature-section--media-' . $media_position;
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => $classes ] ); ?>>
  <div class="mytheme-feature-section__container">

    <div class="mytheme-feature-section__col">

      <?php if ( $title || $title_highlight ) : ?>
        <<?php echo $title_tag; ?> class="mytheme-feature-section__title">
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
        <p class="mytheme-feature-section__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
      <?php endif; ?>

      <?php if ( ! empty( $items ) ) : ?>
        <ul class="mytheme-feature-section__list">
          <?php foreach ( $items as $item ) : ?>
            <?php
            $item_title = $item['title'] ?? '';
            $item_text  = trim( (string) ( $item['description'] ?? '' ) );
            $item_icon  = $item['iconUrl'] ?? '';
            $item_alt   = $item['iconAlt'] ?? '';

            if ( '' === trim( (string) $item_title ) && '' === $item_text ) {
                continue;
            }
            ?>
            <li class="mytheme-feature-section__item">

              <span class="mytheme-feature-section__bullet" aria-hidden="true">
                <?php if ( $item_icon ) : ?>
                  <img src="<?php echo esc_url( $item_icon ); ?>" alt="<?php echo esc_attr( $item_alt ); ?>" loading="lazy" />
                <?php else : ?>
                  <?php // Default bullet matching the design when no icon is uploaded. ?>
                  <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0.5" y="0.5" width="19" height="19" rx="3" stroke="currentColor"></rect>
                    <path d="M5 10.5 L8.5 14 L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                <?php endif; ?>
              </span>

              <span class="mytheme-feature-section__item-body">
                <?php if ( '' !== trim( (string) $item_title ) ) : ?>
                  <strong class="mytheme-feature-section__item-title"><?php echo esc_html( $item_title ); ?></strong>
                <?php endif; ?>

                <?php if ( '' !== $item_text ) : ?>
                  <span class="mytheme-feature-section__item-text"><?php echo wp_kses_post( $item_text ); ?></span>
                <?php endif; ?>
              </span>

            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php if ( ! empty( $buttons ) ) : ?>
        <div class="mytheme-feature-section__buttons">
          <?php foreach ( $buttons as $button ) : ?>
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

            <a class="mytheme-feature-section__btn" href="<?php echo esc_url( $href ); ?>"<?php echo $extra; ?>>

              <?php if ( $btn_icon ) : ?>
                <span class="mytheme-feature-section__btn-icon">
                  <img src="<?php echo esc_url( $btn_icon ); ?>" alt="<?php echo esc_attr( $btn_alt ); ?>" loading="lazy" />
                </span>
              <?php endif; ?>

              <span class="mytheme-feature-section__btn-text">
                <?php if ( '' !== trim( (string) $btn_small ) ) : ?>
                  <span class="mytheme-feature-section__btn-small"><?php echo esc_html( $btn_small ); ?></span>
                <?php endif; ?>

                <?php if ( '' !== trim( (string) $btn_main ) ) : ?>
                  <span class="mytheme-feature-section__btn-main"><?php echo esc_html( $btn_main ); ?></span>
                <?php endif; ?>
              </span>

            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>

    <?php if ( $image_url ) : ?>
      <div class="mytheme-feature-section__media">
        <?php if ( $image_id ) : ?>
          <?php
          echo wp_get_attachment_image(
              $image_id,
              'full',
              false,
              [
                  'class'   => 'mytheme-feature-section__image',
                  'alt'     => $image_alt,
                  'loading' => 'lazy',
              ]
          );
          ?>
        <?php else : ?>
          <img
            class="mytheme-feature-section__image"
            src="<?php echo esc_url( $image_url ); ?>"
            alt="<?php echo esc_attr( $image_alt ); ?>"
            loading="lazy"
          />
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

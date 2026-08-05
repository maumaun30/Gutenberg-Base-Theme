<?php
$title           = $attributes['title'] ?? '';
$title_highlight = $attributes['titleHighlight'] ?? '';
$subtitle        = trim( (string) ( $attributes['subtitle'] ?? '' ) );
$is_page_title   = $attributes['isPageTitle'] ?? false;
$items           = $attributes['items'] ?? [];
$single_open     = $attributes['singleOpen'] ?? true;

// SEO: this section is only an <h1> when explicitly marked as the page title.
$title_tag = $is_page_title ? 'h1' : 'h2';

// Shared name makes the <details> elements mutually exclusive natively; older
// browsers simply ignore it and allow several open at once.
$group_name = 'mytheme-faq-' . uniqid();
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => 'mytheme-faq' ] ); ?>>
  <div class="mytheme-faq__container">

    <?php if ( $title || $title_highlight || '' !== $subtitle ) : ?>
      <header class="mytheme-faq__header">

        <?php if ( $title || $title_highlight ) : ?>
          <<?php echo $title_tag; ?> class="mytheme-faq__title">
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
          <p class="mytheme-faq__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
        <?php endif; ?>

      </header>
    <?php endif; ?>

    <?php if ( ! empty( $items ) ) : ?>
      <div class="mytheme-faq__list">

        <?php foreach ( $items as $item ) : ?>
          <?php
          $question = trim( (string) ( $item['question'] ?? '' ) );
          $answer   = trim( (string) ( $item['answer'] ?? '' ) );

          if ( '' === $question ) {
              continue;
          }
          ?>

          <?php // Native <details> — the accordion works with no JavaScript. ?>
          <details
            class="mytheme-faq__item"
            <?php if ( $single_open ) : ?>name="<?php echo esc_attr( $group_name ); ?>"<?php endif; ?>
          >
            <summary class="mytheme-faq__question">
              <span class="mytheme-faq__question-text"><?php echo esc_html( $question ); ?></span>
              <span class="mytheme-faq__mark" aria-hidden="true"></span>
            </summary>

            <?php if ( '' !== $answer ) : ?>
              <div class="mytheme-faq__answer">
                <?php echo wpautop( wp_kses_post( $answer ) ); ?>
              </div>
            <?php endif; ?>
          </details>
        <?php endforeach; ?>

      </div>
    <?php endif; ?>

  </div>
</section>

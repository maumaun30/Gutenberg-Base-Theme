<?php
$heading          = trim( (string) ( $attributes['heading'] ?? '' ) );
$content          = (string) ( $attributes['content'] ?? '' );
$collapsible      = (bool) ( $attributes['collapsible'] ?? true );
$collapsed_height = (float) ( $attributes['collapsedHeight'] ?? 8.5 );

// Nothing to show when the body is empty (an empty <p> from the editor counts
// as empty too).
if ( '' === trim( strip_tags( $content ) ) ) {
  return;
}
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => 'mytheme-about' ] ); ?>>
  <div class="mytheme-about__wrap">

    <?php if ( '' !== $heading ) : ?>
      <div class="mytheme-about__hd">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" />
          <line x1="12" y1="8" x2="12" y2="12" />
          <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg>
        <h2><?php echo esc_html( $heading ); ?></h2>
      </div>
    <?php endif; ?>

    <?php
    // The collapsed height is inlined as a custom property so view.js can read
    // one value for both the CSS clamp and the collapse animation.
    ?>
    <div class="mytheme-about__content<?php echo $collapsible ? ' is-collapsible' : ''; ?>"
      <?php if ( $collapsible ) : ?>style="--about-collapsed: <?php echo esc_attr( $collapsed_height ); ?>em;"<?php endif; ?>>
      <?php echo wp_kses_post( $content ); ?>
    </div>

    <?php if ( $collapsible ) : ?>
      <button type="button" class="mytheme-about__toggle"
        data-more="Read More" data-less="Read Less">Read More</button>
    <?php endif; ?>

  </div>
</section>

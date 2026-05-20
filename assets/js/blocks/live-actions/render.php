<?php
/**
 * Live Actions Block – render.php
 * No functions defined here to avoid Fatal: Cannot redeclare on multi-block pages.
 *
 * @param array $attributes Block attributes.
 */

$heading_top       = $attributes['headingTop']       ?? 'JOIN LIVE ONLINE';
$heading_highlight = $attributes['headingHighlight'] ?? 'CASINO GAMES';
$heading_bottom    = $attributes['headingBottom']    ?? 'AT YOUR OWN PACE';
$description       = $attributes['description']      ?? '';
$closing_text      = $attributes['closingText']      ?? '';

// Badge: collect non-empty parts, auto-join with pink dot separator in template
$badge_parts = array_values( array_filter( [
    $attributes['badge1'] ?? 'FUN',
    $attributes['badge2'] ?? 'FAST',
    $attributes['badge3'] ?? 'SECURE',
], fn( $v ) => trim( $v ) !== '' ) );

$media_url = $attributes['mediaImageUrl'] ?? '';
$media_alt = $attributes['mediaImageAlt'] ?? '';

// Features — each with unique index for CSS targeting
$features = [
    1 => [
        'icon_url' => $attributes['feature1IconUrl'] ?? '',
        'title'    => $attributes['feature1Title']   ?? 'SAFE',
        'desc'     => $attributes['feature1Desc']    ?? 'Your security is our priority.',
    ],
    2 => [
        'icon_url' => $attributes['feature2IconUrl'] ?? '',
        'title'    => $attributes['feature2Title']   ?? 'FAST CASHOUT',
        'desc'     => $attributes['feature2Desc']    ?? 'Quick withdrawals, hassle-free.',
    ],
    3 => [
        'icon_url' => $attributes['feature3IconUrl'] ?? '',
        'title'    => $attributes['feature3Title']   ?? 'BIG WINS',
        'desc'     => $attributes['feature3Desc']    ?? 'Bigger jackpots await you.',
    ],
];
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => 'lva-section' ] ); ?>>
    <div class="lva-section__inner">

        <!-- ── Left: Content ─────────────────────────────────── -->
        <div class="lva-content">

            <?php if ( ! empty( $badge_parts ) ) : ?>
                <div class="lva-badge">
                    <?php foreach ( $badge_parts as $i => $part ) : ?>
                        <?php if ( $i > 0 ) : ?>
                            <span class="lva-badge__dot" aria-hidden="true">•</span>
                        <?php endif; ?>
                        <span class="lva-badge__item"><?php echo esc_html( $part ); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h2 class="lva-heading">
                <?php echo esc_html( $heading_top ); ?><br>
                <span class="lva-heading__highlight"><?php echo esc_html( $heading_highlight ); ?></span><br>
                <?php echo esc_html( $heading_bottom ); ?>
            </h2>

            <?php if ( $description ) : ?>
                <p class="lva-description"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>

            <?php if ( ! empty( $features ) ) : ?>
                <div class="lva-features">
                    <?php foreach ( $features as $num => $feature ) : ?>
                        <div class="lva-feature lva-feature--<?php echo $num; ?>">
                            <div class="lva-feature__icon lva-feature__icon--<?php echo $num; ?>">
                                <?php if ( ! empty( $feature['icon_url'] ) ) : ?>
                                    <img
                                        src="<?php echo esc_url( $feature['icon_url'] ); ?>"
                                        alt=""
                                        width="32"
                                        height="32"
                                        loading="lazy"
                                        decoding="async"
                                        aria-hidden="true"
                                    >
                                <?php endif; ?>
                            </div>
                            <div class="lva-feature__text lva-feature__text--<?php echo $num; ?>">
                                <strong class="lva-feature__title lva-feature__title--<?php echo $num; ?>">
                                    <?php echo esc_html( $feature['title'] ); ?>
                                </strong>
                                <span class="lva-feature__desc lva-feature__desc--<?php echo $num; ?>">
                                    <?php echo esc_html( $feature['desc'] ); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( $closing_text ) : ?>
                <p class="lva-closing"><?php echo esc_html( $closing_text ); ?></p>
            <?php endif; ?>

        </div><!-- .lva-content -->

        <!-- ── Right: Media ──────────────────────────────────── -->
        <div class="lva-media">
            <?php if ( $media_url ) : ?>
                <img
                    src="<?php echo esc_url( $media_url ); ?>"
                    alt="<?php echo esc_attr( $media_alt ); ?>"
                    class="lva-media__img"
                    loading="lazy"
                    decoding="async"
                >
            <?php endif; ?>
        </div><!-- .lva-media -->

    </div><!-- .lva-section__inner -->
</section>
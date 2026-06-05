<?php
/**
 * Fast Deposit Banner Block — render.php
 */

$badge_label        = ! empty( $attributes['badgeLabel'] )       ? $attributes['badgeLabel']       : 'Fast Deposit';
$badge_sub_label    = ! empty( $attributes['badgeSubLabel'] )    ? $attributes['badgeSubLabel']    : 'With Trusted E-Wallets';
$cta_text           = ! empty( $attributes['ctaText'] )          ? $attributes['ctaText']          : 'Instant top-ups with GCash and Maya';
$logos              = ! empty( $attributes['logos'] ) && is_array( $attributes['logos'] ) ? $attributes['logos'] : [];
$gcash_logo_url     = ! empty( $attributes['gcashLogoUrl'] )     ? $attributes['gcashLogoUrl']     : '';
$maya_logo_url      = ! empty( $attributes['mayaLogoUrl'] )      ? $attributes['mayaLogoUrl']      : '';
$shield_icon_url    = ! empty( $attributes['shieldIconUrl'] )    ? $attributes['shieldIconUrl']    : '';

// Back-compat: if no repeatable logos saved yet, fall back to the legacy
// GCash / Maya single-logo fields so existing blocks keep rendering.
if ( empty( $logos ) ) {
	if ( $gcash_logo_url ) {
		$logos[] = [ 'url' => $gcash_logo_url, 'alt' => 'GCash' ];
	}
	if ( $maya_logo_url ) {
		$logos[] = [ 'url' => $maya_logo_url, 'alt' => 'Maya' ];
	}
}

// Center row holds a maximum of 4 logos.
$logos = array_slice( $logos, 0, 4 );
$lightning_icon_url = ! empty( $attributes['lightningIconUrl'] ) ? $attributes['lightningIconUrl'] : '';

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'fnlmx-fast-deposit-wrap' ] );
?>

<div <?php echo $wrapper_attributes; ?>>
	<div class="fnlmx-fast-deposit">

		<?php /* ── Left: shield + label ── */ ?>
		<div class="fnlmx-fast-deposit__left">
			<div class="fnlmx-fast-deposit__icon">
				<?php if ( $shield_icon_url ) : ?>
					<img src="<?php echo esc_url( $shield_icon_url ); ?>" alt="<?php echo esc_attr( $badge_label ); ?>" class="fnlmx-fast-deposit__shield-img" loading="lazy" decoding="async" />
				<?php else : ?>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd"/>
					</svg>
				<?php endif; ?>
			</div>
			<div class="fnlmx-fast-deposit__label">
				<span class="fnlmx-fast-deposit__title"><?php echo esc_html( $badge_label ); ?></span>
				<span class="fnlmx-fast-deposit__subtitle"><?php echo esc_html( $badge_sub_label ); ?></span>
			</div>
		</div>

		<div class="fnlmx-fast-deposit__divider" aria-hidden="true"></div>

		<?php /* ── Center: logos (repeatable) ── */ ?>
		<div class="fnlmx-fast-deposit__logos">
			<?php if ( ! empty( $logos ) ) : ?>
				<?php foreach ( $logos as $logo ) :
					$logo_url = ! empty( $logo['url'] ) ? $logo['url'] : '';
					$logo_alt = ! empty( $logo['alt'] ) ? $logo['alt'] : '';
					if ( ! $logo_url ) {
						continue;
					}
				?>
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>" class="fnlmx-fast-deposit__logo" loading="lazy" decoding="async" />
				<?php endforeach; ?>
			<?php else : ?>
				<span class="fnlmx-fast-deposit__logo-placeholder">GCash</span>
				<span class="fnlmx-fast-deposit__logo-placeholder fnlmx-fast-deposit__logo-placeholder--maya">maya</span>
			<?php endif; ?>
		</div>

		<div class="fnlmx-fast-deposit__divider" aria-hidden="true"></div>

		<?php /* ── Right: CTA text + lightning (always visible) ── */ ?>
		<div class="fnlmx-fast-deposit__right">
			<span class="fnlmx-fast-deposit__cta-text"><?php echo esc_html( $cta_text ); ?></span>
			<div class="fnlmx-fast-deposit__lightning" aria-hidden="true">
				<?php if ( $lightning_icon_url ) : ?>
					<img src="<?php echo esc_url( $lightning_icon_url ); ?>" alt="" class="fnlmx-fast-deposit__lightning-img" loading="lazy" decoding="async" />
				<?php else : ?>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
						<path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.818a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.845-.143z" clip-rule="evenodd"/>
					</svg>
				<?php endif; ?>
			</div>
		</div>

	</div>
</div>
<?php
        $section_title    = $attributes['sectionTitle'] ?? '';
        $section_subtitle = $attributes['sectionSubtitle'] ?? '';
        $features         = $attributes['features'] ?? [];

        /**
         * Safely load an SVG from the media library by attachment ID.
         * Falls back to an <img> tag if the file can't be read inline.
         *
         * @param int    $svg_id  Attachment ID.
         * @param string $svg_url Fallback URL.
         * @return string HTML string.
         */

        if (! function_exists('mytheme_render_svg_icon')) {
            function mytheme_render_svg_icon(int $svg_id, string $svg_url): string
            {
                if (! $svg_id) {
                    return '';
                }

                $file_path = get_attached_file($svg_id);

                // Inline SVG — allows CSS color control via currentColor
                if ($file_path && file_exists($file_path) && 'image/svg+xml' === get_post_mime_type($svg_id)) {
                    $svg = file_get_contents($file_path); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

                    /* Strip <?xml ... ?> declaration and doctype */
                    $svg = preg_replace('/<\?xml[^>]*\?>/i', '', $svg);
                    $svg = preg_replace('/<!DOCTYPE[^>]*>/i', '', $svg);

                    // Force consistent sizing via class; let CSS handle dimensions
                    $svg = preg_replace('/<svg/', '<svg class="mytheme-feature-svg"', $svg, 1);

                    return trim($svg);
                }

                // Fallback: regular <img> tag
                if ($svg_url) {
                    return '<img src="' . esc_url($svg_url) . '" alt="" class="mytheme-feature-svg" aria-hidden="true" />';
                }

                return '';
            }
        }
        ?>

<section <?php echo get_block_wrapper_attributes(['class' => 'mytheme-why-choose-us']); ?>>
    <div class="mytheme-wcu__container">

        <?php if ($section_title || $section_subtitle) : ?>
            <div class="mytheme-wcu__heading">
                <?php if ($section_title) : ?>
                    <h2 class="mytheme-wcu__title"><?php echo wp_kses_post($section_title); ?></h2>
                <?php endif; ?>
                <?php if ($section_subtitle) : ?>
                    <p class="mytheme-wcu__subtitle"><?php echo wp_kses_post($section_subtitle); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="mytheme-wcu__grid">
            <?php foreach ($features as $feature) :
                $svg_id  = intval($feature['svgId'] ?? 0);
                $svg_url = esc_url($feature['svgUrl'] ?? '');
                $title   = esc_html($feature['title'] ?? '');
                $desc    = esc_html($feature['description'] ?? '');
                $svg_html = mytheme_render_svg_icon($svg_id, $svg_url);
            ?>
                <div class="mytheme-wcu__card group">

                    <?php if ($svg_html) : ?>
                        <div class="mytheme-wcu__icon-box">
                            <?php echo $svg_html; ?>
                        </div>
                    <?php endif; ?>

                    <h3 class="mytheme-wcu__card-title"><?php echo $title; ?></h3>
                    <p class="mytheme-wcu__card-desc"><?php echo $desc; ?></p>

                    <div class="mytheme-wcu__accent-line" aria-hidden="true"></div>
                    <div class="mytheme-wcu__glow" aria-hidden="true"></div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
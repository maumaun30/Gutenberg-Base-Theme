<?php get_header(); ?>

<main class="site-main bg-dark-3">
    
    <?php if (have_posts()) : ?>
        
        <?php while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php
                /* Split the content so regular page blocks (core/*) sit inside a
                   width-constrained .entry-content wrapper, while full-bleed
                   sections — custom theme blocks (mytheme/*) and shortcode
                   blocks like [fnlmx_cta] — render outside it. do_shortcode()
                   is applied because render_block() alone doesn't expand
                   shortcodes (normally the_content's filter does). */
                $fnlmx_blocks = parse_blocks( get_the_content() );
                $fnlmx_buffer = '';

                $fnlmx_flush = static function () use ( &$fnlmx_buffer ) {
                    if ( trim( $fnlmx_buffer ) !== '' ) {
                        echo '<div class="entry-content">' . do_shortcode( $fnlmx_buffer ) . '</div>';
                        $fnlmx_buffer = '';
                    }
                };

                foreach ( $fnlmx_blocks as $fnlmx_block ) {
                    $fnlmx_name = $fnlmx_block['blockName'] ?? '';

                    $fnlmx_full_width = $fnlmx_name && (
                        strpos( $fnlmx_name, 'mytheme/' ) === 0 ||
                        $fnlmx_name === 'core/shortcode'
                    );

                    if ( $fnlmx_full_width ) {
                        $fnlmx_flush();
                        echo do_shortcode( render_block( $fnlmx_block ) );
                    } else {
                        $fnlmx_buffer .= render_block( $fnlmx_block );
                    }
                }
                $fnlmx_flush();
                ?>

            </article>

        <?php endwhile; ?>

    <?php else : ?>

        <article>
            <p>Sorry, nothing matched your request.</p>
        </article>

    <?php endif; ?>

</main>

<?php get_footer(); ?>
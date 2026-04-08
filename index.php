<?php get_header(); ?>

<main class="site-main bg-dark-3">
    
    <?php if (have_posts()) : ?>
        
        <?php while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <?php the_content(); ?>

            </article>

        <?php endwhile; ?>

    <?php else : ?>

        <article>
            <p>Sorry, nothing matched your request.</p>
        </article>

    <?php endif; ?>

</main>

<?php get_footer(); ?>
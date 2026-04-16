<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header bg-dark-2">
    <div class="container site-header__inner">
        <div class="site-header__row">
            
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-branding__text">
                        <?php bloginfo('name'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <button
                id="mobile-menu-toggle"
                class="mobile-menu-toggle"
                aria-label="<?php esc_attr_e('Toggle Menu', 'mytheme'); ?>"
                aria-controls="site-navigation"
                aria-expanded="false"
                type="button"
            >
                ☰
            </button>

            <nav id="site-navigation" class="site-navigation" aria-label="<?php esc_attr_e('Primary Menu', 'mytheme'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'primary-menu',
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>

        </div>
    </div>
</header>
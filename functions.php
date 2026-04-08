<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    if (!class_exists('ACF')) {
        include_once get_stylesheet_directory() . '/acf/acf.php';
    }
});

add_filter('acf/settings/path', function ($path) {
    return get_stylesheet_directory() . '/acf/';
});

add_filter('acf/settings/dir', function ($dir) {
    return get_stylesheet_directory_uri() . '/acf/';
});

function mytheme_setup() {
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('post-thumbnails');

    add_editor_style('build/editor.css');
}
add_action('after_setup_theme', 'mytheme_setup');

function mytheme_enqueue_assets() {
    $main_css = get_theme_file_path('/build/main.css');

    if (file_exists($main_css)) {
        wp_enqueue_style(
            'mytheme-main',
            get_theme_file_uri('/build/main.css'),
            [],
            filemtime($main_css)
        );
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_assets');

function mytheme_enqueue_editor_assets() {
    $editor_css = get_theme_file_path('/build/editor.css');

    if (file_exists($editor_css)) {
        wp_enqueue_style(
            'mytheme-editor',
            get_theme_file_uri('/build/editor.css'),
            [],
            filemtime($editor_css)
        );
    }
}
add_action('enqueue_block_editor_assets', 'mytheme_enqueue_editor_assets');

function mytheme_register_blocks() {
    $blocks_dir = get_theme_file_path('/assets/js/blocks');

    if (!is_dir($blocks_dir)) {
        return;
    }

    $block_folders = scandir($blocks_dir);

    foreach ($block_folders as $folder) {
        if ($folder === '.' || $folder === '..') {
            continue;
        }

        $block_path = $blocks_dir . '/' . $folder;

        if (is_dir($block_path) && file_exists($block_path . '/block.json')) {
            register_block_type($block_path);
        }
    }
}
add_action('init', 'mytheme_register_blocks');
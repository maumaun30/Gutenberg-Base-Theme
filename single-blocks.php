<?php

/**
 * Template Name: Full Block Layout
 * Template Post Type: post
 */

get_header();

if (have_posts()) :
  while (have_posts()) : the_post();
?>
    <style>
      .fnlmx-single-blocks {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
        color: #fff;
        font-family: 'Montserrat', sans-serif;
      }

      .fnlmx-single-blocks__content {
        width: 100%;
        max-width: none;
        margin: 0;
        min-width: 0;
        overflow-wrap: anywhere;
      }

      .fnlmx-single-blocks__content img,
      .fnlmx-single-blocks__content video,
      .fnlmx-single-blocks__content iframe,
      .fnlmx-single-blocks__content table {
        max-width: 100%;
      }

      .fnlmx-single-blocks__content img,
      .fnlmx-single-blocks__content video {
        height: auto;
      }

      .fnlmx-single-blocks__content iframe {
        width: 100%;
      }

      .fnlmx-single-blocks__content table {
        display: block;
        overflow-x: auto;
      }

      @media (max-width: 640px) {
        .fnlmx-single-blocks {
          padding: 0;
        }
      }
    </style>
    <main class="fnlmx-single-blocks">
      <div class="fnlmx-single-blocks__content"><?php the_content(); ?></div>
    </main>
<?php
  endwhile;
endif;

get_footer();

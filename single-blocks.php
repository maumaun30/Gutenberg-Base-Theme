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
        max-width: 1280px;
        margin: 0 auto;
        padding: clamp(2rem, 5vw, 5rem) 1.5rem;
        color: #fff;
        font-family: 'Montserrat', sans-serif;
      }

      .fnlmx-single-blocks__title {
        max-width: 900px;
        margin: 0 auto 2rem;
        color: #fff;
        font-size: clamp(2rem, 5vw, 4rem);
        line-height: 1.1;
        text-align: center;
      }

      .fnlmx-single-blocks__content {
        max-width: 1100px;
        margin: 0 auto;
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
          padding-right: 1rem;
          padding-left: 1rem;
        }
      }
    </style>
    <main class="fnlmx-single-blocks">
      <h1 class="fnlmx-single-blocks__title"><?php the_title(); ?></h1>
      <div class="fnlmx-single-blocks__content"><?php the_content(); ?></div>
    </main>
<?php
  endwhile;
endif;

get_footer();

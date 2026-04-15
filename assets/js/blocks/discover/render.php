<?php
$heading    = $attributes['heading'] ?? '';
$paragraphs = $attributes['paragraphs'] ?? [];
$stats      = $attributes['stats'] ?? [];
?>

<section <?php echo get_block_wrapper_attributes(['class' => 'discover-block bg-dark-3 section']); ?>>
  <div class="discover-inner">

    <?php if ($heading) : ?>
      <h2 class="discover-heading"><?php echo esc_html($heading); ?></h2>
    <?php endif; ?>

    <?php if (!empty($paragraphs)) : ?>
      <div class="discover-body">
        <?php foreach ($paragraphs as $paragraph) : ?>
          <?php if ($paragraph) : ?>
            <p><?php echo esc_html($paragraph); ?></p>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($stats)) : ?>
      <div class="discover-stats">
        <?php foreach ($stats as $stat) : ?>
          <div class="discover-stat">
            <?php if (!empty($stat['value'])) : ?>
              <span class="discover-stat__value"><?php echo esc_html($stat['value']); ?></span>
            <?php endif; ?>
            <?php if (!empty($stat['label'])) : ?>
              <span class="discover-stat__label"><?php echo esc_html($stat['label']); ?></span>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>
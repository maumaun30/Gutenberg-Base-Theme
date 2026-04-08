<?php
$title = $attributes['title'] ?? '';
$description = $attributes['description'] ?? '';
?>

<section <?php echo get_block_wrapper_attributes(['class' => 'card']); ?>>
    <?php if ($title) : ?>
        <h2><?php echo esc_html($title); ?></h2>
    <?php endif; ?>

    <?php if ($description) : ?>
        <p><?php echo esc_html($description); ?></p>
    <?php endif; ?>
</section>

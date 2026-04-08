<?php
$title = $attributes['title'] ?? '';
$content = $attributes['content'] ?? '';
$button_text = $attributes['buttonText'] ?? 'Learn more';
$button_url = $attributes['buttonUrl'] ?? '#';
?>

<section <?php echo get_block_wrapper_attributes(['class' => 'rounded-2xl bg-gray-100 px-6 py-16']); ?>>
    <?php if ($title) : ?>
        <h2 class="mb-4 text-4xl font-bold"><?php echo esc_html($title); ?></h2>
    <?php endif; ?>

    <?php if ($content) : ?>
        <p class="mb-6 text-lg text-gray-600"><?php echo esc_html($content); ?></p>
    <?php endif; ?>

    <a href="<?php echo esc_url($button_url); ?>" class="inline-flex rounded-lg bg-blue-600 px-5 py-3 text-white no-underline">
        <?php echo esc_html($button_text); ?>
    </a>
</section>
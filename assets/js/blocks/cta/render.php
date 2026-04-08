<?php
$heading = $attributes['heading'] ?? '';
$button_text = $attributes['buttonText'] ?? 'Contact us';
$button_url = $attributes['buttonUrl'] ?? '#';
?>

<section <?php echo get_block_wrapper_attributes(['class' => 'rounded-2xl bg-gray-900 px-6 py-12 text-white']); ?>>
    <?php if ($heading) : ?>
        <h3 class="mb-4 text-3xl font-bold"><?php echo esc_html($heading); ?></h3>
    <?php endif; ?>

    <a href="<?php echo esc_url($button_url); ?>" class="inline-flex rounded-lg bg-white px-5 py-3 text-gray-900 no-underline">
        <?php echo esc_html($button_text); ?>
    </a>
</section>
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Single Option Template
 *
 * @var string $image_id
 * @var string $input_type
 * @var string $terms_str
 */
?>

<div class="input-wrapper type-<?php echo $input_type ?> option-wrapper" data-terms="<?php echo $terms_str ?>">
    <input type="<?php echo $input_type ?>" name="<?php echo $attr_id ?>" id="<?php echo $image_id ?>"
        value="<?php echo $image_id ?>">
    <label for="<?php echo $image_id ?>">
        <?php echo wp_get_attachment_image($image_id, 'medium') ?>
    </label>
</div>
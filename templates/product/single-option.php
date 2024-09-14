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

$image_url = wp_get_attachment_image_url($image_id, 'medium');
$image_full_size_url = wp_get_attachment_image_url($image_id, 'full');
?>

<div class="input-wrapper type-<?php echo $input_type ?> option-wrapper gallery-item" data-terms="<?php echo $terms_str ?>">
    <input type="<?php echo $input_type ?>" name="<?php echo "attributes[$attr_id]" ?>" id="<?php echo $image_id ?>"
        value="<?php echo $image_full_size_url ?>">
    <label for="<?php echo $image_id ?>" style="background-image: url(<?php echo $image_url ?>)">
    </label>
</div>
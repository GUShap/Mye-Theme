<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Attributes Template
 * 
 * @var array $custom_attributes
 */
?>

<div class="custom-attr-container">
    <h4 class="custom-attr-title">תוספות</h4>
    <div class="attributes-wrapper" data-added-price="0" data-variation-price="0">
        <?php foreach ($custom_attributes as $attr_id) {
            $attribute = [
                'name' => get_the_title($attr_id),
                'id' => get_field('attribute_id', $attr_id),
                'description' => get_field('attribute_description', $attr_id),
                'price' => get_field('attribute_price', $attr_id),
                'options' => get_field('attribute_options', $attr_id),
                'multiple_options' => get_field('multiple_options', $attr_id),
                'price_per_option' => get_field('price_per_options', $attr_id),
                'max_avialable_options' => get_field('max_avialable_options', $attr_id),
                'single_option_label' => get_field('single_option_label', $attr_id),
                'sub_options_available' => get_field('sub_options_available', $attr_id),
                'filter_available' => get_field('filter_available', $attr_id)
            ];
            $has_options = is_array($attribute['options']) && count($attribute['options']) > 0 ? true : false;
            $is_price_per_option = !empty($attribute['price_per_option']) ? true : false;
            ?>
            <div class="attribute-wrapper" data-attr-id="<?php echo $attribute['id'] ?>">
                <?php setStep1($attr_id, $attribute, $has_options, $is_price_per_option) ?>
                <?php setStep2($attribute, $has_options, $is_price_per_option) ?>
            </div>
        <?php } ?>
    </div>
</div>
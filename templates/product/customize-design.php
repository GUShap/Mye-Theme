<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customize Design Template
 * 
 * @param array $attributes
 * @param boolean $enable_custom_text
 */
// dd($enable_custom_text);

?>
<div class="customize-design-container">
    <div class="heading-wrapper"></div>
    <div class="content-wrapper">
        <div class="attributes-wrapper active">
            <?php foreach ($attributes as $attr_idx => $attr_id) {
                $is_active = $attr_idx === 0;
                // $attribute = [
                //     'id' => get_field('attribute_id', $attr_id),
                //     'description' => get_field('attribute_description', $attr_id),
                //     'single_option_label' => get_field('single_option_label', $attr_id),
                //     'sub_options_available' => get_field('sub_options_available', $attr_id),
                //     'filter_available' => get_field('filter_available', $attr_id)
                // ];
                $title = get_the_title($attr_id);
                $icon = get_field('icon', $attr_id);
                $options = get_field('attribute_options', $attr_id);
                $multiple_selection = !empty(get_field('multiple_selection', $attr_id));
                $max_avialable_options = get_field('max_avialable_options', $attr_id);
                $has_options = !empty($options);
                $attr_price = get_field('attribute_price', $attr_id);
                $price_per_option = get_field('price_per_options', $attr_id);
                $is_price_per_option = !empty($price_per_option);
                $image_upload_enabled = !empty(get_field('enable_user_image_upload', $attr_id));
                $is_search_enabled = !empty(get_field('enable_search', $attr_id));
                ?>
                <div class="attribute-wrapper<?php echo $is_active ? ' active' : '' ?>">
                    <div class="attr-heading">
                        <button type="button">
                            <?php echo wp_get_attachment_image($icon, 'thumbnail', true) ?>
                            <span><?php echo $title ?></span>
                        </button>
                    </div>
                    <div class="attr-content" data-limit="<?php echo $max_avialable_options ?>">
                        <?php if ($is_search_enabled) { ?>
                            <div class="searchform-wrapper">
                                <input type="search" name="<?php echo $attr_id ?>search" id="<?php echo $attr_id ?>search"
                                    placeholder="חיפוש">
                            </div>
                        <?php } ?>
                        <div class="gallery-wrapper">
                            <?php foreach ($options as $options_idx => $option) {
                                $label = $option['label'];
                                $value = $option['value'];
                                $search_terms = $option['search_terms'];
                                $gallery = $option['reff_gallery'];

                                $input_type = $multiple_selection ? 'checkbox' : 'radio';
                                $terms_str = "$label, $search_terms";

                                foreach ($gallery as $image_idx => $image_id) {
                                    $single_option_template_path = HE_CHILD_THEME_DIR . '/templates/product/single-option.php';
                                    if (file_exists($single_option_template_path)) {
                                        include $single_option_template_path;
                                    }
                                }
                            } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <canvas></canvas>
    </div>
</div>
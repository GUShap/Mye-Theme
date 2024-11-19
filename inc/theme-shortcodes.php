<?php
if (!defined('ABSPATH')) {
    exit;
}

function customize_design_template_callback($atts)
{
    $attributes = explode(',', $atts['custom_attributes']);
    $enable_custom_text = !empty($atts['enable_custom_text']);
    $product_id = $atts['product_id'];
    $add_chocolate_writing = !empty(get_field('add_chocolate_writing', $product_id));
    $customize_design_template_path = HE_CHILD_THEME_DIR . '/templates/product/customize-design.php';
    if (file_exists($customize_design_template_path)) {
        ob_start();
        include $customize_design_template_path;
        return ob_get_clean();
    }
}

add_shortcode('customize_design', 'customize_design_template_callback');
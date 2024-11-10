<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if (!function_exists('chld_thm_cfg_locale_css')):
    function chld_thm_cfg_locale_css($uri)
    {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')):
    function child_theme_configurator_css()
    {
        wp_enqueue_style('chld_thm_cfg_separate', trailingslashit(get_stylesheet_directory_uri()) . 'ctc-style.css', array('hello-elementor', 'hello-elementor', 'hello-elementor-theme-style'));
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 100);

// END ENQUEUE PARENT ACTION

define('HE_CHILD_THEME_DIR', trailingslashit(get_stylesheet_directory()));

/*******/
require_once HE_CHILD_THEME_DIR . 'inc/admin-functions.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-actions.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-filters.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-shortcodes.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-ajax.php';
require_once HE_CHILD_THEME_DIR . 'inc/admin-hooks.php';
require_once HE_CHILD_THEME_DIR . 'inc/woocommerce-hooks.php';
// END ENQUEUE PARENT ACTION

function set_item_custom_information($cart_item)
{
    $allergen_list = $cart_item['allergen_list'] ?? [];
    $custom_attributes = $cart_item['custom_attributes'] ?? [];
    if (empty($allergen_list) && empty($custom_attributes))
        return;
    $custom_item_information_template_path = HE_CHILD_THEME_DIR . '/templates/cart/custom-item-information.php';
    if (file_exists($custom_item_information_template_path)) {
        include $custom_item_information_template_path;
    }
}

function set_custom_variation_attr_list($cart_item)
{
    $variation_id = $cart_item['variation_id'];
    $product = wc_get_product($variation_id);
    $variation_attributes = $product->get_variation_attributes();
    $variation_info = '';
    if (!empty($variation_attributes)) {
        $variation_info = '<ul class="variation-info">';
        foreach ($variation_attributes as $attribute_name => $term_value) {
            $taxonomy = str_replace('attribute_', '', $attribute_name);
            $term = get_term_by('slug', $term_value, $taxonomy);
            $label = wc_attribute_label($taxonomy);
            $term_name = $term->name;
            if ($term_value !== 'false') {
                $variation_info .= '<li class="variation-attribute">' . wc_attribute_label($label);
                $variation_info .= $term_value == 'true'
                    ? '</li>'
                    : " : $term_name</li>";
            }
        }
        $variation_info .= '</ul>';
    }

    echo $variation_info;
}
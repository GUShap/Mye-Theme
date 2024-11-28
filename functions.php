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
	$writing_text = $cart_item['cake_writing'] ?? '';
	if (empty($allergen_list) && empty($custom_attributes) && empty($writing_text))
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
/************/

// Automatically apply the custom checkbox column to all WooCommerce product attribute taxonomies
function apply_custom_checkbox_to_taxonomies_list()
{
	$taxonomies = wc_get_attribute_taxonomies();
	// 	dd($taxonomies);

	if (!empty($taxonomies)) {
		foreach ($taxonomies as $taxonomy) {
			$taxonomy_name = wc_attribute_taxonomy_name($taxonomy->attribute_name);

			// Add the checkbox column to the terms list
			add_filter("manage_edit-{$taxonomy_name}_columns", 'add_custom_checkbox_column_to_terms_list');
			add_filter("manage_{$taxonomy_name}_custom_column", 'display_custom_checkbox_column_in_terms_list', 10, 3);
		}
	}
}
add_action('init', 'apply_custom_checkbox_to_taxonomies_list');

/******************************/
// add_action('template_redirect', function () {
// 	return;
// 	$product_id = 1672; // Your product ID
// 	$product = wc_get_product($product_id);
// 	$variations = $product->get_children();
// 	foreach ($variations as $variation_id) {
// 		$variation_price = 0;
// 		$variation = wc_get_product($variation_id);  // Get variation object
// 		$attributes = $variation->get_attributes();
// 		foreach ($attributes as $attr_slug => $attr_val) {
// 			if (empty($attr_val)) {
// 				wp_delete_post($variation_id, true);
// 				continue 1;
// 			}
// 			switch ($attr_slug) {
// 				case 'pa_mini-cupcakes-quantity':
// 					if ($attr_val == '24')
// 						$variation_price += 270;
// 					if ($attr_val == '40')
// 						$variation_price += 300;
// 					break;
// 				case 'pa_gluten':
// 					if ($attr_val == 'true')
// 						$variation_price += 30;
// 					break;
// 				case 'pa_vegan':
// 					if ($attr_val == 'true')
// 						$variation_price += 20;
// 					break;
// 				case 'pa_chocolate_ganach':
// 					if ($attr_val == 'true')
// 						$variation_price += 35;
// 					break;
// 			}
// 		}
// 		update_post_meta($variation_id, '_regular_price', $variation_price);
// 	}
// });
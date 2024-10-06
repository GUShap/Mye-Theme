<?php

if (!defined('ABSPATH')) {
  exit;
}

function get_cake_themes_callback()
{
  $taxonomy = 'pa_' . sanitize_title('cake_theme');
  $theme_options = get_terms([
    'taxonomy' => $taxonomy,
    'hide_empty' => false,
  ]);
  $data = [];

  foreach ($theme_options as $idx => $option) {
    $theme_images = get_field('theme_images', $option);
    $search_terms = make_array_shallow(get_field('search_terms', $option));
    $search_terms[] = $option->name;

    if (!empty($theme_images)) {
      $images = [];

      foreach ($theme_images as $inner_idx => $image_id) {
        $images[$inner_idx] = wp_get_attachment_image($image_id, 'full');
      }

      $data[] = [
        'name' => $option->name,
        'slug' => $option->slug,
        'search_term' => $search_terms,
        'images' => $images
      ];
    }
  }
  // You can do further processing on $theme_options if needed

  // Return the data as JSON
  wp_send_json($data);
}
// Hook for the AJAX action in WordPress
add_action('wp_ajax_get_cake_themes', 'get_cake_themes_callback');
add_action('wp_ajax_nopriv_get_cake_themes', 'get_cake_themes_callback');
function add_attributes_to_item()
{
  $response = [];
  $item_data = $_POST['item_data'];
  $added_price = 0;
  foreach ($item_data as $attr_id => $options) {
    $attr_price = get_field('attribute_price', $attr_id);
    $is_multiple_selection = !empty(get_field('multiple_selection', $attr_id));
    $is_price_per_options = !empty(get_field('price_per_options', $attr_id));

    if (!$is_multiple_selection) {
      $added_price += $attr_price;
      continue;
    }
    $is_price_per_options
      ? $added_price += $attr_price * count($options)
      : $added_price += $attr_price;
  }

  $response['status'] = 'success';
  $response['added_price'] = $added_price;

  $main_image_id = '';
  $fallback_image_url = '';
  $gallery_image_ids = [];

  $term_counter = 0;

  foreach ($item_data as $attr_id => $image_options) {
    foreach ($image_options as $term_id => $image_src) {
      $image_id = attachment_url_to_postid($image_src) ?? '';
      if ($term_counter === 0) {
        $term_id == 'custom_image' ? $fallback_image_url = $image_src : $main_image_id = $image_id;
        WC()->session->set('custom_image', $image_src);
      } else {
        if ($term_id == 'custom_image') {
          $fallback_image_url = $image_src;
          $gallery_image_ids[] = $main_image_id;
          $main_image_id = null;
          WC()->session->set('custom_image', $image_src);
          continue;
        }
        ;
        $gallery_image_ids[] = $image_id;
      }
      $term_counter++;
    }
  }

  $pretty_gallery_template_path = HE_CHILD_THEME_DIR . 'inc/elementor/templates/pretty-product-gallery.php';
  if (!file_exists($pretty_gallery_template_path)) {
    wp_send_json_error('Template file not found');
  }
  // get template content
  ob_start();
  include $pretty_gallery_template_path;
  $response['gallery_html'] = ob_get_clean();
  $response['gallery_selector'] = '.pretty-product-gallery';

  wp_send_json_success($response);
}
add_action('wp_ajax_add_attributes_to_item', 'add_attributes_to_item');
add_action('wp_ajax_nopriv_add_attributes_to_item', 'add_attributes_to_item');

function woocommerce_add_to_cart_variable_rc_callback()
{
  ob_start();

  $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
  $quantity = empty($_POST['quantity']) ? 1 : apply_filters('woocommerce_stock_amount', $_POST['quantity']);
  $variation_id = $_POST['variation_id'];

  $cart_item_data = [];

  $allergen_list = $_POST['allergen_list'] ?? [];
  $custom_attributes_raw = !empty($_POST['custom_attributes']) ? json_decode(stripslashes($_POST['custom_attributes']), true) : [];
  $added_price = $_POST['added_price'] ?? 0;
  $custom_attributes = [];
  if (!empty($custom_attributes_raw)) {
    foreach ($custom_attributes_raw as $inner_array) {
      foreach ($inner_array as $key => $values) {
        foreach ($values as $idx => $value) {
          switch ($value) {
            case 'custom_image':
              $base_64_file = WC()->session->get('custom_image');
              $custom_image_id = save_user_image_as_file($base_64_file);
              if ($custom_image_id) {
                wp_schedule_single_event(time() + 3 * WEEK_IN_SECONDS, 'delete_user_custom_image', array($custom_image_id));
              }
              WC()->session->set('custom_image', '');
              $custom_attributes[$key][$idx] = $custom_image_id;
              break;
            default:
              $custom_attributes[$key][$idx] = str_replace("{$key}_", '', $value);
              break;
          }
        }
      }
    }
  }

  if (!empty($allergen_list))
    $cart_item_data['allergen_list'] = $allergen_list;

  if (!empty($custom_attributes)) {
    $cart_item_data['custom_attributes'] = $custom_attributes;
  }
  if (!empty($added_price))
    $cart_item_data['added_price'] = $added_price;

  $variation = [];

  foreach ($cart_item_data as $key => $values) {
    if (preg_match("/^attribute*/", $key)) {
      $variation[$key] = $values;
    }
  }

  $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);

  if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $cart_item_data)) {
    do_action('woocommerce_ajax_added_to_cart', $product_id);
    if (get_option('woocommerce_cart_redirect_after_add') == 'yes') {
      wc_add_to_cart_message($product_id);
    }
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();
    wc_setcookie('woocommerce_items_in_cart', count($items));
    wc_setcookie('woocommerce_cart_hash', md5(json_encode($items)));
    do_action('woocommerce_set_cart_cookies', true);
    // Return fragments
    WC_AJAX::get_refreshed_fragments();

  } else {

    // If there was an error adding to the cart, redirect to the product page to show any errors
    $data = array(
      'error' => true,
      'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
    );
    wp_send_json_error($data);
  }
}
add_action('wp_ajax_woocommerce_add_to_cart_variable_rc', 'woocommerce_add_to_cart_variable_rc_callback');
add_action('wp_ajax_nopriv_woocommerce_add_to_cart_variable_rc', 'woocommerce_add_to_cart_variable_rc_callback');
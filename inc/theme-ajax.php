<?php

if (!defined('ABSPATH')) {
  exit;
}

function add_attributes_to_item()
{
  $response = [];
  $item_data = $_POST['item_data'];
  $cake_writing_chars = $_POST['cake_writing_chars'];
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

  if ($cake_writing_chars) {
    $letters_threshold = get_field('letters_threshold', 'options');
    $price = get_field('price_above_threshold', 'options');
    if ($cake_writing_chars > $letters_threshold) {
      $added_price += $price;
    }
  }

  $response['status'] = 'success';
  $response['added_price'] = $added_price;

  $main_image_id = '';
  $fallback_image_url = '';
  $gallery_image_ids = [];
  $term_counter = 0;

  foreach ($item_data as $attr_id => $image_options) {
    foreach ($image_options as $term_id => $term_data) {
      $image_src = $term_data['image_src'];
      $image_id = attachment_url_to_postid($image_src) ?? '';
      if ($term_counter === 0) {
        switch ($term_id) {
          case 'custom_image':
            $fallback_image_url = $image_src;
            continue 2;
          default:
            $main_image_id = $image_id;
            break;
        }
      } else {
        if ($term_id == 'custom_image') {
          $fallback_image_url = $image_src;
          $gallery_image_ids[] = $main_image_id;
          $main_image_id = null;
          WC()->session->set('custom_image', $image_src);
          continue;
        }
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
  $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
  $quantity = empty($_POST['quantity']) ? 1 : apply_filters('woocommerce_stock_amount', $_POST['quantity']);
  $variation_id = $_POST['variation_id'];

  $cart_item_data = [];
  $allergen_list = $_POST['allergen_list'] ?? [];
  $added_price = $_POST['added_price'] ?? 0;
  $custom_attributes_raw = $_POST['items_data'] ?? [];
  $cake_writing = $_POST['writing'] ?? '';
  $custom_attributes = [];
  $multiple_options_attr = $_POST['multi_options_attr'] ?? [];

  if (!empty($custom_attributes_raw)) {
    foreach ($custom_attributes_raw as $attr_key => $inner_array) {
      foreach ($inner_array as $option_key_raw => $values) {
        if ($option_key_raw == 'custom_image') {
          $custom_image_id = save_user_image_as_file($values['image_src']);
          if ($custom_image_id) {
            $values['image_src'] = wp_get_attachment_url($custom_image_id);
            wp_schedule_single_event(time() + 3 * WEEK_IN_SECONDS, 'delete_user_custom_image', array($custom_image_id));
          }
        }
        $option_key = $option_key_raw == 'custom_image'
          ? 'custom_image'
          : str_replace("{$attr_key}_", '', $option_key_raw);
        $custom_attributes[$attr_key][$option_key] = $values;

      }
    }
  }
  if (!empty($allergen_list)) {
    $cart_item_data['allergen_list'] = $allergen_list;
  }
  if (!empty($custom_attributes)) {
    $cart_item_data['custom_attributes'] = $custom_attributes;
  }
  if (!empty($added_price)) {
    $cart_item_data['added_price'] = $added_price;
  }
  if (!empty($cake_writing)) {
    $cart_item_data['cake_writing'] = $cake_writing;
  }
  if (!empty($multiple_options_attr)) {
    foreach ($multiple_options_attr as $attr_slug => $options) {
      if (count($options) < 2)
        continue;

      $cart_item_data['multiple_options_attributes'][$attr_slug] = $options;
    }
  }

  $variation = [];

  foreach ($cart_item_data as $option_key => $values) {
    if (preg_match("/^attribute*/", $option_key)) {
      $variation[$option_key] = $values;
    }
  }

  $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
  $added_to_cart = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $cart_item_data);

  if ($passed_validation && $added_to_cart) {
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
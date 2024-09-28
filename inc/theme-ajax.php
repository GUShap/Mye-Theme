<?php

if (!defined('ABSPATH')) {
    exit;
}

// ajax for setting item data in session, before adding to cart

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
    wp_send_json_success($response);
}

add_action('wp_ajax_add_attributes_to_item', 'add_attributes_to_item');
add_action('wp_ajax_nopriv_add_attributes_to_item', 'add_attributes_to_item');
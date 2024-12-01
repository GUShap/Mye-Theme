<?php

if (!defined('ABSPATH')) {
    exit;
}
/***PRODUCT***/
function add_custom_data_button()
{
    echo '<button type="button" class="custom-data-toggle-button">עיצוב עוגה</button>';
}
add_action('woocommerce_after_variations_table', 'add_custom_data_button', 0, 9);

function start_custom_data_container()
{
    echo '<div class="custom-data-container">';
}
add_action('woocommerce_after_variations_table', 'start_custom_data_container', 0, 10);

function add_custom_design()
{
    // Retrieve custom attributes using the current post ID
    $custom_attributes = get_field('attribute_options', get_the_ID());
    $enable_custom_text = !empty(get_field('enable_text', get_the_ID())); // Boolean value

    // Prepare attributes string as a JSON encoded string
    // $attributes_string = esc_attr(json_encode($custom_attributes));

    // Construct the shortcode with JSON encoded attributes and boolean value for enable_custom_text
    echo do_shortcode('[customize_design product_id="' . get_the_ID() . '" custom_attributes="' . implode(',', $custom_attributes) . '" enable_custom_text="' . $enable_custom_text . '"]');
}
add_action('woocommerce_after_variations_table', 'add_custom_design', 0, 11);

function end_custom_data_container()
{
    echo '</div>';
}
add_action('woocommerce_after_variations_table', 'end_custom_data_container', 0, 15);

function add_allergens_to_product()
{
    $allergies_list = get_field('allergies_to_reffer', get_the_ID());
    $product_allergens_list_template_path = HE_CHILD_THEME_DIR . '/templates/product/allergenes-list.php';
    if (file_exists($product_allergens_list_template_path)) {
        include $product_allergens_list_template_path;
    }
}
add_action('woocommerce_after_variations_table', 'add_allergens_to_product', 10, 20);

function add_price_addition_input()
{
    echo '<input type="hidden" name="added_price" id="added-price" value="0">';
    echo '<input type="hidden" name="custom_attributes" id="custom-attributes" value="">';
}
add_action('woocommerce_after_add_to_cart_button', 'add_price_addition_input', 0, 21);

function custom_variable_product_price($price, $product)
{
    if ($product->is_type('variable')) {
        $variation_prices = $product->get_variation_prices(true);
        $min_price = current($variation_prices['price']);
        $max_price = end($variation_prices['price']);

        // You can customize the price range text here
        $price = sprintf('החל מ- %s', wc_price($min_price));
    }
    return $price;
}
add_filter('woocommerce_variable_sale_price_html', 'custom_variable_product_price', 10, 2);
add_filter('woocommerce_variable_price_html', 'custom_variable_product_price', 10, 2);

function add_label_as_data_attribute_to_variation_select($html, $args)
{
    // Get the attribute label
    $attribute_slug = $args['attribute'] ?? '';
    if ($attribute_slug) {
        $attribute_label = wc_attribute_label($attribute_slug);

        // Modify the select tag to add the data-attribute-label
        $html = str_replace('<select', '<select data-label="' . esc_attr($attribute_label) . '"', $html);
    }

    return $html;
}
add_filter('woocommerce_dropdown_variation_attribute_options_html', 'add_label_as_data_attribute_to_variation_select', 10, 2);

/********************/

/*** CART ***/
function update_cart_item_price($cart_object)
{
    foreach ($cart_object->cart_contents as $cart_item_key => $cart_item) {
        $price = +$cart_item['data']->get_price();
        $added_price = $cart_item['added_price'] ?? 0;
        $cart_item['data']->set_price($price + $added_price);
    }
}
add_action('woocommerce_before_calculate_totals', 'update_cart_item_price', 10, 1);

function custom_modify_item_attributes($item_data, $cart_item)
{
    if ($cart_item['variation_id'] > 0) {
        foreach ($item_data as $idx => $item) {
            if ($item['key'] == 'טבעוני' || $item['key'] == 'ללא גלוטן') {
                if ($item['value'] == 'לא טבעוני' || $item['value'] == 'רגיל')
                    unset($item_data[$idx]);
                else {
                    $item_data[$idx]['key'] = $item['key'] == 'טבעוני'
                        ? '<vegan>' . $item_data[$idx]['key']
                        : '<gluten>' . $item_data[$idx]['key'];
                    $item_data[$idx]['value'] = '';
                }
            }
        }
    }
    return $item_data;
}
add_filter('woocommerce_get_item_data', 'custom_modify_item_attributes', 10, 2);

function add_custom_data_to_order_item($item, $cart_item_key, $values, $order)
{
    // Retrieve custom data from the cart item
    if (isset($values['allergen_list'])) {
        $item->add_meta_data('allergen_list', $values['allergen_list'], true);
    }
    if (isset($values['custom_attributes'])) {
        $item->add_meta_data('custom_attributes', $values['custom_attributes'], true);
    }
    if (isset($values['multiple_options_attributes'])) {
        $item->add_meta_data('multiple_options_attributes', $values['multiple_options_attributes'], true);
    }
    return $item;
}
add_filter('woocommerce_checkout_create_order_line_item', 'add_custom_data_to_order_item', 10, 4);

function set_custom_item_thumbnail($thumbnail, $cart_item, $cart_item_key)
{
    $custom_attributes_terms = $cart_item['custom_attributes'] ?? [];
    if (!empty($custom_attributes_terms)) {
        $thumbnail_html = '<div class="cart-item-thumbnail-gallery">';
        foreach ($custom_attributes_terms as $term_value_data) {
            foreach ($term_value_data as $key => $value_set) {
                if (!empty($value_set['image_src'])) {
                    $thumbnail_html .= '<img src="' . $value_set['image_src'] . '" alt="custom-image">';
                }
            }
        }
        $thumbnail_html .= '</div>';
        return $thumbnail_html;
    } else {
        return $thumbnail;
    }
}
add_filter('woocommerce_cart_item_thumbnail', 'set_custom_item_thumbnail', 10, 3);

function add_custom_data_info($cart_item, $cart_item_key)
{
    set_custom_variation_attr_list($cart_item);
    set_item_custom_information($cart_item);
}
add_action('woocommerce_after_cart_item_name', 'add_custom_data_info', 10, 2);

/***CHECKOUT***/
add_filter('woocommerce_cart_item_permalink', '__return_false');

function add_custom_data_info_checkout($quantity, $cart_item, $cart_item_key)
{
    // dd($cart_item);
    ob_start();
    set_item_custom_information($cart_item);
    $custom_data_info = ob_get_clean();
    // echo $custom_data_info;
    echo "$quantity$custom_data_info";
    return $quantity;
}
add_filter('woocommerce_checkout_cart_item_quantity', 'add_custom_data_info_checkout', 10, 3);

function add_variation_info($quantity, $cart_item, $cart_item_key)
{
    ob_start();
    set_custom_variation_attr_list($cart_item);
    return ob_get_clean();
}
add_filter('woocommerce_checkout_cart_item_quantity', 'add_variation_info', 11, 3);

function filter_woocommerce_get_item_data($item_data, $cart_item)
{
    return null;
}

// add the filter 
add_filter('woocommerce_get_item_data', 'filter_woocommerce_get_item_data', 10, 2);

function hide_shipping_when_free_shipping_is_available($show_shipping)
{
    // Hide shipping by returning false (prevents shipping from being shown)
    return false;
}
add_filter('woocommerce_cart_ready_to_calc_shipping', 'hide_shipping_when_free_shipping_is_available', 99);

function custom_pickup_information()
{
    $totals_pickup_information_template_path = HE_CHILD_THEME_DIR . '/templates/cart/totals-pickup-information.php';

    if (file_exists($totals_pickup_information_template_path)) {
        include $totals_pickup_information_template_path;
    }
}
add_action('woocommerce_proceed_to_checkout', 'custom_pickup_information', 10);

// Remove shipping methods and shipping address form from the checkout page
function remove_shipping_details_on_checkout($checkout)
{
    if (is_checkout()) {
        remove_action('woocommerce_checkout_shipping', 'woocommerce_checkout_shipping', 10);
    }
}
add_action('woocommerce_before_checkout_form', 'remove_shipping_details_on_checkout');

function set_order_recipients($checkout)
{
    $recipients_details_template_path = HE_CHILD_THEME_DIR . '/templates/checkout/recipients-details.php';
    if (file_exists($recipients_details_template_path)) {
        include $recipients_details_template_path;
    }
}
add_action('woocommerce_after_checkout_billing_form', 'set_order_recipients', 10);

// pickup calendar
function set_product_pickup_selection()
{
    $pickup_date_template_path = HE_CHILD_THEME_DIR . '/templates/checkout/pickup-selection.php';
    if (file_exists($pickup_date_template_path)) {
        include $pickup_date_template_path;
    }
}
add_action('woocommerce_after_checkout_billing_form', 'set_product_pickup_selection', 11);

function validate_pickup_date()
{
    if (empty($_POST['pickup_date'])) {
        wc_add_notice(__('יש לבחור תאריך איסוף הזמנה', 'woocommerce'), 'error');
    }
}
add_action('woocommerce_checkout_process', 'validate_pickup_date');

/****************************/

/*** ORDER ***/
function save_custom_fields_value($order, $data)
{
    $is_other_recipients = !empty($_POST['is_other_recipients']);
    $form_recipients = $_POST['recipients'] ?? [];
    $recipients = [];

    $billing_email = $order->get_billing_email();
    $billing_phone = $order->get_billing_phone();
    $billing_first_name = $order->get_billing_first_name();
    $billing_last_name = $order->get_billing_last_name();

    $recipients[0] = [
        'name' => "$billing_first_name $billing_last_name",
        'email' => $billing_email,
        'phone' => $billing_phone,
        'status' => 'pending'
    ];

    if ($is_other_recipients) {
        foreach ($form_recipients as $recipient_idx => $recipient_data) {
            if (empty($recipient_data['name']) || empty($recipient_data['phone']))
                continue;
            $recipients[$recipient_idx + 1] = [
                'name' => $recipient_data['name'],
                'email' => $recipient_data['email'],
                'phone' => $recipient_data['phone'],
                'status' => 'pending'
            ];
        }
    }

    $order->update_meta_data('_order_recipients', $recipients);

    if (!empty($_POST['pickup_date'])) {
        $order->update_meta_data('pickup_date', $_POST['pickup_date']);
    }
}

add_action('woocommerce_checkout_create_order', 'save_custom_fields_value', 10, 2);

function notify_order_recipients($order_id)
{
    // Retrieve the '_order_recipients' meta as an array
    $order_recipients = get_post_meta($order_id, '_order_recipients', true);
    $messages_data = [];

    if (empty($order_recipients))
        return;
    // Check if there are recipients to process
    foreach ($order_recipients as $index => $recipient) {
        // Check if the recipient's status is 'pending'
        if (isset($recipient['status']) && $recipient['status'] !== 'pending')
            continue;
        // Extract the phone number
        $phone = $recipient['phone'];
        $message = "היי {$recipient['name']}, התקבלה אצלנו הזמנת קינוח עבורך, מספר הזמנה *{$order_id}* 🎂🎂🎂. כדי שכולם/ן יוכלו להנות עליך למלא טופס אישור רכיבים בקישור הבא https://shorturl.at/lOKpO";
        $messages_data[$phone] = $message;

    }
    // Run the WhatsApp function to send a message
    magic_wa_send_multiple_messages_handler($order_id, $messages_data);

    // send_multiple_messages($messages_data);
}

add_action('woocommerce_order_status_processing', 'notify_order_recipients', 10, 1);

/***** ORDER ADMIN ******/

// Add custom data to the .order_data_column section in the order details table.
function add_custom_order_data_as_column($order)
{
    // Get the custom data you want to display.
    $order_recipients = get_post_meta($order->get_id(), '_order_recipients', true);
    // foreach ($order_recipients as $resipient_key => $recipient) {
    //     $order_recipients[$resipient_key]['status'] = 'pending';
    // }
    // $order->update_status('pending');
    // $order->update_meta_data('_order_recipients', $order_recipients);
    // $order->save();

    // Check if the custom data exists.
    if (!empty($order_recipients)) {
        $order_recipients_list_template_path = HE_CHILD_THEME_DIR . '/templates/admin/order/order-recipients-list.php';
        if (file_exists($order_recipients_list_template_path)) {
            include $order_recipients_list_template_path;
        }
    }
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'add_custom_order_data_as_column');

function admin_order_item_headers()
{
    $column_1_name = 'תוספות';
    $column_2_name = 'אלרגיות';
    echo "<th style='text-align:center'><span style=\"color:green;font-weight:600;\">$column_1_name</span></th>";
    echo "<th><span style=\"color:red;font-weight:600;\">$column_2_name</span></th>";
}
add_action('woocommerce_admin_order_item_headers', 'admin_order_item_headers', 10);

function admin_order_item_values($_product, $item, $item_id = null)
{
    $custom_attributes = $item['custom_attributes'] ?? [];
    $allergens_list = $item['allergen_list'] ?? [];

    $custom_attributes_column_template_path = HE_CHILD_THEME_DIR . '/templates/admin/order/item-custom-attributes-column.php';
    $allergens_column_template_path = HE_CHILD_THEME_DIR . '/templates/admin/order/item-allergens-column.php';

    if (file_exists($custom_attributes_column_template_path)) {
        include $custom_attributes_column_template_path;
    }

    if (file_exists($allergens_column_template_path)) {
        include $allergens_column_template_path;
    }
}
add_action('woocommerce_admin_order_item_values', 'admin_order_item_values', 10, 3);

// Modify the product thumbnail in WooCommerce admin order details
function set_admin_order_item_thumbnail($thumbnail, $item_id, $item)
{
    return '';
}
add_filter('woocommerce_admin_order_item_thumbnail', 'set_admin_order_item_thumbnail', 10, 3);

// changing the column count in the WooCommerce admin order details table
function modify_admin_html_output($buffer)
{
    // Search for specific HTML and replace it
    $search_replace = [
        '<th class="item sortable" colspan="2" data-sort="string-ins">Item</th>' => '<th class="item sortable" colspan="1" data-sort="string-ins">Item</th>',
        '<th class="item sortable" colspan="2" data-sort="string-ins">פריט</th>' => '<th class="item sortable" colspan="1" data-sort="string-ins">פריט</th>',
    ];

    foreach ($search_replace as $search => $replace) {
        $buffer = str_replace($search, $replace, $buffer);
    }
    $buffer = preg_replace('/<td class="thumb">.*?<\/td>/s', '', $buffer);

    return $buffer;
}
function start_admin_html_buffer()
{
    ob_start('modify_admin_html_output');
}
function end_admin_html_buffer()
{
    ob_end_flush();
}
// Hook into the admin pages
add_action('admin_head', 'start_admin_html_buffer', 999);
add_action('admin_footer', 'end_admin_html_buffer', 999);

function wp_kama_woocommerce_before_order_itemmeta_action($item_id, $item, $null)
{

    $multiple_options_attributes = $item->get_meta('multiple_options_attributes');
    if (empty($multiple_options_attributes))
        return;

        foreach($multiple_options_attributes as $attr_slug => $options){
            $term_value = implode(', ', array_map(function ($option) use ($attr_slug) {
                $term = get_term_by('slug', $option, $attr_slug);
                return $term->name;
            }, $options));
            $item->update_meta_data($attr_slug, $term_value);
        }
        
    // dd($item->get_meta('pa_cupcake_base'));
}

add_action('woocommerce_before_order_itemmeta', 'wp_kama_woocommerce_before_order_itemmeta_action', 10, 3);

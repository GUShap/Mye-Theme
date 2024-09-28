<?php

/***PRODUCT***/
add_action('woocommerce_after_variations_table', 'add_custom_data_button', 0, 9);
function add_custom_data_button(){
    echo '<button type="button" class="custom-data-toggle-button">עיצוב עוגה</button>';
}

add_action('woocommerce_after_variations_table', 'start_custom_data_container', 0, 10);

function start_custom_data_container()
{
    echo '<div class="custom-data-container">';
}



add_action('woocommerce_after_variations_table', 'add_custom_design', 0, 11);
function add_custom_design()
{
    // Retrieve custom attributes using the current post ID
    $custom_attributes = get_field('attribute_options', get_the_ID());
    $enable_custom_text = !empty(get_field('enable_text', get_the_ID())); // Boolean value

    // Prepare attributes string as a JSON encoded string
    // $attributes_string = esc_attr(json_encode($custom_attributes));

    // Construct the shortcode with JSON encoded attributes and boolean value for enable_custom_text
    echo do_shortcode('[customize_design custom_attributes="' . implode(',', $custom_attributes) . '" enable_custom_text="' . $enable_custom_text . '"]');
}

// add_action('woocommerce_after_variations_table', 'add_custom_attributes_to_product', 0, 11);
function add_custom_attributes_to_product()
{
    $custom_attributes = get_field('attribute_options', get_the_ID());
    $custom_attributes_template_path = HE_CHILD_THEME_DIR . '/templates/product/custom-attributes.php';
    if (empty($custom_attributes))
        return;

    if (file_exists($custom_attributes_template_path)) {
        include $custom_attributes_template_path;
    }

}

// add_action('woocommerce_after_variations_table', 'add_customer_text_to_product', 0, 12);

function add_customer_text_to_product()
{
    $customer_text = [
        'enable_text' => !empty(get_field('enable_text', get_the_ID())),
        'text_attributes' => get_field('text_attributes', get_the_ID()),
    ];
    $customer_text_template_path = HE_CHILD_THEME_DIR . '/templates/product/customize-text.php';
    if (empty($customer_text) || !$customer_text['enable_text'])
        return;

    if (file_exists($customer_text_template_path)) {
        include $customer_text_template_path;
    }
}

add_action('woocommerce_after_variations_table', 'end_custom_data_container', 0, 15);

function end_custom_data_container()
{
    echo '</div>';
}

add_action('woocommerce_after_variations_table', 'add_allergens_to_product', 0, 20);
function add_allergens_to_product()
{
    $allergies_list = get_field('allergies_to_reffer', get_the_ID());
    $product_allergens_list_template_path = HE_CHILD_THEME_DIR . '/templates/product/allergenes-list.php';
    if (file_exists($product_allergens_list_template_path)) {
        include $product_allergens_list_template_path;
    }
}

add_filter('woocommerce_add_cart_item_data', 'custom_add_cart_item_data', 10, 4);
function custom_add_cart_item_data($cart_item_data, $product_id, $variation_id, $quantity)
{
    if (isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
        foreach ($_POST as $attr_label => $attr_value) {
            $is_custom_attr = str_contains($attr_label, 'gs_custom_');
            if ($is_custom_attr) {
                $attribute_price = get_field('attribute_price', $attr_value);
                $attribute_name = get_the_title($attr_value);
                $is_price_per_option = !empty(get_field('price_per_options', $attr_value)) ? true : false;
                $data_label = str_replace('gs_custom_', '', $attr_label);
                if ($is_price_per_option) {
                    $length = count(explode(',', $_POST[$data_label . '-data']));
                    $attribute_price *= $length;
                }
                $cart_item_data['custom_data'][$data_label] = [
                    'price' => $attribute_price,
                    'name' => '<' . $data_label . '>' . $attribute_name,
                    'options' => !empty($_POST[$data_label . '-data']) ? $_POST[$data_label . '-data'] : 'true'
                ];
                if ($data_label == 'theme' && !empty($attr_value)) {
                    $theme_name = $_POST['theme-data'];
                    $cart_item_data['custom_data']['theme_image'] = [
                        'name' => '<delete>קישור תמונה',
                        'options' => $_POST[$theme_name . '-sub-option']
                    ];
                    $cart_item_data['custom_data'][$data_label]['options'] = $_POST['theme-option'];
                }
                $cart_item_data[$data_label]['value'] = !empty($_POST[$data_label . '-data']) ? $_POST[$data_label . '-data'] : 'true';
            }
        }
        // Handles user text
        if (!empty($_POST['customer_text'])) {
            $cart_item_data['custom_data']['customer_text'] = [
                'name' => 'טקסט על הקינוח',
                'options' => $_POST['customer_text']
            ];
        }
        // Handles user uploaded image
        if (isset($_FILES['user-image-upload']) && !empty($_FILES['user-image-upload']['name'])) {
            $image_upload = $_FILES['user-image-upload'];

            // Check if it's a valid image file type
            $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml');
            if (in_array($image_upload['type'], $allowed_types)) {
                // Define the custom directory path
                $custom_upload_dir = 'users_uploaded_images'; // Change this to your desired directory name

                // Get the WordPress upload directory
                $upload_dir = wp_upload_dir();

                // Create the custom directory if it doesn't exist
                $custom_upload_path = $upload_dir['basedir'] . '/' . $custom_upload_dir;
                if (!file_exists($custom_upload_path)) {
                    wp_mkdir_p($custom_upload_path);
                }

                $original_filename = basename($image_upload['name']);

                // Replace spaces with underscores in the filename
                $cleaned_filename = str_replace(' ', '_', $original_filename);

                // Define the upload path within the custom directory
                $upload_path = $custom_upload_path . '/' . $cleaned_filename;

                // Move the uploaded file to the custom directory
                move_uploaded_file($image_upload['tmp_name'], $upload_path);

                // Store the cleaned image URL in the cart item data
                $custom_image_url = $upload_dir['baseurl'] . '/' . $custom_upload_dir . '/' . $cleaned_filename;
                $cart_item_data['custom_data']['theme']['options'] = 'תמונת משתמש/ת';
                $cart_item_data['custom_data']['theme_image']['options'] = $custom_image_url;
            }
        }
        $cart_item_data['allergens_list'] = $_POST['allergens-for-product'];
    }
    return $cart_item_data;
}
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

/***CART & CHECKOUT***/
function update_cart_item_price($cart_object)
{
    foreach ($cart_object->cart_contents as $cart_item_key => $cart_item) {
        $price = +$cart_item['data']->get_price();
        if (isset($cart_item['custom_data'])) {
            foreach ($cart_item['custom_data'] as $data_item) {
                if (!empty($data_item['price'])) {
                    $price += +$data_item['price'];
                }
            }
        }
        $cart_item['data']->set_price($price);
    }
}
add_action('woocommerce_before_calculate_totals', 'update_cart_item_price', 10, 1);

function custom_modify_item_attributes($item_data, $cart_item)
{
    if ($cart_item['variation_id'] > 0) {

        $counter = 0;
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

        foreach ($cart_item['custom_data'] as $custom_data_label => $custom_data_value) {
            $item_index = count($item_data) + $counter;
            $item_data[$item_index]['key'] = $custom_data_value['name'];
            $item_data[$item_index]['value'] = $custom_data_value['options'] !== 'true' ? $custom_data_value['options'] : 'כן';
            // if($custom_data_label === 'theme') $item_data[$item_index]['value'] = $custom_data_value['thumbnail_url'];
            $counter++;
        }
        return $item_data;
    }
}
add_filter('woocommerce_get_item_data', 'custom_modify_item_attributes', 10, 2);

function add_custom_data_to_order_item($item, $cart_item_key, $values, $order)
{
    // Retrieve custom data from the cart item
    if (isset($values['custom_data'])) {
        $custom_data = $values['custom_data'];
        // Add the custom data as order item meta
        foreach ($custom_data as $label => $data_item) {
            $value = $data_item['options'] !== 'true' ? $data_item['options'] : '';
            $item->add_meta_data($data_item['name'], $value, true);
        }

        $item->add_meta_data('<span class="allergens_list">אלרגיות</span>', $values['allergens_list'], true);
    }
    return $item;
}
add_filter('woocommerce_checkout_create_order_line_item', 'add_custom_data_to_order_item', 10, 4);


add_filter('woocommerce_cart_item_thumbnail', function ($thumbnail, $cart_item, $cart_item_key) {
    if (!empty($cart_item['custom_data']['theme'])) {
        // Replace this with your custom logic to determine the new image URL
        $new_image_url = $cart_item['custom_data']['theme_image']['options'];
        if (empty($new_image_url))
            return;
        // Create a new image HTML tag
        $new_thumbnail = '<img src="' . $new_image_url . '" alt="' . esc_attr($cart_item['data']->get_name()) . '">';

        return $new_thumbnail;
    }
}, 10, 3);

add_action('woocommerce_init', 'shipping_instance_form_fields_filters');

function shipping_instance_form_fields_filters()
{
    $shipping_methods = WC()->shipping->get_shipping_methods();
    foreach ($shipping_methods as $shipping_method) {
        add_filter('woocommerce_shipping_instance_form_fields_' . $shipping_method->id, 'shipping_instance_form_add_extra_fields');
    }
}

function shipping_instance_form_add_extra_fields($settings)
{
    $settings['shipping_zone_cities'] = [
        'title' => 'יישובים',
        'type' => 'text',
        'placeholder' => '',
        'description' => 'רשימת יישובים לאזור משלוח זה'
    ];

    return $settings;
}

add_action('woocommerce_after_checkout_billing_form', 'add_custom_checkout_fields');

function add_custom_checkout_fields($checkout)
{
    $recipients_details_template_path = HE_CHILD_THEME_DIR . '/templates/checkout/recipients-details.php';
    if (file_exists($recipients_details_template_path)) {
        include $recipients_details_template_path;
    }
}


add_filter('woocommerce_cart_shipping_method_full_label', 'change_cart_shipping_method_full_label', 10, 2);
function change_cart_shipping_method_full_label($label, $method)
{
    $method_id = $method->get_method_id();
    $instance_id = $method->get_instance_id();
    $method_settings = get_option('woocommerce_' . $method_id . '_' . $instance_id . '_settings');
    ?>
    <?php if (!empty($method_settings['shipping_zone_cities'])) { ?>
        <input type="hidden" name="zone-cities-list" id="<?php echo $method_id . '_' . $instance_id ?>"
            value="<?php echo $method_settings['shipping_zone_cities'] ?>">
    <?php } ?>
    <?php return $label;
}
/*** ORDER ***/

add_action('woocommerce_checkout_create_order', 'save_recipients_field_value', 10, 2);

function save_recipients_field_value($order, $data)
{
    if ($_POST['is_other_recipients'] !== 'true')
        return;

    unset($_POST['is_other_recipients']);

    $recipients = [];

    foreach ($_POST as $item_key => $item_value) {
        if (strpos($item_key, 'recipient') === 0) {
            $recipient_order = preg_match('/\d/', $item_key, $matches)
                ? reset($matches)
                : 1;

            $detail_type = '';

            if (strpos($item_key, 'name') !== false) {
                $detail_type = 'name';
            } elseif (strpos($item_key, 'email') !== false) {
                $detail_type = 'email';
            } elseif (strpos($item_key, 'phone') !== false) {
                $detail_type = 'phone';
            }
            // Move the item to the $recipientArray
            $recipients['recipient_' . $recipient_order][$detail_type] = $item_value;
            // Remove the item from the $inputArray if needed
            unset($_POST[$item_key]);
        }
    }
    $order->update_meta_data('_order_recipients', $recipients);
}


/***** ORDER ADMIN ******/

// Add custom data to the .order_data_column section in the order details table.
add_action('woocommerce_admin_order_data_after_shipping_address', 'add_custom_order_data_as_column');

function add_custom_order_data_as_column($order)
{
    // Get the custom data you want to display.
    $order_recipients = get_post_meta($order->get_id(), '_order_recipients', true);

    // Check if the custom data exists.
    if (!empty($order_recipients)) {
        $order_recipients_list_template_path = HE_CHILD_THEME_DIR . '/templates/admin/order/order-recipients-list.php';
        if (file_exists($order_recipients_list_template_path)) {
            include $order_recipients_list_template_path;
        }
    }
}


add_action('woocommerce_admin_order_item_headers', 'admin_order_item_headers');
function admin_order_item_headers()
{
    $column_name = 'אלרגיות';
    echo '<th><span style="color:red;font-weight:600;">' . $column_name . '</span></th>';
}

add_action('woocommerce_admin_order_item_values', 'admin_order_item_values', 10, 3);
function admin_order_item_values($_product, $item, $item_id = null)
{
    $allergens_list = explode(',', $item['<span class="allergens_list">אלרגיות</span>']);
    $allergens_column_template_path = HE_CHILD_THEME_DIR . '/templates/admin/order/item-allergens-column.php';
    if (file_exists($allergens_column_template_path)) {
        include $allergens_column_template_path;
    }
}
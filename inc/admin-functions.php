<?php
function make_array_shallow($array)
{
    $shallowArray = array();

    foreach ($array as $value) {
        if (is_array($value)) {
            $shallowArray = array_merge($shallowArray, make_array_shallow($value));
        } else {
            $shallowArray[] = $value;
        }
    }

    return $shallowArray;
}

function dd($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

function flatten_array($array)
{
    $flatArray = [];

    foreach ($array as $item) {
        if (is_array($item)) {
            // Recursively flatten the array
            $flatArray = array_merge($flatArray, flatten_array($item));
        } else {
            // If not an array, add the value directly
            $flatArray[] = $item;
        }
    }

    return $flatArray;
}

// Function to save base64 image as a file in WordPress uploads directory
function save_user_image_as_file($base64_image)
{
    // Ensure the base64 string is valid and properly formatted
    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
        $base64_image = substr($base64_image, strpos($base64_image, ',') + 1);
        $base64_image = base64_decode($base64_image);

        // Generate a unique filename
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'] . '/custom_clients_images/';

        // Create the directory if it doesn't exist
        if (!file_exists($upload_path)) {
            wp_mkdir_p($upload_path);
        }

        // Generate a unique filename
        $image_type = strtolower($type[1]); // jpeg, png, etc.
        $file_name = 'custom-image-' . wp_generate_password(8, false) . '.' . $image_type;
        $file_path = $upload_path . $file_name;

        // Save the file
        if (file_put_contents($file_path, $base64_image)) {
            // Prepare an array of attachment data
            $filetype = wp_check_filetype(basename($file_path), null);
            $attachment = array(
                'guid' => $upload_dir['url'] . '/custom_clients_images/' . basename($file_path),
                'post_mime_type' => $filetype['type'],
                'post_title' => sanitize_file_name($file_name),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            // Insert the attachment post into the database
            $attach_id = wp_insert_attachment($attachment, $file_path);

            // Generate attachment metadata and update the database
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // Return the attachment ID (or you can return the URL)
            return $attach_id; // Or wp_get_attachment_url($attach_id) to return the URL
        } else {
            // Failed to save the image
            return false;
        }
    } else {
        // Invalid base64 string
        return false;
    }
}

function create_image_from_text($text, $name, $font_size = 20, $font_color = [0, 0, 0], $bg_color = [255, 255, 255])
{
    // Define the font path
    $font_path = HE_CHILD_THEME_DIR . 'assets/fonts/RubikDirt-Regular.ttf';

    // Sanitize the text to create a valid filename
    $sanitized_text = sanitize_title($name); // Converts to a URL-friendly string
    $upload_dir = wp_upload_dir();
    $temp_dir = $upload_dir['basedir'] . '/temp-images/'; // Path to store temporary images
    $image_path = "{$temp_dir}image_$sanitized_text.png"; // Create a consistent filename

    // Check if the image already exists
    if (file_exists($image_path)) {
        // Return the existing image id
        $url = $upload_dir['baseurl'] . '/temp-images/image_' . $sanitized_text . '.png';
        // dd($url);
        return attachment_url_to_postid($url);
    }
    $is_rtl = (bool) preg_match('/[\x{0591}-\x{07FF}]/u', $text); // Check for Hebrew or Arabic characters
    $text = $is_rtl
        ? mb_strrev($text) // Reverse the text if it's RTL
        : $text;
    // Set the dimensions of the image
    $width = 300;
    $height = 100;

    // Create a blank image
    $image = imagecreatetruecolor($width, $height);

    // Allocate colors
    $bg_color_alloc = imagecolorallocate($image, $bg_color[0], $bg_color[1], $bg_color[2]);
    $font_color_alloc = imagecolorallocate($image, $font_color[0], $font_color[1], $font_color[2]);

    // Fill the background color
    imagefilledrectangle($image, 0, 0, $width, $height, $bg_color_alloc);

    // Add the text to the image
    $text_bounding_box = imagettfbbox($font_size, 0, $font_path, $text);
    $text_width = $text_bounding_box[2] - $text_bounding_box[0];
    $text_height = $text_bounding_box[1] - $text_bounding_box[7];


    // Center vertically
    $y = ($height - $text_height) / 2 + $text_height;
    $x = ($width - $text_width) / 2;

    // Add text to the image
    imagettftext($image, $font_size, 0, $x, $y, $font_color_alloc, $font_path, $text);

    // Create the temporary directory if it doesn't exist
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0755, true); // Create the directory
    }

    // Save the image to the temporary directory
    imagepng($image, $image_path);
    imagedestroy($image);

    // Check if the image file was created successfully
    if (!file_exists($image_path)) {
        error_log('Image not created: ' . $image_path);
    }

    $attachment = array(
        'guid' => $upload_dir['url'] . '/temp-images/' . basename($image_path),
        'post_mime_type' => 'image/png',
        'post_title' => $text,
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attachment_id = wp_insert_attachment($attachment, $image_path);

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attachment_id, $image_path);
    wp_update_attachment_metadata($attachment_id, $attach_data);
    // Return the URL of the generated image
    return $attachment_id;
}

function mb_strrev($text)
{
    $length = mb_strlen($text);
    $reversed = '';
    while ($length-- > 0) {
        $reversed .= mb_substr($text, $length, 1);
    }
    return $reversed;
}

function get_pickup_orders()
{
    $orders_data = [];

    // Query for WooCommerce orders with status "processing" or "completed"
    $args = [
        'status' => ['processing', 'completed'],
        'limit' => -1, // No limit, retrieves all matching orders
    ];

    $orders = wc_get_orders($args);

    foreach ($orders as $order) {
        $pickup_date = $order->get_meta('pickup_date'); // Get the pickup_date meta
        $order_id = $order->get_id();
        if ($pickup_date) {
            $formatted_date = date('Y-m-d', strtotime($pickup_date));

            // Prepare data for this order
            $order_data = [
                'order_id' => $order_id,
                'order_link' => admin_url("post.php?post={$order_id}&action=edit"),
                'status' => $order->get_status(),
                'items' => []
            ];

            // Loop through order items to check if they require pickup
            foreach ($order->get_items() as $item_id => $item) {
                $product_id = $item->get_product_id();
                if (empty(get_field('require_pickup', $product_id)))
                    continue;// Check ACF field 'require_pickup'
                if (empty($order_data['items'][$product_id])) {
                    $order_data['items'][$product_id] = [
                        'name' => $item->get_name(),
                        'qty' => 0,
                    ];
                }
                $current_qty = $order_data['items'][$product_id]['qty'];
                $order_data['items'][$product_id]['qty'] = $current_qty + $item->get_quantity();
            }

            // If there are items requiring pickup, add this order to the array
            if (!empty($order_data['items'])) {
                $orders_data[$formatted_date][] = $order_data;
            }
        }
    }

    // Sort orders by pickup date in ascending order
    ksort($orders_data);

    return $orders_data;
}

function get_pickup_order_items_count()
{
    $args = [
        'status' => ['processing'],
        'limit' => -1, // No limit, retrieves all matching orders
    ];

    $orders = wc_get_orders($args);
    $dates_counter = [];
    foreach ($orders as $order) {
        $pickup_date = $order->get_meta('pickup_date'); // Get the pickup_date meta
        if ($pickup_date) {
            $formatted_date = date('Y-m-d', strtotime($pickup_date));
            if (empty($dates_counter[$formatted_date])) {
                $dates_counter[$formatted_date] = 0;
            }
            foreach ($order->get_items() as $item_id => $item) {
                $product_id = $item->get_product_id();
                if (empty(get_field('require_pickup', $product_id)))
                    continue;// Check ACF field 'require_pickup'
                $dates_counter[$formatted_date] += $item->get_quantity();
            }
        }
    }

    return $dates_counter;
}

function get_pickup_cart_items_count()
{
    $cart = WC()->cart;
    $cart_items = $cart->get_cart();
    $items_counter = 0;
    foreach ($cart_items as $cart_item) {
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];
        $is_pickup_item = !empty(get_field('require_pickup', $product_id));
        if ($is_pickup_item) {
            $items_counter += $quantity;
        }
    }

    return $items_counter;
}

function fill_calendar_dates_by_month($dates_data)
{
    if (empty($dates_data)) {
        return [];
    }

    // Get the first and last dates in the data
    $dates = array_keys($dates_data);
    sort($dates); // Sort dates to get the earliest and latest
    $first_date = new DateTime($dates[0]);
    $last_date = new DateTime($dates[count($dates) - 1]);

    // Set to the beginning of the first month and end of the last month
    $first_date->modify('first day of this month');
    $last_date->modify('last day of this month');

    $calendar_data = [];

    // Iterate from the start to end date, filling missing dates
    $current_date = clone $first_date;
    while ($current_date <= $last_date) {
        $date_key = $current_date->format('Y-m-d');
        $month_key = $current_date->format('Y-m'); // "yyyy-mm" for month grouping

        // Initialize the month if it doesn't exist
        if (!isset($calendar_data[$month_key])) {
            $calendar_data[$month_key] = [];
        }

        // Add date to the respective month, with order data or empty array
        $calendar_data[$month_key][$date_key] = $dates_data[$date_key] ?? [];

        $current_date->modify('+1 day'); // Move to the next day
    }

    return $calendar_data;
}
function format_hebrew_date($date)
{
    $date_obj = new DateTime($date);
    return $date_obj->format('j');
}

function get_hebrew_month_name($month)
{
    $hebrew_months = [
        "01" => "ינואר",
        "02" => "פברואר",
        "03" => "מרץ",
        "04" => "אפריל",
        "05" => "מאי",
        "06" => "יוני",
        "07" => "יולי",
        "08" => "אוגוסט",
        "09" => "ספטמבר",
        "10" => "אוקטובר",
        "11" => "נובמבר",
        "12" => "דצמבר"
    ];
    return $hebrew_months[$month] ?? '';
}

function get_hebrew_days_of_week()
{
    return ["ראשון", "שני", "שלישי", "רביעי", "חמישי", "שישי", "שבת"];
}

function get_day_of_week_number($date)
{
    $date_obj = new DateTime($date);
    // Returns day of the week as 1 (Sunday) to 7 (Saturday)
    $day_of_week = $date_obj->format('N'); // 1 (Monday) to 7 (Sunday)
    return $day_of_week === '7' ? 1 : $day_of_week + 1; // Adjust for Sunday as 1
}

add_action('template_redirect', function () {
    $order_id = 12900;
    $order_recipients = get_post_meta($order_id, '_order_recipients', true);

    // dd($order_recipients);
//     $message = "היי גיא, התקבלה אצלנו הזמנת קינוח עבורך, מספר הזמנה *12540*. כדי שכולם/ן יוכלו להנות עליך למלא טופס אישור רכיבים בקישור הבא https://shorturl.at/lOKpO";
//     // dd(get_rest_url());
//     send_message_to_multiple_recipients(['0545970911', '0526033388'], $message);
});
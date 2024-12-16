<?php
if (!defined('ABSPATH')) {
    exit;
}

/************************/
function display_sweet_management_page()
{

    if (!current_user_can('manage_options')) {
        return;
    }
    $orders_data = get_pickup_orders() ?? [];
    $workshop_data = get_option('workshop_data') ?? [];
    $managment_page_template_path = HE_CHILD_THEME_DIR . 'templates/admin/managment/managment-page.php';
    $calendar_tamplate_path = HE_CHILD_THEME_DIR . 'templates/calendar.php';
    if (file_exists($managment_page_template_path)) {
        require_once $managment_page_template_path;
    }
}
function register_sweet_management_menu()
{
    add_menu_page(
        'Sweet Management',            // Page title
        'Sweet Management',            // Menu title
        'manage_options',              // Capability required to access the page
        'sweet_management',            // Menu slug
        'display_sweet_management_page', // Function to display the page content
        'dashicons-carrot',            // Icon (dashicons-carrot is an example icon)
        25                             // Position in the menu
    );
}

add_action('admin_menu', 'register_sweet_management_menu');

// add submenu item of "settings"
function display_sweet_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $settings_page_template_path = HE_CHILD_THEME_DIR . 'templates/admin/managment/settings-page.php';
    if (file_exists($settings_page_template_path)) {
        require_once $settings_page_template_path;
    }
}

function register_sweet_settings_submenu()
{
    add_submenu_page(
        'sweet_management',            // Parent menu slug
        'Sweet Settings',              // Page title
        'Settings',                    // Menu title
        'manage_options',              // Capability required to access the page
        'sweet_settings',              // Menu slug
        'display_sweet_settings_page'  // Function to display the page content
    );
}

add_action('admin_menu', 'register_sweet_settings_submenu');

// when saving the settings
function save_sweet_settings_handle()
{
    $ingredients_form_url = $_POST['ingredients_form_url'] ?? '';
    update_option('ingredients_form_url', $ingredients_form_url);
}

add_action('admin_save_sweet_settings', 'save_sweet_settings_handle');
/************************/
function register_custom_widgets($widgets_manager)
{
    require_once HE_CHILD_THEME_DIR . 'inc/elementor/widgets/product-widgets.php'; // Ensure this path is correct
    $widgets_manager->register_widget_type(new \Elementor\Pretty_Product_Gallery_Widget());
}
add_action('elementor/widgets/register', 'register_custom_widgets', 10, 1);

// Hook to the scheduled action to delete a specific image

function delete_image_by_id($image_id)
{
    if (!empty($image_id)) {
        wp_delete_attachment($image_id, true);
    }
}
add_action('delete_user_custom_image', 'delete_image_by_id', 10, 1);

/************************/

function enqueue_custom_admin_script()
{
    // Enqueue the custom script
    wp_enqueue_script('custom-admin-script', HE_CHILD_THEME_URI . '/assets/js/admin-script.js', array('jquery'), time(), true);
    wp_enqueue_style('custom-admin-style', HE_CHILD_THEME_URI . '/assets/css/admin-style.css', array(), time(), 'all');
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_script');

function enqueue_custom_script()
{
    if (is_product()) {
        // $cake_data = get_bases_data();
        $siteConfig = [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'productId' => get_the_ID()
        ];

        wp_enqueue_script('jquery-ui', 'https://code.jquery.com/ui/1.14.0/jquery-ui.min.js', array('jquery'), '1.14.0');
        wp_enqueue_script('touch-punch', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js', array('jquery', 'jquery-ui', 'jquery-ui-draggable'), '0.2.3');
        wp_enqueue_script('htmltocanvas', 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js', array('jquery'), '1.4.1');
        wp_enqueue_script('jscolor-lib', HE_CHILD_THEME_URI . '/assets/lib/color-picker/jscolor.min.js', array('jquery'), '2.3.6');
        wp_enqueue_script('single-product-script', HE_CHILD_THEME_URI . '/assets/js/single-product-script.js', array('jquery', 'slick'), time(), true);
        wp_localize_script('single-product-script', 'siteConfig', $siteConfig);
        wp_enqueue_style('single-product-style', HE_CHILD_THEME_URI . '/assets/css/variable-product-style.css', array(), time(), 'all');

    }
    if (is_singular('gallery')) {
        // wp_enqueue_style( 'single-gallery-style', HE_CHILD_THEME_URI . '/assets/css/single-gallery-style.css', array(), time(), 'all' );
        // wp_enqueue_script( 'single-gallery-script', HE_CHILD_THEME_URI . '/assets/js/single-gallery-script.js', array(), time(), true );
    }

    if (is_cart()) {
        wp_enqueue_script('custom-cart-script', HE_CHILD_THEME_URI . '/assets/js/cart-script.js', array(), time(), true);
        wp_enqueue_style('custom-cart-style', HE_CHILD_THEME_URI . '/assets/css/cart-style.css', array(), time(), 'all');
    }
    if (is_checkout()) {
        // Enqueue the Hebrew locale file for Flatpickr from CDN.
        // wp_enqueue_script('flatpickr-hebrew-locale', 'https://npmcdn.com/flatpickr/dist/l10n/he.js', array('flatpickr'), null, true);
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_enqueue_style('custom-checkout-style', HE_CHILD_THEME_URI . '/assets/css/checkout-style.css', array(), time(), 'all');
        wp_enqueue_script('custom-checkout-script', HE_CHILD_THEME_URI . '/assets/js/checkout-script.js', array('jquery'), time(), true);
        wp_localize_script('custom-checkout-script', 'checkout_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('checkout_nonce'),
            'pickup_dates' => get_pickup_order_items_count(),
            'pickup_dates_limit' => get_field('max_pickup_items', 'option'),
            'pickup_items_count' => get_pickup_cart_items_count(),
        ]);

    }
    wp_enqueue_script('theme-front', HE_CHILD_THEME_URI . '/assets/js/theme-front.js', array('jquery'), time(), true);
    wp_enqueue_style('theme-front', HE_CHILD_THEME_URI . '/assets/css/theme-front.css', array(), time(), 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_script');
// load scripts and style of slick slider
function load_slick_slider()
{
    wp_enqueue_script('slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', ['jquery'], '1.9.0', true);
    wp_enqueue_script('slick-lightbox', 'https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js', ['jquery'], time(), true);
    wp_enqueue_style('slick-style', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css', [], '1.9.0');
    wp_enqueue_style('slick-style-2', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css', [], '1.9.0');
    wp_enqueue_style('slick-lightbox-style', 'https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css', [], time());
}
add_action('wp_enqueue_scripts', 'load_slick_slider');

function enqueue_google_fonts()
{
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Rubik+Dirt&display=swap');
}
add_action('wp_enqueue_scripts', 'enqueue_google_fonts');

// scheduled actions
function magic_wa_send_message_handler($order_id, $recipient_index, $phone, $message)
{
    // Format phone number if it starts with '0'
    if (strpos($phone, '0') === 0) {
        $phone = '972' . substr($phone, 1);
    }

    // Send the WhatsApp message
    $result = send_whatsapp_message($phone, $message);

    // Check if message was sent successfully
    if (!is_wp_error($result)) {
        // Update the recipient's status in order meta
        $order_recipients = get_post_meta($order_id, '_order_recipients', true);

        if (!empty($order_recipients) && isset($order_recipients[$recipient_index])) {
            $order_recipients[$recipient_index]['status'] = 'waiting';
            $order_recipients[$recipient_index]['message_time'] = time();
            update_post_meta($order_id, '_order_recipients', $order_recipients);
        }
    }
}
add_action('magic_wa_send_message_scheduled', 'magic_wa_send_message_handler', 10, 4);

function magic_wa_message_multiple_recipients_handler($order_id, $recipients, $message)
{
    $result = send_message_to_multiple_recipients($recipients, $message);
    if (!is_wp_error($result)) {
        $order_recipients = get_post_meta($order_id, '_order_recipients', true);
        foreach ($recipients as $recipient) {
            $recipient_index = $recipient['index'];
            if (!empty($order_recipients) && isset($order_recipients[$recipient_index])) {
                $order_recipients[$recipient_index]['status'] = 'waiting';
                $order_recipients[$recipient_index]['message_time'] = time();
            }
        }
        update_post_meta($order_id, '_order_recipients', $order_recipients);
    }
}
add_action('magic_wa_message_multiple_recipients_schedule', 'magic_wa_message_multiple_recipients_handler', 10, 4);

function magic_wa_send_multiple_messages_handler($order_id, $messages_data)
{
    $result = send_multiple_messages($messages_data);

    if (!is_wp_error($result)) {
        $order_recipients = get_post_meta($order_id, '_order_recipients', true);
        foreach ($order_recipients as $recipient_idx => $recipient_data) {
            if (!empty($order_recipients) && isset($order_recipients[$recipient_idx])) {
                $order_recipients[$recipient_idx]['status'] = 'waiting';
                $order_recipients[$recipient_idx]['message_time'] = time();
            }
        }
        update_post_meta($order_id, '_order_recipients', $order_recipients);
    }
}
add_action('magic_wa_send_multiple_messages_schedule', 'magic_wa_send_multiple_messages_handler', 10, 2);

add_action('template_redirect', function () {
    $order_id = 12909;
    $order_recipients = get_post_meta($order_id, '_order_recipients', true);
    // dd($order_recipients);
    // $messages_data = [];

    // if (empty($order_recipients))
    //     return;
    // // Check if there are recipients to process
    // foreach ($order_recipients as $index => $recipient) {
    //     // Check if the recipient's status is 'pending'
    //     if (isset($recipient['status']) && $recipient['status'] !== 'pending')
    //         continue;
    //     // Extract the phone number
    //     $phone = $recipient['phone'];
    //     $message = "היי {$recipient['name']}, התקבלה אצלנו הזמנת קינוח עבורך, מספר הזמנה *{$order_id}* 🎂🎂🎂. כדי שכולם/ן יוכלו להנות עליך למלא טופס אישור רכיבים בקישור הבא https://shorturl.at/lOKpO";
    //     $messages_data[$phone] = $message;

    // }
    // wp_schedule_single_event(time(), 'magic_wa_send_multiple_messages_schedule', [
    //     'order_id' => $order_id,
    //     'messages_data' => json_encode($messages_data), // Encode as JSON
    // ]);
});
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
    wp_enqueue_script('custom-admin-script', get_stylesheet_directory_uri() . '/assets/js/admin-script.js', array('jquery'), time(), true);
    wp_enqueue_style('custom-admin-style', get_stylesheet_directory_uri() . '/assets/css/admin-style.css', array(), time(), 'all');
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
        wp_enqueue_script('jscolor-lib', get_stylesheet_directory_uri() . '/assets/lib/color-picker/jscolor.min.js', array('jquery'), '2.3.6');
        wp_enqueue_script('single-product-script', get_stylesheet_directory_uri() . '/assets/js/single-product-script.js', array('jquery', 'slick'), time(), true);
        wp_localize_script('single-product-script', 'siteConfig', $siteConfig);
        wp_enqueue_style('single-product-style', get_stylesheet_directory_uri() . '/assets/css/variable-product-style.css', array(), time(), 'all');

    }
    if (is_singular('gallery')) {
        // wp_enqueue_style( 'single-gallery-style', get_stylesheet_directory_uri() . '/assets/css/single-gallery-style.css', array(), time(), 'all' );
        // wp_enqueue_script( 'single-gallery-script', get_stylesheet_directory_uri() . '/assets/js/single-gallery-script.js', array(), time(), true );
    }

    if (is_cart()) {
        wp_enqueue_script('custom-cart-script', get_stylesheet_directory_uri() . '/assets/js/cart-script.js', array(), time(), true);
        wp_enqueue_style('custom-cart-style', get_stylesheet_directory_uri() . '/assets/css/cart-style.css', array(), time(), 'all');
    }
    if (is_checkout()) {
        // Enqueue the Hebrew locale file for Flatpickr from CDN.
        // wp_enqueue_script('flatpickr-hebrew-locale', 'https://npmcdn.com/flatpickr/dist/l10n/he.js', array('flatpickr'), null, true);
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_enqueue_style('custom-checkout-style', get_stylesheet_directory_uri() . '/assets/css/checkout-style.css', array(), time(), 'all');
        wp_enqueue_script('custom-checkout-script', get_stylesheet_directory_uri() . '/assets/js/checkout-script.js', array('jquery'), time(), true);
        wp_localize_script( 'custom-checkout-script', 'checkout_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('checkout_nonce'),
            'pickup_dates' => get_pickup_order_items_count(),
            'pickup_dates_limit' => get_field('max_pickup_items', 'option'),
            'pickup_items_count' => get_pickup_cart_items_count(),
        ]);

    }
    wp_enqueue_script('theme-front', get_stylesheet_directory_uri() . '/assets/js/theme-front.js', array('jquery'), time(), true);
    wp_enqueue_style('theme-front', get_stylesheet_directory_uri() . '/assets/css/theme-front.css', array(), time(), 'all');
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


add_action('template_redirect', function () {
    // send_whatsapp_message('972526033388', 'Hello from Mye Sweet 256256!');
});
<?php

function register_custom_widgets($widgets_manager)
{
    require_once HE_CHILD_THEME_DIR . 'inc/elementor/widgets/product-widgets.php'; // Ensure this path is correct
    $widgets_manager->register_widget_type(new \Elementor\Pretty_Product_Gallery_Widget());
}
add_action('elementor/widgets/register', 'register_custom_widgets', 10, 1);

// Hook to the scheduled action to delete a specific image

function delete_image_by_id($image_id)
{
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'] . '/custom_clients_images/';

    // Construct the full path of the image
    $file_path = "$upload_path$image_id";

    // Check if the file exists and delete it
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}
add_action('delete_user_custom_image', 'delete_image_by_id', 10, 1);

/************************/

function enqueue_custom_admin_script()
{
    // Enqueue the custom script
    wp_enqueue_script('custom-admin-script', get_stylesheet_directory_uri() . '/assets/js/custom-admin-script.js', array('jquery'), time(), true);
    wp_enqueue_style('custom-admin-style', get_stylesheet_directory_uri() . '/assets/css/custom-admin-style.css', array(), time(), 'all');
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
        wp_enqueue_style('custom-cart-style', get_stylesheet_directory_uri() . '/assets/css/custom-cart-style.css', array(), time(), 'all');
    }
    if (is_checkout()) {
        // Enqueue the Hebrew locale file for Flatpickr from CDN.
        // wp_enqueue_script('flatpickr-hebrew-locale', 'https://npmcdn.com/flatpickr/dist/l10n/he.js', array('flatpickr'), null, true);
        wp_enqueue_script('custom-checkout-script', get_stylesheet_directory_uri() . '/assets/js/checkout-script.js', array('jquery'), time(), true);
        wp_enqueue_style('custom-checkout-style', get_stylesheet_directory_uri() . '/assets/css/custom-checkout-style.css', array(), time(), 'all');

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
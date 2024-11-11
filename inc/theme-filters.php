<?php 
/**
 * Register a new custom order status: Allergy Form Submitted.
 */
function add_allergy_form_submitted_order_status() {
    register_post_status('wc-allergy-form-submitted', array(
        'label' => _x('Allergy Form Submitted', 'Order status label', 'text-domain'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Allergy Form Submitted <span class="count">(%s)</span>', 'Allergy Form Submitted <span class="count">(%s)</span>', 'text-domain')
    ));
}
add_action('init', 'add_allergy_form_submitted_order_status');

function allow_svg_upload($mime_types) {
    $mime_types['svg']  = 'image/svg+xml'; // Add SVG support
    $mime_types['svgz'] = 'image/svg+xml'; // Add compressed SVG support
    return $mime_types;
}
add_filter('upload_mimes', 'allow_svg_upload');

function sanitize_svg_on_upload($data, $file, $filename, $mimes) {
    if (isset($data['type']) && 'image/svg+xml' === $data['type']) {
        // Optional: Sanitize SVG content here for additional security
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'sanitize_svg_on_upload', 10, 4);

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


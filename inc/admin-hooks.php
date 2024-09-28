<?php
function disable_gutenberg_editor($use_block_editor, $post)
{
    // Check if it's a post type where you want to disable Gutenberg (e.g., 'post', 'page', etc.).
    $post_types_to_disable = array('post', 'page');

    if (in_array($post->post_type, $post_types_to_disable)) {
        $use_block_editor = false;
    }

    return $use_block_editor;
}
add_filter('use_block_editor_for_post', 'disable_gutenberg_editor', 10, 2);

$GLOBALS['wc_loop_variation_id'] = null;

/**
 * Checks if the field group is for a variation.
 *
 * @param array $field_group
 * @param array $variation_data
 * @param array $variation_post
 * @return bool
 */
function is_field_group_for_variation($field_group, $variation_data, $variation_post)
{
    return (preg_match('/Variation/i', $field_group['title']) == true);
}

add_action('woocommerce_product_after_variable_attributes', function ($loop_index, $variation_data, $variation_post) {
    $GLOBALS['wc_loop_variation_id'] = $variation_post->ID;
    $product_id = wp_get_post_parent_id($variation_post->ID);
    $product_categories = wc_get_product($product_id)->get_category_ids();

    foreach (acf_get_field_groups() as $field_group) {
        if ($field_group['ID'] == 1464 && !in_array(17, $product_categories)) {
            acf_render_fields($variation_post->ID, acf_get_fields($field_group));
        } else if ($field_group['ID'] == 1410 && in_array(17, $product_categories)) {
            // acf_render_fields($variation_post->ID, acf_get_fields($field_group));
        }
    }
    ?>
    <script>
        ($ => {
            acf.do_action('append', $('#post'));
        })(jQuery);
    </script>
    <?php
    $GLOBALS['wc_loop_variation_id'] = null;
}, 10, 3);

add_action('woocommerce_save_product_variation', 'update_product_ingredients_meta', 10, 2);

/**
 * Updates the product ingredients meta.
 *
 * @param int $variation_id
 * @param int $loop_index
 * @return void
 */
function update_product_ingredients_meta($variation_id, $loop_index)
{
    $ingredients_field_key = 'field_649b1d0b1fcc4';
    $brands_field_key = 'field_649ae3e8bbb2e';
    $product_ingredients = $_POST['acf_variation'][$variation_id][$ingredients_field_key];
    $ingredients_brand_fields = $_POST['acf_variation'][$variation_id][$brands_field_key];

    if (!empty($product_ingredients)) {
        update_field($ingredients_field_key, $product_ingredients, $variation_id);
    }
    if (!empty($ingredients_brand_fields)) {
        update_field($brands_field_key, $ingredients_brand_fields, $variation_id);
    }
}

add_filter('acf/prepare_field', function ($field) {
    if (!$GLOBALS['wc_loop_variation_id']) {
        return $field;
    }

    $field['name'] = preg_replace('/^acf\[/', 'acf_variation[' . $GLOBALS['wc_loop_variation_id'] . '][', $field['name']);
    return $field;
}, 10, 1);

// Add ACF rule
add_filter('acf/location/rule_values/post_type', 'acf_location_rule_values_Post');
/**
 * Provides ACF rule values for the post_type rule.
 *
 * @param array $choices
 * @return array
 */
function acf_location_rule_values_Post($choices)
{
    $choices['product_variation'] = 'Product Variation';
    $choices['ingredient_variation'] = 'Ingredient Variation';
    return $choices;
}

// Add a custom tab to the product data section
function custom_product_data_tab($tabs)
{
    $tabs['custom_tab'] = array(
        'label' => __('Allergies', 'get_stylesheet()'),
        'target' => 'custom_tab_data',
        'class' => array('show_if_simple', 'show_if_variable'),
    );
    return $tabs;
}
add_filter('woocommerce_product_data_tabs', 'custom_product_data_tab');
// Add ACF field to custom tab on product page
function add_acf_field_to_custom_tab()
{
    global $post;

    if (function_exists('acf_render_field')) {
        echo '<div id="custom_tab_data" class="panel woocommerce_options_panel">';
        echo '<div class="options_group">';

        foreach (acf_get_field_groups() as $field_group) {
            if ($field_group['ID'] == 31) {
                acf_render_fields($post->ID, acf_get_fields($field_group));
            }

        }
        echo '</div>';
        echo '</div>';
    }
}
add_action('woocommerce_product_data_panels', 'add_acf_field_to_custom_tab');

function generate_attributes_combinations($attribute_values, $combination, &$attributes_combinations, $index = 0)
{
    if ($index == count($attribute_values)) {
        $attributes_combinations[] = $combination;
        return;
    }
    
    $attribute_name = array_keys($attribute_values)[$index];
    $attribute_options = $attribute_values[$attribute_name];
    
    // dd($attribute_options);
    foreach ($attribute_options as $option) {
        $new_combination = $combination;
        $new_combination[$attribute_name] = $option;
        generate_attributes_combinations($attribute_values, $new_combination, $attributes_combinations, $index + 1);
    }
}
function get_product_attributes($product_id, $product)
{
    $attributes = $product->get_attributes();
    $attribute_values = array();
    foreach ($attributes as $attribute) {
        $attribute_name = $attribute->get_name();
        $attribute_values[$attribute_name] = $attribute->get_options();
    }

    $attribute_values_to_generate = [
        'pa_vegan' => $attribute_values['pa_vegan'],
        'pa_gluten' => $attribute_values['pa_gluten'],
        'pa_base' => $attribute_values['pa_base'],
        'pa_icing' => $attribute_values['pa_icing'],
    ];

    if ($product_id == 28)
        $attribute_values_to_generate['pa_kids-cake-size'] = $attribute_values['pa_kids-cake-size'];
    else if ($product_id == 1686)
        $attribute_values_to_generate['pa_custom-cake-size'] = $attribute_values['pa_custom-cake-size'];
    else if ($product_id == 27)
        $attribute_values_to_generate['pa_cupcakes-quantity'] = $attribute_values['pa_cupcakes-quantity'];
    else if ($product_id == 1672)
        $attribute_values_to_generate['pa_mini-cupcakes-quantity'] = $attribute_values['pa_mini-cupcakes-quantity'];

    return $attribute_values_to_generate;
}

add_action('woocommerce_process_product_meta', 'save_custom_product_field');

// Save the custom field value
function save_custom_product_field($post_id)
{
    $custom_field = isset($_POST['_allergy_field']) ? sanitize_text_field($_POST['_allergy_field']) : '';
    update_post_meta($post_id, '_allergy_field', $custom_field);
}
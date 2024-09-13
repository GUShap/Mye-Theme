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

// Hook the function to run when WooCommerce is initialized
add_action('admin_init', 'create_product_variations_on_init');

function is_avalable_variation($attributes, $product_id)
{
    if($product_id !==28) return true;
    $base = $attributes['pa_base']->slug;
    $is_gluten_free = $attributes['pa_gluten']->slug == 'true' ? true : false;
    $is_vegan = $attributes['pa_vegan']->slug == 'true' ? true : false;

    $is_available = true;


    if ($is_vegan && $is_gluten_free && $base == 'vanilla')
        $is_available = false;
    if ($is_vegan && $is_gluten_free && $base == 'chocolate')
        $is_available = false;
    if ((!$is_vegan || !$is_gluten_free) && $base == 'chocolate-vanilla')
        $is_available = false;
    return $is_available;
}

function get_variation_price($attributes, $product_id)
{
    $size = '';
    $amount = '';

    if ($product_id == 28) $size = $attributes['pa_kids-cake-size']->slug;
    else if ($product_id == 1686) $size = $attributes['pa_custom-cake-size']->slug;
    else if ($product_id == 27) $amount = $attributes['pa_cupcakes-quantity']->slug;
    else if ($product_id == 1672) $amount = $attributes['pa_mini-cupcakes-quantity']->slug;

    $is_gluten_free = $attributes['pa_gluten']->slug == 'true' ? true : false;
    $is_vegan = $attributes['pa_vegan']->slug == 'true' ? true : false;
    $price = 0;

    if (!empty($size)) {
        if ($size == 'large') {
            $product_id == 28
                ? $price += 180
                : $price += 500;
        } else if ($size == 'medium')
            $price += 400;
        else if ($size == 'small') {
            $product_id == 28
                ? $price += 160
                : $price += 300;
        }
    }

    if (!empty($amount)) {
        if($amount == 2) $price += 50;
        else if($amount == 4) $price += 70;
        else if($amount == 6) $price += 90;
        else if($amount == 12) $price += 120;
        else if($amount == 24) {
            $product_id == 27
            ? $price += 200 // regular cupcakes
            : $price += 160; //mini cupcakes
        }
        else if($amount == 30) $price += 250;
        else if($amount == 35) $price += 180; // only mini cupcakes

    }

    $price += $is_gluten_free ? 20 : 0;
    $price += $is_vegan ? 20 : 0;

    return $price;
}

function get_variation_sku($attributes, $product_id)
{
    $size_option = '';
    $amount_option = '';
    $base_option = $attributes['pa_base']->slug;
    $icing_option = $attributes['pa_icing']->slug;
    $is_vegan = $attributes['pa_vegan']->slug == 'true' ? true : false;
    $is_gluten_free = $attributes['pa_gluten']->slug == 'true' ? true : false;
    // Setting SKU
    $sku = '';

    if ($product_id == 28) {
        $size_option = $attributes['pa_kids-cake-size']->slug;
        $sku = 'KID-';
    } else if ($product_id == 1686) {
        $size_option = $attributes['pa_custom-cake-size']->slug;
        $sku = 'CUS-';
    } else if ($product_id == 27) {
        $amount_option = $attributes['pa_cupcakes-quantity']->slug;
        $sku = 'CUP-';
    } else if ($product_id == 1672) {
        $amount_option = $attributes['pa_mini-cupcakes-quantity']->slug;
        $sku = 'MCUP-';
    }

    // size
    if (!empty($size_option)) {
        if ($size_option == 'large')
            $sku .= 'L-';
        else if ($size_option == 'medium')
            $sku .= 'M-';
        else if ($size_option == 'small')
            $sku .= 'S-';
    }
    // amount
    if (!empty($amount_option)) {
        if ($amount_option == 2)
            $sku .= 'II-';
        else if ($amount_option == 4)
            $sku .= 'IV-';
        else if ($amount_option == 6)
            $sku .= 'VI-';
        else if ($amount_option == 12)
            $sku .= 'XII-';
        else if ($amount_option == 24)
            $sku .= 'XXIV-';
        else if ($amount_option == 30)
            $sku .= 'XXX-';
        else if ($amount_option == 35)
            $sku .= 'XXXV-';
    }

    // vegan
    $sku .= $is_vegan ? 'V-' : 'NV-';
    // gluten
    $sku .= $is_gluten_free ? 'GF-' : 'G-';
    // base
    if ($base_option == 'vanilla')
        $sku .= 'VA-';
    else if ($base_option == 'chocolate')
        $sku .= 'CH-';
    else if ($base_option == 'chocolate-vanilla')
        $sku .= 'CV-';
    // icing
    if ($icing_option == 'ganache')
        $sku .= 'GI';
    else if ($icing_option == 'whipped-cream')
        $sku .= 'CI';
    return $sku;
}
function is_sku_exist($product_id, $sku)
{
    $product = wc_get_product($product_id);
    $variations = $product->get_available_variations();

    $sku_array = array();

    foreach ($variations as $variation) {
        $variation_id = $variation['variation_id'];
        $variation = wc_get_product($variation_id);

        $current_sku = $variation->get_sku();
        if (!empty($current_sku)) {
            $sku_array[] = $current_sku;
        }
    }

    return in_array($sku, $sku_array);
}

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

function create_product_variations_on_init()
{
    if (empty($_GET['post']))
        return;

    $product_id = $_GET['post']; // Or get the variable product id dynamically

    // if($product_id !== 27 && $product_id !== 28 && $product_id !== 1672 && $product_id !==  1686) return;
    $product = wc_get_product($product_id);

    if (!$product || !$product->is_type('variable')) {
        return;
    }

    $attribute_values_to_generate = get_product_attributes($product_id, $product);
    $attributes_combinations = [];
    generate_attributes_combinations($attribute_values_to_generate, [], $attributes_combinations);

    foreach ($attributes_combinations as $idx => $combination_options) {
        $data_array = [];
        foreach ($combination_options as $label => $option) {
            $data_array[$label] = get_term($option);
        }
        // test if available comb
        $is_avalable_variation = is_avalable_variation($data_array,$product_id);
        if ($is_avalable_variation)
            $attributes_combinations[$idx] = $data_array;
        else
            unset($attributes_combinations[$idx]);
    }
    $existing_variations_sku = [];
    foreach ($product->get_available_variations() as $variation) {
        $existing_variations_sku[] = $variation['sku'];
    }

    foreach ($attributes_combinations as $attributes) {
        $sku = get_variation_sku($attributes, $product_id);
        if (!in_array($sku, $existing_variations_sku)) {
            $price = get_variation_price($attributes, $product_id);
            $variation_data = [
                'attributes' => $attributes,
                'sku' => $sku,
                'regular_price' => $price,
            ];
            create_product_variation($product_id, $variation_data);
        }
    }

    return;
}

add_action('woocommerce_process_product_meta', 'save_custom_product_field');
// // Add fields to the custom tab
// function custom_product_data_fields() {
//     global $post;

//     echo '<div id="custom_tab_data" class="panel allergies woocommerce_options_panel">';
//     echo '<div class="options_group">';

//     woocommerce_wp_text_input( array(
//         'id'          => '_allergy_field',
//         'label'       => __( 'Allergies', 'get_stylesheet()' ),
//         'placeholder' => __( 'Enter custom field value', 'get_stylesheet()' ),
//         'desc_tip'    => 'true',
//         'description' => __( 'Enter additional information here.', 'get_stylesheet()' ),
//     ) );

//     echo '</div>';
//     echo '</div>';
// }
// add_action( 'woocommerce_product_data_panels', 'custom_product_data_fields' );

// Save the custom field value
function save_custom_product_field($post_id)
{
    $custom_field = isset($_POST['_allergy_field']) ? sanitize_text_field($_POST['_allergy_field']) : '';
    update_post_meta($post_id, '_allergy_field', $custom_field);
}

function create_product_variation($product_id, $variation_data)
{
    $product = wc_get_product($product_id);

    $variation_post = [

        'post_title' => $product->get_title(),

        'post_name' => 'product-' . $product_id . '-variation',

        'post_status' => 'publish',

        'post_parent' => $product_id,

        'post_type' => 'product_variation',

        'guid' => $product->get_permalink()

    ];

    $variation_id = wp_insert_post($variation_post);

    $variation = new WC_Product_Variation($variation_id);

    foreach ($variation_data['attributes'] as $attribute => $term) {
        $term_name = $term->name;
        $taxonomy = $attribute;

        if (!taxonomy_exists($taxonomy)) {

            register_taxonomy(

                $taxonomy,

                'product_variation',

                array(

                    'hierarchical' => false,

                    'label' => ucfirst($attribute),

                    'query_var' => true,

                    'rewrite' => array('slug' => sanitize_title($attribute)),

                )

            );

        }

        if (!term_exists($term_name, $taxonomy))

            wp_insert_term($term_name, $taxonomy);

        $term_slug = $term->slug;

        $post_term_names = wp_get_post_terms($product_id, $taxonomy, array('fields' => 'names'));

        if (!in_array($term_name, $post_term_names))

            wp_set_post_terms($product_id, $term_name, $taxonomy, true);

        update_post_meta($variation_id, 'attribute_' . $taxonomy, $term_slug);

    }

    if (!empty($variation_data['sku']))

        $variation->set_sku($variation_data['sku']);

    if (empty($variation_data['sale_price'])) {

        $variation->set_price($variation_data['regular_price']);

    } else {

        $variation->set_price($variation_data['sale_price']);

        $variation->set_sale_price($variation_data['sale_price']);

    }

    $variation->set_regular_price($variation_data['regular_price']);

    if (!empty($variation_data['stock_qty'])) {

        $variation->set_stock_quantity($variation_data['stock_qty']);

        $variation->set_manage_stock(true);

        $variation->set_stock_status('');

    } else {

        $variation->set_manage_stock(false);

    }

    $variation->set_weight('2');
    $variation->set_stock_status('instock');
    $variation->save();

}
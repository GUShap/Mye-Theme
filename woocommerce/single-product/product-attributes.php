<?php

defined('ABSPATH') || exit;

if (!$product_attributes) {
    return;
}
$custom_attributes = get_field('attribute_options', get_the_ID());
?>
<table class="woocommerce-product-attributes shop_attributes">
    <?php foreach ($product_attributes as $product_attribute_key => $product_attribute): ?>
        <tr
            class="woocommerce-product-attributes-item woocommerce-product-attributes-item--<?php echo esc_attr($product_attribute_key); ?>">
            <th class="woocommerce-product-attributes-item__label">
                <?php echo wp_kses_post($product_attribute['label']); ?>
            </th>
            <td class="woocommerce-product-attributes-item__value">
                <?php echo wp_kses_post($product_attribute['value']); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php foreach ($custom_attributes as $idx => $attribute_id):
        $options = get_field('attribute_options', $attribute_id);
        $labels = '';
        if (!empty($options)) {

            foreach ($options as $idx => $option) {
                $labels .= $idx == count($options) - 1
                    ? $option['label']
                    : $option['label'] . ', ';
            }
            if (empty($options))
                $labels = get_field('attribute_description', $attribute_id);
        }
        ?>
        <tr
            class="woocommerce-product-attributes-item woocommerce-product-attributes-item--<?php echo esc_attr($attribute_id); ?>">
            <th class="woocommerce-product-attributes-item__label">
                <?php echo wp_kses_post(get_the_title($attribute_id)); ?>
            </th>
            <td class="woocommerce-product-attributes-item__value">
                <?php echo wp_kses_post($labels); ?>
            </td>
        </tr>

    <?php endforeach; ?>
</table>
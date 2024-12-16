<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

defined('ABSPATH') || exit;

$text_align = is_rtl() ? 'right' : 'left';
$margin_side = is_rtl() ? 'left' : 'right';

foreach ($items as $item_id => $item):
    $product = $item->get_product();
    $sku = '';
    $purchase_note = '';
    $image = '';

    $variation = wc_get_product($item->get_variation_id());
    $variation_attributes = $variation->get_attributes() ?? [];
    $custom_attributes = $item->get_meta('custom_attributes') ?? [];
    $multiple_options_attributes = $item->get_meta('multiple_options_attributes') ?? [];
    $allergen_list = $item->get_meta('allergen_list') ?? [];

    if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
        continue;
    }

    if (is_object($product)) {
        $sku = $product->get_sku();
        $purchase_note = $product->get_purchase_note();
        $image = $product->get_image($image_size);
    }

    ?>
    <tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
        <td class="td"
            style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
            <?php

            // Show title/image etc.
            if ($show_image) {
                echo wp_kses_post(apply_filters('woocommerce_order_item_thumbnail', $image, $item));
            }

            // Product name.
            echo wp_kses_post(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false));

            // SKU.
            if ($show_sku && $sku) {
                echo wp_kses_post(' (#' . $sku . ')');
            }

            // allow other plugins to add additional product information here.
            do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text);

            if (!empty($variation_attributes)) {
                foreach ($variation_attributes as $attribute_name => $attr_value) {
                    $is_boolean = !empty(get_field('is_boolean', "tax_{$attribute_name}_options"));
                    $is_multiple_options = !empty(get_field('multiple_options', "tax_{$attribute_name}_options"));
                    if ($is_boolean) {
                        echo $attr_value === 'true'
                            ? '<br><strong>' . wc_attribute_label($attribute_name) . '</strong>'
                            : '';
                    } else if ($is_multiple_options) {
                        $term_value = $attr_value;
                        $options = $multiple_options_attributes[$attribute_name];
                        if (!empty($options)) {
                            $term_value = implode(', ', array_map(function ($option) use ($attribute_name) {
                                $term = get_term_by('slug', $option, $attribute_name);
                                return $term->name;
                            }, $options));
                        }
                        echo '<br><strong>' . wc_attribute_label($attribute_name) . ':</strong> ' . $term_value;
                    } else {
                        $term = get_term_by('slug', $attr_value, $attribute_name);
                        echo '<br><strong>' . wc_attribute_label($attribute_name) . ':</strong> ' . wp_kses_post($term->name);
                    }
                }
            }

            if (!empty($custom_attributes)) {
                foreach ($custom_attributes as $attr_id => $values) {
                    echo '<br><strong>' . get_the_title($attr_id) . '</strong> &#215;' . count($values);
                }
            }

            // allow other plugins to add additional product information here.
            do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text);

            ?>
        </td>
        <td class="td"
            style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
            <?php
            $qty = $item->get_quantity();
            $refunded_qty = $order->get_qty_refunded_for_item($item_id);

            if ($refunded_qty) {
                $qty_display = '<del>' . esc_html($qty) . '</del> <ins>' . esc_html($qty - ($refunded_qty * -1)) . '</ins>';
            } else {
                $qty_display = esc_html($qty);
            }
            echo wp_kses_post(apply_filters('woocommerce_email_order_item_quantity', $qty_display, $item));
            ?>
        </td>
        <td class="td"
            style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
            <?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?>
        </td>
        <?php
        if (!empty($allergen_list)) { ?>
            <td class="td"
                style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <?php echo implode(', ', $allergen_list); ?>
            </td>
        <?php } ?>
    </tr>
    <?php

    if ($show_purchase_note && $purchase_note) {
        ?>
        <tr>
            <td colspan="3"
                style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <?php
                echo wp_kses_post(wpautop(do_shortcode($purchase_note)));
                ?>
            </td>
        </tr>
        <?php
    }
    ?>

<?php endforeach; ?>
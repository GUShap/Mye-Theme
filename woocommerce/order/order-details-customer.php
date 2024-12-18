<?php
defined('ABSPATH') || exit;

$show_shipping = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details">
    <h2>פרטי חיוב</h2>
    <address>
        <?php echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'))); ?>

        <?php if ($order->get_billing_phone()): ?>
            <p class="woocommerce-customer-details--phone" style="margin-bottom:5px;"><?php echo esc_html($order->get_billing_phone()); ?>
            </p>
        <?php endif; ?>

        <?php if ($order->get_billing_email()): ?>
            <p class="woocommerce-customer-details--email"><?php echo esc_html($order->get_billing_email()); ?>
            </p>
        <?php endif; ?>

        <?php
        /**
         * Action hook fired after an address in the order customer details.
         *
         * @since 8.7.0
         * @param string $address_type Type of address (billing or shipping).
         * @param WC_Order $order Order object.
         */
        do_action('woocommerce_order_details_after_customer_address', 'billing', $order);
        ?>
    </address>
    <?php do_action('woocommerce_order_details_after_customer_details', $order); ?>

</section>
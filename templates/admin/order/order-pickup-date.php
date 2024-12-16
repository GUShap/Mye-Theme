<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Order Pickup Date Template
 * 
 * @var string $order_pickup_date
 */
?>

<div class="order_data_column" id="order-pickup-column" style="width:100%;">
    <div class="inner" style="border:1px solid #e5e5e5;padding:10px 15px;text-align:center;">
        <h4 style="text-decoration:underline;margin:0;">תאריך איסוף הזמנה</h4>
        <strong><mark style="padding:3px 8px;font-size:16px;"><?php echo date('d/m/Y', strtotime($order_pickup_date)) ?></mark></strong>
    </div>
</div>
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Pickup Date email template
 * 
 * @var string $order_pickup_date
 */
?>

<table id="pickup" cellspacing="0" cellpadding="0"
    style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
    <tr>
        <th style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;"
            valign="top" width="50%">
            <h2>תאריך איסוף הזמנה</h2>
        </th>
    </tr>
    <tr>
        <td style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:1px solid #e5e5e5;"
            valign="top" width="50%">
            <b><mark><?php echo date('d/m/Y', strtotime($order_pickup_date)) ?></mark></b>
        </td>
    </tr>
</table>
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Order Custom Information Template
 * 
 * @var string $order_id
 * @var array $order_recipients
 * @var string $ingredients_form_url
 * @var string $order_pickup_date
 */

?>

<?php if (!empty($order_pickup_date)): ?>
    <table class="woocommerce-table woocommerce-table--order-details shop_table pickup_date">
        <th>
            <h2>תאריך איסוף הזמנה</h2>
        </th>
        <tr>
            <td style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:1px solid #e5e5e5;"
                valign="top" width="30%">
                <b><mark><?php echo date('d/m/Y', strtotime($order_pickup_date)) ?></mark></b>
            </td>
        </tr>
    </table>
<?php endif; ?>
<table class="woocommerce-table woocommerce-table--order-details shop_table recipient_information">
    <th><h2>חשוב לדעת</h2></th>
    <tr>
        <td style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:1px solid #e5e5e5;"
            valign="top" width="30%">
            <strong>כל הזמנה מחויבת במילוי טופס <a style="font-weight:600;"
                    href="<?php echo esc_url($ingredients_form_url); ?>">אישור
                    רכיבים</a></strong>
            <p>יש למלא את הטופס לפני איסוף/קבלת המוצרים, בקישור הבא:</p>
            <p><a class="ingredients-form-link" href="<?php echo esc_url($ingredients_form_url); ?>">https://ingredients-form.form</a>
            </p>
            <p>בפרטי הטופס יש למלא את מספר ההזמנה, שהינו <b><?php echo $order_id ?></b>, ובשורת המייל יש למלא את כתובת
                המייל
                הנוכחית.</p>
            <?php if (count($order_recipients) > 1): ?>
                <p style="margin-bottom:0;">בנוסף אליך - טופס הרכיבים מחוייב גם במילוי של כל מי שעבורו/ה יש התייחסות
                    אלרגנים:</p>
                <ul style="margin-top:0;">
                    <?php foreach ($order_recipients as $recipient): ?>
                        <?php if ($recipient['email'] === $order->get_billing_email())
                            continue; ?>
                        <li><?php echo $recipient['name'] ?></li>
                    <?php endforeach; ?>
                </ul>
                <p style="margin-bottom:0;">מייל עם קישור הטופס נשלח לכל המחוייבים במילוי, אבל יש לוודא זאת.</p>
                <p>הזמנה בה לא <b>כל</b> המחוייבים/ות במילוי הטופס אכן מילאו, <b>אינה ניתנת לאיסוף!</b></p>
            <?php endif; ?>
            <br>
        </td>
    </tr>
</table>
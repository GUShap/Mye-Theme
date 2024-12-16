<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Order Recipient email template
 * 
 * @var object $order
 * @var string $order_id
 * @var array $recipient
 * @var string $subject
 * @var string $order_pickup_date
 * @var string $ingredients_form_url
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo get_bloginfo('name', 'display'); ?></title>
    <style>
        <?php wc_get_template('emails/email-styles.php'); ?>
    </style>
</head>

<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0"
    offset="0">
    <?php wc_get_template('emails/email-header.php', array('email_heading' => $subject)); ?>
    <table width="100%" id="outer_wrapper" style="background-color:#fff;">
        <tr>
            <td valign="top">
                <div id="body_content_inner">
                    <strong>שלום <?php echo $recipient['name'] ?>,</strong>
                    <p>נתקבלה במערכת הזמנת קינוח, שהוזמן על ידי
                        <?php echo "{$order->get_billing_first_name()} {$order->get_billing_last_name()}" ?>,
                        ובה צוינו פרטיך בהתייחסות להמנעות מאלרגנים בקינוחי ההזמנה.
                    </p>
                    <p>חשוב לציין שהמטבח שלנו הינו איזור נקי מאלרגנים, תוך המנעות משימוש במרכיבים הבעייתים לך, ובאופן
                        כללי עבור כולם.</p>
                    <strong>כל הזמנה מחויבת במילוי טופס <a style="font-weight:600;"
                            href="<?php echo esc_url($ingredients_form_url); ?>">אישור
                            רכיבים</a>
                    </strong>
                    <p>
                        יש למלא את הטופס לפני איסוף/קבלת המוצרים, בקישור הבא:
                        <br>
                        <a href="<?php echo esc_url($ingredients_form_url); ?>">
                            <?php echo esc_url($ingredients_form_url) ?>
                        </a>
                    </p>
                    <p>בפרטי הטופס יש למלא את מספר ההזמנה, שהינו <b><?php echo $order_id ?></b>, ובשורת המייל יש למלא את
                        כתובת המייל הנוכחית.
                    </p>
                    <p>הזמנה בה לא <b>כל</b> המחוייבים/ות במילוי הטופס אכן מילאו, <b>אינה ניתנת לאיסוף!</b></p>
                    <p>תאריך איסוף ההזמנה הוא:
                        <b><mark><?php echo date('d/m/Y', strtotime($order_pickup_date)) ?></mark></b>
                    </p>
                </div>
            </td>
        </tr>
    </table>
    <?php wc_get_template('emails/email-footer.php'); ?>
</body>

</html>
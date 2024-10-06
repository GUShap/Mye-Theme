<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Order Recipients List Template
 * 
 * @var array $order_recipients
 */
?>

<div class="order_data_column" id="order-recipients-column">
    <h4>מחוייבים בחתימה על טופס אלרגנים:</h4>
    <ul class="recipients-list">
        <li class="heading-row">
            <p class="name recipient-field">שם</p>
            <p class="email recipient-field">מייל</p>
            <p class="phone recipient-field">טלפון</p>
            <p class="status recipient-field">סטטוס</p>
        </li>
        <?php foreach ($order_recipients as $recipient_label => $recipient) {
            $name = $recipient['name'];
            $email = $recipient['email'];
            $phone = $recipient['phone'];
            ?>
            <li>
                <p class="name recipient-field">
                    <?php echo $name ?>
                </p>
                <p class="email recipient-field">
                    <?php echo $email ?>
                </p>
                <p class="phone recipient-field">
                    <?php echo $phone ?>
                </p>
                <p class="status recipient-field">
                    <?php echo $recipient['status'] ?? 'ממתין' ?>
                </p>
                <input type="hidden" name="<?php echo $recipient_label ?>_name" value="<?php echo $name ?>">
                <input type="hidden" name="<?php echo $recipient_label ?>_email" value="<?php echo $email ?>">
                <input type="hidden" name="<?php echo $recipient_label ?>_phone" value="<?php echo $phone ?>">
            </li>
        <?php } ?>
    </ul>
</div>
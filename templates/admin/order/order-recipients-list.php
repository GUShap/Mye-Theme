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
        <?php foreach ($order_recipients as $recipient_label => $recipient) {
            $name = $recipient['name'];
            $email = $recipient['email'];
            $phone = $recipient['phone'];
            ?>
            <li>
                <p class="name recipient-field"><span class="label">שם:</span> <span class="value">
                        <?php echo $name ?>
                    </span></p>
                <p class="email recipient-field"><span class="label">מייל:</span> <span class="value">
                        <?php echo $email ?>
                    </span></p>
                <p class="phone recipient-field"><span class="label">טלפון:</span><span class="value">
                        <?php echo $phone ?>
                    </span></p>
                <input type="hidden" name="<?php echo $recipient_label ?>_name" value="<?php echo $name ?>">
                <input type="hidden" name="<?php echo $recipient_label ?>_email" value="<?php echo $email ?>">
                <input type="hidden" name="<?php echo $recipient_label ?>_phone" value="<?php echo $phone ?>">
            </li>
        <?php } ?>
    </ul>
</div>
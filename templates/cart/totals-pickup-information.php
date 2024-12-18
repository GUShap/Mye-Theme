<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cart totals pickup information
 * 
 * @var boolean $is_checkout
 */
?>

<div class="pickup-information">
    <strong>איסוף עצמי:</strong>
    <div class="description">
        <p style="margin:0;">את הזמנתך ניתן לאסוף מכתובת העסק</p>
        <p>ברח׳ <b><em>בורוכוב 30, הרצליה</em></b></p>
        <?php if (!$is_checkout) { ?>
            <p>תיאום האיסוף יעשה בעמוד התשלום</p>
        <?php } ?>
    </div>
</div>
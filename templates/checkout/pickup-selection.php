<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Pickup Selection Template
 */
?>
<div class="order-pickup-container">
    <p class="form-row form-row-wide">
        <label for="desired_date"><?php _e('תאריך איסוף הזמנה', 'woocommerce'); ?></label>
        <input type="text" class="input-text" name="front_pickup_date" id="pickup-date" required
        placeholder="<?php _e('לחצו לבחירת תאריך', 'woocommerce'); ?>" autocomplete="off"/>
        <input type="hidden" name="pickup_date" id="pickup-date-value">
    </p>
</div>
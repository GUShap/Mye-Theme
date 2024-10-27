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
        <input type="text" class="input-text" name="pickup_date" id="pickup-date"
        placeholder="<?php _e('Choose a date', 'woocommerce'); ?>" />
    </p>
</div>
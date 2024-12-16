<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Managment settings page template
 * 
 */

?>

<div class="wrap">
    <h1>הגדרות ניהול</h1>
    <form method="post" action="" id="settings_form">
        <?php settings_fields('managment-settings-group'); ?>
        <?php do_settings_sections('managment-settings-group'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">קישור טופס אישור רכיבים</th>
                <td><input type="url" name="ingredients_form_url"
                        value="<?php echo esc_attr(get_option('ingredients_form_url')); ?>" /></td>
            </tr>
        </table>
        <?php submit_button('Save Settings'); ?>
    </form>
</div>
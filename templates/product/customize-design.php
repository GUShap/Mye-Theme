<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customize Design Template
 * 
 * @param array $attributes
 * @param boolean $enable_custom_text
 */
// dd($enable_custom_text);

?>
<div class="customize-design-container">
    <form action="post" id="custom-design-form">
        <div class="heading-wrapper"></div>
        <div class="content-wrapper">
            <div class="attributes-wrapper active">
                <?php foreach ($attributes as $attr_idx => $attr_id) {
                    $is_active = $attr_idx === 0;
                    $title = get_the_title($attr_id);
                    $icon = get_field('icon', $attr_id);
                    $options = get_field('attribute_options', $attr_id);
                    $multiple_selection = !empty(get_field('multiple_selection', $attr_id));
                    $max_avialable_options = get_field('max_avialable_options', $attr_id);
                    $has_options = !empty($options);
                    $attr_price = get_field('attribute_price', $attr_id);
                    $price_per_option = get_field('price_per_options', $attr_id);
                    $is_price_per_option = !empty($price_per_option);
                    $image_upload_enabled = !empty(get_field('enable_user_image_upload', $attr_id));
                    $is_search_enabled = !empty(get_field('enable_search', $attr_id));
                    $is_user_text_enabled = !empty(get_field('enable_user_text', $attr_id));

                    $single_attribute_template_path = HE_CHILD_THEME_DIR . 'templates/product/single-attribute.php';
                    if (file_exists($single_attribute_template_path)) {
                        include $single_attribute_template_path;
                    }
                } ?>
            </div>
            <div class="totals-summary">
                <div class="totals-summary-heading">
                    <h3>סיכום תוספות</h3>
                </div>
                <div class="totals-summary-content">
                    <?php foreach ($attributes as $attr_idx => $attr_id) {
                        $title = get_the_title($attr_id);
                        ?>
                        <div class="attribute-line-wrapper" data-id="<?php echo $attr_id ?>">
                            <div class="attribute-title">
                                <p><?php echo $title ?></p>
                            </div>
                            <div class="attribute-content"></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="footing-wrapper">
            <div class="step-actions">
                <button type="button" class="" id="prev-attr">&#171;</button>
                <button type="button" class="" id="prev-step">שלב קודם</button>
                <button type="button" class="" id="next-step">שלב הבא</button>
                <button type="button" class="active" id="next-attr">&#187;</button>
            </div>
            <div class="summary-actions">
                <button type="button" class="active" id="finish-design">סיכום תוספות</button>
                <button type="button" class="active" id="back-to-edit">עריכת תוספות</button>
                <button type="button" class="active" id="add-to-item">הוספה לקינוח</button>
            </div>
        </div>
    </form>
</div>
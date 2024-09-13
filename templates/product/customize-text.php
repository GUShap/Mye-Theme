<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customize Text Template
 * 
 * @var array $customer_text
 */
?>

<div class="customer-text-container">
    <?php
    $char_limit = $customer_text['text_attributes']['char_limit'];
    $templates = $customer_text['text_attributes']['text_templates'];
    $is_free_text_option = !empty($customer_text['text_attributes']['enable_free_text']);
    ?>
    <h4 class="customer-text-title">כיתוב על הקינוח</h4>
    <div class="customer-text-wrapper">
        <input type="hidden" name="customer_text" id="customer_text">
        <div class="step1-wrapper">
            <input type="checkbox" name="add-customer-text" id="add-customer-text">
            <label for="add-customer-text">להוסיף כיתוב על הקינוח?</label>
        </div>
        <div class="text-templates-wrapper">
            <?php foreach ($templates as $outer_idx => $template) { ?>
                <div class="single-template-wrapper">
                    <input type="radio" name="customer-text-template"
                        id="customer-text-template-<?php echo ++$outer_idx ?>">
                    <label for="customer-text-template-<?php echo $outer_idx ?>">
                        <?php
                        $formatted_template_text = '';
                        foreach ($template['template_part'] as $inner_idx => $template_part) {
                            $type = $template_part['template_part_type'];
                            $text = $template_part['template_part_text'];

                            if ($type == 'template_text')
                                $formatted_template_text .= '<span class="pre-text">' . $text . '</span>';
                            else if ($type == 'free_text') {
                                $free_text_input = '<div class="inner-text-input-wrapper"><span class="input-as-text"></span><input type="text" disabled class="free-text-template-part" maxlength="' . ceil($char_limit / 3) . '"/></div>';
                                $formatted_template_text .= $free_text_input;
                            }
                        } ?>
                        <?php echo $formatted_template_text; ?>
                    </label>
                    <button type="button" class="text-approval-btn text-btn" disabled>אישור</button>
                    <button type="button" class="text-change-btn text-btn" disabled>שינוי</button>
                </div>
            <?php } ?>
            <?php if ($is_free_text_option) { ?>
                <div class="single-template-wrapper free-text">
                    <input type="radio" name="customer-text-template" id="customer-text-template-free">
                    <label for="customer-text-template-free"> טקסט חופשי (עד
                        <?php echo $char_limit ?> תווים)
                    </label>
                    <div class="inner-text-input-wrapper">
                        <span class="input-as-text"></span>
                        <input type="text" name="customer-free-text" id="customer-free-text"
                            maxlength="<?php echo $char_limit ?>" disabled>
                    </div>
                    <button type="button" class="text-approval-btn text-btn" disabled>אישור</button>
                    <button type="button" class="text-change-btn text-btn" disabled>שינוי</button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
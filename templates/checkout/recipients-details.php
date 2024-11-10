<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recipients Details Template
 * 
 * @var array $checkout
 */
?>

<div class="order-recipients-container">
    <h4 class="order-recipients-title">יש לציין:</h4>
    <div class="initial field-wrapper">
        <?php woocommerce_form_field('is_other_recipients', array(
            'type' => 'radio',
            'class' => array('other-recipients form-row-wide'),
            'options' => [
                '1' => 'כן',
                '' => 'לא',
            ],
            'label' => __('האם ההזמנה בעבור אנשים נוספים בעלי אלגיות?', 'woocommerce'),
            'required' => true,
        ), $checkout->get_value('is_other_recipients')); ?>
    </div>
    <div class="second field-wrapper">
        <ul class="recipients-list">
            <li class="single-recipient">
                <?php woocommerce_form_field('recipients[0][name]', array(
                    'type' => 'text',
                    'class' => array('recipient-name form-row-wide'),
                    'label' => __('שם מלא', 'woocommerce'),
                    'required' => true,
                ), $checkout->get_value('recipients[0][name]')); ?>
                <?php woocommerce_form_field('recipients[0][email]', array(
                    'type' => 'email',
                    'class' => array('recipient-email form-row-wide'),
                    'label' => __('מייל', 'woocommerce'),
                    'required' => true,
                ), $checkout->get_value('recipients[0][email]')); ?>
                <?php woocommerce_form_field('recipients[0][phone]', array(
                    'type' => 'tel',
                    'class' => array('recipient-phone form-row-wide'),
                    'label' => __('טלפון', 'woocommerce'),
                    'required' => true,
                ), $checkout->get_value('recipients[0][phone]')); ?>
                <button type="button" class="remove-recipient-btn">&#215;</button>
            </li>
        </ul>
        <button type="button" class="add-recipient-btn">הוספה</button>
    </div>
</div>
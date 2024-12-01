<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Admin Order Item Custom Attributes Column Template
 * 
 * @var array $custom_attributes
 */
?>

<td class="custom-attributes-cell">
    <ul class="custom-attributes-list">
        <?php foreach ($custom_attributes as $attr_id => $attr_vals) {
            $printing_enabled = get_field('enable_printing', $attr_id);
            $term_id = array_key_first($attr_vals);
            $term_data = $attr_vals[$term_id];
            $image_src = $term_data['image_src'];
            $option_label = $term_data['label'];
            ?>
            <li>
                <em><?php echo get_the_title($attr_id) ?><span>:</span></em>
                <a href="<?php echo $image_src ?>" target="_blank">
                    <img src="<?php echo $image_src ?>" alt="<?php echo $option_label ?>">
                </a>
                <div class="actions-wrapper">
                    <a href="#" class="print-image-link" data-image-src="<?php echo $image_src ?>">הדפסת
                        תמונה</a>
                    <a href="#" class="download-image-link" data-image-src="<?php echo $image_src ?>">הורדת
                        תמונה</a>
                </div>
            </li>
        <?php } ?>
    </ul>
</td>
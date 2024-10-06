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
            ?>
            <li>
                <em><?php echo get_the_title($attr_id) ?></em><span>:</span>
                <ul class="inner-list">
                    <?php foreach ($attr_vals as $attachment_id) {
                        $attachment_url = wp_get_attachment_image_url($attachment_id, 'full');
                        $attacment_title = get_the_title($attachment_id);
                        ?>
                        <li>
                            <?php if ($printing_enabled) { ?>
                                <a href="<?php echo $attachment_url ?>" target="_blank">
                                    <?php echo wp_get_attachment_image($attachment_id, 'thumbnail'); ?>
                                </a>
                            <?php } else {
                                echo "<p> $attacment_title </p>";
                            } ?>
                            <?php if ($printing_enabled) { ?>
                                <div class="actions-wrapper">
                                    <a href="#" class="print-image-link" data-image-src="<?php echo $attachment_url ?>">הדפסת
                                        תמונה</a>
                                    <a href="#" class="download-image-link" data-image-src="<?php echo $attachment_url ?>">הורדת
                                        תמונה</a>
                                </div>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
    </ul>
</td>
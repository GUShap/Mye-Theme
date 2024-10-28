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
                    <?php foreach ($attr_vals as $option_vals) {
                        $image_src = $option_vals['image_src'];
                        $option_label = $option_vals['label'];
                        ?>
                        <li>
                            <?php if ($printing_enabled) { ?>
                                <a href="<?php echo $image_src ?>" target="_blank">
                                    <img src="<?php echo $image_src ?>" alt="<?php echo $option_label ?>">
                                </a>
                            <?php } else {
                                echo "<p> $option_label </p>";
                            } ?>
                            <?php if ($printing_enabled) { ?>
                                <div class="actions-wrapper">
                                    <a href="#" class="print-image-link" data-image-src="<?php echo $image_src ?>">הדפסת
                                        תמונה</a>
                                    <a href="#" class="download-image-link" data-image-src="<?php echo $image_src ?>">הורדת
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
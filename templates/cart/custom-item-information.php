<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom item information
 * 
 * @var array $allergen_list
 * @var array $custom_attributes
 */

 $svg_el = '<svg viewBox="0 0 24 24" fill="none">
            <g id="SVGRepo_iconCarrier">
                <path d="M12 17V11" stroke="#024248" stroke-width="1.5" stroke-linecap="round"></path>
                <circle cx="1" cy="1" r="1" transform="matrix(1 0 0 -1 11 9)" fill="#024248"></circle>
                <path d="M7 3.33782C8.47087 2.48697 10.1786 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 10.1786 2.48697 8.47087 3.33782 7" stroke="#024248" stroke-width="1.5" stroke-linecap="round"></path>
            </g>
        </svg>';
?>

<div class="custom-item-data-container">
    <a href="#" class="custom-item-data-toggle"><?php echo "מידע נוסף $svg_el" ?></a>
    <div class="content">
        <?php
        if (!empty($custom_attributes)) { ?>
            <div class="custom-attributes-wrapper">
                <h3>עיצוב</h3>
                <div class="custom-attributes">
                    <ul class="custom-attributes-list custom-item-list">
                        <?php
                        foreach ($custom_attributes as $attribute_id => $value) {
                            $label = get_the_title($attribute_id);
                            $is_multiple_selection = get_field('multiple_selection', $attribute_id);
                            $is_price_per_option = get_field('price_per_options', $attribute_id);
                            $items_count = count($value);
                            echo "<li>$label";
                            if ($is_multiple_selection && $is_price_per_option) {
                                echo " <p>$items_count x</p>";
                            }
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($allergen_list)) { ?>
            <div class="allergen-list-wrapper">
                <h3>אלרגנים</h3>
                <ul class="allergen-list custom-item-list">
                    <?php
                    foreach ($allergen_list as $allergen) {
                        echo "<li>$allergen</li>";
                    }
                    ?>
                </ul>
            </div>
        <?php } ?>
    </div>
</div>
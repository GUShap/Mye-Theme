<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Order Item Allergens Column Template
 * 
 * @var array $allergens_list
 */
?>

<td class="product-allergens" data-title="<?php esc_attr_e('Allergies', 'woocommerce'); ?>">
    <ul class="allergens-list">
        <?php foreach ($allergens_list as $allergen) { ?>
            <li>
                <?php echo $allergen ?>
            </li>
        <?php } ?>
    </ul>
</td>
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Variable product single attribute
 * 
 * @var string $attribute_name
 * @var array $default_attributes
 * @var array $options
 */

global $product;

$is_boolean = !empty(get_field('is_boolean', "tax_{$attribute_name}_options"));
$is_multiple_options = !empty(get_field('multiple_options', "tax_{$attribute_name}_options"));
$max_options = get_field('max_options', "tax_{$attribute_name}_options");
$option_id = esc_attr(sanitize_title($attribute_name));
$options_label = wc_attribute_label($attribute_name);
?>

<?php if (!$is_boolean && !$is_multiple_options) { ?>
    <tr>
        <th class="label">
            <label for="<?php echo $option_id ?>"><?php echo $options_label ?></label>
        </th>
        <td class="value">
            <?php
            wc_dropdown_variation_attribute_options(
                array(
                    'options' => $options,
                    'attribute' => $attribute_name,
                    'product' => $product,
                )
            );
            ?>
        </td>
    </tr>
<?php } ?>
<?php if ($is_boolean) {
    $is_checked = isset($default_attributes[$attribute_name]) && $default_attributes[$attribute_name] === 'true';
    ?>
    <tr class="boolean-option">
        <th class="label">
            <label for="<?php echo $option_id ?>"><?php echo $options_label ?></label>
        </th>
        <td class="value">
            <div class="input-wrapper type-checkbox">
                <label class="checker">
                    <input class="checkbox" type="checkbox" <?php echo $is_checked ? 'checked' : '' ?> />
                    <div class="check-bg"></div>
                    <div class="checkmark">
                        <svg viewBox="0 0 100 100">
                            <path d="M20,55 L40,75 L77,27" fill="none" stroke="#FFF" stroke-width="15"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </label>
            </div>

            <span class="hidden-select">
                <?php
                wc_dropdown_variation_attribute_options(
                    array(
                        'options' => $options,
                        'attribute' => $attribute_name,
                        'product' => $product,
                    )
                );
                ?>
            </span>
        </td>
    </tr>
<?php } ?>
<?php if (!$is_boolean && $is_multiple_options) { ?>
    <tr class="multiple">
        <th class="label">
            <label for="<?php echo $option_id ?>"><?php echo $options_label ?></label>
        </th>
        <td class="value">
            <div class="input-group-wrapper" data-limit="<?php echo $max_options ?>">
                <?php foreach ($options as $option) {
                    $term = get_term_by('slug', $option, $attribute_name);
                    ?>
                    <div class="checkbox-wrapper">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="checkbox-input" name="multi_options_attr[]" value="<?php echo $option ?>"
                                data-term-id="<?php $term->term_taxonomy_id ?>" />
                            <span class="checkbox-tile">
                                <span class="checkbox-label"><?php echo $term->name ?></span>
                            </span>
                        </label>
                    </div>
                <?php } ?>
            </div>
            <span class="hidden-select">
                <?php
                wc_dropdown_variation_attribute_options(
                    array(
                        'options' => $options,
                        'attribute' => $attribute_name,
                        'product' => $product,
                    )
                );
                ?>
            </span>
        </td>
    </tr>
<?php } ?>
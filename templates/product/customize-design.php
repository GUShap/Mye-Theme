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
                ?>
                <div class="attribute-wrapper<?php echo $is_active ? ' active' : '' ?>">
                    <div class="attr-heading">
                        <button type="button">
                            <?php echo wp_get_attachment_image($icon, 'thumbnail', true) ?>
                            <label for="<?php echo "{$attr_id}_is_active" ?>"><?php echo $title ?></label>
                        </button>
                    </div>
                    <div class="attr-content" data-limit="<?php echo $max_avialable_options ?>">
                        <?php if ($image_upload_enabled) { ?>
                            <div class="source-select-wrapper">
                                <div class="input-wrapper type-radio source">
                                    <input type="radio" class="gallery" name="<?php echo "{$attr_id}_source" ?>"
                                        id="<?php echo "{$attr_id}_source_gallery" ?>" checked>
                                    <label for="<?php echo "{$attr_id}_source_gallery" ?>">בחירה מגלריה</label>
                                </div>
                                <div class="input-wrapper type-radio source">
                                    <input type="radio" class="upload" name="<?php echo "{$attr_id}_source" ?>"
                                        id="<?php echo "{$attr_id}_source_upload" ?>">
                                    <label for="<?php echo "{$attr_id}_source_upload" ?>">העלאת תמונה</label>
                                </div>
                            </div>
                            <div class="image-upload-wrapper">
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-input" accept="image/*" />
                                    <div class="upload-box">
                                        <p>Drag & drop or click to upload</p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="gallery-wrapper active">
                            <?php if ($is_search_enabled) { ?>
                                <div class="searchform-wrapper">
                                    <input type="search" name="<?php echo $attr_id ?>search" id="<?php echo $attr_id ?>search"
                                        placeholder="חיפוש">
                                </div>
                            <?php } ?>
                            <div class="gallery-content">
                                <?php foreach ($options as $options_idx => $option) {
                                    $label = $option['label'];
                                    $value = $option['value'];
                                    $search_terms = $option['search_terms'];
                                    $gallery = $option['reff_gallery'];

                                    $input_type = $multiple_selection ? 'checkbox' : 'radio';
                                    $terms_str = "$label, $search_terms";

                                    foreach ($gallery as $image_idx => $image_id) {
                                        $single_option_template_path = HE_CHILD_THEME_DIR . '/templates/product/single-option.php';
                                        if (file_exists($single_option_template_path)) {
                                            include $single_option_template_path;
                                        }
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if ($enable_custom_text) { ?>
                <div class="attribute-wrapper user-text">
                    <div class="attr-heading">
                        <button type="button">
                            <svg fill="#000000" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_iconCarrier">
                                    <path
                                        d="M16 4H4c-1.101 0-2 .9-2 2v7c0 1.1.899 2 2 2h4l4 3v-3h4c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM6 10.6a1.1 1.1 0 1 1 0-2.2 1.1 1.1 0 0 1 0 2.2zm4 0a1.1 1.1 0 1 1 0-2.2 1.1 1.1 0 0 1 0 2.2zm4 0a1.1 1.1 0 1 1 0-2.2 1.1 1.1 0 0 1 0 2.2z">
                                    </path>
                                </g>
                            </svg>
                            <label for="user_text_is_active">הוספת טקסט</label>
                        </button>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="canvas-wrapper">
            <div class="canvas-controls">
                <button class="rotate-canvas-button left" id="rotate-left" type="button">Rotate Left</button>
                <button class="rotate-canvas-button right" id="rotate-right" type="button">Rotate Right</button>
                <button id="add-text-box" type="button">Add Text Box</button>
            </div>

            <canvas class="image-canvas"></canvas>

        </div>
    </div>
    <div class="footing-wrapper">
        <button class="change-image-btn" style="display: none;">החלפת תמונה</button>
    </div>
</div>
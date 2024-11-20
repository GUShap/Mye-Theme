<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Single Design Attribute
 * 
 * @var string $attr_id
 * @var string $title
 * @var string $icon
 * @var array $options
 * @var bool $is_active
 * @var bool $multiple_selection
 * @var bool $is_search_enabled
 * @var bool $is_user_text_enabled
 * @var bool $image_upload_enabled
 * @var int $max_avialable_options
 */
?>

<div class="attribute-wrapper<?php echo $is_active ? ' active' : '' ?>" data-id="<?php echo $attr_id ?>"
    data-limit="<?php echo $max_avialable_options ?>">
    <div class="attr-heading">
        <button type="button">
            <?php echo wp_get_attachment_image($icon, 'thumbnail', true) ?>
            <label for="<?php echo "{$attr_id}_is_active" ?>"><?php echo $title ?></label>
        </button>
    </div>
    <div class="attr-content">
        <div class="step image-selection-wrapper active">
            <?php if ($image_upload_enabled) { ?>
                <div class="source-select-wrapper">
                    <div class="input-wrapper type-radio source">
                        <input type="radio" class="gallery" name="<?php echo "{$attr_id}_source" ?>"
                            id="<?php echo "{$attr_id}_source_gallery" ?>">
                        <label for="<?php echo "{$attr_id}_source_gallery" ?>">בחירה מגלריה</label>
                    </div>
                    <div class="input-wrapper type-radio source">
                        <input type="radio" class="upload" name="<?php echo "{$attr_id}_source" ?>"
                            id="<?php echo "{$attr_id}_source_upload" ?>" checked>
                        <label for="<?php echo "{$attr_id}_source_upload" ?>">העלאת תמונה</label>
                    </div>
                </div>
                <div class="image-upload-wrapper active">
                    <div class="file-upload-wrapper">
                        <input type="hidden" class="file-input-value" name="<?php echo "attributes[$attr_id]" ?>" value="">
                        <input type="file" class="file-input" accept="image/*" />
                        <div class="upload-box">
                            <p>לבחירת תמונה ניתן לגרור או ללחוץ כאן</p>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="gallery-wrapper">
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
                        $single_option_template_path = HE_CHILD_THEME_DIR . '/templates/product/single-option.php';
                        if (!empty($gallery)) {
                            foreach ($gallery as $image_idx => $image_id) {
                                if (file_exists($single_option_template_path)) {
                                    include $single_option_template_path;
                                }
                            }
                        } else {
                            $image_id = create_image_from_text($label, $value);
                            if (file_exists($single_option_template_path)) {
                                include $single_option_template_path;
                            }
                        }
                    } ?>
                </div>
            </div>
        </div>
        <?php if ($is_user_text_enabled) {
            $rotate_right_svg = get_field('rotate_right_icon', 'option');
            $rotate_left_svg = get_field('rotate_left_icon', 'option');
            $font_family_svg = get_field('font_family_icon', 'option');
            $font_size_svg = get_field('font_size_icon', 'option');
            $font_weight_svg = get_field('font_weight_icon', 'option');
            $align_left_svg = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_iconCarrier"><g id="Edit / Text_Align_Left"> <path id="Vector" d="M4 18H14M4 14H20M4 10H14M4 6H20" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg>';
            $align_right_svg = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"<g id="SVGRepo_iconCarrier"> <g id="Edit / Text_Align_Right"> <path id="Vector" d="M20 18H10M20 14H4M20 10H10M20 6H4" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg>';
            $align_center_svg = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_iconCarrier"> <g id="Edit / Text_Align_Center"> <path id="Vector" d="M17 18H7M20 14H4M17 10H7M20 6H4" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg>';
            $text_color_svg = get_field('text_color_icon', 'option');
            ?>
            <div class="step canvas-container">
                <div class="controls-wrapper">
                    <div class="canvas-controls">
                        <button type="button" class="change-selection-button control-button">החלפת תמונה</button>
                        <button class="rotate-canvas-button right control-button" id="rotate-right"
                            type="button"><?php echo wp_get_attachment_image($rotate_right_svg, 'full', true); ?></button>
                        <button class="rotate-canvas-button left control-button" id="rotate-left"
                            type="button"><?php echo wp_get_attachment_image($rotate_left_svg, 'full', true); ?></button>
                        <button class="control-button" id="add-text-box" type="button">הוספת טקסט</button>
                    </div>
                    <div id="text-box-controls" style="display:none;">
                        <div class="input-wrapper type-select">
                            <label for="font-family"><button type="button"
                                    class="control-button"><?php echo wp_get_attachment_image($font_family_svg, 'full', true); ?></button></label>
                            <select id="font-family">
                                <option value="Alef">Alef</option>
                                <option value="Amatic SC">Amatic SC</option>
                                <option value="Arial">Arial</option>
                                <option value="Arial Black">Arial Black</option>
                                <option value="Assistant">Assistant</option>
                                <option value="Avant Garde">Avant Garde</option>
                                <option value="Baskerville">Baskerville</option>
                                <option value="Bookman">Bookman</option>
                                <option value="Brush Script MT">Brush Script MT</option>
                                <option value="Candara">Candara</option>
                                <option value="Century Gothic">Century Gothic</option>
                                <option value="Comic Sans MS">Comic Sans MS</option>
                                <option value="Courier New">Courier New</option>
                                <option value="DM Sans">DM Sans</option>
                                <option value="Fredoka">Fredoka</option>
                                <option value="Franklin Gothic Medium">Franklin Gothic Medium</option>
                                <option value="Garamond">Garamond</option>
                                <option value="Geneva">Geneva</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Gill Sans">Gill Sans</option>
                                <option value="Handjet">Handjet</option>
                                <option value="Heebo">Heebo</option>
                                <option value="Helvetica">Helvetica</option>
                                <option value="Impact">Impact</option>
                                <option value="Lato">Lato</option>
                                <option value="Lucida Console">Lucida Console</option>
                                <option value="Lucida Sans">Lucida Sans</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Open Sans">Open Sans</option>
                                <option value="Optima">Optima</option>
                                <option value="Oswald">Oswald</option>
                                <option value="Palatino">Palatino</option>
                                <option value="Perpetua">Perpetua</option>
                                <option value="Poppins">Poppins</option>
                                <option value="Quicksand">Quicksand</option>
                                <option value="Roboto">Roboto</option>
                                <option value="Rockwell">Rockwell</option>
                                <option value="Rubik">Rubik</option>
                                <option value="Rubik Beastly">Rubik Beastly</option>
                                <option value="Rubik Bubbles">Rubik Bubbles</option>
                                <option value="Rubik Glitch Pop">Rubik Glitch Pop</option>
                                <option value="Rubik Puddles">Rubik Puddles</option>
                                <option value="Rubik Scribble">Rubik Scribble</option>
                                <option value="Secular One">Secular One</option>
                                <option value="Sofadi One">Sofadi One</option>
                                <option value="Solitreo">Solitreo</option>
                                <option value="Tahoma">Tahoma</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Trebuchet MS">Trebuchet MS</option>
                                <option value="Verdana">Verdana</option>
                            </select>
                        </div>
                        <div class="input-wrapper type-number">
                            <label for="font-size"><button type="button"
                                    class="control-button"><?php echo wp_get_attachment_image($font_size_svg, 'full', true); ?></button></label>
                            <input type="range" id="font-size" value="16" min="8" max="72">
                        </div>
                        <div class="input-wrapper type-number">
                            <label for="font-weight">
                                <button type="button" class="control-button">
                                    <?php echo wp_get_attachment_image($font_weight_svg, 'full', true); ?>
                                </button>
                            </label>
                            <input type="range" id="font-weight" value="300" min="100" max="900">
                        </div>
                        <div class="input-wrapper type-select align-wrapper">
                            <select id="text-align">
                                <option value="left">שמאל</option>
                                <option value="center">מרכז</option>
                                <option value="right" selected>ימין</option>
                            </select>
                            <label for="text-align"><button><?php echo $align_right_svg; ?></button></label>
                            <div class="align-options-wrapper">
                                <button type="button" class="align-button right"
                                data-option="right"><?php echo $align_right_svg; ?></button>
                                <button type="button" class="align-button center"
                                data-option="center"><?php echo $align_center_svg; ?></button>
                                <button type="button" class="align-button left"
                                    data-option="left"><?php echo $align_left_svg; ?></button>
                            </div>
                        </div>
                        <div class="input-wrapper type-color">
                            <label for="text-color">
                                <button type="button" class="control-button" id="text-color-button"
                                    data-val-selector="#text-color">
                                </button>
                            </label>
                            <input type="hidden" id="text-color" value="#000000">
                        </div>
                    </div>
                </div>
                <div class="canvas-wrapper">
                    <img src="" alt="custom preview">
                </div>
            </div>
        <?php } ?>
        <div class="step image-preview<?php echo $multiple_selection ? ' multiple' : '' ?>">
            <div class="actions-wrapper">
                <button type="button" class="change-selection-button">עריכה</button>
            </div>
            <div class="preview-wrapper"></div>
        </div>
    </div>
</div>
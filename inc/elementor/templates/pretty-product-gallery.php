<?php
defined('ABSPATH') || exit;
/**
 *  Pretty Product Gallery Template
 * 
 * @var int $main_image_id
 * @var string $fallback_image_url
 * @var array $gallery_image_ids
 */
?>

<div class="pretty-product-gallery">
    <div class="slider-for">
        <?php if (!empty($main_image_id)) { ?>
            <div class="product-image main-product-image">
                <?php echo wp_get_attachment_image($main_image_id, 'full', false, ['class' => 'gs-zoom-img']) ?>
            </div>
        <?php } else {
            if (!empty($fallback_image_url)) {
                echo '<div class="product-image main-product-image">';
                echo "<img src=\"$fallback_image_url\" alt=\"Product Image\" class=\"gs-zoom-img\">";
                echo '</div>';
            }
        } ?>
        <?php if (!empty($gallery_image_ids)) { ?>
            <?php foreach ($gallery_image_ids as $gallery_image_id) {
                if (empty($gallery_image_id))
                    continue;
                ?>
                <div class="product-image gallery-product-image">
                    <?php echo wp_get_attachment_image($gallery_image_id, 'full', false, ['class' => 'gs-zoom-img']) ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <?php if (!wp_is_mobile()) { ?>
        <div class="slider-nav">
            <?php if (!empty($main_image_id)) { ?>
                <div class="product-image main-product-image">
                    <?php echo wp_get_attachment_image($main_image_id, 'thumbnail', false, ) ?>
                </div>
            <?php } else{
                if (!empty($fallback_image_url)) {
                    echo '<div class="product-image main-product-image">';
                    echo "<img src=\"$fallback_image_url\" alt=\"Product Image\" class=\"gs-zoom-img\">";
                    echo '</div>';
                }
            } ?>
            <?php if (!empty($gallery_image_ids)) { ?>
                <?php foreach ($gallery_image_ids as $gallery_image_id) {
                    if (empty($gallery_image_id))
                        continue;
                    ?>
                    <div class="product-image gallery-product-image">
                        <?php echo wp_get_attachment_image($gallery_image_id, 'thumbnail', false) ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    <?php } ?>
</div>
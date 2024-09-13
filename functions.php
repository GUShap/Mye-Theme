<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 100 );

// END ENQUEUE PARENT ACTION

define('HE_CHILD_THEME_DIR', trailingslashit(get_stylesheet_directory()));

/*******/
require_once HE_CHILD_THEME_DIR . 'inc/admin-functions.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-actions.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-filters.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-shortcodes.php';
require_once HE_CHILD_THEME_DIR . 'inc/admin-hooks.php';
require_once HE_CHILD_THEME_DIR . 'inc/woocommerce-hooks.php';
// END ENQUEUE PARENT ACTION

function enqueue_custom_admin_script()
{
    // Check if it's the product edit page in WooCommerce
    // if (in_array($pagenow, array('post.php', 'post-new.php')) && isset($_GET['post']) && get_post_type($_GET['post']) === 'product') {
    // Enqueue the custom script
    wp_enqueue_script('custom-admin-script', get_stylesheet_directory_uri() . '/assets/js/custom-admin-script.js', array('jquery'), time(), true);
    wp_enqueue_style( 'custom-admin-style', get_stylesheet_directory_uri() . '/assets/css/custom-admin-style.css', array(), time(), 'all' );

    // }
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_script');

function enqueue_custom_script()
{
    if (is_product()) {
        // $cake_data = get_bases_data();
        $siteConfig= [
            'ajaxUrl' =>  admin_url( 'admin-ajax.php' ),
            'productId' => get_the_ID()
        ];
        
        wp_enqueue_script('single-product-script', get_stylesheet_directory_uri() . '/assets/js/single-product-script.js', array(), time(), true);
        wp_localize_script('single-product-script', 'siteConfig',  $siteConfig);
        wp_enqueue_style( 'single-product-style', get_stylesheet_directory_uri() . '/assets/css/variable-product-style.css', array(), time(), 'all' );
    }
    if (is_singular('gallery')) {
        // wp_enqueue_style( 'single-gallery-style', get_stylesheet_directory_uri() . '/assets/css/single-gallery-style.css', array(), time(), 'all' );
        // wp_enqueue_script( 'single-gallery-script', get_stylesheet_directory_uri() . '/assets/js/single-gallery-script.js', array(), time(), true );
    }
    
    if(is_cart()){
        wp_enqueue_script('custom-cart-script', get_stylesheet_directory_uri() . '/assets/js/cart-script.js', array(), time(), true);
        wp_enqueue_style( 'custom-cart-style', get_stylesheet_directory_uri() . '/assets/css/custom-cart-style.css', array(), time(), 'all' );
    }
    if(is_checkout()){
   
        // Enqueue the Hebrew locale file for Flatpickr from CDN.
        // wp_enqueue_script('flatpickr-hebrew-locale', 'https://npmcdn.com/flatpickr/dist/l10n/he.js', array('flatpickr'), null, true);
        wp_enqueue_script('custom-checkout-script', get_stylesheet_directory_uri() . '/assets/js/checkout-script.js', array('jquery'), time(), true);
        wp_enqueue_style( 'custom-checkout-style', get_stylesheet_directory_uri() . '/assets/css/custom-checkout-style.css', array(), time(), 'all' );
        
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_script');

/*******************************/ 
function setStep1($attr_id, $attribute, $has_options, $is_price_per_option)
{ ?>
    <div class="inital-field attr-field-wrapper" data-step="1" data-checked="false"
      data-price="<?php echo $attribute['price'] ?>" data-ppo=<?php echo $is_price_per_option ? "true" : "false" ?>>
      <?php if ($has_options) { ?>
        <button type="button" class="change-options-btn">שינוי</button>
      <?php } ?>
      <label for="<?php echo $attribute['id'] ?>">
        <?php echo $attribute['description'] ?>
      </label>
      <input type="checkbox" name="gs_custom_<?php echo $attribute['id'] ?>" id="<?php echo $attribute['id'] ?>"
        value="<?php echo $attr_id ?>" disabled>
    </div>
  <?php }

function setStep2($attribute, $has_options, $is_price_per_option)
{
  if (!$has_options)
    return;
  $is_multiple_options = !empty($attribute['multiple_options']) ? true : false;
  $input_type = $is_multiple_options ? 'checkbox' : 'radio';
  $max_avialable_options = $attribute['max_avialable_options'];
  $single_option_label = $attribute['single_option_label'];
  $price_disclaimer = 'בתוספת תשלום של ₪' . $attribute['price'];
  $is_filter_available = !empty($attribute['filter_available']) ? true : false;
  $is_sub_options_available = !empty($attribute['sub_options_available']) ? true : false;
  $enable_user_image_upload = !empty($attribute['enable_user_image_upload']) ? true : false;
  $price_disclaimer .= ' עבור ' . $single_option_label;
  ?>
    <div class="options-field attr-field-wrapper" data-step="2">
      <dialog>
        <div class="options-heading-wrapper">
          <button type="button" class="close-btn">&#215;</button>
          <h5 class="options-header">
            <?php echo $attribute['name'] ?>
          </h5>
          <div class="subheading-wrapper">
            <p class="options-instructions">יש לבחור
              <?php echo $max_avialable_options ? $max_avialable_options : 'אחת' ?> מהאפשרויות
            </p>
            <?php if ($is_filter_available)
              set_options_filter(); ?>
          </div>
          <?php if ($attribute['id'] == 'theme') { ?>
            <p class="options-instructions">או</p>
            <div class="user-upload-image-wrapper">
              <label for="user-image-upload">להעלות תמונה שלך</label>
              <input type="file" name="user-image-upload" id="user-image-upload" accept=".jpg, .jpeg, .png, .svg">
              <img src="" alt="user image" id="user-selected-image">
              <button type="button" class="change-options-btn">שינוי</button>
            </div>
          <?php } ?>
        </div>
        <div class="options-list-wrapper" <?php echo $max_avialable_options ? 'data-limit="' . $max_avialable_options . '"' : '' ?>>
          <input type="hidden" name="<?php echo $attribute['id'] ?>-data">
          <?php foreach ($attribute['options'] as $option) {
            ?>
            <div class="option-wrapper<?php echo $is_sub_options_available ? ' has-sub-options' : ''; ?>"
              data-option="<?php echo $option['value'] ?>" <?php echo $is_filter_available ? 'data-terms="' . $option['label'] . ', ' . $option['search_terms'] . '"' : '' ?>>
              <input type="<?php echo $input_type ?>" name="<?php echo $attribute['id'] ?>-option"
                id="<?php echo $option['value'] ?>" value="<?php echo $option['label'] ?>">
              <label for="<?php echo $option['value'] ?>"><span>
                  <?php echo $option['label'] ?>
                </span>
              </label>
              <?php if ($is_sub_options_available)
                set_suboption_images($option['reff_gallery'], $option['value']);
              else {
                if (!empty($option['reff_gallery']))
                  set_option_reff_gallery($option['reff_gallery']);
              } ?>
            </div>
          <?php } ?>
        </div>
        <div class="dialog-footing">
          <p class="options-price-disclaimer">
            <?php echo $price_disclaimer; ?>
          </p>
          <button type="button" class="approval-btn disabled" disabled>אישור</button>
        </div>
      </dialog>
    </div>
  <?php }

function set_suboption_images($gallery, $prefix)
{ ?>
    <div class="sub-items-wrapper">
      <?php foreach ($gallery as $idx => $image_id) { ?>
        <div class="sub-option-field-wrapper">
          <input type="radio" name="<?php echo $prefix . '-sub-option' ?>"
            value="<?php echo wp_get_attachment_image_url($image_id, 'full'); ?>" id="<?php echo $prefix . '-' . ++$idx ?>">
          <label for="<?php echo str_replace(' ', '', $prefix) . '-' . $idx ?>">
            <?php echo wp_get_attachment_image($image_id, 'full') ?>
          </label>
        </div>
      <?php } ?>
    </div>
  <?php }

function set_option_reff_gallery($gallery)
{ ?>
    <div class="option-gallery-wrapper">
      <?php foreach ($gallery as $image_id) { ?>
        <figure>
          <?php echo wp_get_attachment_image($image_id, 'full') ?>
        </figure>
      <?php } ?>
    </div>
  <?php }

function set_options_filter()
{ ?>
    <div class="option-filter-wrapper">
      <input type="text" name="" id="option-search-input" placeholder="&#128269; חיפוש נושא לעוגה...">
    </div>
  <?php }

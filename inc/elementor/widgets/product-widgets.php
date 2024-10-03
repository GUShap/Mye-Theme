<?php

namespace Elementor;

if (!defined('ABSPATH'))
    exit;


class Pretty_Product_Gallery_Widget extends Widget_Base
{

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        wp_enqueue_script('single-product-script', get_stylesheet_directory_uri() . '/assets/js/single-product-script.js', array('jquery', 'slick'), time(), true);
        wp_enqueue_style( 'single-product-style', get_stylesheet_directory_uri() . '/assets/css/variable-product-style.css', array(), time(), 'all' );
    }
    public function get_name()
    {
        return 'pretty_product_gallery';
    }

    public function get_title()
    {
        return __('Pretty Product Gallery', 'text-domain');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid';
    }

    public function get_categories()
    {
        return ['gs_widgets'];
    }

    public function get_script_depends()
    {
        return ['slick-carousel', 'single-product-script'];
    }

    public function get_style_depends()
    {
        return ['slick-style', 'slick-lightbox-style', 'single-product-style'];
    }

    protected function register_controls()
    {
// fallback image
        $this->start_controls_section(
            'fallback_image_section',
            [
                'label' => __('Fallback Image', 'text-domain'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'fallback_image_url',
            [
                'label' => __('Fallback Image URL', 'text-domain'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->end_controls_section();

        // main image
        $this->start_controls_section(
            'main_image_section',
            [
                'label' => __('Main Image', 'text-domain'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'main_image_id',
            [
                'label' => __('Main Image ID', 'text-domain'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'id' => '',
                ],
            ]
        );

        $this->end_controls_section();

        // gallery images
        $this->start_controls_section(
            'gallery_images_section',
            [
                'label' => __('Gallery Images', 'text-domain'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'gallery_image_ids',
            [
                'label' => __('Gallery Image IDs', 'text-domain'),
                'type' => Controls_Manager::GALLERY,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $product_id = get_the_ID();
        $product = wc_get_product($product_id);
        $main_image_id = !empty($product) ? $product->get_image_id() : '';
        $gallery_image_ids = !empty($product) ? $product->get_gallery_image_ids() : [];
        $fallback_image_url = $settings['fallback_image_url']['url'];

        $pretty_product_gallery_template_path = HE_CHILD_THEME_DIR . 'inc/elementor/templates/pretty-product-gallery.php';
        if (file_exists($pretty_product_gallery_template_path)) {
            include $pretty_product_gallery_template_path;
        }
    }
}
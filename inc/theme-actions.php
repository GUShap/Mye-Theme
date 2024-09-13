<?php
add_action('cake_svg_45', function () {
  require_once CAKECIUOS_CHILD_THEME_DIR . 'assets/images/cake-svg-45.php';
});

add_action('cake_svg_180', function () {
  require_once CAKECIUOS_CHILD_THEME_DIR . 'assets/images/cake-svg-180.php';
});

add_action('cake_svg_270', function () {
  require_once CAKECIUOS_CHILD_THEME_DIR . 'assets/images/cake-svg-270.php';
});

// Add this code to your theme's functions.php or a custom plugin file

// Callback function for the AJAX action
function get_cake_themes_callback()
{
  $taxonomy = 'pa_' . sanitize_title('cake_theme');
  $theme_options = get_terms([
    'taxonomy' => $taxonomy,
    'hide_empty' => false,
  ]);
  $data = [];

  foreach ($theme_options as $idx => $option) {
    $theme_images = get_field('theme_images', $option);
    $search_terms = make_array_shallow(get_field('search_terms', $option));
    $search_terms[] = $option->name;

    if (!empty($theme_images)) {
      $images = [];

      foreach ($theme_images as $inner_idx => $image_id) {
        $images[$inner_idx] = wp_get_attachment_image($image_id, 'full');
      }

      $data[] = [
        'name' => $option->name,
        'slug' => $option->slug,
        'search_term' => $search_terms,
        'images' => $images
      ];
    }
  }
  // You can do further processing on $theme_options if needed

  // Return the data as JSON
  wp_send_json($data);
}

// Hook for the AJAX action in WordPress
add_action('wp_ajax_get_cake_themes', 'get_cake_themes_callback');
add_action('wp_ajax_nopriv_get_cake_themes', 'get_cake_themes_callback');

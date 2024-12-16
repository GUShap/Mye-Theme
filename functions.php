<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

// BEGIN ENQUEUE PARENT ACTION

// END ENQUEUE PARENT ACTION

define('HE_CHILD_THEME_DIR', trailingslashit(get_stylesheet_directory()));
define('HE_CHILD_THEME_URI', trailingslashit(get_stylesheet_directory_uri()));

/*******/
require_once HE_CHILD_THEME_DIR . 'inc/admin-functions.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-actions.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-filters.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-shortcodes.php';
require_once HE_CHILD_THEME_DIR . 'inc/theme-ajax.php';
require_once HE_CHILD_THEME_DIR . 'inc/admin-hooks.php';
require_once HE_CHILD_THEME_DIR . 'inc/woocommerce-hooks.php';
// END ENQUEUE PARENT ACTION

function set_item_custom_information($cart_item)
{
	$allergen_list = $cart_item['allergen_list'] ?? [];
	$custom_attributes = $cart_item['custom_attributes'] ?? [];
	$writing_text = $cart_item['cake_writing'] ?? '';
	if (empty($allergen_list) && empty($custom_attributes) && empty($writing_text))
		return;
	$custom_item_information_template_path = HE_CHILD_THEME_DIR . '/templates/cart/custom-item-information.php';
	if (file_exists($custom_item_information_template_path)) {
		include $custom_item_information_template_path;
	}
}

function set_custom_variation_attr_list($cart_item)
{
	$variation_id = $cart_item['variation_id'];
	$product = wc_get_product($variation_id);
	$variation_attributes = $product->get_variation_attributes();
	$multiple_options_attributes = $cart_item['multiple_options_attributes'] ?? [];
	$variation_info = '';

	if (!empty($multiple_options_attributes)) {
		foreach ($multiple_options_attributes as $attr_slug => $options) {
			// get both options term->name as a string
			$taxonomy = get_taxonomy($attr_slug);
			$label = $taxonomy->labels->singular_name;
			$variation_attributes["attribute_$attr_slug"] = implode(', ', array_map(function ($option) use ($attr_slug) {
				$term = get_term_by('slug', $option, $attr_slug);
				return $term->name;
			}, $options));


		}
	}
	if (!empty($variation_attributes)) {
		$variation_info = '<ul class="variation-info">';
		foreach ($variation_attributes as $attribute_name => $term_value) {
			$taxonomy = str_replace('attribute_', '', $attribute_name);
			$term = get_term_by('slug', $term_value, $taxonomy);
			$label = wc_attribute_label($taxonomy);
			$term_name = !empty($term) ? $term->name : $term_value;
			if ($term_value !== 'false') {
				$variation_info .= '<li class="variation-attribute">' . wc_attribute_label($label);
				$variation_info .= $term_value == 'true'
					? '</li>'
					: " : $term_name</li>";
			}
		}
		$variation_info .= '</ul>';
	}

	echo $variation_info;
}

function set_order_magicwa_message($order_id, $order_recipients)
{
	$messages_data = [];

	if (empty($order_recipients))
		return;
	// Check if there are recipients to process
	foreach ($order_recipients as $index => $recipient) {
		// Check if the recipient's status is 'pending'
		if (isset($recipient['status']) && $recipient['status'] !== 'pending')
			continue;
		// Extract the phone number
		$phone = $recipient['phone'];
		$message = "היי {$recipient['name']}, התקבלה אצלנו הזמנת קינוח עבורך, מספר הזמנה *{$order_id}* 🎂🎂🎂. כדי שכולם/ן יוכלו להנות עליך למלא טופס אישור רכיבים בקישור הבא https://shorturl.at/lOKpO";
		$messages_data[$phone] = $message;

	}
	// Run the WhatsApp function to send a message
	magic_wa_send_multiple_messages_handler($order_id, $messages_data);

	// send_multiple_messages($messages_data);
}

function set_order_email_message($order_id, $order_recipients)
{
	$order = wc_get_order($order_id);
	$ingredients_form_url = get_option('ingredients_form_url', '');
	$order_pickup_date = get_post_meta($order_id, 'pickup_date', true);
	$recipient_email_message_template_path = HE_CHILD_THEME_DIR . '/woocommerce/emails/email-order-recipient.php';
	foreach ($order_recipients as $recipient) {
		if ($recipient['email'] === $order->get_billing_email())
			continue;
		$to = $recipient['email'];
		$subject = "אישור רכיבים להזמנה מספר $order_id";
		$message = '';
		if (file_exists($recipient_email_message_template_path)) {
			ob_start();
			include $recipient_email_message_template_path;
			$message = ob_get_clean();
			$message = str_replace('{site_title}', get_bloginfo('name'), $message);
		}
		$res = wp_mail($to, $subject, $message, ['Content-Type: text/html; charset=UTF-8']);
		// dd($res);
	}
}

/******************************/
add_action('template_redirect', function () {
	$order_id = 13164;
	$order_recipients = get_post_meta($order_id, '_order_recipients', true);

	// set_order_email_message($order_id, $order_recipients);
	// print_r(wp_mail('gushap2021@gmail.com', 'order_recipients', 'test', ['Content-Type: text/html; charset=UTF-8']));
	// set_order_email_message($order_id, $order_recipients);
});
// add_action('template_redirect', function () {
// 	return;
// 	$product_id = 1672; // Your product ID
// 	$product = wc_get_product($product_id);
// 	$variations = $product->get_children();
// 	foreach ($variations as $variation_id) {
// 		$variation_price = 0;
// 		$variation = wc_get_product($variation_id);  // Get variation object
// 		$attributes = $variation->get_attributes();
// 		foreach ($attributes as $attr_slug => $attr_val) {
// 			if (empty($attr_val)) {
// 				wp_delete_post($variation_id, true);
// 				continue 1;
// 			}
// 			switch ($attr_slug) {
// 				case 'pa_mini-cupcakes-quantity':
// 					if ($attr_val == '24')
// 						$variation_price += 270;
// 					if ($attr_val == '40')
// 						$variation_price += 300;
// 					break;
// 				case 'pa_gluten':
// 					if ($attr_val == 'true')
// 						$variation_price += 30;
// 					break;
// 				case 'pa_vegan':
// 					if ($attr_val == 'true')
// 						$variation_price += 20;
// 					break;
// 				case 'pa_chocolate_ganach':
// 					if ($attr_val == 'true')
// 						$variation_price += 35;
// 					break;
// 			}
// 		}
// 		update_post_meta($variation_id, '_regular_price', $variation_price);
// 	}
// });
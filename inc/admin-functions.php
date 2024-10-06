<?php
function make_array_shallow($array)
{
    $shallowArray = array();

    foreach ($array as $value) {
        if (is_array($value)) {
            $shallowArray = array_merge($shallowArray, make_array_shallow($value));
        } else {
            $shallowArray[] = $value;
        }
    }

    return $shallowArray;
}

function dd($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

function flatten_array($array)
{
    $flatArray = [];

    foreach ($array as $item) {
        if (is_array($item)) {
            // Recursively flatten the array
            $flatArray = array_merge($flatArray, flatten_array($item));
        } else {
            // If not an array, add the value directly
            $flatArray[] = $item;
        }
    }

    return $flatArray;
}

// Function to save base64 image as a file in WordPress uploads directory
function save_user_image_as_file($base64_image)
{
    // Ensure the base64 string is valid and properly formatted
    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
        $base64_image = substr($base64_image, strpos($base64_image, ',') + 1);
        $base64_image = base64_decode($base64_image);

        // Generate a unique filename
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'] . '/custom_clients_images/';

        // Create the directory if it doesn't exist
        if (!file_exists($upload_path)) {
            wp_mkdir_p($upload_path);
        }

        // Generate a unique filename
        $image_type = strtolower($type[1]); // jpeg, png, etc.
        $file_name = 'custom-image-' . wp_generate_password(8, false) . '.' . $image_type;
        $file_path = $upload_path . $file_name;

        // Save the file
        if (file_put_contents($file_path, $base64_image)) {
            // Prepare an array of attachment data
            $filetype = wp_check_filetype(basename($file_path), null);
            $attachment = array(
                'guid' => $upload_dir['url'] . '/custom_clients_images/' . basename($file_path),
                'post_mime_type' => $filetype['type'],
                'post_title' => sanitize_file_name($file_name),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            // Insert the attachment post into the database
            $attach_id = wp_insert_attachment($attachment, $file_path);

            // Generate attachment metadata and update the database
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // Return the attachment ID (or you can return the URL)
            return $attach_id; // Or wp_get_attachment_url($attach_id) to return the URL
        } else {
            // Failed to save the image
            return false;
        }
    } else {
        // Invalid base64 string
        return false;
    }
}

function create_image_from_text($text, $name, $font_size = 20, $font_color = [0, 0, 0], $bg_color = [255, 255, 255])
{
    // Define the font path
    $font_path = HE_CHILD_THEME_DIR . 'assets/fonts/RubikDirt-Regular.ttf';

    // Sanitize the text to create a valid filename
    $sanitized_text = sanitize_title($name); // Converts to a URL-friendly string
    $upload_dir = wp_upload_dir();
    $temp_dir = $upload_dir['basedir'] . '/temp-images/'; // Path to store temporary images
    $image_path = "{$temp_dir}image_$sanitized_text.png"; // Create a consistent filename

    // Check if the image already exists
    if (file_exists($image_path)) {
        // Return the existing image id
        $url = $upload_dir['baseurl'] . '/temp-images/image_' . $sanitized_text.'.png';
        // dd($url);
        return attachment_url_to_postid($url);
    }
    $is_rtl = (bool) preg_match('/[\x{0591}-\x{07FF}]/u', $text); // Check for Hebrew or Arabic characters
    $text = $is_rtl
        ? mb_strrev($text) // Reverse the text if it's RTL
        : $text;
    // Set the dimensions of the image
    $width = 300;
    $height = 100;

    // Create a blank image
    $image = imagecreatetruecolor($width, $height);

    // Allocate colors
    $bg_color_alloc = imagecolorallocate($image, $bg_color[0], $bg_color[1], $bg_color[2]);
    $font_color_alloc = imagecolorallocate($image, $font_color[0], $font_color[1], $font_color[2]);

    // Fill the background color
    imagefilledrectangle($image, 0, 0, $width, $height, $bg_color_alloc);

    // Add the text to the image
    $text_bounding_box = imagettfbbox($font_size, 0, $font_path, $text);
    $text_width = $text_bounding_box[2] - $text_bounding_box[0];
    $text_height = $text_bounding_box[1] - $text_bounding_box[7];


    // Center vertically
    $y = ($height - $text_height) / 2 + $text_height;
    $x = ($width - $text_width) / 2;

    // Add text to the image
    imagettftext($image, $font_size, 0, $x, $y, $font_color_alloc, $font_path, $text);

    // Create the temporary directory if it doesn't exist
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0755, true); // Create the directory
    }

    // Save the image to the temporary directory
    imagepng($image, $image_path);
    imagedestroy($image);

    // Check if the image file was created successfully
    if (!file_exists($image_path)) {
        error_log('Image not created: ' . $image_path);
    }

    $attachment = array(
        'guid' => $upload_dir['url'] . '/temp-images/' . basename($image_path),
        'post_mime_type' => 'image/png',
        'post_title' => $text,
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attachment_id = wp_insert_attachment($attachment, $image_path);

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attachment_id, $image_path);
    wp_update_attachment_metadata($attachment_id, $attach_data);
    // Return the URL of the generated image
    return $attachment_id;
}

function mb_strrev($text)
{
    $length = mb_strlen($text);
    $reversed = '';
    while ($length-- > 0) {
        $reversed .= mb_substr($text, $length, 1);
    }
    return $reversed;
}

function send_whatsapp_message($number, $message) {
    $url = 'http://localhost:3000/send-message'; // URL of your local Node.js API

    $body = array(
        'number' => $number,  // For example: '972545970911'
        'message' => $message // For example: 'Hello from WordPress!'
    );

    $response = wp_remote_post($url, array(
        'method'    => 'POST',
        'body'      => wp_json_encode($body),
        'headers'   => array(
            'Content-Type' => 'application/json',
        ),
    ));

    if (is_wp_error($response)) {
        error_log('Error sending WhatsApp message: ' . $response->get_error_message());
    } else {
        error_log('WhatsApp message sent successfully!');
    }
}

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

function flatten_array($array) {
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
function save_user_image_as_file($base64_image) {
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
            // Return the image URL
            return $upload_dir['baseurl'] . '/custom_clients_images/' . $file_name;
        } else {
            // Failed to save the image
            return false;
        }
    } else {
        // Invalid base64 string
        return false;
    }
}
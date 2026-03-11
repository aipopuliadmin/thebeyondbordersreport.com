<?php
/**
 * Fix missing featured image for post ID 502
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

// Image source
$image_url = 'https://picsum.photos/1200/675';

/**
 * Download and attach featured image
 */
function download_featured_image($post_id, $image_url) {
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    $post = get_post($post_id);
    if (!$post) {
        return false;
    }
    
    // Add random seed to get different images
    $image_url_with_seed = $image_url . '?random=' . rand(1000, 9999) . time();
    
    // Download image
    $tmp = download_url($image_url_with_seed);
    
    if (is_wp_error($tmp)) {
        return $tmp;
    }
    
    // Get file extension
    $file_array = array(
        'name'     => sanitize_file_name($post->post_title) . '-' . rand(100, 999) . '.jpg',
        'tmp_name' => $tmp
    );
    
    // Upload to media library
    $id = media_handle_sideload($file_array, $post_id);
    
    // Clean up temp file
    if (file_exists($tmp)) {
        @unlink($tmp);
    }
    
    if (is_wp_error($id)) {
        return $id;
    }
    
    // Set as featured image
    set_post_thumbnail($post_id, $id);
    
    return $id;
}

$post_id = 502;

echo "Attempting to attach featured image to post ID: {$post_id}\n";

$result = download_featured_image($post_id, $image_url);

if (is_wp_error($result)) {
    echo "✗ Failed: " . $result->get_error_message() . "\n";
} elseif ($result) {
    echo "✓ Success! Featured image attached (ID: {$result})\n";
} else {
    echo "✗ Failed: Unknown error\n";
}

echo "\nDone!\n";

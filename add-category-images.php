<?php
/**
 * Add Featured Images to Categories
 * Downloads and assigns images to all categories
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

// Increase execution time and memory
set_time_limit(0);
ini_set('memory_limit', '512M');

// Image source
$image_url = 'https://picsum.photos/1200/675';

/**
 * Download and attach image to category
 */
function attach_category_image($term_id, $image_url, $category_name) {
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    // Add random seed to get different images
    $image_url_with_seed = $image_url . '?random=' . rand(1000, 9999) . time();
    
    // Download image
    $tmp = download_url($image_url_with_seed);
    
    if (is_wp_error($tmp)) {
        return false;
    }
    
    // Get file extension
    $file_array = array(
        'name'     => sanitize_file_name($category_name) . '-category-' . rand(100, 999) . '.jpg',
        'tmp_name' => $tmp
    );
    
    // Upload to media library (use post_id = 0 for unattached)
    $attachment_id = media_handle_sideload($file_array, 0);
    
    // Clean up temp file
    if (file_exists($tmp)) {
        @unlink($tmp);
    }
    
    if (is_wp_error($attachment_id)) {
        return false;
    }
    
    // Save attachment ID to category meta
    update_term_meta($term_id, 'category_image_id', $attachment_id);
    
    return $attachment_id;
}

// Get all categories
$categories = get_categories([
    'hide_empty' => false,
    'exclude' => [1], // Exclude 'Uncategorized'
]);

echo "Found " . count($categories) . " categories\n";
echo "Starting category image upload...\n\n";

$total_images = 0;
$failed_images = 0;

foreach ($categories as $category) {
    echo "Processing: {$category->name} (ID: {$category->term_id})\n";
    
    // Check if category already has an image
    $existing_image = get_term_meta($category->term_id, 'category_image_id', true);
    
    if ($existing_image) {
        echo "  ⊙ Already has image (ID: {$existing_image})\n";
        $total_images++;
    } else {
        // Download and attach image
        sleep(1); // Small delay to avoid rate limiting
        $image_id = attach_category_image($category->term_id, $image_url, $category->name);
        
        if ($image_id) {
            $total_images++;
            echo "  ✓ Image attached (ID: {$image_id})\n";
        } else {
            $failed_images++;
            echo "  ✗ Failed to attach image\n";
        }
    }
}

echo "\n========================================\n";
echo "SUMMARY\n";
echo "========================================\n";
echo "Total categories: " . count($categories) . "\n";
echo "Images successfully attached: {$total_images}\n";
echo "Images failed: {$failed_images}\n";
echo "========================================\n";

if ($failed_images > 0) {
    echo "\nNote: You can run this script again to retry failed images.\n";
}

echo "\nDone!\n";
echo "\nTo display category images in your theme, use:\n";
echo "\$image_id = get_term_meta(\$category_id, 'category_image_id', true);\n";
echo "\$image_url = wp_get_attachment_image_url(\$image_id, 'full');\n";

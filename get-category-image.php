<?php
/**
 * Get category image information
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

$category_id = 32;

// Get category details
$category = get_term($category_id, 'category');

if (!$category || is_wp_error($category)) {
    echo "Category not found!\n";
    exit;
}

echo "Category: {$category->name}\n";
echo "Slug: {$category->slug}\n";
echo "ID: {$category->term_id}\n\n";

// Get category image
$image_id = get_term_meta($category_id, 'category_image_id', true);

if ($image_id) {
    echo "Image ID: {$image_id}\n";
    
    $image_url = wp_get_attachment_image_url($image_id, 'full');
    $image_url_medium = wp_get_attachment_image_url($image_id, 'medium');
    $image_url_thumbnail = wp_get_attachment_image_url($image_id, 'thumbnail');
    
    echo "Full URL: {$image_url}\n";
    echo "Medium URL: {$image_url_medium}\n";
    echo "Thumbnail URL: {$image_url_thumbnail}\n\n";
    
    // Get image metadata
    $metadata = wp_get_attachment_metadata($image_id);
    if ($metadata) {
        echo "Dimensions: {$metadata['width']} x {$metadata['height']}\n";
        echo "File: {$metadata['file']}\n";
    }
} else {
    echo "No image found for this category.\n";
}

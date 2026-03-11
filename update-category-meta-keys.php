<?php
/**
 * Update category images to use the correct meta key
 * Copies category_image_id to category_featured_image for admin display
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

// Get all categories
$categories = get_categories([
    'hide_empty' => false,
    'exclude' => [1], // Exclude 'Uncategorized'
]);

echo "Updating category image meta keys...\n\n";

$updated = 0;

foreach ($categories as $category) {
    $image_id = get_term_meta($category->term_id, 'category_image_id', true);
    
    if ($image_id) {
        // Update to the meta key the theme expects
        update_term_meta($category->term_id, 'category_featured_image', $image_id);
        update_term_meta($category->term_id, 'featured_image', $image_id);
        
        echo "✓ Updated: {$category->name} (ID: {$category->term_id}) - Image ID: {$image_id}\n";
        $updated++;
    }
}

echo "\n========================================\n";
echo "SUMMARY\n";
echo "========================================\n";
echo "Total categories updated: {$updated}\n";
echo "========================================\n";
echo "\nDone!\n";

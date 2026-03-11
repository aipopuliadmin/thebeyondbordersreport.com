<?php
/**
 * Generate Posts with Featured Images
 * Creates 5 posts per category with downloaded featured images
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

// Increase execution time and memory
set_time_limit(0);
ini_set('memory_limit', '512M');

// Image sources - using placeholder services that work reliably
$image_services = [
    'https://picsum.photos/1200/675', // Lorem Picsum - reliable random images
];

// Travel retail related keywords for varied images
$image_keywords = [
    'airport', 'travel', 'luxury', 'shopping', 'retail', 'business', 
    'aviation', 'hospitality', 'lounge', 'duty-free', 'passenger',
    'terminal', 'airplane', 'journey', 'destination', 'store'
];

// Post title templates by category type
$title_templates = [
    'default' => [
        '{Brand} Expands {Category} Presence in {Location}',
        'New {Category} Innovations Transform Travel Retail',
        '{Location} Airport Unveils Enhanced {Category} Experience',
        'Industry Leaders Discuss Future of {Category}',
        'Strategic Partnership Boosts {Category} Performance',
    ],
    'news' => [
        'Breaking: {Brand} Announces Major {Category} Initiative',
        '{Location} Travel Retail Reports Record {Category} Growth',
        'Industry Update: {Category} Trends Reshape Market',
        'Exclusive: New Regulations Impact {Category} Sector',
        '{Brand} Launches Revolutionary {Category} Strategy',
    ],
    'analysis' => [
        'Deep Dive: Understanding {Category} Market Dynamics',
        'Expert Analysis: {Category} Performance Metrics Explained',
        'Market Trends: What\'s Driving {Category} Growth',
        'Comparative Study: {Category} Across Major Airports',
        'Long-term Outlook: {Category} Industry Forecast',
    ]
];

// Locations for variety
$locations = [
    'Dubai', 'Singapore', 'London Heathrow', 'Paris CDG', 'Hong Kong',
    'Frankfurt', 'Amsterdam Schiphol', 'Tokyo Narita', 'Seoul Incheon',
    'Los Angeles', 'New York JFK', 'Sydney', 'Barcelona', 'Istanbul'
];

// Brands for variety
$brands = [
    'DFS', 'Dufry', 'Lagardère', 'Lotte Duty Free', 'The Shilla',
    'Heinemann', 'King Power', 'China Duty Free', 'Dubai Duty Free',
    'Gebr. Heinemann', 'Nuance', 'Aer Rianta', 'World Duty Free'
];

/**
 * Download and attach featured image
 */
function download_featured_image($post_id, $image_url, $post_title) {
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
        'name'     => sanitize_file_name($post_title) . '-' . rand(100, 999) . '.jpg',
        'tmp_name' => $tmp
    );
    
    // Upload to media library
    $id = media_handle_sideload($file_array, $post_id);
    
    // Clean up temp file
    if (file_exists($tmp)) {
        @unlink($tmp);
    }
    
    if (is_wp_error($id)) {
        return false;
    }
    
    // Set as featured image
    set_post_thumbnail($post_id, $id);
    
    return $id;
}

/**
 * Generate post content
 */
function generate_post_content($category_name, $title) {
    $paragraphs = [
        "In a significant development for the travel retail industry, {title} marks a strategic shift in how operators approach the modern passenger experience. Industry analysts suggest this move reflects broader trends reshaping duty-free and travel retail operations worldwide.",
        
        "The initiative comes at a crucial time as international travel continues its recovery trajectory. Key stakeholders emphasize the importance of innovation and customer-centric approaches in maintaining competitive advantage within the evolving travel retail landscape.",
        
        "Market observers note that this development aligns with emerging consumer preferences for enhanced shopping experiences, digital integration, and sustainable practices. The industry's response demonstrates a commitment to meeting these evolving expectations.",
        
        "According to industry experts, such strategic moves are essential for long-term growth and market positioning. The focus on operational excellence and customer engagement reflects a mature understanding of travel retail dynamics in today's competitive environment.",
        
        "This announcement is expected to influence broader market trends, with industry participants closely monitoring developments and potential implications for their own strategic planning. The travel retail sector continues to demonstrate resilience and adaptability in response to changing market conditions."
    ];
    
    // Replace placeholder in first paragraph
    $paragraphs[0] = str_replace('{title}', strtolower($title), $paragraphs[0]);
    
    // Select 3-4 random paragraphs
    shuffle($paragraphs);
    $selected = array_slice($paragraphs, 0, rand(3, 4));
    
    return implode("\n\n", $selected);
}

/**
 * Generate post title
 */
function generate_title($category_name, $templates, $locations, $brands) {
    $template = $templates[array_rand($templates)];
    
    $replacements = [
        '{Category}' => $category_name,
        '{Location}' => $locations[array_rand($locations)],
        '{Brand}' => $brands[array_rand($brands)],
    ];
    
    return str_replace(array_keys($replacements), array_values($replacements), $template);
}

// Get all categories
$categories = get_categories([
    'hide_empty' => false,
    'exclude' => [1], // Exclude 'Uncategorized'
]);

echo "Found " . count($categories) . " categories\n";
echo "Starting post generation...\n\n";

$total_created = 0;
$total_images = 0;
$failed_images = 0;

foreach ($categories as $category) {
    echo "Processing category: {$category->name} (ID: {$category->term_id})\n";
    
    // Determine template type based on category
    $template_type = 'default';
    $cat_slug_lower = strtolower($category->slug);
    if (strpos($cat_slug_lower, 'news') !== false || strpos($cat_slug_lower, 'update') !== false) {
        $template_type = 'news';
    } elseif (strpos($cat_slug_lower, 'analysis') !== false || strpos($cat_slug_lower, 'insight') !== false) {
        $template_type = 'analysis';
    }
    
    $templates = $title_templates[$template_type];
    
    // Create 5 posts for this category
    for ($i = 1; $i <= 5; $i++) {
        $title = generate_title($category->name, $templates, $locations, $brands);
        $content = generate_post_content($category->name, $title);
        
        // Create post
        $post_data = [
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_category' => [$category->term_id],
            'post_author'   => 1,
            'post_date'     => date('Y-m-d H:i:s', strtotime('-' . rand(1, 90) . ' days')),
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            $total_created++;
            echo "  ✓ Created post #{$i}: {$title} (ID: {$post_id})\n";
            
            // Download and set featured image
            sleep(1); // Small delay to avoid rate limiting
            $image_url = $image_services[0];
            $image_id = download_featured_image($post_id, $image_url, $title);
            
            if ($image_id) {
                $total_images++;
                echo "    ✓ Featured image attached (ID: {$image_id})\n";
            } else {
                $failed_images++;
                echo "    ✗ Failed to attach image\n";
            }
            
            // Add some post meta for variety
            $view_count = rand(50, 5000);
            update_post_meta($post_id, 'post_views_count', $view_count);
            
        } else {
            echo "  ✗ Failed to create post #{$i}\n";
        }
    }
    
    echo "\n";
}

echo "\n========================================\n";
echo "SUMMARY\n";
echo "========================================\n";
echo "Total posts created: {$total_created}\n";
echo "Images successfully attached: {$total_images}\n";
echo "Images failed: {$failed_images}\n";
echo "========================================\n";

if ($failed_images > 0) {
    echo "\nNote: Some images failed to download. You can:\n";
    echo "1. Run this script again to retry failed images\n";
    echo "2. Manually upload images through WordPress admin\n";
    echo "3. Use a different image source\n";
}

echo "\nDone!\n";

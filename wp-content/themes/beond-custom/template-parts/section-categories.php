<?php
/**
 * Template part for categories showcase section
 *
 * @package Beond_Custom
 */

$homepage_settings = get_option('beyond_borders_homepage_settings', array());

if (empty($homepage_settings['enable_categories'])) {
    return;
}

$categories_heading = $homepage_settings['categories_heading'] ?? 'Explore Our Coverage';
$categories_description = $homepage_settings['categories_description'] ?? 'Deep insights across luxury retail, travel, and global business.';
$categories_count = $homepage_settings['categories_display_count'] ?? 6;

$categories = get_categories(array(
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => $categories_count,
    'hide_empty' => true,
));

if (empty($categories)) {
    return;
}
?>

<section class="floating-categories">
    <div class="categories-wrapper">
        <div class="section-header-modern center">
            <?php if ($categories_heading) : ?>
                <h2><?php echo esc_html($categories_heading); ?></h2>
            <?php endif; ?>
        </div>
        
        <div class="categories-image-grid">
            <?php foreach ($categories as $category) : 
                $post_count = $category->count;
                
                // Get category image from custom settings
                $category_image = '';
                
                // Check various possible meta keys for featured image
                $possible_keys = array(
                    'featured_image',
                    'category_featured_image',
                    'showcase_image',
                    'category_image',
                    'thumbnail_id'
                );
                
                foreach ($possible_keys as $key) {
                    $value = get_term_meta($category->term_id, $key, true);
                    
                    if (!empty($value)) {
                        // Check if it's an attachment ID (number)
                        if (is_numeric($value)) {
                            $image_url = wp_get_attachment_image_url($value, 'large');
                            if ($image_url) {
                                $category_image = $image_url;
                                break;
                            }
                        } 
                        // Check if it's a direct URL
                        elseif (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                                $category_image = $value;
                                break;
                            }
                        }
                    }
                }
                
                // Skip category if no valid image found
                if (empty($category_image)) {
                    continue;
                }
            ?>
                <a href="<?php echo get_category_link($category->term_id); ?>" class="category-image-card">
                    <div class="category-image">
                        <img src="<?php echo esc_url($category_image); ?>" alt="<?php echo esc_attr($category->name); ?>">
                    </div>
                    <div class="category-overlay">
                        <div class="category-overlay-content">
                            <h3><?php echo esc_html($category->name); ?></h3>
                            <p class="count"><?php echo $post_count; ?> <?php echo ($post_count > 1) ? 'Articles' : 'Article'; ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

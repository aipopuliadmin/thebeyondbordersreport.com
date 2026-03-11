<?php
/**
 * Template part for featured grid section
 *
 * @package Beond_Custom
 */

$homepage_settings = get_option('beyond_borders_homepage_settings', array());

if (empty($homepage_settings['enable_featured_grid'])) {
    return;
}

$featured_heading = $homepage_settings['featured_grid_heading'] ?? 'Featured Analysis';
$featured_posts_count = $homepage_settings['featured_grid_posts'] ?? 5;
$button_text = $homepage_settings['featured_grid_button_text'] ?? 'View All';
$button_link = $homepage_settings['featured_grid_button_link'] ?? '#';

$featured_args = array(
    'posts_per_page' => $featured_posts_count,
    'post_status' => 'publish',
    'meta_key' => '_thumbnail_id', // Only posts with featured images
);

$featured_query = new WP_Query($featured_args);

if (!$featured_query->have_posts()) {
    return;
}

// Define masonry layout pattern for first 5 items
$masonry_classes = array(
    'large wide', // First item: large and wide (2x2 grid)
    '',           // Second item: standard (1x1)
    '',           // Third item: standard (1x1)
    'wide',       // Fourth item: wide (2x1)
    '',           // Fifth item: standard (1x1)
);
?>

<section class="featured-grid-section">
    <div class="section-header-modern">
        <?php if ($featured_heading) : ?>
            <h2><?php echo esc_html($featured_heading); ?></h2>
        <?php endif; ?>
        
        <?php if ($button_text && $button_link) : ?>
            <a href="<?php echo esc_url($button_link); ?>" class="view-all-link">
                <?php echo esc_html($button_text); ?>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
        <?php endif; ?>
    </div>
    
    <div class="masonry-grid">
        <?php 
        $counter = 0;
        while ($featured_query->have_posts()) : 
            $featured_query->the_post();
            $category = get_the_category();
            $item_class = isset($masonry_classes[$counter]) ? $masonry_classes[$counter] : '';
            $counter++;
            ?>
            <article class="masonry-item <?php echo esc_attr($item_class); ?>">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('large'); ?>
                <?php endif; ?>
                <div class="masonry-content">
                    <?php if (!empty($category)) : ?>
                        <span class="category"><?php echo esc_html($category[0]->name); ?></span>
                    <?php endif; ?>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                </div>
            </article>
        <?php 
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
</section>

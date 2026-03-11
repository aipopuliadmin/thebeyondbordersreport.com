<?php
/**
 * The template for displaying the homepage
 *
 * @package Beond_Custom
 */

get_header();

// Get homepage settings
$homepage_settings = get_option('beyond_borders_homepage_settings', array());

// Define all available sections with their template parts and order
$sections = array(
    'hero' => array(
        'enabled' => $homepage_settings['enable_hero'] ?? true,
        'order' => $homepage_settings['hero_order'] ?? 1,
        'template' => 'section-hero',
    ),
    'categories' => array(
        'enabled' => $homepage_settings['enable_categories'] ?? true,
        'order' => $homepage_settings['categories_order'] ?? 2,
        'template' => 'section-categories',
    ),
    'featured_grid' => array(
        'enabled' => $homepage_settings['enable_featured_grid'] ?? true,
        'order' => $homepage_settings['featured_grid_order'] ?? 3,
        'template' => 'section-featured-grid',
    ),
    'trending' => array(
        'enabled' => $homepage_settings['enable_trending'] ?? true,
        'order' => $homepage_settings['trending_order'] ?? 4,
        'template' => 'section-trending',
    ),
    'latest_news' => array(
        'enabled' => $homepage_settings['enable_latest_news'] ?? true,
        'order' => $homepage_settings['latest_news_order'] ?? 5,
        'template' => 'section-latest-news',
    ),
    'opinion' => array(
        'enabled' => $homepage_settings['enable_opinion'] ?? true,
        'order' => $homepage_settings['opinion_order'] ?? 6,
        'template' => 'section-opinion',
    ),
    'stats' => array(
        'enabled' => $homepage_settings['enable_stats'] ?? true,
        'order' => $homepage_settings['stats_order'] ?? 7,
        'template' => 'section-stats',
    ),
    'authors' => array(
        'enabled' => $homepage_settings['enable_authors'] ?? true,
        'order' => $homepage_settings['authors_order'] ?? 8,
        'template' => 'section-authors',
    ),
);

// Filter enabled sections
$enabled_sections = array_filter($sections, function($section) {
    return !empty($section['enabled']);
});

// Sort sections by order
uasort($enabled_sections, function($a, $b) {
    return ($a['order'] ?? 999) - ($b['order'] ?? 999);
});
?>

<main id="primary" class="site-main homepage-main">
    
    <?php
    // Loop through and render each section in order
    foreach ($enabled_sections as $section_key => $section_data) {
        if (!empty($section_data['template'])) {
            get_template_part('template-parts/' . $section_data['template']);
        }
    }
    ?>

</main><!-- #primary -->

<?php
get_footer();

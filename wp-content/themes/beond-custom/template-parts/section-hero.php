<?php
/**
 * Template part for hero section on homepage
 *
 * @package Beond_Custom
 */

$homepage_settings = get_option('beyond_borders_homepage_settings', array());

if (empty($homepage_settings['enable_hero'])) {
    return;
}

$hero_posts_count = $homepage_settings['hero_posts_count'] ?? 5;
$hero_category = $homepage_settings['hero_category'] ?? 0;
$hero_heading = $homepage_settings['hero_heading'] ?? '';

$hero_args = array(
    'posts_per_page' => $hero_posts_count,
    'post_status' => 'publish',
    'ignore_sticky_posts' => false,
);

if ($hero_category) {
    $hero_args['cat'] = $hero_category;
}

$hero_query = new WP_Query($hero_args);

if (!$hero_query->have_posts()) {
    return;
}
?>

<section class="homepage-hero">
    <div class="hero-container">
        <?php if ($hero_heading) : ?>
            <h2 class="section-label"><?php echo esc_html($hero_heading); ?></h2>
        <?php endif; ?>
        
        <div class="hero-layout">
            <?php 
            $post_counter = 0;
            while ($hero_query->have_posts()) : 
                $hero_query->the_post();
                $post_counter++;
                
                if ($post_counter === 1) :
                    // Main hero story
                    ?>
                    <div class="hero-main-story">
                        <?php get_template_part('template-parts/content', 'hero'); ?>
                    </div>
                    <?php
                else :
                    // Secondary stories
                    if ($post_counter === 2) {
                        echo '<div class="hero-secondary">';
                    }
                    get_template_part('template-parts/content', 'secondary');
                    if ($post_counter === $hero_posts_count) {
                        echo '</div>';
                    }
                endif;
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>

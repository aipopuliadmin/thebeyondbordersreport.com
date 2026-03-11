<?php
/**
 * Template part for trending section
 *
 * @package Beond_Custom
 */

$homepage_settings = get_option('beyond_borders_homepage_settings', array());

if (empty($homepage_settings['enable_trending'])) {
    return;
}

$trending_heading = $homepage_settings['trending_heading'] ?? 'Trending Now';
$most_read_heading = $homepage_settings['most_read_heading'] ?? 'Most Read';
$most_read_subtitle = $homepage_settings['most_read_subtitle'] ?? "This week's top stories";
$trending_posts_count = $homepage_settings['trending_posts'] ?? 4;

// Get single most trending post
$trending_args = array(
    'posts_per_page' => 1,
    'post_status' => 'publish',
    'orderby' => 'comment_count',
    'ignore_sticky_posts' => true,
);

$trending_query = new WP_Query($trending_args);

if (!$trending_query->have_posts()) {
    $trending_args['orderby'] = 'date';
    $trending_query = new WP_Query($trending_args);
}

// Get most read posts (by comment count)
$most_read_args = array(
    'posts_per_page' => $trending_posts_count,
    'post_status' => 'publish',
    'orderby' => 'comment_count',
    'ignore_sticky_posts' => true,
);

$most_read_query = new WP_Query($most_read_args);

if (!$trending_query->have_posts() && !$most_read_query->have_posts()) {
    return;
}
?>

<section class="trending-section">
    <div class="trending-container">
        <div class="trending-grid">
            <!-- Left: Featured Trending Post -->
            <div class="trending-featured">
                <?php if ($trending_heading) : ?>
                    <h2 class="trending-heading"><?php echo esc_html($trending_heading); ?></h2>
                <?php endif; ?>
                
                <?php if ($trending_query->have_posts()) : 
                    $trending_query->the_post();
                    $categories = get_the_category();
                    $category_name = !empty($categories) ? $categories[0]->name : '';
                    $reading_time = ceil(str_word_count(get_the_content()) / 200);
                    $post_date = get_the_date('M j, Y');
                ?>
                    <div class="trending-card">
                        <div class="trending-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large'); ?>
                            <?php else : ?>
                                <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=800&h=600&fit=crop" alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="trending-content">
                            <?php if ($category_name) : ?>
                                <span class="trending-category"><?php echo esc_html(strtoupper($category_name)); ?></span>
                            <?php endif; ?>
                            <h3 class="trending-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="trending-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </div>
                            <div class="trending-meta">
                                <span class="author-name"><?php the_author(); ?></span>
                                <span class="meta-separator">•</span>
                                <span class="reading-time"><?php echo $reading_time; ?> min read</span>
                                <span class="meta-separator">•</span>
                                <span class="post-date"><?php echo $post_date; ?></span>
                            </div>
                        </div>
                    </div>
                <?php 
                    wp_reset_postdata();
                endif; 
                ?>
            </div>
            
            <!-- Right: Most Read List -->
            <div class="most-read-sidebar">
                <h3 class="most-read-heading"><?php echo esc_html($most_read_heading); ?></h3>
                <p class="most-read-subtitle"><?php echo esc_html($most_read_subtitle); ?></p>
                
                <div class="most-read-list">
                    <?php 
                    if ($most_read_query->have_posts()) :
                        $counter = 1;
                        while ($most_read_query->have_posts()) : 
                            $most_read_query->the_post();
                            $reading_time = ceil(str_word_count(get_the_content()) / 200);
                    ?>
                        <article class="most-read-item">
                            <span class="most-read-number">0<?php echo $counter; ?></span>
                            <div class="most-read-content">
                                <h4 class="most-read-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <div class="most-read-meta">
                                    <span class="author-name"><?php the_author(); ?></span>
                                    <span class="meta-separator">•</span>
                                    <span class="reading-time"><?php echo $reading_time; ?> min</span>
                                </div>
                            </div>
                        </article>
                    <?php
                            $counter++;
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

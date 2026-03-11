<?php
/**
 * Template part for latest news grid section
 *
 * @package Beond_Custom
 */

$homepage_settings = get_option('beyond_borders_homepage_settings', array());

if (empty($homepage_settings['enable_latest_news'])) {
    return;
}

$latest_heading = $homepage_settings['latest_news_heading'] ?? 'Latest Insights';
$latest_posts_count = $homepage_settings['latest_news_posts'] ?? 8;
$button_text = $homepage_settings['latest_news_button_text'] ?? '';
$button_link = $homepage_settings['latest_news_button_link'] ?? '';

$latest_args = array(
    'posts_per_page' => $latest_posts_count,
    'post_status' => 'publish',
);

$latest_query = new WP_Query($latest_args);

if (!$latest_query->have_posts()) {
    return;
}
?>

<section class="latest-news-section">
    <div class="latest-news-wrapper">
        <div class="section-header-modern">
            <?php if ($latest_heading) : ?>
                <h2><?php echo esc_html($latest_heading); ?></h2>
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
        
        <div class="news-grid">
            <?php 
            while ($latest_query->have_posts()) : 
                $latest_query->the_post();
                $category = get_the_category();
                $post_date = human_time_diff(get_the_time('U'), current_time('timestamp'));
                ?>
                <article class="news-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="news-card-image">
                            <?php the_post_thumbnail('medium_large'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="news-card-content">
                        <?php if (!empty($category)) : ?>
                            <span class="category"><?php echo esc_html($category[0]->name); ?></span>
                        <?php endif; ?>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p class="news-card-meta"><?php echo esc_html($post_date); ?> ago • <?php echo !empty($category[1]) ? esc_html($category[1]->name) : esc_html($category[0]->name); ?></p>
                    </div>
                </article>
            <?php 
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>

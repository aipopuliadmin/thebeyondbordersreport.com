<?php
/**
 * Template part for featured authors section
 *
 * @package Beond_Custom
 */

$homepage_settings = get_option('beyond_borders_homepage_settings', array());

if (empty($homepage_settings['enable_authors'])) {
    return;
}

$authors_heading = $homepage_settings['authors_heading'] ?? 'Featured Contributors';
$authors_count = $homepage_settings['authors_display_count'] ?? 3;
$button_text = $homepage_settings['authors_button_text'] ?? 'All Authors';
$button_link = $homepage_settings['authors_button_link'] ?? '#';

$authors = get_users(array(
    'capability' => 'edit_posts',
    'has_published_posts' => true,
    'orderby' => 'post_count',
    'order' => 'DESC',
    'number' => $authors_count,
));

if (empty($authors)) {
    return;
}
?>

<section class="featured-authors-section">
    <div class="section-header-modern">
        <?php if ($authors_heading) : ?>
            <h2><?php echo esc_html($authors_heading); ?></h2>
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
    
    <div class="authors-grid">
        <?php foreach ($authors as $author) : 
            $author_id = $author->ID;
            $author_posts_count = count_user_posts($author_id, 'post', true);
            $author_bio = get_user_meta($author_id, 'description', true);
            $author_title = get_user_meta($author_id, 'job_title', true);
        ?>
            <article class="author-card">
                <div class="author-card-avatar">
                    <?php echo get_avatar($author_id, 240); ?>
                </div>
                <h3><?php echo esc_html($author->display_name); ?></h3>
                <?php if ($author_title) : ?>
                    <p class="title"><?php echo esc_html($author_title); ?></p>
                <?php endif; ?>
                <?php if ($author_bio) : ?>
                    <p class="bio"><?php echo wp_trim_words($author_bio, 20); ?></p>
                <?php endif; ?>
                <p class="articles-count"><?php echo $author_posts_count; ?> articles published</p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

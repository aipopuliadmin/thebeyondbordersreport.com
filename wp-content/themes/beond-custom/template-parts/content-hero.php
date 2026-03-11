<?php
/**
 * Template part for displaying hero article card (large featured)
 *
 * @package Beond_Custom
 */

$post_id = get_the_ID();
$category = get_the_category();

// Calculate reading time
$reading_time = get_post_meta($post_id, '_reading_time', true);
if (empty($reading_time)) {
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
}
?>

<?php if (has_post_thumbnail()) : ?>
    <?php the_post_thumbnail('hero', array('loading' => 'eager')); ?>
<?php else : ?>
    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder-hero.jpg" alt="<?php the_title_attribute(); ?>" loading="eager">
<?php endif; ?>

<div class="hero-overlay">
    <?php if (!empty($category)) : ?>
        <span class="category"><?php echo esc_html($category[0]->name); ?></span>
    <?php endif; ?>
    
    <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
    
    <div class="author-meta">
        <span class="author"><?php the_author(); ?></span>
        <span>•</span>
        <span><?php echo esc_html($reading_time); ?> min read</span>
        <span>•</span>
        <span><?php echo get_the_date('M j, Y'); ?></span>
    </div>
</div>

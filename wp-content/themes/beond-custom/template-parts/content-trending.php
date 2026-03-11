<?php
/**
 * Template part for displaying trending post item
 *
 * @package Beond_Custom
 * 
 * Variables available:
 * $trending_number - The trending position number
 */

$trending_number = isset($trending_number) ? $trending_number : '';
$category = get_the_category();
?>

<article <?php post_class('trending-item'); ?>>
    <?php if ($trending_number) : ?>
        <span class="trending-number"><?php echo esc_html($trending_number); ?></span>
    <?php endif; ?>
    
    <div class="trending-content">
        <?php if (!empty($category)) : ?>
            <span class="trending-category">
                <?php echo esc_html($category[0]->name); ?>
            </span>
        <?php endif; ?>
        
        <h4 class="trending-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h4>
        
        <div class="trending-meta">
            <time datetime="<?php echo get_the_date('c'); ?>">
                <?php echo get_the_date(); ?>
            </time>
        </div>
    </div>
</article>

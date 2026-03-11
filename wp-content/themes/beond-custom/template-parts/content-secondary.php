<?php
/**
 * Template part for displaying secondary hero stories
 *
 * @package Beond_Custom
 */

$category = get_the_category();
?>

<article <?php post_class('secondary-story'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="secondary-story-image">
            <?php the_post_thumbnail('thumb'); ?>
        </div>
    <?php endif; ?>
    
    <div class="secondary-story-content">
        <?php if (!empty($category)) : ?>
            <span class="category"><?php echo esc_html($category[0]->name); ?></span>
        <?php endif; ?>
        
        <h3>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <p class="meta">
            <?php 
            $author_name = get_the_author();
            $reading_time = get_post_meta(get_the_ID(), '_reading_time', true);
            if (empty($reading_time)) {
                $content = get_post_field('post_content', get_the_ID());
                $word_count = str_word_count(strip_tags($content));
                $reading_time = ceil($word_count / 200);
            }
            echo esc_html($author_name) . ' • ' . esc_html($reading_time) . ' min';
            ?>
        </p>
    </div>
</article>

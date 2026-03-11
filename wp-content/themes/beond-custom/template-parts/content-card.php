<?php
/**
 * Template part for displaying article cards in grids/lists
 *
 * @package Beond_Custom
 * 
 * Variables available:
 * $card_style - 'featured', 'standard', 'small', 'list' (default: 'standard')
 * $show_excerpt - boolean (default: true)
 * $show_category - boolean (default: true)
 * $show_author - boolean (default: true)
 * $show_date - boolean (default: true)
 */

$card_style = isset($card_style) ? $card_style : 'standard';
$show_excerpt = isset($show_excerpt) ? $show_excerpt : true;
$show_category = isset($show_category) ? $show_category : true;
$show_author = isset($show_author) ? $show_author : true;
$show_date = isset($show_date) ? $show_date : true;

$post_id = get_the_ID();
$category = get_the_category();
$reading_time = beond_get_reading_time($post_id);
?>

<article id="post-<?php echo $post_id; ?>" <?php post_class('article-card card-' . $card_style); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="article-card-image">
            <a href="<?php the_permalink(); ?>">
                <?php 
                $image_size = ($card_style === 'featured') ? 'featured' : 'card';
                the_post_thumbnail($image_size); 
                ?>
            </a>
        </div>
    <?php endif; ?>
    
    <div class="article-card-content">
        <?php if ($show_category && !empty($category)) : ?>
            <span class="article-category">
                <a href="<?php echo get_category_link($category[0]->term_id); ?>">
                    <?php echo esc_html($category[0]->name); ?>
                </a>
            </span>
        <?php endif; ?>
        
        <h3 class="article-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <?php if ($show_excerpt && $card_style !== 'small') : ?>
            <div class="article-excerpt">
                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
            </div>
        <?php endif; ?>
        
        <div class="article-meta">
            <?php if ($show_author) : ?>
                <span class="article-author">
                    <?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                        <?php the_author(); ?>
                    </a>
                </span>
            <?php endif; ?>
            
            <?php if ($show_date) : ?>
                <time class="article-date" datetime="<?php echo get_the_date('c'); ?>">
                    <?php echo get_the_date(); ?>
                </time>
            <?php endif; ?>
            
            <?php if ($reading_time) : ?>
                <span class="article-reading-time"><?php echo $reading_time; ?> min read</span>
            <?php endif; ?>
        </div>
    </div>
</article>

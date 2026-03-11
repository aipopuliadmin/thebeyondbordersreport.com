<?php
/**
 * Template part for displaying posts in archives
 *
 * @package Beond_Custom
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('archive-post'); ?>>
    <div class="archive-post-thumbnail">
        <a href="<?php the_permalink(); ?>">
            <?php 
            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'card', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) );
            } else {
                echo '<img src="https://picsum.photos/600/400?random=' . get_the_ID() . '" alt="' . esc_attr( get_the_title() ) . '">';
            }
            ?>
        </a>
    </div>
    
    <div class="archive-post-content">
        <?php
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '" class="post-category">' . esc_html( $categories[0]->name ) . '</a>';
        }
        ?>
        
        <h3 class="archive-post-title">
            <a href="<?php the_permalink(); ?>" rel="bookmark">
                <?php the_title(); ?>
            </a>
        </h3>
        
        <div class="archive-post-meta">
            <span class="post-date"><?php echo get_the_date(); ?></span>
            <span class="meta-separator">•</span>
            <span class="reading-time"><?php echo beond_reading_time(); ?></span>
        </div>
        
        <div class="archive-post-excerpt">
            <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
        </div>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->

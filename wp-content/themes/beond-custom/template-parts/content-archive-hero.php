<?php
/**
 * Template part for displaying the hero/featured post in archives
 *
 * @package Beond_Custom
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('archive-hero-post'); ?>>
    <div class="hero-post-thumbnail">
        <?php 
        if ( has_post_thumbnail() ) {
            the_post_thumbnail( 'hero', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) );
        } else {
            echo '<img src="https://picsum.photos/1200/600?random=' . get_the_ID() . '" alt="' . esc_attr( get_the_title() ) . '">';
        }
        ?>
        <div class="hero-post-overlay">
            <div class="hero-post-content">
                <?php
                $categories = get_the_category();
                if ( ! empty( $categories ) ) {
                    echo '<span class="hero-category">' . esc_html( $categories[0]->name ) . '</span>';
                }
                ?>
                
                <h2 class="hero-post-title">
                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                        <?php the_title(); ?>
                    </a>
                </h2>
                
                <div class="hero-post-meta">
                    <span class="post-date"><?php echo get_the_date(); ?></span>
                    <span class="meta-separator">•</span>
                    <span class="post-author">by <?php the_author(); ?></span>
                    <span class="meta-separator">•</span>
                    <span class="reading-time"><?php echo beond_reading_time(); ?></span>
                </div>
                
                <div class="hero-post-excerpt">
                    <?php echo wp_trim_words( get_the_excerpt(), 30, '...' ); ?>
                </div>
            </div>
        </div>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->

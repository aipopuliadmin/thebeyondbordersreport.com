<?php
/**
 * Template part for displaying posts
 *
 * @package Beond_Custom
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
    <?php beond_post_thumbnail(); ?>
    
    <header class="entry-header">
        <?php
        if ( is_singular() ) :
            the_title( '<h1 class="entry-title">', '</h1>' );
        else :
            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif;

        if ( 'post' === get_post_type() ) :
            ?>
            <div class="entry-meta">
                <?php
                beond_posted_on();
                beond_posted_by();
                ?>
                <span class="reading-time">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                    </svg>
                    <?php echo beond_reading_time(); ?>
                </span>
            </div><!-- .entry-meta -->
            <?php
        endif;
        ?>
    </header><!-- .entry-header -->

    <div class="entry-content">
        <?php
        if ( is_singular() ) {
            the_content();
            
            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'beond-custom' ),
                'after'  => '</div>',
            ) );
        } else {
            the_excerpt();
        }
        ?>
    </div><!-- .entry-content -->

    <?php if ( ! is_singular() ) : ?>
    <footer class="entry-footer">
        <?php beond_entry_footer(); ?>
    </footer><!-- .entry-footer -->
    <?php endif; ?>
    
</article><!-- #post-<?php the_ID(); ?> -->

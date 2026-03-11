<?php
/**
 * The template for displaying all single posts
 *
 * @package Beond_Custom
 */

get_header();
?>

<div class="single-post-main">
    <div class="container">
        <div class="single-post-layout">
            
            <main id="primary" class="site-main single-content-area">
                <?php
                while ( have_posts() ) :
                    the_post();
                    
                    // Main article content
                    get_template_part( 'template-parts/content', 'single' );
                    
                    // Author bio
                    if ( is_singular( 'post' ) ) {
                        get_template_part( 'template-parts/author-bio' );
                    }
                    
                    // Related posts
                    get_template_part( 'template-parts/related-posts' );
                    
                    // Post navigation
                    the_post_navigation( array(
                        'prev_text' => '<div class="post-nav-label">' . esc_html__( 'Previous Article', 'beond-custom' ) . '</div><div class="post-nav-title">%title</div>',
                        'next_text' => '<div class="post-nav-label">' . esc_html__( 'Next Article', 'beond-custom' ) . '</div><div class="post-nav-title">%title</div>',
                    ) );
                    
                    // Comments
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                    
                endwhile;
                ?>
            </main><!-- #primary -->
            
            <?php get_sidebar(); ?>
            
        </div><!-- .single-post-layout -->
    </div><!-- .container -->
</div><!-- .single-post-main -->

<?php
get_footer();

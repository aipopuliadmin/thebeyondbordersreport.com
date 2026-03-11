<?php
/**
 * The template for displaying all pages
 *
 * @package Beond_Custom
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area">
            
            <?php
            while ( have_posts() ) :
                the_post();
                
                get_template_part( 'template-parts/content', 'page' );
                
                // Comments on pages
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                
            endwhile;
            ?>
            
        </div><!-- .content-area -->
        
        <?php get_sidebar(); ?>
        
    </div><!-- .container -->
</main><!-- #primary -->

<?php
get_footer();

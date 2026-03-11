<?php
/**
 * The main template file
 *
 * @package Beond_Custom
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area">
            
            <?php
            if ( have_posts() ) :
                
                // Page header
                if ( is_home() && ! is_front_page() ) :
                    ?>
                    <header class="page-header">
                        <h1 class="page-title"><?php single_post_title(); ?></h1>
                    </header>
                    <?php
                endif;
                
                // Start the Loop
                while ( have_posts() ) :
                    the_post();
                    
                    get_template_part( 'template-parts/content', get_post_type() );
                    
                endwhile;
                
                // Pagination
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => esc_html__( '&larr; Previous', 'beond-custom' ),
                    'next_text' => esc_html__( 'Next &rarr;', 'beond-custom' ),
                ) );
                
            else :
                
                get_template_part( 'template-parts/content', 'none' );
                
            endif;
            ?>
            
        </div><!-- .content-area -->
        
        <?php get_sidebar(); ?>
        
    </div><!-- .container -->
</main><!-- #primary -->

<?php
get_footer();

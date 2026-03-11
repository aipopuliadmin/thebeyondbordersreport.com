<?php
/**
 * The template for displaying archive pages
 *
 * @package Beond_Custom
 */

get_header();
?>

<main id="primary" class="site-main archive-page">
    <div class="container">
        <div class="content-area">
            
            <?php if ( have_posts() ) : ?>

                <header class="page-header">
                    <?php
                    the_archive_title( '<h1 class="page-title">', '</h1>' );
                    the_archive_description( '<div class="archive-description">', '</div>' );
                    ?>
                </header><!-- .page-header -->

                <div class="archive-posts-wrapper">
                    <?php
                    $post_count = 0;
                    
                    // Start the Loop
                    while ( have_posts() ) :
                        the_post();
                        $post_count++;
                        
                        // First post as hero
                        if ( $post_count === 1 ) {
                            get_template_part( 'template-parts/content', 'archive-hero' );
                        } else {
                            get_template_part( 'template-parts/content', 'archive' );
                        }
                        
                    endwhile;
                    ?>
                </div><!-- .archive-posts-wrapper -->
                
                <?php
                // Pagination
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => esc_html__( '&larr; Previous', 'beond-custom' ),
                    'next_text' => esc_html__( 'Next &rarr;', 'beond-custom' ),
                ) );
                ?>

            <?php else : ?>

                <?php get_template_part( 'template-parts/content', 'none' ); ?>

            <?php endif; ?>
            
        </div><!-- .content-area -->
        
        <?php get_sidebar(); ?>
        
    </div><!-- .container -->
</main><!-- #primary -->

<?php
get_footer();

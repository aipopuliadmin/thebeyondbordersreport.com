<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Beond_Custom
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="error-404 not-found">
            
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e( '404', 'beond-custom' ); ?></h1>
                <p class="page-subtitle"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'beond-custom' ); ?></p>
            </header><!-- .page-header -->

            <div class="page-content">
                <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'beond-custom' ); ?></p>

                <?php get_search_form(); ?>

                <div class="widget widget_categories">
                    <h2 class="widget-title"><?php esc_html_e( 'Most Used Categories', 'beond-custom' ); ?></h2>
                    <ul>
                        <?php
                        wp_list_categories( array(
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'show_count' => 1,
                            'title_li'   => '',
                            'number'     => 10,
                        ) );
                        ?>
                    </ul>
                </div>
                
            </div><!-- .page-content -->
            
        </div><!-- .error-404 -->
    </div><!-- .container -->
</main><!-- #primary -->

<?php
get_footer();

<?php
/**
 * Template Name: Terms and Conditions
 * 
 * @package Beond_Custom
 */

// Enqueue terms and conditions specific styles
function enqueue_terms_conditions_styles() {
    wp_enqueue_style(
        'terms-conditions-styles',
        get_template_directory_uri() . '/assets/css/terms-conditions.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_terms_conditions_styles');

get_header();
?>

<main id="primary" class="site-main terms-conditions-page">
    <?php while ( have_posts() ) : the_post(); ?>
        
        <!-- Terms and Conditions Hero -->
        <section class="about-hero">
            <div class="container">
                <span class="hero-label">Legal</span>
                <h1 class="hero-title"><?php the_title(); ?></h1>
                <p class="hero-subtitle">
                    <?php echo get_the_modified_date('M Y, h:i a'); ?> Last Updated
                </p>
                
            </div>
        </section>

        <!-- Terms and Conditions Content -->
        <section class="mission-section">
            <div class="container">
                <div class="terms-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>

    <?php endwhile; ?>
</main><!-- #primary -->

<?php
get_footer();

<?php
/**
 * Template Name: Cookies Policy
 * 
 * @package Beond_Custom
 */

// Enqueue cookies policy specific styles
function enqueue_cookies_policy_styles() {
    wp_enqueue_style(
        'cookies-policy-styles',
        get_template_directory_uri() . '/assets/css/cookies-policy.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_cookies_policy_styles');

get_header();
?>

<main id="primary" class="site-main cookies-policy-page">
    <?php while ( have_posts() ) : the_post(); ?>
        
        <!-- Cookies Policy Hero -->
        <section class="about-hero">
            <div class="container">
                <span class="hero-label">Legal</span>
                <h1 class="hero-title"><?php the_title(); ?></h1>
                <p class="hero-subtitle">
                    <?php echo get_the_modified_date('M Y, h:i a'); ?> Last Updated
                </p>
                
            </div>
        </section>

        <!-- Cookies Policy Content -->
        <section class="mission-section">
            <div class="container">
                <div class="cookies-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>

    <?php endwhile; ?>
</main><!-- #primary -->

<?php
get_footer();

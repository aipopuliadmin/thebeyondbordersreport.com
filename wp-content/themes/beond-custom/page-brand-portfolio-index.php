<?php
/**
 * Template Name: Brand Portfolio Index
 * 
 * @package Beond_Custom
 */

get_header();

// Load Brand Viz class for database access
require_once WP_PLUGIN_DIR . '/beyond-borders/includes/class-beyond-borders-brand-viz.php';

// Enqueue scripts
wp_enqueue_script('jquery');
wp_enqueue_script('three-js', 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js', array(), '128', true);
wp_enqueue_script(
    'brand-visualization',
    BEOND_THEME_URI . '/assets/js/brand-visualization.js',
    array('jquery', 'three-js'),
    BEOND_VERSION,
    true
);

// Pass AJAX URL to JavaScript
wp_localize_script('brand-visualization', 'brandVizConfig', array(
    'ajaxurl' => admin_url('admin-ajax.php')
));
?>

<main id="primary" class="site-main brand-portfolio-index-page">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>

        <!-- Brand Visualization Section -->
        <section class="brand-visualization-section" aria-labelledby="viz-heading">
            <div class="brand-viz-container">
                <div class="visualization-controls">
                    <h2 id="viz-heading">Brand Portfolio Visualization</h2>
                    
                    <div class="viz-controls-row">
                        <div class="viz-control-group">
                            <label for="root-division" class="viz-control-label">Root Division</label>
                            <select id="root-division" class="viz-select" aria-describedby="viz-tip">
                                <option value="0">All Divisions</option>
                                <?php
                                $divisions = Beyond_Borders_Brand_Viz::get_divisions(0);
                                foreach ($divisions as $div):
                                ?>
                                <option value="<?php echo esc_attr($div->id); ?>"><?php echo esc_html($div->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <button type="button" id="refresh-viz" class="viz-btn viz-btn-primary" aria-label="Update visualization with selected division">
                                Update View
                            </button>
                        </div>
                        
                        <div>
                            <button type="button" id="toggle-stats" class="viz-btn viz-btn-secondary" aria-label="Toggle performance statistics" aria-pressed="false">
                                View Stats
                            </button>
                        </div>
                    </div>
                    
                    <p id="viz-tip" class="viz-tip">
                        <strong>Tip:</strong> Use mouse or touch to pan, scroll to zoom, click/tap nodes to expand. Keyboard: Tab to focus, Enter/Space to activate, +/- to zoom.
                    </p>
                </div>
                
                <div id="visualization-container" role="region" aria-label="3D brand portfolio visualization">
                    <div class="viz-loading" role="status" aria-live="polite">
                        <div class="viz-loading-icon" aria-hidden="true">🔄</div>
                        <p class="viz-loading-text">Loading visualization...</p>
                    </div>
                </div>
            </div>
        </section>

    <?php
    endwhile;
    ?>

</main>

<?php
get_footer();

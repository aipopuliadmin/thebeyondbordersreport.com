<?php
/**
 * Plugin Name: Beyond Borders
 * Plugin URI: https://thebeyondbordersreport.com
 * Description: Editorial platform customization plugin for Beyond Borders Report. Adds custom post types, taxonomies, author management, and theme customization features.
 * Version: 1.0.0
 * Author: Beyond Borders Report
 * Author URI: https://thebeyondbordersreport.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: beyond-borders
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 */
define( 'BEYOND_BORDERS_VERSION', '1.0.0' );
define( 'BEYOND_BORDERS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BEYOND_BORDERS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BEYOND_BORDERS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_beyond_borders() {
    require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-activator.php';
    Beyond_Borders_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_beyond_borders() {
    require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-deactivator.php';
    Beyond_Borders_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_beyond_borders' );
register_deactivation_hook( __FILE__, 'deactivate_beyond_borders' );

/**
 * The core plugin class.
 */
require BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders.php';

/**
 * Category meta fields.
 */
require BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-category-meta.php';

/**
 * Font manager.
 */
require BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-font-manager.php';

/**
 * SMTP email handler.
 */
require BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-smtp.php';

/**
 * Newsletter handler.
 */
require BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-newsletter.php';

/**
 * Contact form handler.
 */
define( 'BEYOND_BORDERS_PLUGIN_FILE', __FILE__ );
require BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-contact.php';

/**
 * User management & role-based settings.
 */
require BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-user-management.php';

/**
 * Authentication system (registration, login, profile).
 */
require BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-auth.php';

/**
 * Brand Visualization shortcode
 */
function beyond_borders_brand_viz_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'root_id' => 0,
        'style' => 'iard', // 'iard' or '3d'
    ), $atts );
    
    require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';
    
    $root_id = intval( $atts['root_id'] );
    $style = sanitize_text_field( $atts['style'] );
    $instance_id = 'brand-viz-' . uniqid();
    
    ob_start();
    ?>
    <div class="brand-visualization-wrapper" id="<?php echo esc_attr( $instance_id ); ?>" style="width: 100%; min-height: 600px;">
        <div class="loading" style="text-align: center; padding: 100px; color: #999;">
            Loading visualization...
        </div>
    </div>

    <script>
    (function() {
        var container = document.getElementById('<?php echo $instance_id; ?>');
        var style = '<?php echo esc_js( $style ); ?>';
        var rootId = <?php echo intval( $root_id ); ?>;
        
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?php echo admin_url( 'admin-ajax.php' ); ?>?action=get_brand_hierarchy&parent_id=' + rootId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    renderVisualization(response.data.data);
                }
            }
        };
        xhr.send();
        
        function renderVisualization(data) {
            var templateFile = style === 'iard' ? 'iard-visualization.html' : '3d-visualization.html';
            var iframe = document.createElement('iframe');
            iframe.src = '<?php echo BEYOND_BORDERS_PLUGIN_URL; ?>templates/' + templateFile;
            iframe.style.width = '100%';
            iframe.style.height = style === 'iard' ? '800px' : '600px';
            iframe.style.border = 'none';
            
            iframe.onload = function() {
                setTimeout(function() {
                    iframe.contentWindow.postMessage({
                        type: 'brandData',
                        data: data
                    }, '*');
                }, 500);
            };
            
            container.innerHTML = '';
            container.appendChild(iframe);
        }
    })();
    </script>

    <style>
    .brand-visualization-wrapper {
        position: relative;
        overflow: hidden;
    }
    .brand-visualization-wrapper iframe {
        display: block;
    }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode( 'brand_visualization', 'beyond_borders_brand_viz_shortcode' );

/**
 * Begins execution of the plugin.
 */
function run_beyond_borders() {
    $plugin = new Beyond_Borders();
    $plugin->run();
    
    // Initialize authentication system
    Beyond_Borders_Auth::init();
}
run_beyond_borders();

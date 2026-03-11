<?php
/**
 * Visualization Preview Page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';

$divisions = Beyond_Borders_Brand_Viz::get_divisions( 0 ); // Get root divisions only
?>

<div class="wrap">
    <h1>Brand Visualization Preview</h1>
    
    <div class="brand-viz-preview-controls" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4;">
        <h2>Display Options</h2>
        <table class="form-table">
            <tr>
                <th><label for="viz-style">Visualization Style</label></th>
                <td>
                    <select id="viz-style">
                        <option value="iard">IARD Style (2D, Logos, All Visible)</option>
                        <option value="3d">3D Interactive (Click to Expand)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="root-division">Root Division</label></th>
                <td>
                    <select id="root-division">
                        <option value="0">All (Show all root divisions)</option>
                        <?php foreach ( $divisions as $div ): ?>
                        <option value="<?php echo esc_attr( $div->id ); ?>"><?php echo esc_html( $div->name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <p>
            <button type="button" class="button button-primary" id="refresh-viz">Refresh Visualization</button>
            <button type="button" class="button" id="copy-shortcode">Copy Shortcode</button>
        </p>
        <p class="description">
            <strong>Shortcode:</strong> <code id="shortcode-display">[brand_visualization style="iard" root_id="0"]</code>
        </p>
    </div>
    
    <div id="visualization-container" style="background: #fff; border: 1px solid #ccd0d4; min-height: 600px;">
        <!-- Visualization will load here -->
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    function updateShortcode() {
        var style = $('#viz-style').val();
        var rootId = $('#root-division').val();
        var shortcode = '[brand_visualization style="' + style + '" root_id="' + rootId + '"]';
        $('#shortcode-display').text(shortcode);
    }
    
    $('#viz-style, #root-division').on('change', updateShortcode);
    
    $('#copy-shortcode').on('click', function() {
        var shortcode = $('#shortcode-display').text();
        navigator.clipboard.writeText(shortcode).then(function() {
            alert('Shortcode copied to clipboard!');
        });
    });
    
    $('#refresh-viz').on('click', function() {
        var style = $('#viz-style').val();
        var rootId = $('#root-division').val();
        
        // Load visualization
        $('#visualization-container').html('<div style="text-align: center; padding: 100px;"><p>Loading visualization...</p></div>');
        
        $.get(ajaxurl, {
            action: 'get_hierarchy_data',
            parent_id: rootId
        }, function(response) {
            if (response.success) {
                if (style === 'iard') {
                    renderIARDStyle(response.data.data);
                } else {
                    render3DStyle(response.data.data);
                }
            }
        });
    });
    
    function renderIARDStyle(data) {
        $('#visualization-container').html('<iframe src="<?php echo BEYOND_BORDERS_PLUGIN_URL; ?>templates/iard-visualization.html" style="width: 100%; height: 800px; border: none;"></iframe>');
        
        // Pass data to iframe when loaded
        setTimeout(function() {
            var iframe = $('#visualization-container iframe')[0];
            iframe.contentWindow.postMessage({
                type: 'brandData',
                data: data
            }, '*');
        }, 500);
    }
    
    function render3DStyle(data) {
        $('#visualization-container').html('<iframe src="<?php echo BEYOND_BORDERS_PLUGIN_URL; ?>templates/3d-visualization.html" style="width: 100%; height: 800px; border: none;"></iframe>');
        
        setTimeout(function() {
            var iframe = $('#visualization-container iframe')[0];
            iframe.contentWindow.postMessage({
                type: 'brandData',
                data: data
            }, '*');
        }, 500);
    }
    
    // Initial load
    $('#refresh-viz').click();
});
</script>

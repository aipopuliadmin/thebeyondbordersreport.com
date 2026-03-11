<?php
/**
 * Brands Management Page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load Brand Viz class
require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';

// Handle form submission
if ( isset( $_POST['save_brand'] ) && check_admin_referer( 'brand_viz_brand' ) ) {
    $result = Beyond_Borders_Brand_Viz::save_brand( $_POST );
    if ( $result ) {
        echo '<div class="notice notice-success"><p>Brand saved successfully!</p></div>';
    }
}

// Handle deletion
if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) && check_admin_referer( 'delete_brand_' . $_GET['id'] ) ) {
    Beyond_Borders_Brand_Viz::delete_brand( intval( $_GET['id'] ) );
    echo '<div class="notice notice-success"><p>Brand deleted successfully!</p></div>';
}

$divisions = Beyond_Borders_Brand_Viz::get_divisions();
$selected_division = isset( $_GET['division_id'] ) ? intval( $_GET['division_id'] ) : 0;
$brands = array();

if ( $selected_division ) {
    $brands = Beyond_Borders_Brand_Viz::get_brands_by_division( $selected_division );
} else {
    global $wpdb;
    $brands = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}brand_items ORDER BY division_id, display_order ASC, name ASC" );
}

$editing_brand = null;
if ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && isset( $_GET['id'] ) ) {
    global $wpdb;
    $editing_brand = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}brand_items WHERE id = %d",
        intval( $_GET['id'] )
    ) );
}
?>

<div class="wrap brand-viz-admin">
    <h1 class="wp-heading-inline">Brands</h1>
    <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-brand-viz-brands&action=add' ); ?>" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    
    <!-- Filter by Division -->
    <div class="tablenav top">
        <div class="alignleft actions">
            <label for="filter-division" class="screen-reader-text">Filter by division</label>
            <select id="filter-division" onchange="location.href=this.value">
                <option value="<?php echo admin_url( 'admin.php?page=beyond-borders-brand-viz-brands' ); ?>">All Divisions</option>
                <?php foreach ( $divisions as $div ): ?>
                <option value="<?php echo admin_url( 'admin.php?page=beyond-borders-brand-viz-brands&division_id=' . $div->id ); ?>"
                    <?php selected( $selected_division, $div->id ); ?>>
                    <?php echo esc_html( $div->name ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="brand-viz-container">
        <!-- Left Column: List -->
        <div class="brand-viz-list">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 80px;">Logo</th>
                        <th>Brand Name</th>
                        <th>Division</th>
                        <th style="width: 50px;">Color</th>
                        <th>Website</th>
                        <th>Order</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $brands ) ): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            No brands found. <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-brand-viz-brands&action=add' ); ?>">Add your first brand</a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ( $brands as $brand ): 
                        global $wpdb;
                        $division_name = $wpdb->get_var( $wpdb->prepare(
                            "SELECT name FROM {$wpdb->prefix}brand_divisions WHERE id = %d",
                            $brand->division_id
                        ) );
                    ?>
                    <tr>
                        <td>
                            <?php if ( $brand->logo_url ): ?>
                                <img src="<?php echo esc_url( $brand->logo_url ); ?>" alt="<?php echo esc_attr( $brand->name ); ?>" style="max-width: 60px; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                <div style="width: 60px; height: 60px; background-color: <?php echo esc_attr( $brand->color ); ?>; border-radius: 4px; border: 1px solid #ddd;"></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo esc_html( $brand->name ); ?></strong></td>
                        <td><?php echo esc_html( $division_name ); ?></td>
                        <td>
                            <div style="width: 30px; height: 30px; background-color: <?php echo esc_attr( $brand->color ); ?>; border-radius: 4px; border: 1px solid #ddd;"></div>
                        </td>
                        <td>
                            <?php if ( $brand->website ): ?>
                                <a href="<?php echo esc_url( $brand->website ); ?>" target="_blank">Visit</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo intval( $brand->display_order ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-brand-viz-brands&action=edit&id=' . $brand->id ); ?>" class="button button-small">Edit</a>
                            <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=beyond-borders-brand-viz-brands&action=delete&id=' . $brand->id ), 'delete_brand_' . $brand->id ); ?>" 
                               class="button button-small" 
                               onclick="return confirm('Are you sure you want to delete this brand?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Right Column: Add/Edit Form -->
        <?php if ( isset( $_GET['action'] ) && ( $_GET['action'] === 'add' || $_GET['action'] === 'edit' ) ): ?>
        <div class="brand-viz-form">
            <div class="brand-viz-card">
                <h2><?php echo $editing_brand ? 'Edit Brand' : 'Add New Brand'; ?></h2>
                
                <form method="post" action="" id="brand-form">
                    <?php wp_nonce_field( 'brand_viz_brand' ); ?>
                    <input type="hidden" name="id" value="<?php echo $editing_brand ? esc_attr( $editing_brand->id ) : ''; ?>">
                    <input type="hidden" name="logo_url" id="logo_url" value="<?php echo $editing_brand ? esc_attr( $editing_brand->logo_url ) : ''; ?>">
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="name">Brand Name *</label></th>
                            <td>
                                <input type="text" id="name" name="name" class="regular-text" 
                                       value="<?php echo $editing_brand ? esc_attr( $editing_brand->name ) : ''; ?>" required>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="division_id">Division *</label></th>
                            <td>
                                <select id="division_id" name="division_id" class="regular-text" required>
                                    <option value="">Select Division</option>
                                    <?php foreach ( $divisions as $div ): ?>
                                    <option value="<?php echo esc_attr( $div->id ); ?>"
                                        <?php selected( $editing_brand && $editing_brand->division_id == $div->id ); ?>>
                                        <?php echo esc_html( $div->name ); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label>Logo</label></th>
                            <td>
                                <div id="logo-preview" style="margin-bottom: 10px;">
                                    <?php if ( $editing_brand && $editing_brand->logo_url ): ?>
                                        <img src="<?php echo esc_url( $editing_brand->logo_url ); ?>" alt="Logo" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 5px;">
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="button" id="upload-logo-btn">Upload Logo</button>
                                <button type="button" class="button" id="remove-logo-btn" style="<?php echo ( ! $editing_brand || ! $editing_brand->logo_url ) ? 'display:none;' : ''; ?>">Remove Logo</button>
                                <p class="description">Recommended: PNG with transparent background, max 500x500px</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="color">Color</label></th>
                            <td>
                                <input type="text" id="color" name="color" class="color-picker" 
                                       value="<?php echo $editing_brand ? esc_attr( $editing_brand->color ) : '#000000'; ?>">
                                <p class="description">Used as fallback if no logo is set</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="website">Website URL</label></th>
                            <td>
                                <input type="url" id="website" name="website" class="regular-text" 
                                       value="<?php echo $editing_brand ? esc_attr( $editing_brand->website ) : ''; ?>" 
                                       placeholder="https://">
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="display_order">Display Order</label></th>
                            <td>
                                <input type="number" id="display_order" name="display_order" class="small-text" 
                                       value="<?php echo $editing_brand ? esc_attr( $editing_brand->display_order ) : '0'; ?>">
                                <p class="description">Lower numbers appear first</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="description">Description</label></th>
                            <td>
                                <textarea id="description" name="description" class="large-text" rows="4"><?php echo $editing_brand ? esc_textarea( $editing_brand->description ?? '' ) : ''; ?></textarea>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <button type="submit" name="save_brand" class="button button-primary">Save Brand</button>
                        <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-brand-viz-brands' ); ?>" class="button">Cancel</a>
                    </p>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Color picker
    $('.color-picker').wpColorPicker();
    
    // Logo upload
    var mediaUploader;
    $('#upload-logo-btn').on('click', function(e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media({
            title: 'Choose Brand Logo',
            button: {
                text: 'Use this logo'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#logo_url').val(attachment.url);
            $('#logo-preview').html('<img src="' + attachment.url + '" alt="Logo" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 5px;">');
            $('#remove-logo-btn').show();
        });
        
        mediaUploader.open();
    });
    
    // Remove logo
    $('#remove-logo-btn').on('click', function(e) {
        e.preventDefault();
        $('#logo_url').val('');
        $('#logo-preview').html('');
        $(this).hide();
    });
});
</script>

<style>
.brand-viz-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.brand-viz-list {
    flex: 1;
}

.brand-viz-form {
    flex: 0 0 450px;
}

.brand-viz-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px;
}

.brand-viz-card h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}
</style>

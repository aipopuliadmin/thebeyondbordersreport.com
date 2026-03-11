<?php
/**
 * Divisions Management Page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load Brand Viz class
require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';

// Handle form submission for adding/editing divisions
if ( isset( $_POST['save_division'] ) && check_admin_referer( 'brand_viz_division' ) ) {
    $result = Beyond_Borders_Brand_Viz::save_division( $_POST );
    if ( $result ) {
        echo '<div class="notice notice-success"><p>Division saved successfully!</p></div>';
    }
}

// Handle deletion
if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) && check_admin_referer( 'delete_division_' . $_GET['id'] ) ) {
    Beyond_Borders_Brand_Viz::delete_division( intval( $_GET['id'] ) );
    echo '<div class="notice notice-success"><p>Division deleted successfully!</p></div>';
}

$divisions = Beyond_Borders_Brand_Viz::get_divisions();
$editing_division = null;

if ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && isset( $_GET['id'] ) ) {
    $editing_division = array_filter( $divisions, function( $d ) {
        return $d->id == intval( $_GET['id'] );
    } );
    $editing_division = reset( $editing_division );
}
?>

<div class="wrap brand-viz-admin">
    <h1 class="wp-heading-inline">Brand Divisions</h1>
    <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-brand-viz&action=add' ); ?>" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    
    <div class="brand-viz-container">
        <!-- Left Column: List -->
        <div class="brand-viz-list">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;">Color</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Brands</th>
                        <th>Order</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $divisions as $division ): 
                        global $wpdb;
                        $brands_count = $wpdb->get_var( $wpdb->prepare(
                            "SELECT COUNT(*) FROM {$wpdb->prefix}brand_items WHERE division_id = %d",
                            $division->id
                        ) );
                        
                        $parent_name = '-';
                        if ( $division->parent_id > 0 ) {
                            $parent = $wpdb->get_var( $wpdb->prepare(
                                "SELECT name FROM {$wpdb->prefix}brand_divisions WHERE id = %d",
                                $division->parent_id
                            ) );
                            $parent_name = $parent ?: '-';
                        }
                    ?>
                    <tr>
                        <td>
                            <div style="width: 30px; height: 30px; background-color: <?php echo esc_attr( $division->color ); ?>; border-radius: 4px; border: 1px solid #ddd;"></div>
                        </td>
                        <td><strong><?php echo esc_html( $division->name ); ?></strong></td>
                        <td><?php echo esc_html( $parent_name ); ?></td>
                        <td><?php echo intval( $brands_count ); ?></td>
                        <td><?php echo intval( $division->display_order ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-brand-viz&action=edit&id=' . $division->id ); ?>" class="button button-small">Edit</a>
                            <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=beyond-borders-brand-viz&action=delete&id=' . $division->id ), 'delete_division_' . $division->id ); ?>" 
                               class="button button-small" 
                               onclick="return confirm('Are you sure you want to delete this division?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Right Column: Add/Edit Form -->
        <?php if ( isset( $_GET['action'] ) && ( $_GET['action'] === 'add' || $_GET['action'] === 'edit' ) ): ?>
        <div class="brand-viz-form">
            <div class="brand-viz-card">
                <h2><?php echo $editing_division ? 'Edit Division' : 'Add New Division'; ?></h2>
                
                <form method="post" action="">
                    <?php wp_nonce_field( 'brand_viz_division' ); ?>
                    <input type="hidden" name="id" value="<?php echo $editing_division ? esc_attr( $editing_division->id ) : ''; ?>">
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="name">Division Name *</label></th>
                            <td>
                                <input type="text" id="name" name="name" class="regular-text" 
                                       value="<?php echo $editing_division ? esc_attr( $editing_division->name ) : ''; ?>" required>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="color">Color</label></th>
                            <td>
                                <input type="text" id="color" name="color" class="color-picker" 
                                       value="<?php echo $editing_division ? esc_attr( $editing_division->color ) : '#000000'; ?>">
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="parent_id">Parent Division</label></th>
                            <td>
                                <select id="parent_id" name="parent_id" class="regular-text">
                                    <option value="0">None (Root Level)</option>
                                    <?php foreach ( $divisions as $div ): 
                                        if ( $editing_division && $div->id == $editing_division->id ) continue;
                                    ?>
                                    <option value="<?php echo esc_attr( $div->id ); ?>"
                                        <?php selected( $editing_division && $editing_division->parent_id == $div->id ); ?>>
                                        <?php echo esc_html( $div->name ); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="display_order">Display Order</label></th>
                            <td>
                                <input type="number" id="display_order" name="display_order" class="small-text" 
                                       value="<?php echo $editing_division ? esc_attr( $editing_division->display_order ) : '0'; ?>">
                                <p class="description">Lower numbers appear first</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="description">Description</label></th>
                            <td>
                                <textarea id="description" name="description" class="large-text" rows="4"><?php echo $editing_division ? esc_textarea( $editing_division->description ) : ''; ?></textarea>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <button type="submit" name="save_division" class="button button-primary">Save Division</button>
                        <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-brand-viz' ); ?>" class="button">Cancel</a>
                    </p>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

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
    flex: 0 0 400px;
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

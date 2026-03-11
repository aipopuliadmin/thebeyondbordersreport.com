<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Beyond_Borders
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Delete plugin options
 */
delete_option( 'beyond_borders_version' );
delete_option( 'beyond_borders_settings' );
delete_option( 'beyond_borders_theme_colors' );
delete_option( 'beyond_borders_typography' );
delete_option( 'beyond_borders_editorial_settings' );

/**
 * Delete transients
 */
delete_transient( 'beyond_borders_cache' );

/**
 * Remove custom capabilities from roles (if added)
 */
$roles = array( 'administrator', 'editor', 'author', 'contributor' );
foreach ( $roles as $role_name ) {
    $role = get_role( $role_name );
    if ( $role ) {
        // Remove custom capabilities if any were added
        $role->remove_cap( 'edit_featured_article' );
        $role->remove_cap( 'publish_featured_article' );
    }
}

/**
 * Optional: Remove custom post type data
 * Uncomment if you want to delete all custom content on uninstall
 */
/*
global $wpdb;

// Get all custom post types created by this plugin
$custom_post_types = array( 'featured_article', 'editorial_note' );

foreach ( $custom_post_types as $post_type ) {
    $wpdb->query( 
        $wpdb->prepare( 
            "DELETE FROM {$wpdb->posts} WHERE post_type = %s", 
            $post_type 
        ) 
    );
}

// Clean up orphaned meta data
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id NOT IN (SELECT id FROM {$wpdb->posts})" );
$wpdb->query( "DELETE FROM {$wpdb->term_relationships} WHERE object_id NOT IN (SELECT id FROM {$wpdb->posts})" );
*/

/**
 * Clear scheduled hooks
 */
wp_clear_scheduled_hook( 'beyond_borders_daily_cleanup' );

/**
 * Flush rewrite rules
 */
flush_rewrite_rules();

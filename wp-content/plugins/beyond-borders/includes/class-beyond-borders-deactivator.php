<?php
/**
 * Fired during plugin deactivation.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Deactivator {

    /**
     * Deactivation tasks.
     *
     * - Clears scheduled events
     * - Flushes rewrite rules
     * - Clears transients
     */
    public static function deactivate() {
        
        // Clear scheduled hooks
        wp_clear_scheduled_hook( 'beyond_borders_daily_cleanup' );

        // Flush rewrite rules
        flush_rewrite_rules();

        // Clear transients
        delete_transient( 'beyond_borders_cache' );

        // Log deactivation timestamp
        update_option( 'beyond_borders_deactivated', current_time( 'timestamp' ) );

        // Note: We don't remove capabilities or delete data on deactivation
        // That only happens on uninstall via uninstall.php
    }
}

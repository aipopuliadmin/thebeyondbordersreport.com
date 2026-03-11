<?php
/**
 * Newsletter functionality
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Newsletter {

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action( 'wp_ajax_subscribe_newsletter', array( $this, 'handle_subscription' ) );
        add_action( 'wp_ajax_nopriv_subscribe_newsletter', array( $this, 'handle_subscription' ) );
        add_action( 'admin_post_export_newsletter_subscribers', array( $this, 'export_subscribers' ) );
        add_action( 'admin_post_delete_newsletter_subscriber', array( $this, 'delete_subscriber' ) );
    }

    /**
     * Create newsletter table on plugin activation
     */
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'newsletter_subscribers';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(100) NOT NULL,
            name varchar(100) DEFAULT '',
            status varchar(20) DEFAULT 'subscribed',
            subscribed_date datetime DEFAULT CURRENT_TIMESTAMP,
            ip_address varchar(45) DEFAULT '',
            user_agent text DEFAULT '',
            PRIMARY KEY  (id),
            UNIQUE KEY email (email)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Handle newsletter subscription via AJAX
     */
    public function handle_subscription() {
        check_ajax_referer( 'newsletter_subscription', 'nonce' );

        $email = sanitize_email( $_POST['email'] ?? '' );
        $name = sanitize_text_field( $_POST['name'] ?? '' );

        if ( ! is_email( $email ) ) {
            wp_send_json_error( array(
                'message' => __( 'Please enter a valid email address.', 'beyond-borders' )
            ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'newsletter_subscribers';

        // Check if email already exists
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE email = %s",
            $email
        ) );

        if ( $exists ) {
            wp_send_json_error( array(
                'message' => __( 'This email is already subscribed to our newsletter.', 'beyond-borders' )
            ) );
        }

        // Insert new subscriber
        $result = $wpdb->insert(
            $table_name,
            array(
                'email' => $email,
                'name' => $name,
                'status' => 'subscribed',
                'ip_address' => $this->get_user_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ),
            array( '%s', '%s', '%s', '%s', '%s' )
        );

        if ( $result ) {
            // Send welcome email (optional)
            $this->send_welcome_email( $email, $name );

            wp_send_json_success( array(
                'message' => __( 'Thank you for subscribing! Check your email for confirmation.', 'beyond-borders' )
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'An error occurred. Please try again later.', 'beyond-borders' )
            ) );
        }
    }

    /**
     * Send welcome email to new subscriber
     */
    private function send_welcome_email( $email, $name ) {
        $subject = get_bloginfo( 'name' ) . ' - ' . __( 'Welcome to our newsletter!', 'beyond-borders' );
        
        $message = sprintf(
            __( 'Hi %s,

Thank you for subscribing to our newsletter!

You will now receive exclusive insights and updates from %s.

Best regards,
The %s Team', 'beyond-borders' ),
            $name ? $name : 'there',
            get_bloginfo( 'name' ),
            get_bloginfo( 'name' )
        );

        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        
        wp_mail( $email, $subject, nl2br( $message ), $headers );
    }

    /**
     * Get user IP address
     */
    private function get_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '';
        }
    }

    /**
     * Export subscribers to CSV
     */
    public function export_subscribers() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have permission to access this page.', 'beyond-borders' ) );
        }

        check_admin_referer( 'export_newsletter_subscribers' );

        global $wpdb;
        $table_name = $wpdb->prefix . 'newsletter_subscribers';
        $subscribers = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY subscribed_date DESC", ARRAY_A );

        if ( empty( $subscribers ) ) {
            wp_redirect( admin_url( 'admin.php?page=beyond-borders-newsletter&error=no_subscribers' ) );
            exit;
        }

        // Set headers for CSV download
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=newsletter-subscribers-' . date( 'Y-m-d' ) . '.csv' );

        // Create output stream
        $output = fopen( 'php://output', 'w' );

        // Add CSV headers
        fputcsv( $output, array( 'ID', 'Email', 'Name', 'Status', 'Subscribed Date', 'IP Address' ) );

        // Add data rows
        foreach ( $subscribers as $subscriber ) {
            fputcsv( $output, array(
                $subscriber['id'],
                $subscriber['email'],
                $subscriber['name'],
                $subscriber['status'],
                $subscriber['subscribed_date'],
                $subscriber['ip_address']
            ) );
        }

        fclose( $output );
        exit;
    }

    /**
     * Delete subscriber
     */
    public function delete_subscriber() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have permission to access this page.', 'beyond-borders' ) );
        }

        check_admin_referer( 'delete_newsletter_subscriber' );

        $subscriber_id = absint( $_POST['subscriber_id'] ?? 0 );

        if ( ! $subscriber_id ) {
            wp_redirect( admin_url( 'admin.php?page=beyond-borders-newsletter&error=invalid_id' ) );
            exit;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'newsletter_subscribers';
        
        $wpdb->delete( $table_name, array( 'id' => $subscriber_id ), array( '%d' ) );

        wp_redirect( admin_url( 'admin.php?page=beyond-borders-newsletter&deleted=1' ) );
        exit;
    }

    /**
     * Get all subscribers
     */
    public static function get_subscribers( $status = 'all', $limit = 100, $offset = 0 ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'newsletter_subscribers';

        $sql = "SELECT * FROM $table_name";
        
        if ( $status !== 'all' ) {
            $sql .= $wpdb->prepare( " WHERE status = %s", $status );
        }
        
        $sql .= " ORDER BY subscribed_date DESC LIMIT %d OFFSET %d";
        
        return $wpdb->get_results( $wpdb->prepare( $sql, $limit, $offset ) );
    }

    /**
     * Get subscriber count
     */
    public static function get_subscriber_count( $status = 'all' ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'newsletter_subscribers';

        if ( $status === 'all' ) {
            return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        } else {
            return $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE status = %s",
                $status
            ) );
        }
    }
}

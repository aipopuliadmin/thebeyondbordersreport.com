<?php
/**
 * SMTP Email Handler
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_SMTP {

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
        add_action( 'wp_ajax_test_smtp_connection', array( $this, 'test_smtp_connection' ) );
    }

    /**
     * Configure SMTP settings for PHPMailer
     */
    public function configure_smtp( $phpmailer ) {
        $smtp_settings = get_option( 'beyond_borders_smtp_settings', array() );
        
        // Check if SMTP is enabled
        if ( empty( $smtp_settings['smtp_enabled'] ) ) {
            return;
        }
        
        // Set mailer to SMTP
        $phpmailer->isSMTP();
        
        // SMTP Host
        if ( ! empty( $smtp_settings['smtp_host'] ) ) {
            $phpmailer->Host = $smtp_settings['smtp_host'];
        }
        
        // SMTP Port
        if ( ! empty( $smtp_settings['smtp_port'] ) ) {
            $phpmailer->Port = $smtp_settings['smtp_port'];
        }
        
        // SMTP Encryption
        if ( ! empty( $smtp_settings['smtp_encryption'] ) && $smtp_settings['smtp_encryption'] !== 'none' ) {
            $phpmailer->SMTPSecure = $smtp_settings['smtp_encryption'];
        }
        
        // SMTP Authentication
        if ( ! empty( $smtp_settings['smtp_username'] ) && ! empty( $smtp_settings['smtp_password'] ) ) {
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $smtp_settings['smtp_username'];
            $phpmailer->Password = $smtp_settings['smtp_password'];
        }
        
        // From Email
        if ( ! empty( $smtp_settings['smtp_from_email'] ) ) {
            $phpmailer->From = $smtp_settings['smtp_from_email'];
        }
        
        // From Name
        if ( ! empty( $smtp_settings['smtp_from_name'] ) ) {
            $phpmailer->FromName = $smtp_settings['smtp_from_name'];
        }
        
        // Enable debug if WP_DEBUG is on
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            $phpmailer->SMTPDebug = 2;
        }
    }

    /**
     * Test SMTP connection
     */
    public function test_smtp_connection() {
        check_ajax_referer( 'test_smtp', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        
        $smtp_settings = get_option( 'beyond_borders_smtp_settings', array() );
        
        if ( empty( $smtp_settings['smtp_enabled'] ) ) {
            wp_send_json_error( 'SMTP is not enabled' );
        }
        
        $to = get_option( 'admin_email' );
        $subject = 'Beyond Borders SMTP Test Email';
        $message = 'This is a test email sent from Beyond Borders plugin to verify your SMTP configuration is working correctly.';
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        
        $sent = wp_mail( $to, $subject, $message, $headers );
        
        if ( $sent ) {
            wp_send_json_success( 'Test email sent to ' . $to );
        } else {
            global $phpmailer;
            $error_message = 'Unknown error';
            
            if ( isset( $phpmailer->ErrorInfo ) ) {
                $error_message = $phpmailer->ErrorInfo;
            }
            
            wp_send_json_error( $error_message );
        }
    }
}

// Initialize SMTP handler
new Beyond_Borders_SMTP();

<?php
/**
 * Contact Form Handler
 *
 * @package Beyond_Borders
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Beyond_Borders_Contact {
    
    /**
     * Initialize the contact form
     */
    public function __construct() {
        add_action( 'wp_ajax_submit_contact_form', array( $this, 'handle_submission' ) );
        add_action( 'wp_ajax_nopriv_submit_contact_form', array( $this, 'handle_submission' ) );
        add_action( 'wp_ajax_get_contact_submission', array( $this, 'get_submission_details' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
    }
    
    /**
     * Get submission details for AJAX
     */
    public function get_submission_details() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_submissions';
        
        $id = intval( $_POST['id'] ?? 0 );
        
        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid submission ID' ) );
            return;
        }
        
        $submission = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ) );
        
        if ( ! $submission ) {
            wp_send_json_error( array( 'message' => 'Submission not found' ) );
            return;
        }
        
        wp_send_json_success( array(
            'id' => $submission->id,
            'first_name' => $submission->first_name,
            'last_name' => $submission->last_name,
            'email' => $submission->email,
            'phone' => $submission->phone,
            'subject' => $this->get_subject_label( $submission->subject ),
            'message' => $submission->message,
            'newsletter_subscribe' => $submission->newsletter_subscribe,
            'submitted_at' => date( 'F j, Y g:i A', strtotime( $submission->submitted_at ) ),
            'status' => $submission->status,
            'ip_address' => $submission->ip_address
        ) );
    }
    
    /**
     * Create database table for contact submissions
     */
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_submissions';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(50) DEFAULT NULL,
            subject varchar(100) NOT NULL,
            message text NOT NULL,
            newsletter_subscribe tinyint(1) DEFAULT 0,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'new',
            PRIMARY KEY  (id),
            KEY email (email),
            KEY submitted_at (submitted_at)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    /**
     * Handle contact form submission
     */
    public function handle_submission() {
        // Prevent any output before JSON response
        ob_start();
        
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'contact_form_nonce' ) ) {
            ob_end_clean();
            wp_send_json_error( array( 'message' => 'Security verification failed. Please refresh the page and try again.' ) );
            return;
        }
        
        // SPAM PROTECTION 1: Honeypot field check
        if ( ! empty( $_POST['website'] ) ) {
            ob_end_clean();
            // Log spam attempt
            error_log( 'Contact form spam blocked (honeypot): ' . ( $_SERVER['REMOTE_ADDR'] ?? 'unknown IP' ) );
            // Return success to fool bots
            wp_send_json_success( array( 'message' => 'Thank you for your message!' ) );
            return;
        }
        
        // SPAM PROTECTION 2: Time-based check (form must take at least 3 seconds)
        $timestamp = intval( $_POST['form_timestamp'] ?? 0 );
        $current_time = time() * 1000; // Convert to milliseconds
        $time_elapsed = ( $current_time - $timestamp ) / 1000; // Convert to seconds
        
        if ( $time_elapsed < 3 ) {
            ob_end_clean();
            error_log( 'Contact form spam blocked (too fast): ' . $time_elapsed . 's from IP ' . ( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) );
            wp_send_json_error( array( 'message' => 'Please take your time filling out the form.' ) );
            return;
        }
        
        // SPAM PROTECTION 3: Rate limiting - check for duplicate submissions
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_submissions';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Check if same IP submitted in last 2 minutes
        $recent_submission = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE ip_address = %s AND submitted_at > DATE_SUB(NOW(), INTERVAL 2 MINUTE)",
            $ip_address
        ) );
        
        if ( $recent_submission > 0 ) {
            ob_end_clean();
            error_log( 'Contact form spam blocked (rate limit): IP ' . $ip_address );
            wp_send_json_error( array( 'message' => 'Please wait a few minutes before submitting another message.' ) );
            return;
        }
        
        // Sanitize and validate inputs
        $first_name = sanitize_text_field( $_POST['firstName'] ?? '' );
        $last_name = sanitize_text_field( $_POST['lastName'] ?? '' );
        $email = sanitize_email( $_POST['email'] ?? '' );
        $phone = sanitize_text_field( $_POST['phone'] ?? '' );
        $subject = sanitize_text_field( $_POST['subject'] ?? '' );
        $message = sanitize_textarea_field( $_POST['message'] ?? '' );
        $newsletter = isset( $_POST['newsletter'] ) ? 1 : 0;
        
        // Validation
        $errors = array();
        
        if ( empty( $first_name ) ) {
            $errors[] = 'First name is required.';
        }
        
        if ( empty( $last_name ) ) {
            $errors[] = 'Last name is required.';
        }
        
        if ( empty( $email ) || ! is_email( $email ) ) {
            $errors[] = 'Valid email is required.';
        }
        
        if ( empty( $subject ) ) {
            $errors[] = 'Subject is required.';
        }
        
        if ( empty( $message ) ) {
            $errors[] = 'Message is required.';
        }
        
        if ( ! empty( $errors ) ) {
            ob_end_clean();
            wp_send_json_error( array( 'message' => implode( ' ', $errors ) ) );
            return;
        }
        
        // Store in database
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_submissions';
        
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message,
                'newsletter_subscribe' => $newsletter,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'submitted_at' => current_time( 'mysql' ),
                'status' => 'new'
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
        );
        
        if ( $inserted === false ) {
            ob_end_clean();
            wp_send_json_error( array( 'message' => 'Failed to save your message. Please try again.' ) );
            return;
        }
        
        // Send email notification to admin
        $admin_email = get_option( 'admin_email' );
        $site_name = get_bloginfo( 'name' );
        
        $email_subject = sprintf( '[%s] New Contact Form Submission - %s', $site_name, $subject );
        
        $email_body = "New contact form submission:\n\n";
        $email_body .= "Name: $first_name $last_name\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Phone: $phone\n";
        $email_body .= "Subject: $subject\n\n";
        $email_body .= "Message:\n$message\n\n";
        $email_body .= "Newsletter Subscription: " . ( $newsletter ? 'Yes' : 'No' ) . "\n";
        $email_body .= "Submitted: " . current_time( 'mysql' ) . "\n";
        $email_body .= "IP Address: " . ( $_SERVER['REMOTE_ADDR'] ?? 'Unknown' ) . "\n";
        
        $headers = array(
            'From: ' . $site_name . ' <' . $admin_email . '>',
            'Reply-To: ' . $first_name . ' ' . $last_name . ' <' . $email . '>'
        );
        
        // Send email notification (don't fail form submission if email fails)
        $email_sent = wp_mail( $admin_email, $email_subject, $email_body, $headers );
        
        // Log email failure but don't stop the process
        if ( ! $email_sent ) {
            error_log( 'Contact form: Failed to send admin notification email' );
        }
        
        // Send confirmation email to user
        $user_subject = sprintf( 'Thank you for contacting %s', $site_name );
        $user_body = "Dear $first_name,\n\n";
        $user_body .= "Thank you for reaching out to us. We have received your message and will get back to you within 24 hours.\n\n";
        $user_body .= "Your message:\n$message\n\n";
        $user_body .= "Best regards,\n";
        $user_body .= "$site_name Team\n";
        
        $user_email_sent = wp_mail( $email, $user_subject, $user_body );
        
        if ( ! $user_email_sent ) {
            error_log( 'Contact form: Failed to send user confirmation email to ' . $email );
        }
        
        // If newsletter subscription is checked, add to newsletter
        if ( $newsletter ) {
            $newsletter_table = $wpdb->prefix . 'newsletter_subscribers';
            
            // Check if email already exists
            $exists = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $newsletter_table WHERE email = %s",
                $email
            ) );
            
            if ( ! $exists ) {
                $wpdb->insert(
                    $newsletter_table,
                    array(
                        'email' => $email,
                        'status' => 'active',
                        'subscribed_at' => current_time( 'mysql' )
                    ),
                    array( '%s', '%s', '%s' )
                );
            }
        }
        
        // Clean output buffer and send success response
        ob_end_clean();
        wp_send_json_success( array( 
            'message' => 'Thank you for your message! We will get back to you soon.' 
        ) );
    }
    
    /**
     * Add admin menu for contact submissions
     */
    public function add_admin_menu() {
        add_menu_page(
            'Contact Submissions',
            'Contact Forms',
            'manage_options',
            'contact-submissions',
            array( $this, 'display_submissions_page' ),
            'dashicons-email',
            26
        );
    }
    
    /**
     * Get subject label from value
     */
    private function get_subject_label( $subject ) {
        $subjects = array(
            'general' => 'General Inquiry',
            'press' => 'Press/Media Request',
            'story' => 'Story Submission',
            'partnership' => 'Partnership Opportunity',
            'technical' => 'Technical Support',
            'other' => 'Other'
        );
        
        return isset( $subjects[ $subject ] ) ? $subjects[ $subject ] : ucfirst( $subject );
    }
    
    /**
     * Display submissions page in admin
     */
    public function display_submissions_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_submissions';
        
        // Handle delete action
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) ) {
            check_admin_referer( 'delete_submission_' . intval( $_GET['id'] ) );
            $wpdb->delete( $table_name, array( 'id' => intval( $_GET['id'] ) ), array( '%d' ) );
            echo '<div class="notice notice-success is-dismissible"><p>Submission deleted successfully.</p></div>';
        }
        
        // Handle status updates
        if ( isset( $_POST['update_status'] ) && isset( $_POST['submission_id'] ) ) {
            check_admin_referer( 'update_submission_status' );
            
            $wpdb->update(
                $table_name,
                array( 'status' => sanitize_text_field( $_POST['status'] ) ),
                array( 'id' => intval( $_POST['submission_id'] ) ),
                array( '%s' ),
                array( '%d' )
            );
            
            echo '<div class="notice notice-success is-dismissible"><p>Status updated successfully.</p></div>';
        }
        
        // Pagination
        $per_page = 20;
        $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $offset = ( $current_page - 1 ) * $per_page;
        
        // Filtering
        $status_filter = isset( $_GET['status_filter'] ) ? sanitize_text_field( $_GET['status_filter'] ) : '';
        $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
        
        // Build query
        $where = array();
        if ( $status_filter ) {
            $where[] = $wpdb->prepare( "status = %s", $status_filter );
        }
        if ( $search ) {
            $where[] = $wpdb->prepare( 
                "(first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR subject LIKE %s OR message LIKE %s)",
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%'
            );
        }
        
        $where_clause = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';
        
        // Get total count
        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $where_clause" );
        $total_pages = ceil( $total_items / $per_page );
        
        // Get submissions
        $submissions = $wpdb->get_results( 
            "SELECT * FROM $table_name $where_clause ORDER BY submitted_at DESC LIMIT $per_page OFFSET $offset" 
        );
        
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Contact Form Submissions</h1>
            <hr class="wp-header-end">
            
            <!-- Filters -->
            <div class="tablenav top">
                <div class="alignleft actions">
                    <form method="get" style="display: inline-flex; gap: 10px; align-items: center;">
                        <input type="hidden" name="page" value="contact-submissions">
                        
                        <select name="status_filter" id="status-filter">
                            <option value="">All Statuses</option>
                            <option value="new" <?php selected( $status_filter, 'new' ); ?>>New</option>
                            <option value="read" <?php selected( $status_filter, 'read' ); ?>>Read</option>
                            <option value="replied" <?php selected( $status_filter, 'replied' ); ?>>Replied</option>
                        </select>
                        
                        <input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Search submissions...">
                        
                        <input type="submit" class="button" value="Filter">
                        
                        <?php if ( $status_filter || $search ) : ?>
                            <a href="<?php echo admin_url( 'admin.php?page=contact-submissions' ); ?>" class="button">Clear Filters</a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo number_format_i18n( $total_items ); ?> items</span>
                    <?php if ( $total_pages > 1 ) : ?>
                        <span class="pagination-links">
                            <?php
                            $page_links = paginate_links( array(
                                'base' => add_query_arg( 'paged', '%#%' ),
                                'format' => '',
                                'prev_text' => '&laquo;',
                                'next_text' => '&raquo;',
                                'total' => $total_pages,
                                'current' => $current_page,
                                'type' => 'list',
                            ) );
                            echo $page_links;
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 140px;">Date</th>
                        <th style="width: 180px;">Name</th>
                        <th style="width: 200px;">Email</th>
                        <th style="width: 130px;">Subject</th>
                        <th>Message</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $submissions ) ) : ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px 20px;">
                                <p style="color: #666; font-size: 16px;">No submissions found.</p>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $submissions as $submission ) : ?>
                            <tr>
                                <td><strong>#<?php echo esc_html( $submission->id ); ?></strong></td>
                                <td>
                                    <?php 
                                    $date = new DateTime( $submission->submitted_at );
                                    echo esc_html( $date->format( 'M j, Y' ) );
                                    ?>
                                    <br>
                                    <small style="color: #666;"><?php echo esc_html( $date->format( 'g:i A' ) ); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo esc_html( $submission->first_name . ' ' . $submission->last_name ); ?></strong>
                                    <?php if ( $submission->phone ) : ?>
                                        <br><small style="color: #666;"><?php echo esc_html( $submission->phone ); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo esc_attr( $submission->email ); ?>" style="text-decoration: none;">
                                        <?php echo esc_html( $submission->email ); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="subject-badge subject-<?php echo esc_attr( $submission->subject ); ?>">
                                        <?php echo esc_html( $this->get_subject_label( $submission->subject ) ); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="message-preview" style="max-width: 300px;">
                                        <?php echo esc_html( wp_trim_words( $submission->message, 15 ) ); ?>
                                        <?php if ( str_word_count( $submission->message ) > 15 ) : ?>
                                            <a href="#" class="view-full-message" data-id="<?php echo esc_attr( $submission->id ); ?>">Read more</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo esc_attr( $submission->status ); ?>">
                                        <?php echo esc_html( ucfirst( $submission->status ) ); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="row-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <button type="button" class="button button-small view-details" data-id="<?php echo esc_attr( $submission->id ); ?>">
                                            View
                                        </button>
                                        <button type="button" class="button button-small update-status-btn" data-id="<?php echo esc_attr( $submission->id ); ?>" data-current="<?php echo esc_attr( $submission->status ); ?>">
                                            Status
                                        </button>
                                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=contact-submissions&action=delete&id=' . $submission->id ), 'delete_submission_' . $submission->id ); ?>" 
                                           class="button button-small button-link-delete" 
                                           onclick="return confirm('Are you sure you want to delete this submission?');">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Bottom Pagination -->
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo number_format_i18n( $total_items ); ?> items</span>
                    <?php if ( $total_pages > 1 ) : ?>
                        <span class="pagination-links">
                            <?php echo $page_links; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- View Details Modal -->
        <div id="submission-modal" style="display: none;">
            <div class="submission-modal-content"></div>
        </div>
        
        <!-- Status Update Modal -->
        <div id="status-modal" style="display: none;">
            <div class="status-modal-content"></div>
        </div>
        
        <style>
            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .status-new {
                background: #E3F2FD;
                color: #1976D2;
            }
            
            .status-read {
                background: #F5F5F5;
                color: #757575;
            }
            
            .status-replied {
                background: #E8F5E9;
                color: #388E3C;
            }
            
            .subject-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: 500;
                background: #F0F0F1;
                color: #2C3338;
            }
            
            .subject-press {
                background: #FFF3E0;
                color: #E65100;
            }
            
            .subject-partnership {
                background: #F3E5F5;
                color: #7B1FA2;
            }
            
            .subject-technical {
                background: #FFEBEE;
                color: #C62828;
            }
            
            .message-preview {
                line-height: 1.5;
            }
            
            .view-full-message {
                color: #2271b1;
                text-decoration: none;
                font-weight: 500;
                font-size: 12px;
            }
            
            .view-full-message:hover {
                text-decoration: underline;
            }
            
            .button-small {
                padding: 4px 8px;
                font-size: 12px;
                line-height: 1.5;
                min-height: 0;
            }
            
            #submission-modal, #status-modal {
                position: fixed;
                z-index: 100000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
            }
            
            .submission-modal-content, .status-modal-content {
                background: #fff;
                margin: 5% auto;
                padding: 30px;
                border-radius: 8px;
                max-width: 700px;
                max-height: 80vh;
                overflow-y: auto;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            }
            
            .modal-header {
                border-bottom: 1px solid #ddd;
                padding-bottom: 15px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .modal-header h2 {
                margin: 0;
                font-size: 20px;
            }
            
            .modal-close {
                font-size: 28px;
                font-weight: bold;
                color: #666;
                cursor: pointer;
                background: none;
                border: none;
                padding: 0;
                width: 30px;
                height: 30px;
                line-height: 1;
            }
            
            .modal-close:hover {
                color: #000;
            }
            
            .submission-details-simple {
                padding: 10px 0;
            }
            
            .detail-item-simple {
                display: flex;
                align-items: flex-start;
                gap: 15px;
                padding: 15px 0;
                border-bottom: 1px solid #f0f0f1;
            }
            
            .detail-item-simple:last-child {
                border-bottom: none;
            }
            
            .detail-item-simple .dashicons {
                color: #2271b1;
                font-size: 20px;
                width: 20px;
                height: 20px;
                margin-top: 2px;
            }
            
            .detail-content {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            
            .detail-content label {
                font-size: 11px;
                font-weight: 600;
                color: #666;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin: 0;
            }
            
            .detail-content span {
                font-size: 14px;
                color: #2C3338;
                line-height: 1.5;
            }
            
            .detail-content a {
                color: #2271b1;
                text-decoration: none;
            }
            
            .detail-content a:hover {
                text-decoration: underline;
            }
            
            .message-content {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 4px;
                border-left: 3px solid #2271b1;
                white-space: pre-wrap;
                line-height: 1.6;
                margin-top: 5px;
            }
            
            .submission-details {
                padding: 20px 0;
            }
            
            .detail-section {
                margin-bottom: 30px;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 8px;
                border-left: 4px solid #2271b1;
            }
            
            .section-heading {
                margin: 0 0 20px 0;
                font-size: 16px;
                font-weight: 600;
                color: #2271b1;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .detail-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .detail-grid-small {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            
            .detail-item {
                display: flex;
                gap: 12px;
                align-items: flex-start;
            }
            
            .detail-icon {
                font-size: 24px;
                line-height: 1;
            }
            
            .detail-value-large {
                font-size: 15px;
                color: #2C3338;
                font-weight: 500;
                margin-top: 4px;
            }
            
            .detail-item-inline {
                display: flex;
                gap: 8px;
                padding: 10px;
                background: #fff;
                border-radius: 4px;
            }
            
            .detail-value-inline {
                color: #2C3338;
                font-weight: 500;
            }
            
            .message-box {
                background: #fff;
                padding: 20px;
                border-radius: 6px;
                border: 1px solid #ddd;
                line-height: 1.6;
                color: #2C3338;
                white-space: pre-wrap;
                font-size: 14px;
            }
            
            .detail-row {
                margin-bottom: 20px;
            }
            
            .detail-label {
                font-weight: 600;
                color: #666;
                font-size: 12px;
                text-transform: uppercase;
                margin-bottom: 5px;
            }
            
            .detail-value {
                font-size: 14px;
                color: #2C3338;
            }
            
            .tablenav {
                margin-bottom: 15px;
            }
            
            .tablenav.top {
                margin-top: 15px;
            }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // View details modal
            $('.view-details').on('click', function() {
                var id = $(this).data('id');
                
                $.post(ajaxurl, {
                    action: 'get_contact_submission',
                    id: id
                }, function(response) {
                    if (response.success) {
                        var data = response.data;
                        var html = '<div class="modal-header">';
                        html += '<h2>Submission #' + data.id + '</h2>';
                        html += '<button class="modal-close">&times;</button>';
                        html += '</div>';
                        
                        html += '<div class="submission-details-simple">';
                        
                        html += '<div class="detail-item-simple">';
                        html += '<span class="dashicons dashicons-admin-users"></span>';
                        html += '<div class="detail-content">';
                        html += '<label>Name</label>';
                        html += '<span>' + data.first_name + ' ' + data.last_name + '</span>';
                        html += '</div>';
                        html += '</div>';
                        
                        html += '<div class="detail-item-simple">';
                        html += '<span class="dashicons dashicons-email"></span>';
                        html += '<div class="detail-content">';
                        html += '<label>Email</label>';
                        html += '<span><a href="mailto:' + data.email + '">' + data.email + '</a></span>';
                        html += '</div>';
                        html += '</div>';
                        
                        if (data.phone) {
                            html += '<div class="detail-item-simple">';
                            html += '<span class="dashicons dashicons-phone"></span>';
                            html += '<div class="detail-content">';
                            html += '<label>Phone</label>';
                            html += '<span>' + data.phone + '</span>';
                            html += '</div>';
                            html += '</div>';
                        }
                        
                        html += '<div class="detail-item-simple">';
                        html += '<span class="dashicons dashicons-tag"></span>';
                        html += '<div class="detail-content">';
                        html += '<label>Subject</label>';
                        html += '<span>' + data.subject + '</span>';
                        html += '</div>';
                        html += '</div>';
                        
                        html += '<div class="detail-item-simple">';
                        html += '<span class="dashicons dashicons-text-page"></span>';
                        html += '<div class="detail-content">';
                        html += '<label>Message</label>';
                        html += '<div class="message-content">' + data.message + '</div>';
                        html += '</div>';
                        html += '</div>';
                        
                        html += '<div class="detail-item-simple">';
                        html += '<span class="dashicons dashicons-calendar-alt"></span>';
                        html += '<div class="detail-content">';
                        html += '<label>Submitted</label>';
                        html += '<span>' + data.submitted_at + '</span>';
                        html += '</div>';
                        html += '</div>';
                        
                        html += '<div class="detail-item-simple">';
                        html += '<span class="dashicons dashicons-info"></span>';
                        html += '<div class="detail-content">';
                        html += '<label>Status</label>';
                        html += '<span><span class="status-badge status-' + data.status + '">' + data.status.toUpperCase() + '</span></span>';
                        html += '</div>';
                        html += '</div>';
                        
                        if (data.newsletter_subscribe) {
                            html += '<div class="detail-item-simple">';
                            html += '<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>';
                            html += '<div class="detail-content">';
                            html += '<label>Newsletter</label>';
                            html += '<span style="color: #46b450;">Subscribed</span>';
                            html += '</div>';
                            html += '</div>';
                        }
                        
                        html += '</div>';
                        
                        $('.submission-modal-content').html(html);
                        $('#submission-modal').fadeIn();
                    }
                });
            });
            
            // Update status modal
            $('.update-status-btn').on('click', function() {
                var id = $(this).data('id');
                var current = $(this).data('current');
                
                var html = '<div class="modal-header">';
                html += '<h2>Update Status</h2>';
                html += '<button class="modal-close">&times;</button>';
                html += '</div>';
                
                html += '<form method="post">';
                html += '<?php wp_nonce_field( "update_submission_status" ); ?>';
                html += '<input type="hidden" name="update_status" value="1">';
                html += '<input type="hidden" name="submission_id" value="' + id + '">';
                html += '<div class="detail-row">';
                html += '<label for="status"><strong>Select Status:</strong></label><br>';
                html += '<select name="status" id="status" style="width: 100%; margin-top: 10px; padding: 8px;">';
                html += '<option value="new"' + (current === 'new' ? ' selected' : '') + '>New</option>';
                html += '<option value="read"' + (current === 'read' ? ' selected' : '') + '>Read</option>';
                html += '<option value="replied"' + (current === 'replied' ? ' selected' : '') + '>Replied</option>';
                html += '</select>';
                html += '</div>';
                html += '<div style="margin-top: 20px;">';
                html += '<button type="submit" class="button button-primary">Update Status</button> ';
                html += '<button type="button" class="button modal-close">Cancel</button>';
                html += '</div>';
                html += '</form>';
                
                $('.status-modal-content').html(html);
                $('#status-modal').fadeIn();
            });
            
            // Close modals
            $(document).on('click', '.modal-close', function() {
                $('#submission-modal, #status-modal').fadeOut();
            });
            
            $(document).on('click', '#submission-modal, #status-modal', function(e) {
                if (e.target.id === 'submission-modal' || e.target.id === 'status-modal') {
                    $(this).fadeOut();
                }
            });
            
            // View full message inline
            $('.view-full-message').on('click', function(e) {
                e.preventDefault();
                $(this).closest('.view-details').click();
            });
        });
        </script>
        <?php
    }
}

// Initialize
new Beyond_Borders_Contact();

// Create table on plugin activation
register_activation_hook( BEYOND_BORDERS_PLUGIN_FILE, array( 'Beyond_Borders_Contact', 'create_table' ) );

<?php
/**
 * Beyond Borders Authentication System
 * Handles custom registration, login, and user profile management
 */

class Beyond_Borders_Auth {

    /**
     * Initialize authentication hooks
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'setup_rewrite_rules' ) );
        add_action( 'query_vars', array( __CLASS__, 'add_query_vars' ) );
        add_action( 'template_redirect', array( __CLASS__, 'handle_auth_pages' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_auth_scripts' ) );
    }

    /**
     * Set up URL rewrite rules for /user/register and /user/login
     */
    public static function setup_rewrite_rules() {
        add_rewrite_rule(
            '^user/register/?$',
            'index.php?bb_auth=register',
            'top'
        );
        add_rewrite_rule(
            '^user/login/?$',
            'index.php?bb_auth=login',
            'top'
        );
        add_rewrite_rule(
            '^user/profile/?$',
            'index.php?bb_auth=profile',
            'top'
        );
        add_rewrite_rule(
            '^user/logout/?$',
            'index.php?bb_auth=logout',
            'top'
        );
    }

    /**
     * Add custom query variables
     */
    public static function add_query_vars( $vars ) {
        $vars[] = 'bb_auth';
        return $vars;
    }

    /**
     * Handle auth page requests
     */
    public static function handle_auth_pages() {
        $auth = get_query_var( 'bb_auth' );

        if ( ! $auth ) {
            return;
        }

        // Handle logout
        if ( 'logout' === $auth ) {
            wp_logout();
            wp_safe_redirect( home_url() );
            exit;
        }

        // Handle login
        if ( 'login' === $auth ) {
            self::handle_login_page();
        }

        // Handle registration
        if ( 'register' === $auth ) {
            self::handle_register_page();
        }

        // Handle profile
        if ( 'profile' === $auth ) {
            self::handle_profile_page();
        }
    }

    /**
     * Display login page
     */
    public static function handle_login_page() {
        if ( is_user_logged_in() ) {
            wp_safe_redirect( home_url( '/user/profile' ) );
            exit;
        }

        // Handle login form submission
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['bb_login_nonce'] ) ) {
            self::process_login_form();
        }

        // Load login template
        status_header( 200 );
        include BEYOND_BORDERS_PLUGIN_DIR . 'templates/auth-login.php';
        exit;
    }

    /**
     * Display registration page
     */
    public static function handle_register_page() {
        if ( is_user_logged_in() ) {
            wp_safe_redirect( home_url( '/user/profile' ) );
            exit;
        }

        // Handle registration form submission
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['bb_register_nonce'] ) ) {
            self::process_register_form();
        }

        // Load registration template
        status_header( 200 );
        include BEYOND_BORDERS_PLUGIN_DIR . 'templates/auth-register.php';
        exit;
    }

    /**
     * Display user profile page
     */
    public static function handle_profile_page() {
        if ( ! is_user_logged_in() ) {
            wp_safe_redirect( home_url( '/user/login' ) );
            exit;
        }

        // Handle profile update
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['bb_profile_nonce'] ) ) {
            self::process_profile_update();
        }

        // Load profile template
        status_header( 200 );
        include BEYOND_BORDERS_PLUGIN_DIR . 'templates/auth-profile.php';
        exit;
    }

    /**
     * Process login form submission
     */
    public static function process_login_form() {
        // Verify nonce
        if ( ! isset( $_POST['bb_login_nonce'] ) || ! wp_verify_nonce( $_POST['bb_login_nonce'], 'bb_login_action' ) ) {
            wp_die( 'Security check failed' );
        }

        $email = sanitize_email( $_POST['email'] ?? '' );
        $password = sanitize_text_field( $_POST['password'] ?? '' );

        if ( empty( $email ) || empty( $password ) ) {
            $_SESSION['bb_login_error'] = 'Email and password are required.';
            return;
        }

        // Get user by email
        $user = get_user_by( 'email', $email );

        if ( ! $user ) {
            $_SESSION['bb_login_error'] = 'Invalid email or password.';
            return;
        }

        // Verify password
        if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
            $_SESSION['bb_login_error'] = 'Invalid email or password.';
            return;
        }

        // Log user in
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID, false );

        // Redirect to profile
        wp_safe_redirect( home_url( '/user/profile' ) );
        exit;
    }

    /**
     * Process registration form submission
     */
    public static function process_register_form() {
        // Verify nonce
        if ( ! isset( $_POST['bb_register_nonce'] ) || ! wp_verify_nonce( $_POST['bb_register_nonce'], 'bb_register_action' ) ) {
            wp_die( 'Security check failed' );
        }

        $first_name = sanitize_text_field( $_POST['first_name'] ?? '' );
        $last_name = sanitize_text_field( $_POST['last_name'] ?? '' );
        $email = sanitize_email( $_POST['email'] ?? '' );
        $password = sanitize_text_field( $_POST['password'] ?? '' );
        $confirm_password = sanitize_text_field( $_POST['confirm_password'] ?? '' );
        $job_title = sanitize_text_field( $_POST['job_title'] ?? '' );
        $company = sanitize_text_field( $_POST['company'] ?? '' );
        $bio = sanitize_textarea_field( $_POST['bio'] ?? '' );

        // Validate inputs
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
        if ( empty( $password ) || strlen( $password ) < 6 ) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if ( $password !== $confirm_password ) {
            $errors[] = 'Passwords do not match.';
        }

        // Check if email already exists
        if ( email_exists( $email ) ) {
            $errors[] = 'This email is already registered.';
        }

        if ( ! empty( $errors ) ) {
            $_SESSION['bb_register_errors'] = $errors;
            return;
        }

        // Create user
        $username = sanitize_user( str_replace( '@', '_', $email ) . '_' . rand( 100, 999 ) );
        $user_data = array(
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
        );

        $user_id = wp_insert_user( $user_data );

        if ( is_wp_error( $user_id ) ) {
            $_SESSION['bb_register_errors'] = array( $user_id->get_error_message() );
            return;
        }

        // Store profile metadata
        update_user_meta( $user_id, 'job_title_bb', $job_title );
        update_user_meta( $user_id, 'company_bb', $company );
        update_user_meta( $user_id, 'bio_bb', $bio );
        update_user_meta( $user_id, 'author_type', 'guest_author' );

        // Handle profile image upload
        if ( ! empty( $_FILES['profile_image'] ) ) {
            $image_id = self::handle_image_upload( $_FILES['profile_image'], $user_id );
            if ( $image_id ) {
                update_user_meta( $user_id, 'profile_image_id', $image_id );
            }
        }

        // Log user in
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id, false );
        do_action( 'wp_login', $username, get_userdata( $user_id ) );

        // Redirect to profile
        wp_safe_redirect( home_url( '/user/profile' ) );
        exit;
    }

    /**
     * Process profile update
     */
    public static function process_profile_update() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        // Verify nonce
        if ( ! isset( $_POST['bb_profile_nonce'] ) || ! wp_verify_nonce( $_POST['bb_profile_nonce'], 'bb_profile_update' ) ) {
            wp_die( 'Security check failed' );
        }

        $user_id = get_current_user_id();
        $user_data = array(
            'ID' => $user_id,
            'first_name' => sanitize_text_field( $_POST['first_name'] ?? '' ),
            'last_name' => sanitize_text_field( $_POST['last_name'] ?? '' ),
        );

        wp_update_user( $user_data );

        // Update metadata
        update_user_meta( $user_id, 'job_title_bb', sanitize_text_field( $_POST['job_title'] ?? '' ) );
        update_user_meta( $user_id, 'company_bb', sanitize_text_field( $_POST['company'] ?? '' ) );
        update_user_meta( $user_id, 'bio_bb', sanitize_textarea_field( $_POST['bio'] ?? '' ) );

        // Handle profile image update
        if ( ! empty( $_FILES['profile_image'] ) ) {
            $image_id = self::handle_image_upload( $_FILES['profile_image'], $user_id );
            if ( $image_id ) {
                update_user_meta( $user_id, 'profile_image_id', $image_id );
            }
        }

        $_SESSION['bb_profile_success'] = 'Profile updated successfully.';
    }

    /**
     * Handle image upload
     */
    public static function handle_image_upload( $file, $user_id ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $upload = wp_handle_upload( $file, array( 'test_form' => false ) );

        if ( isset( $upload['error'] ) ) {
            return false;
        }

        $attachment_data = array(
            'post_mime_type' => $upload['type'],
            'post_title' => 'Profile Image - ' . $user_id,
            'post_content' => '',
            'post_status' => 'inherit',
        );

        $attachment_id = wp_insert_attachment( $attachment_data, $upload['file'] );

        if ( is_wp_error( $attachment_id ) ) {
            return false;
        }

        wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );

        return $attachment_id;
    }

    /**
     * Enqueue authentication scripts and styles
     */
    public static function enqueue_auth_scripts() {
        $auth = get_query_var( 'bb_auth' );

        if ( ! in_array( $auth, array( 'login', 'register', 'profile' ), true ) ) {
            return;
        }

        wp_enqueue_style(
            'bb-auth-style',
            BEYOND_BORDERS_PLUGIN_URL . 'css/auth-styles.css',
            array(),
            BEYOND_BORDERS_VERSION
        );

        wp_enqueue_script(
            'bb-auth-script',
            BEYOND_BORDERS_PLUGIN_URL . 'js/auth.js',
            array( 'jquery' ),
            BEYOND_BORDERS_VERSION,
            true
        );
    }

    /**
     * Get user profile image URL
     */
    public static function get_user_profile_image( $user_id, $size = 'thumbnail' ) {
        $image_id = get_user_meta( $user_id, 'profile_image_id', true );

        if ( ! $image_id ) {
            return self::get_default_avatar( $user_id );
        }

        $image = wp_get_attachment_image_src( $image_id, $size );

        return $image ? $image[0] : self::get_default_avatar( $user_id );
    }

    /**
     * Get default avatar
     */
    public static function get_default_avatar( $user_id ) {
        $user = get_userdata( $user_id );
        return get_avatar_url( $user->user_email, array( 'size' => 150 ) );
    }
}

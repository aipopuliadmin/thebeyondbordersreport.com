<?php
/**
 * User Management & Role-Based Settings
 *
 * Handles user role management, capabilities, and author type settings.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_User_Management {

    /**
     * Plugin name
     */
    private $plugin_name;

    /**
     * Plugin version
     */
    private $version;

    /**
     * Initialize the class
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Hook to enforce author approval workflow
        add_filter( 'wp_insert_post_data', array( $this, 'enforce_author_approval' ), 10, 2 );
        add_action( 'transition_post_status', array( $this, 'notify_admin_on_pending_post' ), 10, 3 );
    }

    /**
     * Register settings for user management
     */
    public function register_settings() {
        register_setting( 'beyond_borders_user_management', 'beyond_borders_user_roles' );
        register_setting( 'beyond_borders_user_management', 'beyond_borders_author_types' );
        register_setting( 'beyond_borders_user_management', 'beyond_borders_capability_settings' );
        register_setting( 'beyond_borders_user_management', 'beyond_borders_author_permissions' );
    }

    /**
     * Get all user roles with custom caps
     */
    public static function get_all_roles() {
        global $wp_roles;
        
        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        $roles = array();
        foreach ( $wp_roles->roles as $role_name => $role_info ) {
            $roles[ $role_name ] = array(
                'display_name' => $role_info['name'],
                'capabilities' => $role_info['capabilities'],
            );
        }

        return $roles;
    }

    /**
     * Get custom capabilities for Beyond Borders
     */
    public static function get_custom_capabilities() {
        return array(
            'edit_featured_article' => __( 'Edit Featured Articles', 'beyond-borders' ),
            'read_featured_article' => __( 'Read Featured Articles', 'beyond-borders' ),
            'delete_featured_article' => __( 'Delete Featured Articles', 'beyond-borders' ),
            'edit_featured_articles' => __( 'Edit All Featured Articles', 'beyond-borders' ),
            'edit_others_featured_articles' => __( 'Edit Others\' Featured Articles', 'beyond-borders' ),
            'publish_featured_articles' => __( 'Publish Featured Articles', 'beyond-borders' ),
            'read_private_featured_articles' => __( 'Read Private Featured Articles', 'beyond-borders' ),
            'manage_newsletter' => __( 'Manage Newsletter', 'beyond-borders' ),
            'view_analytics' => __( 'View Analytics', 'beyond-borders' ),
            'manage_author_types' => __( 'Manage Author Types', 'beyond-borders' ),
        );
    }

    /**
     * Get author types
     */
    public static function get_author_types() {
        return array(
            'bb_desk' => array(
                'label' => __( 'BB Desk (Staff Writer)', 'beyond-borders' ),
                'description' => __( 'Internal journalist/editor with full content control', 'beyond-borders' ),
                'badge_class' => 'author-badge-bb-desk',
            ),
            'guest_author' => array(
                'label' => __( 'Guest Author', 'beyond-borders' ),
                'description' => __( 'External contributor with company/job title metadata', 'beyond-borders' ),
                'badge_class' => 'author-badge-guest',
            ),
            'podcast_guest' => array(
                'label' => __( 'Podcast Guest', 'beyond-borders' ),
                'description' => __( 'Podcast interviewee with episode metadata', 'beyond-borders' ),
                'badge_class' => 'author-badge-podcast',
            ),
        );
    }

    /**
     * Add capability to role
     */
    public function add_capability_to_role( $role_name, $capability ) {
        $role = get_role( $role_name );
        if ( $role ) {
            $role->add_cap( $capability );
            return true;
        }
        return false;
    }

    /**
     * Remove capability from role
     */
    public function remove_capability_from_role( $role_name, $capability ) {
        $role = get_role( $role_name );
        if ( $role ) {
            $role->remove_cap( $capability );
            return true;
        }
        return false;
    }

    /**
     * Check if user has capability
     */
    public function user_has_capability( $user_id, $capability ) {
        $user = get_userdata( $user_id );
        if ( $user ) {
            return $user->has_cap( $capability );
        }
        return false;
    }

    /**
     * Get user author type
     */
    public static function get_user_author_type( $user_id ) {
        $author_type = get_user_meta( $user_id, 'author_type', true );
        return ! empty( $author_type ) ? $author_type : 'bb_desk';
    }

    /**
     * Set user author type
     */
    public function set_user_author_type( $user_id, $author_type ) {
        $valid_types = array_keys( self::get_author_types() );
        if ( in_array( $author_type, $valid_types, true ) ) {
            return update_user_meta( $user_id, 'author_type', $author_type );
        }
        return false;
    }

    /**
     * Get users by author type
     */
    public static function get_users_by_author_type( $author_type = null ) {
        $args = array(
            'role__in' => array( 'author', 'editor', 'administrator' ),
            'fields' => 'all',
        );

        $users = get_users( $args );
        
        if ( $author_type ) {
            $filtered = array();
            foreach ( $users as $user ) {
                if ( self::get_user_author_type( $user->ID ) === $author_type ) {
                    $filtered[] = $user;
                }
            }
            return $filtered;
        }

        return $users;
    }

    /**
     * Get capability stats
     */
    public static function get_capability_stats() {
        global $wp_roles;

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        $stats = array();
        $custom_caps = self::get_custom_capabilities();

        foreach ( $wp_roles->roles as $role_name => $role_info ) {
            $cap_count = 0;
            foreach ( array_keys( $custom_caps ) as $cap ) {
                if ( isset( $role_info['capabilities'][ $cap ] ) && $role_info['capabilities'][ $cap ] ) {
                    $cap_count++;
                }
            }
            
            $stats[ $role_name ] = array(
                'display_name' => $role_info['name'],
                'custom_cap_count' => $cap_count,
                'total_caps' => count( $role_info['capabilities'] ),
            );
        }

        return $stats;
    }

    /**
     * Get author type distribution
     */
    public static function get_author_type_distribution() {
        $author_types = array_keys( self::get_author_types() );
        $distribution = array();

        foreach ( $author_types as $type ) {
            $count = count( self::get_users_by_author_type( $type ) );
            $distribution[ $type ] = $count;
        }

        return $distribution;
    }

    /**
     * Export user role data
     */
    public static function export_user_data() {
        $users = get_users( array( 'fields' => 'all' ) );
        $data = array();

        foreach ( $users as $user ) {
            $data[] = array(
                'ID' => $user->ID,
                'user_login' => $user->user_login,
                'user_email' => $user->user_email,
                'user_role' => implode( ', ', $user->roles ),
                'author_type' => self::get_user_author_type( $user->ID ),
                'author_job_title' => get_user_meta( $user->ID, 'author_job_title', true ),
                'author_company' => get_user_meta( $user->ID, 'author_company', true ),
            );
        }

        return $data;
    }

    /**
     * Enforce author approval workflow
     * Prevent authors from publishing posts directly
     *
     * @param array $data Post data
     * @param array $postarr Array of slashed post data
     * @return array Modified post data
     */
    public function enforce_author_approval( $data, $postarr ) {
        // Only apply to posts (not pages or custom post types)
        if ( $data['post_type'] !== 'post' ) {
            return $data;
        }

        // Skip for revisions and auto-drafts
        if ( $data['post_status'] === 'auto-draft' || wp_is_post_revision( $postarr['ID'] ) ) {
            return $data;
        }

        // Get current user
        $current_user = wp_get_current_user();

        // Check if user is an author (and not editor or admin)
        if ( in_array( 'author', $current_user->roles, true ) && 
             ! in_array( 'administrator', $current_user->roles, true ) &&
             ! in_array( 'editor', $current_user->roles, true ) ) {
            
            // If author is trying to publish, change to pending
            if ( $data['post_status'] === 'publish' || $data['post_status'] === 'future' ) {
                $data['post_status'] = 'pending';
            }
        }

        return $data;
    }

    /**
     * Notify admin when author submits post for review
     *
     * @param string $new_status New post status
     * @param string $old_status Old post status
     * @param WP_Post $post Post object
     */
    public function notify_admin_on_pending_post( $new_status, $old_status, $post ) {
        // Only for posts transitioning to pending
        if ( $new_status !== 'pending' || $post->post_type !== 'post' ) {
            return;
        }

        // Skip if already was pending
        if ( $old_status === 'pending' ) {
            return;
        }

        // Get post author
        $author = get_userdata( $post->post_author );
        
        // Only notify for author role submissions
        if ( ! $author || ! in_array( 'author', $author->roles, true ) ) {
            return;
        }

        // Get admin email
        $admin_email = get_option( 'admin_email' );
        
        // Prepare email
        $subject = sprintf(
            /* translators: %s: Blog name */
            __( '[%s] New Post Awaiting Approval', 'beyond-borders' ),
            get_bloginfo( 'name' )
        );

        $message = sprintf(
            /* translators: 1: Author display name, 2: Post title, 3: Post edit URL */
            __( "A new post by %1\$s is awaiting your approval:\n\nPost Title: %2\$s\n\nReview and approve: %3\$s", 'beyond-borders' ),
            $author->display_name,
            $post->post_title,
            admin_url( 'post.php?post=' . $post->ID . '&action=edit' )
        );

        // Send notification
        wp_mail( $admin_email, $subject, $message );
    }}
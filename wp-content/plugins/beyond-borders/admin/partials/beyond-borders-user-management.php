<?php
/**
 * User Management & Role Settings Page
 *
 * @package Beyond_Borders
 */

if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( esc_html__( 'You do not have permission to access this page.', 'beyond-borders' ) );
}

// Get user management helper
require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-user-management.php';
$user_manager = new Beyond_Borders_User_Management( 'beyond-borders', BEYOND_BORDERS_VERSION );

// Handle form submissions
if ( isset( $_POST['beyond_borders_user_management_nonce'] ) ) {
    check_admin_referer( 'beyond_borders_user_management_nonce' );

    // Update role capabilities
    if ( isset( $_POST['update_role_caps'] ) ) {
        foreach ( $_POST['role_caps'] ?? array() as $role => $caps ) {
            // Get role object
            $role_obj = get_role( sanitize_text_field( $role ) );
            if ( $role_obj ) {
                foreach ( Beyond_Borders_User_Management::get_custom_capabilities() as $cap => $label ) {
                    if ( isset( $caps[ $cap ] ) && $caps[ $cap ] ) {
                        $role_obj->add_cap( $cap );
                    } else {
                        $role_obj->remove_cap( $cap );
                    }
                }
            }
        }
        echo '<div class="notice notice-success"><p>' . esc_html__( 'Role capabilities updated successfully.', 'beyond-borders' ) . '</p></div>';
    }

    // Update user author types
    if ( isset( $_POST['update_author_types'] ) && isset( $_POST['user_author_types'] ) ) {
        foreach ( $_POST['user_author_types'] as $user_id => $author_type ) {
            $user_manager->set_user_author_type( 
                intval( $user_id ), 
                sanitize_text_field( $author_type ) 
            );
        }
        echo '<div class="notice notice-success"><p>' . esc_html__( 'Author types updated successfully.', 'beyond-borders' ) . '</p></div>';
    }

    // Refresh author capabilities for approval workflow
    if ( isset( $_POST['refresh_author_caps'] ) ) {
        $author_role = get_role( 'author' );
        if ( $author_role ) {
            // Remove publish capability from authors
            $author_role->remove_cap( 'publish_posts' );
            
            // Ensure authors can assign categories and tags
            $author_role->add_cap( 'assign_categories' );
            $author_role->add_cap( 'assign_post_tags' );
            
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Author capabilities updated. Authors now require admin approval to publish posts.', 'beyond-borders' ) . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'Error: Author role not found.', 'beyond-borders' ) . '</p></div>';
        }
    }
}

// Get data
$all_roles = Beyond_Borders_User_Management::get_all_roles();
$custom_caps = Beyond_Borders_User_Management::get_custom_capabilities();
$author_types = Beyond_Borders_User_Management::get_author_types();
$cap_stats = Beyond_Borders_User_Management::get_capability_stats();
$author_distribution = Beyond_Borders_User_Management::get_author_type_distribution();
$users = get_users( array( 'role__in' => array( 'author', 'editor', 'administrator' ), 'orderby' => 'user_login' ) );
?>

<div class="wrap beyond-borders-admin-page">
    <h1><?php esc_html_e( 'User Management & Roles', 'beyond-borders' ); ?></h1>
    <p class="description"><?php esc_html_e( 'Manage user roles, capabilities, and author types for content creators.', 'beyond-borders' ); ?></p>

    <!-- Tabs Navigation -->
    <nav class="nav-tab-wrapper wp-clearfix">
        <a href="#tabs-roles" class="nav-tab nav-tab-active" data-tab="roles"><?php esc_html_e( 'Role Management', 'beyond-borders' ); ?></a>
        <a href="#tabs-capabilities" class="nav-tab" data-tab="capabilities"><?php esc_html_e( 'Capabilities', 'beyond-borders' ); ?></a>
        <a href="#tabs-authors" class="nav-tab" data-tab="authors"><?php esc_html_e( 'Author Types', 'beyond-borders' ); ?></a>
        <a href="#tabs-users" class="nav-tab" data-tab="users"><?php esc_html_e( 'User Management', 'beyond-borders' ); ?></a>
    </nav>

    <!-- Tab 1: Role Management -->
    <div id="tabs-roles" class="tab-content active">
        <div class="postbox">
            <h2 class="hndle"><span><?php esc_html_e( 'Role Overview', 'beyond-borders' ); ?></span></h2>
            <div class="inside">
                <p><?php esc_html_e( 'View custom capabilities assigned to each role.', 'beyond-borders' ); ?></p>

                <table class="wp-list-table widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Role', 'beyond-borders' ); ?></th>
                            <th><?php esc_html_e( 'Custom Capabilities', 'beyond-borders' ); ?></th>
                            <th><?php esc_html_e( 'Total Capabilities', 'beyond-borders' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $cap_stats as $role => $stat ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $stat['display_name'] ); ?></strong></td>
                                <td>
                                    <span class="badge badge-info"><?php echo intval( $stat['custom_cap_count'] ); ?></span>
                                </td>
                                <td><?php echo intval( $stat['total_caps'] ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Author Approval Workflow -->
        <div class="postbox">
            <h2 class="hndle"><span><?php esc_html_e( 'Author Approval Workflow', 'beyond-borders' ); ?></span></h2>
            <div class="inside">
                <?php
                $author_role = get_role( 'author' );
                $can_publish = $author_role && isset( $author_role->capabilities['publish_posts'] ) && $author_role->capabilities['publish_posts'];
                ?>
                
                <p><?php esc_html_e( 'Control whether authors can publish posts directly or need admin approval.', 'beyond-borders' ); ?></p>
                
                <div style="padding: 15px; background: <?php echo $can_publish ? '#fff3cd' : '#d1e7dd'; ?>; border-left: 4px solid <?php echo $can_publish ? '#ffc107' : '#28a745'; ?>; margin: 15px 0;">
                    <h3 style="margin-top: 0;">
                        <?php if ( $can_publish ) : ?>
                            <span class="dashicons dashicons-warning" style="color: #856404;"></span>
                            <?php esc_html_e( 'Authors Can Publish Directly', 'beyond-borders' ); ?>
                        <?php else : ?>
                            <span class="dashicons dashicons-yes-alt" style="color: #155724;"></span>
                            <?php esc_html_e( 'Approval Workflow Active', 'beyond-borders' ); ?>
                        <?php endif; ?>
                    </h3>
                    <p style="margin: 10px 0 0 0;">
                        <?php if ( $can_publish ) : ?>
                            <?php esc_html_e( 'Authors currently have permission to publish posts without approval. Click the button below to enable the approval workflow.', 'beyond-borders' ); ?>
                        <?php else : ?>
                            <?php esc_html_e( 'Authors must submit posts for review. Admins and editors will be notified when new posts are pending approval.', 'beyond-borders' ); ?>
                        <?php endif; ?>
                    </p>
                </div>

                <form method="post" style="margin-top: 15px;">
                    <?php wp_nonce_field( 'beyond_borders_user_management_nonce' ); ?>
                    <input type="hidden" name="refresh_author_caps" value="1" />
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-update-alt" style="vertical-align: middle;"></span>
                        <?php esc_html_e( 'Enable Author Approval Workflow', 'beyond-borders' ); ?>
                    </button>
                    <p class="description">
                        <?php esc_html_e( 'This will remove the publish_posts capability from the author role. Authors will still be able to create and edit posts, but they will require admin approval before publishing.', 'beyond-borders' ); ?>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab 2: Capability Management -->
    <div id="tabs-capabilities" class="tab-content">
        <form method="post" class="postbox">
            <h2 class="hndle"><span><?php esc_html_e( 'Assign Custom Capabilities to Roles', 'beyond-borders' ); ?></span></h2>
            <div class="inside">
                <p><?php esc_html_e( 'Select which capabilities each role should have access to.', 'beyond-borders' ); ?></p>

                <?php wp_nonce_field( 'beyond_borders_user_management_nonce' ); ?>

                <table class="wp-list-table widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Capability', 'beyond-borders' ); ?></th>
                            <?php foreach ( array( 'administrator', 'editor', 'author', 'contributor' ) as $role ) : ?>
                                <th style="text-align: center;">
                                    <?php echo esc_html( ucfirst( $role ) ); ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $custom_caps as $cap => $label ) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html( $label ); ?></strong>
                                    <br/>
                                    <small style="color: #666;">
                                        <code><?php echo esc_html( $cap ); ?></code>
                                    </small>
                                </td>
                                <?php foreach ( array( 'administrator', 'editor', 'author', 'contributor' ) as $role ) : ?>
                                    <td style="text-align: center;">
                                        <?php
                                        $role_obj = get_role( $role );
                                        $has_cap = $role_obj && isset( $role_obj->capabilities[ $cap ] ) && $role_obj->capabilities[ $cap ];
                                        ?>
                                        <input 
                                            type="checkbox" 
                                            name="role_caps[<?php echo esc_attr( $role ); ?>][<?php echo esc_attr( $cap ); ?>]"
                                            value="1"
                                            <?php checked( $has_cap ); ?>
                                        />
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <hr/>
                <button type="submit" name="update_role_caps" class="button button-primary">
                    <?php esc_html_e( 'Update Capabilities', 'beyond-borders' ); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Tab 3: Author Types -->
    <div id="tabs-authors" class="tab-content">
        <div class="postbox">
            <h2 class="hndle"><span><?php esc_html_e( 'Author Types Distribution', 'beyond-borders' ); ?></span></h2>
            <div class="inside">
                <p><?php esc_html_e( 'Overview of authors assigned to each type.', 'beyond-borders' ); ?></p>

                <table class="wp-list-table widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Author Type', 'beyond-borders' ); ?></th>
                            <th><?php esc_html_e( 'Description', 'beyond-borders' ); ?></th>
                            <th><?php esc_html_e( 'Count', 'beyond-borders' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $author_types as $type_key => $type_info ) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html( $type_info['label'] ); ?></strong>
                                    <br/>
                                    <span class="<?php echo esc_attr( $type_info['badge_class'] ); ?>" style="font-size: 11px;">
                                        <?php echo esc_html( str_replace( '-', ' ', strtoupper( $type_key ) ) ); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html( $type_info['description'] ); ?></td>
                                <td>
                                    <span class="badge badge-secondary">
                                        <?php echo intval( $author_distribution[ $type_key ] ?? 0 ); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 4: User Management -->
    <div id="tabs-users" class="tab-content">
        <form method="post" class="postbox">
            <h2 class="hndle"><span><?php esc_html_e( 'Manage User Author Types', 'beyond-borders' ); ?></span></h2>
            <div class="inside">
                <p><?php esc_html_e( 'Assign author types to users who create content.', 'beyond-borders' ); ?></p>

                <?php wp_nonce_field( 'beyond_borders_user_management_nonce' ); ?>

                <?php if ( ! empty( $users ) ) : ?>
                    <table class="wp-list-table widefat">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'User', 'beyond-borders' ); ?></th>
                                <th><?php esc_html_e( 'Role', 'beyond-borders' ); ?></th>
                                <th><?php esc_html_e( 'Author Type', 'beyond-borders' ); ?></th>
                                <th><?php esc_html_e( 'Job Title', 'beyond-borders' ); ?></th>
                                <th><?php esc_html_e( 'Company', 'beyond-borders' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $users as $user ) : 
                                $author_type = Beyond_Borders_User_Management::get_user_author_type( $user->ID );
                                $job_title = get_user_meta( $user->ID, 'author_job_title', true );
                                $company = get_user_meta( $user->ID, 'author_company', true );
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html( $user->display_name ); ?></strong>
                                        <br/>
                                        <small><?php echo esc_html( $user->user_email ); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge">
                                            <?php echo esc_html( ucfirst( implode( ', ', $user->roles ) ) ); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <select name="user_author_types[<?php echo intval( $user->ID ); ?>]" class="regular-text">
                                            <?php foreach ( $author_types as $type_key => $type_info ) : ?>
                                                <option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $author_type, $type_key ); ?>>
                                                    <?php echo esc_html( $type_info['label'] ); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><?php echo esc_html( $job_title ); ?></td>
                                    <td><?php echo esc_html( $company ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <hr/>
                    <button type="submit" name="update_author_types" class="button button-primary">
                        <?php esc_html_e( 'Update Author Types', 'beyond-borders' ); ?>
                    </button>
                <?php else : ?>
                    <p><?php esc_html_e( 'No users with author or editor roles found.', 'beyond-borders' ); ?></p>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<style>
.beyond-borders-admin-page {
    max-width: 1100px;
}

.nav-tab-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 16px 0 18px;
    border-bottom: 0;
    padding: 0;
}

.nav-tab {
    float: none;
    margin: 0;
    padding: 8px 14px;
    border: 1px solid #dcdcde;
    border-radius: 6px;
    background: #fff;
    color: #1d2327;
    text-decoration: none;
    font-weight: 500;
    line-height: 1.4;
    transition: all 0.15s ease;
}

.nav-tab:hover {
    background: #f0f6fc;
    border-color: #2271b1;
    color: #135e96;
}

.nav-tab:focus {
    outline: none;
    box-shadow: 0 0 0 1px #2271b1;
}

.nav-tab.nav-tab-active {
    background: #2271b1;
    border-color: #2271b1;
    color: #fff;
}

.tab-content {
    display: none;
    opacity: 0;
    min-height: 400px;
}

.tab-content.active {
    display: block !important;
    opacity: 1 !important;
    animation: fadeIn 0.2s ease-in forwards;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.postbox {
    background: #fff;
    border: 1px solid #dcdcde;
    margin-bottom: 16px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.04);
}

.postbox .hndle {
    background: #f6f7f7;
    border-bottom: 1px solid #dcdcde;
    padding: 16px 22px;
    margin: 0;
    cursor: default;
    font-size: 14px;
    font-weight: 600;
}

.postbox .hndle span {
    display: block;
    margin: 0;
    padding: 0;
}

.postbox .inside {
    padding: 22px;
}

.badge {
    display: inline-block;
    padding: 2px 8px;
    background: #646970;
    color: #fff;
    border-radius: 999px;
    font-size: 11px;
    line-height: 1.8;
}

.badge-info {
    background: #2271b1;
}

.badge-secondary {
    background: #646970;
}

table.widefat {
    border-collapse: collapse;
}

table.widefat th,
table.widefat td {
    padding: 12px 12px;
    border-bottom: 1px solid #f0f0f1;
    vertical-align: middle;
}

table.widefat thead {
    background: #f6f7f7;
}

table.widefat thead th {
    font-weight: 600;
    color: #1d2327;
}

table.widefat tbody tr:nth-child(even) {
    background: #fcfcfc;
}

.regular-text {
    min-width: 200px;
}

.description {
    margin-bottom: 18px;
    color: #646970;
}

.postbox .inside > p:first-child {
    margin-top: 0;
}

.postbox .inside > p:last-child {
    margin-bottom: 0;
}

.postbox .inside h3,
.postbox .inside h4 {
    margin: 16px 0 12px;
}

hr {
    margin: 20px 0;
    border: 0;
    border-top: 1px solid #f0f0f1;
}

.button-primary {
    padding: 8px 16px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        var tabId = $(this).data('tab');
        
        // Hide all tabs
        $('.tab-content').removeClass('active');
        
        // Remove active class from all tabs
        $('.nav-tab').removeClass('nav-tab-active');
        
        // Show selected tab
        $('#tabs-' + tabId).addClass('active');
        $(this).addClass('nav-tab-active');
    });
});
</script>

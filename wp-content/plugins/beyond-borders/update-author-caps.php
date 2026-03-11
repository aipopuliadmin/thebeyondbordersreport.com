<?php
/**
 * One-time script to update author capabilities
 * 
 * This script removes the publish_posts capability from the author role,
 * ensuring that authors must submit posts for admin approval.
 * 
 * USAGE:
 * 1. Access this file once via browser: http://yoursite.com/wp-content/plugins/beyond-borders/update-author-caps.php
 * 2. Delete this file after running for security
 * 
 * @package Beyond_Borders
 */

// Load WordPress
require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';

// Security check - only admins can run this
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You do not have permission to access this page.', 'Unauthorized', array( 'response' => 403 ) );
}

// Get the author role
$author_role = get_role( 'author' );

if ( ! $author_role ) {
    wp_die( 'Author role not found.', 'Error', array( 'response' => 500 ) );
}

// Check current status
$can_publish = isset( $author_role->capabilities['publish_posts'] ) && $author_role->capabilities['publish_posts'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Author Capabilities - Beyond Borders</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f0f0f1;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.13);
        }
        h1 {
            color: #1d2327;
            margin-top: 0;
        }
        .status {
            padding: 15px;
            border-left: 4px solid;
            margin: 20px 0;
        }
        .status.warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .status.success {
            background: #d1e7dd;
            border-color: #28a745;
            color: #155724;
        }
        .button {
            background: #2271b1;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover {
            background: #135e96;
        }
        .button.secondary {
            background: #dcdcde;
            color: #2c3338;
        }
        .button.secondary:hover {
            background: #c3c4c7;
        }
        code {
            background: #f0f0f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: Consolas, Monaco, monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Author Capabilities Update</h1>
        
        <?php if ( isset( $_POST['update_caps'] ) && check_admin_referer( 'update_author_caps' ) ) : ?>
            <?php
            // Remove publish capability from authors
            $author_role->remove_cap( 'publish_posts' );
            
            // Ensure authors can assign categories and tags
            $author_role->add_cap( 'assign_categories' );
            $author_role->add_cap( 'assign_post_tags' );
            ?>
            
            <div class="status success">
                <h2>✓ Capabilities Updated Successfully</h2>
                <p><strong>Authors can no longer publish posts directly.</strong></p>
                <p>When authors submit posts, they will now be saved as "Pending Review" and require admin or editor approval before publishing.</p>
            </div>
            
            <h3>What happens next?</h3>
            <ul>
                <li>Authors can still create, edit, and save their own posts</li>
                <li>When they click "Publish", posts will be submitted for review instead</li>
                <li>Admins/editors will receive email notifications for pending posts</li>
                <li>Only admins and editors can approve and publish author submissions</li>
            </ul>
            
            <div style="margin-top: 30px;">
                <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-user-management' ); ?>" class="button">Go to User Management</a>
                <a href="<?php echo admin_url(); ?>" class="button secondary">Go to Dashboard</a>
            </div>
            
            <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 4px;">
                <p style="margin: 0;"><strong>Security Note:</strong> You can now delete this file (<code>update-author-caps.php</code>) as it's no longer needed.</p>
            </div>
            
        <?php else : ?>
            
            <div class="status <?php echo $can_publish ? 'warning' : 'success'; ?>">
                <h2>Current Status</h2>
                <?php if ( $can_publish ) : ?>
                    <p><strong>⚠ Authors can currently publish posts directly without approval.</strong></p>
                <?php else : ?>
                    <p><strong>✓ Author approval workflow is already active.</strong></p>
                    <p>Authors must submit posts for admin approval before publishing.</p>
                <?php endif; ?>
            </div>
            
            <?php if ( $can_publish ) : ?>
                <h3>Enable Author Approval Workflow</h3>
                <p>This will remove the <code>publish_posts</code> capability from the author role. Authors will still be able to:</p>
                <ul>
                    <li>Create new posts</li>
                    <li>Edit their own posts</li>
                    <li>Save drafts</li>
                    <li>Submit posts for review</li>
                </ul>
                <p><strong>They will NOT be able to:</strong></p>
                <ul>
                    <li>Publish posts directly</li>
                    <li>Make posts publicly visible without approval</li>
                </ul>
                
                <form method="post" style="margin-top: 30px;">
                    <?php wp_nonce_field( 'update_author_caps' ); ?>
                    <input type="hidden" name="update_caps" value="1">
                    <button type="submit" class="button">Enable Author Approval Workflow</button>
                    <a href="<?php echo admin_url(); ?>" class="button secondary">Cancel</a>
                </form>
            <?php else : ?>
                <p>The author approval workflow is already configured. No action needed.</p>
                <div style="margin-top: 30px;">
                    <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-user-management' ); ?>" class="button">Go to User Management</a>
                </div>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
</body>
</html>

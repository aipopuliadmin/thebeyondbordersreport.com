<?php
/**
 * Provide an admin area view for the plugin settings.
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Save settings if form submitted
if ( isset( $_POST['beyond_borders_save_settings'] ) && check_admin_referer( 'beyond_borders_settings_save', 'beyond_borders_settings_nonce' ) ) {
    
    $settings = array(
        'enable_customizer'   => isset( $_POST['enable_customizer'] ) ? 1 : 0,
        'enable_custom_posts' => isset( $_POST['enable_custom_posts'] ) ? 1 : 0,
        'enable_analytics'    => isset( $_POST['enable_analytics'] ) ? 1 : 0,
    );
    
    // SMTP Settings
    $smtp_settings = array(
        'smtp_enabled'    => isset( $_POST['smtp_enabled'] ) ? 1 : 0,
        'smtp_host'       => sanitize_text_field( $_POST['smtp_host'] ?? '' ),
        'smtp_port'       => intval( $_POST['smtp_port'] ?? 587 ),
        'smtp_encryption' => sanitize_text_field( $_POST['smtp_encryption'] ?? 'tls' ),
        'smtp_username'   => sanitize_text_field( $_POST['smtp_username'] ?? '' ),
        'smtp_password'   => $_POST['smtp_password'] ?? '', // Don't sanitize password
        'smtp_from_email' => sanitize_email( $_POST['smtp_from_email'] ?? '' ),
        'smtp_from_name'  => sanitize_text_field( $_POST['smtp_from_name'] ?? '' ),
    );
    
    update_option( 'beyond_borders_settings', $settings );
    update_option( 'beyond_borders_smtp_settings', $smtp_settings );
    echo '<div class="notice notice-success"><p>' . __( 'Settings saved successfully!', 'beyond-borders' ) . '</p></div>';
}

$settings = get_option( 'beyond_borders_settings', array() );
$smtp_settings = get_option( 'beyond_borders_smtp_settings', array() );
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <div class="beyond-borders-admin-header">
        <h2><?php _e( 'Beyond Borders Editorial Platform', 'beyond-borders' ); ?></h2>
        <p><?php _e( 'Configure your editorial platform settings, color palette, and publishing workflow.', 'beyond-borders' ); ?></p>
    </div>

    <form method="post" action="">
        <?php wp_nonce_field( 'beyond_borders_settings_save', 'beyond_borders_settings_nonce' ); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'Enable Customizer Integration', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enable_customizer" value="1" <?php checked( $settings['enable_customizer'] ?? true, 1 ); ?> />
                        <?php _e( 'Allow theme color customization via WordPress Customizer', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e( 'Enable Custom Post Types', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enable_custom_posts" value="1" <?php checked( $settings['enable_custom_posts'] ?? true, 1 ); ?> />
                        <?php _e( 'Register Featured Articles and custom taxonomies', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e( 'Enable Analytics', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enable_analytics" value="1" <?php checked( $settings['enable_analytics'] ?? false, 1 ); ?> />
                        <?php _e( 'Track article views and engagement metrics', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="beyond_borders_save_settings" class="button button-primary" value="<?php _e( 'Save Settings', 'beyond-borders' ); ?>" />
        </p>
    </form>

    <hr style="margin: 40px 0;" />

    <!-- SMTP Email Settings -->
    <form method="post" action="">
        <?php wp_nonce_field( 'beyond_borders_settings_save', 'beyond_borders_settings_nonce' ); ?>
        
        <h2><?php _e( 'Email (SMTP) Settings', 'beyond-borders' ); ?></h2>
        <p class="description"><?php _e( 'Configure SMTP settings to send emails from your domain instead of the server default.', 'beyond-borders' ); ?></p>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'Enable SMTP', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="smtp_enabled" value="1" <?php checked( $smtp_settings['smtp_enabled'] ?? false, 1 ); ?> id="smtp_enabled" />
                        <?php _e( 'Send emails using SMTP (recommended for better deliverability)', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>
        </table>
        
        <div id="smtp_settings_container" style="display: <?php echo ( $smtp_settings['smtp_enabled'] ?? false ) ? 'block' : 'none'; ?>;">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="smtp_host"><?php _e( 'SMTP Host', 'beyond-borders' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="smtp_host" 
                               id="smtp_host" 
                               value="<?php echo esc_attr( $smtp_settings['smtp_host'] ?? '' ); ?>" 
                               class="regular-text" 
                               placeholder="smtp.gmail.com">
                        <p class="description"><?php _e( 'Your SMTP server address (e.g., smtp.gmail.com, smtp.office365.com)', 'beyond-borders' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="smtp_port"><?php _e( 'SMTP Port', 'beyond-borders' ); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               name="smtp_port" 
                               id="smtp_port" 
                               value="<?php echo esc_attr( $smtp_settings['smtp_port'] ?? 587 ); ?>" 
                               class="small-text">
                        <p class="description"><?php _e( 'Usually 587 (TLS) or 465 (SSL)', 'beyond-borders' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="smtp_encryption"><?php _e( 'Encryption', 'beyond-borders' ); ?></label>
                    </th>
                    <td>
                        <select name="smtp_encryption" id="smtp_encryption">
                            <option value="none" <?php selected( $smtp_settings['smtp_encryption'] ?? 'tls', 'none' ); ?>><?php _e( 'None', 'beyond-borders' ); ?></option>
                            <option value="ssl" <?php selected( $smtp_settings['smtp_encryption'] ?? 'tls', 'ssl' ); ?>><?php _e( 'SSL', 'beyond-borders' ); ?></option>
                            <option value="tls" <?php selected( $smtp_settings['smtp_encryption'] ?? 'tls', 'tls' ); ?>><?php _e( 'TLS (recommended)', 'beyond-borders' ); ?></option>
                        </select>
                        <p class="description"><?php _e( 'TLS on port 587 is most common', 'beyond-borders' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="smtp_username"><?php _e( 'SMTP Username', 'beyond-borders' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="smtp_username" 
                               id="smtp_username" 
                               value="<?php echo esc_attr( $smtp_settings['smtp_username'] ?? '' ); ?>" 
                               class="regular-text" 
                               autocomplete="off">
                        <p class="description"><?php _e( 'Your email account username (usually your email address)', 'beyond-borders' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="smtp_password"><?php _e( 'SMTP Password', 'beyond-borders' ); ?></label>
                    </th>
                    <td>
                        <input type="password" 
                               name="smtp_password" 
                               id="smtp_password" 
                               value="<?php echo esc_attr( $smtp_settings['smtp_password'] ?? '' ); ?>" 
                               class="regular-text" 
                               autocomplete="new-password">
                        <p class="description"><?php _e( 'Your email account password or app-specific password', 'beyond-borders' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="smtp_from_email"><?php _e( 'From Email', 'beyond-borders' ); ?></label>
                    </th>
                    <td>
                        <input type="email" 
                               name="smtp_from_email" 
                               id="smtp_from_email" 
                               value="<?php echo esc_attr( $smtp_settings['smtp_from_email'] ?? get_option('admin_email') ); ?>" 
                               class="regular-text">
                        <p class="description"><?php _e( 'Email address to send from (must match your SMTP account)', 'beyond-borders' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="smtp_from_name"><?php _e( 'From Name', 'beyond-borders' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="smtp_from_name" 
                               id="smtp_from_name" 
                               value="<?php echo esc_attr( $smtp_settings['smtp_from_name'] ?? get_bloginfo('name') ); ?>" 
                               class="regular-text">
                        <p class="description"><?php _e( 'Name that appears in the "From" field', 'beyond-borders' ); ?></p>
                    </td>
                </tr>
            </table>
            
            <div style="background: #f0f6fc; padding: 15px; border-left: 4px solid #003265; margin: 20px 0;">
                <h4 style="margin-top: 0;"><?php _e( 'Common SMTP Settings:', 'beyond-borders' ); ?></h4>
                <ul style="margin-left: 20px;">
                    <li><strong>Gmail:</strong> smtp.gmail.com | Port 587 | TLS | <em><?php _e( '(Use App Password)', 'beyond-borders' ); ?></em></li>
                    <li><strong>Outlook/Office365:</strong> smtp.office365.com | Port 587 | TLS</li>
                    <li><strong>SendGrid:</strong> smtp.sendgrid.net | Port 587 | TLS</li>
                    <li><strong>Mailgun:</strong> smtp.mailgun.org | Port 587 | TLS</li>
                </ul>
            </div>
        </div>

        <p class="submit">
            <input type="submit" name="beyond_borders_save_settings" class="button button-primary" value="<?php _e( 'Save SMTP Settings', 'beyond-borders' ); ?>" />
            
            <?php if ( $smtp_settings['smtp_enabled'] ?? false ) : ?>
                <button type="button" id="test_smtp_connection" class="button" style="margin-left: 10px;">
                    <?php _e( 'Send Test Email', 'beyond-borders' ); ?>
                </button>
            <?php endif; ?>
        </p>
    </form>
    
    <script>
    jQuery(document).ready(function($) {
        // Toggle SMTP settings visibility
        $('#smtp_enabled').on('change', function() {
            $('#smtp_settings_container').toggle(this.checked);
        });
        
        // Test SMTP connection
        $('#test_smtp_connection').on('click', function() {
            var button = $(this);
            button.prop('disabled', true).text('<?php _e( 'Sending...', 'beyond-borders' ); ?>');
            
            $.post(ajaxurl, {
                action: 'test_smtp_connection',
                nonce: '<?php echo wp_create_nonce( 'test_smtp' ); ?>'
            }, function(response) {
                if (response.success) {
                    alert('<?php _e( 'Test email sent successfully! Check your inbox.', 'beyond-borders' ); ?>');
                } else {
                    alert('<?php _e( 'Failed to send test email: ', 'beyond-borders' ); ?>' + response.data);
                }
                button.prop('disabled', false).text('<?php _e( 'Send Test Email', 'beyond-borders' ); ?>');
            });
        });
    });
    </script>

    <hr />

    <h2><?php _e( 'Plugin Information', 'beyond-borders' ); ?></h2>
    <table class="widefat">
        <tr>
            <td><strong><?php _e( 'Version:', 'beyond-borders' ); ?></strong></td>
            <td><?php echo esc_html( BEYOND_BORDERS_VERSION ); ?></td>
        </tr>
        <tr>
            <td><strong><?php _e( 'Activated:', 'beyond-borders' ); ?></strong></td>
            <td><?php echo date_i18n( get_option( 'date_format' ), get_option( 'beyond_borders_activated', time() ) ); ?></td>
        </tr>
        <tr>
            <td><strong><?php _e( 'Custom Post Types:', 'beyond-borders' ); ?></strong></td>
            <td><?php _e( 'Featured Articles, Regions Taxonomy', 'beyond-borders' ); ?></td>
        </tr>
    </table>
</div>

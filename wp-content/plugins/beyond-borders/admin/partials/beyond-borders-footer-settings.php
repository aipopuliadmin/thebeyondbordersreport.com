<?php
/**
 * Footer settings page.
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Save footer settings if form submitted
if ( isset( $_POST['beyond_borders_save_footer'] ) && check_admin_referer( 'beyond_borders_footer_save', 'beyond_borders_footer_nonce' ) ) {
    
    $footer_settings = array(
        'enable_footer_widgets'    => isset( $_POST['enable_footer_widgets'] ) ? 1 : 0,
        'footer_columns'           => absint( $_POST['footer_columns'] ?? 4 ),
        'show_newsletter_signup'   => isset( $_POST['show_newsletter_signup'] ) ? 1 : 0,
        'newsletter_title'         => sanitize_text_field( $_POST['newsletter_title'] ?? '' ),
        'newsletter_description'   => sanitize_textarea_field( $_POST['newsletter_description'] ?? '' ),
        'show_social_links'        => isset( $_POST['show_social_links'] ) ? 1 : 0,
        'footer_tagline'           => sanitize_text_field( $_POST['footer_tagline'] ?? '' ),
        'copyright_text'           => sanitize_text_field( $_POST['copyright_text'] ?? '' ),
        'show_back_to_top'         => isset( $_POST['show_back_to_top'] ) ? 1 : 0,
        'footer_layout'            => sanitize_text_field( $_POST['footer_layout'] ?? 'default' ),
        'enable_footer_menu'       => isset( $_POST['enable_footer_menu'] ) ? 1 : 0,
    );
    
    update_option( 'beyond_borders_footer_settings', $footer_settings );
    echo '<div class="notice notice-success"><p>' . __( 'Footer settings saved successfully!', 'beyond-borders' ) . '</p></div>';
}

$footer_settings = get_option( 'beyond_borders_footer_settings', array(
    'enable_footer_widgets'  => true,
    'footer_columns'         => 4,
    'show_newsletter_signup' => true,
    'newsletter_title'       => 'Stay Informed',
    'newsletter_description' => 'Subscribe to our newsletter for exclusive insights from global business leaders.',
    'show_social_links'      => true,
    'footer_tagline'         => 'Curated Insights for a Border less World',
    'copyright_text'         => '© 2026 Beyond Borders Report. All rights reserved.',
    'show_back_to_top'       => true,
    'footer_layout'          => 'default',
    'enable_footer_menu'     => true,
) );
?>

<div class="wrap beyond-borders-settings">
    <div class="bb-settings-header">
        <h1><?php _e( 'Footer Settings', 'beyond-borders' ); ?></h1>
        <p class="bb-subtitle"><?php _e( 'Configure your website footer, widgets, and newsletter signup.', 'beyond-borders' ); ?></p>
    </div>

    <form method="post" action="" class="bb-settings-form">
        <?php wp_nonce_field( 'beyond_borders_footer_save', 'beyond_borders_footer_nonce' ); ?>
        
        <div class="bb-settings-grid">
            <!-- Footer Layout Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Footer Layout', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label for="footer_layout" class="bb-label"><?php _e( 'Footer Style', 'beyond-borders' ); ?></label>
                        <select name="footer_layout" id="footer_layout" class="bb-select">
                            <option value="default" <?php selected( $footer_settings['footer_layout'] ?? 'default', 'default' ); ?>><?php _e( 'Default', 'beyond-borders' ); ?></option>
                            <option value="minimal" <?php selected( $footer_settings['footer_layout'] ?? 'default', 'minimal' ); ?>><?php _e( 'Minimal', 'beyond-borders' ); ?></option>
                            <option value="magazine" <?php selected( $footer_settings['footer_layout'] ?? 'default', 'magazine' ); ?>><?php _e( 'Magazine Style', 'beyond-borders' ); ?></option>
                            <option value="dark" <?php selected( $footer_settings['footer_layout'] ?? 'default', 'dark' ); ?>><?php _e( 'Dark Theme', 'beyond-borders' ); ?></option>
                        </select>
                        <p class="bb-help-text"><?php _e( 'Choose the overall footer layout style', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_footer_widgets" value="1" <?php checked( $footer_settings['enable_footer_widgets'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable footer widget areas', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="footer_columns" class="bb-label"><?php _e( 'Widget Columns', 'beyond-borders' ); ?></label>
                        <select name="footer_columns" id="footer_columns" class="bb-select">
                            <option value="1" <?php selected( $footer_settings['footer_columns'] ?? 4, 1 ); ?>>1</option>
                            <option value="2" <?php selected( $footer_settings['footer_columns'] ?? 4, 2 ); ?>>2</option>
                            <option value="3" <?php selected( $footer_settings['footer_columns'] ?? 4, 3 ); ?>>3</option>
                            <option value="4" <?php selected( $footer_settings['footer_columns'] ?? 4, 4 ); ?>>4</option>
                        </select>
                        <p class="bb-help-text"><?php _e( 'Number of widget columns in footer', 'beyond-borders' ); ?></p>
                    </div>
                </div>
            </div>

            <!-- Newsletter Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Newsletter Signup', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="show_newsletter_signup" value="1" <?php checked( $footer_settings['show_newsletter_signup'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Show newsletter signup in footer', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="newsletter_title" class="bb-label"><?php _e( 'Newsletter Title', 'beyond-borders' ); ?></label>
                        <input type="text" name="newsletter_title" id="newsletter_title" value="<?php echo esc_attr( $footer_settings['newsletter_title'] ?? '' ); ?>" class="bb-input" />
                    </div>

                    <div class="bb-field">
                        <label for="newsletter_description" class="bb-label"><?php _e( 'Newsletter Description', 'beyond-borders' ); ?></label>
                        <textarea name="newsletter_description" id="newsletter_description" rows="3" class="bb-textarea"><?php echo esc_textarea( $footer_settings['newsletter_description'] ?? '' ); ?></textarea>
                        <p class="bb-help-text"><?php _e( 'Description text for newsletter signup section', 'beyond-borders' ); ?></p>
                    </div>
                </div>
            </div>

            <!-- Footer Content Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Footer Content', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_footer_menu" value="1" <?php checked( $footer_settings['enable_footer_menu'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Display footer navigation menu', 'beyond-borders' ); ?></span>
                        </label>
                        <p class="bb-help-text"><?php _e( 'Configure menu at Appearance → Menus', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="show_social_links" value="1" <?php checked( $footer_settings['show_social_links'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Display social media icons in footer', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="footer_tagline" class="bb-label"><?php _e( 'Footer Tagline', 'beyond-borders' ); ?></label>
                        <input type="text" name="footer_tagline" id="footer_tagline" value="<?php echo esc_attr( $footer_settings['footer_tagline'] ?? '' ); ?>" class="bb-input" />
                        <p class="bb-help-text"><?php _e( 'Tagline displayed below site title in footer', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label for="copyright_text" class="bb-label"><?php _e( 'Copyright Text', 'beyond-borders' ); ?></label>
                        <input type="text" name="copyright_text" id="copyright_text" value="<?php echo esc_attr( $footer_settings['copyright_text'] ?? '' ); ?>" class="bb-input" />
                        <p class="bb-help-text"><?php _e( 'Copyright notice displayed at bottom of footer', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="show_back_to_top" value="1" <?php checked( $footer_settings['show_back_to_top'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Show "Back to Top" button', 'beyond-borders' ); ?></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bb-settings-footer">
            <button type="submit" name="beyond_borders_save_footer" class="bb-button bb-button-primary">
                <?php _e( 'Save Footer Settings', 'beyond-borders' ); ?>
            </button>
            <p class="bb-footer-note">
                <?php _e( 'Configure footer widgets at', 'beyond-borders' ); ?> 
                <a href="<?php echo admin_url( 'widgets.php' ); ?>"><?php _e( 'Appearance → Widgets', 'beyond-borders' ); ?></a>
            </p>
        </div>
    </form>
</div>

<style>
.beyond-borders-settings {
    max-width: 1200px;
    padding: 20px 0 100px 0;
}

.bb-settings-header {
    margin-bottom: 30px;
}

.bb-settings-header h1 {
    font-size: 28px;
    font-weight: 600;
    color: #1e1e1e;
    margin: 0 0 8px 0;
}

.bb-subtitle {
    font-size: 14px;
    color: #646970;
    margin: 0;
}

.bb-settings-grid {
    display: grid;
    gap: 20px;
    margin-bottom: 30px;
}

.bb-settings-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.bb-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
}

.bb-card-header h2 {
    font-size: 16px;
    font-weight: 600;
    color: #1e1e1e;
    margin: 0;
}

.bb-card-body {
    padding: 24px;
}

.bb-field {
    margin-bottom: 24px;
}

.bb-field:last-child {
    margin-bottom: 0;
}

.bb-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #1e1e1e;
    margin-bottom: 8px;
}

.bb-input,
.bb-select,
.bb-textarea {
    width: 100%;
    padding: 12px 16px;
    font-size: 14px;
    line-height: 1.5;
    color: #1e1e1e;
    background: #f5f5f587;
    border: none;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.bb-input:hover,
.bb-select:hover,
.bb-textarea:hover {
    background: #f5f5f587;
}

.bb-input:focus,
.bb-select:focus,
.bb-textarea:focus {
    outline: none;
    background: #e8e8e8;
    box-shadow: 0 0 0 2px rgba(0, 50, 101, 0.1);
}

.bb-textarea {
    resize: vertical;
    font-family: inherit;
}

.bb-help-text {
    margin: 6px 0 0 0;
    font-size: 13px;
    color: #646970;
}

.bb-toggle {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    padding: 12px 16px;
    width: 30%;
    background: #f5f5f587;
    border: none;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.bb-toggle:hover {
    background: #ebebeb;
}

.bb-toggle input[type="checkbox"] {
    position: relative;
    width: 20px;
    height: 20px;
    margin: 0 12px 0 0;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background: #e0e0e0;
    border: none;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.bb-toggle input[type="checkbox"]:hover {
    background: #d0d0d0;
}

.bb-toggle input[type="checkbox"]:checked {
    background: #003265;
}

.bb-toggle input[type="checkbox"]:checked::after {
    content: '';
    position: absolute;
    left: 6px;
    top: 2px;
    width: 4px;
    height: 9px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.bb-toggle input[type="checkbox"]:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 50, 101, 0.1);
}

.bb-toggle-label {
    font-size: 14px;
    font-weight: 500;
    color: #1e1e1e;
}

.bb-settings-footer {
    position: fixed;
    bottom: 0;
    left: 160px;
    right: 0;
    background: #fff;
    border-top: 1px solid #e0e0e0;
    padding: 16px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 100;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
}

.bb-button {
    padding: 12px 32px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    letter-spacing: 0.3px;
}

.bb-button-primary {
    background: #003265;
    color: #fff;
    box-shadow: 0 2px 4px rgba(0, 50, 101, 0.2);
}

.bb-button-primary:hover {
    background: #0A1628;
    box-shadow: 0 4px 8px rgba(0, 50, 101, 0.3);
    transform: translateY(-1px);
}

.bb-button-primary:active {
    transform: translateY(0);
    box-shadow: 0 1px 2px rgba(0, 50, 101, 0.2);
}

.bb-footer-note {
    margin: 0;
    font-size: 13px;
    color: #646970;
}

.bb-footer-note a {
    color: #003265;
    text-decoration: none;
    font-weight: 500;
}

.bb-footer-note a:hover {
    text-decoration: underline;
}

/* Folded menu style */
body.folded .bb-settings-footer {
    left: 36px;
}

@media (max-width: 960px) {
    .bb-settings-footer {
        left: 0;
    }
}

@media (max-width: 768px) {
    .bb-settings-footer {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
        padding: 16px 20px;
    }
    
    .bb-button {
        width: 100%;
    }
}
</style>

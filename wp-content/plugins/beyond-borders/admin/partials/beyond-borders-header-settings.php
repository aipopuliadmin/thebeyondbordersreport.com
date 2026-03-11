<?php
/**
 * Header settings page.
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Save header settings if form submitted
if ( isset( $_POST['beyond_borders_save_header'] ) && check_admin_referer( 'beyond_borders_header_save', 'beyond_borders_header_nonce' ) ) {
    
    $header_settings = array(
        'enable_top_bar'           => isset( $_POST['enable_top_bar'] ) ? 1 : 0,
        'top_bar_text'             => sanitize_text_field( $_POST['top_bar_text'] ?? '' ),
        'enable_sticky_header'     => isset( $_POST['enable_sticky_header'] ) ? 1 : 0,
        'show_search'              => isset( $_POST['show_search'] ) ? 1 : 0,
        'search_style'             => sanitize_text_field( $_POST['search_style'] ?? 'fullscreen' ),
        'show_social_links'        => isset( $_POST['show_social_links'] ) ? 1 : 0,
        'selected_menu'            => sanitize_text_field( $_POST['selected_menu'] ?? '' ),
        'menu_position'            => sanitize_text_field( $_POST['menu_position'] ?? 'center' ),
        'menu_style'               => sanitize_text_field( $_POST['menu_style'] ?? 'classic' ),
        'enable_mega_menu'         => isset( $_POST['enable_mega_menu'] ) ? 1 : 0,
        'header_layout'            => sanitize_text_field( $_POST['header_layout'] ?? 'default' ),
        'logo_image_id'            => absint( $_POST['logo_image_id'] ?? 0 ),
        'dark_logo_image_id'       => absint( $_POST['dark_logo_image_id'] ?? 0 ),
        'logo_text'                => sanitize_text_field( $_POST['logo_text'] ?? '' ),
        'logo_type'                => sanitize_text_field( $_POST['logo_type'] ?? 'icon' ),
        'show_site_name'           => isset( $_POST['show_site_name'] ) ? 1 : 0,
        'logo_width'               => absint( $_POST['logo_width'] ?? 0 ),
        'show_theme_toggle'        => isset( $_POST['show_theme_toggle'] ) ? 1 : 0,
        'default_theme'            => sanitize_text_field( $_POST['default_theme'] ?? 'light' ),
        'show_subscribe_button'    => isset( $_POST['show_subscribe_button'] ) ? 1 : 0,
        'subscribe_button_text'    => sanitize_text_field( $_POST['subscribe_button_text'] ?? 'Subscribe' ),
        'subscribe_button_url'     => esc_url_raw( $_POST['subscribe_button_url'] ?? '#subscribe' ),
        'social_facebook'          => esc_url_raw( $_POST['social_facebook'] ?? '' ),
        'social_twitter'           => esc_url_raw( $_POST['social_twitter'] ?? '' ),
        'social_instagram'         => esc_url_raw( $_POST['social_instagram'] ?? '' ),
        'social_linkedin'          => esc_url_raw( $_POST['social_linkedin'] ?? '' ),
        'social_youtube'           => esc_url_raw( $_POST['social_youtube'] ?? '' ),
        'social_rss'               => esc_url_raw( $_POST['social_rss'] ?? '' ),
    );
    
    update_option( 'beyond_borders_header_settings', $header_settings );
    echo '<div class="notice notice-success"><p>' . __( 'Header settings saved successfully!', 'beyond-borders' ) . '</p></div>';
}

$header_settings = get_option( 'beyond_borders_header_settings', array(
    'enable_top_bar'        => true,
    'top_bar_text'          => 'Premium Business Publication',
    'enable_sticky_header'  => true,
    'show_search'           => true,
    'search_style'          => 'fullscreen',
    'show_social_links'     => true,
    'selected_menu'         => '',
    'menu_position'         => 'center',
    'menu_style'            => 'classic',
    'enable_mega_menu'      => false,
    'header_layout'         => 'default',
    'logo_image_id'         => 0,
    'dark_logo_image_id'    => 0,
    'logo_text'             => '',
    'logo_type'             => 'icon',
    'show_site_name'        => true,
    'logo_width'            => 200,
    'show_theme_toggle'     => true,
    'default_theme'         => 'light',
    'show_subscribe_button' => true,
    'subscribe_button_text' => 'Subscribe',
    'subscribe_button_url'  => '#subscribe',
    'social_facebook'       => '',
    'social_twitter'        => '',
    'social_instagram'      => '',
    'social_linkedin'       => '',
    'social_youtube'        => '',
    'social_rss'            => '',
) );

// Get logo image URL if ID exists
$logo_image_url = '';
if ( ! empty( $header_settings['logo_image_id'] ) ) {
    $logo_image_url = wp_get_attachment_image_url( $header_settings['logo_image_id'], 'medium' );
}
?>

<div class="wrap beyond-borders-settings">
    <div class="bb-settings-header">
        <h1><?php _e( 'Header Settings', 'beyond-borders' ); ?></h1>
        <p class="bb-subtitle"><?php _e( 'Configure your website header, navigation, and top bar settings.', 'beyond-borders' ); ?></p>
    </div>

    <form method="post" action="" class="bb-settings-form">
        <?php wp_nonce_field( 'beyond_borders_header_save', 'beyond_borders_header_nonce' ); ?>
        
        <div class="bb-settings-grid">
        <div class="bb-settings-grid">
        
        <!-- Logo Settings Card -->
        <div class="bb-settings-card">
            <div class="bb-card-header">
                <h2><?php _e( 'Logo Settings', 'beyond-borders' ); ?></h2>
            </div>
            <div class="bb-card-body">
                <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="logo_type"><?php _e( 'Logo Type', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <select name="logo_type" id="logo_type">
                        <option value="icon" <?php selected( $header_settings['logo_type'] ?? 'icon', 'icon' ); ?>><?php _e( 'Icon with Initials (BB)', 'beyond-borders' ); ?></option>
                        <option value="image" <?php selected( $header_settings['logo_type'] ?? 'icon', 'image' ); ?>><?php _e( 'Custom Image', 'beyond-borders' ); ?></option>
                        <option value="text" <?php selected( $header_settings['logo_type'] ?? 'icon', 'text' ); ?>><?php _e( 'Text Only', 'beyond-borders' ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'Choose how your logo should appear', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr class="logo-image-row">
                <th scope="row">
                    <label for="logo_image"><?php _e( 'Logo Image', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <div class="logo-upload-wrapper">
                        <input type="hidden" name="logo_image_id" id="logo_image_id" value="<?php echo esc_attr( $header_settings['logo_image_id'] ?? 0 ); ?>" />
                        
                        <div class="logo-preview" style="margin-bottom: 10px;">
                            <?php if ( $logo_image_url ) : ?>
                                <img src="<?php echo esc_url( $logo_image_url ); ?>" alt="Logo" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px; background: #f9f9f9;" />
                            <?php else : ?>
                                <div style="width: 200px; height: 100px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999;">
                                    <?php _e( 'No logo uploaded', 'beyond-borders' ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="button" class="button button-secondary upload-logo-button">
                            <?php _e( 'Upload Logo', 'beyond-borders' ); ?>
                        </button>
                        
                        <?php if ( $logo_image_url ) : ?>
                            <button type="button" class="button button-link-delete remove-logo-button" style="margin-left: 10px; color: #a00;">
                                <?php _e( 'Remove Logo', 'beyond-borders' ); ?>
                            </button>
                        <?php endif; ?>
                        
                        <p class="description"><?php _e( 'Upload a custom logo image (recommended: 200-400px wide)', 'beyond-borders' ); ?></p>
                    </div>
                </td>
            </tr>

            <tr class="logo-image-row">
                <th scope="row">
                    <label for="dark_logo_image"><?php _e( 'Dark Theme Logo', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <div class="dark-logo-upload-wrapper">
                        <input type="hidden" name="dark_logo_image_id" id="dark_logo_image_id" value="<?php echo esc_attr( $header_settings['dark_logo_image_id'] ?? 0 ); ?>" />
                        
                        <div class="dark-logo-preview" style="margin-bottom: 10px;">
                            <?php 
                            $dark_logo_image_id = $header_settings['dark_logo_image_id'] ?? 0;
                            $dark_logo_image_url = $dark_logo_image_id ? wp_get_attachment_image_url( $dark_logo_image_id, 'full' ) : '';
                            ?>
                            <?php if ( $dark_logo_image_url ) : ?>
                                <img src="<?php echo esc_url( $dark_logo_image_url ); ?>" alt="Dark Logo" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px; background: #1a1a1a;" />
                            <?php else : ?>
                                <div style="width: 200px; height: 100px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999;">
                                    <?php _e( 'No dark logo uploaded', 'beyond-borders' ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="button" class="button button-secondary upload-dark-logo-button">
                            <?php _e( 'Upload Dark Logo', 'beyond-borders' ); ?>
                        </button>
                        
                        <?php if ( $dark_logo_image_url ) : ?>
                            <button type="button" class="button button-link-delete remove-dark-logo-button" style="margin-left: 10px; color: #a00;">
                                <?php _e( 'Remove Dark Logo', 'beyond-borders' ); ?>
                            </button>
                        <?php endif; ?>
                        
                        <p class="description"><?php _e( 'Upload a logo for dark theme (optional, will use light logo if not set)', 'beyond-borders' ); ?></p>
                    </div>
                </td>
            </tr>

            <tr class="logo-text-row">
                <th scope="row">
                    <label for="logo_text"><?php _e( 'Logo Text', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="logo_text" id="logo_text" value="<?php echo esc_attr( $header_settings['logo_text'] ?? '' ); ?>" class="regular-text" />
                    <p class="description"><?php _e( 'Custom text for logo (leave empty to use site name)', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e( 'Display Options', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="show_site_name" value="1" <?php checked( $header_settings['show_site_name'] ?? true, 1 ); ?> />
                        <?php _e( 'Show site name next to logo', 'beyond-borders' ); ?>
                    </label>
                    <p class="description"><?php _e( 'Display the site name beside the logo/icon', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr class="logo-width-row">
                <th scope="row">
                    <label for="logo_width"><?php _e( 'Logo Width', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="number" name="logo_width" id="logo_width" value="<?php echo esc_attr( $header_settings['logo_width'] ?? 200 ); ?>" min="50" max="600" step="10" />
                    <span>px</span>
                    <p class="description"><?php _e( 'Maximum width for the logo image (50-600px)', 'beyond-borders' ); ?></p>
                </td>
            </tr>
        </table>

        <h2><?php _e( 'Header Layout', 'beyond-borders' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="header_layout"><?php _e( 'Header Style', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <select name="header_layout" id="header_layout">
                        <option value="default" <?php selected( $header_settings['header_layout'] ?? 'default', 'default' ); ?>><?php _e( 'Default', 'beyond-borders' ); ?></option>
                        <option value="centered" <?php selected( $header_settings['header_layout'] ?? 'default', 'centered' ); ?>><?php _e( 'Centered Logo', 'beyond-borders' ); ?></option>
                        <option value="minimal" <?php selected( $header_settings['header_layout'] ?? 'default', 'minimal' ); ?>><?php _e( 'Minimal', 'beyond-borders' ); ?></option>
                        <option value="magazine" <?php selected( $header_settings['header_layout'] ?? 'default', 'magazine' ); ?>><?php _e( 'Magazine Style', 'beyond-borders' ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'Choose the overall header layout style', 'beyond-borders' ); ?></p>
                </td>
            </tr>
        </table>

        <h2><?php _e( 'Top Bar', 'beyond-borders' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'Enable Top Bar', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enable_top_bar" value="1" <?php checked( $header_settings['enable_top_bar'] ?? true, 1 ); ?> />
                        <?php _e( 'Show top bar above header', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="top_bar_text"><?php _e( 'Top Bar Text', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="top_bar_text" id="top_bar_text" value="<?php echo esc_attr( $header_settings['top_bar_text'] ?? '' ); ?>" class="regular-text" />
                    <p class="description"><?php _e( 'Text displayed in the top bar', 'beyond-borders' ); ?></p>
                </td>
            </tr>
        </table>

        <h2><?php _e( 'Navigation', 'beyond-borders' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'Sticky Header', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enable_sticky_header" value="1" <?php checked( $header_settings['enable_sticky_header'] ?? true, 1 ); ?> />
                        <?php _e( 'Keep header fixed when scrolling', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="selected_menu"><?php _e( 'Select Menu', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <select name="selected_menu" id="selected_menu">
                        <option value=""><?php _e( 'Default (Primary Menu)', 'beyond-borders' ); ?></option>
                        <?php
                        $menus = wp_get_nav_menus();
                        foreach ( $menus as $menu ) {
                            $selected = selected( $header_settings['selected_menu'] ?? '', $menu->term_id, false );
                            echo '<option value="' . esc_attr( $menu->term_id ) . '" ' . $selected . '>' . esc_html( $menu->name ) . '</option>';
                        }
                        ?>
                    </select>
                    <p class="description"><?php _e( 'Choose which menu to display in the header', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="menu_position"><?php _e( 'Menu Position', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <select name="menu_position" id="menu_position">
                        <option value="left" <?php selected( $header_settings['menu_position'] ?? 'center', 'left' ); ?>><?php _e( 'Left Aligned', 'beyond-borders' ); ?></option>
                        <option value="center" <?php selected( $header_settings['menu_position'] ?? 'center', 'center' ); ?>><?php _e( 'Center Aligned', 'beyond-borders' ); ?></option>
                        <option value="right" <?php selected( $header_settings['menu_position'] ?? 'center', 'right' ); ?>><?php _e( 'Right Aligned', 'beyond-borders' ); ?></option>
                        <option value="split" <?php selected( $header_settings['menu_position'] ?? 'center', 'split' ); ?>><?php _e( 'Split (Logo Center)', 'beyond-borders' ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'Navigation menu alignment in header', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="menu_style"><?php _e( 'Menu Style', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <select name="menu_style" id="menu_style">
                        <option value="classic" <?php selected( $header_settings['menu_style'] ?? 'classic', 'classic' ); ?>><?php _e( 'Classic Dropdown', 'beyond-borders' ); ?></option>
                        <option value="overlay" <?php selected( $header_settings['menu_style'] ?? 'classic', 'overlay' ); ?>><?php _e( 'Overlay Menu', 'beyond-borders' ); ?></option>
                        <option value="slide-in" <?php selected( $header_settings['menu_style'] ?? 'classic', 'slide-in' ); ?>><?php _e( 'Slide-in Menu', 'beyond-borders' ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'Navigation menu display style', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e( 'Mega Menu', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enable_mega_menu" value="1" <?php checked( $header_settings['enable_mega_menu'] ?? false, 1 ); ?> />
                        <?php _e( 'Enable mega menu for top-level items', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>
        </table>

        <h2><?php _e( 'Header Elements', 'beyond-borders' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'Search Icon', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="show_search" value="1" <?php checked( $header_settings['show_search'] ?? true, 1 ); ?> />
                        <?php _e( 'Show search icon in header', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="search_style"><?php _e( 'Search Style', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <select name="search_style" id="search_style">
                        <option value="fullscreen" <?php selected( $header_settings['search_style'] ?? 'fullscreen', 'fullscreen' ); ?>><?php _e( 'Full Screen Overlay', 'beyond-borders' ); ?></option>
                        <option value="popup" <?php selected( $header_settings['search_style'] ?? 'fullscreen', 'popup' ); ?>><?php _e( 'Popup Modal', 'beyond-borders' ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'Choose between full screen overlay or centered popup modal', 'beyond-borders' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Theme Toggle (Dark Mode)', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="show_theme_toggle" value="1" <?php checked( $header_settings['show_theme_toggle'] ?? true, 1 ); ?> />
                        <?php _e( 'Show light/dark theme toggle button', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="default_theme"><?php _e( 'Default Theme', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <select name="default_theme" id="default_theme">
                        <option value="light" <?php selected( $header_settings['default_theme'] ?? 'light', 'light' ); ?>><?php _e( 'Light Mode', 'beyond-borders' ); ?></option>
                        <option value="dark" <?php selected( $header_settings['default_theme'] ?? 'light', 'dark' ); ?>><?php _e( 'Dark Mode', 'beyond-borders' ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'Default color scheme for first-time visitors', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e( 'Subscribe Button', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="show_subscribe_button" value="1" <?php checked( $header_settings['show_subscribe_button'] ?? true, 1 ); ?> />
                        <?php _e( 'Show subscribe button in header', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="subscribe_button_text"><?php _e( 'Subscribe Button Text', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="subscribe_button_text" id="subscribe_button_text" value="<?php echo esc_attr( $header_settings['subscribe_button_text'] ?? 'Subscribe' ); ?>" class="regular-text" />
                    <p class="description"><?php _e( 'Text displayed on the subscribe button', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="subscribe_button_url"><?php _e( 'Subscribe Button URL', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="url" name="subscribe_button_url" id="subscribe_button_url" value="<?php echo esc_url( $header_settings['subscribe_button_url'] ?? '#subscribe' ); ?>" class="regular-text" />
                    <p class="description"><?php _e( 'Link for the subscribe button (e.g., /subscribe or external URL)', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e( 'Social Links', 'beyond-borders' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="show_social_links" value="1" <?php checked( $header_settings['show_social_links'] ?? true, 1 ); ?> />
                        <?php _e( 'Display social media icons in header', 'beyond-borders' ); ?>
                    </label>
                </td>
            </tr>
        </table>

        <h2><?php _e( 'Social Media Links', 'beyond-borders' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="social_facebook"><?php _e( 'Facebook URL', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="url" name="social_facebook" id="social_facebook" value="<?php echo esc_url( $header_settings['social_facebook'] ?? '' ); ?>" class="regular-text" placeholder="https://facebook.com/yourpage" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="social_twitter"><?php _e( 'Twitter/X URL', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="url" name="social_twitter" id="social_twitter" value="<?php echo esc_url( $header_settings['social_twitter'] ?? '' ); ?>" class="regular-text" placeholder="https://twitter.com/yourhandle" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="social_instagram"><?php _e( 'Instagram URL', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="url" name="social_instagram" id="social_instagram" value="<?php echo esc_url( $header_settings['social_instagram'] ?? '' ); ?>" class="regular-text" placeholder="https://instagram.com/yourhandle" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="social_linkedin"><?php _e( 'LinkedIn URL', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="url" name="social_linkedin" id="social_linkedin" value="<?php echo esc_url( $header_settings['social_linkedin'] ?? '' ); ?>" class="regular-text" placeholder="https://linkedin.com/company/yourcompany" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="social_youtube"><?php _e( 'YouTube URL', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="url" name="social_youtube" id="social_youtube" value="<?php echo esc_url( $header_settings['social_youtube'] ?? '' ); ?>" class="regular-text" placeholder="https://youtube.com/@yourchannel" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="social_rss"><?php _e( 'RSS Feed URL', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="url" name="social_rss" id="social_rss" value="<?php echo esc_url( $header_settings['social_rss'] ?? '' ); ?>" class="regular-text" placeholder="<?php echo esc_url( home_url( '/feed' ) ); ?>" />
                    <p class="description"><?php _e( 'Leave empty to use default WordPress RSS feed', 'beyond-borders' ); ?></p>
                </td>
            </tr>
        </table>

        </div>
        </div>
        <!-- End of settings grid -->

        <div class="bb-settings-footer">
            <button type="submit" name="beyond_borders_save_header" class="bb-button bb-button-primary">
                <?php _e( 'Save Header Settings', 'beyond-borders' ); ?>
            </button>
            <p class="bb-footer-note">
                <?php _e( 'Configure navigation menus at', 'beyond-borders' ); ?> 
                <a href="<?php echo admin_url( 'nav-menus.php' ); ?>"><?php _e( 'Appearance → Menus', 'beyond-borders' ); ?></a>
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

.bb-card-body .form-table th {
    padding: 12px 0 12px 0;
    font-weight: 500;
    color: #1e1e1e;
}

.bb-card-body .form-table td {
    padding: 12px 0;
}

.bb-card-body .form-table input[type="text"],
.bb-card-body .form-table input[type="number"],
.bb-card-body .form-table select,
.bb-card-body .form-table textarea {
    background: #f5f5f587;
    border: none;
    border-radius: 6px;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.bb-card-body .form-table input[type="text"]:hover,
.bb-card-body .form-table input[type="number"]:hover,
.bb-card-body .form-table select:hover,
.bb-card-body .form-table textarea:hover {
    background: #f5f5f587;
}

.bb-card-body .form-table input[type="text"]:focus,
.bb-card-body .form-table input[type="number"]:focus,
.bb-card-body .form-table select:focus,
.bb-card-body .form-table textarea:focus {
    outline: none;
    background: #e8e8e8;
    box-shadow: 0 0 0 2px rgba(0, 50, 101, 0.1);
}

.bb-card-body .form-table .description {
    margin: 6px 0 0 0;
    font-size: 13px;
    color: #646970;
}

.bb-card-body .form-table label {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: #f5f5f587;
    border: none;
    border-radius: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
    max-width: 30%;
}

.bb-card-body .form-table label:hover {
    background: #ebebeb;
}

.bb-card-body .form-table input[type="checkbox"] {
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

.bb-card-body .form-table input[type="checkbox"]:hover {
    background: #d0d0d0;
}

.bb-card-body .form-table input[type="checkbox"]:checked {
    background: #003265;
}

.bb-card-body .form-table input[type="checkbox"]:checked::after {
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

.bb-card-body .form-table input[type="checkbox"]:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 50, 101, 0.1);
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

<script>
jQuery(document).ready(function($) {
    
    // Logo type conditional display
    function toggleLogoFields() {
        var logoType = $('#logo_type').val();
        
        $('.logo-image-row, .logo-width-row').toggle(logoType === 'image');
        $('.logo-text-row').toggle(logoType === 'text');
    }
    
    $('#logo_type').on('change', toggleLogoFields);
    toggleLogoFields(); // Run on load
    
    // WordPress Media Uploader
    var logoUploader;
    
    $('.upload-logo-button').on('click', function(e) {
        e.preventDefault();
        
        // If the uploader object has already been created, reopen the dialog
        if (logoUploader) {
            logoUploader.open();
            return;
        }
        
        // Create the media frame
        logoUploader = wp.media({
            title: '<?php _e( 'Choose Logo Image', 'beyond-borders' ); ?>',
            button: {
                text: '<?php _e( 'Use this image', 'beyond-borders' ); ?>'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        // When an image is selected, run a callback
        logoUploader.on('select', function() {
            var attachment = logoUploader.state().get('selection').first().toJSON();
            
            // Set the image ID
            $('#logo_image_id').val(attachment.id);
            
            // Update preview
            var imgUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
            $('.logo-preview').html('<img src="' + imgUrl + '" alt="Logo" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px; background: #f9f9f9;" />');
            
            // Show remove button if not already visible
            if (!$('.remove-logo-button').length) {
                $('.upload-logo-button').after('<button type="button" class="button button-link-delete remove-logo-button" style="margin-left: 10px; color: #a00;"><?php _e( 'Remove Logo', 'beyond-borders' ); ?></button>');
            }
        });
        
        // Open the uploader dialog
        logoUploader.open();
    });
    
    // Remove logo
    $(document).on('click', '.remove-logo-button', function(e) {
        e.preventDefault();
        
        if (confirm('<?php _e( 'Are you sure you want to remove the logo?', 'beyond-borders' ); ?>')) {
            $('#logo_image_id').val('');
            $('.logo-preview').html('<div style="width: 200px; height: 100px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999;"><?php _e( 'No logo uploaded', 'beyond-borders' ); ?></div>');
            $('.remove-logo-button').remove();
        }
    });
    
    // Dark Logo Upload
    var darkLogoUploader;
    
    $('.upload-dark-logo-button').on('click', function(e) {
        e.preventDefault();
        
        if (darkLogoUploader) {
            darkLogoUploader.open();
            return;
        }
        
        darkLogoUploader = wp.media({
            title: '<?php _e( 'Choose Dark Theme Logo', 'beyond-borders' ); ?>',
            button: {
                text: '<?php _e( 'Use this image', 'beyond-borders' ); ?>'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        darkLogoUploader.on('select', function() {
            var attachment = darkLogoUploader.state().get('selection').first().toJSON();
            $('#dark_logo_image_id').val(attachment.id);
            
            var imgUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
            $('.dark-logo-preview').html('<img src="' + imgUrl + '" alt="Dark Logo" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px; background: #1a1a1a;" />');
            
            if (!$('.remove-dark-logo-button').length) {
                $('.upload-dark-logo-button').after('<button type="button" class="button button-link-delete remove-dark-logo-button" style="margin-left: 10px; color: #a00;"><?php _e( 'Remove Dark Logo', 'beyond-borders' ); ?></button>');
            }
        });
        
        darkLogoUploader.open();
    });
    
    // Remove dark logo
    $(document).on('click', '.remove-dark-logo-button', function(e) {
        e.preventDefault();
        
        if (confirm('<?php _e( 'Are you sure you want to remove the dark logo?', 'beyond-borders' ); ?>')) {
            $('#dark_logo_image_id').val('');
            $('.dark-logo-preview').html('<div style="width: 200px; height: 100px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999;"><?php _e( 'No dark logo uploaded', 'beyond-borders' ); ?></div>');
            $('.remove-dark-logo-button').remove();
        }
    });
});
</script>

<style>
.logo-upload-wrapper {
    max-width: 600px;
}

.logo-preview img {
    display: block;
}

.upload-logo-button {
    margin-top: 10px;
}
</style>

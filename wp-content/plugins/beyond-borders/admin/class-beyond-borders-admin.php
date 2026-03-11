<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Admin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style( 
            $this->plugin_name, 
            BEYOND_BORDERS_PLUGIN_URL . 'admin/css/beyond-borders-admin.css', 
            array(), 
            $this->version, 
            'all' 
        );
        
        // Enqueue admin settings styles on Beyond Borders pages
        $screen = get_current_screen();
        if ( $screen && strpos( $screen->id, 'beyond-borders' ) !== false ) {
            wp_enqueue_style( 
                $this->plugin_name . '-settings', 
                BEYOND_BORDERS_PLUGIN_URL . 'admin/css/admin-settings.css', 
                array(), 
                $this->version, 
                'all' 
            );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 
            $this->plugin_name, 
            BEYOND_BORDERS_PLUGIN_URL . 'admin/js/beyond-borders-admin.js', 
            array( 'jquery' ), 
            $this->version, 
            false 
        );
        
        // Enqueue media uploader on header settings page
        $screen = get_current_screen();
        if ( $screen && $screen->id === 'beyond-borders_page_beyond-borders-header' ) {
            wp_enqueue_media();
        }
        
        // Enqueue media uploader on font settings page for custom font uploads
        if ( $screen && $screen->id === 'beyond-borders_page_beyond-borders-fonts' ) {
            wp_enqueue_media();
            wp_enqueue_script( 
                $this->plugin_name . '-fonts', 
                BEYOND_BORDERS_PLUGIN_URL . 'admin/js/font-settings.js', 
                array( 'jquery', 'wp-color-picker' ), 
                $this->version, 
                false 
            );
        }
        
        // Enqueue color picker on settings pages
        if ( $screen && strpos( $screen->id, 'beyond-borders' ) !== false ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
        }

        // Enqueue user management scripts on user management page
        if ( $screen && $screen->id === 'beyond-borders_page_beyond-borders-users' ) {
            wp_enqueue_script( 
                $this->plugin_name . '-user-management', 
                BEYOND_BORDERS_PLUGIN_URL . 'admin/js/user-management.js', 
                array( 'jquery' ), 
                $this->version, 
                false 
            );

            // Localize script with translation strings
            wp_localize_script(
                $this->plugin_name . '-user-management',
                'beyondBordersL10n',
                array(
                    'saving' => esc_html__( 'Saving...', 'beyond-borders' ),
                    'saved' => esc_html__( 'Saved', 'beyond-borders' ),
                    'error' => esc_html__( 'Error', 'beyond-borders' ),
                )
            );
        }
    }

    /**
     * Add plugin admin menu.
     */
    public function add_plugin_admin_menu() {
        
        add_menu_page(
            __( 'Beyond Borders', 'beyond-borders' ),
            __( 'Beyond Borders', 'beyond-borders' ),
            'manage_options',
            'beyond-borders',
            array( $this, 'display_plugin_admin_page' ),
            BEYOND_BORDERS_PLUGIN_URL . 'icon-20x20.png',
            26
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Header Settings', 'beyond-borders' ),
            __( 'Header', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-header',
            array( $this, 'display_header_settings_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Footer Settings', 'beyond-borders' ),
            __( 'Footer', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-footer',
            array( $this, 'display_footer_settings_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Homepage Settings', 'beyond-borders' ),
            __( 'Homepage', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-homepage',
            array( $this, 'display_homepage_settings_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Stats Panel', 'beyond-borders' ),
            __( 'Stats Panel', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-stats',
            array( $this, 'display_stats_settings_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Color Palette', 'beyond-borders' ),
            __( 'Color Palette', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-colors',
            array( $this, 'display_color_palette_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Font Settings', 'beyond-borders' ),
            __( 'Fonts', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-fonts',
            array( $this, 'display_font_settings_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Newsletter', 'beyond-borders' ),
            __( 'Newsletter', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-newsletter',
            array( $this, 'display_newsletter_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'User Management', 'beyond-borders' ),
            __( 'User Management', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-users',
            array( $this, 'display_user_management_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Brand Visualization', 'beyond-borders' ),
            __( 'Brand Viz', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-brand-viz',
            array( $this, 'display_brand_viz_divisions_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Brands', 'beyond-borders' ),
            __( 'Brands', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-brand-viz-brands',
            array( $this, 'display_brand_viz_brands_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Visualization Preview', 'beyond-borders' ),
            __( 'Visualization', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-brand-viz-display',
            array( $this, 'display_brand_viz_visualization_page' )
        );

        add_submenu_page(
            'beyond-borders',
            __( 'Settings', 'beyond-borders' ),
            __( 'Settings', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-settings',
            array( $this, 'display_plugin_admin_page' )
        );

        // Pax Data - Separate Main Menu
        add_menu_page(
            __( 'PAX Data', 'beyond-borders' ),
            __( 'PAX Data', 'beyond-borders' ),
            'manage_options',
            'pax-data-main',
            array( $this, 'display_pax_data_page' ),
            'dashicons-chart-line',
            27
        );

        add_submenu_page(
            'pax-data-main',
            __( 'PAX Data', 'beyond-borders' ),
            __( 'PAX Data', 'beyond-borders' ),
            'manage_options',
            'pax-data-main',
            array( $this, 'display_pax_data_page' )
        );

        add_submenu_page(
            'pax-data-main',
            __( 'Airport Management', 'beyond-borders' ),
            __( 'Airports', 'beyond-borders' ),
            'manage_options',
            'beyond-borders-airports',
            array( $this, 'display_airports_page' )
        );
    }

    /**
     * Render the settings page for this plugin.
     */
    public function display_plugin_admin_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-admin-display.php';
    }

    /**
     * Render the header settings page.
     */
    public function display_header_settings_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-header-settings.php';
    }

    /**
     * Render the footer settings page.
     */
    public function display_footer_settings_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-footer-settings.php';
    }

    /**
     * Render the homepage settings page.
     */
    public function display_homepage_settings_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-homepage-settings.php';
    }

    /**
     * Render the stats panel settings page.
     */
    public function display_stats_settings_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-stats-settings.php';
    }

    /**
     * Render the color palette page.
     */
    public function display_color_palette_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-color-palette.php';
    }

    /**
     * Render the font settings page.
     */
    public function display_font_settings_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-font-settings.php';
    }

    /**
     * Render the newsletter page.
     */
    public function display_newsletter_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-newsletter.php';
    }

    /**
     * Render the user management page.
     */
    public function display_user_management_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-user-management.php';
    }

    /**
     * Render the PAX data page.
     */
    public function display_pax_data_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-pax-data.php';
    }

    /**
     * Render the Airport Management page.
     */
    public function display_airports_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/beyond-borders-airports.php';
    }

    /**
     * Render Brand Viz Divisions page
     */
    public function display_brand_viz_divisions_page() {
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/brand-visualization/divisions-page.php';
    }

    /**
     * Render Brand Viz Brands page
     */
    public function display_brand_viz_brands_page() {
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/brand-visualization/brands-page.php';
    }

    /**
     * Render Brand Viz Visualization page
     */
    public function display_brand_viz_visualization_page() {
        include_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/partials/brand-visualization/visualization-page.php';
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        
        register_setting( 
            'beyond_borders_settings_group', 
            'beyond_borders_settings' 
        );

        register_setting( 
            'beyond_borders_colors_group', 
            'beyond_borders_theme_colors' 
        );

        register_setting( 
            'beyond_borders_editorial_group', 
            'beyond_borders_editorial_settings' 
        );

        register_setting( 
            'beyond_borders_header_group', 
            'beyond_borders_header_settings' 
        );

        register_setting( 
            'beyond_borders_footer_group', 
            'beyond_borders_footer_settings' 
        );

        register_setting( 
            'beyond_borders_homepage_group', 
            'beyond_borders_homepage_settings' 
        );

        register_setting( 
            'beyond_borders_stats_group', 
            'beyond_borders_stats_settings' 
        );

        register_setting( 
            'beyond_borders_fonts_group', 
            'beyond_borders_font_settings' 
        );
    }
}

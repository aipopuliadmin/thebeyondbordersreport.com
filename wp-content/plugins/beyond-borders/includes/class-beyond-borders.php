<?php
/**
 * The core plugin class.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        
        $this->version = BEYOND_BORDERS_VERSION;
        $this->plugin_name = 'beyond-borders';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        
        // Loader class
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-loader.php';
        
        // Internationalization
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-i18n.php';
        
        // Admin-specific functionality
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'admin/class-beyond-borders-admin.php';
        
        // Public-facing functionality
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'public/class-beyond-borders-public.php';
        
        // Custom post types
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-post-types.php';
        
        // Customizer integration
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-customizer.php';
        
        // PAX Data management
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-pax-data.php';

        $this->loader = new Beyond_Borders_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     */
    private function set_locale() {
        
        $plugin_i18n = new Beyond_Borders_i18n();
        
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     */
    private function define_admin_hooks() {
        
        $plugin_admin = new Beyond_Borders_Admin( $this->get_plugin_name(), $this->get_version() );

        // Enqueue admin scripts and styles
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
        // Add admin menu
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        
        // Register settings
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );

        // User Management
        $user_manager = new Beyond_Borders_User_Management( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'admin_init', $user_manager, 'register_settings' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        
        $plugin_public = new Beyond_Borders_Public( $this->get_plugin_name(), $this->get_version() );

        // Enqueue public scripts and styles
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        
        // Custom post types
        $post_types = new Beyond_Borders_Post_Types();
        $this->loader->add_action( 'init', $post_types, 'register_post_types' );
        $this->loader->add_action( 'init', $post_types, 'register_taxonomies' );
        
        // Customizer
        $customizer = new Beyond_Borders_Customizer();
        $this->loader->add_action( 'customize_register', $customizer, 'register_customizer_settings' );
        $this->loader->add_action( 'wp_head', $customizer, 'output_customizer_css' );
        $this->loader->add_action( 'customize_preview_init', $customizer, 'enqueue_customizer_preview' );
        
        // Newsletter
        $newsletter = new Beyond_Borders_Newsletter();
        $this->loader->add_action( 'wp_ajax_subscribe_newsletter', $newsletter, 'handle_subscription' );
        $this->loader->add_action( 'wp_ajax_nopriv_subscribe_newsletter', $newsletter, 'handle_subscription' );
        
        // PAX Data
        $pax_data = new Beyond_Borders_Pax_Data();
        $this->loader->add_action( 'wp_ajax_preview_pax_import', $pax_data, 'handle_preview_import' );
        $this->loader->add_action( 'wp_ajax_import_pax_csv', $pax_data, 'handle_csv_import' );
        $this->loader->add_action( 'wp_ajax_export_pax_csv', $pax_data, 'handle_csv_export' );
        $this->loader->add_action( 'wp_ajax_delete_pax_record', $pax_data, 'handle_delete_record' );
        $this->loader->add_action( 'wp_ajax_get_pax_data', $pax_data, 'handle_get_pax_data' );
        $this->loader->add_action( 'wp_ajax_nopriv_get_pax_data', $pax_data, 'handle_get_pax_data' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}

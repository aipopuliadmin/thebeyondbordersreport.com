<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Public {

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
     * Register the stylesheets for the public-facing side.
     */
    public function enqueue_styles() {
        wp_enqueue_style( 
            $this->plugin_name, 
            BEYOND_BORDERS_PLUGIN_URL . 'public/css/beyond-borders-public.css', 
            array(), 
            $this->version, 
            'all' 
        );
    }

    /**
     * Register the JavaScript for the public-facing side.
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 
            $this->plugin_name, 
            BEYOND_BORDERS_PLUGIN_URL . 'public/js/beyond-borders-public.js', 
            array( 'jquery' ), 
            $this->version, 
            false 
        );
    }
}

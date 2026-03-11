<?php
/**
 * Define the internationalization functionality.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_i18n {

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {
        
        load_plugin_textdomain(
            'beyond-borders',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }
}

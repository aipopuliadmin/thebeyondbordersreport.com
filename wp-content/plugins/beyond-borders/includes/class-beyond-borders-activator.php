<?php
/**
 * Fired during plugin activation.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Activator {

    /**
     * Activation tasks.
     *
     * - Creates default options
     * - Sets up custom capabilities
     * - Flushes rewrite rules
     * - Creates default terms
     */
    public static function activate() {
        
        // Check WordPress version
        if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'Beyond Borders requires WordPress 6.0 or higher.', 'beyond-borders' ) );
        }

        // Check PHP version
        if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'Beyond Borders requires PHP 7.4 or higher.', 'beyond-borders' ) );
        }

        // Set default options
        self::set_default_options();

        // Add custom capabilities to roles
        self::add_custom_capabilities();

        // Create default taxonomy terms
        self::create_default_terms();

        // Create newsletter database table
        self::create_newsletter_table();

        // Create PAX data database tables
        self::create_pax_data_tables();

        // Create brand visualization tables
        self::create_brand_viz_tables();

        // Set plugin version
        update_option( 'beyond_borders_version', BEYOND_BORDERS_VERSION );

        // Flush rewrite rules
        flush_rewrite_rules();

        // Set activation timestamp
        update_option( 'beyond_borders_activated', current_time( 'timestamp' ) );
    }

    /**
     * Set default plugin options.
     */
    private static function set_default_options() {
        
        // Default color palette from THEME-CONFIG-FINAL.md
        $default_colors = array(
            'navy_deep'       => '#0A1628',
            'navy_primary'    => '#003366',
            'gold_primary'    => '#C9A961',
            'gold_champagne'  => '#D4AF37',
            'cream'           => '#FAF8F5',
            'charcoal_dark'   => '#1A1A2E',
        );
        
        if ( ! get_option( 'beyond_borders_theme_colors' ) ) {
            add_option( 'beyond_borders_theme_colors', $default_colors );
        }

        // Default typography settings
        $default_typography = array(
            'headline_font' => 'Playfair Display',
            'body_font'     => 'Lora',
            'ui_font'       => 'Inter',
        );
        
        if ( ! get_option( 'beyond_borders_typography' ) ) {
            add_option( 'beyond_borders_typography', $default_typography );
        }

        // Default editorial settings
        $default_editorial = array(
            'enable_author_dashboard'   => true,
            'enable_submission_workflow' => true,
            'require_featured_image'    => true,
            'excerpt_length'            => 55,
        );
        
        if ( ! get_option( 'beyond_borders_editorial_settings' ) ) {
            add_option( 'beyond_borders_editorial_settings', $default_editorial );
        }

        // General settings
        $default_settings = array(
            'enable_customizer'     => true,
            'enable_custom_posts'   => true,
            'enable_analytics'      => false,
        );
        
        if ( ! get_option( 'beyond_borders_settings' ) ) {
            add_option( 'beyond_borders_settings', $default_settings );
        }
    }

    /**
     * Add custom capabilities to user roles.
     */
    private static function add_custom_capabilities() {
        
        // Get roles
        $admin = get_role( 'administrator' );
        $editor = get_role( 'editor' );
        $author = get_role( 'author' );

        // Custom capabilities for featured articles
        $caps = array(
            'edit_featured_article',
            'read_featured_article',
            'delete_featured_article',
            'edit_featured_articles',
            'edit_others_featured_articles',
            'publish_featured_articles',
            'read_private_featured_articles',
        );

        // Add capabilities to administrator
        if ( $admin ) {
            foreach ( $caps as $cap ) {
                $admin->add_cap( $cap );
            }
        }

        // Add capabilities to editor
        if ( $editor ) {
            foreach ( $caps as $cap ) {
                $editor->add_cap( $cap );
            }
        }

        // Limited capabilities for authors
        if ( $author ) {
            $author->add_cap( 'edit_featured_article' );
            $author->add_cap( 'read_featured_article' );
            $author->add_cap( 'delete_featured_article' );
            $author->add_cap( 'edit_featured_articles' );
            
            // Ensure authors can assign categories and tags
            $author->add_cap( 'assign_categories' );
            $author->add_cap( 'assign_post_tags' );
            
            // Remove publish capability - authors need admin approval to publish
            $author->remove_cap( 'publish_posts' );
        }
    }

    /**
     * Create default taxonomy terms.
     */
    private static function create_default_terms() {
        
        // Default categories for editorial content
        $default_categories = array(
            'Business'       => 'Global business news and insights',
            'Travel'         => 'Travel destinations and experiences',
            'Aviation'       => 'Aviation industry and air travel',
            'Luxury Retail'  => 'High-end retail and fashion',
            'Leadership'     => 'Executive leadership and management',
            'Innovation'     => 'Technology and innovation',
        );

        foreach ( $default_categories as $name => $description ) {
            if ( ! term_exists( $name, 'category' ) ) {
                wp_insert_term(
                    $name,
                    'category',
                    array(
                        'description' => $description,
                        'slug'        => sanitize_title( $name ),
                    )
                );
            }
        }
    }

    /**
     * Create newsletter database table.
     */
    private static function create_newsletter_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'newsletter_subscribers';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            email varchar(100) NOT NULL,
            name varchar(100) DEFAULT NULL,
            status varchar(20) DEFAULT 'active',
            subscribed_date datetime DEFAULT CURRENT_TIMESTAMP,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email),
            KEY status (status)
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Create PAX data database tables.
     */
    private static function create_pax_data_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create airports reference table
        $airports_table = $wpdb->prefix . 'airports_reference';
        $sql_airports = "CREATE TABLE IF NOT EXISTS $airports_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            airport_name varchar(100) NOT NULL,
            iata_code varchar(3) DEFAULT NULL,
            icao_code varchar(4) DEFAULT NULL,
            airport_type varchar(50) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY airport_name (airport_name),
            KEY iata_code (iata_code),
            KEY icao_code (icao_code)
        ) $charset_collate;";
        
        // Create PAX data table
        $pax_table = $wpdb->prefix . 'pax_data';
        $sql_pax = "CREATE TABLE IF NOT EXISTS $pax_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            airport_id bigint(20) unsigned NOT NULL,
            pax_type varchar(50) NOT NULL,
            year int(4) NOT NULL,
            month varchar(20) NOT NULL,
            passengers bigint(20) unsigned NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY airport_id (airport_id),
            KEY pax_type (pax_type),
            KEY year (year),
            KEY month (month)
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_airports );
        dbDelta( $sql_pax );
        
        // Pre-populate airports reference table
        self::populate_airports_reference();
    }

    /**
     * Populate airports reference table with Indian airports.
     */
    private static function populate_airports_reference() {
        global $wpdb;
        
        $airports_table = $wpdb->prefix . 'airports_reference';
        
        // Check if already populated
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM $airports_table" );
        if ( $count > 0 ) {
            return; // Already populated
        }
        
        // Indian airports data
        $airports = array(
            array( 'name' => 'CHENNAI', 'iata' => 'MAA', 'icao' => 'VOMM', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'KOLKATA', 'iata' => 'CCU', 'icao' => 'VECC', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'AHMEDABAD', 'iata' => 'AMD', 'icao' => 'VAAH', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'GOA', 'iata' => 'GOI', 'icao' => 'VOGO', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'TRIVANDRUM', 'iata' => 'TRV', 'icao' => 'VOTV', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'LUCKNOW', 'iata' => 'LKO', 'icao' => 'VILK', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'JAIPUR', 'iata' => 'JAI', 'icao' => 'VIJP', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'GUWAHATI', 'iata' => 'GAU', 'icao' => 'VEGT', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'SRINAGAR', 'iata' => 'SXR', 'icao' => 'VISR', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'CALICUT', 'iata' => 'CCJ', 'icao' => 'VOCL', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'BHUBANESWAR', 'iata' => 'BBI', 'icao' => 'VEBS', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'COIMBATORE', 'iata' => 'CJB', 'icao' => 'VOCB', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'MANGALORE', 'iata' => 'IXE', 'icao' => 'VOML', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'VARANASI', 'iata' => 'VNS', 'icao' => 'VIBN', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'TRICHY', 'iata' => 'TRZ', 'icao' => 'VOTR', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'AMRITSAR', 'iata' => 'ATQ', 'icao' => 'VIAR', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'PORTBLAIR', 'iata' => 'IXZ', 'icao' => 'VOPB', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'IMPHAL', 'iata' => 'IMF', 'icao' => 'VEIM', 'type' => '18 INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'DELHI (DIAL)', 'iata' => 'DEL', 'icao' => 'VIDP', 'type' => '6 JV INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'MUMBAI (MIAL)', 'iata' => 'BOM', 'icao' => 'VABB', 'type' => '6 JV INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'BANGALORE', 'iata' => 'BLR', 'icao' => 'VOBL', 'type' => '6 JV INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'HYDERABAD', 'iata' => 'HYD', 'icao' => 'VOHS', 'type' => '6 JV INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'COCHIN', 'iata' => 'COK', 'icao' => 'VOCI', 'type' => '6 JV INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'NAGPUR', 'iata' => 'NAG', 'icao' => 'VANP', 'type' => '6 JV INTERNATIONAL AIRPORTS' ),
            array( 'name' => 'AURANGABAD', 'iata' => 'IXU', 'icao' => 'VAAU', 'type' => '8 CUSTOM AIRPORTS' ),
            array( 'name' => 'GAYA', 'iata' => 'GAY', 'icao' => 'VEGY', 'type' => '8 CUSTOM AIRPORTS' ),
            array( 'name' => 'CHANDIGARH', 'iata' => 'IXC', 'icao' => 'VICG', 'type' => '8 CUSTOM AIRPORTS' ),
        );
        
        foreach ( $airports as $airport ) {
            $wpdb->insert(
                $airports_table,
                array(
                    'airport_name' => $airport['name'],
                    'iata_code'    => $airport['iata'],
                    'icao_code'    => $airport['icao'],
                    'airport_type' => $airport['type'],
                ),
                array( '%s', '%s', '%s', '%s' )
            );
        }
    }

    /**
     * Create brand visualization database tables
     */
    private static function create_brand_viz_tables() {
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';
        Beyond_Borders_Brand_Viz::create_tables();
    }
}


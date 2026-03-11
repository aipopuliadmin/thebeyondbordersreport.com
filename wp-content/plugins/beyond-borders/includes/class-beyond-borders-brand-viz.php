<?php
/**
 * Brand Visualization Management
 */

class Beyond_Borders_Brand_Viz {
    
    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Divisions table
        $divisions_table = $wpdb->prefix . 'brand_divisions';
        $divisions_sql = "CREATE TABLE IF NOT EXISTS $divisions_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            color varchar(7) DEFAULT '#000000',
            description text,
            parent_id mediumint(9) DEFAULT 0,
            display_order int DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Brands table
        $brands_table = $wpdb->prefix . 'brand_items';
        $brands_sql = "CREATE TABLE IF NOT EXISTS $brands_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            logo_url varchar(500),
            color varchar(7) DEFAULT '#000000',
            division_id mediumint(9) NOT NULL,
            description text,
            website varchar(500),
            display_order int DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY division_id (division_id)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $divisions_sql );
        dbDelta( $brands_sql );
        
        // Insert default data if tables are empty
        self::insert_default_data();
    }
    
    /**
     * Insert default LVMH data
     */
    private static function insert_default_data() {
        global $wpdb;
        
        $divisions_table = $wpdb->prefix . 'brand_divisions';
        $brands_table = $wpdb->prefix . 'brand_items';
        
        // Check if data already exists
        $existing = $wpdb->get_var( "SELECT COUNT(*) FROM $divisions_table" );
        if ( $existing > 0 ) {
            return;
        }
        
        // Insert LVMH as root
        $wpdb->insert( $divisions_table, array(
            'name' => 'LVMH',
            'color' => '#1a1a1a',
            'description' => 'LVMH Moët Hennessy Louis Vuitton',
            'parent_id' => 0,
            'display_order' => 0
        ) );
        $lvmh_id = $wpdb->insert_id;
        
        // Insert divisions with sample brands
        $divisions_data = array(
            array(
                'name' => 'Fashion & Leather Goods',
                'color' => '#8B4513',
                'brands' => array(
                    array( 'name' => 'Louis Vuitton', 'color' => '#654321' ),
                    array( 'name' => 'Dior', 'color' => '#D4AF37' ),
                    array( 'name' => 'Fendi', 'color' => '#FFD700' ),
                    array( 'name' => 'Céline', 'color' => '#C19A6B' ),
                    array( 'name' => 'Givenchy', 'color' => '#8B7355' ),
                    array( 'name' => 'Loewe', 'color' => '#A0826D' ),
                )
            ),
            array(
                'name' => 'Wines & Spirits',
                'color' => '#8B0000',
                'brands' => array(
                    array( 'name' => 'Moët & Chandon', 'color' => '#FFD700' ),
                    array( 'name' => 'Dom Pérignon', 'color' => '#C0C0C0' ),
                    array( 'name' => 'Veuve Clicquot', 'color' => '#FFA500' ),
                    array( 'name' => 'Hennessy', 'color' => '#8B4513' ),
                )
            ),
            array(
                'name' => 'Perfumes & Cosmetics',
                'color' => '#FF69B4',
                'brands' => array(
                    array( 'name' => 'Parfums Christian Dior', 'color' => '#FFB6C1' ),
                    array( 'name' => 'Guerlain', 'color' => '#DDA0DD' ),
                    array( 'name' => 'Givenchy Parfums', 'color' => '#FF1493' ),
                )
            ),
            array(
                'name' => 'Watches & Jewelry',
                'color' => '#4169E1',
                'brands' => array(
                    array( 'name' => 'Bvlgari', 'color' => '#FFD700' ),
                    array( 'name' => 'TAG Heuer', 'color' => '#C0C0C0' ),
                    array( 'name' => 'Hublot', 'color' => '#000000' ),
                )
            ),
            array(
                'name' => 'Selective Retailing',
                'color' => '#32CD32',
                'brands' => array(
                    array( 'name' => 'Sephora', 'color' => '#000000' ),
                    array( 'name' => 'Le Bon Marché', 'color' => '#006400' ),
                )
            ),
            array(
                'name' => 'Other Activities',
                'color' => '#9370DB',
                'brands' => array(
                    array( 'name' => 'Cheval Blanc', 'color' => '#F5F5DC' ),
                    array( 'name' => 'Les Echos', 'color' => '#4B0082' ),
                )
            )
        );
        
        $order = 1;
        foreach ( $divisions_data as $division ) {
            $wpdb->insert( $divisions_table, array(
                'name' => $division['name'],
                'color' => $division['color'],
                'parent_id' => $lvmh_id,
                'display_order' => $order++
            ) );
            $division_id = $wpdb->insert_id;
            
            $brand_order = 1;
            foreach ( $division['brands'] as $brand ) {
                $wpdb->insert( $brands_table, array(
                    'name' => $brand['name'],
                    'color' => $brand['color'],
                    'division_id' => $division_id,
                    'display_order' => $brand_order++,
                    'logo_url' => ''
                ) );
            }
        }
    }
    
    /**
     * Get divisions
     */
    public static function get_divisions( $parent_id = null ) {
        global $wpdb;
        $table = $wpdb->prefix . 'brand_divisions';
        
        if ( $parent_id !== null ) {
            return $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table WHERE parent_id = %d ORDER BY display_order ASC, name ASC",
                $parent_id
            ) );
        }
        
        return $wpdb->get_results( "SELECT * FROM $table ORDER BY display_order ASC, name ASC" );
    }
    
    /**
     * Get brands by division
     */
    public static function get_brands_by_division( $division_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'brand_items';
        
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table WHERE division_id = %d ORDER BY display_order ASC, name ASC",
            $division_id
        ) );
    }
    
    /**
     * Get complete hierarchy
     */
    public static function get_hierarchy( $parent_id = 0 ) {
        $divisions = self::get_divisions( $parent_id );
        $hierarchy = array();
        
        foreach ( $divisions as $division ) {
            $division_data = array(
                'id' => $division->id,
                'name' => $division->name,
                'color' => $division->color,
                'description' => $division->description,
                'type' => 'division',
                'children' => array()
            );
            
            // Get child divisions
            $child_divisions = self::get_hierarchy( $division->id );
            
            // Get brands
            $brands = self::get_brands_by_division( $division->id );
            
            foreach ( $brands as $brand ) {
                $division_data['children'][] = array(
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'color' => $brand->color,
                    'logo_url' => $brand->logo_url,
                    'description' => $brand->description,
                    'website' => $brand->website,
                    'type' => 'brand'
                );
            }
            
            $division_data['children'] = array_merge( $division_data['children'], $child_divisions );
            
            $hierarchy[] = $division_data;
        }
        
        return $hierarchy;
    }
    
    /**
     * Save division
     */
    public static function save_division( $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'brand_divisions';
        
        $division_data = array(
            'name' => sanitize_text_field( $data['name'] ),
            'color' => sanitize_hex_color( $data['color'] ),
            'description' => sanitize_textarea_field( $data['description'] ?? '' ),
            'parent_id' => intval( $data['parent_id'] ?? 0 ),
            'display_order' => intval( $data['display_order'] ?? 0 )
        );
        
        if ( ! empty( $data['id'] ) ) {
            $wpdb->update( $table, $division_data, array( 'id' => intval( $data['id'] ) ) );
            return intval( $data['id'] );
        } else {
            $wpdb->insert( $table, $division_data );
            return $wpdb->insert_id;
        }
    }
    
    /**
     * Save brand
     */
    public static function save_brand( $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'brand_items';
        
        $brand_data = array(
            'name' => sanitize_text_field( $data['name'] ),
            'color' => sanitize_hex_color( $data['color'] ),
            'division_id' => intval( $data['division_id'] ),
            'description' => sanitize_textarea_field( $data['description'] ?? '' ),
            'website' => esc_url_raw( $data['website'] ?? '' ),
            'logo_url' => esc_url_raw( $data['logo_url'] ?? '' ),
            'display_order' => intval( $data['display_order'] ?? 0 )
        );
        
        if ( ! empty( $data['id'] ) ) {
            $wpdb->update( $table, $brand_data, array( 'id' => intval( $data['id'] ) ) );
            return intval( $data['id'] );
        } else {
            $wpdb->insert( $table, $brand_data );
            return $wpdb->insert_id;
        }
    }
    
    /**
     * Delete division
     */
    public static function delete_division( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'brand_divisions';
        return $wpdb->delete( $table, array( 'id' => intval( $id ) ) );
    }
    
    /**
     * Delete brand
     */
    public static function delete_brand( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'brand_items';
        return $wpdb->delete( $table, array( 'id' => intval( $id ) ) );
    }
}

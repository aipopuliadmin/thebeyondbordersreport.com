<?php
/**
 * Register custom post types and taxonomies.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Post_Types {

    /**
     * Register custom post types.
     */
    public function register_post_types() {
        // Featured Articles post type removed
    }

    /**
     * Register custom taxonomies.
     */
    public function register_taxonomies() {
        
        // Article Region Taxonomy
        $labels = array(
            'name'                       => _x( 'Regions', 'Taxonomy General Name', 'beyond-borders' ),
            'singular_name'              => _x( 'Region', 'Taxonomy Singular Name', 'beyond-borders' ),
            'menu_name'                  => __( 'Regions', 'beyond-borders' ),
            'all_items'                  => __( 'All Regions', 'beyond-borders' ),
            'new_item_name'              => __( 'New Region Name', 'beyond-borders' ),
            'add_new_item'               => __( 'Add New Region', 'beyond-borders' ),
            'edit_item'                  => __( 'Edit Region', 'beyond-borders' ),
            'update_item'                => __( 'Update Region', 'beyond-borders' ),
            'view_item'                  => __( 'View Region', 'beyond-borders' ),
            'search_items'               => __( 'Search Regions', 'beyond-borders' ),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        );

        register_taxonomy( 'region', array( 'post' ), $args );
    }
}

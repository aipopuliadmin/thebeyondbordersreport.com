<?php
/**
 * Beyond Borders Custom Theme Functions
 *
 * @package Beond_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Define Constants
 */
define( 'BEOND_VERSION', '1.0.2' );
define( 'BEOND_THEME_DIR', get_template_directory() );
define( 'BEOND_THEME_URI', get_template_directory_uri() );

/**
 * Load Custom Widgets
 */
require_once BEOND_THEME_DIR . '/inc/class-numbered-recent-posts-widget.php';
require_once BEOND_THEME_DIR . '/inc/class-numbered-categories-widget.php';
require_once BEOND_THEME_DIR . '/inc/class-numbered-archives-widget.php';

/**
 * Load Mobile Accordion Walker
 */
require_once BEOND_THEME_DIR . '/inc/class-mobile-accordion-walker.php';

/**
 * Load Desktop Navigation Walker
 */
require_once BEOND_THEME_DIR . '/inc/class-desktop-nav-walker.php';

/**
 * Load AEO/SEO/GEO Optimization Classes
 */
require_once BEOND_THEME_DIR . '/inc/class-faq-schema.php';
require_once BEOND_THEME_DIR . '/inc/class-howto-schema.php';
require_once BEOND_THEME_DIR . '/inc/class-content-enhancements.php';

/**
 * Theme Setup
 */
function beond_theme_setup() {
    
    // Make theme available for translation
    load_theme_textdomain( 'beond-custom', BEOND_THEME_DIR . '/languages' );
    
    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );
    
    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );
    
    // Enable support for Post Thumbnails
    add_theme_support( 'post-thumbnails' );
    
    // Add custom image sizes
    add_image_size( 'hero', 1400, 700, true );          // Hero images
    add_image_size( 'featured', 800, 600, true );       // Featured posts
    add_image_size( 'card', 600, 400, true );           // Article cards
    add_image_size( 'thumb', 400, 300, true );          // Thumbnails
    add_image_size( 'beond-hero', 1400, 600, true );    // Legacy support
    add_image_size( 'beond-featured', 800, 600, true ); // Legacy support
    add_image_size( 'beond-card', 600, 400, true );     // Legacy support
    add_image_size( 'beond-thumb', 400, 300, true );    // Legacy support
    
    // Register navigation menus
    register_nav_menus( array(
        'primary'   => esc_html__( 'Primary Menu', 'beond-custom' ),
        'secondary' => esc_html__( 'Secondary Menu', 'beond-custom' ),
        'footer'    => esc_html__( 'Footer Menu', 'beond-custom' ),
    ) );
    
    // Switch default core markup to output valid HTML5
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );
    
    // Add theme support for selective refresh for widgets
    add_theme_support( 'customize-selective-refresh-widgets' );
    
    // Add support for custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-width'  => true,
        'flex-height' => true,
    ) );
    
    // Add support for editor styles
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor-style.css' );
    
    // Add support for wide and full alignment
    add_theme_support( 'align-wide' );
    
    // Add support for responsive embedded content
    add_theme_support( 'responsive-embeds' );
    
    // Add support for custom background
    add_theme_support( 'custom-background', array(
        'default-color' => 'ffffff',
    ) );
    
    // Set content width
    if ( ! isset( $content_width ) ) {
        $content_width = 720;
    }
}
add_action( 'after_setup_theme', 'beond_theme_setup' );

/**
 * Register Widget Areas
 */
function beond_widgets_init() {
    
    // Main Sidebar
    register_sidebar( array(
        'name'          => esc_html__( 'Main Sidebar', 'beond-custom' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'beond-custom' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
    
    // Footer widget areas
    $footer_columns = get_option( 'beyond_borders_footer_settings', array() );
    $columns = $footer_columns['footer_columns'] ?? 4;
    
    for ( $i = 1; $i <= $columns; $i++ ) {
        register_sidebar( array(
            'name'          => sprintf( esc_html__( 'Footer Column %d', 'beond-custom' ), $i ),
            'id'            => 'footer-' . $i,
            'description'   => sprintf( esc_html__( 'Add widgets here to appear in footer column %d.', 'beond-custom' ), $i ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ) );
    }
}
add_action( 'widgets_init', 'beond_widgets_init' );

/**
 * Ensure authors can access categories in Block Editor
 */
function beond_enable_categories_for_authors() {
    // Register taxonomies to post type (in case they were unregistered)
    register_taxonomy_for_object_type( 'category', 'post' );
    register_taxonomy_for_object_type( 'post_tag', 'post' );
    
    // Ensure author role has taxonomy capabilities
    $author_role = get_role( 'author' );
    if ( $author_role ) {
        $author_role->add_cap( 'manage_categories' );
        $author_role->add_cap( 'assign_categories' );
        $author_role->add_cap( 'assign_post_tags' );
    }
}
add_action( 'init', 'beond_enable_categories_for_authors', 0 ); // Priority 0 - run EARLY

/**
 * Force refresh post type object to include taxonomies
 */
function beond_force_post_taxonomies() {
    global $wp_post_types;
    
    // Force WordPress to recognize category and tag taxonomies for posts
    if ( isset( $wp_post_types['post'] ) ) {
        $wp_post_types['post']->taxonomies = get_object_taxonomies( 'post', 'names' );
    }
}
add_action( 'init', 'beond_force_post_taxonomies', 1000 ); // Priority 1000 - run LATE

/**
 * Make categories visible in Block Editor for all users
 */
function beond_rest_prepare_taxonomy( $response, $taxonomy, $request ) {
    $context = ! empty( $request['context'] ) ? $request['context'] : 'view';
    
    // Ensure categories and tags are visible in block editor
    if ( $context === 'edit' && in_array( $taxonomy->name, array( 'category', 'post_tag' ) ) ) {
        $data = $response->get_data();
        $data['visibility']['show_ui'] = true;
        $response->set_data( $data );
    }
    
    return $response;
}
add_filter( 'rest_prepare_taxonomy', 'beond_rest_prepare_taxonomy', 10, 3 );

/**
 * Enqueue scripts for Block Editor
 */
function beond_enqueue_block_editor_assets() {
    // Enable categories panel
    wp_enqueue_script(
        'beond-enable-categories',
        BEOND_THEME_URI . '/assets/js/enable-categories-panel.js',
        array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-data' ),
        BEOND_VERSION,
        true
    );
    
    // Curated Review content template - WORKING VERSION
    wp_enqueue_script(
        'beond-curated-review-template',
        BEOND_THEME_URI . '/assets/js/curated-review-template-working.js',
        array( 'wp-blocks', 'wp-dom-ready', 'wp-data' ),
        time(), // Force reload
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'beond_enqueue_block_editor_assets' );

/**
 * Enqueue Scripts and Styles
 */
function beond_scripts() {
    
    // Google Fonts - Playfair Display, Lora, Inter
    wp_enqueue_style( 
        'beond-google-fonts', 
        'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Inter:wght@300;400;500;600;700&display=swap', 
        array(), 
        null 
    );
    
    // Main stylesheet
    wp_enqueue_style( 
        'beond-style', 
        get_stylesheet_uri(), 
        array(), 
        BEOND_VERSION 
    );
    
    // Main CSS
    wp_enqueue_style( 
        'beond-main', 
        BEOND_THEME_URI . '/assets/css/main.css', 
        array( 'beond-style' ), 
        BEOND_VERSION 
    );
    
    // Header CSS - Modern Header
    $header_settings = get_option( 'beyond_borders_header_settings', array() );
    $header_layout = $header_settings['header_layout'] ?? 'default';
    
    wp_enqueue_style( 
        'beond-header-modern', 
        BEOND_THEME_URI . '/assets/css/header/header-modern.css', 
        array( 'beond-main' ), 
        BEOND_VERSION 
    );
    
    // Homepage CSS
    if ( is_front_page() ) {
        wp_enqueue_style( 
            'beond-homepage', 
            BEOND_THEME_URI . '/assets/css/homepage.css', 
            array( 'beond-main' ), 
            BEOND_VERSION 
        );
    }
    
    // Single Post CSS
    if ( is_single() ) {
        wp_enqueue_style( 
            'beond-single-post', 
            BEOND_THEME_URI . '/assets/css/single-post.css', 
            array( 'beond-main' ), 
            BEOND_VERSION 
        );
        
        wp_enqueue_style( 
            'beond-author-card', 
            BEOND_THEME_URI . '/assets/css/author-card.css', 
            array( 'beond-single-post' ), 
            BEOND_VERSION 
        );
    }
    
    // Archive CSS
    if ( is_archive() || is_home() ) {
        wp_enqueue_style( 
            'beond-archive', 
            BEOND_THEME_URI . '/assets/css/archive.css', 
            array( 'beond-main' ), 
            BEOND_VERSION 
        );
    }
    
    // Footer CSS - Default Footer
    $footer_settings = get_option( 'beyond_borders_footer_settings', array() );
    $footer_layout = $footer_settings['footer_layout'] ?? 'default';
    
    wp_enqueue_style( 
        'beond-footer-' . $footer_layout, 
        BEOND_THEME_URI . '/assets/css/footer/footer-' . $footer_layout . '.css', 
        array( 'beond-main' ), 
        BEOND_VERSION 
    );
    
    // Search Page CSS
    if ( is_search() ) {
        wp_enqueue_style( 
            'beond-search', 
            BEOND_THEME_URI . '/assets/css/search.css', 
            array( 'beond-main' ), 
            BEOND_VERSION 
        );
    }
    
    // Key Points CSS
    if ( is_singular( 'post' ) ) {
        wp_enqueue_style( 
            'beond-key-points', 
            BEOND_THEME_URI . '/assets/css/key-points.css', 
            array( 'beond-main' ), 
            BEOND_VERSION 
        );
    }
    
    // About Page CSS
    if ( is_page_template( 'page-about.php' ) ) {
        wp_enqueue_style( 
            'beond-about', 
            BEOND_THEME_URI . '/assets/css/about.css', 
            array( 'beond-main' ), 
            BEOND_VERSION 
        );
    }
    
    // Contact Page CSS
    if ( is_page_template( 'page-contact.php' ) ) {
        wp_enqueue_style( 
            'beond-contact', 
            BEOND_THEME_URI . '/assets/css/contact.css', 
            array( 'beond-main' ), 
            BEOND_VERSION 
        );
    }
    
    // Numbered Recent Posts Widget CSS
    wp_enqueue_style( 
        'beond-numbered-recent-posts', 
        BEOND_THEME_URI . '/assets/css/numbered-recent-posts.css', 
        array( 'beond-main' ), 
        BEOND_VERSION 
    );
    
    // AEO/SEO/GEO Optimization Styles
    wp_enqueue_style( 
        'beond-aeo-styles', 
        BEOND_THEME_URI . '/assets/css/aeo-styles.css', 
        array( 'beond-main' ), 
        BEOND_VERSION 
    );
    
    // Pax Data & Traffic Trends Page
    if ( is_page_template( 'page-pax-data-traffic-trends.php' ) ) {
        wp_enqueue_style( 
            'beond-pax-data-traffic-trends', 
            BEOND_THEME_URI . '/assets/css/pax-data-traffic-trends.css', 
            array( 'beond-main' ), 
            BEOND_VERSION 
        );
    }
    
    // Brand Portfolio Index Page
    if ( is_page_template( 'page-brand-portfolio-index.php' ) ) {
        wp_enqueue_style( 
            'beond-brand-portfolio-index', 
            BEOND_THEME_URI . '/assets/css/brand-portfolio-index.css', 
            array( 'beond-main' ), 
            BEOND_VERSION 
        );
    }
    
    // Main JavaScript
    wp_enqueue_script( 
        'beond-script', 
        BEOND_THEME_URI . '/assets/js/main.js', 
        array( 'jquery' ), 
        BEOND_VERSION, 
        true 
    );
    
    // Load More Categories JavaScript
    wp_enqueue_script( 
        'beond-load-more-categories', 
        BEOND_THEME_URI . '/assets/js/load-more-categories.js', 
        array( 'jquery' ), 
        BEOND_VERSION, 
        true 
    );
    
    // Header JavaScript - Modern Header
    wp_enqueue_script( 
        'beond-header-modern', 
        BEOND_THEME_URI . '/assets/js/header/header-modern.js', 
        array(), 
        BEOND_VERSION, 
        true 
    );
    
    // Newsletter JavaScript
    wp_enqueue_script( 
        'beond-newsletter', 
        BEOND_THEME_URI . '/assets/js/newsletter.js', 
        array( 'jquery' ), 
        BEOND_VERSION, 
        true 
    );
    
    // Pass header settings to JavaScript
    $header_settings = get_option( 'beyond_borders_header_settings', array() );
    wp_localize_script( 'beond-header-modern', 'beondHeaderSettings', array(
        'defaultTheme' => $header_settings['default_theme'] ?? 'light',
    ) );
    
    // Localize script for newsletter
    wp_localize_script( 'beond-newsletter', 'beyondBordersNewsletter', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'newsletter_subscription' ),
    ) );
    
    // Comment reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
    
    // Localize script for main.js
    wp_localize_script( 'beond-script', 'beondVars', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'beond-nonce' ),
    ) );
    
    // Localize script for load-more-categories.js
    wp_localize_script( 'beond-load-more-categories', 'beondVars', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'beond-nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'beond_scripts' );

/**
 * Custom Excerpt Length
 */
function beond_excerpt_length( $length ) {
    $settings = get_option( 'beyond_borders_editorial_settings', array() );
    return $settings['excerpt_length'] ?? 55;
}
add_filter( 'excerpt_length', 'beond_excerpt_length', 999 );

/**
 * Custom Excerpt More
 */
function beond_excerpt_more( $more ) {
    return '... <a class="read-more" href="' . get_permalink() . '">' . esc_html__( 'Read More', 'beond-custom' ) . '</a>';
}
add_filter( 'excerpt_more', 'beond_excerpt_more' );

/**
 * Modify search query based on filters
 */
function beond_modify_search_query( $query ) {
    // Only modify main search query on frontend
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        
        // Set posts per page for search results
        $query->set( 'posts_per_page', 12 );
        
        // Get filter parameters
        $search_in = isset($_GET['search_in']) ? sanitize_text_field($_GET['search_in']) : 'both';
        $order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
        $date_filter = isset($_GET['date_filter']) ? sanitize_text_field($_GET['date_filter']) : 'all';
        
        // Apply date filter
        if ($date_filter === 'week') {
            $query->set('date_query', array(array('after' => '1 week ago')));
        } elseif ($date_filter === 'month') {
            $query->set('date_query', array(array('after' => '1 month ago')));
        } elseif ($date_filter === 'year') {
            $query->set('date_query', array(array('after' => '1 year ago')));
        }
        
        // Apply order
        if ($order_by === 'title') {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
        } elseif ($order_by === 'oldest') {
            $query->set('orderby', 'date');
            $query->set('order', 'ASC');
        }
        
        // Note: search_in filtering for title/content needs custom SQL modification
        // which is more complex - keeping default search behavior for now
    }
}
add_action( 'pre_get_posts', 'beond_modify_search_query' );

/**
 * Add body classes
 */
function beond_body_classes( $classes ) {
    
    // Add class if sidebar is active
    if ( is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'has-sidebar';
    }
    
    // Add class for sticky header
    $header_settings = get_option( 'beyond_borders_header_settings', array() );
    if ( ! empty( $header_settings['enable_sticky_header'] ) ) {
        $classes[] = 'sticky-header';
    }
    
    // Explicit class for Curated Review single posts (ID 24 + children 25–30)
    if ( is_single() && in_category( array( 24, 25, 26, 27, 28, 29, 30 ) ) ) {
        $classes[] = 'single-curated-review';
    }
    
    return $classes;
}
add_filter( 'body_class', 'beond_body_classes' );

/**
 * Custom template tags
 */
require BEOND_THEME_DIR . '/inc/template-tags.php';

/**
 * Customizer additions
 */
require BEOND_THEME_DIR . '/inc/customizer.php';

/**
 * Theme hooks
 */
require BEOND_THEME_DIR . '/inc/hooks.php';

/**
 * Helper functions
 */
require BEOND_THEME_DIR . '/inc/helpers.php';

/**
 * Author Type System
 * ===================
 * Handles author types, badges, profiles, and SEO schema
 */

/**
 * Add custom user meta fields for author types
 */
function beond_add_author_meta_fields( $user ) {
    ?>
    <h3><?php _e('Author Profile Settings', 'beond-custom'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="author_type"><?php _e('Author Type', 'beond-custom'); ?></label></th>
            <td>
                <select name="author_type" id="author_type">
                    <option value="bb_desk" <?php selected( get_user_meta( $user->ID, 'author_type', true ), 'bb_desk' ); ?>>
                        <?php _e('BB Desk (Staff Writer)', 'beond-custom'); ?>
                    </option>
                    <option value="guest_author" <?php selected( get_user_meta( $user->ID, 'author_type', true ), 'guest_author' ); ?>>
                        <?php _e('Guest Author', 'beond-custom'); ?>
                    </option>
                    <option value="podcast_guest" <?php selected( get_user_meta( $user->ID, 'author_type', true ), 'podcast_guest' ); ?>>
                        <?php _e('Podcast Guest', 'beond-custom'); ?>
                    </option>
                </select>
                <p class="description"><?php _e('Select the type of author for proper display and SEO.', 'beond-custom'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="author_job_title"><?php _e('Job Title / Position', 'beond-custom'); ?></label></th>
            <td>
                <input type="text" name="author_job_title" id="author_job_title" 
                       value="<?php echo esc_attr( get_user_meta( $user->ID, 'author_job_title', true ) ); ?>" 
                       class="regular-text" />
                <p class="description"><?php _e('E.g., "Editor-in-Chief", "Travel Retail Analyst", "CEO at Company Name"', 'beond-custom'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="author_company"><?php _e('Company / Organization', 'beond-custom'); ?></label></th>
            <td>
                <input type="text" name="author_company" id="author_company" 
                       value="<?php echo esc_attr( get_user_meta( $user->ID, 'author_company', true ) ); ?>" 
                       class="regular-text" />
                <p class="description"><?php _e('For guest authors and podcast guests.', 'beond-custom'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="author_linkedin"><?php _e('LinkedIn URL', 'beond-custom'); ?></label></th>
            <td>
                <input type="url" name="author_linkedin" id="author_linkedin" 
                       value="<?php echo esc_attr( get_user_meta( $user->ID, 'author_linkedin', true ) ); ?>" 
                       class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="author_twitter"><?php _e('Twitter/X URL', 'beond-custom'); ?></label></th>
            <td>
                <input type="url" name="author_twitter" id="author_twitter" 
                       value="<?php echo esc_attr( get_user_meta( $user->ID, 'author_twitter', true ) ); ?>" 
                       class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="author_podcast_episode"><?php _e('Podcast Episode Number', 'beond-custom'); ?></label></th>
            <td>
                <input type="text" name="author_podcast_episode" id="author_podcast_episode" 
                       value="<?php echo esc_attr( get_user_meta( $user->ID, 'author_podcast_episode', true ) ); ?>" 
                       class="regular-text" />
                <p class="description"><?php _e('For podcast guests - e.g., "Episode 24"', 'beond-custom'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'beond_add_author_meta_fields' );
add_action( 'edit_user_profile', 'beond_add_author_meta_fields' );

/**
 * Save custom user meta fields
 */
function beond_save_author_meta_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    
    update_user_meta( $user_id, 'author_type', sanitize_text_field( $_POST['author_type'] ?? 'bb_desk' ) );
    update_user_meta( $user_id, 'author_job_title', sanitize_text_field( $_POST['author_job_title'] ?? '' ) );
    update_user_meta( $user_id, 'author_company', sanitize_text_field( $_POST['author_company'] ?? '' ) );
    update_user_meta( $user_id, 'author_linkedin', esc_url_raw( $_POST['author_linkedin'] ?? '' ) );
    update_user_meta( $user_id, 'author_twitter', esc_url_raw( $_POST['author_twitter'] ?? '' ) );
    update_user_meta( $user_id, 'author_podcast_episode', sanitize_text_field( $_POST['author_podcast_episode'] ?? '' ) );
}
add_action( 'personal_options_update', 'beond_save_author_meta_fields' );
add_action( 'edit_user_profile_update', 'beond_save_author_meta_fields' );

/**
 * Add meta box for author display control on posts
 */
function beond_add_author_display_meta_box() {
    add_meta_box(
        'beond_author_display',
        __( 'Author Display Settings', 'beond-custom' ),
        'beond_author_display_meta_box_callback',
        'post',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'beond_add_author_display_meta_box' );

/**
 * Meta box callback for author display control
 */
function beond_author_display_meta_box_callback( $post ) {
    wp_nonce_field( 'beond_author_display_nonce', 'beond_author_display_nonce' );
    $show_author = get_post_meta( $post->ID, '_beond_show_author', true );
    $auto_detect = get_post_meta( $post->ID, '_beond_author_auto_detect', true );
    
    // Default to auto-detect if not set
    if ( $auto_detect === '' ) {
        $auto_detect = '1';
    }
    ?>
    <p>
        <label>
            <input type="checkbox" name="beond_author_auto_detect" value="1" <?php checked( $auto_detect, '1' ); ?> />
            <?php _e( 'Auto-detect based on category', 'beond-custom' ); ?>
        </label>
    </p>
    <p class="description" style="margin-top: -8px; margin-bottom: 12px;">
        <?php _e( 'Opinion/Analysis/Interviews = show author<br>News = hide author', 'beond-custom' ); ?>
    </p>
    <p>
        <label>
            <input type="checkbox" name="beond_show_author" value="1" <?php checked( $show_author, '1' ); ?> />
            <?php _e( 'Force show author card', 'beond-custom' ); ?>
        </label>
    </p>
    <p class="description" style="margin-top: -8px;">
        <?php _e( 'Override auto-detection', 'beond-custom' ); ?>
    </p>
    <?php
}

/**
 * Save author display meta box data
 */
function beond_save_author_display_meta_box( $post_id ) {
    if ( ! isset( $_POST['beond_author_display_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['beond_author_display_nonce'], 'beond_author_display_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    $auto_detect = isset( $_POST['beond_author_auto_detect'] ) ? '1' : '0';
    $show_author = isset( $_POST['beond_show_author'] ) ? '1' : '0';
    
    update_post_meta( $post_id, '_beond_author_auto_detect', $auto_detect );
    update_post_meta( $post_id, '_beond_show_author', $show_author );
}
add_action( 'save_post', 'beond_save_author_display_meta_box' );

/**
 * Check if author should be displayed for a post
 */
function beond_should_show_author( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $auto_detect = get_post_meta( $post_id, '_beond_author_auto_detect', true );
    $force_show = get_post_meta( $post_id, '_beond_show_author', true );
    
    // If forced to show, always show
    if ( $force_show === '1' ) {
        return true;
    }
    
    // If auto-detect is disabled, don't show
    if ( $auto_detect === '0' ) {
        return false;
    }
    
    // Auto-detect based on category
    $categories = get_the_category( $post_id );
    if ( empty( $categories ) ) {
        return false;
    }
    
    // Categories that should show author
    $author_categories = array(
        'analysis-opinion',
        'analysis',
        'opinion',
        'voices-interviews',
        'voices',
        'interviews',
        'podcast',
        'curated-reviews',
        'reviews',
        'editorials',
        'columns',
        'guest-perspectives'
    );
    
    foreach ( $categories as $category ) {
        if ( in_array( $category->slug, $author_categories ) ) {
            return true;
        }
    }
    
    return false;
}

/**
 * Get author type badge HTML
 */
function beond_get_author_badge( $author_id = null ) {
    if ( ! $author_id ) {
        $author_id = get_the_author_meta( 'ID' );
    }
    
    $author_type = get_user_meta( $author_id, 'author_type', true );
    
    if ( ! $author_type ) {
        $author_type = 'bb_desk'; // Default
    }
    
    $badges = array(
        'bb_desk' => array(
            'label' => __( 'BB DESK', 'beond-custom' ),
            'class' => 'author-badge-bb-desk'
        ),
        'guest_author' => array(
            'label' => __( 'GUEST AUTHOR', 'beond-custom' ),
            'class' => 'author-badge-guest'
        ),
        'podcast_guest' => array(
            'label' => __( 'PODCAST GUEST', 'beond-custom' ),
            'class' => 'author-badge-podcast'
        ),
    );
    
    $badge = $badges[ $author_type ] ?? $badges['bb_desk'];
    
    return sprintf(
        '<span class="author-type-badge %s">%s</span>',
        esc_attr( $badge['class'] ),
        esc_html( $badge['label'] )
    );
}

/**
 * Output Schema.org Person/Article markup for SEO
 */
function beond_output_author_schema() {
    if ( ! is_single() || ! beond_should_show_author() ) {
        return;
    }
    
    $author_id = get_the_author_meta( 'ID' );
    $author_type = get_user_meta( $author_id, 'author_type', true ) ?: 'bb_desk';
    $job_title = get_user_meta( $author_id, 'author_job_title', true );
    $company = get_user_meta( $author_id, 'author_company', true );
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title(),
        'datePublished' => get_the_date( 'c' ),
        'dateModified' => get_the_modified_date( 'c' ),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author(),
            'url' => get_author_posts_url( $author_id ),
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => get_bloginfo( 'name' ),
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => get_site_icon_url(),
            ),
        ),
    );
    
    // Add job title if available
    if ( $job_title ) {
        $schema['author']['jobTitle'] = $job_title;
    }
    
    // Add organization/company for guest authors
    if ( $company && $author_type !== 'bb_desk' ) {
        $schema['author']['worksFor'] = array(
            '@type' => 'Organization',
            'name' => $company,
        );
    }
    
    // Add featured image if available
    if ( has_post_thumbnail() ) {
        $schema['image'] = get_the_post_thumbnail_url( null, 'full' );
    }
    
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'beond_output_author_schema' );

/**
 * ============================================
 * COMPREHENSIVE SEO META TAGS
 * ============================================
 */

/**
 * Add Open Graph and Twitter Card meta tags
 */
function beond_add_seo_meta_tags() {
    if ( is_singular() ) {
        global $post;
        
        // Get post data
        $title = get_the_title();
        $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( strip_shortcodes( $post->post_content ), 30 );
        $url = get_permalink();
        $site_name = get_bloginfo( 'name' );
        
        // Get featured image
        $image = has_post_thumbnail() ? get_the_post_thumbnail_url( $post, 'full' ) : get_site_icon_url( 512 );
        
        // Open Graph tags
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $excerpt ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
        
        if ( $image ) {
            echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
            echo '<meta property="og:image:width" content="1200" />' . "\n";
            echo '<meta property="og:image:height" content="630" />' . "\n";
        }
        
        // Article specific tags
        echo '<meta property="article:published_time" content="' . get_the_date( 'c' ) . '" />' . "\n";
        echo '<meta property="article:modified_time" content="' . get_the_modified_date( 'c' ) . '" />' . "\n";
        echo '<meta property="article:author" content="' . esc_attr( get_the_author() ) . '" />' . "\n";
        
        // Twitter Card tags
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $excerpt ) . '" />' . "\n";
        
        if ( $image ) {
            echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
        }
        
        // Add meta description
        echo '<meta name="description" content="' . esc_attr( $excerpt ) . '" />' . "\n";
        
    } elseif ( is_home() || is_front_page() ) {
        $site_name = get_bloginfo( 'name' );
        $site_description = get_bloginfo( 'description' );
        $url = home_url( '/' );
        $image = get_site_icon_url( 512 );
        
        // Open Graph tags
        echo '<meta property="og:type" content="website" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $site_name ) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $site_description ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
        
        if ( $image ) {
            echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
        }
        
        // Twitter Card tags
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $site_name ) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $site_description ) . '" />' . "\n";
        
        if ( $image ) {
            echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
        }
        
        // Add meta description
        echo '<meta name="description" content="' . esc_attr( $site_description ) . '" />' . "\n";
    }
}
add_action( 'wp_head', 'beond_add_seo_meta_tags', 1 );

/**
 * Add Organization Schema for homepage
 */
function beond_add_organization_schema() {
    if ( is_home() || is_front_page() ) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_bloginfo( 'name' ),
            'url' => home_url( '/' ),
            'logo' => get_site_icon_url( 512 ),
            'description' => get_bloginfo( 'description' ),
            'sameAs' => array(
                // Add your social media profiles here
            ),
        );
        
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'beond_add_organization_schema', 2 );

/**
 * Add WebSite Schema for search functionality
 */
function beond_add_website_schema() {
    if ( is_home() || is_front_page() ) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo( 'name' ),
            'url' => home_url( '/' ),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => home_url( '/' ) . '?s={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ),
        );
        
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'beond_add_website_schema', 3 );

/**
 * ============================================
 * KEY POINTS FEATURE FOR AEO/SEO
 * ============================================
 */

/**
 * Add Key Points Meta Box
 */
function beond_add_key_points_meta_box() {
    add_meta_box(
        'beond_key_points',
        __( 'Key Points Summary', 'beond-custom' ),
        'beond_key_points_meta_box_callback',
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'beond_add_key_points_meta_box' );

/**
 * Key Points Meta Box Callback
 */
function beond_key_points_meta_box_callback( $post ) {
    wp_nonce_field( 'beond_save_key_points', 'beond_key_points_nonce' );
    
    $key_points = get_post_meta( $post->ID, '_beond_key_points', true );
    $show_key_points = get_post_meta( $post->ID, '_beond_show_key_points', true );
    
    // Default to checked for new posts
    if ( $post->post_status === 'auto-draft' || empty( $show_key_points ) ) {
        $show_key_points = '1';
    }
    
    if ( ! is_array( $key_points ) ) {
        $key_points = array( '', '', '' );
    }
    
    // Ensure we have at least 3 empty points
    while ( count( $key_points ) < 3 ) {
        $key_points[] = '';
    }
    ?>
    
    <div class="beond-key-points-meta">
        <p class="description" style="margin-bottom: 15px;">
            <?php _e( 'Add 3-5 key takeaways from this article. These will be displayed prominently and improve SEO/AEO visibility.', 'beond-custom' ); ?>
        </p>
        
        <div id="beond-key-points-list">
            <?php foreach ( $key_points as $index => $point ) : ?>
                <div class="beond-key-point-item" style="margin-bottom: 12px; display: flex; gap: 10px; align-items: start;">
                    <span style="font-weight: 600; color: #003265; min-width: 20px;">•</span>
                    <textarea 
                        name="beond_key_points[]" 
                        rows="2" 
                        style="width: 100%; padding: 8px;"
                        placeholder="<?php esc_attr_e( 'Enter key point...', 'beond-custom' ); ?>"
                    ><?php echo esc_textarea( $point ); ?></textarea>
                    <?php if ( $index >= 3 ) : ?>
                        <button type="button" class="button beond-remove-point" style="flex-shrink: 0;">
                            <?php _e( 'Remove', 'beond-custom' ); ?>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <p style="margin-top: 15px;">
            <button type="button" id="beond-add-point" class="button">
                <?php _e( '+ Add Another Point', 'beond-custom' ); ?>
            </button>
        </p>
        
        <hr style="margin: 20px 0;">
        
        <p>
            <label>
                <input type="checkbox" name="beond_show_key_points" value="1" <?php checked( $show_key_points, '1' ); ?>>
                <?php _e( 'Display Key Points on article page', 'beond-custom' ); ?>
            </label>
        </p>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Add new point
        $('#beond-add-point').on('click', function() {
            var html = '<div class="beond-key-point-item" style="margin-bottom: 12px; display: flex; gap: 10px; align-items: start;">' +
                '<span style="font-weight: 600; color: #003265; min-width: 20px;">•</span>' +
                '<textarea name="beond_key_points[]" rows="2" style="width: 100%; padding: 8px;" placeholder="<?php esc_attr_e( 'Enter key point...', 'beond-custom' ); ?>"></textarea>' +
                '<button type="button" class="button beond-remove-point" style="flex-shrink: 0;"><?php _e( 'Remove', 'beond-custom' ); ?></button>' +
                '</div>';
            $('#beond-key-points-list').append(html);
        });
        
        // Remove point
        $(document).on('click', '.beond-remove-point', function() {
            $(this).closest('.beond-key-point-item').remove();
        });
    });
    </script>
    
    <style>
    .beond-key-points-meta textarea {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .beond-key-points-meta textarea:focus {
        border-color: #003265;
        outline: none;
        box-shadow: 0 0 0 1px #003265;
    }
    </style>
    
    <?php
}

/**
 * Save Key Points Meta
 */
function beond_save_key_points_meta( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['beond_key_points_nonce'] ) || 
         ! wp_verify_nonce( $_POST['beond_key_points_nonce'], 'beond_save_key_points' ) ) {
        return;
    }
    
    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Save key points
    if ( isset( $_POST['beond_key_points'] ) ) {
        $key_points = array_map( 'sanitize_textarea_field', $_POST['beond_key_points'] );
        // Remove empty points
        $key_points = array_filter( $key_points, function( $point ) {
            return ! empty( trim( $point ) );
        });
        // Reindex array
        $key_points = array_values( $key_points );
        
        update_post_meta( $post_id, '_beond_key_points', $key_points );
    } else {
        delete_post_meta( $post_id, '_beond_key_points' );
    }
    
    // Save show/hide setting
    if ( isset( $_POST['beond_show_key_points'] ) ) {
        update_post_meta( $post_id, '_beond_show_key_points', '1' );
    } else {
        update_post_meta( $post_id, '_beond_show_key_points', '0' );
    }
}
add_action( 'save_post', 'beond_save_key_points_meta' );

/**
 * Get Key Points for a post
 */
function beond_get_key_points( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $show = get_post_meta( $post_id, '_beond_show_key_points', true );
    if ( $show !== '1' ) {
        return false;
    }
    
    $points = get_post_meta( $post_id, '_beond_key_points', true );
    
    if ( ! is_array( $points ) || empty( $points ) ) {
        return false;
    }
    
    return $points;
}

/**
 * Output Key Points Schema for AEO
 */
function beond_output_key_points_schema() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }
    
    $key_points = beond_get_key_points();
    
    if ( ! $key_points ) {
        return;
    }
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'mainEntity' => array(
            '@type' => 'Article',
            'headline' => get_the_title(),
            'articleBody' => get_the_excerpt(),
            'about' => array(
                '@type' => 'ItemList',
                'itemListElement' => array(),
            ),
        ),
    );
    
    foreach ( $key_points as $index => $point ) {
        $schema['mainEntity']['about']['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $point,
        );
    }
    
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'beond_output_key_points_schema' );

/**
 * Add Contact Page Meta Boxes
 */
function beond_add_contact_page_meta_boxes() {
    $screen = get_current_screen();
    
    // Only add meta boxes if this is the contact page template
    if ( $screen && $screen->id === 'page' ) {
        $template = get_page_template_slug();
        if ( $template === 'page-contact.php' || ( isset( $_GET['post'] ) && get_page_template_slug( $_GET['post'] ) === 'page-contact.php' ) ) {
            add_meta_box(
                'beond_contact_hero',
                __( 'Hero Section', 'beond-custom' ),
                'beond_contact_hero_meta_box_callback',
                'page',
                'normal',
                'high'
            );
            
            add_meta_box(
                'beond_contact_info',
                __( 'Contact Information', 'beond-custom' ),
                'beond_contact_info_meta_box_callback',
                'page',
                'normal',
                'high'
            );
            
            add_meta_box(
                'beond_contact_social',
                __( 'Social Media Links', 'beond-custom' ),
                'beond_contact_social_meta_box_callback',
                'page',
                'normal',
                'high'
            );
            
            add_meta_box(
                'beond_contact_faq',
                __( 'FAQ Section', 'beond-custom' ),
                'beond_contact_faq_meta_box_callback',
                'page',
                'normal',
                'high'
            );
        }
    }
}
add_action( 'add_meta_boxes', 'beond_add_contact_page_meta_boxes' );

/**
 * Add About Page Meta Boxes
 */
function beond_add_about_page_meta_boxes() {
    $screen = get_current_screen();
    
    // Only add meta boxes if this is the about page template
    if ( $screen && $screen->id === 'page' ) {
        $template = get_page_template_slug();
        if ( $template === 'page-about.php' || ( isset( $_GET['post'] ) && get_page_template_slug( $_GET['post'] ) === 'page-about.php' ) ) {
            // Enqueue media scripts for the media library picker
            wp_enqueue_media();
            add_meta_box(
                'beond_about_hero',
                __( 'Hero Section', 'beond-custom' ),
                'beond_about_hero_meta_box_callback',
                'page',
                'normal',
                'high'
            );
            
            add_meta_box(
                'beond_about_stats',
                __( 'Hero Statistics', 'beond-custom' ),
                'beond_about_stats_meta_box_callback',
                'page',
                'normal',
                'high'
            );
            
            add_meta_box(
                'beond_about_mission',
                __( 'Mission Section', 'beond-custom' ),
                'beond_about_mission_meta_box_callback',
                'page',
                'normal',
                'default'
            );
            
            add_meta_box(
                'beond_about_story',
                __( 'Story Section', 'beond-custom' ),
                'beond_about_story_meta_box_callback',
                'page',
                'normal',
                'default'
            );
            
            add_meta_box(
                'beond_about_values',
                __( 'Values Section', 'beond-custom' ),
                'beond_about_values_meta_box_callback',
                'page',
                'normal',
                'default'
            );
            
            add_meta_box(
                'beond_about_timeline',
                __( 'Timeline Section', 'beond-custom' ),
                'beond_about_timeline_meta_box_callback',
                'page',
                'normal',
                'default'
            );
            
            add_meta_box(
                'beond_about_team',
                __( 'Team Section', 'beond-custom' ),
                'beond_about_team_meta_box_callback',
                'page',
                'normal',
                'default'
            );
        }
    }
}
add_action( 'add_meta_boxes', 'beond_add_about_page_meta_boxes' );

/**
 * Hero Section Meta Box Callback
 */
function beond_contact_hero_meta_box_callback( $post ) {
    wp_nonce_field( 'beond_contact_meta', 'beond_contact_meta_nonce' );
    
    $hero_label = get_post_meta( $post->ID, '_contact_hero_label', true );
    $hero_title = get_post_meta( $post->ID, '_contact_hero_title', true );
    $hero_subtitle = get_post_meta( $post->ID, '_contact_hero_subtitle', true );
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="contact_hero_label"><?php _e( 'Hero Label', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="contact_hero_label" name="contact_hero_label" value="<?php echo esc_attr( $hero_label ?: 'GET IN TOUCH' ); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Small label above the title (e.g., "GET IN TOUCH")', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_hero_title"><?php _e( 'Hero Title', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="contact_hero_title" name="contact_hero_title" value="<?php echo esc_attr( $hero_title ?: 'Contact Beyond Borders' ); ?>" class="large-text" />
                <p class="description"><?php _e( 'Main heading for the contact page', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_hero_subtitle"><?php _e( 'Hero Subtitle', 'beond-custom' ); ?></label>
            </th>
            <td>
                <textarea id="contact_hero_subtitle" name="contact_hero_subtitle" rows="3" class="large-text"><?php echo esc_textarea( $hero_subtitle ?: "Have a question, story idea, or just want to say hello? We'd love to hear from you. Our team is here to help and typically responds within 24 hours." ); ?></textarea>
                <p class="description"><?php _e( 'Description text below the title', 'beond-custom' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Contact Information Meta Box Callback
 */
function beond_contact_info_meta_box_callback( $post ) {
    $email = get_post_meta( $post->ID, '_contact_email', true );
    $press_email = get_post_meta( $post->ID, '_contact_press_email', true );
    $phone = get_post_meta( $post->ID, '_contact_phone', true );
    $phone_hours = get_post_meta( $post->ID, '_contact_phone_hours', true );
    $phone2 = get_post_meta( $post->ID, '_contact_phone2', true );
    $phone2_hours = get_post_meta( $post->ID, '_contact_phone2_hours', true );
    $address_line1 = get_post_meta( $post->ID, '_contact_address_line1', true );
    $address_line2 = get_post_meta( $post->ID, '_contact_address_line2', true );
    $address_note = get_post_meta( $post->ID, '_contact_address_note', true );
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="contact_email"><?php _e( 'Email Address', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr( $email ?: 'info@beyondborders.com' ); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Main contact email address', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_press_email"><?php _e( 'Press Email', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="email" id="contact_press_email" name="contact_press_email" value="<?php echo esc_attr( $press_email ?: 'press@beyondborders.com' ); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Press inquiries email address', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_phone"><?php _e( 'Phone Number 1', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="tel" id="contact_phone" name="contact_phone" value="<?php echo esc_attr( $phone ?: '+1 (555) 123-4567' ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_phone_hours"><?php _e( 'Phone 1 Hours', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="contact_phone_hours" name="contact_phone_hours" value="<?php echo esc_attr( $phone_hours ?: 'Mon-Fri, 9am-6pm EST' ); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Business hours for phone 1', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_phone2"><?php _e( 'Phone Number 2', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="tel" id="contact_phone2" name="contact_phone2" value="<?php echo esc_attr( $phone2 ); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Optional second phone number', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_phone2_hours"><?php _e( 'Phone 2 Hours', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="contact_phone2_hours" name="contact_phone2_hours" value="<?php echo esc_attr( $phone2_hours ); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Business hours for phone 2 (leave blank to hide)', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_address_line1"><?php _e( 'Address Line 1', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="contact_address_line1" name="contact_address_line1" value="<?php echo esc_attr( $address_line1 ?: '123 Global News Avenue' ); ?>" class="large-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_address_line2"><?php _e( 'Address Line 2', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="contact_address_line2" name="contact_address_line2" value="<?php echo esc_attr( $address_line2 ?: 'New York, NY 10001' ); ?>" class="large-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_address_note"><?php _e( 'Address Note', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="contact_address_note" name="contact_address_note" value="<?php echo esc_attr( $address_note ?: 'By appointment only' ); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Additional note about visiting (e.g., "By appointment only")', 'beond-custom' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Social Media Links Meta Box Callback
 */
function beond_contact_social_meta_box_callback( $post ) {
    $twitter = get_post_meta( $post->ID, '_contact_twitter', true );
    $facebook = get_post_meta( $post->ID, '_contact_facebook', true );
    $linkedin = get_post_meta( $post->ID, '_contact_linkedin', true );
    $instagram = get_post_meta( $post->ID, '_contact_instagram', true );
    $youtube = get_post_meta( $post->ID, '_contact_youtube', true );
    $tiktok = get_post_meta( $post->ID, '_contact_tiktok', true );
    $pinterest = get_post_meta( $post->ID, '_contact_pinterest', true );
    $whatsapp = get_post_meta( $post->ID, '_contact_whatsapp', true );
    $telegram = get_post_meta( $post->ID, '_contact_telegram', true );
    $snapchat = get_post_meta( $post->ID, '_contact_snapchat', true );
    $reddit = get_post_meta( $post->ID, '_contact_reddit', true );
    $github = get_post_meta( $post->ID, '_contact_github', true );
    ?>
    <p class="description" style="margin-bottom: 15px;"><?php _e( 'Enter full URLs for your social media profiles. Leave blank to hide from the page.', 'beond-custom' ); ?></p>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="contact_twitter"><?php _e( 'Twitter / X', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_twitter" name="contact_twitter" value="<?php echo esc_attr( $twitter ); ?>" class="large-text" placeholder="https://twitter.com/yourprofile" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_facebook"><?php _e( 'Facebook', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_facebook" name="contact_facebook" value="<?php echo esc_attr( $facebook ); ?>" class="large-text" placeholder="https://facebook.com/yourpage" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_instagram"><?php _e( 'Instagram', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_instagram" name="contact_instagram" value="<?php echo esc_attr( $instagram ); ?>" class="large-text" placeholder="https://instagram.com/yourprofile" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_linkedin"><?php _e( 'LinkedIn', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_linkedin" name="contact_linkedin" value="<?php echo esc_attr( $linkedin ); ?>" class="large-text" placeholder="https://linkedin.com/company/yourcompany" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_youtube"><?php _e( 'YouTube', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_youtube" name="contact_youtube" value="<?php echo esc_attr( $youtube ); ?>" class="large-text" placeholder="https://youtube.com/@yourchannel" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_tiktok"><?php _e( 'TikTok', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_tiktok" name="contact_tiktok" value="<?php echo esc_attr( $tiktok ); ?>" class="large-text" placeholder="https://tiktok.com/@yourprofile" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_pinterest"><?php _e( 'Pinterest', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_pinterest" name="contact_pinterest" value="<?php echo esc_attr( $pinterest ); ?>" class="large-text" placeholder="https://pinterest.com/yourprofile" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_whatsapp"><?php _e( 'WhatsApp', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_whatsapp" name="contact_whatsapp" value="<?php echo esc_attr( $whatsapp ); ?>" class="large-text" placeholder="https://wa.me/1234567890" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_telegram"><?php _e( 'Telegram', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_telegram" name="contact_telegram" value="<?php echo esc_attr( $telegram ); ?>" class="large-text" placeholder="https://t.me/yourprofile" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_snapchat"><?php _e( 'Snapchat', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_snapchat" name="contact_snapchat" value="<?php echo esc_attr( $snapchat ); ?>" class="large-text" placeholder="https://snapchat.com/add/yourprofile" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_reddit"><?php _e( 'Reddit', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_reddit" name="contact_reddit" value="<?php echo esc_attr( $reddit ); ?>" class="large-text" placeholder="https://reddit.com/u/yourprofile" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_github"><?php _e( 'GitHub', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="url" id="contact_github" name="contact_github" value="<?php echo esc_attr( $github ); ?>" class="large-text" placeholder="https://github.com/yourprofile" />
            </td>
        </tr>
    </table>
    <?php
}

/**
 * FAQ Section Meta Box Callback
 */
function beond_contact_faq_meta_box_callback( $post ) {
    $faq_label = get_post_meta( $post->ID, '_contact_faq_label', true );
    $faq_title = get_post_meta( $post->ID, '_contact_faq_title', true );
    $faqs = get_post_meta( $post->ID, '_contact_faqs', true );
    
    if ( ! is_array( $faqs ) || empty( $faqs ) ) {
        $faqs = array(
            array(
                'question' => 'How quickly will I receive a response?',
                'answer' => 'We typically respond to all inquiries within 24 hours during business days. For urgent press matters, please call our media hotline directly.'
            ),
            array(
                'question' => 'Can I submit a story idea?',
                'answer' => 'Absolutely! We welcome story submissions from our readers. Please use the "Story Submission" subject when contacting us and provide as much detail as possible.'
            ),
            array(
                'question' => 'Do you accept guest contributions?',
                'answer' => 'Yes, we occasionally publish guest articles from subject matter experts. Please select "Partnership Opportunity" and include writing samples with your inquiry.'
            ),
            array(
                'question' => 'How can I report an error in an article?',
                'answer' => 'We take accuracy seriously. Please email us with the article URL and details of the error. We review all corrections promptly and update articles as needed.'
            )
        );
    }
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="contact_faq_label"><?php _e( 'FAQ Label', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="contact_faq_label" name="contact_faq_label" value="<?php echo esc_attr( $faq_label ?: 'FAQ' ); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Small label above the title (e.g., "FAQ")', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="contact_faq_title"><?php _e( 'FAQ Title', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="contact_faq_title" name="contact_faq_title" value="<?php echo esc_attr( $faq_title ?: 'Frequently Asked Questions' ); ?>" class="large-text" />
            </td>
        </tr>
    </table>
    
    <div id="faq-items-container" style="margin-top: 20px;">
        <h4><?php _e( 'FAQ Items', 'beond-custom' ); ?></h4>
        <p class="description"><?php _e( 'Add or remove FAQ items. Drag to reorder.', 'beond-custom' ); ?></p>
        <div id="faq-items-list">
            <?php foreach ( $faqs as $index => $faq ) : ?>
                <div class="faq-item-row" style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-left: 4px solid #2271b1; position: relative;">
                    <button type="button" class="remove-faq-item button" style="position: absolute; top: 10px; right: 10px;"><?php _e( 'Remove', 'beond-custom' ); ?></button>
                    <p><strong><?php _e( 'Question', 'beond-custom' ); ?></strong></p>
                    <input type="text" name="contact_faq_questions[]" value="<?php echo esc_attr( $faq['question'] ); ?>" class="large-text" style="margin-bottom: 10px;" />
                    <p><strong><?php _e( 'Answer', 'beond-custom' ); ?></strong></p>
                    <textarea name="contact_faq_answers[]" rows="3" class="large-text"><?php echo esc_textarea( $faq['answer'] ); ?></textarea>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-faq-item" class="button button-primary"><?php _e( '+ Add FAQ Item', 'beond-custom' ); ?></button>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Add new FAQ item
        $('#add-faq-item').on('click', function() {
            var html = '<div class="faq-item-row" style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-left: 4px solid #2271b1; position: relative;">' +
                '<button type="button" class="remove-faq-item button" style="position: absolute; top: 10px; right: 10px;"><?php _e( 'Remove', 'beond-custom' ); ?></button>' +
                '<p><strong><?php _e( 'Question', 'beond-custom' ); ?></strong></p>' +
                '<input type="text" name="contact_faq_questions[]" value="" class="large-text" style="margin-bottom: 10px;" />' +
                '<p><strong><?php _e( 'Answer', 'beond-custom' ); ?></strong></p>' +
                '<textarea name="contact_faq_answers[]" rows="3" class="large-text"></textarea>' +
                '</div>';
            $('#faq-items-list').append(html);
        });
        
        // Remove FAQ item
        $(document).on('click', '.remove-faq-item', function() {
            if (confirm('<?php _e( 'Are you sure you want to remove this FAQ item?', 'beond-custom' ); ?>')) {
                $(this).closest('.faq-item-row').remove();
            }
        });
    });
    </script>
    <?php
}

/**
 * Save Contact Page Meta Boxes
 */
function beond_save_contact_page_meta_boxes( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['beond_contact_meta_nonce'] ) || ! wp_verify_nonce( $_POST['beond_contact_meta_nonce'], 'beond_contact_meta' ) ) {
        return;
    }
    
    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Check permissions
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }
    
    // Check if this is the contact page template
    $template = get_page_template_slug( $post_id );
    if ( $template !== 'page-contact.php' ) {
        return;
    }
    
    // Save Hero Section
    if ( isset( $_POST['contact_hero_label'] ) ) {
        update_post_meta( $post_id, '_contact_hero_label', sanitize_text_field( $_POST['contact_hero_label'] ) );
    }
    if ( isset( $_POST['contact_hero_title'] ) ) {
        update_post_meta( $post_id, '_contact_hero_title', sanitize_text_field( $_POST['contact_hero_title'] ) );
    }
    if ( isset( $_POST['contact_hero_subtitle'] ) ) {
        update_post_meta( $post_id, '_contact_hero_subtitle', sanitize_textarea_field( $_POST['contact_hero_subtitle'] ) );
    }
    
    // Save Contact Information
    if ( isset( $_POST['contact_email'] ) ) {
        update_post_meta( $post_id, '_contact_email', sanitize_email( $_POST['contact_email'] ) );
    }
    if ( isset( $_POST['contact_press_email'] ) ) {
        update_post_meta( $post_id, '_contact_press_email', sanitize_email( $_POST['contact_press_email'] ) );
    }
    if ( isset( $_POST['contact_phone'] ) ) {
        update_post_meta( $post_id, '_contact_phone', sanitize_text_field( $_POST['contact_phone'] ) );
    }
    if ( isset( $_POST['contact_phone_hours'] ) ) {
        update_post_meta( $post_id, '_contact_phone_hours', sanitize_text_field( $_POST['contact_phone_hours'] ) );
    }
    if ( isset( $_POST['contact_phone2'] ) ) {
        $phone2_value = sanitize_text_field( $_POST['contact_phone2'] );
        if ( ! empty( $phone2_value ) ) {
            update_post_meta( $post_id, '_contact_phone2', $phone2_value );
        } else {
            delete_post_meta( $post_id, '_contact_phone2' );
        }
    }
    if ( isset( $_POST['contact_phone2_hours'] ) ) {
        $phone2_hours_value = sanitize_text_field( $_POST['contact_phone2_hours'] );
        if ( ! empty( $phone2_hours_value ) ) {
            update_post_meta( $post_id, '_contact_phone2_hours', $phone2_hours_value );
        } else {
            delete_post_meta( $post_id, '_contact_phone2_hours' );
        }
    }
    if ( isset( $_POST['contact_address_line1'] ) ) {
        update_post_meta( $post_id, '_contact_address_line1', sanitize_text_field( $_POST['contact_address_line1'] ) );
    }
    if ( isset( $_POST['contact_address_line2'] ) ) {
        update_post_meta( $post_id, '_contact_address_line2', sanitize_text_field( $_POST['contact_address_line2'] ) );
    }
    if ( isset( $_POST['contact_address_note'] ) ) {
        update_post_meta( $post_id, '_contact_address_note', sanitize_text_field( $_POST['contact_address_note'] ) );
    }
    
    // Save Social Media Links
    $social_fields = array(
        'contact_twitter',
        'contact_facebook',
        'contact_instagram',
        'contact_linkedin',
        'contact_youtube',
        'contact_tiktok',
        'contact_pinterest',
        'contact_whatsapp',
        'contact_telegram',
        'contact_snapchat',
        'contact_reddit',
        'contact_github'
    );
    
    foreach ( $social_fields as $field ) {
        if ( isset( $_POST[$field] ) ) {
            $url = sanitize_text_field( $_POST[$field] );
            if ( ! empty( $url ) ) {
                update_post_meta( $post_id, '_' . $field, esc_url_raw( $url ) );
            } else {
                delete_post_meta( $post_id, '_' . $field );
            }
        }
    }
    
    // Save FAQ Section
    if ( isset( $_POST['contact_faq_label'] ) ) {
        update_post_meta( $post_id, '_contact_faq_label', sanitize_text_field( $_POST['contact_faq_label'] ) );
    }
    if ( isset( $_POST['contact_faq_title'] ) ) {
        update_post_meta( $post_id, '_contact_faq_title', sanitize_text_field( $_POST['contact_faq_title'] ) );
    }
    
    // Save FAQ Items
    if ( isset( $_POST['contact_faq_questions'] ) && isset( $_POST['contact_faq_answers'] ) ) {
        $questions = $_POST['contact_faq_questions'];
        $answers = $_POST['contact_faq_answers'];
        $faqs = array();
        
        for ( $i = 0; $i < count( $questions ); $i++ ) {
            if ( ! empty( $questions[$i] ) && ! empty( $answers[$i] ) ) {
                $faqs[] = array(
                    'question' => sanitize_text_field( $questions[$i] ),
                    'answer' => sanitize_textarea_field( $answers[$i] )
                );
            }
        }
        
        if ( ! empty( $faqs ) ) {
            update_post_meta( $post_id, '_contact_faqs', $faqs );
        } else {
            delete_post_meta( $post_id, '_contact_faqs' );
        }
    }
}
add_action( 'save_post_page', 'beond_save_contact_page_meta_boxes' );

/**
 * About Page - Hero Section Meta Box Callback
 */
function beond_about_hero_meta_box_callback( $post ) {
    wp_nonce_field( 'beond_about_meta', 'beond_about_meta_nonce' );
    
    $hero_label = get_post_meta( $post->ID, '_about_hero_label', true );
    $hero_title = get_post_meta( $post->ID, '_about_hero_title', true );
    $hero_subtitle = get_post_meta( $post->ID, '_about_hero_subtitle', true );
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="about_hero_label"><?php _e( 'Hero Label', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="about_hero_label" name="about_hero_label" value="<?php echo esc_attr( $hero_label ?: 'About Us' ); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Small label above the title', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="about_hero_title"><?php _e( 'Hero Title', 'beond-custom' ); ?></label>
            </th>
            <td>
                <input type="text" id="about_hero_title" name="about_hero_title" value="<?php echo esc_attr( $hero_title ?: 'Curated Insights for a Borderless World' ); ?>" class="large-text" />
                <p class="description"><?php _e( 'Main heading for the about page', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="about_hero_subtitle"><?php _e( 'Hero Subtitle', 'beond-custom' ); ?></label>
            </th>
            <td>
                <textarea id="about_hero_subtitle" name="about_hero_subtitle" rows="4" class="large-text"><?php echo esc_textarea( $hero_subtitle ?: "Beyond Borders Report is your premier source for in-depth analysis and expert perspectives on global business, leadership, and innovation. We connect industry leaders with the insights that matter most." ); ?></textarea>
                <p class="description"><?php _e( 'Description text below the title', 'beond-custom' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * About Page - Statistics Meta Box Callback
 */
function beond_about_stats_meta_box_callback( $post ) {
    $stat1_number = get_post_meta( $post->ID, '_about_stat1_number', true );
    $stat1_label = get_post_meta( $post->ID, '_about_stat1_label', true );
    $stat2_number = get_post_meta( $post->ID, '_about_stat2_number', true );
    $stat2_label = get_post_meta( $post->ID, '_about_stat2_label', true );
    $stat3_number = get_post_meta( $post->ID, '_about_stat3_number', true );
    $stat3_label = get_post_meta( $post->ID, '_about_stat3_label', true );
    $stat4_number = get_post_meta( $post->ID, '_about_stat4_number', true );
    $stat4_label = get_post_meta( $post->ID, '_about_stat4_label', true );
    ?>
    <p class="description" style="margin-bottom: 15px;"><?php _e( 'Statistics displayed in the hero section', 'beond-custom' ); ?></p>
    <table class="form-table">
        <tr>
            <th scope="row"><?php _e( 'Statistic 1', 'beond-custom' ); ?></th>
            <td>
                <input type="text" name="about_stat1_number" value="<?php echo esc_attr( $stat1_number ?: '200K+' ); ?>" class="regular-text" placeholder="200K+" />
                <p class="description"><?php _e( 'Number/value', 'beond-custom' ); ?></p>
                <input type="text" name="about_stat1_label" value="<?php echo esc_attr( $stat1_label ?: 'Global Readers' ); ?>" class="regular-text" style="margin-top: 8px;" placeholder="Global Readers" />
                <p class="description"><?php _e( 'Label', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e( 'Statistic 2', 'beond-custom' ); ?></th>
            <td>
                <input type="text" name="about_stat2_number" value="<?php echo esc_attr( $stat2_number ?: '500+' ); ?>" class="regular-text" placeholder="500+" />
                <p class="description"><?php _e( 'Number/value', 'beond-custom' ); ?></p>
                <input type="text" name="about_stat2_label" value="<?php echo esc_attr( $stat2_label ?: 'Expert Contributors' ); ?>" class="regular-text" style="margin-top: 8px;" placeholder="Expert Contributors" />
                <p class="description"><?php _e( 'Label', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e( 'Statistic 3', 'beond-custom' ); ?></th>
            <td>
                <input type="text" name="about_stat3_number" value="<?php echo esc_attr( $stat3_number ?: '50+' ); ?>" class="regular-text" placeholder="50+" />
                <p class="description"><?php _e( 'Number/value', 'beond-custom' ); ?></p>
                <input type="text" name="about_stat3_label" value="<?php echo esc_attr( $stat3_label ?: 'Industries Covered' ); ?>" class="regular-text" style="margin-top: 8px;" placeholder="Industries Covered" />
                <p class="description"><?php _e( 'Label', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e( 'Statistic 4', 'beond-custom' ); ?></th>
            <td>
                <input type="text" name="about_stat4_number" value="<?php echo esc_attr( $stat4_number ?: '10+' ); ?>" class="regular-text" placeholder="10+" />
                <p class="description"><?php _e( 'Number/value', 'beond-custom' ); ?></p>
                <input type="text" name="about_stat4_label" value="<?php echo esc_attr( $stat4_label ?: 'Years of Excellence' ); ?>" class="regular-text" style="margin-top: 8px;" placeholder="Years of Excellence" />
                <p class="description"><?php _e( 'Label', 'beond-custom' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * About Page - Mission Section Meta Box Callback
 */
function beond_about_mission_meta_box_callback( $post ) {
    // Mission header
    $mission_label = get_post_meta( $post->ID, '_about_mission_label', true );
    $mission_title = get_post_meta( $post->ID, '_about_mission_title', true );
    $mission_description = get_post_meta( $post->ID, '_about_mission_description', true );
    
    // Mission cards
    $mission1_icon = get_post_meta( $post->ID, '_about_mission1_icon', true );
    $mission1_title = get_post_meta( $post->ID, '_about_mission1_title', true );
    $mission1_text = get_post_meta( $post->ID, '_about_mission1_text', true );
    $mission2_icon = get_post_meta( $post->ID, '_about_mission2_icon', true );
    $mission2_title = get_post_meta( $post->ID, '_about_mission2_title', true );
    $mission2_text = get_post_meta( $post->ID, '_about_mission2_text', true );
    $mission3_icon = get_post_meta( $post->ID, '_about_mission3_icon', true );
    $mission3_title = get_post_meta( $post->ID, '_about_mission3_title', true );
    $mission3_text = get_post_meta( $post->ID, '_about_mission3_text', true );
    ?>
    <table class="form-table">
        <tr>
            <th colspan="2"><h3 style="margin: 0;"><?php _e( 'Section Header', 'beond-custom' ); ?></h3></th>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission_label"><?php _e( 'Label', 'beond-custom' ); ?></label></th>
            <td>
                <input type="text" id="about_mission_label" name="about_mission_label" value="<?php echo esc_attr( $mission_label ?: 'Our Mission' ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission_title"><?php _e( 'Title', 'beond-custom' ); ?></label></th>
            <td>
                <input type="text" id="about_mission_title" name="about_mission_title" value="<?php echo esc_attr( $mission_title ?: 'Empowering Leaders Through Knowledge' ); ?>" class="large-text" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission_description"><?php _e( 'Description', 'beond-custom' ); ?></label></th>
            <td>
                <textarea id="about_mission_description" name="about_mission_description" rows="3" class="large-text"><?php echo esc_textarea( $mission_description ?: "We believe in the power of informed decision-making. Our mission is to deliver premium, actionable insights that help business leaders navigate an increasingly complex global landscape." ); ?></textarea>
            </td>
        </tr>
        <tr>
            <th colspan="2"><h3 style="margin: 20px 0 10px 0;"><?php _e( 'Mission Card 1', 'beond-custom' ); ?></h3></th>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission1_icon"><?php _e( 'Icon (Emoji)', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_mission1_icon" name="about_mission1_icon" value="<?php echo esc_attr( $mission1_icon ?: '🎯' ); ?>" class="small-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission1_title"><?php _e( 'Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_mission1_title" name="about_mission1_title" value="<?php echo esc_attr( $mission1_title ?: 'Editorial Excellence' ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission1_text"><?php _e( 'Description', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_mission1_text" name="about_mission1_text" rows="3" class="large-text"><?php echo esc_textarea( $mission1_text ?: "Every piece of content is meticulously researched, fact-checked, and crafted to provide genuine value. We maintain the highest standards of journalistic integrity and editorial quality." ); ?></textarea></td>
        </tr>
        <tr>
            <th colspan="2"><h3 style="margin: 20px 0 10px 0;"><?php _e( 'Mission Card 2', 'beond-custom' ); ?></h3></th>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission2_icon"><?php _e( 'Icon (Emoji)', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_mission2_icon" name="about_mission2_icon" value="<?php echo esc_attr( $mission2_icon ?: '🌍' ); ?>" class="small-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission2_title"><?php _e( 'Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_mission2_title" name="about_mission2_title" value="<?php echo esc_attr( $mission2_title ?: 'Global Perspective' ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission2_text"><?php _e( 'Description', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_mission2_text" name="about_mission2_text" rows="3" class="large-text"><?php echo esc_textarea( $mission2_text ?: "With contributors across six continents, we bring diverse viewpoints and cross-cultural insights that help you see the bigger picture in international business and leadership." ); ?></textarea></td>
        </tr>
        <tr>
            <th colspan="2"><h3 style="margin: 20px 0 10px 0;"><?php _e( 'Mission Card 3', 'beond-custom' ); ?></h3></th>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission3_icon"><?php _e( 'Icon (Emoji)', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_mission3_icon" name="about_mission3_icon" value="<?php echo esc_attr( $mission3_icon ?: '💡' ); ?>" class="small-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission3_title"><?php _e( 'Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_mission3_title" name="about_mission3_title" value="<?php echo esc_attr( $mission3_title ?: 'Innovation Focus' ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_mission3_text"><?php _e( 'Description', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_mission3_text" name="about_mission3_text" rows="3" class="large-text"><?php echo esc_textarea( $mission3_text ?: "We stay ahead of trends, tracking emerging technologies, business models, and leadership paradigms that are shaping the future of work and commerce." ); ?></textarea></td>
        </tr>
    </table>
    <?php
}

/**
 * About Page - Story Section Meta Box Callback
 */
function beond_about_story_meta_box_callback( $post ) {
    $story_label = get_post_meta( $post->ID, '_about_story_label', true );
    $story_title = get_post_meta( $post->ID, '_about_story_title', true );
    $story_image = get_post_meta( $post->ID, '_about_story_image', true );
    $story_heading = get_post_meta( $post->ID, '_about_story_heading', true );
    $story_para1 = get_post_meta( $post->ID, '_about_story_para1', true );
    $story_para2 = get_post_meta( $post->ID, '_about_story_para2', true );
    $story_para3 = get_post_meta( $post->ID, '_about_story_para3', true );
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="about_story_label"><?php _e( 'Section Label', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_story_label" name="about_story_label" value="<?php echo esc_attr( $story_label ?: 'Our Story' ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_story_title"><?php _e( 'Section Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_story_title" name="about_story_title" value="<?php echo esc_attr( $story_title ?: 'A Decade of Impact' ); ?>" class="large-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_story_image"><?php _e( 'Story Image', 'beond-custom' ); ?></label></th>
            <td>
                <input type="hidden" id="about_story_image" name="about_story_image" value="<?php echo esc_attr( $story_image ); ?>" />
                <div class="story-image-preview" style="margin-bottom: 10px;">
                    <?php if ( $story_image ): ?>
                        <img src="<?php echo esc_url( $story_image ); ?>" style="max-width: 300px; height: auto; display: block;" />
                    <?php endif; ?>
                </div>
                <button type="button" class="button story-image-upload" data-target="about_story_image">
                    <?php _e( 'Choose Image', 'beond-custom' ); ?>
                </button>
                <button type="button" class="button story-image-remove" <?php echo $story_image ? '' : 'style="display:none;"'; ?>>
                    <?php _e( 'Remove Image', 'beond-custom' ); ?>
                </button>
                <p class="description"><?php _e( 'Upload or select an image from media library', 'beond-custom' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="about_story_heading"><?php _e( 'Story Heading', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_story_heading" name="about_story_heading" value="<?php echo esc_attr( $story_heading ?: 'From Vision to Leading Voice' ); ?>" class="large-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_story_para1"><?php _e( 'Paragraph 1', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_story_para1" name="about_story_para1" rows="4" class="large-text"><?php echo esc_textarea( $story_para1 ?: "Founded in 2016 by a team of seasoned journalists and business strategists, Beyond Borders Report emerged from a simple observation: the world needed a platform that transcended geographical and ideological boundaries to deliver truly global business intelligence." ); ?></textarea></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_story_para2"><?php _e( 'Paragraph 2', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_story_para2" name="about_story_para2" rows="4" class="large-text"><?php echo esc_textarea( $story_para2 ?: "What started as a modest newsletter for 500 subscribers has grown into a multimedia platform reaching over 200,000 industry leaders monthly. Our content has been cited by Fortune 500 companies, featured in major media outlets, and trusted by decision-makers worldwide." ); ?></textarea></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_story_para3"><?php _e( 'Paragraph 3', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_story_para3" name="about_story_para3" rows="4" class="large-text"><?php echo esc_textarea( $story_para3 ?: "Today, we're proud to be recognized as one of the most trusted sources for global business insights, combining rigorous journalism with expert analysis to help leaders make informed decisions in an uncertain world." ); ?></textarea></td>
        </tr>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        // Story Image Upload
        $('.story-image-upload').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetInput = $('#' + button.data('target'));
            var preview = button.siblings('.story-image-preview');
            var removeBtn = button.siblings('.story-image-remove');
            
            var mediaUploader = wp.media({
                title: 'Choose Story Image',
                button: {
                    text: 'Select Image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url);
                preview.html('<img src="' + attachment.url + '" style="max-width: 300px; height: auto; display: block;" />');
                removeBtn.show();
            });
            
            mediaUploader.open();
        });
        
        // Story Image Remove
        $('.story-image-remove').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetInput = $('#about_story_image');
            var preview = button.siblings('.story-image-preview');
            
            targetInput.val('');
            preview.html('');
            button.hide();
        });
    });
    </script>
    <?php
}

/**
 * About Page - Values Section Meta Box Callback  
 */
function beond_about_values_meta_box_callback( $post ) {
    $values_label = get_post_meta( $post->ID, '_about_values_label', true );
    $values_title = get_post_meta( $post->ID, '_about_values_title', true );
    $values_description = get_post_meta( $post->ID, '_about_values_description', true );
    
    // 4 value cards
    $defaults = array(
        array( 'num' => '01', 'title' => 'Integrity First', 'text' => "We never compromise on accuracy, transparency, or ethical journalism. Our readers trust us because we've earned it." ),
        array( 'num' => '02', 'title' => 'Diversity of Thought', 'text' => 'We actively seek out diverse perspectives and challenge conventional wisdom to provide balanced, nuanced analysis.' ),
        array( 'num' => '03', 'title' => 'Reader-Centric', 'text' => 'Your time is valuable. We deliver concise, actionable insights without fluff or unnecessary jargon.' ),
        array( 'num' => '04', 'title' => 'Continuous Learning', 'text' => "The world evolves, and so do we. We're committed to staying curious and adapting to serve our readers better." )
    );
    
    $values = array();
    for ( $i = 1; $i <= 4; $i++ ) {
        $values[$i] = array(
            'number' => get_post_meta( $post->ID, "_about_value{$i}_number", true ),
            'title' => get_post_meta( $post->ID, "_about_value{$i}_title", true ),
            'text' => get_post_meta( $post->ID, "_about_value{$i}_text", true )
        );
    }
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="about_values_label"><?php _e( 'Section Label', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_values_label" name="about_values_label" value="<?php echo esc_attr( $values_label ?: 'Our Values' ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_values_title"><?php _e( 'Section Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_values_title" name="about_values_title" value="<?php echo esc_attr( $values_title ?: 'What We Stand For' ); ?>" class="large-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_values_description"><?php _e( 'Description', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_values_description" name="about_values_description" rows="2" class="large-text"><?php echo esc_textarea( $values_description ?: 'Our core values guide everything we do, from content creation to community engagement.' ); ?></textarea></td>
        </tr>
        <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
        <tr>
            <th colspan="2"><h3 style="margin: 20px 0 10px 0;"><?php printf( __( 'Value Card %d', 'beond-custom' ), $i ); ?></h3></th>
        </tr>
        <tr>
            <th scope="row"><label for="about_value<?php echo $i; ?>_number"><?php _e( 'Number', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_value<?php echo $i; ?>_number" name="about_value<?php echo $i; ?>_number" value="<?php echo esc_attr( $values[$i]['number'] ?: $defaults[$i-1]['num'] ); ?>" class="small-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_value<?php echo $i; ?>_title"><?php _e( 'Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_value<?php echo $i; ?>_title" name="about_value<?php echo $i; ?>_title" value="<?php echo esc_attr( $values[$i]['title'] ?: $defaults[$i-1]['title'] ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_value<?php echo $i; ?>_text"><?php _e( 'Description', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_value<?php echo $i; ?>_text" name="about_value<?php echo $i; ?>_text" rows="3" class="large-text"><?php echo esc_textarea( $values[$i]['text'] ?: $defaults[$i-1]['text'] ); ?></textarea></td>
        </tr>
        <?php endfor; ?>
    </table>
    <?php
}

/**
 * About Page - Timeline Section Meta Box Callback
 */
function beond_about_timeline_meta_box_callback( $post ) {
    $timeline_label = get_post_meta( $post->ID, '_about_timeline_label', true );
    $timeline_title = get_post_meta( $post->ID, '_about_timeline_title', true );
    
    $defaults = array(
        array( 'year' => '2016', 'title' => 'The Beginning', 'text' => 'Beyond Borders Report launched with a weekly newsletter reaching 500 business leaders across North America and Europe.' ),
        array( 'year' => '2018', 'title' => 'Global Expansion', 'text' => 'Expanded coverage to Asia-Pacific and Middle East markets, establishing regional editorial teams and reaching 50,000 subscribers.' ),
        array( 'year' => '2020', 'title' => 'Digital Transformation', 'text' => 'Launched multimedia platform with podcasts, video interviews, and interactive data visualizations. Reader base grew to 150,000.' ),
        array( 'year' => '2023', 'title' => 'Industry Recognition', 'text' => 'Received "Best Business Publication" award and established exclusive partnerships with leading business schools and think tanks.' ),
        array( 'year' => '2026', 'title' => 'AI-Enhanced Insights', 'text' => 'Integrated advanced AI tools for personalized content recommendations while maintaining human editorial oversight and reaching 200,000+ readers.' )
    );
    
    $milestones = array();
    for ( $i = 1; $i <= 5; $i++ ) {
        $milestones[$i] = array(
            'year' => get_post_meta( $post->ID, "_about_milestone{$i}_year", true ),
            'title' => get_post_meta( $post->ID, "_about_milestone{$i}_title", true ),
            'text' => get_post_meta( $post->ID, "_about_milestone{$i}_text", true )
        );
    }
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="about_timeline_label"><?php _e( 'Section Label', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_timeline_label" name="about_timeline_label" value="<?php echo esc_attr( $timeline_label ?: 'Our Journey' ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_timeline_title"><?php _e( 'Section Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_timeline_title" name="about_timeline_title" value="<?php echo esc_attr( $timeline_title ?: 'Key Milestones' ); ?>" class="large-text" /></td>
        </tr>
        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
        <tr>
            <th colspan="2"><h3 style="margin: 20px 0 10px 0;"><?php printf( __( 'Milestone %d', 'beond-custom' ), $i ); ?></h3></th>
        </tr>
        <tr>
            <th scope="row"><label for="about_milestone<?php echo $i; ?>_year"><?php _e( 'Year', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_milestone<?php echo $i; ?>_year" name="about_milestone<?php echo $i; ?>_year" value="<?php echo esc_attr( $milestones[$i]['year'] ?: $defaults[$i-1]['year'] ); ?>" class="small-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_milestone<?php echo $i; ?>_title"><?php _e( 'Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_milestone<?php echo $i; ?>_title" name="about_milestone<?php echo $i; ?>_title" value="<?php echo esc_attr( $milestones[$i]['title'] ?: $defaults[$i-1]['title'] ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_milestone<?php echo $i; ?>_text"><?php _e( 'Description', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_milestone<?php echo $i; ?>_text" name="about_milestone<?php echo $i; ?>_text" rows="3" class="large-text"><?php echo esc_textarea( $milestones[$i]['text'] ?: $defaults[$i-1]['text'] ); ?></textarea></td>
        </tr>
        <?php endfor; ?>
    </table>
    <?php
}

/**
 * About Page - Team Section Meta Box Callback
 */
function beond_about_team_meta_box_callback( $post ) {
    $team_label = get_post_meta( $post->ID, '_about_team_label', true );
    $team_title = get_post_meta( $post->ID, '_about_team_title', true );
    $team_description = get_post_meta( $post->ID, '_about_team_description', true );
    
    $defaults = array(
        array(
            'image' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=400&fit=crop',
            'name' => 'Sarah Mitchell',
            'role' => 'Editor-in-Chief',
            'bio' => 'Former Wall Street Journal correspondent with 20+ years covering global markets and business leadership.',
            'linkedin' => '#',
            'twitter' => '#'
        ),
        array(
            'image' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&h=400&fit=crop',
            'name' => 'David Chen',
            'role' => 'Managing Editor',
            'bio' => 'Technology and innovation specialist, previously at Bloomberg and TechCrunch. MBA from Stanford GSB.',
            'linkedin' => '#',
            'twitter' => '#'
        ),
        array(
            'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400&fit=crop',
            'name' => 'Maria Rodriguez',
            'role' => 'Senior Global Analyst',
            'bio' => 'International affairs expert with expertise in emerging markets and cross-border commerce. PhD in Economics.',
            'linkedin' => '#',
            'twitter' => '#'
        )
    );
    
    $team = array();
    for ( $i = 1; $i <= 3; $i++ ) {
        $team[$i] = array(
            'image' => get_post_meta( $post->ID, "_about_team{$i}_image", true ),
            'name' => get_post_meta( $post->ID, "_about_team{$i}_name", true ),
            'role' => get_post_meta( $post->ID, "_about_team{$i}_role", true ),
            'bio' => get_post_meta( $post->ID, "_about_team{$i}_bio", true ),
            'linkedin' => get_post_meta( $post->ID, "_about_team{$i}_linkedin", true ),
            'twitter' => get_post_meta( $post->ID, "_about_team{$i}_twitter", true )
        );
    }
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="about_team_label"><?php _e( 'Section Label', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_team_label" name="about_team_label" value="<?php echo esc_attr( $team_label ?: 'Our Team' ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_team_title"><?php _e( 'Section Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_team_title" name="about_team_title" value="<?php echo esc_attr( $team_title ?: 'Meet the Leaders' ); ?>" class="large-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_team_description"><?php _e( 'Description', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_team_description" name="about_team_description" rows="2" class="large-text"><?php echo esc_textarea( $team_description ?: 'Our editorial team brings together decades of experience in journalism, business strategy, and global affairs.' ); ?></textarea></td>
        </tr>
        <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
        <tr>
            <th colspan="2"><h3 style="margin: 20px 0 10px 0;"><?php printf( __( 'Team Member %d', 'beond-custom' ), $i ); ?></h3></th>
        </tr>
        <tr>
            <th scope="row"><label for="about_team<?php echo $i; ?>_image"><?php _e( 'Image', 'beond-custom' ); ?></label></th>
            <td>
                <div class="team-image-wrapper">
                    <input type="hidden" id="about_team<?php echo $i; ?>_image" name="about_team<?php echo $i; ?>_image" value="<?php echo esc_attr( $team[$i]['image'] ?: $defaults[$i-1]['image'] ); ?>" />
                    <div class="team-image-preview" style="margin-bottom: 10px;">
                        <?php if ( $team[$i]['image'] || $defaults[$i-1]['image'] ) : ?>
                            <img src="<?php echo esc_url( $team[$i]['image'] ?: $defaults[$i-1]['image'] ); ?>" style="max-width: 150px; height: auto; display: block;" />
                        <?php endif; ?>
                    </div>
                    <button type="button" class="button team-image-upload" data-target="about_team<?php echo $i; ?>_image"><?php _e( 'Choose Image', 'beond-custom' ); ?></button>
                    <button type="button" class="button team-image-remove" data-target="about_team<?php echo $i; ?>_image" style="<?php echo empty( $team[$i]['image'] ) ? 'display:none;' : ''; ?>"><?php _e( 'Remove Image', 'beond-custom' ); ?></button>
                    <p class="description"><?php _e( 'Upload or select an image from media library', 'beond-custom' ); ?></p>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="about_team<?php echo $i; ?>_name"><?php _e( 'Name', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_team<?php echo $i; ?>_name" name="about_team<?php echo $i; ?>_name" value="<?php echo esc_attr( $team[$i]['name'] ?: $defaults[$i-1]['name'] ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_team<?php echo $i; ?>_role"><?php _e( 'Role/Title', 'beond-custom' ); ?></label></th>
            <td><input type="text" id="about_team<?php echo $i; ?>_role" name="about_team<?php echo $i; ?>_role" value="<?php echo esc_attr( $team[$i]['role'] ?: $defaults[$i-1]['role'] ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_team<?php echo $i; ?>_bio"><?php _e( 'Bio', 'beond-custom' ); ?></label></th>
            <td><textarea id="about_team<?php echo $i; ?>_bio" name="about_team<?php echo $i; ?>_bio" rows="3" class="large-text"><?php echo esc_textarea( $team[$i]['bio'] ?: $defaults[$i-1]['bio'] ); ?></textarea></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_team<?php echo $i; ?>_linkedin"><?php _e( 'LinkedIn URL', 'beond-custom' ); ?></label></th>
            <td><input type="url" id="about_team<?php echo $i; ?>_linkedin" name="about_team<?php echo $i; ?>_linkedin" value="<?php echo esc_attr( $team[$i]['linkedin'] ?: $defaults[$i-1]['linkedin'] ); ?>" class="large-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="about_team<?php echo $i; ?>_twitter"><?php _e( 'Twitter URL', 'beond-custom' ); ?></label></th>
            <td><input type="url" id="about_team<?php echo $i; ?>_twitter" name="about_team<?php echo $i; ?>_twitter" value="<?php echo esc_attr( $team[$i]['twitter'] ?: $defaults[$i-1]['twitter'] ); ?>" class="large-text" /></td>
        </tr>
        <?php endfor; ?>
    </table>
    <script>
    jQuery(document).ready(function($) {
        // Team Image Upload
        $('.team-image-upload').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var targetInput = button.data('target');
            var preview = button.siblings('.team-image-preview');
            var removeBtn = button.siblings('.team-image-remove');
            
            var mediaUploader = wp.media({
                title: '<?php _e( 'Choose Team Member Image', 'beond-custom' ); ?>',
                button: {
                    text: '<?php _e( 'Select Image', 'beond-custom' ); ?>'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#' + targetInput).val(attachment.url);
                preview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto; display: block;" />');
                removeBtn.show();
            });
            
            mediaUploader.open();
        });
        
        // Team Image Remove
        $('.team-image-remove').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var targetInput = button.data('target');
            var preview = button.siblings('.team-image-preview');
            
            $('#' + targetInput).val('');
            preview.html('');
            button.hide();
        });
    });
    </script>
    <?php
}

/**
 * Save About Page Meta Boxes
 */
function beond_save_about_page_meta_boxes( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['beond_about_meta_nonce'] ) || ! wp_verify_nonce( $_POST['beond_about_meta_nonce'], 'beond_about_meta' ) ) {
        return;
    }
    
    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Check permissions
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }
    
    // Check if this is the about page template
    $template = get_page_template_slug( $post_id );
    if ( $template !== 'page-about.php' ) {
        return;
    }
    
    // Save Hero Section
    if ( isset( $_POST['about_hero_label'] ) ) {
        update_post_meta( $post_id, '_about_hero_label', sanitize_text_field( $_POST['about_hero_label'] ) );
    }
    if ( isset( $_POST['about_hero_title'] ) ) {
        update_post_meta( $post_id, '_about_hero_title', sanitize_text_field( $_POST['about_hero_title'] ) );
    }
    if ( isset( $_POST['about_hero_subtitle'] ) ) {
        update_post_meta( $post_id, '_about_hero_subtitle', sanitize_textarea_field( $_POST['about_hero_subtitle'] ) );
    }
    
    // Save Statistics
    $stats_fields = array( 'stat1_number', 'stat1_label', 'stat2_number', 'stat2_label', 'stat3_number', 'stat3_label', 'stat4_number', 'stat4_label' );
    foreach ( $stats_fields as $field ) {
        if ( isset( $_POST['about_' . $field] ) ) {
            update_post_meta( $post_id, '_about_' . $field, sanitize_text_field( $_POST['about_' . $field] ) );
        }
    }
    
    // Save Mission Section
    $mission_fields = array( 'mission_label', 'mission_title', 'mission_description', 'mission1_icon', 'mission1_title', 'mission1_text', 'mission2_icon', 'mission2_title', 'mission2_text', 'mission3_icon', 'mission3_title', 'mission3_text' );
    foreach ( $mission_fields as $field ) {
        if ( isset( $_POST['about_' . $field] ) ) {
            update_post_meta( $post_id, '_about_' . $field, sanitize_textarea_field( $_POST['about_' . $field] ) );
        }
    }
    
    // Save Story Section
    $story_fields = array( 'story_label', 'story_title', 'story_image', 'story_heading', 'story_para1', 'story_para2', 'story_para3' );
    foreach ( $story_fields as $field ) {
        if ( isset( $_POST['about_' . $field] ) ) {
            if ( $field === 'story_image' ) {
                $value = sanitize_text_field( $_POST['about_' . $field] );
                if ( ! empty( $value ) ) {
                    update_post_meta( $post_id, '_about_' . $field, esc_url_raw( $value ) );
                } else {
                    delete_post_meta( $post_id, '_about_' . $field );
                }
            } else {
                update_post_meta( $post_id, '_about_' . $field, sanitize_textarea_field( $_POST['about_' . $field] ) );
            }
        }
    }
    
    // Save Values Section
    update_post_meta( $post_id, '_about_values_label', sanitize_text_field( $_POST['about_values_label'] ?? '' ) );
    update_post_meta( $post_id, '_about_values_title', sanitize_text_field( $_POST['about_values_title'] ?? '' ) );
    update_post_meta( $post_id, '_about_values_description', sanitize_textarea_field( $_POST['about_values_description'] ?? '' ) );
    for ( $i = 1; $i <= 4; $i++ ) {
        update_post_meta( $post_id, "_about_value{$i}_number", sanitize_text_field( $_POST["about_value{$i}_number"] ?? '' ) );
        update_post_meta( $post_id, "_about_value{$i}_title", sanitize_text_field( $_POST["about_value{$i}_title"] ?? '' ) );
        update_post_meta( $post_id, "_about_value{$i}_text", sanitize_textarea_field( $_POST["about_value{$i}_text"] ?? '' ) );
    }
    
    // Save Timeline Section
    update_post_meta( $post_id, '_about_timeline_label', sanitize_text_field( $_POST['about_timeline_label'] ?? '' ) );
    update_post_meta( $post_id, '_about_timeline_title', sanitize_text_field( $_POST['about_timeline_title'] ?? '' ) );
    for ( $i = 1; $i <= 5; $i++ ) {
        update_post_meta( $post_id, "_about_milestone{$i}_year", sanitize_text_field( $_POST["about_milestone{$i}_year"] ?? '' ) );
        update_post_meta( $post_id, "_about_milestone{$i}_title", sanitize_text_field( $_POST["about_milestone{$i}_title"] ?? '' ) );
        update_post_meta( $post_id, "_about_milestone{$i}_text", sanitize_textarea_field( $_POST["about_milestone{$i}_text"] ?? '' ) );
    }
    
    // Save Team Section
    update_post_meta( $post_id, '_about_team_label', sanitize_text_field( $_POST['about_team_label'] ?? '' ) );
    update_post_meta( $post_id, '_about_team_title', sanitize_text_field( $_POST['about_team_title'] ?? '' ) );
    update_post_meta( $post_id, '_about_team_description', sanitize_textarea_field( $_POST['about_team_description'] ?? '' ) );
    for ( $i = 1; $i <= 3; $i++ ) {
        update_post_meta( $post_id, "_about_team{$i}_image", esc_url_raw( $_POST["about_team{$i}_image"] ?? '' ) );
        update_post_meta( $post_id, "_about_team{$i}_name", sanitize_text_field( $_POST["about_team{$i}_name"] ?? '' ) );
        update_post_meta( $post_id, "_about_team{$i}_role", sanitize_text_field( $_POST["about_team{$i}_role"] ?? '' ) );
        update_post_meta( $post_id, "_about_team{$i}_bio", sanitize_textarea_field( $_POST["about_team{$i}_bio"] ?? '' ) );
        update_post_meta( $post_id, "_about_team{$i}_linkedin", esc_url_raw( $_POST["about_team{$i}_linkedin"] ?? '' ) );
        update_post_meta( $post_id, "_about_team{$i}_twitter", esc_url_raw( $_POST["about_team{$i}_twitter"] ?? '' ) );
    }
}
add_action( 'save_post_page', 'beond_save_about_page_meta_boxes' );
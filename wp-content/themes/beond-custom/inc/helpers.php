<?php
/**
 * Helper functions
 *
 * @package Beond_Custom
 */

/**
 * Get plugin settings helper
 */
function beond_get_header_settings( $key = '' ) {
    $settings = get_option( 'beyond_borders_header_settings', array() );
    
    if ( ! empty( $key ) ) {
        return $settings[ $key ] ?? null;
    }
    
    return $settings;
}

function beond_get_footer_settings( $key = '' ) {
    $settings = get_option( 'beyond_borders_footer_settings', array() );
    
    if ( ! empty( $key ) ) {
        return $settings[ $key ] ?? null;
    }
    
    return $settings;
}

function beond_get_theme_colors( $key = '' ) {
    $colors = get_option( 'beyond_borders_theme_colors', array() );
    
    if ( ! empty( $key ) ) {
        return $colors[ $key ] ?? null;
    }
    
    return $colors;
}

/**
 * Check if page has sidebar
 */
function beond_has_sidebar() {
    // Don't show sidebar on full-width pages
    if ( is_page_template( 'template-fullwidth.php' ) ) {
        return false;
    }
    
    return is_active_sidebar( 'sidebar-1' );
}

/**
 * Get related posts
 */
function beond_get_related_posts( $post_id, $limit = 3 ) {
    $categories = wp_get_post_categories( $post_id );
    
    if ( empty( $categories ) ) {
        return array();
    }
    
    $args = array(
        'category__in'   => $categories,
        'post__not_in'   => array( $post_id ),
        'posts_per_page' => $limit,
        'orderby'        => 'rand',
    );
    
    return new WP_Query( $args );
}

/**
 * Truncate text
 */
function beond_truncate_text( $text, $length = 100, $append = '...' ) {
    if ( strlen( $text ) <= $length ) {
        return $text;
    }
    
    return substr( $text, 0, $length ) . $append;
}

/**
 * Check if post is featured
 */
function beond_is_featured_post( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_post_type( $post_id ) === 'featured_article';
}

/**
 * Calculate reading time for a post
 */
function beond_get_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $content = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( strip_tags( $content ) );
    $reading_time = ceil( $word_count / 200 ); // Average reading speed: 200 words per minute
    
    return $reading_time;
}

/**
 * Get homepage settings helper
 */
function beond_get_homepage_settings( $key = '' ) {
    $settings = get_option( 'beyond_borders_homepage_settings', array() );
    
    if ( ! empty( $key ) ) {
        return $settings[ $key ] ?? null;
    }
    
    return $settings;
}

/**
 * Get stats settings helper
 */
function beond_get_stats_settings( $key = '' ) {
    $settings = get_option( 'beyond_borders_stats_settings', array() );
    
    if ( ! empty( $key ) ) {
        return $settings[ $key ] ?? null;
    }
    
    return $settings;
}

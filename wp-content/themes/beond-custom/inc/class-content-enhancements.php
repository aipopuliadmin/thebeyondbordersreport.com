<?php
/**
 * Content Enhancements for AEO/SEO/GEO Optimization
 * 
 * Table of Contents, Reading Time, Last Updated, etc.
 *
 * @package Beond_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Beond_Content_Enhancements {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Table of Contents
        add_filter( 'the_content', array( $this, 'add_table_of_contents' ), 10 );
        
        // Reading time
        add_action( 'beond_before_content', array( $this, 'display_reading_time' ) );
        
        // Last updated display
        add_action( 'beond_after_post_meta', array( $this, 'display_last_updated' ) );
    }
    
    /**
     * Add Table of Contents to content
     */
    public function add_table_of_contents( $content ) {
        if ( ! is_single() || ! in_the_loop() ) {
            return $content;
        }
        
        // Extract headings
        preg_match_all( '/<h([23])([^>]*)>(.*?)<\/h[23]>/i', $content, $headings, PREG_SET_ORDER );
        
        if ( count( $headings ) < 4 ) {
            return $content; // Skip if less than 4 headings
        }
        
        // Generate TOC
        $toc = '<div class="table-of-contents">';
        $toc .= '<h2>Table of Contents</h2>';
        $toc .= '<ul class="toc-list">';
        
        foreach ( $headings as $index => $heading ) {
            $level = $heading[1];
            $heading_text = wp_strip_all_tags( $heading[3] );
            $anchor = sanitize_title( $heading_text );
            
            // Skip if heading is already an anchor or is "Table of Contents"
            if ( stripos( $heading_text, 'table of contents' ) !== false ) {
                continue;
            }
            
            $class = $level == 3 ? 'toc-sub-item' : 'toc-item';
            
            $toc .= sprintf(
                '<li class="%s"><a href="#%s">%s</a></li>',
                esc_attr( $class ),
                esc_attr( $anchor ),
                esc_html( $heading_text )
            );
            
            // Add ID to original heading
            $content = preg_replace(
                '/<h' . $level . '([^>]*)>' . preg_quote( $heading[3], '/' ) . '<\/h' . $level . '>/i',
                '<h' . $level . '$1 id="' . esc_attr( $anchor ) . '">' . $heading[3] . '</h' . $level . '>',
                $content,
                1
            );
        }
        
        $toc .= '</ul></div>';
        
        // Insert TOC after first paragraph
        $paragraphs = explode( '</p>', $content, 2 );
        if ( count( $paragraphs ) > 1 ) {
            $content = $paragraphs[0] . '</p>' . $toc . $paragraphs[1];
        }
        
        return $content;
    }
    
    /**
     * Calculate and display reading time
     */
    public function display_reading_time() {
        if ( ! is_single() ) {
            return;
        }
        
        global $post;
        
        $word_count = str_word_count( strip_tags( $post->post_content ) );
        $reading_time = ceil( $word_count / 200 ); // Average 200 words per minute
        
        echo '<span class="reading-time">';
        echo '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0a8 8 0 100 16A8 8 0 008 0zm0 14.5a6.5 6.5 0 110-13 6.5 6.5 0 010 13z"/><path d="M7.5 4v4.5l3.5 2 .5-.866-3-1.732V4z"/></svg>';
        echo sprintf( 
            esc_html( _n( '%d min read', '%d min read', $reading_time, 'beond-custom' ) ),
            $reading_time
        );
        echo '</span>';
    }
    
    /**
     * Display last updated date
     */
    public function display_last_updated() {
        if ( ! is_single() ) {
            return;
        }
        
        $published = get_the_date( 'U' );
        $modified = get_the_modified_date( 'U' );
        
        // Only show if actually updated (more than 1 day difference)
        if ( $modified > $published + 86400 ) {
            echo '<span class="last-updated">';
            echo '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M11.534 7h3.932a.25.25 0 01.192.41l-1.966 2.36a.25.25 0 01-.384 0l-1.966-2.36a.25.25 0 01.192-.41zm-11 2h3.932a.25.25 0 00.192-.41L2.692 6.23a.25.25 0 00-.384 0L.342 8.59A.25.25 0 00.534 9z"/><path d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 11-.771-.636A6.002 6.002 0 0113.917 7H12.9A5.002 5.002 0 008 3zM3.1 9a5.002 5.002 0 008.757 2.182.5.5 0 11.771.636A6.002 6.002 0 012.083 9H3.1z"/></svg>';
            echo '<span>Updated: ' . get_the_modified_date( 'F j, Y' ) . '</span>';
            echo '</span>';
        }
    }
    
    /**
     * Get reading time (utility function)
     */
    public static function get_reading_time( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        
        $post = get_post( $post_id );
        $word_count = str_word_count( strip_tags( $post->post_content ) );
        return ceil( $word_count / 200 );
    }
}

// Initialize
new Beond_Content_Enhancements();

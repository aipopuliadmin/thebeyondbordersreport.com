<?php
/**
 * FAQ Schema Generator for AEO Optimization
 * 
 * Automatically generates FAQPage schema from Rank Math FAQ blocks
 * and custom FAQ sections in posts.
 *
 * @package Beond_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Beond_FAQ_Schema {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_head', array( $this, 'output_faq_schema' ), 5 );
    }
    
    /**
     * Generate and output FAQ schema for single posts
     */
    public function output_faq_schema() {
        if ( ! is_single() ) {
            return;
        }
        
        global $post;
        
        // Get FAQs from post meta
        $faqs = get_post_meta( $post->ID, '_beond_faqs', true );
        
        // Also check for Rank Math FAQ block in content
        $content_faqs = $this->extract_faqs_from_content( $post->post_content );
        
        // Merge FAQs
        if ( ! empty( $content_faqs ) ) {
            $faqs = ! empty( $faqs ) ? array_merge( $faqs, $content_faqs ) : $content_faqs;
        }
        
        if ( empty( $faqs ) || ! is_array( $faqs ) ) {
            return;
        }
        
        $schema = $this->generate_faq_schema( $faqs );
        
        if ( $schema ) {
            echo $schema . "\n";
        }
    }
    
    /**
     * Extract FAQs from Rank Math FAQ blocks in content
     */
    private function extract_faqs_from_content( $content ) {
        $faqs = array();
        
        // Pattern to match FAQ sections with h3 questions and following paragraphs
        preg_match_all( '/<h3[^>]*class="[^"]*faq-question[^"]*"[^>]*>(.*?)<\/h3>\s*<p[^>]*class="[^"]*faq-answer[^"]*"[^>]*>(.*?)<\/p>/is', $content, $matches, PREG_SET_ORDER );
        
        foreach ( $matches as $match ) {
            $faqs[] = array(
                'question' => wp_strip_all_tags( $match[1] ),
                'answer' => wp_strip_all_tags( $match[2] )
            );
        }
        
        // Also look for our custom FAQ format
        preg_match_all( '/<h3>(.*?)\?<\/h3>\s*<p><strong>Answer:<\/strong>(.*?)<\/p>/is', $content, $custom_matches, PREG_SET_ORDER );
        
        foreach ( $custom_matches as $match ) {
            $faqs[] = array(
                'question' => wp_strip_all_tags( $match[1] ) . '?',
                'answer' => wp_strip_all_tags( $match[2] )
            );
        }
        
        return $faqs;
    }
    
    /**
     * Generate FAQ schema markup
     */
    private function generate_faq_schema( $faqs ) {
        if ( empty( $faqs ) ) {
            return '';
        }
        
        $faq_items = array();
        
        foreach ( $faqs as $faq ) {
            if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) {
                continue;
            }
            
            $faq_items[] = array(
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                )
            );
        }
        
        if ( empty( $faq_items ) ) {
            return '';
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faq_items
        );
        
        return '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . '</script>';
    }
    
    /**
     * Save FAQs to post meta (for use in post editor)
     */
    public static function save_faqs( $post_id, $faqs ) {
        if ( ! is_array( $faqs ) ) {
            return false;
        }
        
        return update_post_meta( $post_id, '_beond_faqs', $faqs );
    }
    
    /**
     * Get FAQs from post meta
     */
    public static function get_faqs( $post_id ) {
        $faqs = get_post_meta( $post_id, '_beond_faqs', true );
        return is_array( $faqs ) ? $faqs : array();
    }
}

// Initialize
new Beond_FAQ_Schema();

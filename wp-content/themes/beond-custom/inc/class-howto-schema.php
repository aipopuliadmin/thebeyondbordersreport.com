<?php
/**
 * HowTo Schema Generator for AEO Optimization
 * 
 * Automatically generates HowTo schema for step-by-step content
 *
 * @package Beond_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Beond_HowTo_Schema {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_head', array( $this, 'output_howto_schema' ), 15 );
    }
    
    /**
     * Generate and output HowTo schema for single posts
     */
    public function output_howto_schema() {
        if ( ! is_single() ) {
            return;
        }
        
        global $post;
        
        // Get HowTo data from post meta
        $howto = get_post_meta( $post->ID, '_beond_howto', true );
        
        // Also try to extract from content
        if ( empty( $howto ) ) {
            $howto = $this->extract_howto_from_content( $post->post_content );
        }
        
        if ( empty( $howto ) ) {
            return;
        }
        
        $schema = $this->generate_howto_schema( $howto );
        
        if ( $schema ) {
            echo $schema . "\n";
        }
    }
    
    /**
     * Extract HowTo steps from content
     */
    private function extract_howto_from_content( $content ) {
        $steps = array();
        
        // Look for "Step X:" or numbered list patterns
        preg_match_all( '/(?:<h[23]>|<strong>|<b>)\s*(?:Step\s*)?(\d+)[:\.]?\s*(.*?)<\/(?:h[23]|strong|b)>\s*(?:<p>)?(.*?)(?:<\/p>)?(?=<h[23]>|<strong>Step|\z)/is', $content, $matches, PREG_SET_ORDER );
        
        foreach ( $matches as $match ) {
            $step_number = $match[1];
            $step_title = wp_strip_all_tags( $match[2] );
            $step_text = wp_strip_all_tags( $match[3] );
            
            if ( ! empty( $step_title ) && ! empty( $step_text ) ) {
                $steps[] = array(
                    'name' => $step_title,
                    'text' => $step_text
                );
            }
        }
        
        // Need at least 3 steps to be valid HowTo
        if ( count( $steps ) < 3 ) {
            return null;
        }
        
        return array(
            'name' => get_the_title(),
            'steps' => $steps
        );
    }
    
    /**
     * Generate HowTo schema markup
     */
    private function generate_howto_schema( $howto ) {
        if ( empty( $howto['steps'] ) || count( $howto['steps'] ) < 3 ) {
            return '';
        }
        
        $steps = array();
        
        foreach ( $howto['steps'] as $index => $step ) {
            $step_data = array(
                '@type' => 'HowToStep',
                'name' => $step['name'],
                'text' => $step['text'],
                'position' => $index + 1
            );
            
            // Add image if available
            if ( ! empty( $step['image'] ) ) {
                $step_data['image'] = $step['image'];
            }
            
            $steps[] = $step_data;
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => ! empty( $howto['name'] ) ? $howto['name'] : get_the_title(),
            'step' => $steps
        );
        
        // Add optional fields
        if ( ! empty( $howto['description'] ) ) {
            $schema['description'] = $howto['description'];
        }
        
        if ( ! empty( $howto['totalTime'] ) ) {
            $schema['totalTime'] = $howto['totalTime'];
        }
        
        if ( has_post_thumbnail() ) {
            $schema['image'] = get_the_post_thumbnail_url( null, 'full' );
        }
        
        return '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . '</script>';
    }
    
    /**
     * Save HowTo data to post meta
     */
    public static function save_howto( $post_id, $howto ) {
        return update_post_meta( $post_id, '_beond_howto', $howto );
    }
}

// Initialize
new Beond_HowTo_Schema();

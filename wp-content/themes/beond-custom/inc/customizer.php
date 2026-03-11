<?php
/**
 * Theme Customizer
 *
 * @package Beond_Custom
 */

/**
 * Add postMessage support for site title and description.
 */
function beond_customize_register( $wp_customize ) {
    
    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
    $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

    if ( isset( $wp_customize->selective_refresh ) ) {
        $wp_customize->selective_refresh->add_partial(
            'blogname',
            array(
                'selector'        => '.site-title a',
                'render_callback' => function() {
                    bloginfo( 'name' );
                },
            )
        );
        $wp_customize->selective_refresh->add_partial(
            'blogdescription',
            array(
                'selector'        => '.site-description',
                'render_callback' => function() {
                    bloginfo( 'description' );
                },
            )
        );
    }
}
add_action( 'customize_register', 'beond_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function beond_customize_preview_js() {
    wp_enqueue_script( 
        'beond-customizer', 
        BEOND_THEME_URI . '/assets/js/customizer.js', 
        array( 'customize-preview' ), 
        BEOND_VERSION, 
        true 
    );
}
add_action( 'customize_preview_init', 'beond_customize_preview_js' );

<?php
/**
 * WordPress Customizer integration.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Customizer {

    /**
     * Constructor.
     */
    public function __construct() {
        // Hook to sync customizer changes to the admin option
        add_action( 'customize_save_after', array( $this, 'sync_customizer_to_option' ) );
    }

    /**
     * Register customizer settings.
     */
    public function register_customizer_settings( $wp_customize ) {
        
        // Add Beyond Borders Section
        $wp_customize->add_section( 'beyond_borders_colors', array(
            'title'       => __( 'Beyond Borders Colors', 'beyond-borders' ),
            'priority'    => 30,
            'description' => __( 'Customize the color palette for your editorial platform', 'beyond-borders' ),
        ) );

        // Get saved colors
        $saved_colors = get_option( 'beyond_borders_theme_colors', array() );

        // Navy Deep Color
        $wp_customize->add_setting( 'beyond_borders_navy_deep', array(
            'default'           => $saved_colors['navy_deep'] ?? '#0A1628',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_navy_deep', array(
            'label'    => __( 'Navy Deep', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_navy_deep',
        ) ) );

        // Navy Primary Color
        $wp_customize->add_setting( 'beyond_borders_navy_primary', array(
            'default'           => $saved_colors['navy_primary'] ?? '#003366',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_navy_primary', array(
            'label'    => __( 'Navy Primary', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_navy_primary',
        ) ) );

        // Gold Primary Color
        $wp_customize->add_setting( 'beyond_borders_gold_primary', array(
            'default'           => $saved_colors['gold_primary'] ?? '#C9A961',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_gold_primary', array(
            'label'    => __( 'Gold Primary', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_gold_primary',
        ) ) );

        // Gold Champagne Color
        $wp_customize->add_setting( 'beyond_borders_gold_champagne', array(
            'default'           => $saved_colors['gold_champagne'] ?? '#D4AF37',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_gold_champagne', array(
            'label'    => __( 'Gold Champagne', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_gold_champagne',
        ) ) );

        // Cream Color
        $wp_customize->add_setting( 'beyond_borders_cream', array(
            'default'           => $saved_colors['cream'] ?? '#FAF8F5',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_cream', array(
            'label'    => __( 'Cream', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_cream',
        ) ) );

        // Charcoal Dark Color
        $wp_customize->add_setting( 'beyond_borders_charcoal_dark', array(
            'default'           => $saved_colors['charcoal_dark'] ?? '#1A1A2E',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_charcoal_dark', array(
            'label'    => __( 'Charcoal Dark', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_charcoal_dark',
        ) ) );

        // Card Background Color (Dark Theme)
        $wp_customize->add_setting( 'beyond_borders_faq_card_bg', array(
            'default'           => $saved_colors['faq_card_bg'] ?? '#1F2937',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_faq_card_bg', array(
            'label'    => __( 'Card Background', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_faq_card_bg',
        ) ) );

        // Section Background Color
        $wp_customize->add_setting( 'beyond_borders_section_bg', array(
            'default'           => $saved_colors['section_bg'] ?? '#0A1628',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_section_bg', array(
            'label'    => __( 'Section Background', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_section_bg',
        ) ) );

        // Text Color
        $wp_customize->add_setting( 'beyond_borders_text_color', array(
            'default'           => $saved_colors['text_color'] ?? '#FFFFFF',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_text_color', array(
            'label'    => __( 'Text Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_text_color',
        ) ) );

        // Button Color
        $wp_customize->add_setting( 'beyond_borders_button_color', array(
            'default'           => $saved_colors['button_color'] ?? '#D4AF37',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_button_color', array(
            'label'    => __( 'Button Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_button_color',
        ) ) );

        // Heading Color
        $wp_customize->add_setting( 'beyond_borders_heading_color', array(
            'default'           => $saved_colors['heading_color'] ?? '#1F2937',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_heading_color', array(
            'label'    => __( 'Heading Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_heading_color',
        ) ) );

        // Sub Heading Color
        $wp_customize->add_setting( 'beyond_borders_sub_heading_color', array(
            'default'           => $saved_colors['sub_heading_color'] ?? '#374151',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_sub_heading_color', array(
            'label'    => __( 'Sub Heading Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_sub_heading_color',
        ) ) );

        // Title Color
        $wp_customize->add_setting( 'beyond_borders_title_color', array(
            'default'           => $saved_colors['title_color'] ?? '#0A0A0A',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_title_color', array(
            'label'    => __( 'Title Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_title_color',
        ) ) );

        // Sub Title Color
        $wp_customize->add_setting( 'beyond_borders_sub_title_color', array(
            'default'           => $saved_colors['sub_title_color'] ?? '#6B7280',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_sub_title_color', array(
            'label'    => __( 'Sub Title Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_sub_title_color',
        ) ) );

        // Paragraph Color
        $wp_customize->add_setting( 'beyond_borders_paragraph_color', array(
            'default'           => $saved_colors['paragraph_color'] ?? '#4B5563',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_paragraph_color', array(
            'label'    => __( 'Paragraph Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_paragraph_color',
        ) ) );

        // Meta Color
        $wp_customize->add_setting( 'beyond_borders_meta_color', array(
            'default'           => $saved_colors['meta_color'] ?? '#9CA3AF',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_meta_color', array(
            'label'    => __( 'Meta Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_meta_color',
        ) ) );

        // Link Color
        $wp_customize->add_setting( 'beyond_borders_link_color', array(
            'default'           => $saved_colors['link_color'] ?? '#003265',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_link_color', array(
            'label'    => __( 'Link Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_link_color',
        ) ) );

        // Link Hover Color
        $wp_customize->add_setting( 'beyond_borders_link_hover_color', array(
            'default'           => $saved_colors['link_hover_color'] ?? '#D4AF37',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_link_hover_color', array(
            'label'    => __( 'Link Hover Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_link_hover_color',
        ) ) );

        // Button Hover Color
        $wp_customize->add_setting( 'beyond_borders_button_hover_color', array(
            'default'           => $saved_colors['button_hover_color'] ?? '#B8941F',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_button_hover_color', array(
            'label'    => __( 'Button Hover Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_button_hover_color',
        ) ) );

        // Button Background Color
        $wp_customize->add_setting( 'beyond_borders_button_bg_color', array(
            'default'           => $saved_colors['button_bg_color'] ?? '#D4AF37',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_button_bg_color', array(
            'label'    => __( 'Button Background Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_button_bg_color',
        ) ) );

        // Button Background Hover Color
        $wp_customize->add_setting( 'beyond_borders_button_bg_hover_color', array(
            'default'           => $saved_colors['button_bg_hover_color'] ?? '#003265',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'beyond_borders_button_bg_hover_color', array(
            'label'    => __( 'Button Background Hover Color', 'beyond-borders' ),
            'section'  => 'beyond_borders_colors',
            'settings' => 'beyond_borders_button_bg_hover_color',
        ) ) );
    }

    /**
     * Output customizer CSS to wp_head.
     */
    public function output_customizer_css() {
        
        $navy_deep = get_theme_mod( 'beyond_borders_navy_deep', '#0A1628' );
        $navy_primary = get_theme_mod( 'beyond_borders_navy_primary', '#003366' );
        $gold_primary = get_theme_mod( 'beyond_borders_gold_primary', '#C9A961' );
        $gold_champagne = get_theme_mod( 'beyond_borders_gold_champagne', '#D4AF37' );
        $cream = get_theme_mod( 'beyond_borders_cream', '#FAF8F5' );
        $charcoal_dark = get_theme_mod( 'beyond_borders_charcoal_dark', '#1A1A2E' );
        $faq_card_bg = get_theme_mod( 'beyond_borders_faq_card_bg', '#1F2937' );
        $section_bg = get_theme_mod( 'beyond_borders_section_bg', '#0A1628' );
        $text_color = get_theme_mod( 'beyond_borders_text_color', '#FFFFFF' );
        $button_color = get_theme_mod( 'beyond_borders_button_color', '#D4AF37' );
        $heading_color = get_theme_mod( 'beyond_borders_heading_color', '#1F2937' );
        $sub_heading_color = get_theme_mod( 'beyond_borders_sub_heading_color', '#374151' );
        $title_color = get_theme_mod( 'beyond_borders_title_color', '#0A0A0A' );
        $sub_title_color = get_theme_mod( 'beyond_borders_sub_title_color', '#6B7280' );
        $paragraph_color = get_theme_mod( 'beyond_borders_paragraph_color', '#4B5563' );
        $meta_color = get_theme_mod( 'beyond_borders_meta_color', '#9CA3AF' );
        $link_color = get_theme_mod( 'beyond_borders_link_color', '#003265' );
        $link_hover_color = get_theme_mod( 'beyond_borders_link_hover_color', '#D4AF37' );
        $button_hover_color = get_theme_mod( 'beyond_borders_button_hover_color', '#B8941F' );
        $button_bg_color = get_theme_mod( 'beyond_borders_button_bg_color', '#D4AF37' );
        $button_bg_hover_color = get_theme_mod( 'beyond_borders_button_bg_hover_color', '#003265' );

        ?>
        <style type="text/css" id="beyond-borders-custom-colors">
            :root {
                --navy-deep: <?php echo esc_attr( $navy_deep ); ?>;
                --navy-primary: <?php echo esc_attr( $navy_primary ); ?>;
                --gold-primary: <?php echo esc_attr( $gold_primary ); ?>;
                --gold-champagne: <?php echo esc_attr( $gold_champagne ); ?>;
                --cream: <?php echo esc_attr( $cream ); ?>;
                --charcoal-dark: <?php echo esc_attr( $charcoal_dark ); ?>;
                --faq-card-bg: <?php echo esc_attr( $faq_card_bg ); ?>;
                --section-bg: <?php echo esc_attr( $section_bg ); ?>;
                --text-color: <?php echo esc_attr( $text_color ); ?>;
                --button-color: <?php echo esc_attr( $button_color ); ?>;
                --heading-color: <?php echo esc_attr( $heading_color ); ?>;
                --sub-heading-color: <?php echo esc_attr( $sub_heading_color ); ?>;
                --title-color: <?php echo esc_attr( $title_color ); ?>;
                --sub-title-color: <?php echo esc_attr( $sub_title_color ); ?>;
                --paragraph-color: <?php echo esc_attr( $paragraph_color ); ?>;
                --meta-color: <?php echo esc_attr( $meta_color ); ?>;
                --link-color: <?php echo esc_attr( $link_color ); ?>;
                --link-hover-color: <?php echo esc_attr( $link_hover_color ); ?>;
                --button-hover-color: <?php echo esc_attr( $button_hover_color ); ?>;
                --button-bg-color: <?php echo esc_attr( $button_bg_color ); ?>;
                --button-bg-hover-color: <?php echo esc_attr( $button_bg_hover_color ); ?>;
            }
        </style>
        <?php
    }

    /**
     * Enqueue customizer preview JavaScript.
     */
    public function enqueue_customizer_preview() {
        wp_enqueue_script(
            'beyond-borders-customizer-preview',
            BEYOND_BORDERS_PLUGIN_URL . 'admin/js/customizer-preview.js',
            array( 'customize-preview' ),
            BEYOND_BORDERS_VERSION,
            true
        );
    }

    /**
     * Sync customizer changes to the admin page option.
     * 
     * This ensures that changes made in the customizer are reflected
     * in the admin color palette page and vice versa.
     */
    public function sync_customizer_to_option() {
        $colors = array(
            'navy_deep'              => get_theme_mod( 'beyond_borders_navy_deep', '#0A1628' ),
            'navy_primary'           => get_theme_mod( 'beyond_borders_navy_primary', '#003265' ),
            'gold_primary'           => get_theme_mod( 'beyond_borders_gold_primary', '#C9A961' ),
            'gold_champagne'         => get_theme_mod( 'beyond_borders_gold_champagne', '#D4AF37' ),
            'cream'                  => get_theme_mod( 'beyond_borders_cream', '#FAF8F5' ),
            'charcoal_dark'          => get_theme_mod( 'beyond_borders_charcoal_dark', '#2C3E50' ),
            'faq_card_bg'            => get_theme_mod( 'beyond_borders_faq_card_bg', '#1F2937' ),
            'section_bg'             => get_theme_mod( 'beyond_borders_section_bg', '#0A1628' ),
            'text_color'             => get_theme_mod( 'beyond_borders_text_color', '#FFFFFF' ),
            'button_color'           => get_theme_mod( 'beyond_borders_button_color', '#D4AF37' ),
            'heading_color'          => get_theme_mod( 'beyond_borders_heading_color', '#1F2937' ),
            'sub_heading_color'      => get_theme_mod( 'beyond_borders_sub_heading_color', '#374151' ),
            'title_color'            => get_theme_mod( 'beyond_borders_title_color', '#0A0A0A' ),
            'sub_title_color'        => get_theme_mod( 'beyond_borders_sub_title_color', '#6B7280' ),
            'paragraph_color'        => get_theme_mod( 'beyond_borders_paragraph_color', '#4B5563' ),
            'meta_color'             => get_theme_mod( 'beyond_borders_meta_color', '#9CA3AF' ),
            'link_color'             => get_theme_mod( 'beyond_borders_link_color', '#003265' ),
            'link_hover_color'       => get_theme_mod( 'beyond_borders_link_hover_color', '#D4AF37' ),
            'button_hover_color'     => get_theme_mod( 'beyond_borders_button_hover_color', '#B8941F' ),
            'button_bg_color'        => get_theme_mod( 'beyond_borders_button_bg_color', '#D4AF37' ),
            'button_bg_hover_color'  => get_theme_mod( 'beyond_borders_button_bg_hover_color', '#003265' ),
        );

        update_option( 'beyond_borders_theme_colors', $colors );
    }
}

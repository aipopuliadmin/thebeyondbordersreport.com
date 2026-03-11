<?php
/**
 * Font Manager Class
 * 
 * Handles font loading and CSS generation
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Font_Manager {

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action( 'wp_head', array( $this, 'output_font_styles' ), 999 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts' ), 999 );
    }

    /**
     * Get font settings
     */
    private function get_font_settings() {
        $defaults = array(
            'heading_source' => 'google',
            'heading_google_font' => 'Playfair Display',
            'heading_weight' => '700',
            
            'subheading_source' => 'google',
            'subheading_google_font' => 'Lora',
            'subheading_weight' => '600',
            
            'paragraph_source' => 'google',
            'paragraph_google_font' => 'Inter',
            'paragraph_weight' => '400',
            
            'link_source' => 'google',
            'link_google_font' => 'Inter',
            'link_weight' => '500',
            
            'button_source' => 'google',
            'button_google_font' => 'Inter',
            'button_weight' => '600',
        );

        $settings = get_option( 'beyond_borders_font_settings', array() );
        return wp_parse_args( $settings, $defaults );
    }

    /**
     * Enqueue Google Fonts
     */
    public function enqueue_fonts() {
        $settings = $this->get_font_settings();
        $google_fonts = array();

        // Collect all Google fonts used
        $elements = array( 'heading', 'subheading', 'paragraph', 'link', 'button' );
        
        foreach ( $elements as $element ) {
            if ( isset( $settings[$element . '_source'] ) && $settings[$element . '_source'] === 'google' ) {
                $font = $settings[$element . '_google_font'] ?? '';
                $weight = $settings[$element . '_weight'] ?? '400';
                
                if ( !empty( $font ) ) {
                    if ( !isset( $google_fonts[$font] ) ) {
                        $google_fonts[$font] = array();
                    }
                    $google_fonts[$font][] = $weight;
                }
            }
        }

        // Enqueue Google Fonts if any
        if ( !empty( $google_fonts ) ) {
            $font_families = array();
            
            foreach ( $google_fonts as $font => $weights ) {
                $weights = array_unique( $weights );
                sort( $weights );
                $font_families[] = str_replace( ' ', '+', $font ) . ':wght@' . implode( ';', $weights );
            }
            
            $fonts_url = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $font_families ) . '&display=swap';
            
            wp_enqueue_style( 'beyond-borders-google-fonts', $fonts_url, array(), null );
        }
    }

    /**
     * Output custom font CSS and variables
     */
    public function output_font_styles() {
        $settings = $this->get_font_settings();
        
        echo "\n<!-- Beyond Borders Font Styles -->\n";
        echo "<style id='beyond-borders-fonts'>\n";
        
        // Output @font-face for custom fonts
        $elements = array( 'heading', 'subheading', 'paragraph', 'link', 'button' );
        
        foreach ( $elements as $element ) {
            if ( isset( $settings[$element . '_source'] ) && $settings[$element . '_source'] === 'custom' ) {
                $custom_name = $settings[$element . '_custom_name'] ?? '';
                $custom_files = $settings[$element . '_custom_files'] ?? array();
                
                if ( !empty( $custom_name ) && !empty( $custom_files ) ) {
                    echo $this->generate_font_face( $custom_name, $custom_files );
                }
            }
        }
        
        // Generate CSS variables
        echo "\n:root {\n";
        
        // Heading font (override theme's --font-headline)
        echo "    --font-headline: " . $this->get_font_family_css( 'heading', $settings ) . ";\n";
        echo "    --font-primary: " . $this->get_font_family_css( 'heading', $settings ) . ";\n";
        echo "    --heading-weight: " . ( $settings['heading_weight'] ?? '700' ) . ";\n";
        
        // Subheading font
        echo "    --font-secondary: " . $this->get_font_family_css( 'subheading', $settings ) . ";\n";
        echo "    --subheading-weight: " . ( $settings['subheading_weight'] ?? '600' ) . ";\n";
        
        // Paragraph font (override theme's --font-body)
        echo "    --font-body: " . $this->get_font_family_css( 'paragraph', $settings ) . ";\n";
        echo "    --paragraph-weight: " . ( $settings['paragraph_weight'] ?? '400' ) . ";\n";
        
        // Link font
        echo "    --font-link: " . $this->get_font_family_css( 'link', $settings ) . ";\n";
        echo "    --link-weight: " . ( $settings['link_weight'] ?? '500' ) . ";\n";
        
        // Button font (override theme's --font-ui)
        echo "    --font-ui: " . $this->get_font_family_css( 'button', $settings ) . ";\n";
        echo "    --button-weight: " . ( $settings['button_weight'] ?? '600' ) . ";\n";
        
        echo "}\n";
        
        // Apply fonts to elements with font weights
        echo "\n/* Typography Application */\n";
        echo "h1, h2, h3, h4, h5, h6 { font-family: var(--font-headline) !important; font-weight: var(--heading-weight) !important; }\n";
        echo "body, p { font-family: var(--font-body) !important; font-weight: var(--paragraph-weight) !important; }\n";
        echo "a { font-weight: var(--link-weight) !important; }\n";
        echo "button, .button, .btn, input[type='submit'] { font-family: var(--font-ui) !important; font-weight: var(--button-weight) !important; }\n";
        
        echo "</style>\n";
        echo "<!-- /Beyond Borders Font Styles -->\n\n";
    }

    /**
     * Get font family CSS value
     */
    private function get_font_family_css( $element, $settings ) {
        $source = $settings[$element . '_source'] ?? 'google';
        
        if ( $source === 'google' ) {
            $font = $settings[$element . '_google_font'] ?? 'Inter';
            // Add fallback fonts
            if ( strpos( $font, 'serif' ) !== false || in_array( $font, array( 'Playfair Display', 'Lora', 'Merriweather', 'Crimson Text' ) ) ) {
                return "'{$font}', Georgia, serif";
            } else {
                return "'{$font}', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
            }
            
        } elseif ( $source === 'custom' ) {
            $custom_name = $settings[$element . '_custom_name'] ?? '';
            if ( !empty( $custom_name ) ) {
                return "'{$custom_name}', sans-serif";
            }
            
        } elseif ( $source === 'system' ) {
            return "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif";
        }
        
        return "Inter, sans-serif";
    }

    /**
     * Generate @font-face CSS
     */
    private function generate_font_face( $font_name, $file_ids ) {
        if ( empty( $file_ids ) ) {
            return '';
        }
        
        $css = "\n@font-face {\n";
        $css .= "    font-family: '{$font_name}';\n";
        
        $src_values = array();
        
        foreach ( $file_ids as $file_id ) {
            $file_url = wp_get_attachment_url( $file_id );
            if ( !$file_url ) {
                continue;
            }
            
            $extension = pathinfo( $file_url, PATHINFO_EXTENSION );
            
            $format = 'woff';
            if ( $extension === 'woff2' ) {
                $format = 'woff2';
            } elseif ( $extension === 'ttf' ) {
                $format = 'truetype';
            }
            
            $src_values[] = "url('{$file_url}') format('{$format}')";
        }
        
        if ( !empty( $src_values ) ) {
            $css .= "    src: " . implode( ",\n         ", $src_values ) . ";\n";
            $css .= "    font-display: swap;\n";
            $css .= "}\n";
            
            return $css;
        }
        
        return '';
    }
}

// Initialize the font manager
new Beyond_Borders_Font_Manager();

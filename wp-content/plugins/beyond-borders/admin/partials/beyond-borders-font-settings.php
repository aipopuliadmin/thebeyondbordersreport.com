<?php
/**
 * Font Settings Page
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get current font settings
$font_settings = get_option( 'beyond_borders_font_settings', array() );

// Default values
$defaults = array(
    'heading_source' => 'google',
    'heading_google_font' => 'Playfair Display',
    'heading_weight' => '700',
    'heading_custom_name' => '',
    'heading_custom_files' => array(),
    
    'subheading_source' => 'google',
    'subheading_google_font' => 'Lora',
    'subheading_weight' => '600',
    'subheading_custom_name' => '',
    'subheading_custom_files' => array(),
    
    'paragraph_source' => 'google',
    'paragraph_google_font' => 'Inter',
    'paragraph_weight' => '400',
    'paragraph_custom_name' => '',
    'paragraph_custom_files' => array(),
    
    'link_source' => 'google',
    'link_google_font' => 'Inter',
    'link_weight' => '500',
    'link_custom_name' => '',
    'link_custom_files' => array(),
    
    'button_source' => 'google',
    'button_google_font' => 'Inter',
    'button_weight' => '600',
    'button_custom_name' => '',
    'button_custom_files' => array(),
);

$font_settings = wp_parse_args( $font_settings, $defaults );

// Popular Google Fonts list
$google_fonts = array(
    'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins',
    'Raleway', 'Nunito', 'Work Sans', 'Merriweather', 'Lora', 'Playfair Display',
    'Source Sans Pro', 'PT Sans', 'Oswald', 'Ubuntu', 'Roboto Condensed',
    'Noto Sans', 'Rubik', 'DM Sans', 'Karla', 'Space Grotesk',
    'Crimson Text', 'Libre Baskerville', 'Cormorant', 'EB Garamond',
    'Quicksand', 'Josefin Sans', 'Mukta', 'Barlow', 'Manrope',
    'Outfit', 'Plus Jakarta Sans', 'Sora', 'Epilogue', 'Satoshi',
    'IBM Plex Sans', 'Red Hat Display', 'Lexend', 'Archivo', 'Hind'
);

// Font weights
$font_weights = array(
    '100' => 'Thin (100)',
    '200' => 'Extra Light (200)',
    '300' => 'Light (300)',
    '400' => 'Regular (400)',
    '500' => 'Medium (500)',
    '600' => 'Semi Bold (600)',
    '700' => 'Bold (700)',
    '800' => 'Extra Bold (800)',
    '900' => 'Black (900)',
);

// Handle form submission
if ( isset( $_POST['beyond_borders_fonts_save'] ) && check_admin_referer( 'beyond_borders_fonts_action', 'beyond_borders_fonts_nonce' ) ) {
    
    $new_settings = array();
    
    // Typography elements
    $elements = array( 'heading', 'subheading', 'paragraph', 'link', 'button' );
    
    foreach ( $elements as $element ) {
        $new_settings[$element . '_source'] = sanitize_text_field( $_POST[$element . '_source'] ?? 'google' );
        $new_settings[$element . '_google_font'] = sanitize_text_field( $_POST[$element . '_google_font'] ?? 'Inter' );
        $new_settings[$element . '_weight'] = sanitize_text_field( $_POST[$element . '_weight'] ?? '400' );
        $new_settings[$element . '_custom_name'] = sanitize_text_field( $_POST[$element . '_custom_name'] ?? '' );
        
        // Handle custom font files (comma-separated attachment IDs)
        $new_settings[$element . '_custom_files'] = array();
        if ( !empty( $_POST[$element . '_custom_files'] ) ) {
            $file_ids = explode( ',', sanitize_text_field( $_POST[$element . '_custom_files'] ) );
            $new_settings[$element . '_custom_files'] = array_map( 'intval', $file_ids );
        }
    }
    
    update_option( 'beyond_borders_font_settings', $new_settings );
    $font_settings = $new_settings;
    
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Font settings saved successfully!', 'beyond-borders' ) . '</p></div>';
}

// Function to render font selector
function render_font_selector( $element, $label, $settings, $google_fonts, $font_weights ) {
    $source = $settings[$element . '_source'] ?? 'google';
    $google_font = $settings[$element . '_google_font'] ?? 'Inter';
    $weight = $settings[$element . '_weight'] ?? '400';
    $custom_name = $settings[$element . '_custom_name'] ?? '';
    $custom_files = $settings[$element . '_custom_files'] ?? array();
    ?>
    
    <div class="bb-font-section">
        <h3><?php echo esc_html( $label ); ?></h3>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label><?php _e( 'Font Source', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <label class="bb-radio-label">
                        <input type="radio" name="<?php echo esc_attr( $element ); ?>_source" value="google" 
                               class="bb-font-source" data-element="<?php echo esc_attr( $element ); ?>"
                               <?php checked( $source, 'google' ); ?>>
                        <span><?php _e( 'Google Fonts', 'beyond-borders' ); ?></span>
                    </label>
                    
                    <label class="bb-radio-label">
                        <input type="radio" name="<?php echo esc_attr( $element ); ?>_source" value="custom" 
                               class="bb-font-source" data-element="<?php echo esc_attr( $element ); ?>"
                               <?php checked( $source, 'custom' ); ?>>
                        <span><?php _e( 'Custom Font Upload', 'beyond-borders' ); ?></span>
                    </label>
                    
                    <label class="bb-radio-label">
                        <input type="radio" name="<?php echo esc_attr( $element ); ?>_source" value="system" 
                               class="bb-font-source" data-element="<?php echo esc_attr( $element ); ?>"
                               <?php checked( $source, 'system' ); ?>>
                        <span><?php _e( 'System Font', 'beyond-borders' ); ?></span>
                    </label>
                </td>
            </tr>
            
            <!-- Google Fonts Option -->
            <tr class="bb-google-font-row bb-font-option-<?php echo esc_attr( $element ); ?>" 
                style="display: <?php echo $source === 'google' ? 'table-row' : 'none'; ?>;">
                <th scope="row">
                    <label><?php _e( 'Select Google Font', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <select name="<?php echo esc_attr( $element ); ?>_google_font" class="bb-google-font-select" style="width: 300px;">
                        <?php foreach ( $google_fonts as $font ) : ?>
                            <option value="<?php echo esc_attr( $font ); ?>" <?php selected( $google_font, $font ); ?>>
                                <?php echo esc_html( $font ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <p class="description">
                        <a href="https://fonts.google.com/" target="_blank"><?php _e( 'Browse Google Fonts →', 'beyond-borders' ); ?></a>
                    </p>
                </td>
            </tr>
            
            <!-- Custom Font Option -->
            <tr class="bb-custom-font-row bb-font-option-<?php echo esc_attr( $element ); ?>" 
                style="display: <?php echo $source === 'custom' ? 'table-row' : 'none'; ?>;">
                <th scope="row">
                    <label><?php _e( 'Custom Font Name', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="<?php echo esc_attr( $element ); ?>_custom_name" 
                           value="<?php echo esc_attr( $custom_name ); ?>" 
                           class="regular-text" 
                           placeholder="<?php esc_attr_e( 'e.g., My Custom Font', 'beyond-borders' ); ?>">
                    <p class="description"><?php _e( 'Enter a name for your custom font', 'beyond-borders' ); ?></p>
                </td>
            </tr>
            
            <tr class="bb-custom-font-row bb-font-option-<?php echo esc_attr( $element ); ?>" 
                style="display: <?php echo $source === 'custom' ? 'table-row' : 'none'; ?>;">
                <th scope="row">
                    <label><?php _e( 'Upload Font Files', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <button type="button" class="button bb-upload-font-btn" data-element="<?php echo esc_attr( $element ); ?>">
                        <?php _e( 'Upload Font Files', 'beyond-borders' ); ?>
                    </button>
                    
                    <input type="hidden" name="<?php echo esc_attr( $element ); ?>_custom_files" 
                           id="<?php echo esc_attr( $element ); ?>_custom_files" 
                           value="<?php echo esc_attr( implode( ',', $custom_files ) ); ?>">
                    
                    <div id="<?php echo esc_attr( $element ); ?>_font_preview" class="bb-font-preview">
                        <?php if ( !empty( $custom_files ) ) : ?>
                            <?php foreach ( $custom_files as $file_id ) : ?>
                                <?php 
                                $file_url = wp_get_attachment_url( $file_id );
                                $file_name = basename( $file_url );
                                ?>
                                <div class="bb-font-file">
                                    <span class="dashicons dashicons-media-document"></span>
                                    <a href="<?php echo esc_url( $file_url ); ?>" target="_blank">
                                        <?php echo esc_html( $file_name ); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <p class="description">
                        <?php _e( 'Upload .woff, .woff2, or .ttf font files. For best compatibility, upload both WOFF2 and WOFF formats.', 'beyond-borders' ); ?>
                    </p>
                </td>
            </tr>
            
            <!-- Font Weight -->
            <tr class="bb-font-weight-row">
                <th scope="row">
                    <label><?php _e( 'Font Weight', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <select name="<?php echo esc_attr( $element ); ?>_weight" style="width: 200px;">
                        <?php foreach ( $font_weights as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $weight, $value ); ?>>
                                <?php echo esc_html( $label ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <!-- Live Preview -->
            <tr>
                <th scope="row">
                    <label><?php _e( 'Preview', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <div class="bb-font-preview-box" id="preview_<?php echo esc_attr( $element ); ?>">
                        <p class="bb-preview-text" data-element="<?php echo esc_attr( $element ); ?>">
                            <?php 
                            if ( $element === 'heading' ) {
                                echo 'The Quick Brown Fox Jumps Over the Lazy Dog';
                            } elseif ( $element === 'paragraph' ) {
                                echo 'The travel retail industry continues to evolve with innovative approaches to passenger experience and duty-free shopping.';
                            } else {
                                echo 'Sample Text Preview';
                            }
                            ?>
                        </p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <?php
}
?>

<div class="wrap bb-font-settings">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <p class="description" style="margin-bottom: 30px;">
        <?php _e( 'Configure typography settings for your website. Choose between Google Fonts or upload custom font files.', 'beyond-borders' ); ?>
    </p>
    
    <form method="post" action="">
        <?php wp_nonce_field( 'beyond_borders_fonts_action', 'beyond_borders_fonts_nonce' ); ?>
        
        <div class="bb-font-settings-container">
            
            <?php render_font_selector( 'heading', __( 'Headings (H1-H6)', 'beyond-borders' ), $font_settings, $google_fonts, $font_weights ); ?>
            
            <?php render_font_selector( 'subheading', __( 'Subheadings', 'beyond-borders' ), $font_settings, $google_fonts, $font_weights ); ?>
            
            <?php render_font_selector( 'paragraph', __( 'Paragraphs & Body Text', 'beyond-borders' ), $font_settings, $google_fonts, $font_weights ); ?>
            
            <?php render_font_selector( 'link', __( 'Links', 'beyond-borders' ), $font_settings, $google_fonts, $font_weights ); ?>
            
            <?php render_font_selector( 'button', __( 'Buttons', 'beyond-borders' ), $font_settings, $google_fonts, $font_weights ); ?>
            
        </div>
        
        <p class="submit">
            <input type="submit" name="beyond_borders_fonts_save" class="button button-primary" 
                   value="<?php esc_attr_e( 'Save Font Settings', 'beyond-borders' ); ?>">
            
            <a href="<?php echo esc_url( add_query_arg( 'reset', '1' ) ); ?>" class="button" 
               onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to reset all font settings to defaults?', 'beyond-borders' ); ?>');">
                <?php _e( 'Reset to Defaults', 'beyond-borders' ); ?>
            </a>
        </p>
    </form>
</div>

<style>
.bb-font-settings-container {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin-bottom: 20px;
}

.bb-font-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #e5e5e5;
}

.bb-font-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.bb-font-section h3 {
    margin-top: 0;
    color: #003265;
    font-size: 18px;
}

.bb-radio-label {
    display: inline-block;
    margin-right: 20px;
}

.bb-radio-label input {
    margin-right: 5px;
}

.bb-font-preview {
    margin-top: 10px;
}

.bb-font-file {
    display: inline-block;
    padding: 8px 12px;
    background: #f0f0f1;
    border-radius: 4px;
    margin: 5px 5px 0 0;
}

.bb-font-file .dashicons {
    color: #2271b1;
    font-size: 16px;
    width: 16px;
    height: 16px;
    vertical-align: middle;
}

.bb-font-preview-box {
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-height: 60px;
}

.bb-preview-text {
    margin: 0;
    font-size: 18px;
    line-height: 1.6;
}

.bb-google-font-select {
    font-size: 14px;
}
</style>

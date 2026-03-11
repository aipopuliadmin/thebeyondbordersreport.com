<?php
/**
 * Stats Panel settings page.
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Save stats settings if form submitted
if ( isset( $_POST['beyond_borders_save_stats'] ) && check_admin_referer( 'beyond_borders_stats_save', 'beyond_borders_stats_nonce' ) ) {
    
    $stats_settings = array(
        'enable_stats_section'     => isset( $_POST['enable_stats_section'] ) ? 1 : 0,
        'auto_calculate'           => isset( $_POST['auto_calculate'] ) ? 1 : 0,
        
        // Reader Stats
        'reader_count'             => sanitize_text_field( $_POST['reader_count'] ?? '' ),
        'reader_label'             => sanitize_text_field( $_POST['reader_label'] ?? '' ),
        'reader_suffix'            => sanitize_text_field( $_POST['reader_suffix'] ?? '' ),
        
        // Contributor Stats
        'contributor_count'        => sanitize_text_field( $_POST['contributor_count'] ?? '' ),
        'contributor_label'        => sanitize_text_field( $_POST['contributor_label'] ?? '' ),
        'contributor_suffix'       => sanitize_text_field( $_POST['contributor_suffix'] ?? '' ),
        
        // Countries Stats
        'countries_count'          => sanitize_text_field( $_POST['countries_count'] ?? '' ),
        'countries_label'          => sanitize_text_field( $_POST['countries_label'] ?? '' ),
        'countries_suffix'         => sanitize_text_field( $_POST['countries_suffix'] ?? '' ),
        
        // Section Content
        'stats_heading'            => sanitize_text_field( $_POST['stats_heading'] ?? '' ),
        'stats_description'        => sanitize_textarea_field( $_POST['stats_description'] ?? '' ),
        'stats_background_color'   => sanitize_hex_color( $_POST['stats_background_color'] ?? '' ),
    );
    
    update_option( 'beyond_borders_stats_settings', $stats_settings );
    echo '<div class="notice notice-success"><p>' . __( 'Stats panel settings saved successfully!', 'beyond-borders' ) . '</p></div>';
}

$stats_settings = get_option( 'beyond_borders_stats_settings', array(
    'enable_stats_section'   => true,
    'auto_calculate'         => false,
    'reader_count'           => '200',
    'reader_label'           => 'Global Readers',
    'reader_suffix'          => 'K+',
    'contributor_count'      => '120',
    'contributor_label'      => 'Expert Contributors',
    'contributor_suffix'     => '+',
    'countries_count'        => '45',
    'countries_label'        => 'Countries Covered',
    'countries_suffix'       => '+',
    'stats_heading'          => 'Trusted by Business Leaders Worldwide',
    'stats_description'      => 'Join thousands of executives, analysts, and decision-makers who rely on Beyond Borders for actionable insights.',
    'stats_background_color' => '#f8f9fa',
) );

// Calculate auto stats if enabled
if ( $stats_settings['auto_calculate'] ?? false ) {
    // Auto-calculate readers from user count
    $user_count = count_users();
    $auto_readers = ceil( $user_count['total_users'] / 1000 ); // Convert to K
    
    // Auto-calculate contributors (users with published posts)
    $contributors = get_users( array(
        'who' => 'authors',
        'has_published_posts' => true,
    ) );
    $auto_contributors = count( $contributors );
    
    // Auto-calculate countries from region taxonomy
    $regions = get_terms( array(
        'taxonomy' => 'region',
        'hide_empty' => true,
    ) );
    $auto_countries = is_array( $regions ) ? count( $regions ) : 0;
}
?>

<div class="wrap beyond-borders-settings">
    <div class="bb-settings-header">
        <h1><?php _e( 'Stats Panel Settings', 'beyond-borders' ); ?></h1>
        <p class="bb-subtitle"><?php _e( 'Configure the statistics panel displayed on your homepage.', 'beyond-borders' ); ?></p>
    </div>

    <form method="post" action="" class="bb-settings-form">
        <?php wp_nonce_field( 'beyond_borders_stats_save', 'beyond_borders_stats_nonce' ); ?>
        
        <div class="bb-settings-grid">
            <!-- General Settings Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'General Settings', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_stats_section" value="1" <?php checked( $stats_settings['enable_stats_section'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable stats panel on homepage', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="auto_calculate" value="1" <?php checked( $stats_settings['auto_calculate'] ?? false, 1 ); ?> id="auto_calculate_toggle" />
                            <span class="bb-toggle-label"><?php _e( 'Auto-calculate stats from site data', 'beyond-borders' ); ?></span>
                        </label>
                        <p class="bb-help-text"><?php _e( 'Automatically calculate stats from actual site data. Manual values below will override when filled.', 'beyond-borders' ); ?></p>
                    </div>

                    <?php if ( $stats_settings['auto_calculate'] ?? false ) : ?>
                    <div class="bb-auto-stats-preview">
                        <p><strong><?php _e( 'Auto-calculated values:', 'beyond-borders' ); ?></strong></p>
                        <ul>
                            <li><?php printf( __( 'Readers: %s', 'beyond-borders' ), $auto_readers ?? 0 ); ?></li>
                            <li><?php printf( __( 'Contributors: %s', 'beyond-borders' ), $auto_contributors ?? 0 ); ?></li>
                            <li><?php printf( __( 'Countries: %s', 'beyond-borders' ), $auto_countries ?? 0 ); ?></li>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div class="bb-field">
                        <label for="stats_heading" class="bb-label"><?php _e( 'Section Heading', 'beyond-borders' ); ?></label>
                        <input type="text" name="stats_heading" id="stats_heading" class="bb-input" value="<?php echo esc_attr( $stats_settings['stats_heading'] ?? '' ); ?>" />
                    </div>

                    <div class="bb-field">
                        <label for="stats_description" class="bb-label"><?php _e( 'Section Description', 'beyond-borders' ); ?></label>
                        <textarea name="stats_description" id="stats_description" class="bb-textarea" rows="3"><?php echo esc_textarea( $stats_settings['stats_description'] ?? '' ); ?></textarea>
                    </div>

                    <div class="bb-field">
                        <label for="stats_background_color" class="bb-label"><?php _e( 'Background Color', 'beyond-borders' ); ?></label>
                        <input type="text" name="stats_background_color" id="stats_background_color" class="bb-color-picker" value="<?php echo esc_attr( $stats_settings['stats_background_color'] ?? '#f8f9fa' ); ?>" />
                    </div>
                </div>
            </div>

            <!-- Reader Stats Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Reader Statistics', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label for="reader_count" class="bb-label"><?php _e( 'Reader Count', 'beyond-borders' ); ?></label>
                        <input type="text" name="reader_count" id="reader_count" class="bb-input" value="<?php echo esc_attr( $stats_settings['reader_count'] ?? '' ); ?>" placeholder="200" />
                        <p class="bb-help-text"><?php _e( 'Enter the number (e.g., 200 for 200K+)', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label for="reader_suffix" class="bb-label"><?php _e( 'Suffix', 'beyond-borders' ); ?></label>
                        <input type="text" name="reader_suffix" id="reader_suffix" class="bb-input" value="<?php echo esc_attr( $stats_settings['reader_suffix'] ?? '' ); ?>" placeholder="K+" />
                        <p class="bb-help-text"><?php _e( 'Appears after the number (K+, M+, etc.)', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label for="reader_label" class="bb-label"><?php _e( 'Label Text', 'beyond-borders' ); ?></label>
                        <input type="text" name="reader_label" id="reader_label" class="bb-input" value="<?php echo esc_attr( $stats_settings['reader_label'] ?? '' ); ?>" placeholder="Global Readers" />
                    </div>
                </div>
            </div>

            <!-- Contributor Stats Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Contributor Statistics', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label for="contributor_count" class="bb-label"><?php _e( 'Contributor Count', 'beyond-borders' ); ?></label>
                        <input type="text" name="contributor_count" id="contributor_count" class="bb-input" value="<?php echo esc_attr( $stats_settings['contributor_count'] ?? '' ); ?>" placeholder="120" />
                        <p class="bb-help-text"><?php _e( 'Number of expert contributors', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label for="contributor_suffix" class="bb-label"><?php _e( 'Suffix', 'beyond-borders' ); ?></label>
                        <input type="text" name="contributor_suffix" id="contributor_suffix" class="bb-input" value="<?php echo esc_attr( $stats_settings['contributor_suffix'] ?? '' ); ?>" placeholder="+" />
                    </div>

                    <div class="bb-field">
                        <label for="contributor_label" class="bb-label"><?php _e( 'Label Text', 'beyond-borders' ); ?></label>
                        <input type="text" name="contributor_label" id="contributor_label" class="bb-input" value="<?php echo esc_attr( $stats_settings['contributor_label'] ?? '' ); ?>" placeholder="Expert Contributors" />
                    </div>
                </div>
            </div>

            <!-- Countries Stats Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Countries Statistics', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label for="countries_count" class="bb-label"><?php _e( 'Countries Count', 'beyond-borders' ); ?></label>
                        <input type="text" name="countries_count" id="countries_count" class="bb-input" value="<?php echo esc_attr( $stats_settings['countries_count'] ?? '' ); ?>" placeholder="45" />
                        <p class="bb-help-text"><?php _e( 'Number of countries covered', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label for="countries_suffix" class="bb-label"><?php _e( 'Suffix', 'beyond-borders' ); ?></label>
                        <input type="text" name="countries_suffix" id="countries_suffix" class="bb-input" value="<?php echo esc_attr( $stats_settings['countries_suffix'] ?? '' ); ?>" placeholder="+" />
                    </div>

                    <div class="bb-field">
                        <label for="countries_label" class="bb-label"><?php _e( 'Label Text', 'beyond-borders' ); ?></label>
                        <input type="text" name="countries_label" id="countries_label" class="bb-input" value="<?php echo esc_attr( $stats_settings['countries_label'] ?? '' ); ?>" placeholder="Countries Covered" />
                    </div>
                </div>
            </div>
        </div>

        <div class="bb-settings-footer">
            <button type="submit" name="beyond_borders_save_stats" class="button button-primary button-hero">
                <?php _e( 'Save Stats Settings', 'beyond-borders' ); ?>
            </button>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize color picker
    $('.bb-color-picker').wpColorPicker();
    
    // Toggle auto-calculate preview
    $('#auto_calculate_toggle').on('change', function() {
        if ($(this).is(':checked')) {
            $('form').submit();
        }
    });
});
</script>

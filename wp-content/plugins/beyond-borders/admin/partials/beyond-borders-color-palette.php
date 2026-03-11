<?php
/**
 * Color palette management page.
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Save colors if form submitted
if ( isset( $_POST['beyond_borders_save_colors'] ) && check_admin_referer( 'beyond_borders_colors_save', 'beyond_borders_colors_nonce' ) ) {
    
    $colors = array(
        'navy_deep'      => sanitize_hex_color( $_POST['navy_deep'] ),
        'navy_primary'   => sanitize_hex_color( $_POST['navy_primary'] ),
        'gold_primary'   => sanitize_hex_color( $_POST['gold_primary'] ),
        'gold_champagne' => sanitize_hex_color( $_POST['gold_champagne'] ),
        'cream'          => sanitize_hex_color( $_POST['cream'] ),
        'charcoal_dark'  => sanitize_hex_color( $_POST['charcoal_dark'] ),
    );
    
    update_option( 'beyond_borders_theme_colors', $colors );
    echo '<div class="notice notice-success"><p>' . __( 'Color palette saved successfully!', 'beyond-borders' ) . '</p></div>';
}

$colors = get_option( 'beyond_borders_theme_colors', array(
    'navy_deep'      => '#0A1628',
    'navy_primary'   => '#003366',
    'gold_primary'   => '#C9A961',
    'gold_champagne' => '#D4AF37',
    'cream'          => '#FAF8F5',
    'charcoal_dark'  => '#1A1A2E',
) );
?>

<div class="wrap">
    <h1><?php _e( 'Color Palette', 'beyond-borders' ); ?></h1>
    <p><?php _e( 'Customize the editorial platform color scheme. These colors are used throughout your theme.', 'beyond-borders' ); ?></p>

    <form method="post" action="">
        <?php wp_nonce_field( 'beyond_borders_colors_save', 'beyond_borders_colors_nonce' ); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="navy_deep"><?php _e( 'Navy Deep', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="navy_deep" id="navy_deep" value="<?php echo esc_attr( $colors['navy_deep'] ); ?>" class="color-picker" />
                    <p class="description"><?php _e( 'Used for hero backgrounds and footer', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="navy_primary"><?php _e( 'Navy Primary', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="navy_primary" id="navy_primary" value="<?php echo esc_attr( $colors['navy_primary'] ); ?>" class="color-picker" />
                    <p class="description"><?php _e( 'Navigation and primary buttons', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="gold_primary"><?php _e( 'Gold Primary', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="gold_primary" id="gold_primary" value="<?php echo esc_attr( $colors['gold_primary'] ); ?>" class="color-picker" />
                    <p class="description"><?php _e( 'Headings, icons, and premium accents', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="gold_champagne"><?php _e( 'Gold Champagne', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="gold_champagne" id="gold_champagne" value="<?php echo esc_attr( $colors['gold_champagne'] ); ?>" class="color-picker" />
                    <p class="description"><?php _e( 'CTAs and highlights', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="cream"><?php _e( 'Cream', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="cream" id="cream" value="<?php echo esc_attr( $colors['cream'] ); ?>" class="color-picker" />
                    <p class="description"><?php _e( 'Warm section backgrounds', 'beyond-borders' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="charcoal_dark"><?php _e( 'Charcoal Dark', 'beyond-borders' ); ?></label>
                </th>
                <td>
                    <input type="text" name="charcoal_dark" id="charcoal_dark" value="<?php echo esc_attr( $colors['charcoal_dark'] ); ?>" class="color-picker" />
                    <p class="description"><?php _e( 'Dark section backgrounds', 'beyond-borders' ); ?></p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="beyond_borders_save_colors" class="button button-primary" value="<?php _e( 'Save Color Palette', 'beyond-borders' ); ?>" />
        </p>
    </form>

    <script>
        jQuery(document).ready(function($) {
            $('.color-picker').wpColorPicker();
        });
    </script>
</div>

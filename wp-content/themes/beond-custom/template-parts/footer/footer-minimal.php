<?php
/**
 * Minimal Footer Template
 * Clean, simple footer with essential information only
 *
 * @package Beond_Custom
 */

$footer_settings = get_option( 'beyond_borders_footer_settings', array() );
$header_settings = get_option( 'beyond_borders_header_settings', array() );

// Get logo for light theme (use color logo)
$logo_image_id = $header_settings['logo_image_id'] ?? 0;
$logo_url = $logo_image_id ? wp_get_attachment_image_url( $logo_image_id, 'medium' ) : '';
?>

<footer id="colophon" class="site-footer footer-minimal">
    <div class="footer-minimal-wrapper">
        <div class="footer-minimal-content">
            <div class="footer-minimal-brand">
                <?php if ( $logo_url ) : ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo-link">
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="footer-logo" style="max-width: 150px; height: auto; margin-bottom: 10px;" />
                    </a>
                <?php else : ?>
                    <h3 class="site-title"><?php bloginfo( 'name' ); ?></h3>
                <?php endif; ?>
                <p class="site-tagline"><?php bloginfo( 'description' ); ?></p>
            </div>

            <div class="footer-minimal-main">
                <?php if ( ! empty( $footer_settings['show_social_links'] ) ) : ?>
                <div class="footer-social-links">
                    <?php
                    $social_links = array(
                        'social_facebook'  => array( 'icon' => 'facebook', 'label' => 'Facebook' ),
                        'social_twitter'   => array( 'icon' => 'twitter', 'label' => 'Twitter' ),
                        'social_instagram' => array( 'icon' => 'instagram', 'label' => 'Instagram' ),
                        'social_linkedin'  => array( 'icon' => 'linkedin', 'label' => 'LinkedIn' ),
                    );
                    
                    $header_settings = get_option( 'beyond_borders_header_settings', array() );
                    
                    foreach ( $social_links as $key => $data ) {
                        if ( ! empty( $header_settings[$key] ) ) {
                            printf(
                                '<a href="%s" class="social-link" aria-label="%s" target="_blank" rel="noopener noreferrer"><i class="icon-%s"></i></a>',
                                esc_url( $header_settings[$key] ),
                                esc_attr( $data['label'] ),
                                esc_attr( $data['icon'] )
                            );
                        }
                    }
                    ?>
                </div>
                <?php endif; ?>

                <div class="footer-minimal-copyright">
                    <?php
                    if ( ! empty( $footer_settings['copyright_text'] ) ) {
                        echo esc_html( $footer_settings['copyright_text'] );
                    } else {
                        printf(
                            esc_html__( '© %1$s %2$s', 'beond-custom' ),
                            date( 'Y' ),
                            get_bloginfo( 'name' )
                        );
                    }
                    ?>
                </div>

                <?php if ( has_nav_menu( 'footer' ) ) : ?>
                <nav class="footer-minimal-nav">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer',
                        'menu_class'     => 'footer-minimal-menu',
                        'container'      => false,
                        'depth'          => 1,
                    ) );
                    ?>
                </nav>
                <?php else : ?>
                <div class="footer-minimal-links">
                    <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'beond-custom' ); ?></a>
                    <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'beond-custom' ); ?></a>
                    <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'beond-custom' ); ?></a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->

<?php
/**
 * Magazine Footer Template
 * Editorial-style footer with extensive content areas
 *
 * @package Beond_Custom
 */

$footer_settings = get_option( 'beyond_borders_footer_settings', array() );
$header_settings = get_option( 'beyond_borders_header_settings', array() );

// Get logo for light theme (use color logo)
$logo_image_id = $header_settings['logo_image_id'] ?? 0;
$logo_url = $logo_image_id ? wp_get_attachment_image_url( $logo_image_id, 'medium' ) : '';
?>

<footer id="colophon" class="site-footer footer-magazine">
    
    <?php if ( ! empty( $footer_settings['show_newsletter_signup'] ) ) : ?>
    <!-- Newsletter Section -->
    <div class="footer-newsletter-section">
        <div class="footer-newsletter-container">
            <div class="newsletter-editorial-content">
                <div class="newsletter-editorial-text">
                    <h2 class="newsletter-editorial-title">
                        <?php echo esc_html( $footer_settings['newsletter_title'] ?? 'Stay Informed' ); ?>
                    </h2>
                    <p class="newsletter-editorial-description">
                        <?php echo esc_html( $footer_settings['newsletter_description'] ?? 'Get our weekly digest of expert insights, analysis, and perspectives delivered to your inbox.' ); ?>
                    </p>
                </div>
                <form class="newsletter-editorial-form" method="post" action="">
                    <?php wp_nonce_field( 'newsletter_subscription', 'newsletter_nonce' ); ?>
                    <div class="newsletter-form-group">
                        <input type="email" name="newsletter_email" placeholder="<?php esc_attr_e( 'Your email address', 'beond-custom' ); ?>" required>
                        <button type="submit" class="btn-newsletter">
                            <?php esc_html_e( 'Subscribe', 'beond-custom' ); ?>
                        </button>
                    </div>
                    <p class="newsletter-privacy-note">
                        <?php esc_html_e( 'By subscribing, you agree to our Privacy Policy and consent to receive updates.', 'beond-custom' ); ?>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer Widgets / Editorial Grid -->
    <div class="footer-editorial-grid">
        <div class="footer-editorial-container">
            <div class="footer-grid-wrapper">
                
                <div class="footer-grid-main">
                    <div class="footer-about">
                        <?php if ( $logo_url ) : ?>
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo-link">
                                <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="footer-logo" style="max-width: 180px; height: auto; margin-bottom: 15px;" />
                            </a>
                        <?php else : ?>
                            <h3 class="footer-logo"><?php bloginfo( 'name' ); ?></h3>
                        <?php endif; ?>
                        <?php if ( ! empty( $footer_settings['footer_tagline'] ) ) : ?>
                            <p class="footer-tagline"><?php echo esc_html( $footer_settings['footer_tagline'] ); ?></p>
                        <?php endif; ?>
                        <p class="footer-description">
                            <?php echo esc_html( get_bloginfo( 'description' ) ?: 'Your source for global business insights, travel industry analysis, and luxury retail perspectives.' ); ?>
                        </p>
                        
                        <?php if ( ! empty( $footer_settings['show_social_links'] ) ) : ?>
                        <div class="footer-social-editorial">
                            <h4><?php esc_html_e( 'Follow Us', 'beond-custom' ); ?></h4>
                            <div class="social-icons-editorial">
                                <?php
                                $social_links = array(
                                    'social_linkedin'  => array( 'label' => 'LinkedIn', 'svg' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>' ),
                                    'social_twitter'   => array( 'label' => 'Twitter', 'svg' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' ),
                                    'social_instagram' => array( 'label' => 'Instagram', 'svg' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>' ),
                                    'social_youtube'   => array( 'label' => 'YouTube', 'svg' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>' ),
                                    'social_facebook'  => array( 'label' => 'Facebook', 'svg' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>' ),
                                );
                                
                                $header_settings = get_option( 'beyond_borders_header_settings', array() );
                                
                                foreach ( $social_links as $key => $data ) {
                                    if ( ! empty( $header_settings[$key] ) ) {
                                        printf(
                                            '<a href="%s" class="social-editorial-link" aria-label="%s" target="_blank" rel="noopener noreferrer">%s</a>',
                                            esc_url( $header_settings[$key] ),
                                            esc_attr( $data['label'] ),
                                            $data['svg']
                                        );
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                $columns = min( $footer_settings['footer_columns'] ?? 3, 3 );
                $has_widgets = false;
                for ( $i = 1; $i <= $columns; $i++ ) {
                    if ( is_active_sidebar( 'footer-' . $i ) ) {
                        $has_widgets = true;
                        break;
                    }
                }
                
                if ( $has_widgets ) :
                    for ( $i = 1; $i <= $columns; $i++ ) :
                        if ( is_active_sidebar( 'footer-' . $i ) ) :
                ?>
                <div class="footer-grid-column">
                    <?php dynamic_sidebar( 'footer-' . $i ); ?>
                </div>
                <?php
                        endif;
                    endfor;
                else :
                ?>
                    <!-- Default Columns -->
                    <div class="footer-grid-column">
                        <h4 class="footer-column-title"><?php esc_html_e( 'Topics', 'beond-custom' ); ?></h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo esc_url( home_url( '/category/aviation/' ) ); ?>"><?php esc_html_e( 'Aviation', 'beond-custom' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/category/travel-retail/' ) ); ?>"><?php esc_html_e( 'Travel Retail', 'beond-custom' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/category/luxury/' ) ); ?>"><?php esc_html_e( 'Luxury', 'beond-custom' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/category/technology/' ) ); ?>"><?php esc_html_e( 'Technology', 'beond-custom' ); ?></a></li>
                        </ul>
                    </div>

                    <div class="footer-grid-column">
                        <h4 class="footer-column-title"><?php esc_html_e( 'About', 'beond-custom' ); ?></h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'beond-custom' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/authors/' ) ); ?>"><?php esc_html_e( 'Our Team', 'beond-custom' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'beond-custom' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/advertise/' ) ); ?>"><?php esc_html_e( 'Advertise', 'beond-custom' ); ?></a></li>
                        </ul>
                    </div>

                    <div class="footer-grid-column">
                        <h4 class="footer-column-title"><?php esc_html_e( 'Legal', 'beond-custom' ); ?></h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'beond-custom' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms of Use', 'beond-custom' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/cookies/' ) ); ?>"><?php esc_html_e( 'Cookie Policy', 'beond-custom' ); ?></a></li>
                        </ul>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

    <!-- Footer Bottom Bar -->
    <div class="footer-bottom-bar">
        <div class="footer-bottom-container">
            <div class="footer-copyright">
                <?php
                if ( ! empty( $footer_settings['copyright_text'] ) ) {
                    echo esc_html( $footer_settings['copyright_text'] );
                } else {
                    printf(
                        esc_html__( '© %1$s %2$s. All rights reserved.', 'beond-custom' ),
                        date( 'Y' ),
                        get_bloginfo( 'name' )
                    );
                }
                ?>
            </div>
            <div class="footer-meta-links">
                <a href="<?php echo esc_url( home_url( '/sitemap/' ) ); ?>"><?php esc_html_e( 'Sitemap', 'beond-custom' ); ?></a>
                <a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>"><?php esc_html_e( 'RSS', 'beond-custom' ); ?></a>
            </div>
        </div>
    </div>
    
</footer><!-- #colophon -->

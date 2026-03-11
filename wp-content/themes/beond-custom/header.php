<?php
/**
 * The header for our theme
 *
 * @package Beond_Custom
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'beond-custom' ); ?></a>

    <?php
    // Get header settings from plugin
    $header_settings = get_option( 'beyond_borders_header_settings', array() );
    ?>

    <?php if ( ! empty( $header_settings['enable_top_bar'] ) ) : ?>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <span class="top-bar-text">
                    <?php echo esc_html( $header_settings['top_bar_text'] ?? 'Premium Business Publication' ); ?>
                </span>
                
                <?php if ( ! empty( $header_settings['show_social_links'] ) ) : ?>
                <div class="social-links">
                    <?php do_action( 'beond_social_links' ); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <?php
    // Get header layout from plugin settings
    $header_layout = $header_settings['header_layout'] ?? 'default';
    
    // Load the appropriate header layout template
    get_template_part( 'template-parts/header/header', $header_layout );
    ?>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay-container" id="mobile-menu-overlay">
        <div class="mobile-menu-overlay-header">
            <button class="mobile-menu-close" aria-label="<?php esc_attr_e( 'Close menu', 'beond-custom' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mobile-menu-overlay-content">
            <?php
            // Get mobile menu
            $mobile_menu_args = array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'mobile-menu-list',
                'fallback_cb'    => '__return_false',
                'walker'         => new Mobile_Accordion_Walker(),
            );
            
            if ( ! empty( $header_settings['selected_menu'] ) ) {
                $mobile_menu_args['menu'] = $header_settings['selected_menu'];
            }
            
            wp_nav_menu( $mobile_menu_args );
            ?>
        </div>
    </div>

    <?php if ( ! empty( $header_settings['show_search'] ) ) : 
        $search_style = $header_settings['search_style'] ?? 'fullscreen';
    ?>
    <!-- Search Modal -->
    <div class="search-modal search-modal-<?php echo esc_attr( $search_style ); ?>" id="search-modal">
        <div class="search-modal-content">
            <button class="search-close">&times;</button>
            <?php get_search_form(); ?>
        </div>
    </div>
    <?php endif; ?>

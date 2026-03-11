<?php
/**
 * Header Layout: Centered
 * Centered logo with navigation and actions
 *
 * @package Beond_Custom
 */

    $header_settings = get_option( 'beyond_borders_header_settings', array() );
    $logo_type = $header_settings['logo_type'] ?? 'icon';
    $logo_image_id = $header_settings['logo_image_id'] ?? 0;
    $dark_logo_image_id = $header_settings['dark_logo_image_id'] ?? 0;
    $logo_text = $header_settings['logo_text'] ?? '';
    $show_site_name = $header_settings['show_site_name'] ?? true;
    $logo_width = $header_settings['logo_width'] ?? 200;
    $menu_position = $header_settings['menu_position'] ?? 'center';
    $enable_sticky = $header_settings['enable_sticky_header'] ?? true;
?>

<header class="modern-header header-centered menu-position-<?php echo esc_attr( $menu_position ); ?><?php echo $enable_sticky ? ' header-sticky' : ''; ?>">
    <div class="header-wrapper">
        
        <!-- Brand Logo - Centered -->
        <div class="centered-logo-wrapper">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-logo">
                <?php if ( $logo_type === 'image' && $logo_image_id ) : ?>
                    <?php 
                    $logo_url = wp_get_attachment_image_url( $logo_image_id, 'full' );
                    $dark_logo_url = $dark_logo_image_id ? wp_get_attachment_image_url( $dark_logo_image_id, 'full' ) : $logo_url;
                    ?>
                    <?php if ( $logo_url ) : ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" 
                             alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" 
                             class="logo-image logo-light"
                             style="max-width: <?php echo esc_attr( $logo_width ); ?>px; height: auto;" />
                        <?php if ( $dark_logo_url && $dark_logo_image_id ) : ?>
                        <img src="<?php echo esc_url( $dark_logo_url ); ?>" 
                             alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" 
                             class="logo-image logo-dark"
                             style="max-width: <?php echo esc_attr( $logo_width ); ?>px; height: auto; display: none;" />
                        <?php endif; ?>
                    <?php endif; ?>
                <?php elseif ( $logo_type === 'text' ) : ?>
                    <span class="logo-text-only"><?php echo esc_html( $logo_text ?: get_bloginfo( 'name' ) ); ?></span>
                <?php else : ?>
                    <div class="logo-icon">
                        <?php 
                        $site_name = get_bloginfo( 'name' );
                        $initials = '';
                        $words = explode( ' ', $site_name );
                        foreach ( $words as $word ) {
                            if ( ! empty( $word ) ) {
                                $initials .= strtoupper( substr( $word, 0, 1 ) );
                                if ( strlen( $initials ) >= 2 ) break;
                            }
                        }
                        echo esc_html( $initials ?: 'BB' );
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( $show_site_name && $logo_type !== 'text' ) : ?>
                    <span><?php echo esc_html( $logo_text ?: get_bloginfo( 'name' ) ); ?></span>
                <?php endif; ?>
            </a>
        </div>
        
        <!-- Header Navigation -->
        <nav class="header-nav">
            <?php
            $menu_args = array(
                'menu_id'        => 'primary-menu',
                'menu_class'     => '',
                'container'      => false,
                'fallback_cb'    => '__return_false',
                'walker'         => new Desktop_Nav_Walker(),
            );
            
            // Use selected menu or default to theme location
            if ( ! empty( $header_settings['selected_menu'] ) ) {
                $menu_args['menu'] = $header_settings['selected_menu'];
            } else {
                $menu_args['theme_location'] = 'primary';
            }
            
            wp_nav_menu( $menu_args );
            ?>
        </nav>
        
        <!-- Header Actions -->
        <div class="header-actions">
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle mobile menu', 'beond-custom' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <?php if ( ! empty( $header_settings['show_search'] ) ) : ?>
            <div class="search-icon">
                <button class="search-toggle" aria-label="<?php esc_attr_e( 'Toggle search', 'beond-custom' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                        <circle cx="9" cy="9" r="7" stroke-width="2"/>
                        <path d="M14 14L18 18" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if ( ! empty( $header_settings['show_theme_toggle'] ) ) : ?>
            <!-- Theme Toggle -->
            <div class="theme-toggle" id="themeToggle" aria-label="<?php esc_attr_e( 'Toggle theme', 'beond-custom' ); ?>">
                <svg class="theme-icon-light" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                </svg>
                <svg class="theme-icon-dark" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </div>
            <?php endif; ?>
            
            <?php if ( ! empty( $header_settings['show_subscribe_button'] ) ) : ?>
            <a href="<?php echo esc_url( $header_settings['subscribe_button_url'] ?? '#subscribe' ); ?>" class="btn-subscribe">
                <?php echo esc_html( $header_settings['subscribe_button_text'] ?? __( 'Subscribe', 'beond-custom' ) ); ?>
            </a>
            <?php endif; ?>
        </div>
        
    </div><!-- .header-wrapper -->
</header><!-- .modern-header -->

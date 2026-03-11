<?php
/**
 * Homepage settings page.
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Save homepage settings if form submitted
if ( isset( $_POST['beyond_borders_save_homepage'] ) && check_admin_referer( 'beyond_borders_homepage_save', 'beyond_borders_homepage_nonce' ) ) {
    
    $homepage_settings = array(
        // Hero Section
        'enable_hero'             => isset( $_POST['enable_hero'] ) ? 1 : 0,
        'hero_order'              => absint( $_POST['hero_order'] ?? 1 ),
        'hero_heading'            => sanitize_text_field( $_POST['hero_heading'] ?? '' ),
        'hero_posts_count'        => absint( $_POST['hero_posts_count'] ?? 5 ),
        'hero_category'           => absint( $_POST['hero_category'] ?? 0 ),
        
        // Categories Section
        'enable_categories'       => isset( $_POST['enable_categories'] ) ? 1 : 0,
        'categories_order'        => absint( $_POST['categories_order'] ?? 2 ),
        'categories_heading'      => sanitize_text_field( $_POST['categories_heading'] ?? '' ),
        'categories_description'  => sanitize_textarea_field( $_POST['categories_description'] ?? '' ),
        'categories_display_count' => absint( $_POST['categories_display_count'] ?? 6 ),
        
        // Featured Grid Section
        'enable_featured_grid'    => isset( $_POST['enable_featured_grid'] ) ? 1 : 0,
        'featured_grid_order'     => absint( $_POST['featured_grid_order'] ?? 3 ),
        'featured_grid_heading'   => sanitize_text_field( $_POST['featured_grid_heading'] ?? '' ),
        'featured_grid_posts'     => absint( $_POST['featured_grid_posts'] ?? 6 ),
        
        // Trending Section
        'enable_trending'         => isset( $_POST['enable_trending'] ) ? 1 : 0,
        'trending_order'          => absint( $_POST['trending_order'] ?? 4 ),
        'trending_heading'        => sanitize_text_field( $_POST['trending_heading'] ?? '' ),
        'trending_posts'          => absint( $_POST['trending_posts'] ?? 5 ),
        
        // Latest News Section
        'enable_latest_news'      => isset( $_POST['enable_latest_news'] ) ? 1 : 0,
        'latest_news_order'       => absint( $_POST['latest_news_order'] ?? 5 ),
        'latest_news_heading'     => sanitize_text_field( $_POST['latest_news_heading'] ?? '' ),
        'latest_news_posts'       => absint( $_POST['latest_news_posts'] ?? 8 ),
        'latest_news_button_text' => sanitize_text_field( $_POST['latest_news_button_text'] ?? '' ),
        'latest_news_button_link' => esc_url_raw( $_POST['latest_news_button_link'] ?? '' ),
        
        // Opinion Section
        'enable_opinion'          => isset( $_POST['enable_opinion'] ) ? 1 : 0,
        'opinion_order'           => absint( $_POST['opinion_order'] ?? 6 ),
        'opinion_heading'         => sanitize_text_field( $_POST['opinion_heading'] ?? '' ),
        'opinion_posts'           => absint( $_POST['opinion_posts'] ?? 5 ),
        
        // Stats Section (visibility control)
        'enable_stats'            => isset( $_POST['enable_stats'] ) ? 1 : 0,
        'stats_order'             => absint( $_POST['stats_order'] ?? 7 ),
        
        // Authors Section
        'enable_authors'          => isset( $_POST['enable_authors'] ) ? 1 : 0,
        'authors_order'           => absint( $_POST['authors_order'] ?? 8 ),
        'authors_heading'         => sanitize_text_field( $_POST['authors_heading'] ?? '' ),
        'authors_description'     => sanitize_textarea_field( $_POST['authors_description'] ?? '' ),
        'authors_display_count'   => absint( $_POST['authors_display_count'] ?? 4 ),
        
        // General Settings
        'homepage_bg_color'       => sanitize_hex_color( $_POST['homepage_bg_color'] ?? '' ),
    );
    
    update_option( 'beyond_borders_homepage_settings', $homepage_settings );
    echo '<div class="notice notice-success"><p>' . __( 'Homepage settings saved successfully!', 'beyond-borders' ) . '</p></div>';
}

$homepage_settings = get_option( 'beyond_borders_homepage_settings', array(
    'enable_hero'             => true,
    'hero_order'              => 1,
    'hero_heading'            => '',
    'hero_posts_count'        => 5,
    'hero_category'           => 0,
    
    'enable_categories'       => true,
    'categories_order'        => 2,
    'categories_heading'      => 'Explore Our Coverage',
    'categories_description'  => 'Deep insights across luxury retail, travel, and global business.',
    'categories_display_count' => 6,
    
    'enable_featured_grid'    => true,
    'featured_grid_order'     => 3,
    'featured_grid_heading'   => 'Featured Analysis',
    'featured_grid_posts'     => 6,
    
    'enable_trending'         => true,
    'trending_order'          => 4,
    'trending_heading'        => 'Trending Now',
    'trending_posts'          => 5,
    
    'enable_latest_news'      => true,
    'latest_news_order'       => 5,
    'latest_news_heading'     => 'Latest Insights',
    'latest_news_posts'       => 8,
    'latest_news_button_text' => 'View All Articles',
    'latest_news_button_link' => '/blog',
    
    'enable_opinion'          => true,
    'opinion_order'           => 6,
    'opinion_heading'         => 'Opinion & Analysis',
    'opinion_posts'           => 5,
    
    'enable_stats'            => true,
    'stats_order'             => 7,
    
    'enable_authors'          => true,
    'authors_order'           => 8,
    'authors_heading'         => 'Featured Contributors',
    'authors_description'     => 'Insights from industry leaders, analysts, and experts worldwide.',
    'authors_display_count'   => 4,
    
    'homepage_bg_color'       => '#FFFFFF',
) );
?>

<div class="wrap beyond-borders-settings">
    <div class="bb-settings-header">
        <h1><?php _e( 'Homepage Settings', 'beyond-borders' ); ?></h1>
        <p class="bb-subtitle"><?php _e( 'Configure your homepage sections, order, and content.', 'beyond-borders' ); ?></p>
    </div>

    <form method="post" action="" class="bb-settings-form">
        <?php wp_nonce_field( 'beyond_borders_homepage_save', 'beyond_borders_homepage_nonce' ); ?>
        
        <div class="bb-section-notice">
            <p><strong><?php _e( 'Section Order:', 'beyond-borders' ); ?></strong> <?php _e( 'Enter numbers (1, 2, 3, etc.) to control the order sections appear on your homepage. Lower numbers appear first.', 'beyond-borders' ); ?></p>
        </div>
        
        <div class="bb-settings-grid">
            <!-- Hero Section Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Hero Section', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_hero" value="1" <?php checked( $homepage_settings['enable_hero'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable hero section', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="hero_order" class="bb-label"><?php _e( 'Section Order', 'beyond-borders' ); ?></label>
                        <input type="number" name="hero_order" id="hero_order" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['hero_order'] ?? 1 ); ?>" min="1" max="20" />
                        <p class="bb-help-text"><?php _e( 'Position on page (1 = first)', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label for="hero_heading" class="bb-label"><?php _e( 'Section Heading (optional)', 'beyond-borders' ); ?></label>
                        <input type="text" name="hero_heading" id="hero_heading" class="bb-input" value="<?php echo esc_attr( $homepage_settings['hero_heading'] ?? '' ); ?>" placeholder="Leave empty for no heading" />
                    </div>

                    <div class="bb-field">
                        <label for="hero_posts_count" class="bb-label"><?php _e( 'Number of Stories', 'beyond-borders' ); ?></label>
                        <input type="number" name="hero_posts_count" id="hero_posts_count" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['hero_posts_count'] ?? 5 ); ?>" min="1" max="10" />
                        <p class="bb-help-text"><?php _e( '1 main story + remaining as secondary stories', 'beyond-borders' ); ?></p>
                    </div>

                    <div class="bb-field">
                        <label for="hero_category" class="bb-label"><?php _e( 'Featured Category (optional)', 'beyond-borders' ); ?></label>
                        <?php 
                        wp_dropdown_categories( array(
                            'name'            => 'hero_category',
                            'id'              => 'hero_category',
                            'class'           => 'bb-select',
                            'show_option_all' => __( 'All Categories (Most Recent)', 'beyond-borders' ),
                            'selected'        => $homepage_settings['hero_category'] ?? 0,
                            'hierarchical'    => true,
                            'hide_empty'      => false,
                        ) );
                        ?>
                    </div>
                </div>
            </div>

            <!-- Categories Section Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Categories Showcase', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_categories" value="1" <?php checked( $homepage_settings['enable_categories'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable categories section', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="categories_order" class="bb-label"><?php _e( 'Section Order', 'beyond-borders' ); ?></label>
                        <input type="number" name="categories_order" id="categories_order" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['categories_order'] ?? 2 ); ?>" min="1" max="20" />
                    </div>

                    <div class="bb-field">
                        <label for="categories_heading" class="bb-label"><?php _e( 'Section Heading', 'beyond-borders' ); ?></label>
                        <input type="text" name="categories_heading" id="categories_heading" class="bb-input" value="<?php echo esc_attr( $homepage_settings['categories_heading'] ?? '' ); ?>" />
                    </div>

                    <div class="bb-field">
                        <label for="categories_description" class="bb-label"><?php _e( 'Section Description', 'beyond-borders' ); ?></label>
                        <textarea name="categories_description" id="categories_description" class="bb-textarea" rows="2"><?php echo esc_textarea( $homepage_settings['categories_description'] ?? '' ); ?></textarea>
                    </div>

                    <div class="bb-field">
                        <label for="categories_display_count" class="bb-label"><?php _e( 'Categories to Display', 'beyond-borders' ); ?></label>
                        <input type="number" name="categories_display_count" id="categories_display_count" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['categories_display_count'] ?? 6 ); ?>" min="1" max="12" />
                    </div>

                    <p class="bb-info-box">
                        <?php _e( 'Category icons and colors are managed in WordPress Categories page.', 'beyond-borders' ); ?>
                        <a href="<?php echo admin_url( 'edit-tags.php?taxonomy=category' ); ?>"><?php _e( 'Edit Categories →', 'beyond-borders' ); ?></a>
                    </p>
                </div>
            </div>

            <!-- Featured Grid Section Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Featured Grid', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_featured_grid" value="1" <?php checked( $homepage_settings['enable_featured_grid'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable featured grid section', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="featured_grid_order" class="bb-label"><?php _e( 'Section Order', 'beyond-borders' ); ?></label>
                        <input type="number" name="featured_grid_order" id="featured_grid_order" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['featured_grid_order'] ?? 3 ); ?>" min="1" max="20" />
                    </div>

                    <div class="bb-field">
                        <label for="featured_grid_heading" class="bb-label"><?php _e( 'Section Heading', 'beyond-borders' ); ?></label>
                        <input type="text" name="featured_grid_heading" id="featured_grid_heading" class="bb-input" value="<?php echo esc_attr( $homepage_settings['featured_grid_heading'] ?? '' ); ?>" />
                    </div>

                    <div class="bb-field">
                        <label for="featured_grid_posts" class="bb-label"><?php _e( 'Number of Posts', 'beyond-borders' ); ?></label>
                        <input type="number" name="featured_grid_posts" id="featured_grid_posts" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['featured_grid_posts'] ?? 6 ); ?>" min="1" max="12" />
                    </div>
                </div>
            </div>

            <!-- Trending Section Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Trending Section', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_trending" value="1" <?php checked( $homepage_settings['enable_trending'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable trending section', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="trending_order" class="bb-label"><?php _e( 'Section Order', 'beyond-borders' ); ?></label>
                        <input type="number" name="trending_order" id="trending_order" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['trending_order'] ?? 4 ); ?>" min="1" max="20" />
                    </div>

                    <div class="bb-field">
                        <label for="trending_heading" class="bb-label"><?php _e( 'Section Heading', 'beyond-borders' ); ?></label>
                        <input type="text" name="trending_heading" id="trending_heading" class="bb-input" value="<?php echo esc_attr( $homepage_settings['trending_heading'] ?? '' ); ?>" />
                    </div>

                    <div class="bb-field">
                        <label for="trending_posts" class="bb-label"><?php _e( 'Number of Posts', 'beyond-borders' ); ?></label>
                        <input type="number" name="trending_posts" id="trending_posts" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['trending_posts'] ?? 5 ); ?>" min="1" max="10" />
                    </div>
                </div>
            </div>

            <!-- Latest News Section Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Latest News Grid', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_latest_news" value="1" <?php checked( $homepage_settings['enable_latest_news'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable latest news section', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="latest_news_order" class="bb-label"><?php _e( 'Section Order', 'beyond-borders' ); ?></label>
                        <input type="number" name="latest_news_order" id="latest_news_order" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['latest_news_order'] ?? 5 ); ?>" min="1" max="20" />
                    </div>

                    <div class="bb-field">
                        <label for="latest_news_heading" class="bb-label"><?php _e( 'Section Heading', 'beyond-borders' ); ?></label>
                        <input type="text" name="latest_news_heading" id="latest_news_heading" class="bb-input" value="<?php echo esc_attr( $homepage_settings['latest_news_heading'] ?? '' ); ?>" />
                    </div>

                    <div class="bb-field">
                        <label for="latest_news_posts" class="bb-label"><?php _e( 'Number of Posts', 'beyond-borders' ); ?></label>
                        <input type="number" name="latest_news_posts" id="latest_news_posts" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['latest_news_posts'] ?? 8 ); ?>" min="1" max="16" />
                    </div>

                    <div class="bb-field">
                        <label for="latest_news_button_text" class="bb-label"><?php _e( 'Button Text', 'beyond-borders' ); ?></label>
                        <input type="text" name="latest_news_button_text" id="latest_news_button_text" class="bb-input" value="<?php echo esc_attr( $homepage_settings['latest_news_button_text'] ?? '' ); ?>" />
                    </div>

                    <div class="bb-field">
                        <label for="latest_news_button_link" class="bb-label"><?php _e( 'Button Link', 'beyond-borders' ); ?></label>
                        <input type="text" name="latest_news_button_link" id="latest_news_button_link" class="bb-input" value="<?php echo esc_attr( $homepage_settings['latest_news_button_link'] ?? '' ); ?>" placeholder="/blog or https://example.com" />
                    </div>
                </div>
            </div>

            <!-- Opinion Section Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Opinion & Analysis', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_opinion" value="1" <?php checked( $homepage_settings['enable_opinion'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable opinion section', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="opinion_order" class="bb-label"><?php _e( 'Section Order', 'beyond-borders' ); ?></label>
                        <input type="number" name="opinion_order" id="opinion_order" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['opinion_order'] ?? 6 ); ?>" min="1" max="20" />
                    </div>

                    <div class="bb-field">
                        <label for="opinion_heading" class="bb-label"><?php _e( 'Section Heading', 'beyond-borders' ); ?></label>
                        <input type="text" name="opinion_heading" id="opinion_heading" class="bb-input" value="<?php echo esc_attr( $homepage_settings['opinion_heading'] ?? '' ); ?>" />
                    </div>

                    <div class="bb-field">
                        <label for="opinion_posts" class="bb-label"><?php _e( 'Number of Posts', 'beyond-borders' ); ?></label>
                        <input type="number" name="opinion_posts" id="opinion_posts" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['opinion_posts'] ?? 5 ); ?>" min="1" max="10" />
                    </div>
                </div>
            </div>

            <!-- Stats Section Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Stats Panel', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_stats" value="1" <?php checked( $homepage_settings['enable_stats'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable stats panel', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="stats_order" class="bb-label"><?php _e( 'Section Order', 'beyond-borders' ); ?></label>
                        <input type="number" name="stats_order" id="stats_order" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['stats_order'] ?? 7 ); ?>" min="1" max="20" />
                    </div>

                    <p class="bb-info-box">
                        <?php _e( 'Stats content (numbers, labels) are managed in the Stats Panel settings page.', 'beyond-borders' ); ?>
                        <a href="<?php echo admin_url( 'admin.php?page=beyond-borders-stats' ); ?>"><?php _e( 'Configure Stats →', 'beyond-borders' ); ?></a>
                    </p>
                </div>
            </div>

            <!-- Authors Section Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'Featured Authors', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label class="bb-toggle">
                            <input type="checkbox" name="enable_authors" value="1" <?php checked( $homepage_settings['enable_authors'] ?? true, 1 ); ?> />
                            <span class="bb-toggle-label"><?php _e( 'Enable authors section', 'beyond-borders' ); ?></span>
                        </label>
                    </div>

                    <div class="bb-field">
                        <label for="authors_order" class="bb-label"><?php _e( 'Section Order', 'beyond-borders' ); ?></label>
                        <input type="number" name="authors_order" id="authors_order" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['authors_order'] ?? 8 ); ?>" min="1" max="20" />
                    </div>

                    <div class="bb-field">
                        <label for="authors_heading" class="bb-label"><?php _e( 'Section Heading', 'beyond-borders' ); ?></label>
                        <input type="text" name="authors_heading" id="authors_heading" class="bb-input" value="<?php echo esc_attr( $homepage_settings['authors_heading'] ?? '' ); ?>" />
                    </div>

                    <div class="bb-field">
                        <label for="authors_description" class="bb-label"><?php _e( 'Section Description', 'beyond-borders' ); ?></label>
                        <textarea name="authors_description" id="authors_description" class="bb-textarea" rows="2"><?php echo esc_textarea( $homepage_settings['authors_description'] ?? '' ); ?></textarea>
                    </div>

                    <div class="bb-field">
                        <label for="authors_display_count" class="bb-label"><?php _e( 'Authors to Display', 'beyond-borders' ); ?></label>
                        <input type="number" name="authors_display_count" id="authors_display_count" class="bb-input-small" value="<?php echo esc_attr( $homepage_settings['authors_display_count'] ?? 4 ); ?>" min="1" max="12" />
                    </div>
                </div>
            </div>

            <!-- General Settings Card -->
            <div class="bb-settings-card">
                <div class="bb-card-header">
                    <h2><?php _e( 'General Settings', 'beyond-borders' ); ?></h2>
                </div>
                <div class="bb-card-body">
                    <div class="bb-field">
                        <label for="homepage_bg_color" class="bb-label"><?php _e( 'Background Color', 'beyond-borders' ); ?></label>
                        <input type="text" name="homepage_bg_color" id="homepage_bg_color" class="bb-color-picker" value="<?php echo esc_attr( $homepage_settings['homepage_bg_color'] ?? '#FFFFFF' ); ?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="bb-settings-footer">
            <button type="submit" name="beyond_borders_save_homepage" class="button button-primary button-hero">
                <?php _e( 'Save Homepage Settings', 'beyond-borders' ); ?>
            </button>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize color picker
    $('.bb-color-picker').wpColorPicker();
});
</script>

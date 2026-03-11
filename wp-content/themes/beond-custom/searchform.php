<?php
/**
 * Search form template
 *
 * @package Beond_Custom
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label>
        <span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'beond-custom' ); ?></span>
        <input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search...', 'beond-custom' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    </label>
    <button type="submit" class="search-submit">
        <span class="screen-reader-text"><?php esc_html_e( 'Search', 'beond-custom' ); ?></span>
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
            <circle cx="9" cy="9" r="7" stroke-width="2"/>
            <path d="M14 14L18 18" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </button>
</form>

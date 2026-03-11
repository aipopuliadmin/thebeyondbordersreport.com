<?php
/**
 * The template for displaying the footer
 *
 * @package Beond_Custom
 */

?>

    <?php
    // Get footer settings from plugin
    $footer_settings = get_option( 'beyond_borders_footer_settings', array() );
    
    // Get footer layout choice (default to 'default')
    $footer_layout = $footer_settings['footer_layout'] ?? 'default';
    
    // Load the appropriate footer template
    get_template_part( 'template-parts/footer/footer', $footer_layout );
    ?>

    <?php if ( ! empty( $footer_settings['show_back_to_top'] ) ) : ?>
    <!-- Back to Top -->
    <button id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'beond-custom' ); ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
        </svg>
    </button>
    <?php endif; ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

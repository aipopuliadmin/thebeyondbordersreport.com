<?php
/**
 * Template Part: Key Points Display
 *
 * @package Beond_Custom
 */

$key_points = beond_get_key_points();

if ( ! $key_points ) {
    return;
}
?>

<div class="article-key-points">
    <div class="key-points-header">
        <h3 class="key-points-title"><?php _e( 'KEY POINTS', 'beond-custom' ); ?></h3>
    </div>
    
    <ul class="key-points-list">
        <?php foreach ( $key_points as $point ) : ?>
            <li class="key-point-item">
                <span class="key-point-bullet">•</span>
                <span class="key-point-text"><?php echo esc_html( $point ); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

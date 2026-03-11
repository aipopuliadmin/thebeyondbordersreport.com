<?php
/**
 * Author Card Template
 * 
 * Displays author information with badge, bio, and social links
 * Supports BB Desk, Guest Author, and Podcast Guest types
 *
 * @package Beond_Custom
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if author should be displayed
if ( ! beond_should_show_author() ) {
    return;
}

// Get author data
$author_id = get_the_author_meta( 'ID' );
$author_name = get_the_author();
$author_bio = get_the_author_meta( 'description' );
$author_type = get_user_meta( $author_id, 'author_type', true ) ?: 'bb_desk';
$job_title = get_user_meta( $author_id, 'author_job_title', true );
$company = get_user_meta( $author_id, 'author_company', true );
$linkedin = get_user_meta( $author_id, 'author_linkedin', true );
$twitter = get_user_meta( $author_id, 'author_twitter', true );
$podcast_episode = get_user_meta( $author_id, 'author_podcast_episode', true );
$author_url = get_author_posts_url( $author_id );

?>

<div class="author-card" data-author-type="<?php echo esc_attr( $author_type ); ?>">
    <div class="author-card-inner">
        
        <!-- Author Avatar -->
        <div class="author-avatar-wrapper">
            <a href="<?php echo esc_url( $author_url ); ?>" class="author-avatar-link">
                <?php echo get_avatar( $author_id, 96, '', $author_name, array( 'class' => 'author-avatar-img' ) ); ?>
            </a>
        </div>
        
        <!-- Author Info -->
        <div class="author-info">
            
            <!-- Author Type Badge -->
            <div class="author-badge-wrapper">
                <?php echo beond_get_author_badge( $author_id ); ?>
            </div>
            
            <!-- Author Name & Title -->
            <div class="author-header">
                <h3 class="author-name">
                    <a href="<?php echo esc_url( $author_url ); ?>">
                        <?php echo esc_html( $author_name ); ?>
                    </a>
                </h3>
                
                <?php if ( $job_title || $company ) : ?>
                    <p class="author-title">
                        <?php 
                        if ( $job_title && $company ) {
                            echo esc_html( $job_title ) . ' <span class="author-title-separator">at</span> ' . esc_html( $company );
                        } elseif ( $job_title ) {
                            echo esc_html( $job_title );
                        } else {
                            echo esc_html( $company );
                        }
                        ?>
                    </p>
                <?php endif; ?>
                
                <?php if ( $podcast_episode && $author_type === 'podcast_guest' ) : ?>
                    <p class="author-podcast-episode">
                        <svg class="podcast-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 14c2.21 0 4-1.79 4-4V6c0-2.21-1.79-4-4-4S8 3.79 8 6v4c0 2.21 1.79 4 4 4z"/>
                            <path d="M19 10v2c0 3.87-3.13 7-7 7s-7-3.13-7-7v-2"/>
                            <path d="M12 19v5M8 24h8"/>
                        </svg>
                        <?php echo esc_html( $podcast_episode ); ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- Author Bio -->
            <?php if ( $author_bio ) : ?>
                <div class="author-bio">
                    <?php echo wp_kses_post( wpautop( $author_bio ) ); ?>
                </div>
            <?php endif; ?>
            
            <!-- Author Social Links & Actions -->
            <div class="author-footer">
                
                <?php if ( $linkedin || $twitter ) : ?>
                    <div class="author-social-links">
                        <?php if ( $linkedin ) : ?>
                            <a href="<?php echo esc_url( $linkedin ); ?>" 
                               class="author-social-link" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="<?php echo esc_attr( sprintf( __( '%s on LinkedIn', 'beond-custom' ), $author_name ) ); ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ( $twitter ) : ?>
                            <a href="<?php echo esc_url( $twitter ); ?>" 
                               class="author-social-link" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="<?php echo esc_attr( sprintf( __( '%s on Twitter/X', 'beond-custom' ), $author_name ) ); ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <a href="<?php echo esc_url( $author_url ); ?>" class="author-view-all">
                    <?php 
                    if ( $author_type === 'bb_desk' ) {
                        _e( 'More from BB Desk', 'beond-custom' );
                    } else {
                        printf( __( 'More by %s', 'beond-custom' ), esc_html( $author_name ) );
                    }
                    ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                
            </div>
            
        </div>
        
    </div>
</div>

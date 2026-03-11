<?php
/**
 * Template part for displaying author bio in single posts
 *
 * @package Beond_Custom
 */

$author_id = get_the_author_meta('ID');
$author_description = get_the_author_meta('description');

// Only show if author has a bio
if ( ! $author_description ) {
    return;
}

// Get author's post count
$author_posts_count = count_user_posts( $author_id, 'post', true );

// Get author's social links (from user meta)
$twitter = get_the_author_meta('twitter');
$linkedin = get_the_author_meta('linkedin');
$website = get_the_author_meta('url');
?>

<div class="author-bio-section">
    <div class="author-bio-card">
        <div class="author-bio-header">
            <div class="author-bio-avatar">
                <?php echo get_avatar( $author_id, 120 ); ?>
            </div>
            <div class="author-bio-info">
                <h3 class="author-bio-name">
                    <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
                        <?php echo esc_html( get_the_author() ); ?>
                    </a>
                </h3>
                <?php
                $author_title = get_the_author_meta('author_title');
                if ( $author_title ) :
                    ?>
                    <p class="author-bio-title"><?php echo esc_html( $author_title ); ?></p>
                <?php endif; ?>
                <p class="author-bio-posts-count">
                    <?php
                    printf(
                        _n(
                            '%s article published',
                            '%s articles published',
                            $author_posts_count,
                            'beond-custom'
                        ),
                        number_format_i18n( $author_posts_count )
                    );
                    ?>
                </p>
            </div>
        </div>
        
        <div class="author-bio-description">
            <?php echo wp_kses_post( wpautop( $author_description ) ); ?>
        </div>
        
        <?php if ( $twitter || $linkedin || $website ) : ?>
            <div class="author-bio-social">
                <?php if ( $website ) : ?>
                    <a href="<?php echo esc_url( $website ); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer author" 
                       class="author-social-link"
                       aria-label="Visit website">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        Website
                    </a>
                <?php endif; ?>
                
                <?php if ( $twitter ) : ?>
                    <a href="<?php echo esc_url( 'https://twitter.com/' . ltrim( $twitter, '@' ) ); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="author-social-link"
                       aria-label="Follow on Twitter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                        </svg>
                        Twitter
                    </a>
                <?php endif; ?>
                
                <?php if ( $linkedin ) : ?>
                    <a href="<?php echo esc_url( $linkedin ); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="author-social-link"
                       aria-label="Connect on LinkedIn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path>
                            <circle cx="4" cy="4" r="2"></circle>
                        </svg>
                        LinkedIn
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="author-bio-footer">
            <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" 
               class="view-all-posts-btn">
                View All Articles
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </div>
</div>
        </a>
    </div>
</div><!-- .author-bio -->

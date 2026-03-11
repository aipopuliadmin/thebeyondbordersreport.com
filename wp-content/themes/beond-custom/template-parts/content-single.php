<?php
/**
 * Template part for displaying single posts
 *
 * @package Beond_Custom
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>
    
    <!-- Article Header -->
    <header class="article-header">
        <!-- Category Badge -->
        <?php
        $categories = get_the_category();
        if ( ! empty( $categories ) ) :
            $category = $categories[0];
            ?>
            <span class="category-badge"><?php echo esc_html( strtoupper( $category->name ) ); ?></span>
        <?php endif; ?>
        
        <!-- Title -->
        <h1 class="article-title"><?php the_title(); ?></h1>
        
        <!-- Meta Information -->
        <div class="article-meta">
            <div class="meta-left">
                <span class="author-avatar">
                    <?php echo get_avatar( get_the_author_meta('ID'), 32 ); ?>
                </span>
                <span class="meta-text">
                    <span class="author-prefix">by</span> 
                    <span class="author-name"><?php the_author(); ?></span>
                </span>
                <span class="meta-separator">•</span>
                <time datetime="<?php echo esc_attr( get_the_date('c') ); ?>" class="meta-text">
                    <?php echo get_the_date('d F Y'); ?>
                </time>
                <span class="meta-separator">•</span>
                <span class="meta-text">
                    <?php 
                    $comments_number = get_comments_number();
                    if ( $comments_number == 0 ) {
                        echo '0 Comments';
                    } else {
                        comments_number();
                    }
                    ?>
                </span>
                <span class="meta-separator">•</span>
                <span class="meta-text reading-time">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <?php echo beond_get_reading_time( get_the_ID() ); ?> Minutes Read
                </span>
            </div>
        </div>
    </header>
    
    <!-- Key Points (if available) -->
    <?php get_template_part( 'template-parts/key-points' ); ?>
    
    <!-- Featured Image -->
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="article-featured-image">
            <?php the_post_thumbnail('full', array('alt' => get_the_title())); ?>
        </div>
    <?php endif; ?>

    <!-- Article Content -->
    <div class="article-content">
        <?php
        the_content(
            sprintf(
                wp_kses(
                    __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'beond-custom' ),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post( get_the_title() )
            )
        );
        
        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'beond-custom' ),
                'after'  => '</div>',
            )
        );
        ?>
    </div>
    
    <!-- Article Footer (Tags) -->
    <footer class="article-footer">
        <?php
        $tags = get_the_tags();
        if ( $tags ) :
            ?>
            <div class="article-tags">
                <span class="tags-label"><?php esc_html_e( 'Tags:', 'beond-custom' ); ?></span>
                <div class="tags-list">
                    <?php foreach ( $tags as $tag ) : ?>
                        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" 
                           class="tag-link">
                            <?php echo esc_html( $tag->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </footer>
    
    <!-- Author Card -->
    <?php get_template_part( 'template-parts/author-card' ); ?>
    
</article>

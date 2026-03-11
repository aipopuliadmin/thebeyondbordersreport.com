<?php
/**
 * Template part for displaying related posts
 *
 * @package Beond_Custom
 */

$post_id = get_the_ID();
$categories = get_the_category( $post_id );

if ( ! $categories ) {
    return;
}

$category_ids = wp_list_pluck( $categories, 'term_id' );

// Query for related posts
$related_args = array(
    'post_type'      => 'post',
    'posts_per_page' => 8,
    'post__not_in'   => array( $post_id ),
    'category__in'   => $category_ids,
    'orderby'        => 'rand',
    'no_found_rows'  => true,
);

$related_posts = new WP_Query( $related_args );

if ( ! $related_posts->have_posts() ) {
    wp_reset_postdata();
    return;
}
?>

<section class="related-posts-section">
    <div class="related-posts-header">
        <h2>Related <span class="highlight">Posts</span></h2>
        <div class="related-posts-nav">
            <button class="nav-arrow prev-arrow" aria-label="Previous" data-slider="related-posts">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button class="nav-arrow next-arrow" aria-label="Next" data-slider="related-posts">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
    
    <div class="related-posts-wrapper">
        <div class="related-posts-grid" id="related-posts-slider"><?php
        while ( $related_posts->have_posts() ) :
            $related_posts->the_post();
            
            // Get first category
            $post_categories = get_the_category();
            $category = ! empty( $post_categories ) ? $post_categories[0] : null;
            ?>
            
            <article class="related-post-card">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="related-post-image">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium_large', array('alt' => get_the_title())); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="related-post-content">
                    <?php if ( $category ) : ?>
                        <span class="category-badge">
                            <?php echo esc_html( strtoupper( $category->name ) ); ?>
                        </span>
                    <?php endif; ?>
                    
                    <h3>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>
                    
                    <div class="related-post-meta">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <time datetime="<?php echo esc_attr( get_the_date('c') ); ?>">
                            <?php echo get_the_date('d F Y'); ?>
                        </time>
                    </div>
                </div>
            </article>
            
        <?php endwhile; ?>
    </div>
    </div>
</section>

<?php wp_reset_postdata(); ?>

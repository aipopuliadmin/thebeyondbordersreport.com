<?php
/**
 * The template for displaying Curated Review category archive
 * 
 * Shows posts in the "Curated Review" category with special layout
 * Content on left, Quick Facts/Specs table on right
 * 
 * @package Beond_Custom
 */

get_header();
?>

<main id="primary" class="site-main curated-review-archive">
    <div class="container">
        <div class="content-area">
            
            <?php if ( have_posts() ) : ?>

                <header class="page-header curated-review-header">
                    <div class="header-content">
                        <?php
                        the_archive_title( '<h1 class="page-title">', '</h1>' );
                        the_archive_description( '<div class="archive-description">', '</div>' );
                        ?>
                        <div class="curated-review-intro">
                            <p><?php esc_html_e( 'Expert-curated reviews and recommendations from our team of specialists.', 'beond-custom' ); ?></p>
                        </div>
                    </div>
                </header><!-- .page-header -->

                <div class="curated-review-list">
                    <?php
                    // Start the Loop
                    while ( have_posts() ) :
                        the_post();
                        
                        // Get the subcategory if exists
                        $categories = get_the_category();
                        $subcategory = '';
                        foreach ( $categories as $cat ) {
                            if ( $cat->term_id !== 24 ) { // 24 is Curated Review parent
                                $subcategory = $cat->name;
                                break;
                            }
                        }
                        ?>
                        
                        <article class="curated-review-article" id="post-<?php the_ID(); ?>">
                            
                            <!-- Featured Image -->
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="review-full-image">
                                    <?php the_post_thumbnail( 'large' ); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="review-article-wrapper">
                                
                                <!-- Left Column: Content -->
                                <div class="review-content-left">
                                    <div class="review-header-info">
                                        <?php if ( $subcategory ) : ?>
                                            <div class="review-subcategory">
                                                <?php echo esc_html( $subcategory ); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <h2 class="review-title">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_title(); ?>
                                            </a>
                                        </h2>
                                        
                                        <div class="review-meta">
                                            <span class="review-date">
                                                <?php echo esc_html( get_the_date( 'M d, Y' ) ); ?>
                                            </span>
                                            <span class="review-author">
                                                <?php esc_html_e( 'by', 'beond-custom' ); ?> 
                                                <?php the_author(); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Main Content -->
                                    <div class="review-body-content">
                                        <?php 
                                        the_content();
                                        
                                        // Extract and display verdict section if exists
                                        $content = get_the_content();
                                        if ( strpos( $content, 'verdict' ) !== false || strpos( $content, 'Verdict' ) !== false ) {
                                            echo '<div class="verdict-section">';
                                            echo 'Check full review for detailed verdict';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                    
                                    <a href="<?php the_permalink(); ?>" class="read-full-review-btn">
                                        <?php esc_html_e( 'Read Full Review', 'beond-custom' ); ?> 
                                        <span class="arrow">→</span>
                                    </a>
                                </div>
                                
                                <!-- Right Column: Quick Facts/Specs -->
                                <div class="review-specs-right">
                                    <div class="quick-facts-box">
                                        <h3><?php esc_html_e( 'Quick Facts', 'beond-custom' ); ?></h3>
                                        
                                        <!-- Check for product info in post content -->
                                        <dl class="facts-list">
                                            <dt><?php esc_html_e( 'Category', 'beond-custom' ); ?></dt>
                                            <dd><?php echo esc_html( $subcategory ) ?: 'Curated Selection'; ?></dd>
                                            
                                            <dt><?php esc_html_e( 'Published', 'beond-custom' ); ?></dt>
                                            <dd><?php echo esc_html( get_the_date( 'M d, Y' ) ); ?></dd>
                                            
                                            <dt><?php esc_html_e( 'Reviewer', 'beond-custom' ); ?></dt>
                                            <dd><?php the_author(); ?></dd>
                                        </dl>
                                        
                                        <!-- Specs Table Placeholder -->
                                        <div class="specs-notice">
                                            <p><?php esc_html_e( 'Read the full review for detailed specifications and quick facts table.', 'beond-custom' ); ?></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Rating Box -->
                                    <?php 
                                    // Check if post has a rating in content
                                    $content = get_the_content();
                                    if ( preg_match( '/rating[:\s]*(\d+)\s*\/\s*(\d+|\d\s*stars)/i', $content, $matches ) ) {
                                        $rating = $matches[1];
                                        $max = $matches[2];
                                        echo '<div class="rating-box">';
                                        echo '<h4>Rating</h4>';
                                        echo '<div class="stars">' . str_repeat( '★', (int)$rating ) . str_repeat( '☆', max( 0, 5 - (int)$rating ) ) . '</div>';
                                        echo '<span class="rating-text">' . esc_html( $rating ) . ' / 5</span>';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                        </article>
                        
                        <?php
                    endwhile;
                    ?>
                </div><!-- .curated-review-list -->
                
                <?php
                // Pagination
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => esc_html__( '&larr; Previous', 'beond-custom' ),
                    'next_text' => esc_html__( 'Next &rarr;', 'beond-custom' ),
                ) );
                ?>

            <?php else : ?>
                
                <div class="no-posts-message">
                    <h2><?php esc_html_e( 'No Reviews Found', 'beond-custom' ); ?></h2>
                    <p><?php esc_html_e( 'Sorry, no curated reviews match your criteria.', 'beond-custom' ); ?></p>
                </div>

            <?php endif; ?>
            
        </div><!-- .content-area -->
    </div><!-- .container -->
</main><!-- #primary -->

<style>
/* CSS Variables for Light/Dark Theme */
:root {
    --color-primary: #003265;
    --color-accent: #D4AF37;
    --color-text-primary: #333;
    --color-text-secondary: #666;
    --color-text-muted: #999;
    --color-bg-primary: #fff;
    --color-bg-secondary: #f9f9f9;
    --color-bg-tertiary: #fff9e6;
    --color-border: #ddd;
    --color-border-light: #e0e0e0;
    --color-shadow-light: rgba(0, 50, 101, 0.15);
    --color-shadow-hover: 0 8px 20px var(--color-shadow-light);
    --color-shadow-normal: 0 2px 8px rgba(0, 0, 0, 0.08);
}

@media (prefers-color-scheme: dark) {
    :root {
        --color-text-primary: #e0e0e0;
        --color-text-secondary: #b0b0b0;
        --color-text-muted: #808080;
        --color-bg-primary: #1a1a1a;
        --color-bg-secondary: #2a2a2a;
        --color-bg-tertiary: #2a2800;
        --color-border: #444;
        --color-border-light: #3a3a3a;
        --color-shadow-light: rgba(0, 0, 0, 0.3);
        --color-shadow-normal: 0 2px 8px rgba(0, 0, 0, 0.3);
    }
}

.curated-review-archive {
    padding: 2rem 0;
    background-color: var(--color-bg-primary);
    color: var(--color-text-primary);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.curated-review-header {
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid var(--color-primary);
}

.curated-review-header .page-title {
    color: var(--color-primary);
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.curated-review-intro {
    color: var(--color-text-secondary);
    font-size: 1.1rem;
    max-width: 600px;
}

.curated-review-list {
    display: flex;
    flex-direction: column;
    gap: 4rem;
    margin-bottom: 3rem;
}

.curated-review-article {
    border: 1px solid var(--color-border);
    border-radius: 8px;
    overflow: hidden;
    background: var(--color-bg-primary);
    box-shadow: var(--color-shadow-normal);
    transition: all 0.3s ease;
}

.curated-review-article:hover {
    box-shadow: var(--color-shadow-hover);
}

.review-full-image {
    width: 100%;
    max-height: 400px;
    overflow: hidden;
    background: var(--color-bg-secondary);
}

.review-full-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.review-article-wrapper {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 2rem;
    padding: 2rem;
}

.review-content-left {
    min-width: 0;
}

.review-header-info {
    margin-bottom: 2rem;
}

.review-subcategory {
    display: inline-block;
    background: var(--color-accent);
    color: var(--color-primary);
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    text-transform: uppercase;
}

.review-title {
    margin: 0 0 1rem 0;
    font-size: 1.8rem;
    color: var(--color-primary);
}

.review-title a {
    color: var(--color-primary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.review-title a:hover {
    color: var(--color-accent);
}

.review-meta {
    display: flex;
    gap: 1.5rem;
    font-size: 0.9rem;
    color: var(--color-text-muted);
}

.review-body-content {
    color: var(--color-text-primary);
    line-height: 1.8;
    margin-bottom: 2rem;
    max-height: 300px;
    overflow: hidden;
    position: relative;
}

.review-body-content::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: linear-gradient(to bottom, transparent, var(--color-bg-primary));
    pointer-events: none;
}

.review-body-content p {
    margin-bottom: 1rem;
}

.read-full-review-btn {
    display: inline-block;
    background: var(--color-primary);
    color: white;
    padding: 0.8rem 1.8rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid var(--color-primary);
}

.read-full-review-btn:hover {
    background: var(--color-accent);
    color: var(--color-primary);
    border-color: var(--color-accent);
}

.read-full-review-btn .arrow {
    margin-left: 0.5rem;
}

/* Right Sidebar */
.review-specs-right {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.quick-facts-box,
.rating-box {
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-border-light);
    border-radius: 6px;
    padding: 1.5rem;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.quick-facts-box h3,
.rating-box h4 {
    color: var(--color-primary);
    font-size: 1rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.facts-list {
    margin-bottom: 1rem;
}

.facts-list dt {
    font-weight: 600;
    color: var(--color-primary);
    font-size: 0.85rem;
    text-transform: uppercase;
    margin-top: 0.8rem;
    margin-bottom: 0.2rem;
}

.facts-list dt:first-child {
    margin-top: 0;
}

.facts-list dd {
    color: var(--color-text-secondary);
    margin-left: 0;
    margin-bottom: 0.8rem;
}

.specs-notice {
    background: var(--color-bg-tertiary);
    border-left: 3px solid var(--color-accent);
    padding: 1rem;
    border-radius: 4px;
    font-size: 0.85rem;
    color: var(--color-text-secondary);
}

.specs-notice p {
    margin: 0;
}

.rating-box {
    text-align: center;
}

.stars {
    font-size: 1.8rem;
    color: var(--color-accent);
    margin: 0.8rem 0;
    letter-spacing: 0.2rem;
}

.rating-text {
    display: block;
    color: var(--color-primary);
    font-weight: 700;
    font-size: 1.1rem;
}

.no-posts-message {
    text-align: center;
    padding: 3rem 2rem;
    background: var(--color-bg-secondary);
    border-radius: 8px;
}

.no-posts-message h2 {
    color: var(--color-primary);
    margin-bottom: 1rem;
}

.no-posts-message p {
    color: var(--color-text-secondary);
}

/* Mobile Responsive */
@media (max-width: 900px) {
    .review-article-wrapper {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .review-specs-right {
        flex-direction: row;
    }
    
    .quick-facts-box,
    .rating-box {
        flex: 1;
    }
}

@media (max-width: 768px) {
    .curated-review-header .page-title {
        font-size: 1.8rem;
    }
    
    .review-title {
        font-size: 1.3rem;
    }
    
    .review-article-wrapper {
        padding: 1.5rem;
    }
    
    .review-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .review-specs-right {
        flex-direction: column;
    }
}
</style>

<?php
get_footer();
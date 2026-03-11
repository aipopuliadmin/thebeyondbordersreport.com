<?php
/**
 * The template for displaying search results pages
 *
 * @package Beond_Custom
 */

get_header();

// Get filter parameters for display purposes
$search_query = get_search_query();
$search_in = isset($_GET['search_in']) ? sanitize_text_field($_GET['search_in']) : 'both';
$order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
$date_filter = isset($_GET['date_filter']) ? sanitize_text_field($_GET['date_filter']) : 'all';
?>

<main id="primary" class="site-main search-page">
    <div class="search-container">
        
        <!-- Search Filters -->
        <div class="search-filters">
            <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-filter-form">
                <input type="hidden" name="s" value="<?php echo esc_attr($search_query); ?>">
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label><?php esc_html_e('Search for', 'beond-custom'); ?></label>
                        <input type="text" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="Enter keywords...">
                    </div>
                    
                    <div class="filter-group">
                        <label><?php esc_html_e('Order by Date', 'beond-custom'); ?></label>
                        <select name="orderby">
                            <option value="date" <?php selected($order_by, 'date'); ?>><?php esc_html_e('Newest', 'beond-custom'); ?></option>
                            <option value="oldest" <?php selected($order_by, 'oldest'); ?>><?php esc_html_e('Oldest', 'beond-custom'); ?></option>
                            <option value="title" <?php selected($order_by, 'title'); ?>><?php esc_html_e('Title (A-Z)', 'beond-custom'); ?></option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label><?php esc_html_e('Dates:', 'beond-custom'); ?></label>
                        <select name="date_filter">
                            <option value="all" <?php selected($date_filter, 'all'); ?>><?php esc_html_e('Show All', 'beond-custom'); ?></option>
                            <option value="week" <?php selected($date_filter, 'week'); ?>><?php esc_html_e('Past Week', 'beond-custom'); ?></option>
                            <option value="month" <?php selected($date_filter, 'month'); ?>><?php esc_html_e('Past Month', 'beond-custom'); ?></option>
                            <option value="year" <?php selected($date_filter, 'year'); ?>><?php esc_html_e('Past Year', 'beond-custom'); ?></option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="search-filter-button"><?php esc_html_e('Search', 'beond-custom'); ?></button>
                    </div>
                </div>
                
                <div class="filter-radio-group">
                    <label><?php esc_html_e('Search within:', 'beond-custom'); ?></label>
                    <label>
                        <input type="radio" name="search_in" value="both" <?php checked($search_in, 'both'); ?>>
                        <?php esc_html_e('Headline & Content', 'beond-custom'); ?>
                    </label>
                    <label>
                        <input type="radio" name="search_in" value="title" <?php checked($search_in, 'title'); ?>>
                        <?php esc_html_e('Headline Only', 'beond-custom'); ?>
                    </label>
                    <label>
                        <input type="radio" name="search_in" value="content" <?php checked($search_in, 'content'); ?>>
                        <?php esc_html_e('Content Only', 'beond-custom'); ?>
                    </label>
                </div>
            </form>
        </div>
        
        <?php if ( have_posts() ) : ?>

            <header class="search-header">
                <h1 class="search-title">
                    <?php
                    printf(
                        esc_html__( 'Search Results for "%s"', 'beond-custom' ),
                        '<span>' . get_search_query() . '</span>'
                    );
                    ?>
                </h1>
                <p class="search-count">
                    <?php
                    global $wp_query;
                    printf(
                        esc_html( _n( '%s result found', '%s results found', $wp_query->found_posts, 'beond-custom' ) ),
                        number_format_i18n( $wp_query->found_posts )
                    );
                    ?>
                </p>
            </header>

            <div class="search-results-grid">
                <?php
                while ( have_posts() ) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-card'); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="search-card-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="search-card-content">
                            <?php
                            $categories = get_the_category();
                            if ( ! empty( $categories ) ) :
                                ?>
                                <span class="search-card-category"><?php echo esc_html( $categories[0]->name ); ?></span>
                            <?php endif; ?>
                            
                            <h2 class="search-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="search-card-meta">
                                <span class="author"><?php the_author(); ?></span>
                                <span class="separator">•</span>
                                <span class="date"><?php echo get_the_date('M j, Y'); ?></span>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>

            <div class="search-pagination">
                <?php
                // Get current page number
                $paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
                
                // Build pagination links
                $pagination = paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'current' => max(1, $paged),
                    'total' => $GLOBALS['wp_query']->max_num_pages,
                    'mid_size' => 2,
                    'prev_text' => __('← Previous', 'beond-custom'),
                    'next_text' => __('Next →', 'beond-custom'),
                    'type' => 'list',
                    'add_args' => array(
                        's' => get_search_query(),
                        'orderby' => $order_by,
                        'date_filter' => $date_filter,
                        'search_in' => $search_in,
                    ),
                ));
                
                if ($pagination) {
                    echo $pagination;
                }
                ?>
            </div>

        <?php else : ?>

            <header class="search-header">
                <h1 class="search-title">
                    <?php
                    printf(
                        esc_html__( 'Search Results for "%s"', 'beond-custom' ),
                        '<span>' . get_search_query() . '</span>'
                    );
                    ?>
                </h1>
                <p class="search-count"><?php esc_html_e( 'No results found', 'beond-custom' ); ?></p>
            </header>

            <div class="search-no-results">
                <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'beond-custom' ); ?></p>
                
                <div class="search-form-wrapper">
                    <?php get_search_form(); ?>
                </div>
            </div>

        <?php endif; ?>
        
    </div>
</main>

<?php
get_footer();

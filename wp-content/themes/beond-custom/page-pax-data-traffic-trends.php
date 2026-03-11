<?php
/**
 * Template Name: Pax Data & Traffic Trends
 * 
 * @package Beond_Custom
 */

// Preload Chart.js for better performance
add_action('wp_head', function() {
    echo '<link rel="preload" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" as="script">';
    echo '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">';
    echo '<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>';
}, 1);

get_header();

// Get PAX data handler
$pax_data_handler = new Beyond_Borders_Pax_Data();
$airports = $pax_data_handler->get_airports();

// Get unique years
global $wpdb;
$pax_table = $wpdb->prefix . 'pax_data';
$years = $wpdb->get_col( "SELECT DISTINCT year FROM $pax_table ORDER BY year ASC" );

// Get latest year for default filter
$latest_year = !empty($years) ? max($years) : date('Y');
?>

<div class="single-post-main">
    <div class="container">
        <div class="single-post-layout">
            
            <main id="primary" class="site-main pax-data-traffic-trends-page">
    
    <div class="pax-page-header">
        <h1 class="page-title">
            <svg class="header-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            PAX Data & Traffic Trends
        </h1>
        <p class="page-description">Explore Indian aviation passenger data with interactive visualizations</p>
    </div>

    <!-- Two Column Layout Wrapper -->
    <div class="pax-layout-wrapper">
        
        <!-- Left Sidebar (30%) - Filters and Settings -->
        <aside class="pax-sidebar">
    
    <!-- Filters Section -->
    <div class="pax-filters-container">
        <div class="filter-header">
            <svg class="filter-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            <h3>Filter Data</h3>
        </div>
        <div class="pax-filters">
            <div class="filter-group">
                <label for="filter-iata">IATA Code</label>
                <select id="filter-iata" class="pax-filter-select" aria-describedby="iata-help">
                    <option value="">All IATA Codes</option>
                    <?php 
                    $unique_iata = array_unique(array_filter(array_column($airports, 'iata_code')));
                    sort($unique_iata);
                    foreach ( $unique_iata as $iata ) : ?>
                        <option value="<?php echo esc_attr( $iata ); ?>"><?php echo esc_html( $iata ); ?></option>
                    <?php endforeach; ?>
                </select>
                <span id="iata-help" class="sr-only">Filter by IATA airport code</span>
            </div>

            <div class="filter-group">
                <label for="filter-icao">ICAO Code</label>
                <select id="filter-icao" class="pax-filter-select" aria-describedby="icao-help">
                    <option value="">All ICAO Codes</option>
                    <?php 
                    $unique_icao = array_unique(array_filter(array_column($airports, 'icao_code')));
                    sort($unique_icao);
                    foreach ( $unique_icao as $icao ) : ?>
                        <option value="<?php echo esc_attr( $icao ); ?>"><?php echo esc_html( $icao ); ?></option>
                    <?php endforeach; ?>
                </select>
                <span id="icao-help" class="sr-only">Filter by ICAO airport code</span>
            </div>

            <div class="filter-group">
                <label for="filter-pax-type">Passenger Type</label>
                <select id="filter-pax-type" class="pax-filter-select" aria-describedby="pax-type-help">
                    <option value="">All Types</option>
                    <option value="INTERNATIONAL PASSENGERS">International Passengers</option>
                    <option value="DOMESTIC PASSENGERS">Domestic Passengers</option>
                </select>
                <span id="pax-type-help" class="sr-only">Filter by domestic or international passenger traffic</span>
            </div>

            <div class="filter-group">
                <label for="filter-year-from">Year From</label>
                <select id="filter-year-from" class="pax-filter-select">
                    <option value="">Select Year</option>
                    <?php foreach ( $years as $year ) : ?>
                        <option value="<?php echo esc_attr( $year ); ?>" <?php selected( $year, $latest_year ); ?>><?php echo esc_html( $year ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-year-to">Year To</label>
                <select id="filter-year-to" class="pax-filter-select">
                    <option value="">Select Year</option>
                    <?php foreach ( $years as $year ) : ?>
                        <option value="<?php echo esc_attr( $year ); ?>" <?php selected( $year, $latest_year ); ?>><?php echo esc_html( $year ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-month">Month</label>
                <select id="filter-month" class="pax-filter-select">
                    <option value="">All Months</option>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                </select>
            </div>

            <div class="filter-actions">
                <button id="apply-filters" class="pax-button primary">Apply Filters</button>
                <button id="clear-filters" class="pax-button secondary">Clear</button>
            </div>
        </div>
    </div>

    <!-- Chart Settings Panel -->
    <div id="chart-settings-panel" class="chart-settings-panel">
        <div class="settings-header">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                Chart Customization
            </h3>
            <p class="settings-description">Customize chart appearance and behavior</p>
        </div>
        
        <div class="settings-grid">
            <div class="setting-group">
                <label for="chart-color-scheme">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 2a10 10 0 0 0 0 20 4 4 0 0 0 0-8 4 4 0 0 0 0-8"></path>
                    </svg>
                    <span>Color Scheme</span>
                </label>
                <select id="chart-color-scheme">
                    <option value="default">Default (Brand Colors)</option>
                    <option value="blue">Blue Tones</option>
                    <option value="green">Green Tones</option>
                    <option value="warm">Warm Colors</option>
                    <option value="cool">Cool Colors</option>
                </select>
            </div>

            <div class="setting-group checkbox-group">
                <label for="chart-animation">
                    <input type="checkbox" id="chart-animation" checked>
                    <span class="checkbox-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3l7 7 10-10"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        Enable Animations
                    </span>
                </label>
            </div>

            <div class="setting-group checkbox-group">
                <label for="chart-legend">
                    <input type="checkbox" id="chart-legend" checked>
                    <span class="checkbox-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                            <line x1="3" y1="6" x2="3.01" y2="6"></line>
                            <line x1="3" y1="12" x2="3.01" y2="12"></line>
                            <line x1="3" y1="18" x2="3.01" y2="18"></line>
                        </svg>
                        Show Legend
                    </span>
                </label>
            </div>

            <div class="setting-group checkbox-group">
                <label for="chart-grid">
                    <input type="checkbox" id="chart-grid" checked>
                    <span class="checkbox-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        Show Grid Lines
                    </span>
                </label>
            </div>
        </div>

        <div class="settings-actions">
            <button id="apply-chart-settings" class="pax-button primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Apply Settings
            </button>
        </div>
    </div>

        </aside><!-- .pax-sidebar -->

        <!-- Right Content Area (70%) - Chart and Insights -->
        <div class="pax-main-content">

    <!-- Chart Type Switcher -->
    <div class="chart-controls">
        <div class="chart-type-switcher">
            <button class="chart-type-btn active" data-chart-type="line">
                <svg class="chart-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
                <span>Line Chart</span>
            </button>
            <button class="chart-type-btn" data-chart-type="bar">
                <svg class="chart-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="20" x2="12" y2="10"></line>
                    <line x1="18" y1="20" x2="18" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="16"></line>
                </svg>
                <span>Bar Chart</span>
            </button>
            <button class="chart-type-btn" data-chart-type="pie">
                <svg class="chart-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                    <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                </svg>
                <span>Pie Chart</span>
            </button>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="pax-chart-container" data-lazy-load="true">
        <div class="chart-loading" role="status" aria-live="polite">
            <div class="loading-spinner" aria-hidden="true"></div>
            <p>Loading chart data...</p>
        </div>
        <canvas id="pax-chart" role="img" aria-label="PAX data visualization chart showing passenger trends"></canvas>
    </div>

    <!-- Data Summary -->
    <div class="pax-data-summary">
        <h2>Data Insights</h2>
        <div class="summary-stats">
            <div class="summary-card-wrapper">
                <h3 class="summary-card-title">Total Passengers</h3>
                <div class="summary-card">
                    <svg class="summary-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <div class="summary-card-content">
                        <p id="total-passengers" class="stat-value">-</p>
                        <span id="total-records" class="stat-subtitle">- records</span>
                    </div>
                </div>
            </div>
            <div class="summary-card-wrapper">
                <h3 class="summary-card-title">Growth Rate</h3>
                <div class="summary-card">
                    <svg class="summary-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                        <polyline points="17 6 23 6 23 12"></polyline>
                    </svg>
                    <div class="summary-card-content">
                        <p id="growth-rate" class="stat-value">-</p>
                        <span id="growth-period" class="stat-subtitle">-</span>
                    </div>
                </div>
            </div>
            <div class="summary-card-wrapper">
                <h3 class="summary-card-title">Peak Month</h3>
                <div class="summary-card">
                    <svg class="summary-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <div class="summary-card-content">
                        <p id="peak-month" class="stat-value">-</p>
                        <span id="peak-passengers" class="stat-subtitle">-</span>
                    </div>
                </div>
            </div>
            <div class="summary-card-wrapper">
                <h3 class="summary-card-title">Busiest Airport</h3>
                <div class="summary-card">
                    <svg class="summary-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline>
                        <polyline points="7.5 19.79 7.5 14.6 3 12"></polyline>
                        <polyline points="21 12 16.5 14.6 16.5 19.79"></polyline>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <div class="summary-card-content">
                        <p id="busiest-airport" class="stat-value">-</p>
                        <span id="busiest-airport-count" class="stat-subtitle">-</span>
                    </div>
                </div>
            </div>
            <div class="summary-card-wrapper">
                <h3 class="summary-card-title">Passenger Split</h3>
                <div class="summary-card">
                    <svg class="summary-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                        <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                    </svg>
                    <div class="summary-card-content">
                        <p id="pax-split" class="stat-value">-</p>
                        <span id="pax-split-subtitle" class="stat-subtitle">-</span>
                    </div>
                </div>
            </div>
            <div class="summary-card-wrapper">
                <h3 class="summary-card-title">Average per Period</h3>
                <div class="summary-card">
                    <svg class="summary-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <div class="summary-card-content">
                        <p id="avg-passengers" class="stat-value">-</p>
                        <span id="avg-trend" class="stat-subtitle">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

        </div><!-- .pax-main-content -->

    </div><!-- .pax-layout-wrapper -->

    <?php
    while ( have_posts() ) :
        the_post();
        if ( get_the_content() ) :
    ?>
        <div class="page-content">
            <?php the_content(); ?>
        </div>
    <?php
        endif;
    endwhile;
    ?>

            </main><!-- #primary -->
            
        </div><!-- .single-post-layout -->
    </div><!-- .container -->
</div><!-- .single-post-main -->

<?php
// Enqueue PAX Data page specific CSS
wp_enqueue_style(
    'pax-data-traffic-trends',
    get_template_directory_uri() . '/assets/css/pax-data-traffic-trends.css',
    array(),
    filemtime( get_template_directory() . '/assets/css/pax-data-traffic-trends.css' )
);

// Enqueue Chart.js with defer and integrity check for security
wp_enqueue_script( 
    'chartjs', 
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', 
    array(), 
    '4.4.0', 
    array(
        'strategy' => 'defer',
        'in_footer' => true
    )
);

// Enqueue custom PAX visualization script
wp_enqueue_script( 
    'pax-visualization', 
    get_template_directory_uri() . '/assets/js/pax-data-visualization.js', 
    array( 'jquery', 'chartjs' ), 
    filemtime( get_template_directory() . '/assets/js/pax-data-visualization.js' ), // Cache busting
    array(
        'strategy' => 'defer',
        'in_footer' => true
    )
);

// Localize script for AJAX
wp_localize_script( 'pax-visualization', 'paxData', array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'nonce'   => wp_create_nonce( 'pax_data_chart' ),
) );

// Add inline script to handle Chart.js CDN failure
add_action('wp_footer', function() {
    ?>
    <script>
    // Fallback if Chart.js CDN fails to load
    window.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js failed to load from CDN');
            var errorDiv = document.querySelector('.pax-chart-container');
            if (errorDiv) {
                errorDiv.innerHTML = '<div style="padding: 40px; text-align: center; background: var(--cream); border: 2px solid var(--gold-primary); border-radius: 8px; color: var(--navy-primary);"><h3 style="color: var(--gold-primary); margin-bottom: 1rem;">⚠️ Chart Library Unavailable</h3><p style="color: var(--charcoal-dark);">Unable to load charting library from CDN. Please check your internet connection or try again later.</p></div>';
            }
        }
    });
    </script>
    <?php
}, 100);
?>

<?php get_footer();

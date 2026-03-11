<?php
/**
 * Template part for stats panel section
 *
 * @package Beond_Custom
 */

$homepage_settings = get_option('beyond_borders_homepage_settings', array());
$stats_settings = get_option('beyond_borders_stats_settings', array());

if (empty($homepage_settings['enable_stats']) || empty($stats_settings['enable_stats_section'])) {
    return;
}

$auto_calculate = $stats_settings['auto_calculate'] ?? false;

// Get stats values
if ($auto_calculate) {
    $user_count = count_users();
    $reader_count = ceil($user_count['total_users'] / 1000);
    
    $contributors = get_users(array(
        'capability' => 'edit_posts',
        'has_published_posts' => true,
    ));
    $contributor_count = count($contributors);
    
    $regions = get_terms(array(
        'taxonomy' => 'region',
        'hide_empty' => true,
    ));
    $countries_count = is_array($regions) ? count($regions) : 0;
} else {
    $reader_count = $stats_settings['reader_count'] ?? '200';
    $contributor_count = $stats_settings['contributor_count'] ?? '120';
    $countries_count = $stats_settings['countries_count'] ?? '45';
}

$reader_suffix = $stats_settings['reader_suffix'] ?? 'K+';
$contributor_suffix = $stats_settings['contributor_suffix'] ?? '+';
$countries_suffix = $stats_settings['countries_suffix'] ?? '+';

$reader_label = $stats_settings['reader_label'] ?? 'Global Readers';
$contributor_label = $stats_settings['contributor_label'] ?? 'Expert Contributors';
$countries_label = $stats_settings['countries_label'] ?? 'Countries Covered';

$stats_heading = $stats_settings['stats_heading'] ?? '';
$stats_description = $stats_settings['stats_description'] ?? '';
$bg_color = $stats_settings['stats_background_color'] ?? '#f8f9fa';
?>

<section class="stats-panel" style="background-color: <?php echo esc_attr($bg_color); ?>;">
    <div class="stats-container">
        <?php if ($stats_heading) : ?>
            <h2 class="stats-heading"><?php echo esc_html($stats_heading); ?></h2>
        <?php endif; ?>
        
        <?php if ($stats_description) : ?>
            <p class="stats-description"><?php echo esc_html($stats_description); ?></p>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">
                    <?php echo esc_html($reader_count); ?><span class="stat-suffix"><?php echo esc_html($reader_suffix); ?></span>
                </div>
                <div class="stat-label"><?php echo esc_html($reader_label); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-value">
                    <?php echo esc_html($contributor_count); ?><span class="stat-suffix"><?php echo esc_html($contributor_suffix); ?></span>
                </div>
                <div class="stat-label"><?php echo esc_html($contributor_label); ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-value">
                    <?php echo esc_html($countries_count); ?><span class="stat-suffix"><?php echo esc_html($countries_suffix); ?></span>
                </div>
                <div class="stat-label"><?php echo esc_html($countries_label); ?></div>
            </div>
        </div>
    </div>
</section>

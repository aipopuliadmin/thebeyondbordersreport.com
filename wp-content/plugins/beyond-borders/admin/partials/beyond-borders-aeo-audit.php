<?php
/**
 * AEO (Answer Engine Optimization) Audit Page
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get the page/post to audit
$audit_url = isset( $_POST['audit_url'] ) ? esc_url_raw( $_POST['audit_url'] ) : home_url();
$audit_results = array();
$total_score = 0;
$max_score = 10;

// Perform audit if URL submitted
if ( isset( $_POST['run_audit'] ) && check_admin_referer( 'bb_aeo_audit', 'bb_aeo_nonce' ) ) {
    
    // Get page ID from URL
    $page_id = url_to_postid( $audit_url );
    if ( ! $page_id ) {
        $page_id = get_option( 'page_on_front' ); // Fallback to homepage
    }
    
    // 1. Title & Meta Description Check
    $title = '';
    $meta_desc = '';
    if ( $page_id ) {
        $title = get_the_title( $page_id );
        $meta_desc = get_post_meta( $page_id, '_yoast_wpseo_metadesc', true );
        if ( empty( $meta_desc ) ) {
            $meta_desc = get_post_meta( $page_id, '_aioseop_description', true );
        }
        if ( empty( $meta_desc ) ) {
            $post = get_post( $page_id );
            $meta_desc = wp_trim_words( $post->post_content, 20 );
        }
    } else {
        $title = get_bloginfo( 'name' );
        $meta_desc = get_bloginfo( 'description' );
    }
    
    $audit_results['title_meta'] = array(
        'label' => 'Title & Meta Description',
        'pass' => ! empty( $title ) && ! empty( $meta_desc ) && strlen( $title ) >= 30 && strlen( $meta_desc ) >= 50,
        'details' => sprintf( 'Title: %d chars, Description: %d chars', strlen( $title ), strlen( $meta_desc ) ),
        'recommendation' => 'Title should be 30-60 chars, Description 50-160 chars for optimal AEO.'
    );
    if ( $audit_results['title_meta']['pass'] ) $total_score++;
    
    // 2. Structured Data (Schema.org) Check
    ob_start();
    wp_head();
    $head_content = ob_get_clean();
    
    $has_schema = strpos( $head_content, 'application/ld+json' ) !== false;
    $audit_results['schema'] = array(
        'label' => 'Structured Data (JSON-LD)',
        'pass' => $has_schema,
        'details' => $has_schema ? 'Schema markup detected' : 'No Schema markup found',
        'recommendation' => 'Add Article, Organization, or relevant Schema.org markup for better AI understanding.'
    );
    if ( $audit_results['schema']['pass'] ) $total_score++;
    
    // 3. FAQ Schema Check
    $has_faq_schema = strpos( $head_content, '"@type":"FAQPage"' ) !== false || 
                      strpos( $head_content, '"@type": "FAQPage"' ) !== false;
    $audit_results['faq_schema'] = array(
        'label' => 'FAQ/HowTo Schema',
        'pass' => $has_faq_schema,
        'details' => $has_faq_schema ? 'FAQ Schema found' : 'No FAQ Schema',
        'recommendation' => 'Add FAQ or HowTo schema for featured snippet opportunities.'
    );
    if ( $audit_results['faq_schema']['pass'] ) $total_score++;
    
    // 4. H1 Heading Check
    if ( $page_id ) {
        $post = get_post( $page_id );
        $content = $post->post_content;
        $has_h1 = preg_match( '/<h1[^>]*>(.*?)<\/h1>/i', $content );
        
        $audit_results['h1_heading'] = array(
            'label' => 'Readable H1 Heading',
            'pass' => $has_h1 || ! empty( $title ),
            'details' => $has_h1 ? 'H1 heading found' : 'Using post title as H1',
            'recommendation' => 'Clear, descriptive H1 helps AI understand page topic.'
        );
        if ( $audit_results['h1_heading']['pass'] ) $total_score++;
    }
    
    // 5. Image Alt Text Check
    if ( $page_id ) {
        $images = get_attached_media( 'image', $page_id );
        $total_images = count( $images );
        $images_with_alt = 0;
        
        foreach ( $images as $image ) {
            $alt = get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
            if ( ! empty( $alt ) ) {
                $images_with_alt++;
            }
        }
        
        $alt_percentage = $total_images > 0 ? ( $images_with_alt / $total_images ) * 100 : 100;
        
        $audit_results['image_alt'] = array(
            'label' => 'Image Alt Text',
            'pass' => $alt_percentage >= 80,
            'details' => sprintf( '%d/%d images have alt text (%.0f%%)', $images_with_alt, $total_images, $alt_percentage ),
            'recommendation' => 'Alt text helps voice search and accessibility.'
        );
        if ( $audit_results['image_alt']['pass'] ) $total_score++;
    }
    
    // 6. Open Graph Tags Check
    $has_og = strpos( $head_content, 'property="og:' ) !== false;
    $has_twitter = strpos( $head_content, 'name="twitter:' ) !== false;
    
    $audit_results['social_tags'] = array(
        'label' => 'Open Graph & Twitter Tags',
        'pass' => $has_og && $has_twitter,
        'details' => sprintf( 'OG: %s, Twitter: %s', $has_og ? 'Yes' : 'No', $has_twitter ? 'Yes' : 'No' ),
        'recommendation' => 'Social tags improve AI content extraction and sharing.'
    );
    if ( $audit_results['social_tags']['pass'] ) $total_score++;
    
    // 7. Language Tag Check
    $has_lang = strpos( $head_content, 'lang=' ) !== false;
    
    $audit_results['language'] = array(
        'label' => 'Language Tag',
        'pass' => $has_lang,
        'details' => $has_lang ? 'Language declared' : 'No lang attribute',
        'recommendation' => 'Language tag helps multilingual AI understanding.'
    );
    if ( $audit_results['language']['pass'] ) $total_score++;
    
    // 8. Internal & External Links Check
    if ( $page_id ) {
        $post = get_post( $page_id );
        $content = $post->post_content;
        
        preg_match_all( '/<a[^>]+href=([\'"])(.*?)\1[^>]*>/i', $content, $links );
        $internal_links = 0;
        $external_links = 0;
        
        foreach ( $links[2] as $link ) {
            if ( strpos( $link, home_url() ) !== false || strpos( $link, '/' ) === 0 ) {
                $internal_links++;
            } else if ( strpos( $link, 'http' ) !== false ) {
                $external_links++;
            }
        }
        
        $audit_results['links'] = array(
            'label' => 'Internal & External Links',
            'pass' => $internal_links >= 2 && $external_links >= 1,
            'details' => sprintf( 'Internal: %d, External: %d', $internal_links, $external_links ),
            'recommendation' => 'Good linking structure helps AI understand content relationships.'
        );
        if ( $audit_results['links']['pass'] ) $total_score++;
    }
    
    // 9. Robots.txt Check
    $robots_url = home_url( '/robots.txt' );
    $robots_response = wp_remote_get( $robots_url );
    $has_robots = ! is_wp_error( $robots_response ) && wp_remote_retrieve_response_code( $robots_response ) === 200;
    
    $audit_results['robots'] = array(
        'label' => 'Robots.txt Presence',
        'pass' => $has_robots,
        'details' => $has_robots ? 'Robots.txt found' : 'No robots.txt',
        'recommendation' => 'Robots.txt guides AI crawlers on what to index.'
    );
    if ( $audit_results['robots']['pass'] ) $total_score++;
    
    // 10. Key Points / Featured Snippet Readiness
    $has_key_points = false;
    if ( $page_id ) {
        $key_points = get_post_meta( $page_id, '_beond_key_points', true );
        $has_key_points = ! empty( $key_points ) && is_array( $key_points );
    }
    
    $audit_results['key_points'] = array(
        'label' => 'Key Points / Featured Snippet Ready',
        'pass' => $has_key_points,
        'details' => $has_key_points ? 'Key points defined' : 'No key points',
        'recommendation' => 'Key points improve chances for Position Zero and AI extraction.'
    );
    if ( $audit_results['key_points']['pass'] ) $total_score++;
}

$score_percentage = ( $total_score / $max_score ) * 100;
?>

<div class="wrap bb-aeo-audit">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <p class="description" style="margin-bottom: 30px;">
        <?php _e( 'Audit your website for Answer Engine Optimization (AEO). This tool checks 10 critical factors that improve visibility in AI search, voice assistants, and featured snippets.', 'beyond-borders' ); ?>
    </p>
    
    <div class="bb-audit-form">
        <form method="post" action="">
            <?php wp_nonce_field( 'bb_aeo_audit', 'bb_aeo_nonce' ); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="audit_url"><?php _e( 'URL to Audit', 'beyond-borders' ); ?></label>
                    </th>
                    <td>
                        <input type="url" 
                               name="audit_url" 
                               id="audit_url" 
                               value="<?php echo esc_url( $audit_url ); ?>" 
                               class="regular-text" 
                               placeholder="<?php echo esc_url( home_url() ); ?>">
                        <p class="description">
                            <?php _e( 'Enter the full URL of any page or post on your site to audit.', 'beyond-borders' ); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" 
                       name="run_audit" 
                       class="button button-primary" 
                       value="<?php esc_attr_e( 'Run AEO Audit', 'beyond-borders' ); ?>">
            </p>
        </form>
    </div>
    
    <?php if ( ! empty( $audit_results ) ) : ?>
        
        <div class="bb-audit-results">
            <div class="bb-audit-score">
                <h2><?php _e( 'AEO Score', 'beyond-borders' ); ?></h2>
                <div class="score-circle <?php echo $score_percentage >= 70 ? 'good' : ( $score_percentage >= 50 ? 'fair' : 'poor' ); ?>">
                    <span class="score-number"><?php echo $total_score; ?>/<?php echo $max_score; ?></span>
                    <span class="score-label"><?php echo round( $score_percentage ); ?>%</span>
                </div>
                <p class="score-description">
                    <?php
                    if ( $score_percentage >= 80 ) {
                        _e( 'Excellent! Your content is well-optimized for answer engines.', 'beyond-borders' );
                    } elseif ( $score_percentage >= 60 ) {
                        _e( 'Good progress. A few improvements will boost your AEO score.', 'beyond-borders' );
                    } else {
                        _e( 'Needs improvement. Follow recommendations below to optimize for AI search.', 'beyond-borders' );
                    }
                    ?>
                </p>
            </div>
            
            <h2><?php _e( 'Detailed Audit Results', 'beyond-borders' ); ?></h2>
            <p class="description"><?php printf( __( 'Auditing: %s', 'beyond-borders' ), esc_url( $audit_url ) ); ?></p>
            
            <table class="wp-list-table widefat fixed striped bb-audit-table">
                <thead>
                    <tr>
                        <th width="5%"><?php _e( 'Status', 'beyond-borders' ); ?></th>
                        <th width="25%"><?php _e( 'AEO Factor', 'beyond-borders' ); ?></th>
                        <th width="30%"><?php _e( 'Details', 'beyond-borders' ); ?></th>
                        <th width="40%"><?php _e( 'Recommendation', 'beyond-borders' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $audit_results as $result ) : ?>
                        <tr class="<?php echo $result['pass'] ? 'pass' : 'fail'; ?>">
                            <td class="status-icon">
                                <?php if ( $result['pass'] ) : ?>
                                    <span class="dashicons dashicons-yes-alt" style="color: #46b450; font-size: 20px;"></span>
                                <?php else : ?>
                                    <span class="dashicons dashicons-dismiss" style="color: #dc3232; font-size: 20px;"></span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo esc_html( $result['label'] ); ?></strong></td>
                            <td><?php echo esc_html( $result['details'] ); ?></td>
                            <td><?php echo esc_html( $result['recommendation'] ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="bb-audit-info" style="margin-top: 30px; padding: 20px; background: #f0f6fc; border-left: 4px solid #003265;">
                <h3><?php _e( 'About Answer Engine Optimization (AEO)', 'beyond-borders' ); ?></h3>
                <p><?php _e( 'AEO helps your content rank in AI-powered search engines like ChatGPT, Google AI Overview, Perplexity, and voice assistants. Focus on:', 'beyond-borders' ); ?></p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><?php _e( 'Structured data (Schema.org markup)', 'beyond-borders' ); ?></li>
                    <li><?php _e( 'Clear, concise key points for featured snippets', 'beyond-borders' ); ?></li>
                    <li><?php _e( 'Descriptive metadata and semantic HTML', 'beyond-borders' ); ?></li>
                    <li><?php _e( 'Accessibility features (alt text, language tags)', 'beyond-borders' ); ?></li>
                    <li><?php _e( 'Quality internal and external linking', 'beyond-borders' ); ?></li>
                </ul>
            </div>
        </div>
        
    <?php endif; ?>
</div>

<style>
.bb-aeo-audit {
    max-width: 1200px;
}

.bb-audit-form {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin-bottom: 30px;
}

.bb-audit-results {
    background: #fff;
    padding: 30px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.bb-audit-score {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px;
    background: linear-gradient(135deg, #f0f6fc 0%, #e8f0f8 100%);
    border-radius: 8px;
}

.score-circle {
    display: inline-block;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 20px auto;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.score-circle.good {
    background: linear-gradient(135deg, #46b450 0%, #3a9943 100%);
}

.score-circle.fair {
    background: linear-gradient(135deg, #ffb900 0%, #e8a200 100%);
}

.score-circle.poor {
    background: linear-gradient(135deg, #dc3232 0%, #c62828 100%);
}

.score-number {
    font-size: 42px;
    font-weight: 700;
    color: #fff;
    line-height: 1;
}

.score-label {
    font-size: 18px;
    color: rgba(255, 255, 255, 0.9);
    margin-top: 5px;
}

.score-description {
    font-size: 16px;
    color: #003265;
    margin-top: 15px;
}

.bb-audit-table {
    margin-top: 20px;
}

.bb-audit-table tr.pass {
    background-color: #f0f9f0 !important;
}

.bb-audit-table tr.fail {
    background-color: #fef7f7 !important;
}

.bb-audit-table .status-icon {
    text-align: center;
    vertical-align: middle;
}

.bb-audit-table td {
    vertical-align: top;
    padding: 12px 10px;
}

.bb-audit-table th {
    font-weight: 600;
    background: #003265;
    color: #fff;
}

.bb-audit-info {
    border-radius: 4px;
}

.bb-audit-info h3 {
    margin-top: 0;
    color: #003265;
}

.bb-audit-info ul li {
    margin-bottom: 8px;
}
</style>

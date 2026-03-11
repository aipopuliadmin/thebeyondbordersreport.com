<?php
/**
 * Bulk Post Generator for Beyond Borders
 * 
 * Access this file via browser: http://beond.local/wp-content/plugins/beyond-borders/bulk-generate-posts.php
 * 
 * SECURITY: Delete this file after running!
 */

// Load WordPress
require_once( dirname(__FILE__) . '/../../../wp-load.php' );

if ( ! current_user_can( 'manage_options' ) ) {
    die( 'Access denied. You must be an administrator.' );
}

// Set execution time
set_time_limit( 300 );

// Sample post template function
function generate_posts_for_category( $category_slug, $category_name, $count = 20 ) {
    $category = get_term_by( 'slug', $category_slug, 'category' );
    if ( ! $category ) {
        return array( 'error' => "Category '{$category_name}' not found!" );
    }
    
    $created_posts = array();
    $post_templates = get_post_templates_for_category( $category_slug );
    
    for ( $i = 0; $i < $count; $i++ ) {
        $template = $post_templates[ $i % count( $post_templates ) ];
        
        // Add variation to avoid duplicates
        $variation = $i > ( count( $post_templates ) - 1 ) ? ' - Part ' . ceil( ( $i + 1 ) / count( $post_templates ) ) : '';
        
        $post_id = wp_insert_post( array(
            'post_title'    => $template['title'] . $variation,
            'post_content'  => $template['content'],
            'post_excerpt'  => $template['excerpt'],
            'post_status'   => 'publish',
            'post_author'   => get_current_user_id(),
            'post_category' => array( $category->term_id ),
            'post_type'     => 'post',
            'post_date'     => date( 'Y-m-d H:i:s', strtotime( "-{$i} days" ) ),
        ) );
        
        if ( $post_id && ! is_wp_error( $post_id ) ) {
            // Mark as generated post for easy deletion
            update_post_meta( $post_id, '_bb_generated_post', '1' );
            $created_posts[] = get_the_title( $post_id );
        }
    }
    
    return $created_posts;
}

// Post templates generator
function get_post_templates_for_category( $category_slug ) {
    $templates = array(
        'aviation' => array(
            array( 'title' => 'The Future of Sustainable Aviation Technology', 'excerpt' => 'Electric and hybrid aircraft are transforming the aviation industry\'s approach to sustainability.', 'content' => '<p>The aviation industry is experiencing a revolutionary shift toward sustainable technology. Major manufacturers are investing billions in electric and hybrid propulsion systems.</p><p>Environmental concerns and regulatory pressure are driving rapid innovation in aircraft design and powertrains. The next decade will see commercial deployment of zero-emission aircraft on short-haul routes.</p><p>This transformation represents the most significant change in aviation since the introduction of jet engines.</p>' ),
            array( 'title' => 'Private Aviation Boom: Luxury Travel Redefined', 'excerpt' => 'Private jet market reaches unprecedented heights as ultra-wealthy prioritize flexibility and exclusivity.', 'content' => '<p>The private aviation sector is experiencing explosive growth, with demand surging beyond pre-pandemic levels. Fractional ownership and jet card programs are democratizing access.</p><p>Technology platforms are revolutionizing how private aviation is booked and managed. Empty-leg marketplaces offer opportunities for cost-conscious luxury travelers.</p><p>Industry analysts project continued expansion as remote work enables location flexibility for executives.</p>' ),
            array( 'title' => 'Airport Innovation: Biometric Revolution', 'excerpt' => 'Facial recognition and biometric screening are streamlining passenger processing at major airports.', 'content' => '<p>International airports are deploying cutting-edge biometric systems that dramatically reduce processing times. Passengers can move through security and boarding with minimal friction.</p><p>Privacy advocates have raised concerns about data security and surveillance implications. Airport authorities emphasize strong protocols for data protection.</p><p>The technology is expected to become standard across global aviation within five years.</p>' ),
            array( 'title' => 'Supersonic Travel Returns to Commercial Aviation', 'excerpt' => 'Next-generation supersonic jets promise to cut transatlantic flight times in half.', 'content' => '<p>Nearly two decades after Concorde\'s retirement, supersonic passenger aviation is making a comeback. Multiple aerospace companies are developing aircraft capable of crossing the Atlantic in under four hours.</p><p>Advanced materials and quieter engines address noise concerns that plagued earlier designs. Regulatory approvals are progressing for commercial operations.</p><p>Inaugural flights are projected for 2028, marking new era in premium air travel.</p>' ),
            array( 'title' => 'Urban Air Mobility: Flying Taxis Become Reality', 'excerpt' => 'Electric vertical takeoff aircraft are beginning to operate commercial passenger services in major cities.', 'content' => '<p>Dubai has launched the world\'s first commercial eVTOL service for passenger transport. Initial routes connect business districts and the airport.</p><p>Safety certifications met rigorous international standards. Fares are premium but expected to decline as operations scale.</p><p>Other cities worldwide are developing infrastructure for urban air mobility systems.</p>' ),
        ),
        'business' => array(
            array( 'title' => 'Supply Chain Transformation: Resilience Over Efficiency', 'excerpt' => 'Companies fundamentally restructure global supply chains following pandemic disruptions.', 'content' => '<p>The just-in-time model is being replaced by more resilient systems. Businesses are diversifying suppliers and nearshoring production.</p><p>Higher costs are offset by improved security against disruptions. Geopolitical risks make resilience increasingly valuable.</p><p>This represents a fundamental shift in global business operations and risk management.</p>' ),
            array( 'title' => 'Remote Work Revolution: The Hybrid Future', 'excerpt' => 'Permanent hybrid work models are reshaping corporate real estate and organizational culture.', 'content' => '<p>Companies are reimagining office spaces as collaboration hubs rather than daily destinations. Traditional layouts are giving way to flexible environments.</p><p>Real estate savings are being redirected to technology and employee benefits. Cultural implications are profound for distributed teams.</p><p>The shift appears permanent, fundamentally changing work and urban development.</p>' ),
            array( 'title' => 'ESG Investing Reaches Mainstream Status', 'excerpt' => 'Environmental, social, and governance criteria become central to global investment decisions.', 'content' => '<p>Sustainable investing has moved from niche to dominant force in capital markets. Institutional investors integrate ESG factors into fundamental analysis.</p><p>Regulatory requirements drive improved corporate disclosure. Financial performance increasingly supports premium valuations.</p><p>Capital allocation is being transformed by sustainability considerations.</p>' ),
            array( 'title' => 'AI Disrupts Professional Services Industry', 'excerpt' => 'Law firms, consultancies, and accounting practices deploy artificial intelligence to enhance productivity.', 'content' => '<p>Professional service firms automate routine tasks through AI, freeing experts for complex challenges. Document review and research increasingly leverage AI capabilities.</p><p>Workforce implications raise questions about future skill requirements. Firms investing in AI gain competitive advantages.</p><p>Transformation of professional services through AI is accelerating.</p>' ),
            array( 'title' => 'Subscription Economy Expands Across Industries', 'excerpt' => 'Recurring revenue models spread beyond software as companies seek predictable cash flows.', 'content' => '<p>Businesses across sectors adopt subscription models. From cars to coffee, consumers access products through ongoing relationships.</p><p>Companies gain revenue visibility and stronger customer relationships. Consumers trade ownership for convenience.</p><p>Subscription services are reshaping consumer spending patterns fundamentally.</p>' ),
        ),
        'innovation' => array(
            array( 'title' => 'Fusion Energy Breakthrough: Clean Power on Horizon', 'excerpt' => 'Scientists achieve net energy gain in fusion reactions, marking historic milestone.', 'content' => '<p>Recent fusion advances suggest unlimited clean energy could become reality within decades. Net energy gain demonstrates technical feasibility.</p><p>Engineering challenges remain before commercial deployment. Investment in fusion research is accelerating dramatically.</p><p>Fusion could transform global energy landscape and climate mitigation efforts.</p>' ),
            array( 'title' => 'CRISPR Gene Editing: Medical Revolution', 'excerpt' => 'Gene editing technology moves from laboratory to clinical applications for genetic diseases.', 'content' => '<p>CRISPR-based therapies show remarkable results in early trials. The technology enables precise DNA modifications with unprecedented accuracy.</p><p>Ethical considerations generate intense debate. Regulatory frameworks evolve to balance innovation and safety.</p><p>Gene editing could revolutionize medicine by addressing root causes of disease.</p>' ),
            array( 'title' => 'Quantum Computing Enters Commercial Phase', 'excerpt' => 'Quantum computers begin solving real-world business problems in optimization and cryptography.', 'content' => '<p>Quantum computing transitions from research to practical applications. Companies in finance and logistics explore quantum advantages.</p><p>Technology could revolutionize drug discovery and financial modeling. Technical challenges remain before broad accessibility.</p><p>Early adopters position themselves to leverage quantum capabilities as technology matures.</p>' ),
            array( 'title' => 'Brain-Computer Interfaces: Neurotechnology Advances', 'excerpt' => 'Direct neural connections enable new treatments for paralysis and neurological conditions.', 'content' => '<p>Brain-computer interface technology achieves major milestones in clinical trials. Patients with paralysis regain ability to communicate and control devices.</p><p>Ethical implications require careful consideration. Regulatory oversight ensures patient safety.</p><p>Neurotechnology could transform treatment of neurological conditions.</p>' ),
            array( 'title' => 'Synthetic Biology: Engineering Living Systems', 'excerpt' => 'Scientists design organisms to produce medicines, materials, and sustainable products.', 'content' => '<p>Synthetic biology enables creation of organisms with novel capabilities. Applications include sustainable manufacturing and environmental remediation.</p><p>Biosafety protocols address potential risks. Commercial applications are expanding rapidly.</p><p>The field represents convergence of biology and engineering.</p>' ),
        ),
        'leadership' => array(
            array( 'title' => 'Authentic Leadership: Vulnerability as Strength', 'excerpt' => 'Modern leaders embrace transparency and emotional intelligence to build trust and engagement.', 'content' => '<p>Leadership paradigms are shifting away from command-and-control toward authentic, vulnerable approaches. Research shows transparency builds stronger teams.</p><p>Emotional intelligence has become critical competency for executives. Leaders who show humanity create more innovative cultures.</p><p>This represents fundamental evolution in leadership philosophy and practice.</p>' ),
            array( 'title' => 'Diversity in C-Suite: Progress and Challenges', 'excerpt' => 'Boardrooms become more diverse but significant gaps remain in representation.', 'content' => '<p>Corporate leadership is slowly diversifying as companies recognize value of varied perspectives. Women and minorities are gaining board seats.</p><p>Progress varies significantly by industry and region. Systemic barriers persist despite stated commitments.</p><p>Sustained effort required to achieve equitable representation in leadership.</p>' ),
            array( 'title' => 'Purpose-Driven Leadership: Beyond Profit', 'excerpt' => 'CEOs articulate missions that encompass social impact alongside financial performance.', 'content' => '<p>Business leaders increasingly emphasize purpose beyond shareholder returns. Employees, especially younger generations, demand meaningful work.</p><p>Purpose-driven companies often outperform peers financially. Authenticity crucial to avoiding accusations of greenwashing.</p><p>The trend reflects broader societal expectations of corporate responsibility.</p>' ),
            array( 'title' => 'Crisis Leadership: Navigating Uncertainty', 'excerpt' => 'Executives develop new skills for leading through perpetual disruption and change.', 'content' => '<p>Modern leaders face near-constant crisis management. Pandemics, climate events, and geopolitical shocks demand adaptive leadership.</p><p>Communication skills and decisiveness under uncertainty are critical. Leaders must balance short-term responses with long-term vision.</p><p>Crisis leadership has become permanent competency requirement.</p>' ),
            array( 'title' => 'Digital Leadership: Technology as Competitive Advantage', 'excerpt' => 'CEOs must understand technology strategy as digital transformation accelerates.', 'content' => '<p>Executive leadership requires fluency in digital technologies. CEOs personally champion digital transformation initiatives.</p><p>Technology expertise on boards is increasing. Leaders who understand digital possibilities gain competitive advantages.</p><p>Digital leadership separates industry leaders from laggards.</p>' ),
        ),
        'luxury-retail' => array(
            array( 'title' => 'Luxury E-Commerce: Digital Transformation of Premium Brands', 'excerpt' => 'High-end retailers embrace online channels while maintaining exclusivity and brand prestige.', 'content' => '<p>Luxury brands have overcome historical reluctance toward e-commerce. Digital platforms now generate significant revenue while preserving brand equity.</p><p>Personalization and exclusive online experiences differentiate luxury digital retail. Omnichannel strategies integrate physical and digital seamlessly.</p><p>E-commerce has become essential for luxury retail growth.</p>' ),
            array( 'title' => 'Sustainable Luxury: Conscious Consumption Trend', 'excerpt' => 'Affluent consumers demand environmental responsibility from premium brands.', 'content' => '<p>Luxury sector faces growing pressure to address sustainability. Wealthy consumers increasingly consider environmental impact in purchasing decisions.</p><p>Brands invest in sustainable materials and transparent supply chains. Resale and circular economy models are emerging in luxury space.</p><p>Sustainability has become luxury industry imperative.</p>' ),
            array( 'title' => 'Personalization in Luxury: Bespoke Experiences', 'excerpt' => 'Ultra-high-net-worth clients expect fully customized products and services.', 'content' => '<p>Luxury brands offer unprecedented customization through technology and craftsmanship. Clients co-create unique products reflecting personal taste.</p><p>Data analytics enable highly personalized customer journeys. Human touch remains essential in luxury personalization.</p><p>Bespoke offerings drive loyalty and premium pricing.</p>' ),
            array( 'title' => 'Luxury Travel Rebound: Experiential Focus', 'excerpt' => 'High-end tourism emphasizes transformative experiences over material possessions.', 'content' => '<p>Luxury travel has recovered strongly with shift toward meaningful experiences. Travelers seek authenticity, adventure, and personal growth.</p><p>Sustainability and responsible tourism influence itinerary planning. Private experiences and exclusive access define luxury travel.</p><p>Experiential luxury represents significant growth opportunity.</p>' ),
            array( 'title' => 'Digital Collectibles: NFTs in Luxury Sector', 'excerpt' => 'Premium brands experiment with digital ownership and virtual products.', 'content' => '<p>Luxury houses explore NFTs and digital collectibles. Virtual fashion and digital art attract new luxury consumers.</p><p>Blockchain enables authentication and scarcity in digital realm. Younger audiences engage with luxury through digital channels.</p><p>Digital luxury represents frontier for brand innovation.</p>' ),
        ),
        'travel' => array(
            array( 'title' => 'Revenge Travel: Post-Pandemic Tourism Surge', 'excerpt' => 'Pent-up demand drives record travel bookings as pandemic restrictions ease globally.', 'content' => '<p>Tourism industry experiences unprecedented surge as travelers make up for lost time. Bookings exceed pre-pandemic levels across categories.</p><p>Labor shortages and capacity constraints challenge industry. Prices reflect strong demand and limited supply.</p><p>The travel rebound appears sustainable as remote work enables flexibility.</p>' ),
            array( 'title' => 'Sustainable Tourism: Responsible Travel Movement', 'excerpt' => 'Travelers increasingly prioritize environmental impact and local community benefit.', 'content' => '<p>Tourism sector embraces sustainability as core value proposition. Travelers seek experiences that benefit destinations.</p><p>Overtourism concerns drive limits on visitor numbers. Certification programs help identify sustainable operators.</p><p>Responsible travel represents growing market segment.</p>' ),
            array( 'title' => 'Digital Nomad Visas: Countries Compete for Remote Workers', 'excerpt' => 'Nations create special visa programs to attract location-independent professionals.', 'content' => '<p>Governments worldwide offer digital nomad visas to capture remote work opportunities. Programs provide legal framework for extended stays.</p><p>Economic benefits include spending from high-earning remote workers. Infrastructure and community integration pose challenges.</p><p>Digital nomad programs are proliferating globally.</p>' ),
            array( 'title' => 'Adventure Travel Growth: Thrill-Seeking Tourists', 'excerpt' => 'Demand for active, challenging travel experiences accelerates across demographics.', 'content' => '<p>Adventure tourism is fastest-growing travel segment. Travelers seek authentic, physically engaging experiences.</p><p>Safety standards and sustainability practices critical for industry credibility. Technology enables access to remote destinations.</p><p>Adventure travel appeals to broad demographic beyond traditional adventurers.</p>' ),
            array( 'title' => 'Wellness Tourism: Travel for Health and Mindfulness', 'excerpt' => 'Wellness-focused trips attract travelers seeking mental and physical rejuvenation.', 'content' => '<p>Wellness tourism combines travel with health and mindfulness practices. Retreats offer meditation, fitness, and nutritional programs.</p><p>Mental health awareness drives demand for restorative travel. Medical tourism complements wellness travel sector.</p><p>Wellness represents high-growth travel category with premium pricing.</p>' ),
        ),
    );
    
    return isset( $templates[ $category_slug ] ) ? $templates[ $category_slug ] : array();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Bulk Post Generator - Beyond Borders</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; padding: 40px; background: #f0f0f1; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h1 { color: #003265; margin: 0 0 10px 0; }
        .subtitle { color: #646970; margin-bottom: 30px; }
        .status { padding: 15px; background: #f0f6fc; border-left: 4px solid #003265; margin: 20px 0; border-radius: 4px; }
        .post { padding: 10px; margin: 5px 0; background: #f9f9f9; border-radius: 4px; }
        .category-section { margin: 30px 0; padding: 20px; background: #fafafa; border-radius: 6px; }
        .category-title { font-size: 18px; font-weight: 600; color: #003265; margin-bottom: 15px; }
        .button { display: inline-block; padding: 12px 24px; background: #003265; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 10px 5px 0 0; cursor: pointer; border: none; }
        .button:hover { background: #0A1628; }
        .button-danger { background: #dc3232; }
        .button-danger:hover { background: #a02222; }
        .success { color: #008000; font-weight: 600; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 4px; margin: 20px 0; border-left: 4px solid #ffc107; }
        .danger-zone { background: #fff0f0; border-left: 4px solid #dc3232; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .info { background: #f0f6fc; padding: 15px; border-radius: 4px; margin: 20px 0; border-left: 4px solid #0073aa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Beyond Borders - Bulk Post Generator</h1>
        <p class="subtitle">Generate sample editorial content for your categories</p>
        
        <?php
        // Handle deletion of generated posts
        if ( isset( $_POST['delete_generated_posts'] ) && isset( $_POST['confirm_delete'] ) ):
            $args = array(
                'post_type'      => 'post',
                'posts_per_page' => -1,
                'meta_key'       => '_bb_generated_post',
                'meta_value'     => '1',
                'fields'         => 'ids',
            );
            
            $generated_posts = get_posts( $args );
            $deleted_count = 0;
            
            echo '<div class="status">';
            echo '<strong>Deleting generated posts...</strong><br><br>';
            
            foreach ( $generated_posts as $post_id ) {
                if ( wp_delete_post( $post_id, true ) ) {
                    $deleted_count++;
                }
            }
            
            echo '</div>';
            echo '<div class="status"><strong class="success">✓ Deleted ' . $deleted_count . ' generated posts</strong></div>';
            echo '<p><a href="' . admin_url( 'edit.php' ) . '" class="button">View All Posts</a> <a href="?" class="button">Back to Generator</a></p>';
            
        elseif ( isset( $_POST['generate_posts'] ) ):
            
            echo '<div class="status">';
            echo '<strong>Generating posts...</strong><br><br>';
            
            $categories = array(
                'aviation' => 'Aviation',
                'business' => 'Business',
                'innovation' => 'Innovation',
                'leadership' => 'Leadership',
                'luxury-retail' => 'Luxury Retail',
                'travel' => 'Travel',
            );
            
            $total_created = 0;
            
            foreach ( $categories as $slug => $name ) {
                echo '<div class="category-section">';
                echo '<div class="category-title">' . esc_html( $name ) . '</div>';
                
                $created_posts = generate_posts_for_category( $slug, $name, 20 );
                
                if ( isset( $created_posts['error'] ) ) {
                    echo '<p style="color: #dc3232;">' . esc_html( $created_posts['error'] ) . '</p>';
                } else {
                    echo '<p class="success">✓ Created ' . count( $created_posts ) . ' posts</p>';
                    $total_created += count( $created_posts );
                    
                    echo '<div style="max-height: 200px; overflow-y: auto; padding: 10px; background: white; border-radius: 4px;">';
                    foreach ( array_slice( $created_posts, 0, 5 ) as $title ) {
                        echo '<div class="post">→ ' . esc_html( $title ) . '</div>';
                    }
                    if ( count( $created_posts ) > 5 ) {
                        echo '<div class="post" style="font-style: italic;">... and ' . ( count( $created_posts ) - 5 ) . ' more</div>';
                    }
                    echo '</div>';
                }
                
                echo '</div>';
            }
            
            echo '</div>';
            echo '<div class="status"><strong class="success">✓ Success!</strong><br>Total posts created: ' . $total_created . '</div>';
            echo '<p><a href="' . admin_url( 'edit.php' ) . '" class="button">View All Posts</a> <a href="?" class="button">Generate More</a></p>';
            
            // Show delete option
            echo '<div class="danger-zone">';
            echo '<strong>⚠ Danger Zone</strong><br>';
            echo '<p>Need to remove all generated posts? Use the delete function below.</p>';
            echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to DELETE ALL ' . $total_created . ' generated posts? This cannot be undone!\');">';
            echo '<input type="hidden" name="confirm_delete" value="1">';
            echo '<button type="submit" name="delete_generated_posts" class="button button-danger">Delete All Generated Posts</button>';
            echo '</form>';
            echo '</div>';
            
            echo '<div class="warning"><strong>⚠ Security Notice:</strong> Delete this file (bulk-generate-posts.php) when you\'re done using it!</div>';
            
        else:
            // Check if there are existing generated posts
            $existing_generated = get_posts( array(
                'post_type'      => 'post',
                'posts_per_page' => 1,
                'meta_key'       => '_bb_generated_post',
                'meta_value'     => '1',
                'fields'         => 'ids',
            ) );
            
            $total_generated = 0;
            if ( $existing_generated ) {
                $generated_count_query = new WP_Query( array(
                    'post_type'      => 'post',
                    'posts_per_page' => -1,
                    'meta_key'       => '_bb_generated_post',
                    'meta_value'     => '1',
                    'fields'         => 'ids',
                ) );
                $total_generated = $generated_count_query->found_posts;
            }
        ?>
        
        <?php if ( $total_generated > 0 ): ?>
        <div class="info">
            <strong>ℹ️ Existing Generated Posts:</strong> You currently have <strong><?php echo $total_generated; ?> generated posts</strong> in your database.
        </div>
        <?php endif; ?>
        
        <div class="warning">
            <strong>⚠ Important:</strong> This tool will create 120 sample posts (20 per category). Make sure you want to proceed before clicking Generate.
        </div>
        
        <div class="status">
            <strong>Posts will be created for these categories:</strong>
            <ul>
                <li>Aviation (20 posts)</li>
                <li>Business (20 posts)</li>
                <li>Innovation (20 posts)</li>
                <li>Leadership (20 posts)</li>
                <li>Luxury Retail (20 posts)</li>
                <li>Travel (20 posts)</li>
            </ul>
            <p>Total: <strong>120 posts</strong></p>
        </div>
        
        <form method="post">
            <button type="submit" name="generate_posts" class="button">Generate Posts</button>
            <a href="<?php echo admin_url(); ?>" class="button" style="background: #666;">Cancel</a>
        </form>
        
        <?php if ( $total_generated > 0 ): ?>
        <div class="danger-zone" style="margin-top: 40px;">
            <strong>⚠ Delete Generated Posts</strong><br>
            <p>Remove all <strong><?php echo $total_generated; ?></strong> posts that were generated by this tool.</p>
            <form method="post" onsubmit="return confirm('Are you sure you want to DELETE ALL <?php echo $total_generated; ?> generated posts? This action cannot be undone!');">
                <input type="hidden" name="confirm_delete" value="1">
                <button type="submit" name="delete_generated_posts" class="button button-danger">Delete All Generated Posts (<?php echo $total_generated; ?>)</button>
            </form>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
        
    </div>
</body>
</html>

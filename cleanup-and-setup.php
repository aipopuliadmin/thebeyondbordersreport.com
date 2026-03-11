<?php
/**
 * Cleanup existing data and create new category structure
 * 
 * IMPORTANT: Run this from WordPress admin or via command line
 * Access via: http://beond.local/cleanup-and-setup.php
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

// Security check - only allow logged-in administrators
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('Access denied. Please log in as an administrator.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cleanup and Setup - Beyond Borders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 50px auto; padding: 20px; }
        .section { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: #0a7e07; }
        .error { color: #d63638; }
        button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #005a87; }
        button.danger { background: #d63638; }
        button.danger:hover { background: #a02222; }
        .category-tree { margin-left: 20px; }
        .category-tree li { margin: 5px 0; }
    </style>
</head>
<body>
    <h1>🧹 Cleanup and Setup: Beyond Borders</h1>

    <?php
    // Handle actions
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($action === 'delete_posts') {
        echo '<div class="section">';
        echo '<h2>Deleting Test Posts...</h2>';
        
        $posts = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));
        
        $deleted = 0;
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
            $deleted++;
            echo "<p>✓ Deleted: {$post->post_title}</p>";
        }
        
        echo "<p class='success'><strong>✓ Deleted $deleted posts</strong></p>";
        echo '</div>';
    }
    
    if ($action === 'delete_categories') {
        echo '<div class="section">';
        echo '<h2>Deleting Test Categories...</h2>';
        
        $categories = get_categories(array(
            'hide_empty' => false,
            'exclude' => 1 // Don't delete "Uncategorized"
        ));
        
        $deleted = 0;
        foreach ($categories as $category) {
            wp_delete_term($category->term_id, 'category');
            $deleted++;
            echo "<p>✓ Deleted: {$category->name}</p>";
        }
        
        echo "<p class='success'><strong>✓ Deleted $deleted categories</strong></p>";
        echo '</div>';
    }
    
    if ($action === 'create_categories') {
        echo '<div class="section">';
        echo '<h2>Creating Category Structure...</h2>';
        
        // Define category structure
        $categories = array(
            'Latest Travel Retail News' => array(
                'All Headlines',
                'Airport & Inflight News',
                'Operator & Concession News',
                'Airline & Supplier News'
            ),
            'Markets & Strategy' => array(
                'Annual Commercial Revenues',
                'Duty Free & Travel Retail Business',
                'Pax Data & Traffic Trends',
                'Airline Partnerships & Retail Media',
                'Regional Briefings (APAC, Europe, Middle East, Americas)'
            ),
            'Curated Review' => array(
                'Beauty & Fragrance',
                'Wines, Spirits & Tobacco',
                'Fashion, Luxury & Accessories',
                'Confectionery & Gourmet',
                'Consumer Tech & Travel Essentials',
                'Books, Toys & Collectibles'
            ),
            'Airports & Places' => array(
                'Airport Profiles & Case Studies',
                'Downtown & Border Stores',
                'Cruise & Ferry Retail',
                'Emerging Markets & New Hubs'
            ),
            'Passenger Experience' => array(
                'Accessibility & Inclusivity',
                'Traveller Trends & Sentiment',
                'Digital Journeys & Omni-channel',
                'Research: Traveller Behaviour & Spend'
            ),
            'Hospitality & Lounges' => array(
                'Lounges & Airport Hospitality',
                'Bars, Restaurants & Cafés',
                'Food-to-Go & Convenience',
                'Concepts, Pop-ups & Local Flavours'
            ),
            'Analysis & Opinion' => array(
                'Editorials & Columns',
                'Guest Perspectives',
                'Data & Insight Briefings',
                'Industry Forecasts & Outlier Reports'
            ),
            'Visual Stories' => array(
                'Image of the Week',
                'Store & Experience Galleries',
                'Airport & Lounge Tours',
                'Short Videos & Reels'
            ),
            'Voices & Interviews' => array(
                'Leadership Interviews',
                'Operator Insights',
                'Video Conversations',
                'Event Sessions & Panels'
            )
        );
        
        $created = 0;
        foreach ($categories as $parent_name => $children) {
            // Create parent category
            $parent = wp_insert_term($parent_name, 'category');
            
            if (is_wp_error($parent)) {
                echo "<p class='error'>✗ Error creating: $parent_name - " . $parent->get_error_message() . "</p>";
                continue;
            }
            
            $parent_id = $parent['term_id'];
            echo "<p class='success'>✓ Created parent: <strong>$parent_name</strong></p>";
            $created++;
            
            // Create child categories
            foreach ($children as $child_name) {
                $child = wp_insert_term($child_name, 'category', array(
                    'parent' => $parent_id
                ));
                
                if (is_wp_error($child)) {
                    echo "<p class='error'>  ✗ Error creating: $child_name - " . $child->get_error_message() . "</p>";
                } else {
                    echo "<p>  ✓ Created child: $child_name</p>";
                    $created++;
                }
            }
        }
        
        echo "<p class='success'><strong>✓ Created $created categories</strong></p>";
        echo '</div>';
    }
    ?>

    <!-- Current Status -->
    <div class="section">
        <h2>📊 Current Database Status</h2>
        <?php
        $post_count = wp_count_posts('post');
        $total_posts = $post_count->publish + $post_count->draft + $post_count->pending;
        
        $categories = get_categories(array('hide_empty' => false));
        $category_count = count($categories);
        ?>
        <p><strong>Posts:</strong> <?php echo $total_posts; ?> (<?php echo $post_count->publish; ?> published, <?php echo $post_count->draft; ?> draft)</p>
        <p><strong>Categories:</strong> <?php echo $category_count; ?></p>
    </div>

    <!-- Actions -->
    <div class="section">
        <h2>⚡ Actions</h2>
        <p><strong>Step 1:</strong> Delete all test posts</p>
        <button class="danger" onclick="if(confirm('Are you sure you want to delete ALL posts?')) window.location.href='?action=delete_posts'">
            Delete All Posts
        </button>
        
        <p><strong>Step 2:</strong> Delete all test categories</p>
        <button class="danger" onclick="if(confirm('Are you sure you want to delete ALL categories?')) window.location.href='?action=delete_categories'">
            Delete All Categories
        </button>
        
        <p><strong>Step 3:</strong> Create new category structure</p>
        <button onclick="window.location.href='?action=create_categories'">
            Create Categories
        </button>
        
        <p><strong>Or do everything at once:</strong></p>
        <button class="danger" onclick="if(confirm('This will DELETE all posts and categories, then create new ones. Continue?')) window.location.href='?action=delete_posts&then=delete_categories&then=create_categories'">
            🔄 Complete Reset & Setup
        </button>
    </div>

    <?php
    // Handle combined action
    if (isset($_GET['then'])) {
        echo '<script>window.location.href="?action=' . $_GET['then'] . '";</script>';
    }
    ?>

    <!-- Category Preview -->
    <div class="section">
        <h2>📁 Current Categories</h2>
        <ul class="category-tree">
        <?php
        $categories = get_categories(array(
            'hide_empty' => false,
            'parent' => 0,
            'orderby' => 'name'
        ));
        
        foreach ($categories as $category) {
            echo "<li><strong>{$category->name}</strong> ({$category->count} posts)";
            
            $children = get_categories(array(
                'hide_empty' => false,
                'parent' => $category->term_id,
                'orderby' => 'name'
            ));
            
            if ($children) {
                echo '<ul class="category-tree">';
                foreach ($children as $child) {
                    echo "<li>{$child->name} ({$child->count} posts)</li>";
                }
                echo '</ul>';
            }
            
            echo "</li>";
        }
        ?>
        </ul>
    </div>

    <div class="section">
        <p><a href="<?php echo admin_url(); ?>">← Back to WordPress Admin</a></p>
    </div>
</body>
</html>

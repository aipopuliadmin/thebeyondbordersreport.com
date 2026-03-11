<?php
/**
 * Airport Management Admin Page
 *
 * @package Beyond_Borders
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$airports_table = $wpdb->prefix . 'airports_reference';

// Handle form submissions
$message = '';
$error = '';

if ( isset( $_POST['add_airport'] ) && check_admin_referer( 'add_airport_action', 'add_airport_nonce' ) ) {
    $airport_name = isset( $_POST['airport_name'] ) ? strtoupper( trim( sanitize_text_field( $_POST['airport_name'] ) ) ) : '';
    $iata_code = isset( $_POST['iata_code'] ) ? strtoupper( trim( sanitize_text_field( $_POST['iata_code'] ) ) ) : '';
    $icao_code = isset( $_POST['icao_code'] ) ? strtoupper( trim( sanitize_text_field( $_POST['icao_code'] ) ) ) : '';
    $airport_type = isset( $_POST['airport_type'] ) ? sanitize_text_field( $_POST['airport_type'] ) : '';

    if ( empty( $airport_name ) ) {
        $error = 'Airport name is required.';
    } else {
        // Check if airport already exists
        $exists = $wpdb->get_var( $wpdb->prepare( 
            "SELECT COUNT(*) FROM $airports_table WHERE UPPER(airport_name) = %s",
            $airport_name
        ) );

        if ( $exists ) {
            $error = "Airport '{$airport_name}' already exists.";
        } else {
            $result = $wpdb->insert(
                $airports_table,
                array(
                    'airport_name' => $airport_name,
                    'iata_code'    => $iata_code,
                    'icao_code'    => $icao_code,
                    'airport_type' => $airport_type,
                ),
                array( '%s', '%s', '%s', '%s' )
            );

            if ( $result ) {
                $message = "Airport '{$airport_name}' added successfully.";
            } else {
                $error = 'Failed to add airport. ' . $wpdb->last_error;
            }
        }
    }
}

if ( isset( $_POST['delete_airport'] ) && check_admin_referer( 'delete_airport_action', 'delete_airport_nonce' ) ) {
    $airport_id = isset( $_POST['airport_id'] ) ? intval( $_POST['airport_id'] ) : 0;
    
    if ( $airport_id ) {
        $deleted = $wpdb->delete( $airports_table, array( 'id' => $airport_id ), array( '%d' ) );
        
        if ( $deleted ) {
            $message = 'Airport deleted successfully.';
        } else {
            $error = 'Failed to delete airport.';
        }
    }
}

// Pagination setup
$per_page = 20;
$current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
$offset = ( $current_page - 1 ) * $per_page;

// Get total count
$total_airports = $wpdb->get_var( "SELECT COUNT(*) FROM $airports_table" );
$total_pages = ceil( $total_airports / $per_page );

// Get airports for current page
$airports = $wpdb->get_results( 
    $wpdb->prepare(
        "SELECT * FROM $airports_table ORDER BY airport_name ASC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    )
);
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <?php if ( $message ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html( $message ); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if ( $error ) : ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo esc_html( $error ); ?></p>
        </div>
    <?php endif; ?>

    <div class="airport-management-container">
        
        <div class="airport-layout" style="display: grid; grid-template-columns: 39% 59%; gap: 2%; margin-top: 20px; align-items: start;">
            
            <!-- Add New Airport - Left -->
            <div class="airport-mgmt-card" style="height: fit-content; position: sticky; top: 32px;">
                <h2>Add New Airport</h2>
            <form method="post" action="">
                <?php wp_nonce_field( 'add_airport_action', 'add_airport_nonce' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="airport_name">Airport Name *</label></th>
                        <td>
                            <input type="text" id="airport_name" name="airport_name" class="regular-text" required placeholder="e.g., CHANDIGARH">
                            <p class="description">Enter airport name in uppercase (e.g., CHANDIGARH, DELHI)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="iata_code">IATA Code</label></th>
                        <td>
                            <input type="text" id="iata_code" name="iata_code" class="regular-text" maxlength="3" placeholder="e.g., IXC">
                            <p class="description">3-letter IATA code (optional)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="icao_code">ICAO Code</label></th>
                        <td>
                            <input type="text" id="icao_code" name="icao_code" class="regular-text" maxlength="4" placeholder="e.g., VICG">
                            <p class="description">4-letter ICAO code (optional)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="airport_type">Airport Type</label></th>
                        <td>
                            <select id="airport_type" name="airport_type" class="regular-text">
                                <option value="">Select Type</option>
                                <option value="18 INTERNATIONAL AIRPORTS">18 International Airports</option>
                                <option value="6 JV INTERNATIONAL AIRPORTS">6 JV International Airports</option>
                                <option value="8 CUSTOM AIRPORTS">8 Custom Airports</option>
                                <option value="DOMESTIC AIRPORT">Domestic Airport</option>
                                <option value="OTHER">Other</option>
                            </select>
                            <p class="description">Select airport category (optional)</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" name="add_airport" class="button button-primary">Add Airport</button>
                </p>
            </form>
        </div>

        <!-- Airport List - Right -->
        <div class="airport-mgmt-card">
            <h2>Existing Airports (<?php echo $total_airports; ?>)</h2>
            
            <?php if ( $airports ) : ?>
                <table class="wp-list-table widefat striped" style="width: 100%; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 30%;">Airport Name</th>
                            <th scope="col" style="width: 12%;">IATA</th>
                            <th scope="col" style="width: 12%;">ICAO</th>
                            <th scope="col" style="width: 31%;">Type</th>
                            <th scope="col" style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $airports as $airport ) : ?>
                            <tr>
                                <td data-colname="Airport Name"><strong><?php echo esc_html( $airport->airport_name ); ?></strong></td>
                                <td data-colname="IATA"><?php echo esc_html( $airport->iata_code ); ?></td>
                                <td data-colname="ICAO"><?php echo esc_html( $airport->icao_code ); ?></td>
                                <td data-colname="Type" style="word-wrap: break-word;"><?php echo esc_html( $airport->airport_type ); ?></td>
                                <td data-colname="Actions">
                                    <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete <?php echo esc_js( $airport->airport_name ); ?>?');">
                                        <?php wp_nonce_field( 'delete_airport_action', 'delete_airport_nonce' ); ?>
                                        <input type="hidden" name="airport_id" value="<?php echo esc_attr( $airport->id ); ?>">
                                        <button type="submit" name="delete_airport" class="button button-small button-link-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if ( $total_pages > 1 ) : ?>
                    <div class="airport-pagination" style="margin-top: 20px; padding: 15px 0; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                        <div class="pagination-info" style="color: #666; font-size: 14px;">
                            <?php printf( 'Showing %d - %d of %s airports', ( $offset + 1 ), min( $offset + $per_page, $total_airports ), number_format_i18n( $total_airports ) ); ?>
                        </div>
                        <div class="pagination-links-wrapper">
                            <?php
                            $page_links = paginate_links( array(
                                'base' => add_query_arg( 'paged', '%#%' ),
                                'format' => '',
                                'prev_text' => '&lsaquo; Previous',
                                'next_text' => 'Next &rsaquo;',
                                'total' => $total_pages,
                                'current' => $current_page,
                                'type' => 'list',
                                'end_size' => 2,
                                'mid_size' => 2,
                            ) );
                            
                            if ( $page_links ) {
                                echo $page_links;
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <p>No airports found. Add your first airport above.</p>
            <?php endif; ?>
        </div>
        
        </div><!-- end airport-layout -->

        <!-- Instructions -->
        <div class="airport-mgmt-card" style="margin-top: 20px; background: #f0f6fc; border-left: 4px solid #0073aa;">
            <h3 style="margin-top: 0;">📋 How to Use</h3>
            <ol style="line-height: 1.8;">
                <li><strong>Before importing CSV:</strong> If you see warnings about unknown airports during CSV preview, come here to add them manually.</li>
                <li><strong>Airport Name Format:</strong> Use UPPERCASE and match exactly how it appears in your CSV file (without suffixes like (BIAL) - the system auto-strips those).</li>
                <li><strong>IATA/ICAO Codes:</strong> These are optional but recommended for proper identification.</li>
                <li><strong>After adding:</strong> Go back to PAX Data import and try uploading your CSV again - warnings should be gone!</li>
            </ol>
            
            <h4>Common Indian Airports Already Added:</h4>
            <p style="margin: 5px 0; color: #666;">
                Delhi, Mumbai, Bangalore, Hyderabad, Chennai, Kolkata, Ahmedabad, Cochin, Goa, Pune, Trivandrum, 
                Lucknow, Jaipur, Guwahati, Srinagar, Calicut, Bhubaneswar, Coimbatore, Mangalore, Varanasi, 
                Trichy, Amritsar, Portblair, Imphal, Nagpur, Aurangabad, Gaya, Chandigarh
            </p>
        </div>
    </div>
</div>

<style>
.airport-mgmt-card {
    padding: 20px;
    background: white;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.airport-management-container h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.airport-management-container .wp-list-table {
    margin-top: 15px;
}

.airport-management-container .description {
    font-style: italic;
    color: #666;
}

/* Pagination Styles */
.pagination-links-wrapper .page-numbers {
    display: inline-flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 5px;
}

.pagination-links-wrapper .page-numbers li {
    display: inline-block;
}

.pagination-links-wrapper .page-numbers a,
.pagination-links-wrapper .page-numbers span.current {
    display: inline-block;
    padding: 8px 12px;
    min-width: 40px;
    text-align: center;
    text-decoration: none;
    color: #2271b1;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    line-height: 1;
    transition: all 0.2s ease;
}

.pagination-links-wrapper .page-numbers a:hover {
    background: #f0f0f1;
    border-color: #2271b1;
    color: #135e96;
}

.pagination-links-wrapper .page-numbers span.current {
    background: #2271b1;
    color: white;
    border-color: #2271b1;
    font-weight: 600;
}

.pagination-links-wrapper .page-numbers .dots {
    padding: 8px 12px;
    color: #666;
    border: none;
    background: transparent;
}

.pagination-links-wrapper .page-numbers .prev,
.pagination-links-wrapper .page-numbers .next {
    font-weight: 500;
}

@media (max-width: 1200px) {
    .airport-layout {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 768px) {
    .airport-pagination {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start !important;
    }
    
    .pagination-info {
        width: 100%;
        text-align: center;
    }
    
    .pagination-links-wrapper {
        width: 100%;
    }
    
    .pagination-links-wrapper .page-numbers {
        justify-content: center;
        flex-wrap: wrap;
    }
}
</style>

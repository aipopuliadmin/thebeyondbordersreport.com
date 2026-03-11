<?php
/**
 * PAX Data admin page
 *
 * @package Beyond_Borders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

$pax_data_handler = new Beyond_Borders_Pax_Data();

$current_page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$per_page = 50;
$offset = ( $current_page - 1 ) * $per_page;

// Get filters
$filter_airport = isset( $_GET['filter_airport'] ) ? intval( $_GET['filter_airport'] ) : '';
$filter_pax_type = isset( $_GET['filter_pax_type'] ) ? sanitize_text_field( $_GET['filter_pax_type'] ) : '';
$filter_year = isset( $_GET['filter_year'] ) ? intval( $_GET['filter_year'] ) : '';

$filters = array(
    'limit'  => $per_page,
    'offset' => $offset,
);

if ( $filter_airport ) {
    $filters['airport_id'] = $filter_airport;
}
if ( $filter_pax_type ) {
    $filters['pax_type'] = $filter_pax_type;
}
if ( $filter_year ) {
    $filters['year'] = $filter_year;
}

$pax_records = $pax_data_handler->get_pax_data( $filters );
$total_count = $pax_data_handler->get_pax_data_count( $filters );
$total_pages = ceil( $total_count / $per_page );

$airports = $pax_data_handler->get_airports();

// Get unique years for filter
global $wpdb;
$pax_table = $wpdb->prefix . 'pax_data';
$years = $wpdb->get_col( "SELECT DISTINCT year FROM $pax_table ORDER BY year DESC" );
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <?php if ( isset( $_GET['deleted'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Record deleted successfully.', 'beyond-borders' ); ?></p>
        </div>
    <?php endif; ?>

    <!-- CSV Import Section -->
    <div class="pax-import-section" style="background: #fff; padding: 20px; margin: 20px 0; border-left: 4px solid #D4AF37; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2><?php esc_html_e( 'Import PAX Data from CSV or Excel', 'beyond-borders' ); ?></h2>
        <p><?php esc_html_e( 'Upload a CSV or Excel file (.csv, .xlsx, .xls) with columns: SL.NO, Type of Pax, Airport Type, AIRPORT, Year, Month, PASSENGERS (IN NOS.)', 'beyond-borders' ); ?></p>
        
        <form id="pax-csv-import-form" enctype="multipart/form-data">
            <input type="file" name="csv_file" id="pax-csv-file" accept=".csv,.xlsx,.xls" required>
            <button type="button" id="preview-import-btn" class="button button-secondary" style="margin-left: 10px;">
                <?php esc_html_e( 'Preview Data', 'beyond-borders' ); ?>
            </button>
            <button type="submit" id="import-btn" class="button button-primary" style="margin-left: 10px; display: none;">
                <?php esc_html_e( 'Confirm Import', 'beyond-borders' ); ?>
            </button>
            <span class="spinner" style="float: none; margin: 0 10px;"></span>
        </form>
        
        <div id="import-result" style="margin-top: 15px;"></div>
        <div id="preview-container" style="display: none; margin-top: 20px; padding: 20px; background: #f9f9f9; border-radius: 4px;"></div>
    </div>

    <!-- Stats Cards -->
    <div class="pax-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
        <div class="stat-card" style="background: #fff; padding: 20px; border-left: 4px solid #D4AF37; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;"><?php esc_html_e( 'Total Records', 'beyond-borders' ); ?></h3>
            <p style="margin: 0; font-size: 32px; font-weight: bold; color: #D4AF37;"><?php echo number_format( $total_count ); ?></p>
        </div>
        
        <div class="stat-card" style="background: #fff; padding: 20px; border-left: 4px solid #003366; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;"><?php esc_html_e( 'Total Airports', 'beyond-borders' ); ?></h3>
            <p style="margin: 0; font-size: 32px; font-weight: bold; color: #003366;"><?php echo number_format( count( $airports ) ); ?></p>
        </div>
    </div>

    <!-- Filters and Export -->
    <div class="tablenav top" style="background: #fff; padding: 15px; margin: 20px 0;">
        <form method="get" action="">
            <input type="hidden" name="page" value="beyond-borders-pax-data">
            
            <select name="filter_airport" style="margin-right: 10px;">
                <option value=""><?php esc_html_e( 'All Airports', 'beyond-borders' ); ?></option>
                <?php foreach ( $airports as $airport ) : ?>
                    <option value="<?php echo esc_attr( $airport['id'] ); ?>" <?php selected( $filter_airport, $airport['id'] ); ?>>
                        <?php echo esc_html( $airport['airport_name'] ); ?> (<?php echo esc_html( $airport['iata_code'] ); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="filter_pax_type" style="margin-right: 10px;">
                <option value=""><?php esc_html_e( 'All PAX Types', 'beyond-borders' ); ?></option>
                <option value="INTERNATIONAL PASSENGERS" <?php selected( $filter_pax_type, 'INTERNATIONAL PASSENGERS' ); ?>>
                    <?php esc_html_e( 'International Passengers', 'beyond-borders' ); ?>
                </option>
                <option value="DOMESTIC PASSENGERS" <?php selected( $filter_pax_type, 'DOMESTIC PASSENGERS' ); ?>>
                    <?php esc_html_e( 'Domestic Passengers', 'beyond-borders' ); ?>
                </option>
            </select>
            
            <select name="filter_year" style="margin-right: 10px;">
                <option value=""><?php esc_html_e( 'All Years', 'beyond-borders' ); ?></option>
                <?php foreach ( $years as $year ) : ?>
                    <option value="<?php echo esc_attr( $year ); ?>" <?php selected( $filter_year, $year ); ?>>
                        <?php echo esc_html( $year ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="button"><?php esc_html_e( 'Filter', 'beyond-borders' ); ?></button>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=beyond-borders-pax-data' ) ); ?>" class="button" style="margin-left: 10px;">
                <?php esc_html_e( 'Clear Filters', 'beyond-borders' ); ?>
            </a>
        </form>
        
        <button id="export-pax-csv" class="button button-primary" style="float: right;">
            <?php esc_html_e( 'Export to CSV', 'beyond-borders' ); ?>
        </button>
    </div>

    <!-- Data Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th width="5%"><?php esc_html_e( 'ID', 'beyond-borders' ); ?></th>
                <th width="20%"><?php esc_html_e( 'Airport', 'beyond-borders' ); ?></th>
                <th width="10%"><?php esc_html_e( 'IATA', 'beyond-borders' ); ?></th>
                <th width="20%"><?php esc_html_e( 'PAX Type', 'beyond-borders' ); ?></th>
                <th width="10%"><?php esc_html_e( 'Year', 'beyond-borders' ); ?></th>
                <th width="10%"><?php esc_html_e( 'Month', 'beyond-borders' ); ?></th>
                <th width="15%"><?php esc_html_e( 'Passengers', 'beyond-borders' ); ?></th>
                <th width="10%"><?php esc_html_e( 'Actions', 'beyond-borders' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $pax_records ) ) : ?>
                <?php foreach ( $pax_records as $record ) : ?>
                    <tr data-record-id="<?php echo esc_attr( $record['id'] ); ?>">
                        <td><?php echo esc_html( $record['id'] ); ?></td>
                        <td><?php echo esc_html( $record['airport_name'] ); ?></td>
                        <td><?php echo esc_html( $record['iata_code'] ); ?></td>
                        <td><?php echo esc_html( $record['pax_type'] ); ?></td>
                        <td><?php echo esc_html( $record['year'] ); ?></td>
                        <td><?php echo esc_html( $record['month'] ); ?></td>
                        <td><?php echo number_format( $record['passengers'] ); ?></td>
                        <td>
                            <button class="button button-small edit-pax-record" data-id="<?php echo esc_attr( $record['id'] ); ?>" 
                                data-airport-id="<?php echo esc_attr( $record['airport_id'] ); ?>"
                                data-pax-type="<?php echo esc_attr( $record['pax_type'] ); ?>"
                                data-year="<?php echo esc_attr( $record['year'] ); ?>"
                                data-month="<?php echo esc_attr( $record['month'] ); ?>"
                                data-passengers="<?php echo esc_attr( $record['passengers'] ); ?>">
                                <?php esc_html_e( 'Edit', 'beyond-borders' ); ?>
                            </button>
                            <button class="button button-small delete-pax-record" data-id="<?php echo esc_attr( $record['id'] ); ?>">
                                <?php esc_html_e( 'Delete', 'beyond-borders' ); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px;">
                        <?php esc_html_e( 'No PAX data found. Import a CSV file to get started.', 'beyond-borders' ); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ( $total_pages > 1 ) : ?>
        <div class="pax-data-pagination" style="margin-top: 20px; padding: 15px 0; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
            <div class="pagination-info" style="color: #666; font-size: 14px;">
                <?php 
                $showing_from = $offset + 1;
                $showing_to = min( $offset + $per_page, $total_count );
                printf( 'Showing %d - %d of %s records', $showing_from, $showing_to, number_format( $total_count ) ); 
                ?>
            </div>
            <div class="pagination-links-wrapper">
                <?php
                $pagination_args = array(
                    'base'      => add_query_arg( 'paged', '%#%' ),
                    'format'    => '',
                    'current'   => $current_page,
                    'total'     => $total_pages,
                    'prev_text' => '&lsaquo; Previous',
                    'next_text' => 'Next &rsaquo;',
                    'type'      => 'list',
                    'end_size'  => 2,
                    'mid_size'  => 2,
                );
                echo paginate_links( $pagination_args );
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Edit PAX Data Modal -->
<div id="edit-pax-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 100000; align-items: center; justify-content: center;">
    <div class="edit-modal-content" style="background: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 500px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
        <h2 style="margin-top: 0; color: #003366;"><?php esc_html_e( 'Edit PAX Record', 'beyond-borders' ); ?></h2>
        
        <form id="edit-pax-form">
            <input type="hidden" id="edit-record-id" name="record_id">
            
            <p>
                <label for="edit-airport-id" style="display: block; margin-bottom: 5px; font-weight: 600;"><?php esc_html_e( 'Airport', 'beyond-borders' ); ?></label>
                <select id="edit-airport-id" name="airport_id" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <?php foreach ( $airports as $airport ) : ?>
                        <option value="<?php echo esc_attr( $airport['id'] ); ?>">
                            <?php echo esc_html( $airport['airport_name'] ); ?> (<?php echo esc_html( $airport['iata_code'] ); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            
            <p>
                <label for="edit-pax-type" style="display: block; margin-bottom: 5px; font-weight: 600;"><?php esc_html_e( 'Passenger Type', 'beyond-borders' ); ?></label>
                <select id="edit-pax-type" name="pax_type" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="INTERNATIONAL PASSENGERS">International Passengers</option>
                    <option value="DOMESTIC PASSENGERS">Domestic Passengers</option>
                </select>
            </p>
            
            <p>
                <label for="edit-year" style="display: block; margin-bottom: 5px; font-weight: 600;"><?php esc_html_e( 'Year', 'beyond-borders' ); ?></label>
                <input type="number" id="edit-year" name="year" required min="2000" max="2100" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </p>
            
            <p>
                <label for="edit-month" style="display: block; margin-bottom: 5px; font-weight: 600;"><?php esc_html_e( 'Month', 'beyond-borders' ); ?></label>
                <select id="edit-month" name="month" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="JAN">January</option>
                    <option value="FEB">February</option>
                    <option value="MAR">March</option>
                    <option value="APR">April</option>
                    <option value="MAY">May</option>
                    <option value="JUN">June</option>
                    <option value="JUL">July</option>
                    <option value="AUG">August</option>
                    <option value="SEP">September</option>
                    <option value="OCT">October</option>
                    <option value="NOV">November</option>
                    <option value="DEC">December</option>
                </select>
            </p>
            
            <p>
                <label for="edit-passengers" style="display: block; margin-bottom: 5px; font-weight: 600;"><?php esc_html_e( 'Passengers', 'beyond-borders' ); ?></label>
                <input type="number" id="edit-passengers" name="passengers" required min="0" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </p>
            
            <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancel-edit" class="button" style="padding: 8px 20px;"><?php esc_html_e( 'Cancel', 'beyond-borders' ); ?></button>
                <button type="submit" class="button button-primary" style="padding: 8px 20px;"><?php esc_html_e( 'Save Changes', 'beyond-borders' ); ?></button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    var tempFileName = '';
    
    // Preview Import
    $('#preview-import-btn').on('click', function() {
        var fileInput = $('#pax-csv-file')[0];
        
        if (fileInput.files.length === 0) {
            alert('Please select a CSV or Excel file.');
            return;
        }
        
        var formData = new FormData();
        formData.append('csv_file', fileInput.files[0]);
        formData.append('action', 'preview_pax_import');
        formData.append('nonce', '<?php echo wp_create_nonce( 'pax_data_preview' ); ?>');
        
        $('.spinner').addClass('is-active');
        $('#import-result').html('');
        $('#preview-container').hide();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('.spinner').removeClass('is-active');
                
                if (response.success) {
                    tempFileName = response.data.temp_file;
                    
                    var previewHtml = '<h3>Data Preview</h3>';
                    previewHtml += '<p><strong>Total Rows to Import:</strong> ' + response.data.total_rows + '</p>';
                    previewHtml += '<p><strong>Valid Records (sample):</strong> ' + response.data.valid_count + '</p>';
                    
                    if (response.data.warnings.length > 0) {
                        previewHtml += '<div class="notice notice-warning inline"><p><strong>Warnings:</strong></p><ul>';
                        response.data.warnings.forEach(function(warning) {
                            previewHtml += '<li>' + warning + '</li>';
                        });
                        previewHtml += '</ul></div>';
                    }
                    
                    previewHtml += '<div style="overflow-x: auto; margin-top: 15px;"><table class="wp-list-table widefat fixed striped" style="max-width: 100%;">';
                    previewHtml += '<thead><tr>';
                    
                    if (response.data.preview_rows.length > 0) {
                        response.data.preview_rows[0].forEach(function(header) {
                            previewHtml += '<th>' + header + '</th>';
                        });
                        previewHtml += '</tr></thead><tbody>';
                        
                        for (var i = 1; i < response.data.preview_rows.length && i <= 10; i++) {
                            previewHtml += '<tr>';
                            response.data.preview_rows[i].forEach(function(cell) {
                                previewHtml += '<td>' + cell + '</td>';
                            });
                            previewHtml += '</tr>';
                        }
                        previewHtml += '</tbody>';
                    }
                    
                    previewHtml += '</table></div>';
                    previewHtml += '<p style="margin-top: 15px;"><em>Showing first 10 rows</em></p>';
                    
                    $('#preview-container').html(previewHtml).slideDown();
                    $('#import-btn').show();
                    $('#preview-import-btn').text('Change File');
                } else {
                    $('#import-result').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $('.spinner').removeClass('is-active');
                $('#import-result').html('<div class="notice notice-error"><p>An error occurred during preview.</p></div>');
            }
        });
    });
    
    // Confirm Import
    $('#pax-csv-import-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!tempFileName) {
            alert('Please preview the data first.');
            return;
        }
        
        if (!confirm('Are you sure you want to import this data?')) {
            return;
        }
        
        var formData = new FormData();
        formData.append('temp_file', tempFileName);
        formData.append('action', 'import_pax_csv');
        formData.append('nonce', '<?php echo wp_create_nonce( 'pax_data_import' ); ?>');
        
        $('.spinner').addClass('is-active');
        $('#import-result').html('');
        $('#import-btn').prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('.spinner').removeClass('is-active');
                $('#import-btn').prop('disabled', false);
                
                if (response.success) {
                    var message = '<div class="notice notice-success"><p>' + response.data.message + '</p>';
                    
                    if (response.data.errors && response.data.errors.length > 0) {
                        message += '<p><strong>Errors:</strong></p><ul>';
                        response.data.errors.forEach(function(error) {
                            message += '<li>' + error + '</li>';
                        });
                        message += '</ul>';
                    }
                    
                    message += '</div>';
                    $('#import-result').html(message);
                    
                    // Reset form
                    tempFileName = '';
                    $('#preview-container').slideUp();
                    $('#import-btn').hide();
                    $('#preview-import-btn').text('Preview Data');
                    $('#pax-csv-file').val('');
                    
                    // Reload page after 2 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $('#import-result').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $('.spinner').removeClass('is-active');
                $('#import-btn').prop('disabled', false);
                $('#import-result').html('<div class="notice notice-error"><p>An error occurred during import.</p></div>');
            }
        });
    });
    
    // CSV Export
    $('#export-pax-csv').on('click', function() {
        var url = ajaxurl + '?action=export_pax_csv&nonce=<?php echo wp_create_nonce( 'pax_data_export' ); ?>';
        window.location.href = url;
    });
    
    // Edit PAX record
    $('.edit-pax-record').on('click', function() {
        const $btn = $(this);
        const recordId = $btn.data('id');
        const airportId = $btn.data('airport-id');
        const paxType = $btn.data('pax-type');
        const year = $btn.data('year');
        const month = $btn.data('month');
        const passengers = $btn.data('passengers');
        
        // Populate modal
        $('#edit-record-id').val(recordId);
        $('#edit-airport-id').val(airportId);
        $('#edit-pax-type').val(paxType);
        $('#edit-year').val(year);
        $('#edit-month').val(month);
        $('#edit-passengers').val(passengers);
        
        // Show modal
        $('#edit-pax-modal').css('display', 'flex');
    });
    
    // Close modal
    $('#cancel-edit, #edit-pax-modal').on('click', function(e) {
        if (e.target === this) {
            $('#edit-pax-modal').hide();
        }
    });
    
    // Save edited record
    $('#edit-pax-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'update_pax_record',
            nonce: '<?php echo wp_create_nonce( 'update_pax_record' ); ?>',
            record_id: $('#edit-record-id').val(),
            airport_id: $('#edit-airport-id').val(),
            pax_type: $('#edit-pax-type').val(),
            year: $('#edit-year').val(),
            month: $('#edit-month').val(),
            passengers: $('#edit-passengers').val()
        };
        
        $.post(ajaxurl, formData, function(response) {
            if (response.success) {
                $('#edit-pax-modal').hide();
                location.reload();
            } else {
                alert('Error updating record: ' + (response.data || 'Unknown error'));
            }
        });
    });
    
    // Delete Record
    $('.delete-pax-record').on('click', function() {
        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }
        
        var recordId = $(this).data('id');
        var $row = $(this).closest('tr');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_pax_record',
                nonce: '<?php echo wp_create_nonce( 'pax_data_delete' ); ?>',
                record_id: recordId
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});
</script>

<style>
/* PAX Data Pagination Styles */
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

@media (max-width: 768px) {
    .pax-data-pagination {
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

<style>
.pax-import-section input[type="file"] {
    padding: 5px;
}
.stat-card {
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-2px);
}
</style>

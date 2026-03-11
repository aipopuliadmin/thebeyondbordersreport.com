<?php
/**
 * PAX Data Management Class
 *
 * Handles PAX (passenger) data operations including CSV import/export,
 * data retrieval, and AJAX handlers.
 *
 * @package Beyond_Borders
 */

class Beyond_Borders_Pax_Data {

    /**
     * Constructor
     */
    public function __construct() {
        // AJAX handlers
        add_action( 'wp_ajax_preview_pax_import', array( $this, 'handle_preview_import' ) );
        add_action( 'wp_ajax_import_pax_csv', array( $this, 'handle_csv_import' ) );
        add_action( 'wp_ajax_export_pax_csv', array( $this, 'handle_csv_export' ) );
        add_action( 'wp_ajax_delete_pax_record', array( $this, 'handle_delete_record' ) );
        add_action( 'wp_ajax_update_pax_record', array( $this, 'handle_update_record' ) );
        add_action( 'wp_ajax_get_pax_data', array( $this, 'handle_get_pax_data' ) );
        
        // Brand Visualization AJAX handlers
        add_action( 'wp_ajax_save_brand_division', array( $this, 'handle_save_division' ) );
        add_action( 'wp_ajax_delete_brand_division', array( $this, 'handle_delete_division' ) );
        add_action( 'wp_ajax_save_brand_item', array( $this, 'handle_save_brand' ) );
        add_action( 'wp_ajax_delete_brand_item', array( $this, 'handle_delete_brand' ) );
        add_action( 'wp_ajax_get_brand_hierarchy', array( $this, 'handle_get_hierarchy' ) );
        
        // Frontend AJAX (for public access)
        add_action( 'wp_ajax_nopriv_get_pax_data', array( $this, 'handle_get_pax_data' ) );
        add_action( 'wp_ajax_nopriv_get_brand_hierarchy', array( $this, 'handle_get_hierarchy' ) );
    }

    /**
     * Get all airports from reference table.
     *
     * @return array List of airports
     */
    public function get_airports() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'airports_reference';
        
        $airports = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY airport_name ASC",
            ARRAY_A
        );
        
        return $airports ? $airports : array();
    }

    /**
     * Get airport by ID.
     *
     * @param int $airport_id Airport ID
     * @return array|null Airport data
     */
    public function get_airport( $airport_id ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'airports_reference';
        
        $airport = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $airport_id
            ),
            ARRAY_A
        );
        
        return $airport;
    }

    /**
     * Get PAX data with filters.
     *
     * @param array $args Query arguments
     * @return array PAX data records
     */
    public function get_pax_data( $args = array() ) {
        global $wpdb;
        
        $pax_table = $wpdb->prefix . 'pax_data';
        $airports_table = $wpdb->prefix . 'airports_reference';
        
        $defaults = array(
            'airport_id' => '',
            'iata_code'  => '',
            'icao_code'  => '',
            'pax_type'   => '',
            'year'       => '',
            'month'      => '',
            'year_from'  => '',
            'year_to'    => '',
            'limit'      => 100,
            'offset'     => 0,
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        $where_clauses = array( '1=1' );
        $prepare_args = array();
        
        if ( ! empty( $args['airport_id'] ) ) {
            $where_clauses[] = 'p.airport_id = %d';
            $prepare_args[] = $args['airport_id'];
        }
        
        if ( ! empty( $args['iata_code'] ) ) {
            $where_clauses[] = 'UPPER(a.iata_code) = UPPER(%s)';
            $prepare_args[] = $args['iata_code'];
        }
        
        if ( ! empty( $args['icao_code'] ) ) {
            $where_clauses[] = 'UPPER(a.icao_code) = UPPER(%s)';
            $prepare_args[] = $args['icao_code'];
        }
        
        if ( ! empty( $args['pax_type'] ) ) {
            $where_clauses[] = 'p.pax_type = %s';
            $prepare_args[] = $args['pax_type'];
        }
        
        if ( ! empty( $args['year'] ) ) {
            $where_clauses[] = 'p.year = %d';
            $prepare_args[] = $args['year'];
        }
        
        if ( ! empty( $args['year_from'] ) ) {
            $where_clauses[] = 'p.year >= %d';
            $prepare_args[] = $args['year_from'];
        }
        
        if ( ! empty( $args['year_to'] ) ) {
            $where_clauses[] = 'p.year <= %d';
            $prepare_args[] = $args['year_to'];
        }
        
        if ( ! empty( $args['month'] ) ) {
            $where_clauses[] = 'p.month = %s';
            $prepare_args[] = $args['month'];
        }
        
        $where_sql = implode( ' AND ', $where_clauses );
        
        $query = "
            SELECT 
                p.*,
                a.airport_name,
                a.iata_code,
                a.icao_code,
                a.airport_type
            FROM $pax_table AS p
            LEFT JOIN $airports_table AS a ON p.airport_id = a.id
            WHERE $where_sql
            ORDER BY p.year DESC, p.month DESC
            LIMIT %d OFFSET %d
        ";
        
        $prepare_args[] = $args['limit'];
        $prepare_args[] = $args['offset'];
        
        if ( ! empty( $prepare_args ) ) {
            $query = $wpdb->prepare( $query, $prepare_args );
        }
        
        $results = $wpdb->get_results( $query, ARRAY_A );
        
        return $results ? $results : array();
    }

    /**
     * Get total count of PAX records.
     *
     * @param array $args Query arguments
     * @return int Total count
     */
    public function get_pax_data_count( $args = array() ) {
        global $wpdb;
        
        $pax_table = $wpdb->prefix . 'pax_data';
        
        $where_clauses = array( '1=1' );
        $prepare_args = array();
        
        if ( ! empty( $args['airport_id'] ) ) {
            $where_clauses[] = 'airport_id = %d';
            $prepare_args[] = $args['airport_id'];
        }
        
        if ( ! empty( $args['pax_type'] ) ) {
            $where_clauses[] = 'pax_type = %s';
            $prepare_args[] = $args['pax_type'];
        }
        
        if ( ! empty( $args['year'] ) ) {
            $where_clauses[] = 'year = %d';
            $prepare_args[] = $args['year'];
        }
        
        $where_sql = implode( ' AND ', $where_clauses );
        
        $query = "SELECT COUNT(*) FROM $pax_table WHERE $where_sql";
        
        if ( ! empty( $prepare_args ) ) {
            $query = $wpdb->prepare( $query, $prepare_args );
        }
        
        return (int) $wpdb->get_var( $query );
    }

    /**
     * Insert PAX data record.
     *
     * @param array $data Record data
     * @return int|false Inserted ID or false on failure
     */
    /**
     * Check if exact duplicate record exists (all columns match including passengers).
     *
     * @param array $data PAX data to check
     * @return bool True if duplicate exists, false otherwise
     */
    private function is_duplicate_record( $data ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'pax_data';
        
        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $table_name 
                WHERE airport_id = %d 
                AND pax_type = %s 
                AND year = %d 
                AND month = %s 
                AND passengers = %d
                LIMIT 1",
                $data['airport_id'],
                $data['pax_type'],
                $data['year'],
                $data['month'],
                $data['passengers']
            )
        );
        
        return $existing !== null;
    }

    public function insert_pax_data( $data ) {
        global $wpdb;
        
        // Check for exact duplicate (all columns including passengers match)
        if ( $this->is_duplicate_record( $data ) ) {
            return 'duplicate'; // Return special value to indicate duplicate
        }
        
        $table_name = $wpdb->prefix . 'pax_data';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'airport_id' => $data['airport_id'],
                'pax_type'   => $data['pax_type'],
                'year'       => $data['year'],
                'month'      => $data['month'],
                'passengers' => $data['passengers'],
            ),
            array( '%d', '%s', '%d', '%s', '%d' )
        );
        
        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Delete PAX data record.
     *
     * @param int $id Record ID
     * @return bool Success status
     */
    public function delete_pax_data( $id ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'pax_data';
        
        $result = $wpdb->delete(
            $table_name,
            array( 'id' => $id ),
            array( '%d' )
        );
        
        return $result !== false;
    }

    /**
     * Handle preview import via AJAX.
     */
    public function handle_preview_import() {
        check_ajax_referer( 'pax_data_preview', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
        }
        
        if ( empty( $_FILES['csv_file'] ) ) {
            wp_send_json_error( array( 'message' => 'No file uploaded.' ) );
        }
        
        $file = $_FILES['csv_file'];
        
        // Validate file size (10MB max)
        $max_file_size = 10 * 1024 * 1024; // 10MB in bytes
        if ( $file['size'] > $max_file_size ) {
            wp_send_json_error( array( 'message' => 'File size exceeds maximum allowed size of 10MB.' ) );
        }
        
        // Validate file type by extension and MIME type
        $file_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        $allowed_types = array( 'csv', 'xlsx', 'xls' );
        
        if ( ! in_array( $file_ext, $allowed_types ) ) {
            wp_send_json_error( array( 'message' => 'Please upload a CSV or Excel file (.csv, .xlsx, .xls)' ) );
        }
        
        // Validate MIME type for additional security
        $allowed_mimes = array(
            'text/csv',
            'text/plain',
            'application/csv',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/octet-stream' // Excel files sometimes report as this
        );
        
        $finfo = finfo_open( FILEINFO_MIME_TYPE );
        $mime_type = finfo_file( $finfo, $file['tmp_name'] );
        finfo_close( $finfo );
        
        if ( ! in_array( $mime_type, $allowed_mimes ) ) {
            wp_send_json_error( array( 'message' => 'Invalid file type. MIME type detected: ' . esc_html( $mime_type ) ) );
        }
        
        // Store file temporarily for later import
        $upload_dir = wp_upload_dir();
        $temp_file = $upload_dir['basedir'] . '/pax-import-temp-' . time() . '.' . $file_ext;
        
        if ( ! move_uploaded_file( $file['tmp_name'], $temp_file ) ) {
            wp_send_json_error( array( 'message' => 'Failed to store temporary file.' ) );
        }
        
        // Parse file based on type
        if ( $file_ext === 'csv' ) {
            $csv_data = $this->parse_csv_file( $temp_file );
        } else {
            $csv_data = $this->parse_excel_file( $temp_file, $file_ext );
        }
        
        if ( ! $csv_data ) {
            if ( file_exists( $temp_file ) ) {
                @unlink( $temp_file );
            }
            wp_send_json_error( array( 'message' => 'Failed to parse file.' ) );
        }
        
        // Preview first 10 rows + validate data
        $preview_rows = array_slice( $csv_data, 0, 11 ); // Header + 10 data rows
        $total_rows = count( $csv_data ) - 1; // Exclude header
        
        $warnings = array();
        $valid_count = 0;
        $invalid_airports = array();
        $skipped_rows = 0;
        
        // Validate a sample of data
        $sample_size = min( 50, count( $csv_data ) );
        for ( $i = 1; $i < $sample_size; $i++ ) {
            if ( ! isset( $csv_data[ $i ] ) ) {
                continue;
            }
            
            $row = $csv_data[ $i ];
            $airport_name = isset( $row[3] ) ? trim( $row[3] ) : '';
            $pax_type = isset( $row[1] ) ? trim( $row[1] ) : '';
            $year = isset( $row[4] ) ? intval( $row[4] ) : 0;
            $month = isset( $row[5] ) ? trim( $row[5] ) : '';
            
            // Skip rows with empty airports silently (some rows legitimately don't have airport data)
            if ( empty( $airport_name ) ) {
                $skipped_rows++;
                continue;
            }
            
            // Check other required fields
            if ( empty( $pax_type ) || empty( $year ) || empty( $month ) ) {
                continue;
            }
            
            $airport_id = $this->get_airport_id_by_name( $airport_name );
            if ( ! $airport_id && ! in_array( $airport_name, $invalid_airports ) ) {
                $invalid_airports[] = $airport_name;
            } else if ( $airport_id ) {
                $valid_count++;
            }
        }
        
        if ( ! empty( $invalid_airports ) ) {
            $warnings[] = "Unknown airports found: " . implode( ', ', array_slice( $invalid_airports, 0, 5 ) );
            if ( count( $invalid_airports ) > 5 ) {
                $warnings[] = "... and " . ( count( $invalid_airports ) - 5 ) . " more.";
            }
        }
        
        if ( $skipped_rows > 0 ) {
            $warnings[] = "Note: " . $skipped_rows . " rows with empty airport names will be skipped.";
        }
        
        wp_send_json_success( array(
            'preview_rows' => $preview_rows,
            'total_rows' => $total_rows,
            'valid_count' => $valid_count,
            'warnings' => $warnings,
            'temp_file' => basename( $temp_file ),
        ) );
    }

    /**
     * Handle CSV import via AJAX.
     */
    public function handle_csv_import() {
        check_ajax_referer( 'pax_data_import', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
        }
        
        // Check if importing from temp file (after preview)
        $temp_file_name = isset( $_POST['temp_file'] ) ? sanitize_file_name( $_POST['temp_file'] ) : '';
        
        if ( $temp_file_name ) {
            // Import from previewed temp file
            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['basedir'] . '/' . $temp_file_name;
            
            if ( ! file_exists( $file_path ) ) {
                wp_send_json_error( array( 'message' => 'Temporary file not found. Please upload again.' ) );
            }
            
            $file_ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
            
            // Parse file based on type
            if ( $file_ext === 'csv' ) {
                $csv_data = $this->parse_csv_file( $file_path );
            } else {
                $csv_data = $this->parse_excel_file( $file_path, $file_ext );
            }
            
            // Delete temp file after parsing
            if ( file_exists( $file_path ) ) {
                @unlink( $file_path );
            }
            
        } else {
            // Direct import without preview (legacy support)
            if ( empty( $_FILES['csv_file'] ) ) {
                wp_send_json_error( array( 'message' => 'No file uploaded.' ) );
            }
            
            $file = $_FILES['csv_file'];
            
            // Validate file size (10MB max)
            $max_file_size = 10 * 1024 * 1024;
            if ( $file['size'] > $max_file_size ) {
                wp_send_json_error( array( 'message' => 'File size exceeds maximum allowed size of 10MB.' ) );
            }
            
            // Validate file type by extension and MIME type
            $file_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
            $allowed_types = array( 'csv', 'xlsx', 'xls' );
            
            if ( ! in_array( $file_ext, $allowed_types ) ) {
                wp_send_json_error( array( 'message' => 'Please upload a CSV or Excel file (.csv, .xlsx, .xls)' ) );
            }
            
            // Validate MIME type for additional security
            $allowed_mimes = array(
                'text/csv',
                'text/plain',
                'application/csv',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream'
            );
            
            $finfo = finfo_open( FILEINFO_MIME_TYPE );
            $mime_type = finfo_file( $finfo, $file['tmp_name'] );
            finfo_close( $finfo );
            
            if ( ! in_array( $mime_type, $allowed_mimes ) ) {
                wp_send_json_error( array( 'message' => 'Invalid file type. MIME type detected: ' . esc_html( $mime_type ) ) );
            }
            
            // Parse file based on type
            if ( $file_ext === 'csv' ) {
                $csv_data = $this->parse_csv_file( $file['tmp_name'] );
            } else {
                $csv_data = $this->parse_excel_file( $file['tmp_name'], $file_ext );
            }
        }
        
        if ( ! $csv_data ) {
            wp_send_json_error( array( 'message' => 'Failed to parse file.' ) );
        }
        
        // Import data
        $imported = 0;
        $skipped = 0;
        $errors = array();
        $auto_added_airports = array();
        
        foreach ( $csv_data as $index => $row ) {
            // Skip header row
            if ( $index === 0 ) {
                continue;
            }
            
            // Map CSV columns to database fields
            $airport_name = isset( $row[3] ) ? trim( $row[3] ) : '';
            $pax_type = isset( $row[1] ) ? trim( $row[1] ) : '';
            $year = isset( $row[4] ) ? intval( $row[4] ) : 0;
            $month = isset( $row[5] ) ? trim( $row[5] ) : '';
            $passengers = isset( $row[6] ) ? intval( $row[6] ) : 0;
            
            // Skip rows with empty airports (some rows don't have airport data)
            if ( empty( $airport_name ) ) {
                continue;
            }
            
            // Validate other required fields
            if ( empty( $pax_type ) || empty( $year ) || empty( $month ) ) {
                $errors[] = "Row " . ( $index + 1 ) . " has missing required fields.";
                continue;
            }
            
            // Find airport ID
            $airport_id = $this->get_airport_id_by_name( $airport_name );
            
            // If airport not found, auto-create it
            if ( ! $airport_id ) {
                $airport_id = $this->auto_add_airport( $airport_name );
                
                if ( $airport_id ) {
                    // Track auto-added airports (only add unique ones)
                    if ( ! in_array( $airport_name, $auto_added_airports ) ) {
                        $auto_added_airports[] = $airport_name;
                    }
                } else {
                    $errors[] = "Row " . ( $index + 1 ) . ": Failed to add airport '$airport_name'.";
                    continue;
                }
            }
            
            // Insert data (with duplicate checking)
            $result = $this->insert_pax_data( array(
                'airport_id' => $airport_id,
                'pax_type'   => $pax_type,
                'year'       => $year,
                'month'      => $month,
                'passengers' => $passengers,
            ) );
            
            if ( $result === 'duplicate' ) {
                $skipped++;
            } elseif ( $result ) {
                $imported++;
            } else {
                $errors[] = "Row " . ( $index + 1 ) . ": Failed to insert data.";
            }
        }
        
        // Build success message
        $message = "$imported records imported successfully.";
        if ( $skipped > 0 ) {
            $message .= " $skipped duplicate records were skipped.";
        }
        if ( ! empty( $auto_added_airports ) ) {
            $message .= " " . count( $auto_added_airports ) . " new airport(s) automatically added: " . implode( ', ', $auto_added_airports ) . ".";
        }
        
        wp_send_json_success( array(
            'message'  => $message,
            'imported' => $imported,
            'skipped'  => $skipped,
            'auto_added_airports' => $auto_added_airports,
            'errors'   => $errors,
        ) );
    }

    /**
     * Parse CSV file.
     *
     * @param string $file_path File path
     * @return array|false CSV data or false on failure
     */
    private function parse_csv_file( $file_path ) {
        if ( ! file_exists( $file_path ) ) {
            return false;
        }
        
        $csv_data = array();
        
        if ( ( $handle = fopen( $file_path, 'r' ) ) !== false ) {
            while ( ( $row = fgetcsv( $handle, 10000, ',' ) ) !== false ) {
                $csv_data[] = $row;
            }
            fclose( $handle );
        }
        
        return $csv_data;
    }

    /**
     * Parse Excel file (.xlsx or .xls).
     *
     * @param string $file_path File path
     * @param string $file_ext File extension
     * @return array|false Excel data or false on failure
     */
    private function parse_excel_file( $file_path, $file_ext ) {
        if ( ! file_exists( $file_path ) ) {
            return false;
        }
        
        // Check if SimpleXLSX library exists (lightweight alternative to PHPSpreadsheet)
        $simplexlsx_path = BEYOND_BORDERS_PLUGIN_DIR . 'includes/libraries/simplexlsx.php';
        
        if ( file_exists( $simplexlsx_path ) ) {
            require_once $simplexlsx_path;
            
            try {
                if ( $file_ext === 'xlsx' ) {
                    $xlsx = SimpleXLSX::parse( $file_path );
                } else {
                    // For .xls files, try SimpleXLS
                    $simplexls_path = BEYOND_BORDERS_PLUGIN_DIR . 'includes/libraries/simplexls.php';
                    if ( file_exists( $simplexls_path ) ) {
                        require_once $simplexls_path;
                        $xlsx = SimpleXLS::parse( $file_path );
                    } else {
                        return $this->parse_excel_fallback( $file_path );
                    }
                }
                
                if ( $xlsx ) {
                    return $xlsx->rows();
                }
            } catch ( Exception $e ) {
                error_log( 'Excel parse error: ' . $e->getMessage() );
            }
        }
        
        // Fallback: Use built-in PHP methods for basic Excel reading
        return $this->parse_excel_fallback( $file_path );
    }

    /**
     * Fallback Excel parsing using PHP's ZIP extension.
     * Works for simple XLSX files without external libraries.
     *
     * @param string $file_path File path
     * @return array|false Parsed data or false on failure
     */
    private function parse_excel_fallback( $file_path ) {
        // For XLSX files, we can read them as ZIP files
        if ( ! class_exists( 'ZipArchive' ) ) {
            error_log( 'ZipArchive class not available for Excel parsing' );
            return false;
        }
        
        $zip = new ZipArchive();
        if ( $zip->open( $file_path ) !== true ) {
            return false;
        }
        
        // Read shared strings
        $shared_strings = array();
        $strings_xml = $zip->getFromName( 'xl/sharedStrings.xml' );
        if ( $strings_xml ) {
            $strings = simplexml_load_string( $strings_xml );
            if ( $strings ) {
                foreach ( $strings->si as $val ) {
                    $shared_strings[] = (string) $val->t;
                }
            }
        }
        
        // Read worksheet data - Check both sheets and pick the one with correct format
        $sheet_xml = null;
        $sheets_to_try = array( 'xl/worksheets/sheet2.xml', 'xl/worksheets/sheet1.xml' );
        
        foreach ( $sheets_to_try as $sheet_path ) {
            $test_xml = $zip->getFromName( $sheet_path );
            if ( $test_xml && $this->validate_sheet_format( $test_xml, $shared_strings ) ) {
                $sheet_xml = $test_xml;
                break;
            }
        }
        
        // If no valid sheet found, use sheet2 or sheet1 as fallback
        if ( ! $sheet_xml ) {
            $sheet_xml = $zip->getFromName( 'xl/worksheets/sheet2.xml' );
            if ( ! $sheet_xml ) {
                $sheet_xml = $zip->getFromName( 'xl/worksheets/sheet1.xml' );
            }
        }
        
        $zip->close();
        
        if ( ! $sheet_xml ) {
            return false;
        }
        
        $sheet = simplexml_load_string( $sheet_xml );
        if ( ! $sheet ) {
            return false;
        }
        
        $rows = array();
        $current_row = array();
        $last_row_index = 0;
        
        foreach ( $sheet->sheetData->row as $row ) {
            $row_index = (int) $row['r'];
            
            // Fill any skipped rows with empty arrays
            while ( $last_row_index < $row_index - 1 ) {
                $rows[] = array();
                $last_row_index++;
            }
            
            $current_row = array();
            $last_col_index = 0;
            
            foreach ( $row->c as $cell ) {
                $col_index = $this->column_index_from_string( (string) $cell['r'] );
                
                // Fill any skipped columns
                while ( $last_col_index < $col_index ) {
                    $current_row[] = '';
                    $last_col_index++;
                }
                
                $value = '';
                if ( isset( $cell['t'] ) && $cell['t'] == 's' ) {
                    // Shared string
                    $index = (int) $cell->v;
                    $value = isset( $shared_strings[ $index ] ) ? $shared_strings[ $index ] : '';
                } else {
                    // Direct value
                    $value = (string) $cell->v;
                }
                
                $current_row[] = $value;
                $last_col_index++;
            }
            
            $rows[] = $current_row;
            $last_row_index = $row_index;
        }
        
        return $rows;
    }

    /**
     * Get column index from Excel cell reference (e.g., 'A1' -> 0, 'B1' -> 1).
     *
     * @param string $cell Cell reference
     * @return int Column index
     */
    private function column_index_from_string( $cell ) {
        preg_match( '/^([A-Z]+)/', $cell, $matches );
        $col = $matches[1];
        
        $index = 0;
        $length = strlen( $col );
        
        for ( $i = 0; $i < $length; $i++ ) {
            $index = $index * 26 + ( ord( $col[ $i ] ) - ord( 'A' ) );
        }
        
        return $index;
    }

    /**
     * Get airport ID by name.
     *
     * @param string $airport_name Airport name
     * @return int|false Airport ID or false if not found
     */
    private function get_airport_id_by_name( $airport_name ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'airports_reference';
        
        // Clean the airport name
        $cleaned_name = trim( $airport_name );
        
        // First try exact match (case-insensitive)
        $airport_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $table_name WHERE UPPER(airport_name) = UPPER(%s)",
                $cleaned_name
            )
        );
        
        // If not found, try stripping suffixes like (BIAL), (GHIAL), (CIAL), (MIPL), etc.
        if ( ! $airport_id ) {
            // Remove anything in parentheses and variations
            $base_name = preg_replace( '/\s*\([^)]*\)\s*$/', '', $cleaned_name );
            $base_name = trim( $base_name );
            
            if ( $base_name !== $cleaned_name ) {
                $airport_id = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT id FROM $table_name WHERE UPPER(airport_name) = UPPER(%s)",
                        $base_name
                    )
                );
            }
        }
        
        return $airport_id ? (int) $airport_id : false;
    }

    /**
     * Automatically add a new airport to the reference table.
     *
     * @param string $airport_name Airport name
     * @return int|false Airport ID on success, false on failure
     */
    private function auto_add_airport( $airport_name ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'airports_reference';
        
        // Clean airport name (strip suffixes like (BIAL), etc.)
        $cleaned_name = trim( $airport_name );
        $cleaned_name = preg_replace( '/\s*\([^)]*\)\s*$/', '', $cleaned_name );
        $cleaned_name = trim( strtoupper( $cleaned_name ) );
        
        // Check if it already exists (shouldn't happen, but safety check)
        $existing_id = $this->get_airport_id_by_name( $cleaned_name );
        if ( $existing_id ) {
            return $existing_id;
        }
        
        // Insert new airport
        $result = $wpdb->insert(
            $table_name,
            array(
                'airport_name' => $cleaned_name,
                'iata_code'    => '',
                'icao_code'    => '',
                'airport_type' => 'AUTO-ADDED',
            ),
            array( '%s', '%s', '%s', '%s' )
        );
        
        if ( $result ) {
            return $wpdb->insert_id;
        }
        
        return false;
    }

    /**
     * Validate if a sheet has the correct PAX data format.
     * Checks if the header row contains expected column names.
     *
     * @param string $sheet_xml Sheet XML content
     * @param array $shared_strings Shared strings array
     * @return bool True if format is valid
     */
    private function validate_sheet_format( $sheet_xml, $shared_strings ) {
        $sheet = simplexml_load_string( $sheet_xml );
        if ( ! $sheet ) {
            return false;
        }
        
        // Get first row (header row)
        $first_row = $sheet->sheetData->row[0] ?? null;
        if ( ! $first_row ) {
            return false;
        }
        
        $headers = array();
        foreach ( $first_row->c as $cell ) {
            $value = '';
            if ( isset( $cell['t'] ) && $cell['t'] == 's' ) {
                $index = (int) $cell->v;
                $value = isset( $shared_strings[ $index ] ) ? strtoupper( trim( $shared_strings[ $index ] ) ) : '';
            } else {
                $value = strtoupper( trim( (string) $cell->v ) );
            }
            $headers[] = $value;
        }
        
        // Check if headers contain expected PAX data columns
        $expected_columns = array( 'TYPE OF PAX', 'AIRPORT', 'YEAR', 'MONTH', 'PASSENGER' );
        $matches = 0;
        
        foreach ( $expected_columns as $expected ) {
            foreach ( $headers as $header ) {
                if ( strpos( $header, $expected ) !== false ) {
                    $matches++;
                    break;
                }
            }
        }
        
        // Consider valid if at least 4 out of 5 expected columns are found
        return $matches >= 4;
    }

    /**
     * Handle CSV export via AJAX.
     */
    public function handle_csv_export() {
        check_ajax_referer( 'pax_data_export', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized access.' );
        }
        
        global $wpdb;
        
        $pax_table = $wpdb->prefix . 'pax_data';
        $airports_table = $wpdb->prefix . 'airports_reference';
        
        $results = $wpdb->get_results( "
            SELECT 
                p.id,
                p.pax_type,
                a.airport_name,
                p.year,
                p.month,
                p.passengers,
                a.iata_code,
                a.icao_code,
                a.airport_type
            FROM $pax_table AS p
            LEFT JOIN $airports_table AS a ON p.airport_id = a.id
            ORDER BY p.year DESC, p.month DESC
        ", ARRAY_A );
        
        // Set headers for CSV download
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="pax-data-export-' . date( 'Y-m-d' ) . '.csv"' );
        
        $output = fopen( 'php://output', 'w' );
        
        // Add CSV headers
        fputcsv( $output, array( 'ID', 'PAX Type', 'Airport', 'Year', 'Month', 'Passengers', 'IATA', 'ICAO', 'Airport Type' ) );
        
        // Add data rows
        foreach ( $results as $row ) {
            fputcsv( $output, $row );
        }
        
        fclose( $output );
        exit;
    }

    /**
     * Handle delete record via AJAX.
     */
    public function handle_delete_record() {
        check_ajax_referer( 'pax_data_delete', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
        }
        
        $record_id = isset( $_POST['record_id'] ) ? intval( $_POST['record_id'] ) : 0;
        
        if ( ! $record_id ) {
            wp_send_json_error( array( 'message' => 'Invalid record ID.' ) );
        }
        
        $result = $this->delete_pax_data( $record_id );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => 'Record deleted successfully.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to delete record.' ) );
        }
    }

    /**
     * Handle update PAX record via AJAX.
     */
    public function handle_update_record() {
        check_ajax_referer( 'update_pax_record', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
        }
        
        // Sanitize and validate inputs
        $record_id = isset( $_POST['record_id'] ) ? intval( $_POST['record_id'] ) : 0;
        $airport_id = isset( $_POST['airport_id'] ) ? intval( $_POST['airport_id'] ) : 0;
        $pax_type = isset( $_POST['pax_type'] ) ? sanitize_text_field( $_POST['pax_type'] ) : '';
        $year = isset( $_POST['year'] ) ? intval( $_POST['year'] ) : 0;
        $month = isset( $_POST['month'] ) ? sanitize_text_field( $_POST['month'] ) : '';
        $passengers = isset( $_POST['passengers'] ) ? intval( $_POST['passengers'] ) : 0;
        
        // Validate required fields
        if ( ! $record_id || ! $airport_id || ! $pax_type || ! $year || ! $month ) {
            wp_send_json_error( array( 'message' => 'Missing required fields.' ) );
        }
        
        // Validate year range
        if ( $year < 1900 || $year > 2100 ) {
            wp_send_json_error( array( 'message' => 'Invalid year value.' ) );
        }
        
        // Validate passengers (should be non-negative)
        if ( $passengers < 0 ) {
            wp_send_json_error( array( 'message' => 'Passengers cannot be negative.' ) );
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'pax_data';
        
        // Check for duplicates (same airport, pax_type, year, month, but different record_id)
        $duplicate = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $table_name 
                WHERE airport_id = %d AND pax_type = %s AND year = %d AND month = %s AND id != %d",
                $airport_id, $pax_type, $year, $month, $record_id
            )
        );
        
        if ( $duplicate ) {
            wp_send_json_error( array( 'message' => 'A record with the same airport, type, year, and month already exists.' ) );
        }
        
        // Update the record
        $result = $wpdb->update(
            $table_name,
            array(
                'airport_id' => $airport_id,
                'pax_type'   => $pax_type,
                'year'       => $year,
                'month'      => $month,
                'passengers' => $passengers,
            ),
            array( 'id' => $record_id ),
            array( '%d', '%s', '%d', '%s', '%d' ),
            array( '%d' )
        );
        
        if ( $result !== false ) {
            wp_send_json_success( array( 'message' => 'Record updated successfully.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to update record.' ) );
        }
    }

    /**
     * Handle get PAX data via AJAX (for frontend charts).
     */
    public function handle_get_pax_data() {
        check_ajax_referer( 'pax_data_chart', 'nonce' );
        
        // Explicitly validate and sanitize inputs
        $airport_id = isset( $_POST['airport_id'] ) ? absint( $_POST['airport_id'] ) : '';
        $pax_type = isset( $_POST['pax_type'] ) ? sanitize_text_field( $_POST['pax_type'] ) : '';
        $year_from = isset( $_POST['year_from'] ) && is_numeric( $_POST['year_from'] ) ? absint( $_POST['year_from'] ) : '';
        $year_to = isset( $_POST['year_to'] ) && is_numeric( $_POST['year_to'] ) ? absint( $_POST['year_to'] ) : '';
        $month = isset( $_POST['month'] ) ? sanitize_text_field( $_POST['month'] ) : '';
        
        // Validate year range
        if ( $year_from && $year_to && $year_from > $year_to ) {
            wp_send_json_error( array( 'message' => 'Invalid year range.' ) );
        }
        
        // Validate year values are reasonable (1900-2100)
        if ( ( $year_from && ( $year_from < 1900 || $year_from > 2100 ) ) ||
             ( $year_to && ( $year_to < 1900 || $year_to > 2100 ) ) ) {
            wp_send_json_error( array( 'message' => 'Invalid year values.' ) );
        }
        
        $args = array(
            'airport_id' => $airport_id,
            'pax_type'   => $pax_type,
            'year_from'  => $year_from,
            'year_to'    => $year_to,
            'month'      => $month,
            'limit'      => 1000, // Get more data for charts
        );
        
        $data = $this->get_pax_data( $args );
        
        wp_send_json_success( array( 'data' => $data ) );
    }

    /**
     * Handle save division AJAX
     */
    public function handle_save_division() {
        check_ajax_referer( 'brand_viz_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
        }
        
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';
        
        $result = Beyond_Borders_Brand_Viz::save_division( $_POST );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => 'Division saved successfully.', 'id' => $result ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to save division.' ) );
        }
    }

    /**
     * Handle delete division AJAX
     */
    public function handle_delete_division() {
        check_ajax_referer( 'brand_viz_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
        }
        
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';
        
        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $result = Beyond_Borders_Brand_Viz::delete_division( $id );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => 'Division deleted successfully.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to delete division.' ) );
        }
    }

    /**
     * Handle save brand AJAX
     */
    public function handle_save_brand() {
        check_ajax_referer( 'brand_viz_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
        }
        
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';
        
        $result = Beyond_Borders_Brand_Viz::save_brand( $_POST );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => 'Brand saved successfully.', 'id' => $result ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to save brand.' ) );
        }
    }

    /**
     * Handle delete brand AJAX
     */
    public function handle_delete_brand() {
        check_ajax_referer( 'brand_viz_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized access.' ) );
        }
        
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';
        
        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $result = Beyond_Borders_Brand_Viz::delete_brand( $id );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => 'Brand deleted successfully.' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to delete brand.' ) );
        }
    }

    /**
     * Handle get hierarchy AJAX
     */
    public function handle_get_hierarchy() {
        require_once BEYOND_BORDERS_PLUGIN_DIR . 'includes/class-beyond-borders-brand-viz.php';
        
        $parent_id = isset( $_GET['parent_id'] ) ? intval( $_GET['parent_id'] ) : 0;
        $hierarchy = Beyond_Borders_Brand_Viz::get_hierarchy( $parent_id );
        
        // If parent_id is 0, wrap in a root object
        if ( $parent_id === 0 ) {
            $root = array(
                'id' => 0,
                'name' => 'LVMH',
                'color' => '#667eea',
                'description' => 'Luxury Conglomerate',
                'type' => 'root',
                'children' => $hierarchy,
                'brands' => array()
            );
            wp_send_json_success( array( 'data' => $root ) );
        } else {
            wp_send_json_success( array( 'data' => $hierarchy ) );
        }
    }
}

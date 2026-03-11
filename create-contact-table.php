<?php
/**
 * Create Contact Submissions Table
 * Run this file once to create the database table
 */

require_once 'wp-load.php';

global $wpdb;
$table_name = $wpdb->prefix . 'contact_submissions';
$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    first_name varchar(100) NOT NULL,
    last_name varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    phone varchar(50) DEFAULT NULL,
    subject varchar(100) NOT NULL,
    message text NOT NULL,
    newsletter_subscribe tinyint(1) DEFAULT 0,
    ip_address varchar(45) DEFAULT NULL,
    user_agent text DEFAULT NULL,
    submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
    status varchar(20) DEFAULT 'new',
    PRIMARY KEY  (id),
    KEY email (email),
    KEY submitted_at (submitted_at)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

echo "Contact submissions table created successfully!\n";
echo "Table name: " . $table_name . "\n";

// Verify table exists
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
echo "Table exists: " . ($table_exists ? 'Yes' : 'No') . "\n";

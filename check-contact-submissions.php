<?php
require_once 'wp-load.php';

global $wpdb;
$table_name = $wpdb->prefix . 'contact_submissions';

$count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
echo "Total contact submissions: " . $count . "\n\n";

if ($count > 0) {
    $submissions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC LIMIT 5");
    echo "Latest submissions:\n";
    foreach ($submissions as $sub) {
        echo "- ID: {$sub->id}, Name: {$sub->first_name} {$sub->last_name}, Email: {$sub->email}, Subject: {$sub->subject}, Date: {$sub->submitted_at}\n";
    }
}

<?php
/**
 * MySQL to Supabase PostgreSQL Migration Script
 * This script exports data from local MySQL and generates SQL for Supabase
 */

$host = 'localhost';
$dbname = 'prize_db';
$username = 'root';
$password = '';

// Disable error display, only log errors
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: text/plain; charset=utf-8');

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "-- Supabase PostgreSQL Migration SQL\n";
    echo "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Export winners table
    echo "-- =============================================\n";
    echo "-- Winners Table Data\n";
    echo "-- =============================================\n\n";
    
    $stmt = $conn->query("SELECT * FROM winners ORDER BY id");
    $winners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($winners) > 0) {
        foreach ($winners as $row) {
            $name_arabic = addslashes($row['name_arabic']);
            $name_english = addslashes($row['name_english']);
            $phone = addslashes($row['phone_number']);
            $created_at = $row['created_at'];
            
            echo "INSERT INTO winners (name_arabic, name_english, phone_number, created_at) VALUES\n";
            echo "('$name_arabic', '$name_english', '$phone', '$created_at');\n";
        }
        echo "\n";
    } else {
        echo "-- No data in winners table\n\n";
    }
    
    // Export page_visits table
    echo "-- =============================================\n";
    echo "-- Page Visits Table Data\n";
    echo "-- =============================================\n\n";
    
    $stmt = $conn->query("SELECT * FROM page_visits ORDER BY id LIMIT 100");
    $visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($visits) > 0) {
        foreach ($visits as $row) {
            $fields = [];
            $values = [];
            
            foreach ($row as $key => $value) {
                if ($key != 'id' && $value !== null) {
                    $fields[] = $key;
                    $values[] = "'" . addslashes($value) . "'";
                }
            }
            
            if (count($fields) > 0) {
                echo "INSERT INTO page_visits (" . implode(', ', $fields) . ") VALUES\n";
                echo "(" . implode(', ', $values) . ");\n";
            }
        }
        echo "\n";
    } else {
        echo "-- No data in page_visits table\n\n";
    }
    
    // Export unique_links table if exists
    $stmt = $conn->query("SHOW TABLES LIKE 'unique_links'");
    if ($stmt->rowCount() > 0) {
        echo "-- =============================================\n";
        echo "-- Unique Links Table Data\n";
        echo "-- =============================================\n\n";
        
        $stmt = $conn->query("SELECT * FROM unique_links ORDER BY id");
        $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($links) > 0) {
            foreach ($links as $row) {
                $link_code = isset($row['link_code']) ? addslashes($row['link_code']) : '';
                $employee_id = isset($row['employee_id']) ? addslashes($row['employee_id']) : '';
                $name_arabic = isset($row['name_arabic']) ? addslashes($row['name_arabic']) : '';
                $name_english = isset($row['name_english']) ? addslashes($row['name_english']) : '';
                $email = isset($row['email']) ? addslashes($row['email']) : '';
                $created_at = isset($row['created_at']) ? $row['created_at'] : 'CURRENT_TIMESTAMP';
                $is_active = isset($row['is_active']) && $row['is_active'] ? 'true' : 'false';
                
                echo "INSERT INTO unique_links (link_code, employee_id, name_arabic, name_english, email, created_at, is_active) VALUES\n";
                echo "('$link_code', '$employee_id', '$name_arabic', '$name_english', '$email', '$created_at', $is_active);\n";
            }
            echo "\n";
        } else {
            echo "-- No data in unique_links table\n\n";
        }
    }
    
    echo "-- =============================================\n";
    echo "-- Migration Complete\n";
    echo "-- =============================================\n";
    
    // Summary
    echo "\n-- Summary:\n";
    $stmt = $conn->query("SELECT COUNT(*) as count FROM winners");
    $winner_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "-- Winners: $winner_count records\n";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM page_visits");
    $visits_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "-- Page Visits: $visits_count records\n";
    
} catch(PDOException $e) {
    echo "-- Error: " . $e->getMessage() . "\n";
}
?>

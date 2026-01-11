<?php
/**
 * Database Setup Script
 * Run this file once to create the database and table
 * Access: http://localhost/prize/setup_database.php
 */

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'prize_db';

try {
    // Create connection without database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sql);
    echo "✓ Database '$dbname' created successfully<br>";
    
    // Use the database
    $conn->exec("USE $dbname");
    
    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS winners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name_arabic VARCHAR(255) NOT NULL,
        name_english VARCHAR(255) NOT NULL,
        iban_number VARCHAR(34) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ip (ip_address),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->exec($sql);
    echo "✓ Table 'winners' created successfully<br>";
    
    echo "<br><strong>Setup completed successfully!</strong><br>";
    echo "You can now access your winning page at: <a href='index.php'>index.php</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Setup</h1>
        <hr>
        <div style="margin-top: 20px;">

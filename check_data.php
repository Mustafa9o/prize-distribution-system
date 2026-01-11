<?php
// Check existing data in local MySQL database

$host = 'localhost';
$dbname = 'prize_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Migration Report</h2>";
    
    // Check winners table
    echo "<h3>Winners Table</h3>";
    $stmt = $conn->query("SELECT COUNT(*) as count FROM winners");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Total records: <strong>$count</strong><br><br>";
    
    if ($count > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name (Arabic)</th><th>Name (English)</th><th>Phone</th><th>Created At</th></tr>";
        $stmt = $conn->query("SELECT * FROM winners ORDER BY id");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['name_arabic']}</td>";
            echo "<td>{$row['name_english']}</td>";
            echo "<td>{$row['phone_number']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    // Check page_visits table
    echo "<h3>Page Visits Table</h3>";
    $stmt = $conn->query("SELECT COUNT(*) as count FROM page_visits");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Total records: <strong>$count</strong><br><br>";
    
    // Check unique_links table if exists
    $stmt = $conn->query("SHOW TABLES LIKE 'unique_links'");
    if ($stmt->rowCount() > 0) {
        echo "<h3>Unique Links Table</h3>";
        $stmt = $conn->query("SELECT COUNT(*) as count FROM unique_links");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Total records: <strong>$count</strong><br><br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

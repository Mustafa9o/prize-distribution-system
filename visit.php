<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'prize_db';
$username = 'root';
$password = '';

$code = isset($_GET['code']) ? $_GET['code'] : null;

if ($code) {
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Find the user by unique code
        $sql = "SELECT * FROM unique_links WHERE unique_code = :code";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':code' => $code]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Update click count and last click time
            $sql_update = "UPDATE unique_links SET clicks = clicks + 1, last_click = NOW() WHERE unique_code = :code";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([':code' => $code]);
            
            // Log the visit immediately
            $ip = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            
            $sql_visit = "INSERT INTO page_visits (
                ip_address, tracked_emp_id, tracked_name_arabic, tracked_name_english, 
                tracked_email, user_agent, referrer, visit_time
            ) VALUES (
                :ip, :emp_id, :name_arabic, :name_english, :email, :user_agent, :referrer, NOW()
            )";
            
            $stmt_visit = $conn->prepare($sql_visit);
            $stmt_visit->execute([
                ':ip' => $ip,
                ':emp_id' => $user['emp_id'],
                ':name_arabic' => $user['name_arabic'],
                ':name_english' => $user['name_english'],
                ':email' => $user['email'],
                ':user_agent' => $user_agent,
                ':referrer' => $referrer
            ]);
            
            // Store user info in session for tracking
            $_SESSION['tracked_user'] = [
                'emp_id' => $user['emp_id'],
                'name_arabic' => $user['name_arabic'],
                'name_english' => $user['name_english'],
                'email' => $user['email']
            ];
            
            // Mark this visit with unique timestamp to allow logging
            $_SESSION['should_log'] = microtime(true);
            
            // Redirect to main page
            header("Location: index.php");
            exit();
        } else {
            // Invalid code, redirect anyway
            header("Location: index.php");
            exit();
        }
        
    } catch(PDOException $e) {
        // Error, redirect anyway
        header("Location: index.php");
        exit();
    }
} else {
    // No code provided, redirect to main page
    header("Location: index.php");
    exit();
}
?>

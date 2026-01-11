<?php
// Database configuration
$host = 'localhost';
$dbname = 'prize_db';
$username = 'root';
$password = '';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct/No Referrer';
        
        // Get tracked user info from session if available
        session_start();
        $tracked_emp_id = isset($_SESSION['tracked_user']) ? $_SESSION['tracked_user']['emp_id'] : null;
        $tracked_name_arabic = isset($_SESSION['tracked_user']) ? $_SESSION['tracked_user']['name_arabic'] : null;
        $tracked_name_english = isset($_SESSION['tracked_user']) ? $_SESSION['tracked_user']['name_english'] : null;
        $tracked_email = isset($_SESSION['tracked_user']) ? $_SESSION['tracked_user']['email'] : null;
        
        // Only log if user came from unique URL click (should_log flag is set)
        if (!isset($_SESSION['should_log'])) {
            // Not from a unique URL click, skip logging
            echo json_encode(['success' => true, 'message' => 'No tracking flag']);
            exit;
        }
        
        // Get the timestamp and clear the flag immediately to prevent duplicate logs from same page load
        $log_timestamp = $_SESSION['should_log'];
        unset($_SESSION['should_log']);
        
        // Get geolocation data from IP
        $geo_data = @json_decode(file_get_contents("http://ip-api.com/json/{$ip_address}"), true);
        $country = isset($geo_data['country']) ? $geo_data['country'] : 'Unknown';
        $city = isset($geo_data['city']) ? $geo_data['city'] : 'Unknown';
        
        // Check if a record exists for this session (created by visit.php)
        // Look for recent visit (within last 30 seconds) from same IP and employee
        $check_sql = "SELECT id FROM page_visits 
                      WHERE tracked_emp_id = :emp_id 
                      AND visit_time >= DATE_SUB(NOW(), INTERVAL 30 SECOND)
                      AND (country IS NULL OR country = '' OR country = 'Unknown')
                      ORDER BY visit_time DESC LIMIT 1";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([':emp_id' => $tracked_emp_id]);
        $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Update the existing record with device details
            $sql = "UPDATE page_visits SET
                local_ip = :local_ip,
                country = :country,
                city = :city,
                timezone = :timezone,
                language = :language,
                screen_size = :screen_size,
                color_scheme = :color_scheme,
                browser = :browser,
                os = :os,
                platform = :platform,
                touch_screen = :touch_screen,
                orientation = :orientation,
                gpu = :gpu,
                ram = :ram,
                cpu_cores = :cpu_cores,
                device_type = :device_type,
                device_model = :device_model,
                pixel_ratio = :pixel_ratio,
                connection_type = :connection_type,
                battery_level = :battery_level
                WHERE id = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':id' => $existing['id'],
                ':local_ip' => $data['local_ip'] ?? 'Unknown',
                ':country' => $country,
                ':city' => $city,
                ':timezone' => $data['timezone'] ?? 'Unknown',
                ':language' => $data['language'] ?? 'Unknown',
                ':screen_size' => $data['screen_size'] ?? 'Unknown',
                ':color_scheme' => $data['color_scheme'] ?? 'Unknown',
                ':browser' => $data['browser'] ?? 'Unknown',
                ':os' => $data['os'] ?? 'Unknown',
                ':platform' => $data['platform'] ?? 'Unknown',
                ':touch_screen' => $data['touch_screen'] ?? 'Unknown',
                ':orientation' => $data['orientation'] ?? 'Unknown',
                ':gpu' => $data['gpu'] ?? 'Unknown',
                ':ram' => $data['ram'] ?? 'Unknown',
                ':cpu_cores' => $data['cpu_cores'] ?? 'Unknown',
                ':device_type' => $data['device_type'] ?? 'Unknown',
                ':device_model' => $data['device_model'] ?? 'Unknown',
                ':pixel_ratio' => $data['pixel_ratio'] ?? 'Unknown',
                ':connection_type' => $data['connection_type'] ?? 'Unknown',
                ':battery_level' => $data['battery_level'] ?? 'Unknown'
            ]);
        } else {
            // Insert new record (fallback if visit.php didn't create one)
            $sql = "INSERT INTO page_visits (
                ip_address, local_ip, tracked_emp_id, tracked_name_arabic, tracked_name_english, tracked_email,
                country, city, timezone, language, screen_size, 
                color_scheme, browser, os, platform, user_agent, referrer, 
                touch_screen, orientation, gpu, ram, cpu_cores, device_type,
                device_model, pixel_ratio, connection_type, battery_level
            ) VALUES (
                :ip, :local_ip, :tracked_emp_id, :tracked_name_arabic, :tracked_name_english, :tracked_email,
                :country, :city, :timezone, :language, :screen_size,
                :color_scheme, :browser, :os, :platform, :user_agent, :referrer,
                :touch_screen, :orientation, :gpu, :ram, :cpu_cores, :device_type,
                :device_model, :pixel_ratio, :connection_type, :battery_level
            )";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':ip' => $ip_address,
                ':local_ip' => $data['local_ip'] ?? 'Unknown',
                ':tracked_emp_id' => $tracked_emp_id,
                ':tracked_name_arabic' => $tracked_name_arabic,
                ':tracked_name_english' => $tracked_name_english,
                ':tracked_email' => $tracked_email,
                ':country' => $country,
                ':city' => $city,
                ':timezone' => $data['timezone'] ?? 'Unknown',
                ':language' => $data['language'] ?? 'Unknown',
                ':screen_size' => $data['screen_size'] ?? 'Unknown',
                ':color_scheme' => $data['color_scheme'] ?? 'Unknown',
                ':browser' => $data['browser'] ?? 'Unknown',
                ':os' => $data['os'] ?? 'Unknown',
                ':platform' => $data['platform'] ?? 'Unknown',
                ':user_agent' => $user_agent,
                ':referrer' => $referrer,
                ':touch_screen' => $data['touch_screen'] ?? 'Unknown',
                ':orientation' => $data['orientation'] ?? 'Unknown',
                ':gpu' => $data['gpu'] ?? 'Unknown',
                ':ram' => $data['ram'] ?? 'Unknown',
                ':cpu_cores' => $data['cpu_cores'] ?? 'Unknown',
                ':device_type' => $data['device_type'] ?? 'Unknown',
                ':device_model' => $data['device_model'] ?? 'Unknown',
                ':pixel_ratio' => $data['pixel_ratio'] ?? 'Unknown',
                ':connection_type' => $data['connection_type'] ?? 'Unknown',
                ':battery_level' => $data['battery_level'] ?? 'Unknown'
            ]);
        }
        
        echo json_encode(['success' => true]);
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>

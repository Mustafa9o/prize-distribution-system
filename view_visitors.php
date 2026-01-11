<?php
// Database configuration
$host = 'localhost';
$dbname = 'prize_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get visitor details if ID is provided
    $visitor_detail = null;
    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("SELECT * FROM page_visits WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $visitor_detail = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get all page visits
    $sql = "SELECT id, ip_address, tracked_emp_id, tracked_name_english, tracked_name_arabic, country, city, browser, os, visit_time FROM page_visits ORDER BY visit_time DESC LIMIT 200";
    $stmt = $conn->query($sql);
    $visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $sql_stats = "SELECT 
                    COUNT(*) as total_visits,
                    COUNT(DISTINCT ip_address) as unique_visitors,
                    MAX(visit_time) as last_visit
                  FROM page_visits";
    $stmt_stats = $conn->query($sql_stats);
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل الزوار - Visitors Log</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-box h3 {
            font-size: 2em;
            margin-bottom: 5px;
        }
        
        .stat-box p {
            opacity: 0.9;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background: #667eea;
            color: white;
            padding: 15px;
            text-align: right;
            font-weight: bold;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: right;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .ip {
            font-family: monospace;
            color: #667eea;
            font-weight: bold;
        }
        
        .time {
            color: #666;
            font-size: 0.9em;
        }
        
        .user-agent {
            color: #999;
            font-size: 0.85em;
            max-width: 400px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .back-btn:hover {
            background: #5568d3;
        }
        
        .detail-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .detail-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: bold;
            color: #667eea;
        }
        
        .detail-value {
            color: #333;
            word-break: break-all;
        }
        
        .view-details-btn {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
        }
        
        .view-details-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← العودة للصفحة الرئيسية</a>
        
        <?php if ($visitor_detail): ?>
        <!-- Detailed View -->
        <h1>📋 تفاصيل الزائر - Visitor Details</h1>
        <a href="view_visitors.php" class="back-btn" style="margin-bottom: 20px; display: inline-block;">← العودة للقائمة</a>
        
        <?php if ($visitor_detail['tracked_name_english']): ?>
        <div class="detail-box" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; margin-bottom: 20px;">
            <h2 style="margin: 0 0 10px 0;">🎯 Tracked User Information</h2>
            <p style="margin: 5px 0; font-size: 1.2em;"><strong>Employee ID:</strong> <?php echo htmlspecialchars($visitor_detail['tracked_emp_id']); ?></p>
            <p style="margin: 5px 0; font-size: 1.2em;"><strong>Name (English):</strong> <?php echo htmlspecialchars($visitor_detail['tracked_name_english']); ?></p>
            <p style="margin: 5px 0; font-size: 1.2em;"><strong>Name (Arabic):</strong> <?php echo htmlspecialchars($visitor_detail['tracked_name_arabic']); ?></p>
            <p style="margin: 5px 0; font-size: 1.2em;"><strong>Email:</strong> <?php echo htmlspecialchars($visitor_detail['tracked_email']); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="detail-box">
            <div class="detail-row">
                <div class="detail-label">Date/Time:</div>
                <div class="detail-value"><?php echo date('Y-m-d H:i:s', strtotime($visitor_detail['visit_time'])); ?> UTC</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">IP Address:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['ip_address']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Country:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['country'] . ', ' . $visitor_detail['city']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Device Type:</div>
                <div class="detail-value"><strong><?php echo htmlspecialchars($visitor_detail['device_type']); ?></strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Device Model:</div>
                <div class="detail-value"><strong><?php echo htmlspecialchars($visitor_detail['device_model']); ?></strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Timezone:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['timezone']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Language:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['language']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Screen Size:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['screen_size']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Pixel Ratio:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['pixel_ratio']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Colour Scheme:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['color_scheme']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Connection Type:</div>
                <div class="detail-value"><strong><?php echo htmlspecialchars($visitor_detail['connection_type']); ?></strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Battery Level:</div>
                <div class="detail-value"><strong><?php echo htmlspecialchars($visitor_detail['battery_level']); ?></strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">GPU:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['gpu']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">RAM:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['ram']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">CPU Cores:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['cpu_cores']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Browser:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['browser']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Operating System:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['os']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Platform:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['platform']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Touch Screen:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['touch_screen']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Orientation:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['orientation']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">User Agent:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['user_agent']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Referring URL:</div>
                <div class="detail-value"><?php echo htmlspecialchars($visitor_detail['referrer']); ?></div>
            </div>
        </div>
        
        <?php else: ?>
        <!-- List View -->
        
        <h1>📊 سجل زوار الصفحة - Visitors Log</h1>
        
        <div class="stats">
            <div class="stat-box">
                <h3><?php echo number_format($stats['total_visits']); ?></h3>
                <p>إجمالي الزيارات<br>Total Visits</p>
            </div>
            <div class="stat-box">
                <h3><?php echo number_format($stats['unique_visitors']); ?></h3>
                <p>عدد الزوار الفريدين<br>Unique Visitors</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $stats['last_visit'] ? date('H:i', strtotime($stats['last_visit'])) : '-'; ?></h3>
                <p>آخر زيارة<br>Last Visit</p>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>اسم المستخدم - Username</th>
                    <th>عنوان IP - IP Address</th>
                    <th>الدولة - Country</th>
                    <th>المتصفح - Browser</th>
                    <th>وقت الزيارة - Visit Time</th>
                    <th>التفاصيل - Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($visits as $visit): ?>
                <tr>
                    <td style="font-weight: bold; color: #28a745;">
                        <?php echo $visit['tracked_name_english'] ? htmlspecialchars($visit['tracked_name_english']) . ' (' . htmlspecialchars($visit['tracked_emp_id']) . ')' : 'Unknown'; ?>
                    </td>
                    <td class="ip"><?php echo htmlspecialchars($visit['ip_address']); ?></td>
                    <td><?php echo htmlspecialchars($visit['country'] . ', ' . $visit['city']); ?></td>
                    <td><?php echo htmlspecialchars(substr($visit['browser'], 0, 30)) . '...'; ?></td>
                    <td class="time"><?php echo date('Y-m-d H:i:s', strtotime($visit['visit_time'])); ?></td>
                    <td><a href="view_visitors.php?id=<?php echo $visit['id']; ?>" class="view-details-btn">عرض التفاصيل</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>

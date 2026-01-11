<?php
// Database configuration
$host = 'localhost';
$dbname = 'prize_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all unique links
    $sql = "SELECT * FROM unique_links ORDER BY name_english ASC";
    $stmt = $conn->query($sql);
    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $base_url = "http://10.128.237.33/prize/visit.php?code=";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unique Links</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #667eea;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .copy-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .copy-btn:hover {
            background: #218838;
        }
        .url-box {
            font-family: monospace;
            font-size: 0.9em;
            color: #666;
        }
        .stats {
            color: #999;
            font-size: 0.9em;
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
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Main Page</a>
        <a href="view_visitors.php" class="back-btn" style="background: #28a745;">View Visitors Log</a>
        
        <h1>🔗 Unique Tracking Links</h1>
        <p>Total Links: <?php echo count($links); ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>Emp ID</th>
                    <th>Name (Arabic)</th>
                    <th>Name (English)</th>
                    <th>Unique URL</th>
                    <th>Clicks</th>
                    <th>Last Click</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($links as $link): ?>
                <tr>
                    <td><?php echo htmlspecialchars($link['emp_id']); ?></td>
                    <td><?php echo htmlspecialchars($link['name_arabic']); ?></td>
                    <td><?php echo htmlspecialchars($link['name_english']); ?></td>
                    <td class="url-box">
                        <span id="url-<?php echo $link['id']; ?>">
                            <?php echo $base_url . $link['unique_code']; ?>
                        </span>
                    </td>
                    <td class="stats"><?php echo $link['clicks']; ?></td>
                    <td class="stats"><?php echo $link['last_click'] ? date('Y-m-d H:i', strtotime($link['last_click'])) : 'Never'; ?></td>
                    <td>
                        <button class="copy-btn" onclick="copyURL('<?php echo $base_url . $link['unique_code']; ?>', <?php echo $link['id']; ?>)">
                            Copy URL
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script>
    function copyURL(url, id) {
        navigator.clipboard.writeText(url).then(function() {
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = '✓ Copied!';
            btn.style.background = '#28a745';
            setTimeout(function() {
                btn.textContent = originalText;
                btn.style.background = '';
            }, 2000);
        });
    }
    </script>
</body>
</html>

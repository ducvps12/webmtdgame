<?php
require_once __DIR__ . '/env.php';
$host = $_ENV_CONFIG['DB_HOST'];
$dbname = $_ENV_CONFIG['DB_NAME'];
$user = $_ENV_CONFIG['DB_USER'];
$pass = $_ENV_CONFIG['DB_PASS'];

$output = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW COLUMNS FROM account");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $colNames = [];
    foreach ($cols as $c) {
        $colNames[] = $c['Field'];
        $output .= $c['Field'] . " | " . $c['Type'] . " | Null:" . $c['Null'] . " | Default:" . ($c['Default'] ?? 'NULL') . "\n";
    }

    $insertCols = ['username', 'password', 'email', 'create_time', 'update_time', 'ban', 'is_admin',
        'last_time_login', 'last_time_logout', 'ip_address', 'active', 'thoi_vang',
        'server_login', 'bd_player', 'is_gift_box', 'gift_time', 'reward', 'vnd',
        'tongnap', 'token', 'xsrf_token', 'newpass', 'luotquay', 'vang', 'event_point',
        'vip', 'tichdiem', 'point_post', 'last_post', 'gioithieu', 'xacnhan_gioitheu',
        'baiviet', 'xacminh', 'admin'];

    $output .= "\n=== MISSING (in INSERT but NOT in table) ===\n";
    foreach ($insertCols as $col) {
        if (!in_array($col, $colNames)) {
            $output .= "MISSING: $col\n";
        }
    }

    $output .= "\n=== EXTRA (in table but NOT in INSERT) ===\n";
    foreach ($colNames as $col) {
        if (!in_array($col, $insertCols) && $col !== 'id') {
            $output .= "EXTRA: $col\n";
        }
    }
} catch (PDOException $e) {
    $output .= "ERROR: " . $e->getMessage() . "\n";
}

file_put_contents(__DIR__ . '/debug_output.txt', $output);
echo "Done! Check debug_output.txt\n";

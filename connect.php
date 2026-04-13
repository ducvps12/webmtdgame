<?php
// connect.php
require_once __DIR__ . '/env.php';

$ip_sv = $_ENV_CONFIG['DB_HOST'];
$dbname_sv = $_ENV_CONFIG['DB_NAME'];
$user_sv = $_ENV_CONFIG['DB_USER'];
$pass_sv = $_ENV_CONFIG['DB_PASS'];

$thesieure_url = $_ENV_CONFIG['TSR_URL'];
$thesieure_partner_id = $_ENV_CONFIG['TSR_PARTNER_ID'];
$thesieure_partner_key = $_ENV_CONFIG['TSR_PARTNER_KEY'];

$conn = new mysqli($ip_sv, $user_sv, $pass_sv, $dbname_sv);

if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
date_default_timezone_set('Asia/Ho_Chi_Minh');
?>
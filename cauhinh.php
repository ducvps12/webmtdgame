<?php
require_once __DIR__ . '/env.php';

$_domain = $_ENV_CONFIG['DOMAIN'];
$_IP = $_ENV_CONFIG['IP'];

// MySQL
$db_host = $_ENV_CONFIG['DB_HOST'];
$db_user = $_ENV_CONFIG['DB_USER'];
$db_pass = $_ENV_CONFIG['DB_PASS'];
$db_name = $_ENV_CONFIG['DB_NAME'];

// API
$w_cuphap_momo = $_ENV_CONFIG['MOMO_CUPHAP'];
$_qrmomo = $_ENV_CONFIG['MOMO_QR'];
$_phonemomo = $_ENV_CONFIG['MOMO_PHONE'];
$_momo = $_ENV_CONFIG['MOMO_BANK'];
$_nganhang = $_ENV_CONFIG['BANK_NAME'];
$_taikhoanmm = $_ENV_CONFIG['BANK_ACCOUNT'];
?>

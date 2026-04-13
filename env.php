<?php
// env.php - Đọc cấu hình từ file .env
$_ENV_CONFIG = [];
$envFile = __DIR__ . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));
        $_ENV_CONFIG[$key] = $value;
    }
} else {
    error_log("CRITICAL: .env file not found at: $envFile");
    die("Lỗi cấu hình hệ thống. Vui lòng liên hệ quản trị viên.");
}
?>

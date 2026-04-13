<?php
/**
 * Map Resource Proxy — serves game background images from data/res
 * Usage: /map-res?bg=b00  or  /map-res?bg=map0  or  /map-res?res=x3&bg=b00
 */

// Allowed resource prefixes (whitelist for security)
$allowed_prefixes = ['b', 'map', 'sun', 'cl', 'Big', 'lacay', 'tuyet', 'caycot', 'sao', 'mua', 'wtf', 'wts', 'uwt', 'twtf'];

$bg = isset($_GET['bg']) ? preg_replace('/[^a-zA-Z0-9\-_]/', '', $_GET['bg']) : '';
$res = isset($_GET['res']) ? preg_replace('/[^x0-9]/', '', $_GET['res']) : 'x3';

if (empty($bg)) {
    http_response_code(400);
    die('Missing bg parameter');
}

// Validate resolution tier
if (!in_array($res, ['x1', 'x2', 'x3', 'x4'])) {
    $res = 'x3';
}

// Security: only allow known prefixes
$valid = false;
foreach ($allowed_prefixes as $prefix) {
    if (strpos($bg, $prefix) === 0) {
        $valid = true;
        break;
    }
}
if (!$valid) {
    http_response_code(403);
    die('Invalid resource');
}

// Build path
$resPath = __DIR__ . '/../NROTFT(2)/data/res/' . $res . '/' . $bg;

// Try Desktop path if not found relative
if (!file_exists($resPath)) {
    $resPath = 'C:/Users/Administrator/Desktop/NROTFT(2)/data/res/' . $res . '/' . $bg;
}

if (!file_exists($resPath)) {
    http_response_code(404);
    die('Resource not found');
}

$data = file_get_contents($resPath);
if ($data === false) {
    http_response_code(500);
    die('Read error');
}

// Detect image type from magic bytes
$mime = 'application/octet-stream';
if (strlen($data) >= 4) {
    $header = substr($data, 0, 4);
    if ($header === "\x89PNG") {
        $mime = 'image/png';
    } elseif (substr($header, 0, 2) === "\xFF\xD8") {
        $mime = 'image/jpeg';
    } elseif ($header === "GIF8") {
        $mime = 'image/gif';
    } elseif ($header === "RIFF") {
        $mime = 'image/webp';
    }
}

// Cache for 7 days
header('Content-Type: ' . $mime);
header('Content-Length: ' . strlen($data));
header('Cache-Control: public, max-age=604800');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
header('Access-Control-Allow-Origin: *');
echo $data;
exit;

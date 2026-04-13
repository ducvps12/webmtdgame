<?php
// Public API to get active gift codes
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../connect.php';

$r = $conn->query("SELECT id, code, count_left, detail, datecreate, expired, type FROM giftcode WHERE count_left > 0 AND expired > NOW() ORDER BY datecreate DESC");
$codes = [];
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $codes[] = [
            'code' => $row['code'],
            'count_left' => (int)$row['count_left'],
            'expired' => $row['expired'],
            'type' => (int)$row['type'],
        ];
    }
}
echo json_encode(['status' => 'success', 'data' => $codes], JSON_UNESCAPED_UNICODE);

<?php
/**
 * Cron ATM Check — External endpoint for curl
 * URL: /api/cron-bank.php?key=<CRON_KEY>
 * Dùng crontab hoặc external service gọi mỗi 1 phút:
 *   curl -s "http://yourdomain.com/api/cron-bank.php?key=YOUR_CRON_KEY"
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../env.php';
require_once __DIR__ . '/../connect.php';

// Verify cron key
$cron_key = $_ENV_CONFIG['CRON_KEY'] ?? 'NROTFT_CRON_2024_SECRET';
$provided_key = $_GET['key'] ?? '';

if (empty($provided_key) || $provided_key !== $cron_key) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid cron key'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Fetch ACB API
$acb_token = $_ENV_CONFIG['ACB_TOKEN'] ?? '';
$acb_api_url = $_ENV_CONFIG['ACB_API_URL'] ?? '';
$min_deposit = intval($_ENV_CONFIG['MIN_DEPOSIT'] ?? 10000);

if (empty($acb_token) || empty($acb_api_url)) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa cấu hình ACB API'], JSON_UNESCAPED_UNICODE);
    exit;
}

$api_url = $acb_api_url . '/' . $acb_token;
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $http_code !== 200) {
    echo json_encode(['status' => 'error', 'message' => 'API error', 'http_code' => $http_code], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode($response, true);
if (!$data || !isset($data['data'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid API response'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Load ALL usernames into memory for matching
$all_users = [];
$user_stmt = $conn->prepare("SELECT id, username FROM account");
$user_stmt->execute();
$user_result = $user_stmt->get_result();
while ($row = $user_result->fetch_assoc()) {
    $all_users[] = $row;
}
$user_stmt->close();

// Sort by username length DESC so longer names match first
usort($all_users, function($a, $b) {
    return strlen($b['username']) - strlen($a['username']);
});

/**
 * Normalize Vietnamese text for matching
 * Removes diacritics, lowercase, remove spaces for comparison
 */
function normalizeVN($str) {
    $str = mb_strtolower($str, 'UTF-8');
    // Remove common Vietnamese diacritics
    $map = [
        'á'=>'a','à'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a',
        'ă'=>'a','ắ'=>'a','ằ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
        'â'=>'a','ấ'=>'a','ầ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
        'đ'=>'d',
        'é'=>'e','è'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e',
        'ê'=>'e','ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
        'í'=>'i','ì'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
        'ó'=>'o','ò'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o',
        'ô'=>'o','ố'=>'o','ồ'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
        'ơ'=>'o','ớ'=>'o','ờ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
        'ú'=>'u','ù'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u',
        'ư'=>'u','ứ'=>'u','ừ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
        'ý'=>'y','ỳ'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y'
    ];
    $str = strtr($str, $map);
    return $str;
}

function stripAllSpaces($str) {
    return preg_replace('/\s+/', '', $str);
}

$transactions = $data['data'];
$processed = 0;
$matched = 0;
$skipped = 0;
$details = [];

foreach ($transactions as $tx) {
    if (($tx['type'] ?? '') !== 'IN') continue;

    $tx_number = $tx['transactionNumber'] ?? null;
    $amount = intval($tx['amount'] ?? 0);
    $description = $tx['description'] ?? '';
    $sender_name = $tx['senderName'] ?? '';

    if (!$tx_number || $amount < $min_deposit) continue;

    // Check if already processed
    $check = $conn->prepare("SELECT id FROM bank_transactions WHERE transaction_number = ?");
    $check->bind_param("i", $tx_number);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $check->close();
        $skipped++;
        continue;
    }
    $check->close();

    // === SMART USERNAME MATCHING ===
    // Strategy: Normalize the description, then try to find any known username in it
    $matched_username = null;
    $matched_account_id = null;
    $status = 'ignored';

    $desc_normalized = normalizeVN($description);
    $desc_nospace = stripAllSpaces($desc_normalized);

    foreach ($all_users as $user) {
        $uname = $user['username'];
        $uname_normalized = normalizeVN($uname);
        $uname_nospace = stripAllSpaces($uname_normalized);

        if (empty($uname_nospace) || strlen($uname_nospace) < 2) continue;

        // Match 1: exact username (no spaces) found in description (no spaces)
        if (strpos($desc_nospace, $uname_nospace) !== false) {
            $matched_username = $uname;
            $matched_account_id = $user['id'];
            break;
        }

        // Match 2: exact username (lowercase) found in description (lowercase)
        $desc_lower = mb_strtolower($description, 'UTF-8');
        $uname_lower = mb_strtolower($uname, 'UTF-8');
        if (strpos($desc_lower, $uname_lower) !== false) {
            $matched_username = $uname;
            $matched_account_id = $user['id'];
            break;
        }
    }

    if ($matched_username && $matched_account_id) {
        $conn->begin_transaction();
        try {
            $upd = $conn->prepare("UPDATE account SET vnd = vnd + ?, tongnap = tongnap + ? WHERE id = ?");
            $upd->bind_param("iii", $amount, $amount, $matched_account_id);
            $upd->execute();
            $upd->close();
            $status = 'success';
            $matched++;
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            $status = 'failed';
        }
    }

    // Save transaction
    $ins = $conn->prepare("INSERT INTO bank_transactions (transaction_number, amount, description, sender_name, type, posting_date, matched_username, matched_account_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $type = $tx['type'];
    $posting_date = $tx['postingDate'] ?? 0;
    $ins->bind_param("iisssisss", $tx_number, $amount, $description, $sender_name, $type, $posting_date, $matched_username, $matched_account_id, $status);
    $ins->execute();
    $ins->close();

    $processed++;
    $details[] = ['tx' => $tx_number, 'amount' => $amount, 'user' => $matched_username, 'status' => $status];
}

echo json_encode([
    'status' => 'success',
    'time' => date('Y-m-d H:i:s'),
    'processed' => $processed,
    'matched' => $matched,
    'skipped' => $skipped,
    'details' => $details
], JSON_UNESCAPED_UNICODE);

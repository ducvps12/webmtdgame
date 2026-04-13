<?php
// api/atm_check.php - Auto ATM Checker
// Gọi ACB API, kiểm tra giao dịch mới, match username, cộng VND

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../env.php';
require_once __DIR__ . '/../connect.php';

// Chỉ cho admin gọi (hoặc cron)
$is_cron = (php_sapi_name() === 'cli');
if (!$is_cron) {
    if (!isset($_SESSION['username'])) {
        echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
        exit;
    }
    // Kiểm tra admin
    $stmt = $conn->prepare("SELECT admin FROM account WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    if (!$user || $user['admin'] != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Không có quyền truy cập']);
        exit;
    }
}

$acb_token = $_ENV_CONFIG['ACB_TOKEN'] ?? '';
$acb_api_url = $_ENV_CONFIG['ACB_API_URL'] ?? '';
$atm_prefix = strtoupper(trim($_ENV_CONFIG['ATM_PREFIX'] ?? 'NB'));

if (empty($acb_token) || empty($acb_api_url)) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa cấu hình ACB API']);
    exit;
}

// Gọi API
$api_url = $acb_api_url . '/' . $acb_token;
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response === false || $http_code !== 200) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi gọi API: ' . $curl_error, 'http_code' => $http_code]);
    exit;
}

$data = json_decode($response, true);
if (!$data || !isset($data['data'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu API không hợp lệ']);
    exit;
}

$transactions = $data['data'];
$processed = 0;
$matched = 0;
$skipped = 0;
$errors = [];

foreach ($transactions as $tx) {
    // Chỉ xử lý giao dịch nhận tiền (IN)
    if (($tx['type'] ?? '') !== 'IN') continue;

    $tx_number = $tx['transactionNumber'] ?? null;
    $amount = intval($tx['amount'] ?? 0);
    $description = $tx['description'] ?? '';
    $sender_name = $tx['senderName'] ?? '';
    $posting_date = $tx['postingDate'] ?? 0;

    if (!$tx_number || $amount <= 0) continue;

    // Kiểm tra đã xử lý chưa
    $check_stmt = $conn->prepare("SELECT id FROM bank_transactions WHERE transaction_number = ?");
    $check_stmt->bind_param("i", $tx_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        $skipped++;
        continue;
    }
    $check_stmt->close();

    // === SMART USERNAME MATCHING ===
    // Load users on first iteration
    static $all_users = null;
    if ($all_users === null) {
        $all_users = [];
        $u_stmt = $conn->prepare("SELECT id, username FROM account");
        $u_stmt->execute();
        $u_res = $u_stmt->get_result();
        while ($r = $u_res->fetch_assoc()) $all_users[] = $r;
        $u_stmt->close();
        // Sort by name length DESC so longer matches first
        usort($all_users, function($a, $b) { return strlen($b['username']) - strlen($a['username']); });
    }

    $matched_username = null;
    $matched_account_id = null;
    $status = 'ignored';

    // Normalize Vietnamese: remove diacritics + lowercase
    $vn_map = ['á'=>'a','à'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ắ'=>'a','ằ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a','â'=>'a','ấ'=>'a','ầ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a','đ'=>'d','é'=>'e','è'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e','í'=>'i','ì'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i','ó'=>'o','ò'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ố'=>'o','ồ'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o','ơ'=>'o','ớ'=>'o','ờ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o','ú'=>'u','ù'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ứ'=>'u','ừ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u','ý'=>'y','ỳ'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y'];
    $desc_norm = preg_replace('/\s+/', '', strtr(mb_strtolower($description, 'UTF-8'), $vn_map));

    foreach ($all_users as $u) {
        $uname_norm = preg_replace('/\s+/', '', strtr(mb_strtolower($u['username'], 'UTF-8'), $vn_map));
        if (empty($uname_norm) || strlen($uname_norm) < 2) continue;
        if (strpos($desc_norm, $uname_norm) !== false) {
            $matched_username = $u['username'];
            $matched_account_id = $u['id'];
            break;
        }
    }

    if ($matched_username && $matched_account_id) {

            // Cộng VND cho user
            $conn->begin_transaction();
            try {
                $update_stmt = $conn->prepare("UPDATE account SET vnd = vnd + ?, tongnap = tongnap + ? WHERE id = ?");
                $update_stmt->bind_param("iii", $amount, $amount, $matched_account_id);
                $update_stmt->execute();
                $update_stmt->close();

                $status = 'success';
                $matched++;
                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
                $status = 'failed';
                $errors[] = "Lỗi cộng VND cho {$matched_username}: " . $e->getMessage();
        }
    }

    // Lưu giao dịch vào DB
    $insert_stmt = $conn->prepare("INSERT INTO bank_transactions (transaction_number, amount, description, sender_name, type, posting_date, matched_username, matched_account_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $type = $tx['type'];
    $insert_stmt->bind_param("iisssisss", $tx_number, $amount, $description, $sender_name, $type, $posting_date, $matched_username, $matched_account_id, $status);
    $insert_stmt->execute();
    $insert_stmt->close();

    $processed++;
}

echo json_encode([
    'status' => 'success',
    'message' => "Đã xử lý {$processed} giao dịch mới, {$matched} khớp, {$skipped} đã có.",
    'processed' => $processed,
    'matched' => $matched,
    'skipped' => $skipped,
    'errors' => $errors
], JSON_UNESCAPED_UNICODE);

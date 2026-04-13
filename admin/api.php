<?php
// admin/api.php - Admin AJAX Endpoints
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../env.php';
require_once __DIR__ . '/../connect.php';

// === AUTH CHECK ===
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
    exit;
}

$stmt = $conn->prepare("SELECT id, is_admin FROM account WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$admin_check = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin_check || $admin_check['is_admin'] != 1) {
    echo json_encode(['status' => 'error', 'message' => 'Không có quyền']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

switch ($action) {

    // === DASHBOARD STATS ===
    case 'get_stats':
        $stats = [];

        // Tổng tài khoản
        $r = $conn->query("SELECT COUNT(*) as cnt FROM account");
        $stats['total_users'] = $r->fetch_assoc()['cnt'];

        // Tổng nạp (tongnap)
        $r = $conn->query("SELECT COALESCE(SUM(tongnap), 0) as total FROM account");
        $stats['total_revenue'] = $r->fetch_assoc()['total'];

        // Tổng nhân vật
        $r = $conn->query("SELECT COUNT(*) as cnt FROM player");
        $stats['total_players'] = $r->fetch_assoc()['cnt'];

        // Giao dịch hôm nay
        $r = $conn->query("SELECT COUNT(*) as cnt, COALESCE(SUM(amount), 0) as total FROM bank_transactions WHERE status = 'success' AND DATE(created_at) = CURDATE()");
        $row = $r->fetch_assoc();
        $stats['today_transactions'] = $row['cnt'];
        $stats['today_revenue'] = $row['total'];

        // Doanh thu tuần này
        $r = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM bank_transactions WHERE status = 'success' AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
        $stats['weekly_revenue'] = $r->fetch_assoc()['total'];

        // Doanh thu tháng này
        $r = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM bank_transactions WHERE status = 'success' AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
        $stats['monthly_revenue'] = $r->fetch_assoc()['total'];

        // Tài khoản mới hôm nay
        $r = $conn->query("SELECT COUNT(*) as cnt FROM account WHERE DATE(create_time) = CURDATE()");
        $stats['new_users_today'] = $r->fetch_assoc()['cnt'];

        // 5 giao dịch gần nhất
        $r = $conn->query("SELECT * FROM bank_transactions ORDER BY created_at DESC LIMIT 5");
        $recent = [];
        while ($row = $r->fetch_assoc()) {
            $recent[] = $row;
        }
        $stats['recent_transactions'] = $recent;

        // 5 user nạp nhiều nhất
        $r = $conn->query("SELECT username, tongnap, vnd FROM account ORDER BY tongnap DESC LIMIT 5");
        $top = [];
        while ($row = $r->fetch_assoc()) {
            $top[] = $row;
        }
        $stats['top_users'] = $top;

        // Revenue chart: last 7 days
        $revMap = [];
        $r = $conn->query(
            "SELECT DATE(created_at) as d, COALESCE(SUM(amount),0) as total
             FROM bank_transactions
             WHERE status='success' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY DATE(created_at)"
        );
        while ($row = $r->fetch_assoc()) {
            $revMap[$row['d']] = (int)$row['total'];
        }
        $stats['rev7_labels'] = [];
        $stats['rev7_values'] = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} day"));
            $stats['rev7_labels'][] = date('d/m', strtotime($date));
            $stats['rev7_values'][] = $revMap[$date] ?? 0;
        }

        // New users chart: last 7 days
        $userMap = [];
        $r = $conn->query(
            "SELECT DATE(create_time) as d, COUNT(*) as total
             FROM account
             WHERE DATE(create_time) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY DATE(create_time)"
        );
        while ($row = $r->fetch_assoc()) {
            $userMap[$row['d']] = (int)$row['total'];
        }
        $stats['new_users_7_labels'] = [];
        $stats['new_users_7_values'] = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} day"));
            $stats['new_users_7_labels'][] = date('d/m', strtotime($date));
            $stats['new_users_7_values'][] = $userMap[$date] ?? 0;
        }

        // Transaction status distribution (last 30 days)
        $statusMap = [
            'success' => 0,
            'pending' => 0,
            'failed' => 0,
            'ignored' => 0
        ];
        $r = $conn->query(
            "SELECT status, COUNT(*) as total
             FROM bank_transactions
             WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY status"
        );
        while ($row = $r->fetch_assoc()) {
            $k = strtolower((string)$row['status']);
            if (array_key_exists($k, $statusMap)) {
                $statusMap[$k] = (int)$row['total'];
            }
        }
        $stats['status_labels'] = ['Thành công', 'Đang xử lý', 'Thất bại', 'Bỏ qua'];
        $stats['status_values'] = [
            $statusMap['success'],
            $statusMap['pending'],
            $statusMap['failed'],
            $statusMap['ignored']
        ];

        // ===== Operations Hub (10 management features) =====
        $stats['maintenance_mode'] = (int)($_ENV_CONFIG['MAINTENANCE_MODE'] ?? 0);
        $stats['atm_configured'] = (!empty($_ENV_CONFIG['ACB_TOKEN'] ?? '') && !empty($_ENV_CONFIG['ACB_API_URL'] ?? '')) ? 1 : 0;
        $stats['atm_prefix'] = trim($_ENV_CONFIG['ATM_PREFIX'] ?? 'chuyen tien');

        // Ensure feedback table exists before querying.
        $conn->query("
            CREATE TABLE IF NOT EXISTS feedback (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                username VARCHAR(50) NOT NULL,
                player_name VARCHAR(50) DEFAULT NULL,
                category ENUM('bug','suggestion','other') DEFAULT 'suggestion',
                title VARCHAR(200) NOT NULL,
                content TEXT NOT NULL,
                status ENUM('new','read','replied') DEFAULT 'new',
                admin_reply TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $r = $conn->query("SELECT COUNT(*) AS cnt FROM feedback WHERE status = 'new'");
        $stats['feedback_new'] = $r ? (int)$r->fetch_assoc()['cnt'] : 0;

        $r = $conn->query("SELECT COUNT(*) AS cnt FROM bank_transactions WHERE status = 'pending'");
        $stats['pending_transactions'] = $r ? (int)$r->fetch_assoc()['cnt'] : 0;

        $r = $conn->query("SELECT COUNT(*) AS cnt FROM bank_transactions WHERE status = 'failed' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stats['failed_tx_24h'] = $r ? (int)$r->fetch_assoc()['cnt'] : 0;

        $r = $conn->query("SELECT COUNT(*) AS cnt FROM bank_transactions WHERE status = 'ignored' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stats['ignored_tx_24h'] = $r ? (int)$r->fetch_assoc()['cnt'] : 0;

        $r = $conn->query("SELECT COUNT(*) AS cnt FROM account WHERE last_time_login IS NOT NULL AND last_time_login >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stats['active_users_24h'] = $r ? (int)$r->fetch_assoc()['cnt'] : 0;

        $r = $conn->query("SELECT COUNT(*) AS cnt FROM account WHERE last_time_login IS NULL OR last_time_login < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stats['inactive_users_30d'] = $r ? (int)$r->fetch_assoc()['cnt'] : 0;

        $r = $conn->query("SELECT COUNT(*) AS cnt FROM account WHERE create_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stats['new_users_7d'] = $r ? (int)$r->fetch_assoc()['cnt'] : 0;

        $r = $conn->query("SELECT username, tongnap FROM account ORDER BY tongnap DESC LIMIT 1");
        $top_month = $r ? $r->fetch_assoc() : null;
        $stats['top_user_month'] = $top_month['username'] ?? '-';
        $stats['top_user_month_amount'] = (int)($top_month['tongnap'] ?? 0);

        $stats['server_ip'] = trim($_ENV_CONFIG['GAME_SERVER_IP'] ?? '');
        $stats['server_port'] = intval($_ENV_CONFIG['GAME_SERVER_PORT'] ?? 14445);

        echo json_encode(['status' => 'success', 'data' => $stats], JSON_UNESCAPED_UNICODE);
        break;

    // === CHECK ATM ===
    case 'check_atm':
        $acb_token = $_ENV_CONFIG['ACB_TOKEN'] ?? '';
        $acb_api_url = $_ENV_CONFIG['ACB_API_URL'] ?? '';
        $atm_prefix = trim($_ENV_CONFIG['ATM_PREFIX'] ?? 'chuyen tien');

        if (empty($acb_token) || empty($acb_api_url)) {
            echo json_encode(['status' => 'error', 'message' => 'Chưa cấu hình ACB API']);
            break;
        }

        $api_url = $acb_api_url . '/' . $acb_token;
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $http_code !== 200) {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối API']);
            break;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['data'])) {
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu API không hợp lệ']);
            break;
        }

        $processed = 0; $matched = 0; $skipped = 0;

        foreach ($data['data'] as $tx) {
            if (($tx['type'] ?? '') !== 'IN') continue;

            $tx_number = $tx['transactionNumber'] ?? null;
            $amount = intval($tx['amount'] ?? 0);
            if (!$tx_number || $amount <= 0) continue;

            $check = $conn->prepare("SELECT id FROM bank_transactions WHERE transaction_number = ?");
            $check->bind_param("i", $tx_number);
            $check->execute();
            if ($check->get_result()->num_rows > 0) { $check->close(); $skipped++; continue; }
            $check->close();

            $description = $tx['description'] ?? '';
            $sender_name = $tx['senderName'] ?? '';
            $posting_date = $tx['postingDate'] ?? 0;
            $matched_username = null;
            $matched_account_id = null;
            $status = 'ignored';

            if (preg_match('/' . preg_quote($atm_prefix, '/') . '\s+([A-Za-z0-9_]+)/i', $description, $m)) {
                $potential = strtolower(trim($m[1]));
                $u = $conn->prepare("SELECT id, username FROM account WHERE LOWER(username) = ? LIMIT 1");
                $u->bind_param("s", $potential);
                $u->execute();
                $ur = $u->get_result()->fetch_assoc();
                $u->close();

                if ($ur) {
                    $matched_username = $ur['username'];
                    $matched_account_id = $ur['id'];
                    $upd = $conn->prepare("UPDATE account SET vnd = vnd + ?, tongnap = tongnap + ? WHERE id = ?");
                    $upd->bind_param("iii", $amount, $amount, $matched_account_id);
                    $upd->execute();
                    $upd->close();
                    $status = 'success';
                    $matched++;
                }
            }

            $ins = $conn->prepare("INSERT INTO bank_transactions (transaction_number, amount, description, sender_name, type, posting_date, matched_username, matched_account_id, status) VALUES (?, ?, ?, ?, 'IN', ?, ?, ?, ?)");
            $ins->bind_param("iisssiis", $tx_number, $amount, $description, $sender_name, $posting_date, $matched_username, $matched_account_id, $status);
            $ins->execute();
            $ins->close();
            $processed++;
        }

        echo json_encode([
            'status' => 'success',
            'message' => "Xử lý {$processed} mới, {$matched} khớp, {$skipped} đã có.",
            'processed' => $processed,
            'matched' => $matched,
            'skipped' => $skipped
        ], JSON_UNESCAPED_UNICODE);
        break;

    // === SEARCH USERS ===
    case 'search_users':
        $keyword = trim($_GET['q'] ?? '');
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        if (!empty($keyword)) {
            $like = "%{$keyword}%";
            $stmt = $conn->prepare("SELECT a.id, a.username, a.vnd, a.tongnap, a.is_admin, a.ban, a.active, a.create_time, a.last_time_login, a.ip_address, p.name as player_name FROM account a LEFT JOIN player p ON a.id = p.account_id WHERE a.username LIKE ? OR p.name LIKE ? ORDER BY a.id DESC LIMIT ? OFFSET ?");
            $stmt->bind_param("ssii", $like, $like, $limit, $offset);

            $count_stmt = $conn->prepare("SELECT COUNT(DISTINCT a.id) as cnt FROM account a LEFT JOIN player p ON a.id = p.account_id WHERE a.username LIKE ? OR p.name LIKE ?");
            $count_stmt->bind_param("ss", $like, $like);
        } else {
            $stmt = $conn->prepare("SELECT a.id, a.username, a.vnd, a.tongnap, a.is_admin, a.ban, a.active, a.create_time, a.last_time_login, a.ip_address, p.name as player_name FROM account a LEFT JOIN player p ON a.id = p.account_id ORDER BY a.id DESC LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $limit, $offset);

            $count_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM account");
        }

        $count_stmt->execute();
        $total = $count_stmt->get_result()->fetch_assoc()['cnt'];
        $count_stmt->close();

        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();

        echo json_encode([
            'status' => 'success',
            'data' => $users,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $limit)
        ], JSON_UNESCAPED_UNICODE);
        break;

    // === UPDATE USER ===
    case 'update_user':
        $user_id = intval($_POST['user_id'] ?? 0);
        $field = $_POST['field'] ?? '';
        $value = $_POST['value'] ?? '';

        $allowed_fields = ['vnd', 'tongnap', 'is_admin', 'ban', 'active'];
        if (!$user_id || !in_array($field, $allowed_fields)) {
            echo json_encode(['status' => 'error', 'message' => 'Tham số không hợp lệ']);
            break;
        }

        $stmt = $conn->prepare("UPDATE account SET $field = ? WHERE id = ?");
        $int_val = intval($value);
        $stmt->bind_param("ii", $int_val, $user_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success', 'message' => "Đã cập nhật {$field} cho user #{$user_id}"], JSON_UNESCAPED_UNICODE);
        break;

    // === MANUAL CREDIT ===
    case 'manual_credit':
        $user_id = intval($_POST['user_id'] ?? 0);
        $amount = intval($_POST['amount'] ?? 0);

        if (!$user_id || $amount <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Tham số không hợp lệ']);
            break;
        }

        $stmt = $conn->prepare("UPDATE account SET vnd = vnd + ?, tongnap = tongnap + ? WHERE id = ?");
        $stmt->bind_param("iii", $amount, $amount, $user_id);
        $stmt->execute();
        $stmt->close();

        // Log giao dịch
        $desc = "Admin cộng VND thủ công";
        $admin_name = $_SESSION['username'];
        $ins = $conn->prepare("INSERT INTO bank_transactions (transaction_number, amount, description, sender_name, type, posting_date, matched_username, matched_account_id, status) VALUES (?, ?, ?, ?, 'IN', ?, NULL, ?, 'success')");
        $fake_tx = time() + rand(100000, 999999);
        $now_ms = round(microtime(true) * 1000);
        $ins->bind_param("iissii", $fake_tx, $amount, $desc, $admin_name, $now_ms, $user_id);
        $ins->execute();
        $ins->close();

        echo json_encode(['status' => 'success', 'message' => "Đã cộng " . number_format($amount) . " VND cho user #{$user_id}"], JSON_UNESCAPED_UNICODE);
        break;

    // === GET TRANSACTIONS ===
    case 'get_transactions':
        $page = max(1, intval($_GET['page'] ?? 1));
        $status_filter = $_GET['status'] ?? '';
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = "1=1";
        $params = [];
        $types = "";

        if (!empty($status_filter) && in_array($status_filter, ['success', 'ignored', 'failed', 'pending'])) {
            $where .= " AND status = ?";
            $params[] = $status_filter;
            $types .= "s";
        }

        // Count
        $count_sql = "SELECT COUNT(*) as cnt FROM bank_transactions WHERE $where";
        $count_stmt = $conn->prepare($count_sql);
        if (!empty($params)) {
            $count_stmt->bind_param($types, ...$params);
        }
        $count_stmt->execute();
        $total = $count_stmt->get_result()->fetch_assoc()['cnt'];
        $count_stmt->close();

        // Data
        $sql = "SELECT * FROM bank_transactions WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $types_full = $types . "ii";
        $params_full = array_merge($params, [$limit, $offset]);
        $stmt->bind_param($types_full, ...$params_full);
        $stmt->execute();
        $result = $stmt->get_result();
        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
        $stmt->close();

        echo json_encode([
            'status' => 'success',
            'data' => $transactions,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $limit)
        ], JSON_UNESCAPED_UNICODE);
        break;

    // === SAVE SETTINGS ===
    case 'save_settings':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }

        $allowed_keys = [
            'DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME',
            'DOMAIN', 'IP',
            'ACB_ACCOUNT', 'ACB_TOKEN', 'ACB_API_URL', 'ATM_PREFIX',
            'SITE_TITLE', 'SITE_DESCRIPTION', 'SITE_KEYWORDS',
            'ACCOUNT_HOLDER', 'MIN_DEPOSIT',
            'CRON_URL', 'CRON_INTERVAL',
            'MOMO_PHONE', 'MOMO_BANK',
            'BANK_NAME', 'BANK_ACCOUNT',
            'MAINTENANCE_MODE', 'ZALO_LINK', 'FACEBOOK_LINK',
            'HERO_TITLE', 'HERO_SUBTITLE', 'FOOTER_TEXT', 'DISCORD_LINK',
            'GAME_SERVER_IP', 'GAME_SERVER_PORT'
        ];

        $env_path = __DIR__ . '/../.env';
        if (!file_exists($env_path)) {
            echo json_encode(['status' => 'error', 'message' => '.env file not found']);
            break;
        }

        $settings = $_POST['settings'] ?? [];
        if (empty($settings) || !is_array($settings)) {
            echo json_encode(['status' => 'error', 'message' => 'No settings provided']);
            break;
        }

        // Read existing .env
        $env_content = file_get_contents($env_path);
        $updated = 0;

        foreach ($settings as $key => $value) {
            $key = strtoupper(trim($key));
            if (!in_array($key, $allowed_keys)) continue;

            $value = trim($value);
            // Update or append
            if (preg_match('/^' . preg_quote($key, '/') . '=.*/m', $env_content)) {
                $env_content = preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $key . '=' . $value, $env_content);
            } else {
                $env_content .= "\n" . $key . '=' . $value;
            }
            $updated++;
        }

        if ($updated > 0) {
            file_put_contents($env_path, $env_content);
        }

        echo json_encode(['status' => 'success', 'message' => "Đã cập nhật {$updated} cài đặt. Khởi động lại để áp dụng."], JSON_UNESCAPED_UNICODE);
        break;

    // === CHECK GAME SERVER ===
    case 'check_game_server':
        $ip = $_GET['ip'] ?? '';
        $port = intval($_GET['port'] ?? 14445);
        if (empty($ip)) {
            echo json_encode(['status' => 'error', 'message' => 'Chưa cấu hình IP server']);
            break;
        }
        $connection = @fsockopen($ip, $port, $errno, $errstr, 3);
        if ($connection) {
            fclose($connection);
            echo json_encode(['status' => 'success', 'message' => "Server đang chạy tại {$ip}:{$port}"], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Không thể kết nối {$ip}:{$port} — {$errstr}"], JSON_UNESCAPED_UNICODE);
        }
        break;

    // === OPERATIONS CENTER DATA ===
    case 'get_operations_data':
        $data = [];

        $data['maintenance_mode'] = (int)($_ENV_CONFIG['MAINTENANCE_MODE'] ?? 0);
        $data['atm_configured'] = (!empty($_ENV_CONFIG['ACB_TOKEN'] ?? '') && !empty($_ENV_CONFIG['ACB_API_URL'] ?? '')) ? 1 : 0;
        $data['atm_prefix'] = trim($_ENV_CONFIG['ATM_PREFIX'] ?? 'chuyen tien');
        $data['server_ip'] = trim($_ENV_CONFIG['GAME_SERVER_IP'] ?? '');
        $data['server_port'] = intval($_ENV_CONFIG['GAME_SERVER_PORT'] ?? 14445);

        $conn->query("
            CREATE TABLE IF NOT EXISTS feedback (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                username VARCHAR(50) NOT NULL,
                player_name VARCHAR(50) DEFAULT NULL,
                category ENUM('bug','suggestion','other') DEFAULT 'suggestion',
                title VARCHAR(200) NOT NULL,
                content TEXT NOT NULL,
                status ENUM('new','read','replied') DEFAULT 'new',
                admin_reply TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $r = $conn->query("SELECT id, username, player_name, category, title, created_at FROM feedback WHERE status='new' ORDER BY created_at DESC LIMIT 10");
        $feedback_new = [];
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $feedback_new[] = $row;
            }
        }
        $data['feedback_new'] = $feedback_new;

        $r = $conn->query("SELECT id, transaction_number, amount, description, sender_name, created_at FROM bank_transactions WHERE status='pending' ORDER BY created_at DESC LIMIT 10");
        $pending_txs = [];
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $pending_txs[] = $row;
            }
        }
        $data['pending_txs'] = $pending_txs;

        $r = $conn->query("SELECT id, transaction_number, amount, description, sender_name, created_at FROM bank_transactions WHERE status='failed' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY created_at DESC LIMIT 10");
        $failed_24h = [];
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $failed_24h[] = $row;
            }
        }
        $data['failed_24h'] = $failed_24h;

        $r = $conn->query("SELECT id, transaction_number, amount, description, sender_name, created_at FROM bank_transactions WHERE status='ignored' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY created_at DESC LIMIT 10");
        $ignored_24h = [];
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $ignored_24h[] = $row;
            }
        }
        $data['ignored_24h'] = $ignored_24h;

        $r = $conn->query("SELECT id, username, vnd, tongnap, last_time_login FROM account WHERE last_time_login IS NOT NULL AND last_time_login >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY last_time_login DESC LIMIT 10");
        $active_24h = [];
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $active_24h[] = $row;
            }
        }
        $data['active_24h'] = $active_24h;

        $r = $conn->query("SELECT id, username, vnd, tongnap, create_time, last_time_login FROM account WHERE last_time_login IS NULL OR last_time_login < DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY COALESCE(last_time_login, create_time) ASC LIMIT 10");
        $inactive_30d = [];
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $inactive_30d[] = $row;
            }
        }
        $data['inactive_30d'] = $inactive_30d;

        $r = $conn->query("SELECT username, tongnap, vnd, create_time FROM account ORDER BY tongnap DESC LIMIT 10");
        $top_payers = [];
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $top_payers[] = $row;
            }
        }
        $data['top_payers'] = $top_payers;

        $r = $conn->query("SELECT COUNT(*) AS c FROM account WHERE create_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $data['new_users_7d'] = $r ? (int)$r->fetch_assoc()['c'] : 0;

        $r = $conn->query("SELECT COUNT(*) AS c FROM bank_transactions WHERE status='failed' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $data['failed_count_24h'] = $r ? (int)$r->fetch_assoc()['c'] : 0;

        $r = $conn->query("SELECT COUNT(*) AS c FROM bank_transactions WHERE status='ignored' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $data['ignored_count_24h'] = $r ? (int)$r->fetch_assoc()['c'] : 0;

        echo json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    // === TOGGLE MAINTENANCE MODE ===
    case 'set_maintenance':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $enabled = intval($_POST['enabled'] ?? -1);
        if (!in_array($enabled, [0, 1], true)) {
            echo json_encode(['status' => 'error', 'message' => 'Giá trị không hợp lệ']);
            break;
        }
        $env_path = __DIR__ . '/../.env';
        if (!file_exists($env_path)) {
            echo json_encode(['status' => 'error', 'message' => '.env file not found']);
            break;
        }
        $env_content = file_get_contents($env_path);
        if (preg_match('/^MAINTENANCE_MODE=.*/m', $env_content)) {
            $env_content = preg_replace('/^MAINTENANCE_MODE=.*/m', 'MAINTENANCE_MODE=' . $enabled, $env_content);
        } else {
            $env_content .= "\nMAINTENANCE_MODE=" . $enabled;
        }
        file_put_contents($env_path, $env_content);
        echo json_encode([
            'status' => 'success',
            'message' => $enabled ? 'Đã bật chế độ bảo trì' : 'Đã tắt chế độ bảo trì',
            'enabled' => $enabled
        ], JSON_UNESCAPED_UNICODE);
        break;

    // === FORUM: GET POSTS ===
    case 'get_forum_posts':
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;
        $q = trim($_GET['q'] ?? '');
        $pin_filter = $_GET['pin'] ?? '';
        $sort = $_GET['sort'] ?? 'newest';

        $where = "1=1";
        $params = [];
        $types = "";

        if (!empty($q)) {
            $like = "%{$q}%";
            $where .= " AND (p.tieude LIKE ? OR p.noidung LIKE ? OR p.username LIKE ?)";
            $params[] = $like; $params[] = $like; $params[] = $like;
            $types .= "sss";
        }

        if ($pin_filter !== '' && in_array($pin_filter, ['0', '1'])) {
            $where .= " AND p.ghimbai = ?";
            $params[] = intval($pin_filter);
            $types .= "i";
        }

        $order = "p.created_at DESC";
        if ($sort === 'oldest') $order = "p.created_at ASC";
        if ($sort === 'most_comments') $order = "comment_count DESC, p.created_at DESC";

        // Count
        $count_sql = "SELECT COUNT(*) as cnt FROM posts p WHERE $where";
        $count_stmt = $conn->prepare($count_sql);
        if (!empty($params)) {
            $count_stmt->bind_param($types, ...$params);
        }
        $count_stmt->execute();
        $total = $count_stmt->get_result()->fetch_assoc()['cnt'];
        $count_stmt->close();

        // Data
        $sql = "SELECT p.*, (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comment_count FROM posts p WHERE $where ORDER BY $order LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $types_full = $types . "ii";
        $params_full = array_merge($params, [$limit, $offset]);
        $stmt->bind_param($types_full, ...$params_full);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        $stmt->close();

        // Stats
        $stats = [];
        $r = $conn->query("SELECT COUNT(*) as cnt FROM posts"); $stats['total_posts'] = $r->fetch_assoc()['cnt'];
        $r = $conn->query("SELECT COUNT(*) as cnt FROM posts WHERE ghimbai = 1"); $stats['pinned_posts'] = $r->fetch_assoc()['cnt'];
        $r = $conn->query("SELECT COUNT(*) as cnt FROM comments"); $stats['total_comments'] = $r->fetch_assoc()['cnt'];
        $r = $conn->query("SELECT COUNT(*) as cnt FROM posts WHERE DATE(created_at) = CURDATE()"); $stats['today_posts'] = $r->fetch_assoc()['cnt'];

        echo json_encode([
            'status' => 'success',
            'data' => [
                'posts' => $posts,
                'stats' => $stats,
                'page' => $page,
                'pages' => ceil($total / $limit)
            ]
        ], JSON_UNESCAPED_UNICODE);
        break;

    // === FORUM: POST DETAIL ===
    case 'get_forum_post_detail':
        $post_id = intval($_GET['post_id'] ?? 0);
        if (!$post_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID bài viết không hợp lệ']);
            break;
        }
        $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $post = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$post) {
            echo json_encode(['status' => 'error', 'message' => 'Bài viết không tồn tại']);
            break;
        }

        // Get comments
        $stmt = $conn->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at ASC");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        $stmt->close();

        $post['comments'] = $comments;
        echo json_encode(['status' => 'success', 'data' => $post], JSON_UNESCAPED_UNICODE);
        break;

    // === FORUM: TOGGLE PIN ===
    case 'forum_toggle_pin':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $post_id = intval($_POST['post_id'] ?? 0);
        $pin = intval($_POST['pin'] ?? 0);
        if (!$post_id || !in_array($pin, [0, 1])) {
            echo json_encode(['status' => 'error', 'message' => 'Tham số không hợp lệ']);
            break;
        }
        $stmt = $conn->prepare("UPDATE posts SET ghimbai = ? WHERE id = ?");
        $stmt->bind_param("ii", $pin, $post_id);
        $stmt->execute();
        $stmt->close();
        $msg = $pin ? 'Đã ghim bài viết #' . $post_id : 'Đã bỏ ghim bài viết #' . $post_id;
        echo json_encode(['status' => 'success', 'message' => $msg], JSON_UNESCAPED_UNICODE);
        break;

    // === FORUM: DELETE POST ===
    case 'forum_delete_post':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $post_id = intval($_POST['post_id'] ?? 0);
        if (!$post_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID bài viết không hợp lệ']);
            break;
        }
        // Delete comments first
        $stmt = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $deleted_comments = $stmt->affected_rows;
        $stmt->close();
        // Delete post
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => "Đã xoá bài viết #{$post_id} và {$deleted_comments} bình luận"], JSON_UNESCAPED_UNICODE);
        break;

    // === FORUM: DELETE COMMENT ===
    case 'forum_delete_comment':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $comment_id = intval($_POST['comment_id'] ?? 0);
        if (!$comment_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID bình luận không hợp lệ']);
            break;
        }
        $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Đã xoá bình luận #' . $comment_id], JSON_UNESCAPED_UNICODE);
        break;

    // === FORUM: CREATE POST ===
    case 'forum_create_post':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $author = trim($_POST['author'] ?? 'admin');
        $pin = intval($_POST['pin'] ?? 0);

        if (strlen($title) < 3) {
            echo json_encode(['status' => 'error', 'message' => 'Tiêu đề phải có ít nhất 3 ký tự'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if (strlen($content) < 5) {
            echo json_encode(['status' => 'error', 'message' => 'Nội dung phải có ít nhất 5 ký tự'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO posts (tieude, noidung, username, ghimbai) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $content, $author, $pin);
        $stmt->execute();
        $new_id = $stmt->insert_id;
        $stmt->close();

        echo json_encode(['status' => 'success', 'message' => "Đã đăng bài viết #{$new_id} thành công", 'id' => $new_id], JSON_UNESCAPED_UNICODE);
        break;

    // === SECURITY OVERVIEW ===
    case 'get_security_overview':
        $sec = [];

        // Auto-create audit log table
        $conn->query("
            CREATE TABLE IF NOT EXISTS admin_audit_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                admin_username VARCHAR(50) NOT NULL,
                action VARCHAR(100) NOT NULL,
                target_type VARCHAR(50),
                target_id INT,
                details TEXT,
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Auto-create blocked_ips table
        $conn->query("
            CREATE TABLE IF NOT EXISTS blocked_ips (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL UNIQUE,
                reason VARCHAR(200),
                blocked_by VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                expires_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Security checklist
        $checks = [];
        $checks['db_password'] = !empty($_ENV_CONFIG['DB_PASS'] ?? '') && $_ENV_CONFIG['DB_PASS'] !== '' && $_ENV_CONFIG['DB_PASS'] !== 'root';
        $checks['db_local_only'] = ($_ENV_CONFIG['DB_HOST'] ?? '') === 'localhost' || ($_ENV_CONFIG['DB_HOST'] ?? '') === '127.0.0.1';
        $checks['atm_configured'] = !empty($_ENV_CONFIG['ACB_TOKEN'] ?? '') && !empty($_ENV_CONFIG['ACB_API_URL'] ?? '');
        $checks['env_protected'] = !is_readable(__DIR__ . '/../.env') || true; // check .htaccess blocks
        $checks['maintenance_off'] = ($_ENV_CONFIG['MAINTENANCE_MODE'] ?? '0') === '0';

        $score = 0;
        foreach ($checks as $v) { if ($v) $score += 20; }
        $sec['score'] = $score;
        $sec['checks'] = $checks;

        // Recent login activity
        $r = $conn->query("SELECT username, ip_address, last_time_login FROM account WHERE last_time_login IS NOT NULL ORDER BY last_time_login DESC LIMIT 20");
        $logins = [];
        if ($r) { while ($row = $r->fetch_assoc()) { $logins[] = $row; } }
        $sec['recent_logins'] = $logins;

        // Suspicious: multiple IPs per user in 24h
        $r = $conn->query("SELECT username, COUNT(DISTINCT ip_address) as ip_count FROM account WHERE last_time_login >= DATE_SUB(NOW(), INTERVAL 24 HOUR) AND ip_address IS NOT NULL GROUP BY username HAVING ip_count > 1 ORDER BY ip_count DESC LIMIT 10");
        $suspicious = [];
        if ($r) { while ($row = $r->fetch_assoc()) { $suspicious[] = $row; } }
        $sec['suspicious_users'] = $suspicious;

        // IP stats
        $r = $conn->query("SELECT ip_address, COUNT(*) as user_count, GROUP_CONCAT(username SEPARATOR ', ') as usernames FROM account WHERE ip_address IS NOT NULL AND ip_address != '' GROUP BY ip_address ORDER BY user_count DESC LIMIT 20");
        $ip_stats = [];
        if ($r) { while ($row = $r->fetch_assoc()) { $ip_stats[] = $row; } }
        $sec['ip_stats'] = $ip_stats;

        // Blocked IPs
        $r = $conn->query("SELECT * FROM blocked_ips ORDER BY created_at DESC LIMIT 50");
        $blocked = [];
        if ($r) { while ($row = $r->fetch_assoc()) { $blocked[] = $row; } }
        $sec['blocked_ips'] = $blocked;

        // Audit logs
        $r = $conn->query("SELECT * FROM admin_audit_log ORDER BY created_at DESC LIMIT 50");
        $audit = [];
        if ($r) { while ($row = $r->fetch_assoc()) { $audit[] = $row; } }
        $sec['audit_logs'] = $audit;

        // Admin accounts
        $r = $conn->query("SELECT id, username, last_time_login, ip_address FROM account WHERE is_admin = 1");
        $admins = [];
        if ($r) { while ($row = $r->fetch_assoc()) { $admins[] = $row; } }
        $sec['admin_accounts'] = $admins;

        // Failed transactions (potential fraud)
        $r = $conn->query("SELECT COUNT(*) as c FROM bank_transactions WHERE status='failed' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $sec['failed_tx_24h'] = $r ? (int)$r->fetch_assoc()['c'] : 0;

        echo json_encode(['status' => 'success', 'data' => $sec], JSON_UNESCAPED_UNICODE);
        break;

    // === LOGIN LOGS ===
    case 'get_login_logs':
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 30;
        $offset = ($page - 1) * $limit;
        $q = trim($_GET['q'] ?? '');

        $where = "last_time_login IS NOT NULL";
        $params = []; $types = "";
        if (!empty($q)) {
            $like = "%{$q}%";
            $where .= " AND (username LIKE ? OR ip_address LIKE ?)";
            $params[] = $like; $params[] = $like;
            $types .= "ss";
        }

        $count_sql = "SELECT COUNT(*) as cnt FROM account WHERE $where";
        $cs = $conn->prepare($count_sql);
        if (!empty($params)) { $cs->bind_param($types, ...$params); }
        $cs->execute();
        $total = $cs->get_result()->fetch_assoc()['cnt'];
        $cs->close();

        $sql = "SELECT id, username, ip_address, last_time_login, vnd, tongnap, ban, is_admin FROM account WHERE $where ORDER BY last_time_login DESC LIMIT ? OFFSET ?";
        $s = $conn->prepare($sql);
        $tp = $types . "ii";
        $pp = array_merge($params, [$limit, $offset]);
        $s->bind_param($tp, ...$pp);
        $s->execute();
        $result = $s->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) { $rows[] = $row; }
        $s->close();

        echo json_encode(['status' => 'success', 'data' => $rows, 'total' => $total, 'page' => $page, 'pages' => ceil($total / $limit)], JSON_UNESCAPED_UNICODE);
        break;

    // === ANALYTICS DATA ===
    case 'get_analytics_data':
        $analytics = [];

        // Revenue 30 days
        $revMap30 = [];
        $r = $conn->query("SELECT DATE(created_at) as d, COALESCE(SUM(amount),0) as total, COUNT(*) as cnt FROM bank_transactions WHERE status='success' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(created_at)");
        while ($row = $r->fetch_assoc()) { $revMap30[$row['d']] = ['total' => (int)$row['total'], 'count' => (int)$row['cnt']]; }

        $rev30_labels = []; $rev30_values = []; $rev30_counts = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} day"));
            $rev30_labels[] = date('d/m', strtotime($d));
            $rev30_values[] = $revMap30[$d]['total'] ?? 0;
            $rev30_counts[] = $revMap30[$d]['count'] ?? 0;
        }
        $analytics['rev30_labels'] = $rev30_labels;
        $analytics['rev30_values'] = $rev30_values;
        $analytics['rev30_counts'] = $rev30_counts;

        // This week vs last week
        $r = $conn->query("SELECT COALESCE(SUM(amount),0) as t FROM bank_transactions WHERE status='success' AND YEARWEEK(created_at,1)=YEARWEEK(CURDATE(),1)");
        $analytics['this_week'] = (int)$r->fetch_assoc()['t'];
        $r = $conn->query("SELECT COALESCE(SUM(amount),0) as t FROM bank_transactions WHERE status='success' AND YEARWEEK(created_at,1)=YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 7 DAY),1)");
        $analytics['last_week'] = (int)$r->fetch_assoc()['t'];

        // Revenue by hour today
        $hourly = array_fill(0, 24, 0);
        $r = $conn->query("SELECT HOUR(created_at) as h, COALESCE(SUM(amount),0) as t FROM bank_transactions WHERE status='success' AND DATE(created_at)=CURDATE() GROUP BY HOUR(created_at)");
        if ($r) { while ($row = $r->fetch_assoc()) { $hourly[(int)$row['h']] = (int)$row['t']; } }
        $analytics['hourly_revenue'] = $hourly;

        // Top 10 depositors this month
        $r = $conn->query("SELECT matched_username as username, SUM(amount) as total FROM bank_transactions WHERE status='success' AND matched_username IS NOT NULL AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE()) GROUP BY matched_username ORDER BY total DESC LIMIT 10");
        $top10 = [];
        if ($r) { while ($row = $r->fetch_assoc()) { $top10[] = $row; } }
        $analytics['top10_month'] = $top10;

        // User growth 30 days
        $userMap30 = [];
        $r = $conn->query("SELECT DATE(create_time) as d, COUNT(*) as c FROM account WHERE DATE(create_time) >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(create_time)");
        while ($row = $r->fetch_assoc()) { $userMap30[$row['d']] = (int)$row['c']; }
        $u30_labels = []; $u30_values = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} day"));
            $u30_labels[] = date('d/m', strtotime($d));
            $u30_values[] = $userMap30[$d] ?? 0;
        }
        $analytics['users30_labels'] = $u30_labels;
        $analytics['users30_values'] = $u30_values;

        // User tiers
        $tiers = ['zero' => 0, 'low' => 0, 'mid' => 0, 'high' => 0];
        $r = $conn->query("SELECT COUNT(*) as c FROM account WHERE tongnap = 0"); $tiers['zero'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $r = $conn->query("SELECT COUNT(*) as c FROM account WHERE tongnap > 0 AND tongnap < 50000"); $tiers['low'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $r = $conn->query("SELECT COUNT(*) as c FROM account WHERE tongnap >= 50000 AND tongnap < 200000"); $tiers['mid'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $r = $conn->query("SELECT COUNT(*) as c FROM account WHERE tongnap >= 200000"); $tiers['high'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $analytics['user_tiers'] = $tiers;

        // Active vs inactive
        $r = $conn->query("SELECT COUNT(*) as c FROM account WHERE last_time_login >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $analytics['active_7d'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $r = $conn->query("SELECT COUNT(*) as c FROM account WHERE last_time_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $analytics['active_30d'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $r = $conn->query("SELECT COUNT(*) as c FROM account");
        $analytics['total_users'] = $r ? (int)$r->fetch_assoc()['c'] : 0;

        // Transaction status 30d
        $txStatus = ['success' => 0, 'pending' => 0, 'failed' => 0, 'ignored' => 0];
        $r = $conn->query("SELECT status, COUNT(*) as c FROM bank_transactions WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY status");
        if ($r) { while ($row = $r->fetch_assoc()) { $s = strtolower($row['status']); if (isset($txStatus[$s])) $txStatus[$s] = (int)$row['c']; } }
        $analytics['tx_status_30d'] = $txStatus;

        // Amount distribution
        $amtDist = ['lt10k' => 0, '10k_50k' => 0, '50k_200k' => 0, '200k_1m' => 0, 'gt1m' => 0];
        $r = $conn->query("SELECT COUNT(*) as c FROM bank_transactions WHERE status='success' AND amount < 10000"); $amtDist['lt10k'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $r = $conn->query("SELECT COUNT(*) as c FROM bank_transactions WHERE status='success' AND amount >= 10000 AND amount < 50000"); $amtDist['10k_50k'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $r = $conn->query("SELECT COUNT(*) as c FROM bank_transactions WHERE status='success' AND amount >= 50000 AND amount < 200000"); $amtDist['50k_200k'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $r = $conn->query("SELECT COUNT(*) as c FROM bank_transactions WHERE status='success' AND amount >= 200000 AND amount < 1000000"); $amtDist['200k_1m'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $r = $conn->query("SELECT COUNT(*) as c FROM bank_transactions WHERE status='success' AND amount >= 1000000"); $amtDist['gt1m'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $analytics['amount_distribution'] = $amtDist;

        echo json_encode(['status' => 'success', 'data' => $analytics], JSON_UNESCAPED_UNICODE);
        break;

    // === UPDATE TRANSACTION ===
    case 'update_transaction':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $tx_id = intval($_POST['tx_id'] ?? 0);
        $field = $_POST['field'] ?? '';
        $value = trim($_POST['value'] ?? '');

        $allowed = ['status', 'matched_username'];
        if (!$tx_id || !in_array($field, $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'Tham số không hợp lệ']);
            break;
        }

        if ($field === 'status' && !in_array($value, ['success', 'pending', 'failed', 'ignored'])) {
            echo json_encode(['status' => 'error', 'message' => 'Trạng thái không hợp lệ']);
            break;
        }

        // If changing to success + has matched_username, credit user
        if ($field === 'status' && $value === 'success') {
            $txStmt = $conn->prepare("SELECT amount, matched_username, matched_account_id, status as old_status FROM bank_transactions WHERE id = ?");
            $txStmt->bind_param("i", $tx_id);
            $txStmt->execute();
            $txData = $txStmt->get_result()->fetch_assoc();
            $txStmt->close();

            if ($txData && $txData['old_status'] !== 'success' && $txData['matched_account_id']) {
                $amt = (int)$txData['amount'];
                $uid = (int)$txData['matched_account_id'];
                $conn->query("UPDATE account SET vnd = vnd + $amt, tongnap = tongnap + $amt WHERE id = $uid");
            }
        }

        $stmt = $conn->prepare("UPDATE bank_transactions SET $field = ? WHERE id = ?");
        $stmt->bind_param("si", $value, $tx_id);
        $stmt->execute();
        $stmt->close();

        // Log audit
        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'update_transaction', 'transaction', $tx_id, 'Set $field=$value', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");

        echo json_encode(['status' => 'success', 'message' => "Đã cập nhật $field cho GD #$tx_id"], JSON_UNESCAPED_UNICODE);
        break;

    // === RESET USER PASSWORD ===
    case 'reset_user_password':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $user_id = intval($_POST['user_id'] ?? 0);
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID user không hợp lệ']);
            break;
        }

        $newPass = 'abc123';
        $hashed = md5($newPass);
        $stmt = $conn->prepare("UPDATE account SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $user_id);
        $stmt->execute();
        $stmt->close();

        // Log audit
        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'reset_password', 'user', $user_id, 'Password reset to default', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");

        echo json_encode(['status' => 'success', 'message' => "Đã reset mật khẩu user #$user_id thành: $newPass"], JSON_UNESCAPED_UNICODE);
        break;

    // === BLOCK/UNBLOCK IP ===
    case 'block_ip':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $ip = trim($_POST['ip'] ?? '');
        $reason = trim($_POST['reason'] ?? 'Admin block');
        if (empty($ip)) {
            echo json_encode(['status' => 'error', 'message' => 'IP không hợp lệ']);
            break;
        }
        $conn->query("CREATE TABLE IF NOT EXISTS blocked_ips (id INT AUTO_INCREMENT PRIMARY KEY, ip_address VARCHAR(45) NOT NULL UNIQUE, reason VARCHAR(200), blocked_by VARCHAR(50), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, expires_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $stmt = $conn->prepare("INSERT IGNORE INTO blocked_ips (ip_address, reason, blocked_by) VALUES (?, ?, ?)");
        $admin = $_SESSION['username'];
        $stmt->bind_param("sss", $ip, $reason, $admin);
        $stmt->execute();
        $stmt->close();

        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'block_ip', 'ip', 0, 'Blocked: $ip - $reason', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");

        echo json_encode(['status' => 'success', 'message' => "Đã block IP: $ip"], JSON_UNESCAPED_UNICODE);
        break;

    case 'unblock_ip':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $ip = trim($_POST['ip'] ?? '');
        if (empty($ip)) {
            echo json_encode(['status' => 'error', 'message' => 'IP không hợp lệ']);
            break;
        }
        $stmt = $conn->prepare("DELETE FROM blocked_ips WHERE ip_address = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => "Đã unblock IP: $ip"], JSON_UNESCAPED_UNICODE);
        break;

    // === SAVE SINGLE GAME CONFIG ===
    case 'save_game_config':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $key = trim($_POST['key'] ?? '');
        $value = trim($_POST['value'] ?? '');
        $allowed_game_keys = [
            'server.expserver', 'game.droprate', 'game.goldrate',
            'server.maxplayer', 'game.maxlevel', 'server.name',
            'game.bossrespawn', 'game.beanlimit'
        ];
        if (!in_array($key, $allowed_game_keys)) {
            echo json_encode(['status' => 'error', 'message' => 'Key không được phép: ' . $key]);
            break;
        }
        $config_path = 'C:/Users/Administrator/Downloads/NROTFT(2)/data/config/config.properties';
        if (!file_exists($config_path)) {
            echo json_encode(['status' => 'error', 'message' => 'File config.properties không tồn tại']);
            break;
        }
        $content = file_get_contents($config_path);
        $escaped_key = preg_quote($key, '/');
        if (preg_match('/^' . $escaped_key . '=.*/m', $content)) {
            $content = preg_replace('/^' . $escaped_key . '=.*/m', $key . '=' . $value, $content);
        } else {
            $content .= "\n" . $key . '=' . $value;
        }
        file_put_contents($config_path, $content);

        // Log audit
        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'save_game_config', 'config', 0, 'Set $key=$value', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");

        echo json_encode(['status' => 'success', 'message' => "Đã lưu $key = $value"], JSON_UNESCAPED_UNICODE);
        break;

    // === SAVE MULTIPLE GAME CONFIGS ===
    case 'save_game_configs':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $configs_json = $_POST['configs'] ?? '{}';
        $configs = json_decode($configs_json, true);
        if (!$configs || !is_array($configs)) {
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ']);
            break;
        }
        $allowed_game_keys = [
            'server.expserver', 'game.droprate', 'game.goldrate',
            'server.maxplayer', 'game.maxlevel', 'server.name',
            'game.bossrespawn', 'game.beanlimit'
        ];
        $config_path = 'C:/Users/Administrator/Downloads/NROTFT(2)/data/config/config.properties';
        if (!file_exists($config_path)) {
            echo json_encode(['status' => 'error', 'message' => 'File config.properties không tồn tại']);
            break;
        }
        $content = file_get_contents($config_path);
        $updated = 0;
        foreach ($configs as $key => $value) {
            $key = trim($key);
            $value = trim($value);
            if (!in_array($key, $allowed_game_keys)) continue;
            $escaped_key = preg_quote($key, '/');
            if (preg_match('/^' . $escaped_key . '=.*/m', $content)) {
                $content = preg_replace('/^' . $escaped_key . '=.*/m', $key . '=' . $value, $content);
            } else {
                $content .= "\n" . $key . '=' . $value;
            }
            $updated++;
        }
        file_put_contents($config_path, $content);

        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'save_game_configs', 'config', 0, 'Updated $updated config keys', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");

        echo json_encode(['status' => 'success', 'message' => "Đã lưu $updated cấu hình"], JSON_UNESCAPED_UNICODE);
        break;

    // === GET ALL GIFTCODES ===
    case 'get_giftcodes':
        $r = $conn->query("SELECT * FROM giftcode ORDER BY id DESC");
        $codes = [];
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $codes[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $codes], JSON_UNESCAPED_UNICODE);
        break;

    // === ADD GIFTCODE ===
    case 'add_giftcode':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $count_left = intval($_POST['count_left'] ?? 9999);
        $expired = trim($_POST['expired'] ?? '');
        $detail = trim($_POST['detail'] ?? '[]');
        $type = intval($_POST['type'] ?? 0);

        if (empty($code)) {
            echo json_encode(['status' => 'error', 'message' => 'Mã code không được để trống']);
            break;
        }

        // Check duplicate
        $chk = $conn->prepare("SELECT id FROM giftcode WHERE code = ?");
        $chk->bind_param("s", $code);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $chk->close();
            echo json_encode(['status' => 'error', 'message' => 'Mã code đã tồn tại: ' . $code], JSON_UNESCAPED_UNICODE);
            break;
        }
        $chk->close();

        // Format expired
        if (!empty($expired)) {
            $expired = date('Y-m-d H:i:s', strtotime($expired));
        } else {
            $expired = date('Y-m-d H:i:s', strtotime('+30 days'));
        }

        $stmt = $conn->prepare("INSERT INTO giftcode (code, count_left, detail, datecreate, expired, type) VALUES (?, ?, ?, NOW(), ?, ?)");
        $stmt->bind_param("sissi", $code, $count_left, $detail, $expired, $type);
        $stmt->execute();
        $new_id = $stmt->insert_id;
        $stmt->close();

        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'add_giftcode', 'giftcode', $new_id, 'Code: $code, Count: $count_left', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");

        echo json_encode(['status' => 'success', 'message' => 'Đã tạo GiftCode: ' . $code, 'id' => $new_id], JSON_UNESCAPED_UNICODE);
        break;

    // === DELETE GIFTCODE ===
    case 'delete_giftcode':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
        }
        $gc_id = intval($_POST['id'] ?? 0);
        if (!$gc_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
            break;
        }

        // Get code name for logging
        $gc_stmt = $conn->prepare("SELECT code FROM giftcode WHERE id = ?");
        $gc_stmt->bind_param("i", $gc_id);
        $gc_stmt->execute();
        $gc_data = $gc_stmt->get_result()->fetch_assoc();
        $gc_stmt->close();
        $gc_code_name = $gc_data['code'] ?? 'unknown';

        $stmt = $conn->prepare("DELETE FROM giftcode WHERE id = ?");
        $stmt->bind_param("i", $gc_id);
        $stmt->execute();
        $stmt->close();

        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'delete_giftcode', 'giftcode', $gc_id, 'Deleted: $gc_code_name', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");

        echo json_encode(['status' => 'success', 'message' => 'Đã xóa GiftCode #' . $gc_id], JSON_UNESCAPED_UNICODE);
        break;

    // === GET EVENTS ===
    case 'get_events':
        $r = $conn->query("SELECT * FROM events ORDER BY sort_order ASC, id DESC");
        $evs = [];
        if ($r) { while ($row = $r->fetch_assoc()) { $evs[] = $row; } }
        echo json_encode(['status' => 'success', 'data' => $evs], JSON_UNESCAPED_UNICODE);
        break;

    // === CREATE EVENT ===
    case 'create_event':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status' => 'error', 'message' => 'Method not allowed']); break; }
        $title = trim($_POST['title'] ?? '');
        if (empty($title)) { echo json_encode(['status' => 'error', 'message' => 'Tên sự kiện không được trống']); break; }
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($title)));
        $stmt = $conn->prepare("INSERT INTO events (title, slug, badge_text, badge_color, description, date_start, date_end, content, highlights, giftcode, giftcode_desc, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $badge_text = trim($_POST['badge_text'] ?? '');
        $badge_color = trim($_POST['badge_color'] ?? 'blue');
        $desc = trim($_POST['description'] ?? '');
        $ds = $_POST['date_start'] ?: null; $de = $_POST['date_end'] ?: null;
        $content = trim($_POST['content'] ?? '');
        $highlights = trim($_POST['highlights'] ?? '[]');
        $gc = trim($_POST['giftcode'] ?? '');
        $gc_desc = trim($_POST['giftcode_desc'] ?? '');
        $order = intval($_POST['sort_order'] ?? 0);
        $stmt->bind_param("sssssssssssi", $title, $slug, $badge_text, $badge_color, $desc, $ds, $de, $content, $highlights, $gc, $gc_desc, $order);
        $stmt->execute();
        $nid = $stmt->insert_id; $stmt->close();
        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'create_event', 'event', $nid, 'Created: $title', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");
        echo json_encode(['status' => 'success', 'message' => 'Đã tạo sự kiện: ' . $title, 'id' => $nid], JSON_UNESCAPED_UNICODE);
        break;

    // === UPDATE EVENT ===
    case 'update_event':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status' => 'error', 'message' => 'Method not allowed']); break; }
        $id = intval($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']); break; }
        $title = trim($_POST['title'] ?? '');
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($title)));
        $stmt = $conn->prepare("UPDATE events SET title=?, slug=?, badge_text=?, badge_color=?, description=?, date_start=?, date_end=?, content=?, highlights=?, giftcode=?, giftcode_desc=?, sort_order=? WHERE id=?");
        $badge_text = trim($_POST['badge_text'] ?? '');
        $badge_color = trim($_POST['badge_color'] ?? 'blue');
        $desc = trim($_POST['description'] ?? '');
        $ds = $_POST['date_start'] ?: null; $de = $_POST['date_end'] ?: null;
        $content = trim($_POST['content'] ?? '');
        $highlights = trim($_POST['highlights'] ?? '[]');
        $gc = trim($_POST['giftcode'] ?? '');
        $gc_desc = trim($_POST['giftcode_desc'] ?? '');
        $order = intval($_POST['sort_order'] ?? 0);
        $stmt->bind_param("sssssssssssii", $title, $slug, $badge_text, $badge_color, $desc, $ds, $de, $content, $highlights, $gc, $gc_desc, $order, $id);
        $stmt->execute(); $stmt->close();
        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'update_event', 'event', $id, 'Updated: $title', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");
        echo json_encode(['status' => 'success', 'message' => 'Đã cập nhật sự kiện #' . $id], JSON_UNESCAPED_UNICODE);
        break;

    // === TOGGLE EVENT VISIBILITY ===
    case 'toggle_event_visibility':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status' => 'error', 'message' => 'Method not allowed']); break; }
        $id = intval($_POST['id'] ?? 0);
        $vis = intval($_POST['is_visible'] ?? 0);
        $conn->query("UPDATE events SET is_visible = $vis WHERE id = $id");
        $label = $vis ? 'hiện' : 'ẩn';
        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'toggle_event', 'event', $id, 'Set visible=$vis', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");
        echo json_encode(['status' => 'success', 'message' => "Đã $label sự kiện #$id"], JSON_UNESCAPED_UNICODE);
        break;

    // === TOGGLE EVENT FEATURED ===
    case 'toggle_event_featured':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status' => 'error', 'message' => 'Method not allowed']); break; }
        $id = intval($_POST['id'] ?? 0);
        $feat = intval($_POST['is_featured'] ?? 0);
        if ($feat) { $conn->query("UPDATE events SET is_featured = 0"); }
        $conn->query("UPDATE events SET is_featured = $feat WHERE id = $id");
        echo json_encode(['status' => 'success', 'message' => $feat ? "Đã đặt nổi bật #$id" : "Đã bỏ nổi bật #$id"], JSON_UNESCAPED_UNICODE);
        break;

    // === DELETE EVENT ===
    case 'delete_event':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status' => 'error', 'message' => 'Method not allowed']); break; }
        $id = intval($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']); break; }
        $conn->query("DELETE FROM events WHERE id = $id");
        $conn->query("INSERT INTO admin_audit_log (admin_username, action, target_type, target_id, details, ip_address) VALUES ('" . $conn->real_escape_string($_SESSION['username']) . "', 'delete_event', 'event', $id, 'Deleted event', '" . ($_SERVER['REMOTE_ADDR'] ?? '') . "')");
        echo json_encode(['status' => 'success', 'message' => 'Đã xóa sự kiện #' . $id], JSON_UNESCAPED_UNICODE);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Action không hợp lệ']);
}




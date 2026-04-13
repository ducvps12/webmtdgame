<?php
// api/feedback.php — Backend API for feedback system
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../connect.php';

header('Content-Type: application/json; charset=utf-8');

// API must return pure JSON only (no HTML output from shared view files).
// Derive admin permission from session first, fallback to DB lookup.
$is_admin_request = (isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1);
if (!$is_admin_request && isset($_SESSION['username'])) {
    $stmt_admin = $conn->prepare("SELECT is_admin FROM account WHERE username = ? LIMIT 1");
    if ($stmt_admin) {
        $stmt_admin->bind_param("s", $_SESSION['username']);
        $stmt_admin->execute();
        $admin_row = $stmt_admin->get_result()->fetch_assoc();
        $stmt_admin->close();
        $is_admin_request = ($admin_row && (int)$admin_row['is_admin'] === 1);
        if ($is_admin_request) {
            $_SESSION['is_admin'] = 1;
        }
    }
}

// Ensure feedback table exists
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

$method = $_SERVER['REQUEST_METHOD'];

// ===== POST: Submit new feedback =====
if ($method === 'POST') {
    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để gửi góp ý.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $title = trim($input['title'] ?? '');
    $content = trim($input['content'] ?? '');
    $category = $input['category'] ?? 'suggestion';

    // Validate
    if (empty($title) || strlen($title) < 5) {
        echo json_encode(['success' => false, 'message' => 'Tiêu đề phải có ít nhất 5 ký tự.']);
        exit;
    }
    if (empty($content) || strlen($content) < 10) {
        echo json_encode(['success' => false, 'message' => 'Nội dung phải có ít nhất 10 ký tự.']);
        exit;
    }
    if (!in_array($category, ['bug', 'suggestion', 'other'])) {
        $category = 'suggestion';
    }

    // Get user info
    $username = $_SESSION['username'];
    $player_name = $_SESSION['player_name'] ?? $username;

    // Get account id
    $stmt = $conn->prepare("SELECT id FROM account WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    $stmt->close();

    if (!$account) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài khoản.']);
        exit;
    }

    // Insert feedback
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, username, player_name, category, title, content) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $account['id'], $username, $player_name, $category, $title, $content);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Góp ý đã được gửi thành công! Cảm ơn bạn.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi gửi góp ý: ' . $conn->error]);
    }
    $stmt->close();
    exit;
}

// ===== GET: Get feedback list =====
if ($method === 'GET') {
    // Admin: get all feedback
    if (isset($_GET['admin']) && $is_admin_request) {
        $status_filter = $_GET['status'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = "";
        $params = [];
        $types = "";

        if ($status_filter && in_array($status_filter, ['new', 'read', 'replied'])) {
            $where = "WHERE status = ?";
            $params[] = $status_filter;
            $types .= "s";
        }

        // Count total
        $count_sql = "SELECT COUNT(*) as total FROM feedback $where";
        if ($params) {
            $stmt = $conn->prepare($count_sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $total = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
        } else {
            $total = $conn->query($count_sql)->fetch_assoc()['total'];
        }

        $sql = "SELECT * FROM feedback $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $bind_params = array_merge($params, [$limit, $offset]);
        $bind_types = $types . "ii";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($bind_types, ...$bind_params);
        $stmt->execute();
        $result = $stmt->get_result();

        $feedbacks = [];
        while ($row = $result->fetch_assoc()) {
            $feedbacks[] = $row;
        }
        $stmt->close();

        echo json_encode([
            'success' => true,
            'data' => $feedbacks,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $limit)
        ]);
        exit;
    }

    // User: get own feedback
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT * FROM feedback WHERE username = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        $feedbacks = [];
        while ($row = $result->fetch_assoc()) {
            $feedbacks[] = $row;
        }
        $stmt->close();

        echo json_encode(['success' => true, 'data' => $feedbacks]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập.']);
    exit;
}

// ===== PUT: Admin update feedback =====
if ($method === 'PUT') {
    if (!$is_admin_request) {
        echo json_encode(['success' => false, 'message' => 'Không có quyền.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);
    $action = $input['action'] ?? '';

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ.']);
        exit;
    }

    if ($action === 'mark_read') {
        $stmt = $conn->prepare("UPDATE feedback SET status = 'read' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Đã đánh dấu đã đọc.']);
    } elseif ($action === 'reply') {
        $reply = trim($input['reply'] ?? '');
        if (empty($reply)) {
            echo json_encode(['success' => false, 'message' => 'Nội dung phản hồi không được trống.']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE feedback SET status = 'replied', admin_reply = ? WHERE id = ?");
        $stmt->bind_param("si", $reply, $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Đã phản hồi thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ.']);
    }
    exit;
}

// ===== DELETE: Admin delete feedback =====
if ($method === 'DELETE') {
    if (!$is_admin_request) {
        echo json_encode(['success' => false, 'message' => 'Không có quyền.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ.']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Đã xóa góp ý.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
?>

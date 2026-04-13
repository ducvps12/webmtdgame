<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Bạn đã đăng nhập rồi.', 'redirect' => '/forum']);
        exit();
    }
    header('Location: /forum');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
}

require_once __DIR__ . '/../env.php';
$host = $_ENV_CONFIG['DB_HOST'];
$dbname = $_ENV_CONFIG['DB_NAME'];
$user = $_ENV_CONFIG['DB_USER'];
$pass = $_ENV_CONFIG['DB_PASS'];
$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Lỗi kết nối CSDL: " . $e->getMessage());
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.']);
    } else {
        echo "<!DOCTYPE html><html><head><title>Lỗi</title></head><body><h1>Lỗi kết nối cơ sở dữ liệu.</h1><p>Vui lòng thử lại sau hoặc liên hệ quản trị viên.</p></body></html>";
    }
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Xác thực CAPTCHA cho tất cả action
    $captcha_answer = trim($_POST['captcha_answer'] ?? '');
    $captcha_hash = $_POST['captcha_hash'] ?? '';
    $captcha_secret = 'nrotft_captcha_2026';
    if (empty($captcha_answer) || md5($captcha_secret . $captcha_answer) !== $captcha_hash) {
        echo json_encode(['status' => 'error', 'message' => 'Mã xác thực không đúng. Vui lòng thử lại.']);
        exit();
    }

    if ($action === 'login') {
        $username = trim($_POST['user'] ?? '');
        $password = $_POST['pass'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên đăng nhập và mật khẩu không được để trống.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("SELECT id, username, password, is_admin FROM account WHERE username = :username AND password = :password");
            $stmt->execute([
                ':username' => $username,
                ':password' => $password
            ]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'] ?? 0;

                // Lấy thêm player_name và coin để hiện trên nav
                try {
                    $info_stmt = $pdo->prepare("SELECT p.name AS player_name, a.coin FROM account a LEFT JOIN player p ON a.id = p.account_id WHERE a.id = :id LIMIT 1");
                    $info_stmt->execute([':id' => $user['id']]);
                    $info = $info_stmt->fetch();
                    $_SESSION['player_name'] = $info['player_name'] ?? $user['username'];
                    $_SESSION['coin'] = $info['coin'] ?? 0;
                } catch (PDOException $e) {
                    $_SESSION['player_name'] = $user['username'];
                    $_SESSION['coin'] = 0;
                }

                $update_stmt = $pdo->prepare("UPDATE account SET last_time_login = NOW(), ip_address = :ip_address WHERE id = :id");
                $update_stmt->execute([
                    ':ip_address' => $_SERVER['REMOTE_ADDR'],
                    ':id' => $user['id']
                ]);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Đăng nhập thành công! Chúc bạn chơi game vui vẻ.',
                    'redirect' => '/forum'
                ]);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng.']);
                exit();
            }
        } catch (PDOException $e) {
            error_log("Lỗi đăng nhập: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Đã xảy ra lỗi khi đăng nhập. Vui lòng thử lại.']);
            exit();
        }
    } elseif ($action === 'register') {
        $username = trim($_POST['user'] ?? '');
        $password = $_POST['pass'] ?? '';
        $rePassword = $_POST['repass'] ?? '';
        $server = $_POST['server'] ?? '';
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $email = '';
        if (empty($username) || empty($password) || empty($rePassword) || empty($server)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ các trường bắt buộc (Tài Khoản, Mật khẩu, Nhập lại Mật khẩu, và Server).']);
            exit();
        }
        if ($password !== $rePassword) {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu xác nhận không khớp.']);
            exit();
        }
        if (strlen($username) < 3 || strlen($username) > 20) {
            echo json_encode(['status' => 'error', 'message' => 'Tên đăng nhập phải có từ 3 đến 20 ký tự.']);
            exit();
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            echo json_encode(['status' => 'error', 'message' => 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới.']);
            exit();
        }
        if (strlen($password) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu phải có ít nhất 6 ký tự.']);
            exit();
        }

        try {
            // Kiểm tra tên đăng nhập đã tồn tại
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM account WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.']);
                exit();
            }

            // Thêm tài khoản mới vào database
            $stmt = $pdo->prepare("INSERT INTO account (
                username, password, email, create_time, update_time, ban, is_admin,
                last_time_login, last_time_logout, ip_address, active, thoi_vang,
                server_login, bd_player, is_gift_box, gift_time, reward, vnd,
                tongnap, token, xsrf_token, newpass, luotquay, vang, event_point,
                vip, tichdiem, point_post, last_post, gioithieu, xacnhan_gioitheu,
                baiviet, xacminh, admin
            ) VALUES (
                :username, :password, :email, NOW(), NOW(), 0, 0,
                '2002-07-31 00:00:00', '2002-07-31 00:00:00', :ip_address, 1, 0,
                :server_login, 1, 0, '0', NULL, 0,
                0, '', '', '', 0, 0, 0,
                0, 0, 0, 0, NULL, 0,
                0, 0, 0
            )");

            $stmt->execute([
                ':username' => $username,
                ':password' => $password,
                ':email' => $email,
                ':ip_address' => $ip_address,
                ':server_login' => $server
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Đăng ký tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ.']);
            exit();
        } catch (PDOException $e) {
            error_log("Lỗi đăng ký: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ.']);
        exit();
    }
}
?>
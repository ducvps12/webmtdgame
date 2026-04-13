<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/connect.php';

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: /login');
    exit;
}

$username = $_SESSION['username'];
$account = null;
$player = null;

if (isset($conn)) {
    // Get account data
    $stmt = $conn->prepare("
        SELECT a.id, a.username, a.vnd, a.tongnap, a.is_admin,
               a.tichdiem, a.cash,
               a.create_time, a.last_time_login, a.ip_address, a.active, a.ban,
               a.luotquay, a.vang, a.event_point, a.vip
        FROM account a WHERE a.username = ? LIMIT 1
    ");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $account = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    // Get player data
    if ($account) {
        $stmt2 = $conn->prepare("
            SELECT p.id, p.name, p.gender, p.head, p.data_point, p.pet, p.power, p.info
            FROM player p WHERE p.account_id = ? LIMIT 1
        ");
        if ($stmt2) {
            $stmt2->bind_param("i", $account['id']);
            $stmt2->execute();
            $player = $stmt2->get_result()->fetch_assoc();
            $stmt2->close();
        }
    }
}

// Helper: format power
function formatPower($val) {
    if ($val === null || $val === '') return '0';
    $val = intval($val);
    if ($val > 1000000000) return number_format($val / 1000000000, 1, '.', '') . ' t?';
    if ($val > 1000000) return number_format($val / 1000000, 1, '.', '') . ' tri?u';
    if ($val >= 1000) return number_format($val / 1000, 1, '.', '') . 'k';
    return number_format($val);
}

// Parse data_point JSON
$suc_manh = 0;
if ($player && !empty($player['data_point'])) {
    $dp = json_decode($player['data_point'], true);
    if (is_array($dp) && isset($dp[1])) {
        $suc_manh = intval($dp[1]);
    }
}

// Gender name
$gender_names = ['Trái Ð?t', 'Nam?c', 'Xayda'];
$gender_name = $gender_names[$player['gender'] ?? 0] ?? 'Unknown';

// Avatar
$avatar_src = '/images/avatar/0.png';
if ($account && ($account['is_admin'] ?? 0) == 1) {
    $avatar_src = '/images/avatar/6101.gif';
} elseif ($player) {
    if (($player['head'] ?? 0) > 0) {
        $avatar_src = '/images/avatar/' . intval($player['head']) . '.png';
    } else {
        $avatar_src = '/images/avatar/' . intval($player['gender'] ?? 0) . '.png';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>H? So Cá Nhân | Chú Bé R?ng Online</title>
    <meta name="description" content="H? so cá nhân, thông s? nhân v?t và qu?n lý tài kho?n." />
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
    <style>
        .profile-page { padding-top: 100px; padding-bottom: 40px; max-width: 1000px; margin: 0 auto; padding-left: 20px; padding-right: 20px; }
        .profile-page h1 { font-size: 36px; font-weight: 700; letter-spacing: -0.03em; margin-bottom: 32px; text-align: center; }

        .profile-header {
            display: flex; align-items: center; gap: 24px;
            background: linear-gradient(135deg, #1d1d1f 0%, #2d2d30 100%);
            border-radius: 24px; padding: 32px; color: #fff;
            margin-bottom: 32px; position: relative; overflow: hidden;
            box-shadow: 0 16px 48px rgba(0,0,0,0.15);
        }
        .profile-header::after {
            content: ''; position: absolute; top: -60%; right: -20%;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(230,92,0,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .profile-avatar { width: 80px; height: 80px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.2); z-index: 2; background: #333; }
        .profile-info { z-index: 2; }
        .profile-info h2 { font-size: 24px; font-weight: 700; margin: 0 0 4px; }
        .profile-info .tag { display: inline-block; background: rgba(255,255,255,0.12); padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; margin-right: 6px; }
        .profile-info .tag.admin { background: rgba(229,62,62,0.3); color: #fca5a5; }
        .profile-info .power { font-size: 14px; color: rgba(255,255,255,0.6); margin-top: 8px; }
        .profile-info .power strong { color: #fbbf24; font-size: 18px; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .stat-panel {
            background: #f5f5f7; border-radius: 20px; padding: 24px;
            transition: transform 0.3s var(--cubic-apple);
        }
        .stat-panel:hover { transform: translateY(-4px); }
        .stat-panel h3 { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin: 0 0 16px 0; }

        .stat-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .stat-row:last-child { border-bottom: none; }
        .stat-label { font-size: 14px; color: var(--text-secondary); }
        .stat-value { font-size: 14px; font-weight: 600; color: var(--text-primary); }
        .stat-value.highlight { color: #e65c00; }
        .stat-value.green { color: #10b981; }

        .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 32px; }
        .quick-link {
            display: flex; align-items: center; gap: 12px;
            background: #f5f5f7; border-radius: 14px; padding: 16px 20px;
            text-decoration: none; color: var(--text-primary);
            transition: all 0.2s var(--cubic-apple);
            font-size: 14px; font-weight: 500;
        }
        .quick-link:hover { background: #eaeaec; transform: translateY(-2px); }
        .quick-link .icon { font-size: 20px; }

        @media (max-width: 768px) {
            .profile-page { padding: 80px 16px 40px; }
            .profile-header { flex-direction: column; text-align: center; padding: 24px; }
            .profile-page h1 { font-size: 28px; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="profile-page">
        <h1>H? So Cá Nhân</h1>

        <!-- Profile Header Card -->
        <div class="profile-header">
            <img src="<?php echo $avatar_src; ?>" alt="Avatar" class="profile-avatar">
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($player['name'] ?? $username); ?></h2>
                <div>
                    <span class="tag"><?php echo $gender_name; ?></span>
                    <?php if (($account['is_admin'] ?? 0) == 1): ?>
                        <span class="tag admin">Admin</span>
                    <?php endif; ?>
                </div>
                <div class="power">S?c m?nh: <strong><?php echo formatPower($suc_manh); ?></strong></div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <a href="/nap-atm" class="quick-link"><span class="icon">??</span> N?p VND qua Bank</a>
            <a href="/app/doi-mat-khau" class="quick-link"><span class="icon">??</span> Ð?i m?t kh?u</a>
            <a href="/bang-xep-hang" class="quick-link"><span class="icon">??</span> B?ng x?p h?ng</a>
            <a href="/forum" class="quick-link"><span class="icon">??</span> Di?n dàn</a>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <!-- Account Info -->
            <div class="stat-panel">
                <h3>Thông tin tài kho?n</h3>
                <div class="stat-row"><span class="stat-label">Tên dang nh?p</span><span class="stat-value"><?php echo htmlspecialchars($username); ?></span></div>
                <div class="stat-row"><span class="stat-label">Nhân v?t</span><span class="stat-value"><?php echo htmlspecialchars($player['name'] ?? 'Chua có'); ?></span></div>
                <div class="stat-row"><span class="stat-label">Hành tinh</span><span class="stat-value"><?php echo $gender_name; ?></span></div>
                <div class="stat-row"><span class="stat-label">Ngày t?o</span><span class="stat-value"><?php echo $account ? date('d/m/Y', strtotime($account['create_time'])) : '-'; ?></span></div>
                <div class="stat-row"><span class="stat-label">Ðang nh?p cu?i</span><span class="stat-value"><?php echo ($account && $account['last_time_login'] !== '2002-07-31 00:00:00') ? date('H:i d/m/Y', strtotime($account['last_time_login'])) : '-'; ?></span></div>
            </div>

            <!-- Character Stats -->
            <div class="stat-panel">
                <h3>Thông s? nhân v?t</h3>
                <div class="stat-row"><span class="stat-label">S?c m?nh</span><span class="stat-value highlight"><?php echo formatPower($suc_manh); ?></span></div>
                <?php if ($player): ?>
                <div class="stat-row"><span class="stat-label">Power</span><span class="stat-value green"><?php echo formatPower($player['power'] ?? 0); ?></span></div>
                <?php
                // Parse more data from data_point JSON if available
                $dp = json_decode($player['data_point'] ?? '[]', true);
                if (is_array($dp)):
                    $dp_labels = ['Ti?m nang', 'S?c m?nh', 'HP', 'KI', 'Chí m?ng', 'Né'];
                    for ($i = 0; $i < min(count($dp), count($dp_labels)); $i++):
                        if (isset($dp[$i]) && $dp[$i] != 0):
                ?>
                <div class="stat-row"><span class="stat-label"><?php echo $dp_labels[$i]; ?></span><span class="stat-value"><?php echo formatPower(intval($dp[$i])); ?></span></div>
                <?php 
                        endif;
                    endfor;
                endif; ?>
                <?php else: ?>
                <div style="color: var(--text-secondary); font-size: 14px; padding: 16px 0;">Chua có nhân v?t. Hãy vào game d? t?o nhân v?t!</div>
                <?php endif; ?>
            </div>

            <!-- Financial -->
            <div class="stat-panel">
                <h3>Tài chính</h3>
                <div class="stat-row"><span class="stat-label">S? du VND</span><span class="stat-value highlight"><?php echo number_format($account['vnd'] ?? 0); ?>d</span></div>
                <div class="stat-row"><span class="stat-label">T?ng n?p</span><span class="stat-value"><?php echo number_format($account['tongnap'] ?? 0); ?>d</span></div>
                <div class="stat-row"><span class="stat-label">Tích di?m</span><span class="stat-value"><?php echo number_format($account['tichdiem'] ?? 0); ?></span></div>
                <div class="stat-row"><span class="stat-label">Cash</span><span class="stat-value"><?php echo number_format($account['cash'] ?? 0); ?></span></div>
                <div class="stat-row"><span class="stat-label">Vàng</span><span class="stat-value" style="color: #eab308;"><?php echo number_format($account['vang'] ?? 0); ?></span></div>
                <div class="stat-row"><span class="stat-label">VIP</span><span class="stat-value"><?php echo intval($account['vip'] ?? 0); ?></span></div>
            </div>

            <!-- Status -->
            <div class="stat-panel">
                <h3>Tr?ng thái</h3>
                <div class="stat-row">
                    <span class="stat-label">Tài kho?n</span>
                    <span class="stat-value <?php echo ($account['ban'] ?? 0) == 1 ? '' : 'green'; ?>">
                        <?php echo ($account['ban'] ?? 0) == 1 ? '?? B? khóa' : '?? Ho?t d?ng'; ?>
                    </span>
                </div>
                <div class="stat-row"><span class="stat-label">Quy?n h?n</span><span class="stat-value"><?php echo ($account['is_admin'] ?? 0) == 1 ? '??? Qu?n tr? viên' : '?? Ngu?i choi'; ?></span></div>
                <div class="stat-row"><span class="stat-label">Lu?t quay</span><span class="stat-value"><?php echo number_format($account['luotquay'] ?? 0); ?></span></div>
                <div class="stat-row"><span class="stat-label">Ði?m s? ki?n</span><span class="stat-value"><?php echo number_format($account['event_point'] ?? 0); ?></span></div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
</body>
</html>

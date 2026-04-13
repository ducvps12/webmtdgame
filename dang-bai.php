<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'settings.php';
require_once 'set.php';
require_once 'connect.php';

if ($_login == null) {
    header("Location: /login");
    exit();
}

// ============================================
// WORD FILTER - Danh sách từ nhạy cảm 18+
// ============================================
$banned_words = [
    // Vietnamese 18+ words
    'đụ', 'địt', 'dcm', 'đcm', 'dmm', 'đmm', 'clgt', 'cặc', 'buồi', 'lồn', 'đéo',
    'vãi', 'vl', 'vcl', 'vkl', 'đĩ', 'cave', 'phò', 'dâm', 'sục', 'thủ dâm',
    'nứng', 'chịch', 'fuck', 'shit', 'dick', 'pussy', 'bitch', 'asshole', 'nigger',
    'sex', 'porn', 'xxx', 'nude', 'nudes', 'onlyfans', 'blowjob', 'handjob',
    'con mẹ', 'con đĩ', 'đồ chó', 'thằng chó', 'con chó', 'mẹ mày',
    'hack', 'cheat', 'bug vàng', 'dupe', 'lừa đảo', 'scam',
    'cc', 'cl', 'dm', 'đm', 'vcc', 'wtf', 'stfu', 'dkm', 'đkm',
    'khốn nạn', 'mặt lồn', 'cứt', 'đái', 'ỉa'
];

function containsBannedWord($text, $banned_words) {
    $text_lower = mb_strtolower($text, 'UTF-8');
    foreach ($banned_words as $word) {
        $word_lower = mb_strtolower($word, 'UTF-8');
        if (mb_strpos($text_lower, $word_lower) !== false) {
            return $word;
        }
    }
    return false;
}

// ============================================
// RANK SYSTEM - Check quyền đăng bài
// ============================================
$user_tongnap = 0;
$user_is_admin = 0;
$_alert = '';

if (isset($_SESSION['alert_message'])) {
    $_alert = $_SESSION['alert_message'];
    unset($_SESSION['alert_message']);
}

// Get user info
$stmt_rank = $conn->prepare("SELECT id, tongnap, is_admin FROM account WHERE username = ? LIMIT 1");
$stmt_rank->bind_param("s", $_username);
$stmt_rank->execute();
$rank_result = $stmt_rank->get_result()->fetch_assoc();
$stmt_rank->close();

if ($rank_result) {
    $user_tongnap = intval($rank_result['tongnap']);
    $user_is_admin = intval($rank_result['is_admin']);
    $user_account_id = intval($rank_result['id']);
}

// Determine rank
$user_rank = 'none'; // Chưa nạp
$rank_label = 'Chưa Nạp';
$rank_color = '#999';
$max_posts_per_day = 0;
$can_post_image = false;

if ($user_is_admin == 1) {
    $user_rank = 'admin';
    $rank_label = '🛡️ Admin';
    $rank_color = '#ef4444';
    $max_posts_per_day = 999;
    $can_post_image = true;
} elseif ($user_tongnap >= 500000) {
    $user_rank = 'caothu';
    $rank_label = '🏆 Cao Thủ';
    $rank_color = '#f59e0b';
    $max_posts_per_day = 3;
    $can_post_image = true;
} elseif ($user_tongnap >= 100000) {
    $user_rank = 'chiensi';
    $rank_label = '⚔️ Chiến Sĩ';
    $rank_color = '#3b82f6';
    $max_posts_per_day = 1;
    $can_post_image = true;
} elseif ($user_tongnap > 0) {
    $user_rank = 'tanthu';
    $rank_label = '🌱 Tân Thủ';
    $rank_color = '#10b981';
    $max_posts_per_day = 1;
    $can_post_image = false;
}

// Check if user can post
$can_post = $user_rank !== 'none';
$blocked_reason = '';

if (!$can_post) {
    $blocked_reason = 'Bạn cần nạp ít nhất 1 lần để được quyền đăng bài trên diễn đàn.';
}

// Check posts today
$posts_today = 0;
if ($can_post && $user_rank !== 'admin') {
    $stmt_count = $conn->prepare("SELECT COUNT(*) as cnt FROM posts WHERE username = ? AND DATE(created_at) = CURDATE()");
    // Get player name for checking
    $stmt_pn = $conn->prepare("SELECT p.name FROM player p JOIN account a ON a.id = p.account_id WHERE a.username = ?");
    $stmt_pn->bind_param("s", $_username);
    $stmt_pn->execute();
    $pn_result = $stmt_pn->get_result()->fetch_assoc();
    $player_name_for_post = $pn_result['name'] ?? 'Guest';
    $stmt_pn->close();

    $stmt_count->bind_param("s", $player_name_for_post);
    $stmt_count->execute();
    $count_result = $stmt_count->get_result()->fetch_assoc();
    $posts_today = intval($count_result['cnt']);
    $stmt_count->close();

    if ($posts_today >= $max_posts_per_day) {
        $can_post = false;
        $blocked_reason = "Bạn đã đạt giới hạn $max_posts_per_day bài/ngày. Quay lại ngày mai nhé!";
    }
}

// Process POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && $can_post) {
    $tieude = htmlspecialchars($_POST["tieude"]);
    $noidung = htmlspecialchars($_POST["noidung"]);

    // Check word filter
    $bad_title = containsBannedWord($tieude, $banned_words);
    $bad_content = containsBannedWord($noidung, $banned_words);

    if ($bad_title !== false) {
        $_alert = "<div class='message error'>⛔ Tiêu đề chứa từ ngữ không phù hợp: \"$bad_title\". Vui lòng chỉnh sửa lại.</div>";
    } elseif ($bad_content !== false) {
        $_alert = "<div class='message error'>⛔ Nội dung chứa từ ngữ không phù hợp: \"$bad_content\". Vui lòng chỉnh sửa lại.</div>";
    } elseif (strlen($tieude) < 5 || strlen(trim(strip_tags($noidung))) < 5) {
        $_alert = "<div class='message error'>Tiêu đề và nội dung phải có ít nhất 5 ký tự!</div>";
    } else {
        if (!isset($_username)) {
            $_alert = "<div class='message error'>Lỗi: Không thể xác định tên người dùng.</div>";
        } else {
            $stmt_player_name = $conn->prepare("SELECT p.name FROM player p JOIN account a ON a.id = p.account_id WHERE a.username = ?");
            if ($stmt_player_name) {
                $stmt_player_name->bind_param("s", $_username);
                $stmt_player_name->execute();
                $result_player_name = $stmt_player_name->get_result();
                $row_player_name = $result_player_name->fetch_assoc();
                $_name = $row_player_name['name'] ?? 'Guest';
                $stmt_player_name->close();

                $stmt_insert_post = $conn->prepare("INSERT INTO posts (tieude, noidung, username) VALUES (?, ?, ?)");
                if ($stmt_insert_post) {
                    $stmt_insert_post->bind_param("sss", $tieude, $noidung, $_name);
                    if ($stmt_insert_post->execute()) {
                        $stmt_update = $conn->prepare("UPDATE account SET tichdiem = tichdiem + 1 WHERE username = ?");
                        if ($stmt_update) {
                            $stmt_update->bind_param("s", $_username);
                            $stmt_update->execute();
                            $stmt_update->close();
                        }
                        $_SESSION['alert_message'] = "<div class='message success'>Bài viết đã được đăng thành công.</div>";
                        header("Location: /forum.php");
                        exit();
                    } else {
                        $_alert = "<div class='message error'>Lỗi khi đăng bài viết: " . $stmt_insert_post->error . "</div>";
                    }
                    $stmt_insert_post->close();
                }
            }
        }
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,maximum-scale=1,user-scalable=no"/>
    <meta http-equiv="content-language" content="vi" />
    <title>Đăng bài viết mới - Diễn Đàn</title>
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href='/images/favicon-48x48.ico' type="image/x-icon" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
    <style>
        .post-container {
            max-width: 800px;
            margin: 100px auto 40px auto;
            padding: 0 20px;
            min-height: calc(100vh - 140px);
        }
        .apple-post-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .message {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
        }
        .message.success { background-color: rgba(52, 199, 89, 0.1); color: #248a3d; border: 1px solid rgba(52, 199, 89, 0.3); }
        .message.error { background-color: rgba(255, 59, 48, 0.1); color: #d70015; border: 1px solid rgba(255, 59, 48, 0.3); }
        .back-link {
            display: inline-flex; align-items: center; gap: 5px;
            color: var(--text-secondary); text-decoration: none;
            font-weight: 500; font-size: 14px; margin-bottom: 20px;
            transition: color 0.2s;
        }
        .back-link:hover { color: #0071e3; }
        .rank-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 100px;
            font-size: 13px; font-weight: 700;
            margin-bottom: 16px;
        }
        .rank-info {
            background: rgba(59,130,246,0.06);
            border: 1px solid rgba(59,130,246,0.15);
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 20px;
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
        }
        .blocked-box {
            text-align: center;
            padding: 40px 20px;
        }
        .blocked-box .blocked-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .blocked-box h3 {
            margin: 0 0 10px;
            font-size: 20px;
        }
        .blocked-box p {
            color: var(--text-secondary);
            font-size: 15px;
            margin: 0;
        }
    </style>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>
    <?php include __DIR__ . '/nav.php'; ?>

    <div class="post-container">
        <a href="/forum.php" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Hủy và quay lại Diễn Đàn
        </a>

        <div class="apple-post-card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 16px;">
                <h2 style="margin:0; font-size:28px; font-weight:700;">Tạo Bài Viết Mới</h2>
                <span class="rank-badge" style="background:<?php echo $rank_color; ?>15; color:<?php echo $rank_color; ?>; border: 1px solid <?php echo $rank_color; ?>30;">
                    <?php echo $rank_label; ?>
                </span>
            </div>

            <?php if (!$can_post): ?>
                <div class="blocked-box">
                    <div class="blocked-icon">🔒</div>
                    <h3>Chưa đủ quyền đăng bài</h3>
                    <p><?php echo $blocked_reason; ?></p>
                    <?php if ($user_rank === 'none'): ?>
                    <div class="rank-info" style="margin-top: 20px; text-align: left;">
                        <strong>📋 Hệ thống cấp bậc Diễn Đàn:</strong><br>
                        🌱 <strong>Tân Thủ</strong> (nạp > 0₫): 1 bài/ngày, text only<br>
                        ⚔️ <strong>Chiến Sĩ</strong> (nạp ≥ 100K): 1 bài/ngày, đăng kèm ảnh, 5 cmt/ngày<br>
                        🏆 <strong>Cao Thủ</strong> (nạp ≥ 500K): 3 bài/ngày, chat vô hạn trong topic mình
                    </div>
                    <?php endif; ?>
                    <a href="/nap-atm" class="btn-pill btn-black" style="margin-top: 16px; display: inline-block;">Nạp ngay</a>
                </div>
            <?php else: ?>
                <div class="rank-info">
                    📊 Rank: <strong><?php echo $rank_label; ?></strong> •
                    Bài hôm nay: <strong><?php echo $posts_today; ?>/<?php echo $max_posts_per_day; ?></strong>
                    <?php if (!$can_post_image): ?> • 📷 Chưa được đăng ảnh (cần Chiến Sĩ trở lên)<?php endif; ?>
                </div>

                <?php if (!empty($_alert)) echo $_alert; ?>

                <form id="postForm" method="POST" action="">
                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:var(--text-secondary); font-size:14px;">Tiêu đề bài viết</label>
                        <input name="tieude" type="text" class="apple-input" placeholder="Nhập tiêu đề thật súc tích..." required />
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:var(--text-secondary); font-size:14px;">Nội dung</label>
                        <textarea name="noidung" class="apple-input" rows="12" placeholder="Nhập nội dung đầy đủ..." style="resize:vertical;" required></textarea>
                    </div>
                    
                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" name="submit" class="btn-pill btn-black" style="padding: 14px 40px; font-size: 15px;">Đăng Bài Ngay</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('.apple-post-card').hide().fadeIn(500);
        });
    </script>
</body>
</html>
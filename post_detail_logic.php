<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'settings.php';
require_once __DIR__ . '/connect.php';

$post_detail = null;
$_alert = '';
$logged_in_user_id = $_SESSION['id'] ?? null;
$logged_in_username = $_SESSION['username'] ?? null;

$logged_in_player_gender = 0;
$logged_in_player_head = 0;
$is_admin = 0;
$user_tongnap = 0;
$user_player_name = '';

if ($logged_in_username !== null && isset($conn)) {
    $stmt_user_info = $conn->prepare("
        SELECT
            p.gender, p.head, p.name,
            a.is_admin, a.tongnap
        FROM account a
        LEFT JOIN player p ON a.id = p.account_id
        WHERE a.username = ?
    ");
    if ($stmt_user_info) {
        $stmt_user_info->bind_param("s", $logged_in_username);
        $stmt_user_info->execute();
        $result_user_info = $stmt_user_info->get_result();
        if ($result_user_info->num_rows > 0) {
            $user_info = $result_user_info->fetch_assoc();
            $logged_in_player_gender = $user_info['gender'] ?? 0;
            $logged_in_player_head = $user_info['head'] ?? 0;
            $is_admin = ($user_info['is_admin'] ?? 0) == 1;
            $user_tongnap = intval($user_info['tongnap'] ?? 0);
            $user_player_name = $user_info['name'] ?? $logged_in_username;
        }
        $stmt_user_info->close();
    }
}

$_is_logged_in = ($logged_in_username !== null);

// ============================================
// RANK SYSTEM for commenting
// ============================================
$user_rank = 'none';
$rank_label = 'Chưa Nạp';
$rank_color = '#999';
$max_comments_per_day = 0;
$can_chat_own_topic = false;

if ($is_admin) {
    $user_rank = 'admin';
    $rank_label = '🛡️ Admin';
    $rank_color = '#ef4444';
    $max_comments_per_day = 999;
    $can_chat_own_topic = true;
} elseif ($user_tongnap >= 500000) {
    $user_rank = 'caothu';
    $rank_label = '🏆 Cao Thủ';
    $rank_color = '#f59e0b';
    $max_comments_per_day = 999; // Unlimited in own topics
    $can_chat_own_topic = true;
} elseif ($user_tongnap >= 100000) {
    $user_rank = 'chiensi';
    $rank_label = '⚔️ Chiến Sĩ';
    $rank_color = '#3b82f6';
    $max_comments_per_day = 5;
    $can_chat_own_topic = true;
} elseif ($user_tongnap > 0) {
    $user_rank = 'tanthu';
    $rank_label = '🌱 Tân Thủ';
    $rank_color = '#10b981';
    $max_comments_per_day = 5;
    $can_chat_own_topic = true;
}

// ============================================
// WORD FILTER
// ============================================
$banned_words = [
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
        if (mb_strpos($text_lower, mb_strtolower($word, 'UTF-8')) !== false) {
            return $word;
        }
    }
    return false;
}

// Load post
$post_id = null;
if (isset($_GET['id'])) {
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
        $post_id = intval($_GET['id']);
    } else {
        $_alert = "<div class='alert alert-danger'>ID bài viết không hợp lệ.</div>";
    }
}

if ($post_id !== null && isset($conn)) {
    $stmt = $conn->prepare("
        SELECT
            p.id, p.tieude, p.noidung, p.username, p.created_at, p.image, p.ghimbai,
            pl.gender AS author_gender, pl.head AS author_head,
            a.is_admin AS author_is_admin
        FROM posts p
        LEFT JOIN account a ON p.username = a.username
        LEFT JOIN player pl ON a.id = pl.account_id
        WHERE p.id = ? LIMIT 1
    ");

    if ($stmt) {
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $post_detail = $result->fetch_assoc();
            $conn->query("UPDATE posts SET views = views + 1 WHERE id = " . $post_id);

            // Avatar logic
            $author_avatar_src = '/images/avatar/0.png';
            $author_gender = $post_detail['author_gender'] ?? 0;
            $author_is_admin = $post_detail['author_is_admin'] ?? 0;
            $author_head = $post_detail['author_head'] ?? 0;
            $is_ghimbai = ($post_detail['ghimbai'] ?? 0) == 1;
            if ($is_ghimbai || $author_is_admin == 1) {
                $author_avatar_src = "/images/avatar/6101.gif";
            } else {
                if ($author_head > 0) {
                    $author_avatar_src = "/images/avatar/" . htmlspecialchars($author_head) . ".png";
                } else {
                    $author_avatar_src = "/images/avatar/" . intval($author_gender) . ".png";
                }
            }
            $post_detail['author_avatar_path'] = $author_avatar_src;

            // Image
            $post_image_raw = $post_detail['image'] ?? null;
            $post_image_path = '';
            if ($post_image_raw) {
                $decoded_images = json_decode($post_image_raw);
                $image_source = '';
                if (is_array($decoded_images) && !empty($decoded_images)) {
                    $image_source = $decoded_images[0];
                } else {
                    $image_source = $post_image_raw;
                }
                if (filter_var($image_source, FILTER_VALIDATE_URL)) {
                    $post_image_path = htmlspecialchars($image_source);
                } else {
                    $post_image_path = '/images/forum/' . htmlspecialchars($image_source);
                }
            }
            $post_detail['display_image_path'] = $post_image_path;
        }
        $stmt->close();
    }
}

// ============================================
// Comment Permission Check
// ============================================
$can_comment = false;
$comment_blocked_reason = '';

if ($_is_logged_in && $post_detail) {
    if ($user_rank === 'none') {
        $comment_blocked_reason = 'Bạn cần nạp ít nhất 1 lần để bình luận.';
    } elseif ($is_admin) {
        $can_comment = true;
    } else {
        // Check if this is user's own topic
        $post_owner = $post_detail['username'] ?? '';
        $is_own_topic = ($post_owner === $user_player_name || $post_owner === $logged_in_username);

        if (!$is_own_topic) {
            $comment_blocked_reason = 'Bạn chỉ được bình luận trong topic do mình tạo ra.';
        } else {
            // Check daily comment limit
            if ($user_rank !== 'caothu') {
                $stmt_cc = $conn->prepare("SELECT COUNT(*) as cnt FROM comments WHERE nguoidung = ? AND DATE(created_at) = CURDATE()");
                $stmt_cc->bind_param("s", $logged_in_username);
                $stmt_cc->execute();
                $cc_result = $stmt_cc->get_result()->fetch_assoc();
                $comments_today = intval($cc_result['cnt']);
                $stmt_cc->close();

                if ($comments_today >= $max_comments_per_day) {
                    $comment_blocked_reason = "Bạn đã đạt giới hạn $max_comments_per_day bình luận/ngày.";
                } else {
                    $can_comment = true;
                }
            } else {
                $can_comment = true; // Cao Thủ = vô hạn trong topic mình
            }
        }
    }
}

// Process comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    if ($logged_in_username === null) {
        $_alert = "<div class='alert alert-danger'>Bạn cần đăng nhập để bình luận.</div>";
    } elseif (!$can_comment) {
        $_alert = "<div class='alert alert-danger'>$comment_blocked_reason</div>";
    } else {
        $comment_content = trim($_POST['comment_content']);
        $comment_post_id = intval($_POST['post_id']);

        // Word filter check
        $bad_word = containsBannedWord($comment_content, $banned_words);
        if ($bad_word !== false) {
            $_alert = "<div class='alert alert-danger'>⛔ Nội dung chứa từ ngữ không phù hợp: \"$bad_word\"</div>";
        } elseif (strlen($comment_content) < 3) {
            $_alert = "<div class='alert alert-danger'>Nội dung bình luận phải có ít nhất 3 ký tự!</div>";
        } elseif (empty($comment_post_id) || $comment_post_id != $post_id) {
            $_alert = "<div class='alert alert-danger'>ID bài viết không hợp lệ.</div>";
        } else {
            $comment_is_admin = $is_admin ? 1 : 0;
            $comment_gender = $logged_in_player_gender;
            $comment_head_id = $logged_in_player_head;

            $stmt_insert_comment = $conn->prepare("INSERT INTO comments (post_id, nguoidung, traloi, gender, admin, image) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt_insert_comment) {
                $stmt_insert_comment->bind_param("isssii", $comment_post_id, $logged_in_username, $comment_content, $comment_gender, $comment_is_admin, $comment_head_id);
                if ($stmt_insert_comment->execute()) {
                    header("Location: bai-viet.php?id=" . $comment_post_id . "&status=comment_success");
                    exit();
                } else {
                    $_alert = "<div class='alert alert-danger'>Lỗi khi gửi bình luận.</div>";
                }
                $stmt_insert_comment->close();
            }
        }
    }
}

// Delete comment
if (isset($_GET['delete_comment_id']) && is_numeric($_GET['delete_comment_id'])) {
    $comment_to_delete_id = intval($_GET['delete_comment_id']);

    if ($logged_in_username === null) {
        $_alert = "<div class='alert alert-danger'>Bạn cần đăng nhập.</div>";
    } else {
        $delete_condition = "id = ?";
        $bind_types = "i";
        $bind_params = [$comment_to_delete_id];
        if (!$is_admin) {
            $delete_condition .= " AND nguoidung = ?";
            $bind_types .= "s";
            $bind_params[] = $logged_in_username;
        }

        $stmt_delete_comment = $conn->prepare("DELETE FROM comments WHERE " . $delete_condition);
        if ($stmt_delete_comment) {
            $stmt_delete_comment->bind_param($bind_types, ...$bind_params);
            $stmt_delete_comment->execute();
            $stmt_delete_comment->close();
        }
    }
    if ($post_id !== null) {
        header("Location: bai-viet.php?id=" . $post_id);
        exit();
    } else {
        header("Location: /Forum");
        exit();
    }
}

// Load comments
$comments = [];
if ($post_id !== null && isset($conn)) {
    $stmt_comments = $conn->prepare("SELECT c.id, c.nguoidung, c.traloi, c.created_at, c.admin, c.gender, c.image AS comment_head_id FROM comments c WHERE c.post_id = ? ORDER BY c.created_at ASC");
    if ($stmt_comments) {
        $stmt_comments->bind_param("i", $post_id);
        $stmt_comments->execute();
        $result_comments = $stmt_comments->get_result();
        while ($row = $result_comments->fetch_assoc()) {
            $comment_avatar_src = '/images/avatar/0.png';
            if ($row['admin'] == 1) {
                $comment_avatar_src = "/images/avatar/6101.gif";
            } else {
                if ($row['comment_head_id'] > 0) {
                    $comment_avatar_src = "/images/avatar/" . htmlspecialchars($row['comment_head_id']) . ".png";
                } else {
                    $comment_avatar_src = "/images/avatar/" . intval($row['gender']) . ".png";
                }
            }
            $row['calculated_avatar_path'] = $comment_avatar_src;
            $comments[] = $row;
        }
        $stmt_comments->close();
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'comment_success') {
    $_alert = "<div class='alert alert-success'>Bình luận của bạn đã được gửi thành công!</div>";
}
?>
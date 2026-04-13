<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'post_detail_logic.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,maximum-scale=1,user-scalable=no"/>
    <meta http-equiv="content-language" content="vi" />
    <title><?php echo $post_detail ? htmlspecialchars($post_detail['tieude']) : 'Bài viết không tồn tại'; ?> - Diễn Đàn</title>
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href='/images/favicon-48x48.ico' type="image/x-icon" />
    <script src="/view/static/js/disable_devtools.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=3.6">
    <style>
        .post-container {
            max-width: 900px;
            margin: 100px auto 40px auto;
            padding: 0 20px;
            min-height: 100vh;
        }
        .apple-post-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .post-header {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .author-info {
            flex-shrink: 0;
            text-align: center;
            width: 80px;
        }
        .author-info img {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .author-name {
            font-size: 13px;
            font-weight: 600;
            margin-top: 8px;
            color: var(--text-primary);
        }
        .author-role {
            font-size: 11px;
            color: #0071e3;
            font-weight: 600;
        }
        .post-body {
            flex-grow: 1;
        }
        .post-meta {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .post-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 15px 0;
            color: var(--text-primary);
            line-height: 1.3;
            letter-spacing: -0.01em;
        }
        .post-content-text {
            font-size: 15px;
            line-height: 1.6;
            color: var(--text-primary);
        }
        .post-content-text img {
            max-width: 100%;
            border-radius: 12px;
            margin-top: 15px;
        }
        
        .comment-item {
            display: flex;
            gap: 15px;
            padding: 20px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .comment-item:last-child {
            border-bottom: none;
        }
        .comment-author img {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            object-fit: cover;
        }
        .comment-body {
            flex-grow: 1;
        }
        .comment-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }
        .comment-text {
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin-top: 5px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 20px;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #0071e3;
        }
    </style>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>

    <nav class="apple-nav">
        <div class="nav-left">
            <a href="/"><img src="/images/logo_sk_he.png" alt="Logo" class="nav-logo"></a>
            <ul class="nav-links">
                <li><a href="/">Trang Chủ</a></li>
                <li><a href="/gioi-thieu">Giới Thiệu</a></li>
                <li><a href="/forum" class="active">Diễn Đàn</a></li>
                <li><a href="https://zalo.me/g/iktqgz458" target="_blank">Cộng Đồng</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="user-balance" style="margin-right: 15px; display: none;">Chào <b><?php echo htmlspecialchars($_SESSION['player_name'] ?? $_SESSION['username']); ?></b></span>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <a href="/admin" class="btn-pill small" style="background: #e53e3e; color: #fff; border: none; margin-right: 6px;">🛡️ Admin</a>
                <?php endif; ?>
                <a href="/app/logout" class="btn-pill btn-outline small">Đăng xuất</a>
            <?php else: ?>
                <a href="/login" class="btn-pill btn-outline small">Đăng Nhập</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="post-container">
        <a href="/forum" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Quay lại diễn đàn
        </a>

        <?php if ($post_detail): ?>
            <!-- Main Post Card -->
            <div class="apple-post-card">
                <div class="post-header">
                    <div class="author-info">
                        <img src="<?php echo htmlspecialchars($post_detail['author_avatar_path']); ?>" alt="Avatar" onerror="this.onerror=null;this.src='/images/avatar/0.png';">
                        <div class="author-name"><?php echo htmlspecialchars($post_detail['username']); ?></div>
                        <?php if($post_detail['author_is_admin'] == 1): ?>
                            <div class="author-role">Admin</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-body">
                        <div class="post-meta">
                            <span>Đăng lúc <?php echo htmlspecialchars($post_detail['created_at']); ?></span>
                        </div>
                        <h1 class="post-title"><?php echo htmlspecialchars($post_detail['tieude']); ?></h1>
                        <div class="post-content-text">
                            <?php echo nl2br(htmlspecialchars($post_detail['noidung'])); ?>
                            <?php if (!empty($post_detail['display_image_path'])): ?>
                                <br><img src='<?php echo htmlspecialchars($post_detail['display_image_path']); ?>' alt="Image">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="apple-post-card">
                <h3 style="margin-top:0; font-size:18px; margin-bottom:20px;">Bình luận</h3>
                
                <?php if ($_is_logged_in): ?>
                    <?php if ($can_comment): ?>
                    <form method="POST" action="bai-viet.php?id=<?php echo htmlspecialchars($post_id); ?>" style="margin-bottom: 30px;">
                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
                        <textarea class="apple-input" name="comment_content" rows="3" placeholder="Chia sẻ suy nghĩ của bạn..." style="width: 100%; border-radius: 12px; margin-bottom: 15px; resize: vertical;"></textarea>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size: 12px; color: var(--text-secondary); padding: 4px 12px; border-radius: 20px; background: <?php echo $rank_color; ?>15; color: <?php echo $rank_color; ?>; font-weight: 600;"><?php echo $rank_label; ?></span>
                            <button type="submit" name="submit_comment" class="btn-pill btn-black">Gửi bình luận</button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div style="background: rgba(239,68,68,0.05); padding: 16px 20px; border-radius: 12px; text-align: center; margin-bottom: 30px; border: 1px solid rgba(239,68,68,0.15);">
                        <span style="font-size: 14px; color: #d70015; font-weight: 500;">🔒 <?php echo $comment_blocked_reason; ?></span>
                        <?php if ($user_rank === 'none'): ?>
                        <br><a href="/nap-atm" style="color: #0071e3; font-weight: 600; text-decoration: none; font-size: 13px; margin-top: 6px; display: inline-block;">Nạp ngay để mở khóa →</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="background: rgba(0,113,227,0.05); padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 30px;">
                        Bạn cần <a href="/login" style="color: #0071e3; font-weight: 600; text-decoration: none;">Đăng nhập</a> để tham gia bình luận.
                    </div>
                <?php endif; ?>

                <div class="comments-list">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment):
                            $comment_avatar_src = '/images/avatar/0.png';
                            if ($comment['admin'] == 1) {
                                $comment_avatar_src = "/images/avatar/6101.gif";
                            } else {
                                if ($comment['comment_head_id'] > 0) {
                                    $comment_avatar_src = "/images/avatar/" . htmlspecialchars($comment['comment_head_id']) . ".png";
                                }
                            }
                        ?>
                            <div class="comment-item">
                                <div class="comment-author">
                                    <img src="<?php echo htmlspecialchars($comment_avatar_src); ?>" alt="Avatar" onerror="this.onerror=null;this.src='/images/avatar/0.png';">
                                </div>
                                <div class="comment-body">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div class="comment-name">
                                            <?php echo htmlspecialchars($comment['nguoidung']); ?>
                                            <?php if($comment['admin'] == 1): ?>
                                                <span style="color: #0071e3; font-size: 11px; margin-left: 5px;">(Admin)</span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-secondary);">
                                            <?php echo htmlspecialchars($comment['created_at']); ?>
                                            
                                            <?php if ($logged_in_username === $comment['nguoidung'] || $is_admin): ?>
                                                <a href="bai-viet.php?id=<?php echo htmlspecialchars($post_id); ?>&delete_comment_id=<?php echo htmlspecialchars($comment['id']); ?>" onclick="return confirm('Xác nhận xoá bình luận?');" style="color: #ff3b30; margin-left: 10px; text-decoration: none;">Xoá</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="comment-text">
                                        <?php echo nl2br(htmlspecialchars($comment['traloi'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: var(--text-secondary); padding: 20px 0;">
                            Chưa có bình luận nào. Hãy là người đầu tiên!
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            <div class="apple-post-card" style="text-align: center; padding: 50px 20px;">
                <h2 style="margin:0 0 10px 0;">Opps!</h2>
                <p style="color: var(--text-secondary);">Bài viết không tồn tại hoặc đã bị quản trị viên xoá.</p>
                <a href="/forum" class="btn-pill btn-black" style="margin-top: 20px;">Trở lại Diễn Đàn</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
</body>
</html>
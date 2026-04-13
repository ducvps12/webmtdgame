<?php
// admin/forum.php - Forum Management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../connect.php';

// Auth check
if (!isset($_SESSION['username'])) {
    header('Location: /login');
    exit;
}
$stmt = $conn->prepare("SELECT id, username, is_admin FROM account WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$admin_user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin_user || $admin_user['is_admin'] != 1) {
    header('Location: /');
    exit;
}

$current_page = 'forum';
$page_title = 'Quản lý Diễn đàn';
$admin_username = htmlspecialchars($admin_user['username']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $page_title; ?></title>
    <link rel="stylesheet" href="/admin/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/images/favicon-48x48.ico">
    <style>
        .forum-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 28px;
        }
        .forum-toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .forum-toolbar .search-bar {
            flex: 1;
            min-width: 200px;
        }
        .filter-select {
            padding: 9px 14px;
            border-radius: var(--radius-sm);
            background: rgba(15,23,42,0.03);
            border: 1px solid var(--border-primary);
            color: var(--text-primary);
            font-size: 13px;
            font-family: inherit;
            outline: none;
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        .filter-select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(88,117,245,0.1);
        }
        /* Table scroll wrapper */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .admin-table { min-width: 780px; }
        .post-title-cell {
            max-width: 300px;
            min-width: 180px;
        }
        .post-title-text {
            font-weight: 600;
            color: var(--text-primary);
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .post-title-preview {
            font-size: 12px;
            color: var(--text-muted);
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }
        .pin-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(245,158,11,0.12);
            color: var(--accent-orange);
            white-space: nowrap;
        }
        .action-btns {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }
        .action-btn {
            width: 32px; height: 32px;
            border-radius: var(--radius-xs);
            border: 1px solid var(--border-primary);
            background: rgba(15,23,42,0.02);
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all var(--transition-fast);
            flex-shrink: 0;
        }
        .action-btn:hover {
            background: rgba(15,23,42,0.06);
            color: var(--text-primary);
            border-color: var(--border-glow);
        }
        .action-btn.pin:hover { color: var(--accent-orange); border-color: var(--accent-orange); }
        .action-btn.unpin { color: var(--accent-orange); }
        .action-btn.delete:hover { color: var(--accent-red); border-color: var(--accent-red); }
        .action-btn.view:hover { color: var(--accent-blue); border-color: var(--accent-blue); }

        /* Comment modal styles */
        .comment-list-item {
            display: flex;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border-primary);
        }
        .comment-list-item:last-child { border-bottom: none; }
        .comment-avatar {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: var(--gradient-1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #fff;
            font-weight: 700;
            flex-shrink: 0;
        }
        .comment-info { flex: 1; min-width: 0; }
        .comment-author-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }
        .comment-time {
            font-size: 11px;
            color: var(--text-muted);
            margin-left: 8px;
        }
        .comment-text-content {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 4px;
            line-height: 1.5;
            word-break: break-word;
        }
        .comment-delete-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.15s;
            font-size: 14px;
        }
        .comment-delete-btn:hover {
            color: var(--accent-red);
            background: rgba(239,68,68,0.08);
        }
        .no-comments {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Post detail view in modal */
        .post-detail-content {
            background: rgba(15,23,42,0.02);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-sm);
            padding: 16px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.7;
            color: var(--text-secondary);
            max-height: 200px;
            overflow-y: auto;
            word-break: break-word;
        }

        /* Create post form */
        .create-form-grid {
            display: grid;
            gap: 16px;
        }
        .create-form-grid .form-group { margin-bottom: 0; }
        .create-form-grid .form-input,
        .create-form-grid .form-select,
        .create-form-grid textarea.form-input {
            width: 100%;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            background: rgba(15,23,42,0.03);
            border: 1px solid var(--border-primary);
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
            transition: all var(--transition-fast);
            outline: none;
        }
        .create-form-grid .form-input:focus,
        .create-form-grid textarea.form-input:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(88,117,245,0.12);
        }
        .create-form-grid textarea.form-input {
            resize: vertical;
            min-height: 120px;
        }
        .pin-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .pin-toggle input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: var(--accent-orange);
            cursor: pointer;
        }
        .pin-toggle label {
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
        }

        /* Mobile cards for small screens */
        .mobile-post-cards { display: none; }
        .mobile-post-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-sm);
            padding: 16px;
            margin-bottom: 12px;
            transition: all 0.2s;
        }
        .mobile-post-card:hover {
            border-color: var(--border-glow);
            box-shadow: var(--shadow-md);
        }
        .mpc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
        }
        .mpc-id {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
        }
        .mpc-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.4;
            word-break: break-word;
        }
        .mpc-preview {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .mpc-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0;
            font-size: 12px;
            color: var(--text-muted);
        }
        .mpc-meta span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .mpc-actions {
            display: flex;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px solid var(--border-primary);
        }
        .mpc-action-btn {
            flex: 1;
            padding: 8px;
            border-radius: var(--radius-xs);
            border: 1px solid var(--border-primary);
            background: rgba(15,23,42,0.02);
            color: var(--text-secondary);
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: all 0.15s;
        }
        .mpc-action-btn:hover { background: rgba(15,23,42,0.06); }
        .mpc-action-btn.view-btn:hover { color: var(--accent-blue); border-color: var(--accent-blue); }
        .mpc-action-btn.pin-btn:hover { color: var(--accent-orange); border-color: var(--accent-orange); }
        .mpc-action-btn.del-btn:hover { color: var(--accent-red); border-color: var(--accent-red); }
        .mpc-action-btn.pinned { color: var(--accent-orange); }

        /* Responsive */
        @media (max-width: 1200px) {
            .forum-stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .forum-stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-card { padding: 14px; }
            .stat-value { font-size: 22px; }
            .stat-icon { width: 36px; height: 36px; font-size: 16px; margin-bottom: 10px; }
            .forum-toolbar { flex-direction: column; align-items: stretch; }
            .forum-toolbar .search-bar { min-width: 100%; max-width: 100%; }
            .filter-select { width: 100%; }
            .desktop-table { display: none; }
            .mobile-post-cards { display: block; }
            .panel-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .topbar-right .btn { font-size: 12px; padding: 6px 10px; }
            .topbar-right .btn span.hide-mobile { display: none; }
            .modal-box { width: 95%; max-height: 90vh; }
        }
        @media (max-width: 480px) {
            .forum-stats-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
            .stat-card { padding: 12px; }
            .stat-value { font-size: 20px; }
            .stat-label { font-size: 10px; }
            .admin-content { padding: 16px 12px; }
            .mpc-actions { flex-wrap: wrap; }
            .mpc-action-btn { min-width: calc(50% - 4px); flex: unset; }
        }
    </style>
</head>
<body class="admin-body">

<!-- SIDEBAR -->
<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/admin" class="sidebar-logo">
            <div class="sidebar-logo-icon">⚡</div>
            <div class="sidebar-logo-text">
                Admin Panel
                <span>Quản trị hệ thống</span>
            </div>
        </a>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-title">Tổng quan</div>
        <a href="/admin" class="nav-item"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="/admin/operations" class="nav-item"><i class="bi bi-diagram-3-fill"></i> Quản trị mở rộng</a>
        <div class="nav-section-title">Quản lý</div>
        <a href="/admin/users" class="nav-item"><i class="bi bi-people-fill"></i> Người dùng</a>
        <a href="/admin/transactions" class="nav-item"><i class="bi bi-receipt"></i> Giao dịch Bank</a>
        <a href="/admin/feedback" class="nav-item"><i class="bi bi-envelope-fill"></i> Góp ý</a>
        <a href="/admin/forum" class="nav-item active"><i class="bi bi-chat-square-text-fill"></i> Diễn đàn</a>
        <div class="nav-section-title">Phân tích</div>
        <a href="/admin/analytics" class="nav-item"><i class="bi bi-bar-chart-line-fill"></i> Báo cáo & Thống kê</a>
        <a href="/admin/payment-flow" class="nav-item"><i class="bi bi-credit-card-2-front"></i> Cơ chế thanh toán</a>
        <div class="nav-section-title">Hệ thống</div>
        <a href="/admin/settings" class="nav-item"><i class="bi bi-gear-fill"></i> Cài đặt</a>
        <a href="/admin/security" class="nav-item"><i class="bi bi-shield-lock-fill"></i> Bảo mật & Nhật ký</a>
        <a href="/" class="nav-item"><i class="bi bi-box-arrow-left"></i> Về trang chủ</a>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><?php echo strtoupper(substr($admin_username, 0, 1)); ?></div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?php echo $admin_username; ?></div>
                <div class="sidebar-user-role">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<!-- MAIN -->
<main class="admin-main">
    <header class="admin-topbar">
        <div class="topbar-left">
            <button class="topbar-btn btn-menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <div class="topbar-title"><?php echo $page_title; ?></div>
                <div class="topbar-breadcrumb">Admin / <?php echo $page_title; ?></div>
            </div>
        </div>
        <div class="topbar-right">
            <a href="/forum" target="_blank" class="btn btn-ghost btn-sm">
                <i class="bi bi-box-arrow-up-right"></i> Xem diễn đàn
            </a>
            <div class="topbar-btn" onclick="loadForumData()" title="Làm mới">
                <i class="bi bi-arrow-clockwise"></i>
            </div>
        </div>
    </header>

    <div class="admin-content">
        <!-- Stats -->
        <div class="forum-stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="bi bi-file-text-fill"></i></div>
                <div class="stat-label">Tổng bài viết</div>
                <div class="stat-value" id="statTotalPosts">-</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon"><i class="bi bi-pin-angle-fill"></i></div>
                <div class="stat-label">Bài ghim</div>
                <div class="stat-value" id="statPinnedPosts">-</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="bi bi-chat-dots-fill"></i></div>
                <div class="stat-label">Tổng bình luận</div>
                <div class="stat-value" id="statTotalComments">-</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-icon"><i class="bi bi-calendar-plus-fill"></i></div>
                <div class="stat-label">Bài mới hôm nay</div>
                <div class="stat-value" id="statTodayPosts">-</div>
            </div>
        </div>

        <!-- Posts Panel -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="bi bi-journal-text" style="margin-right:8px;color:var(--accent-blue)"></i>
                    Danh sách bài viết
                </div>
                <button class="btn btn-primary btn-sm" onclick="openCreateModal()">
                    <i class="bi bi-plus-lg"></i> Thêm bài viết
                </button>
            </div>
            <div class="panel-body padded">
                <div class="forum-toolbar">
                    <div class="search-bar">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchInput" placeholder="Tìm kiếm bài viết..." oninput="debounceSearch()">
                    </div>
                    <select class="filter-select" id="filterPin" onchange="loadForumData()">
                        <option value="">Tất cả</option>
                        <option value="1">Đã ghim</option>
                        <option value="0">Chưa ghim</option>
                    </select>
                    <select class="filter-select" id="filterSort" onchange="loadForumData()">
                        <option value="newest">Mới nhất</option>
                        <option value="oldest">Cũ nhất</option>
                        <option value="most_comments">Nhiều bình luận</option>
                    </select>
                </div>
            </div>
            <div class="panel-body desktop-table">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bài viết</th>
                                <th>Tác giả</th>
                                <th>Trạng thái</th>
                                <th>Bình luận</th>
                                <th>Ngày đăng</th>
                                <th style="min-width:120px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="postsTableBody">
                            <tr><td colspan="7" class="empty-state" style="padding:40px"><div class="spinner"></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Mobile card view -->
            <div class="panel-body padded mobile-post-cards" id="mobilePostCards">
                <div style="text-align:center;padding:20px"><div class="spinner"></div></div>
            </div>
            <div class="pagination" id="postsPagination"></div>
        </div>
    </div>
</main>

<!-- View/Comments Modal -->
<div class="modal-overlay" id="commentsModal">
    <div class="modal-box" style="max-width:600px;">
        <div class="modal-header">
            <div class="modal-title" id="modalPostTitle">Chi tiết bài viết</div>
            <button class="modal-close" onclick="closeModal('commentsModal')">&times;</button>
        </div>
        <div class="modal-body" style="max-height:60vh;overflow-y:auto;">
            <div class="post-detail-content" id="modalPostContent"></div>
            <h4 style="font-size:14px;margin-bottom:12px;color:var(--text-primary)">
                <i class="bi bi-chat-left-text" style="margin-right:6px;color:var(--accent-blue)"></i>
                Bình luận (<span id="modalCommentCount">0</span>)
            </h4>
            <div id="modalCommentsList"></div>
        </div>
    </div>
</div>

<!-- Create Post Modal -->
<div class="modal-overlay" id="createPostModal">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title"><i class="bi bi-plus-circle" style="margin-right:8px;color:var(--accent-green)"></i>Tạo bài viết mới</div>
            <button class="modal-close" onclick="closeModal('createPostModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="create-form-grid">
                <div class="form-group">
                    <label class="form-label">Tiêu đề bài viết</label>
                    <input type="text" class="form-input" id="newPostTitle" placeholder="Nhập tiêu đề bài viết...">
                </div>
                <div class="form-group">
                    <label class="form-label">Nội dung</label>
                    <textarea class="form-input" id="newPostContent" rows="6" placeholder="Nhập nội dung bài viết..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Tên tác giả</label>
                    <input type="text" class="form-input" id="newPostAuthor" value="admin" placeholder="Tên hiển thị...">
                </div>
                <div class="pin-toggle">
                    <input type="checkbox" id="newPostPin">
                    <label for="newPostPin"><i class="bi bi-pin-angle" style="margin-right:4px"></i>Ghim bài viết lên đầu diễn đàn</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeModal('createPostModal')">Huỷ</button>
            <button class="btn btn-primary" id="btnCreatePost" onclick="createPost()">
                <i class="bi bi-send"></i> Đăng bài
            </button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<script>
let searchTimeout = null;
let currentPage = 1;

function showToast(msg, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

function formatTime(t) {
    if (!t) return '-';
    const d = new Date(t);
    if (isNaN(d.getTime())) return t;
    return d.toLocaleString('vi-VN', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'});
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1;
        loadForumData();
    }, 400);
}

function loadForumData() {
    const q = document.getElementById('searchInput').value.trim();
    const pin = document.getElementById('filterPin').value;
    const sort = document.getElementById('filterSort').value;

    let url = `/admin/api.php?action=get_forum_posts&page=${currentPage}&sort=${sort}`;
    if (q) url += `&q=${encodeURIComponent(q)}`;
    if (pin !== '') url += `&pin=${pin}`;

    fetch(url)
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') {
                showToast(res.message || 'Lỗi tải dữ liệu', 'error');
                return;
            }
            const d = res.data;

            // Stats
            document.getElementById('statTotalPosts').textContent = d.stats.total_posts;
            document.getElementById('statPinnedPosts').textContent = d.stats.pinned_posts;
            document.getElementById('statTotalComments').textContent = d.stats.total_comments;
            document.getElementById('statTodayPosts').textContent = d.stats.today_posts;

            // Desktop Table
            const tbody = document.getElementById('postsTableBody');
            const mobileContainer = document.getElementById('mobilePostCards');
            const emptyHtml = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3"></i>Không tìm thấy bài viết nào</td></tr>';
            const emptyMobileHtml = '<div style="text-align:center;color:var(--text-muted);padding:30px"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3"></i>Không tìm thấy bài viết nào</div>';

            if (d.posts.length === 0) {
                tbody.innerHTML = emptyHtml;
                mobileContainer.innerHTML = emptyMobileHtml;
            } else {
                // Desktop rows
                tbody.innerHTML = d.posts.map(p => `
                    <tr>
                        <td style="font-weight:600;color:var(--text-muted)">#${p.id}</td>
                        <td class="post-title-cell">
                            <span class="post-title-text">${escHtml(p.tieude)}</span>
                            <span class="post-title-preview">${escHtml((p.noidung || '').substring(0, 80))}${(p.noidung || '').length > 80 ? '...' : ''}</span>
                        </td>
                        <td><span class="text-username">${escHtml(p.username)}</span></td>
                        <td>${p.ghimbai == 1 ? '<span class="pin-badge"><i class="bi bi-pin-fill"></i> Đã ghim</span>' : '<span class="badge badge-muted">Thường</span>'}</td>
                        <td><span class="badge badge-info">${p.comment_count || 0}</span></td>
                        <td style="white-space:nowrap">${formatTime(p.created_at)}</td>
                        <td>
                            <div class="action-btns">
                                <button class="action-btn view" title="Xem chi tiết" onclick="viewPost(${p.id})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn ${p.ghimbai == 1 ? 'unpin' : 'pin'}" title="${p.ghimbai == 1 ? 'Bỏ ghim' : 'Ghim bài'}" onclick="togglePin(${p.id}, ${p.ghimbai})">
                                    <i class="bi bi-pin${p.ghimbai == 1 ? '-fill' : ''}"></i>
                                </button>
                                <button class="action-btn delete" title="Xoá bài viết" onclick="deletePost(${p.id}, '${escHtml(p.tieude).replace(/'/g, "\\'")}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');

                // Mobile cards
                mobileContainer.innerHTML = d.posts.map(p => `
                    <div class="mobile-post-card">
                        <div class="mpc-header">
                            <div>
                                <div class="mpc-id">#${p.id}</div>
                                <div class="mpc-title">${escHtml(p.tieude)}</div>
                                <div class="mpc-preview">${escHtml((p.noidung || '').substring(0, 100))}</div>
                            </div>
                            ${p.ghimbai == 1 ? '<span class="pin-badge"><i class="bi bi-pin-fill"></i> Ghim</span>' : ''}
                        </div>
                        <div class="mpc-meta">
                            <span><i class="bi bi-person"></i> ${escHtml(p.username)}</span>
                            <span><i class="bi bi-chat-dots"></i> ${p.comment_count || 0}</span>
                            <span><i class="bi bi-clock"></i> ${formatTime(p.created_at)}</span>
                        </div>
                        <div class="mpc-actions">
                            <button class="mpc-action-btn view-btn" onclick="viewPost(${p.id})"><i class="bi bi-eye"></i> Xem</button>
                            <button class="mpc-action-btn pin-btn ${p.ghimbai == 1 ? 'pinned' : ''}" onclick="togglePin(${p.id}, ${p.ghimbai})"><i class="bi bi-pin${p.ghimbai == 1 ? '-fill' : ''}"></i> ${p.ghimbai == 1 ? 'Bỏ ghim' : 'Ghim'}</button>
                            <button class="mpc-action-btn del-btn" onclick="deletePost(${p.id}, '${escHtml(p.tieude).replace(/'/g, "\\'")}')" ><i class="bi bi-trash"></i> Xoá</button>
                        </div>
                    </div>
                `).join('');
            }

            // Pagination
            renderPagination(d.page, d.pages);
        })
        .catch(e => {
            console.error(e);
            showToast('Lỗi kết nối: ' + e.message, 'error');
        });
}

function renderPagination(page, total) {
    const container = document.getElementById('postsPagination');
    if (total <= 1) { container.innerHTML = ''; return; }

    let html = '';
    if (page > 1) html += `<button class="page-btn" onclick="goPage(${page-1})">‹</button>`;
    
    const start = Math.max(1, page - 2);
    const end = Math.min(total, page + 2);
    
    if (start > 1) {
        html += `<button class="page-btn" onclick="goPage(1)">1</button>`;
        if (start > 2) html += `<span style="color:var(--text-muted);padding:0 4px">...</span>`;
    }
    
    for (let i = start; i <= end; i++) {
        html += `<button class="page-btn ${i === page ? 'active' : ''}" onclick="goPage(${i})">${i}</button>`;
    }
    
    if (end < total) {
        if (end < total - 1) html += `<span style="color:var(--text-muted);padding:0 4px">...</span>`;
        html += `<button class="page-btn" onclick="goPage(${total})">${total}</button>`;
    }
    
    if (page < total) html += `<button class="page-btn" onclick="goPage(${page+1})">›</button>`;

    container.innerHTML = html;
}

function goPage(p) {
    currentPage = p;
    loadForumData();
}

function togglePin(postId, current) {
    const newVal = current == 1 ? 0 : 1;
    const fd = new FormData();
    fd.append('action', 'forum_toggle_pin');
    fd.append('post_id', postId);
    fd.append('pin', newVal);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message, res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') loadForumData();
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function deletePost(postId, title) {
    if (!confirm(`Bạn chắc chắn muốn xoá bài viết:\n"${title}"?\n\nTất cả bình luận cũng sẽ bị xoá.`)) return;

    const fd = new FormData();
    fd.append('action', 'forum_delete_post');
    fd.append('post_id', postId);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message, res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') loadForumData();
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function viewPost(postId) {
    fetch(`/admin/api.php?action=get_forum_post_detail&post_id=${postId}`)
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') {
                showToast(res.message || 'Không thể tải bài viết', 'error');
                return;
            }
            const p = res.data;
            document.getElementById('modalPostTitle').textContent = p.tieude;
            document.getElementById('modalPostContent').innerHTML = escHtml(p.noidung).replace(/\n/g, '<br>');
            document.getElementById('modalCommentCount').textContent = p.comments.length;

            if (p.comments.length === 0) {
                document.getElementById('modalCommentsList').innerHTML = '<div class="no-comments"><i class="bi bi-chat" style="font-size:24px;display:block;margin-bottom:8px;opacity:0.3"></i>Chưa có bình luận nào</div>';
            } else {
                document.getElementById('modalCommentsList').innerHTML = p.comments.map(c => `
                    <div class="comment-list-item" id="comment-${c.id}">
                        <div class="comment-avatar">${(c.nguoidung || '?').charAt(0).toUpperCase()}</div>
                        <div class="comment-info">
                            <div>
                                <span class="comment-author-name">${escHtml(c.nguoidung)}</span>
                                <span class="comment-time">${formatTime(c.created_at)}</span>
                            </div>
                            <div class="comment-text-content">${escHtml(c.traloi).replace(/\n/g, '<br>')}</div>
                        </div>
                        <button class="comment-delete-btn" onclick="deleteComment(${c.id}, ${postId})" title="Xoá bình luận">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `).join('');
            }

            document.getElementById('commentsModal').classList.add('show');
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function deleteComment(commentId, postId) {
    if (!confirm('Xác nhận xoá bình luận này?')) return;

    const fd = new FormData();
    fd.append('action', 'forum_delete_comment');
    fd.append('comment_id', commentId);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message, res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') {
                const el = document.getElementById('comment-' + commentId);
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(20px)';
                    el.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        el.remove();
                        const cnt = document.getElementById('modalCommentCount');
                        cnt.textContent = parseInt(cnt.textContent) - 1;
                    }, 300);
                }
                loadForumData();
            }
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function escHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Create post
function openCreateModal() {
    document.getElementById('newPostTitle').value = '';
    document.getElementById('newPostContent').value = '';
    document.getElementById('newPostAuthor').value = 'admin';
    document.getElementById('newPostPin').checked = false;
    document.getElementById('createPostModal').classList.add('show');
    setTimeout(() => document.getElementById('newPostTitle').focus(), 300);
}

function createPost() {
    const title = document.getElementById('newPostTitle').value.trim();
    const content = document.getElementById('newPostContent').value.trim();
    const author = document.getElementById('newPostAuthor').value.trim();
    const pin = document.getElementById('newPostPin').checked ? 1 : 0;

    if (!title || title.length < 3) {
        showToast('Tiêu đề phải có ít nhất 3 ký tự', 'error');
        return;
    }
    if (!content || content.length < 5) {
        showToast('Nội dung phải có ít nhất 5 ký tự', 'error');
        return;
    }

    const btn = document.getElementById('btnCreatePost');
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner" style="width:16px;height:16px;border-width:2px"></div> Đang đăng...';

    const fd = new FormData();
    fd.append('action', 'forum_create_post');
    fd.append('title', title);
    fd.append('content', content);
    fd.append('author', author || 'admin');
    fd.append('pin', pin);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                closeModal('createPostModal');
                loadForumData();
            } else {
                showToast(res.message || 'Lỗi tạo bài viết', 'error');
            }
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send"></i> Đăng bài';
        });
}

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});

// Load on ready
document.addEventListener('DOMContentLoaded', () => {
    loadForumData();
});
</script>

</body>
</html>

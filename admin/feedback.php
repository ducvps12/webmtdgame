<?php
// admin/feedback.php - Admin Feedback Management
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

$current_page = 'feedback';
$page_title = 'Gop Y & De Xuat';
$admin_username = htmlspecialchars($admin_user['username']);

// Count new feedback for badge
$new_count = 0;
$count_result = $conn->query("SELECT COUNT(*) as cnt FROM feedback WHERE status = 'new'");
if ($count_result) {
    $new_count = $count_result->fetch_assoc()['cnt'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo $page_title; ?></title>
    <link rel="stylesheet" href="/admin/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/images/favicon-48x48.ico">
    <style>
    .filter-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .filter-tab {
        padding: 7px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--border-color);
        background: rgba(255,255,255,0.04);
        color: var(--text-secondary);
        transition: all 0.2s;
        font-family: inherit;
    }
    .filter-tab:hover {
        background: rgba(255,255,255,0.08);
        color: var(--text-primary);
    }
    .filter-tab.active {
        background: var(--accent-blue);
        color: #fff;
        border-color: var(--accent-blue);
    }
    .fb-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 12px;
        transition: all 0.2s;
    }
    .fb-card:hover {
        background: rgba(255,255,255,0.06);
        border-color: rgba(79,110,247,0.3);
    }
    .fb-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }
    .fb-card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .fb-card-meta {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }
    .fb-card-body {
        font-size: 14px;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 12px;
        white-space: pre-wrap;
    }
    .fb-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .fb-card-info {
        display: flex;
        gap: 16px;
        font-size: 12px;
        color: var(--text-muted);
    }
    .fb-card-actions {
        display: flex;
        gap: 8px;
    }
    .reply-box {
        margin-top: 12px;
        padding: 14px;
        background: rgba(16,185,129,0.06);
        border-radius: var(--radius-sm);
        border-left: 3px solid var(--accent-green);
    }
    .reply-box strong {
        font-size: 12px;
        color: var(--accent-green);
        display: block;
        margin-bottom: 4px;
    }
    .reply-box p {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.5;
    }
    .reply-textarea {
        width: 100%;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        resize: vertical;
        min-height: 80px;
        outline: none;
        transition: all 0.2s;
        margin-top: 10px;
    }
    .reply-textarea:focus {
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 3px rgba(79,110,247,0.15);
    }
    .reply-form {
        margin-top: 10px;
        display: none;
    }
    .reply-form.show { display: block; }
    .reply-form-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
        justify-content: flex-end;
    }
    </style>
</head>
<body class="admin-body">

<!-- SIDEBAR -->
<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/admin" class="sidebar-logo">
            <div class="sidebar-logo-icon">&#9889;</div>
            <div class="sidebar-logo-text">
                Admin Panel
                <span>Quan tri he thong</span>
            </div>
        </a>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-title">Tong quan</div>
        <a href="/admin" class="nav-item"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="/admin/operations" class="nav-item"><i class="bi bi-diagram-3-fill"></i> Quản trị mở rộng</a>
        <div class="nav-section-title">Quan ly</div>
        <a href="/admin/users" class="nav-item"><i class="bi bi-people-fill"></i> Nguoi dung</a>
        <a href="/admin/transactions" class="nav-item"><i class="bi bi-receipt"></i> Giao dich Bank</a>
        <a href="/admin/feedback" class="nav-item active">
            <i class="bi bi-envelope-fill"></i> Gop y
            <?php if ($new_count > 0): ?>
                <span class="nav-badge"><?php echo $new_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="/admin/forum" class="nav-item"><i class="bi bi-chat-square-text-fill"></i> Dien dan</a>
        <div class="nav-section-title">Phan tich</div>
        <a href="/admin/analytics" class="nav-item"><i class="bi bi-bar-chart-line-fill"></i> Bao cao & Thong ke</a>
        <a href="/admin/payment-flow" class="nav-item"><i class="bi bi-credit-card-2-front"></i> Co che thanh toan</a>
        <div class="nav-section-title">He thong</div>
        <a href="/admin/settings" class="nav-item"><i class="bi bi-gear-fill"></i> Cai dat</a>
        <a href="/admin/security" class="nav-item"><i class="bi bi-shield-lock-fill"></i> Bao mat & Nhat ky</a>
        <a href="/" class="nav-item"><i class="bi bi-box-arrow-left"></i> Ve trang chu</a>
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
            <div class="topbar-btn" onclick="loadFeedback()" title="Lam moi">
                <i class="bi bi-arrow-clockwise"></i>
            </div>
        </div>
    </header>

    <div class="admin-content">
        <!-- Stats -->
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="bi bi-envelope-fill"></i></div>
                <div class="stat-label">Tong gop y</div>
                <div class="stat-value" id="statTotal">-</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon"><i class="bi bi-exclamation-circle-fill"></i></div>
                <div class="stat-label">Chua doc</div>
                <div class="stat-value" id="statNew">-</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-label">Da phan hoi</div>
                <div class="stat-value" id="statReplied">-</div>
            </div>
        </div>

        <!-- Filter & List -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-funnel" style="margin-right:8px;color:var(--accent-blue)"></i>Loc theo trang thai</div>
                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="setFilter('')" data-filter="">Tat ca</button>
                    <button class="filter-tab" onclick="setFilter('new')" data-filter="new">Moi</button>
                    <button class="filter-tab" onclick="setFilter('read')" data-filter="read">Da xem</button>
                    <button class="filter-tab" onclick="setFilter('replied')" data-filter="replied">Da phan hoi</button>
                </div>
            </div>
            <div class="panel-body padded" id="feedbackList">
                <div class="empty-state"><div class="spinner"></div><p style="margin-top:12px">Dang tai...</p></div>
            </div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>
</main>

<!-- Reply Modal -->
<div class="modal-overlay" id="replyModal">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title">Phan hoi gop y</div>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom:16px;">
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:4px;">Tieu de:</div>
                <div style="font-size:15px;font-weight:600;color:var(--text-primary);" id="modalTitle"></div>
            </div>
            <div style="margin-bottom:16px;">
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:4px;">Noi dung:</div>
                <div style="font-size:14px;color:var(--text-secondary);line-height:1.6;white-space:pre-wrap;" id="modalContent"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Noi dung phan hoi</label>
                <textarea class="form-input" id="replyText" rows="4" placeholder="Nhap phan hoi cho nguoi dung..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeModal()">Huy</button>
            <button class="btn btn-success" id="btnSendReply" onclick="sendReply()">
                <i class="bi bi-send"></i> Gui phan hoi
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast-container" id="toastContainer"></div>

<script>
var currentFilter = '';
var currentPage = 1;
var replyFeedbackId = null;

function showToast(msg, type) {
    var container = document.getElementById('toastContainer');
    var toast = document.createElement('div');
    toast.className = 'toast ' + (type || 'info');
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(function() { toast.remove(); }, 4000);
}

function setFilter(filter) {
    currentFilter = filter;
    currentPage = 1;
    document.querySelectorAll('.filter-tab').forEach(function(tab) {
        tab.classList.toggle('active', tab.getAttribute('data-filter') === filter);
    });
    loadFeedback();
}

function categoryBadge(cat) {
    switch(cat) {
        case 'bug': return '<span class="badge badge-danger">Bug</span>';
        case 'suggestion': return '<span class="badge badge-info">De xuat</span>';
        default: return '<span class="badge badge-muted">Khac</span>';
    }
}
function statusBadge(s) {
    switch(s) {
        case 'new': return '<span class="badge badge-info">Moi</span>';
        case 'read': return '<span class="badge badge-warning">Da xem</span>';
        case 'replied': return '<span class="badge badge-success">Da phan hoi</span>';
        default: return '';
    }
}
function formatTime(t) {
    if (!t) return '-';
    var d = new Date(t);
    return d.toLocaleDateString('vi-VN') + ' ' + d.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'});
}
function escapeHtml(text) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text || ''));
    return div.innerHTML;
}

function loadFeedback() {
    var url = '/api/feedback.php?admin=1&page=' + currentPage;
    if (currentFilter) url += '&status=' + currentFilter;

    fetch(url)
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (!res.success) {
            document.getElementById('feedbackList').innerHTML = '<div class="empty-state"><p>' + (res.message || 'Loi') + '</p></div>';
            return;
        }

        // Update stats
        loadStats();

        var data = res.data;
        var container = document.getElementById('feedbackList');

        if (data.length === 0) {
            container.innerHTML = '<div class="empty-state"><i class="bi bi-inbox"></i><p>Khong co gop y nao.</p></div>';
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        var html = '';
        data.forEach(function(fb) {
            html += '<div class="fb-card" id="fb-' + fb.id + '">';
            html += '  <div class="fb-card-header">';
            html += '    <h4 class="fb-card-title">' + escapeHtml(fb.title) + '</h4>';
            html += '    <div class="fb-card-meta">' + categoryBadge(fb.category) + statusBadge(fb.status) + '</div>';
            html += '  </div>';
            html += '  <div class="fb-card-body">' + escapeHtml(fb.content) + '</div>';
            html += '  <div class="fb-card-footer">';
            html += '    <div class="fb-card-info">';
            html += '      <span><i class="bi bi-person"></i> ' + escapeHtml(fb.player_name || fb.username) + '</span>';
            html += '      <span><i class="bi bi-clock"></i> ' + formatTime(fb.created_at) + '</span>';
            html += '    </div>';
            html += '    <div class="fb-card-actions">';
            if (fb.status === 'new') {
                html += '      <button class="btn btn-sm btn-ghost" onclick="markRead(' + fb.id + ')"><i class="bi bi-eye"></i> Da xem</button>';
            }
            if (fb.status !== 'replied') {
                html += '      <button class="btn btn-sm btn-primary" onclick="openReply(' + fb.id + ',\'' + escapeHtml(fb.title).replace(/'/g, "\\'") + '\',\'' + escapeHtml(fb.content).replace(/'/g, "\\'").replace(/\n/g, "\\n") + '\')"><i class="bi bi-reply"></i> Phan hoi</button>';
            }
            html += '      <button class="btn btn-sm btn-danger" onclick="deleteFeedback(' + fb.id + ')"><i class="bi bi-trash"></i></button>';
            html += '    </div>';
            html += '  </div>';
            if (fb.admin_reply) {
                html += '  <div class="reply-box">';
                html += '    <strong>Phan hoi Admin:</strong>';
                html += '    <p>' + escapeHtml(fb.admin_reply) + '</p>';
                html += '  </div>';
            }
            html += '</div>';
        });
        container.innerHTML = html;

        // Pagination
        var pagHtml = '';
        for (var i = 1; i <= res.pages; i++) {
            pagHtml += '<button class="page-btn ' + (i === res.page ? 'active' : '') + '" onclick="goPage(' + i + ')">' + i + '</button>';
        }
        document.getElementById('pagination').innerHTML = pagHtml;
    })
    .catch(function(e) {
        document.getElementById('feedbackList').innerHTML = '<div class="empty-state"><p>Loi tai du lieu</p></div>';
    });
}

function loadStats() {
    fetch('/api/feedback.php?admin=1&status=')
    .then(function(r) { return r.json(); })
    .then(function(res) {
        document.getElementById('statTotal').textContent = res.total || 0;
    });
    fetch('/api/feedback.php?admin=1&status=new')
    .then(function(r) { return r.json(); })
    .then(function(res) {
        document.getElementById('statNew').textContent = res.total || 0;
    });
    fetch('/api/feedback.php?admin=1&status=replied')
    .then(function(r) { return r.json(); })
    .then(function(res) {
        document.getElementById('statReplied').textContent = res.total || 0;
    });
}

function goPage(p) {
    currentPage = p;
    loadFeedback();
}

function markRead(id) {
    fetch('/api/feedback.php', {
        method: 'PUT',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: id, action: 'mark_read'})
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        showToast(res.message, res.success ? 'success' : 'error');
        if (res.success) loadFeedback();
    });
}

function openReply(id, title, content) {
    replyFeedbackId = id;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalContent').textContent = content;
    document.getElementById('replyText').value = '';
    document.getElementById('replyModal').classList.add('show');
}

function closeModal() {
    document.getElementById('replyModal').classList.remove('show');
    replyFeedbackId = null;
}

function sendReply() {
    var reply = document.getElementById('replyText').value.trim();
    if (!reply) {
        showToast('Vui long nhap noi dung phan hoi', 'error');
        return;
    }
    var btn = document.getElementById('btnSendReply');
    btn.disabled = true;

    fetch('/api/feedback.php', {
        method: 'PUT',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: replyFeedbackId, action: 'reply', reply: reply})
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        showToast(res.message, res.success ? 'success' : 'error');
        if (res.success) {
            closeModal();
            loadFeedback();
        }
    })
    .finally(function() {
        btn.disabled = false;
    });
}

function deleteFeedback(id) {
    if (!confirm('Ban co chac muon xoa gop y nay?')) return;
    fetch('/api/feedback.php', {
        method: 'DELETE',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: id})
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        showToast(res.message, res.success ? 'success' : 'error');
        if (res.success) loadFeedback();
    });
}

// Load on ready
document.addEventListener('DOMContentLoaded', function() {
    loadFeedback();
});
</script>

</body>
</html>



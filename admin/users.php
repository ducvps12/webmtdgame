<?php
// admin/users.php - User Management (Enhanced)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../connect.php';

if (!isset($_SESSION['username'])) { header('Location: /login'); exit; }
$stmt = $conn->prepare("SELECT id, username, is_admin FROM account WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$admin_user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$admin_user || $admin_user['is_admin'] != 1) { header('Location: /'); exit; }

$current_page = 'users';
$page_title = 'Quản lý người dùng';
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
        .user-actions { display:flex; gap:4px; }
        .user-actions button { padding:5px 8px; border-radius:6px; border:1px solid var(--border-primary); background:var(--bg-glass); cursor:pointer; font-size:11px; color:var(--text-muted); transition:all .15s; font-family:inherit; display:flex; align-items:center; gap:3px; }
        .user-actions button:hover { transform:translateY(-1px); }
        .user-actions .act-edit:hover { color:var(--accent-blue); border-color:var(--accent-blue); }
        .user-actions .act-ban:hover { color:var(--accent-red); border-color:var(--accent-red); }
        .user-actions .act-reset:hover { color:var(--accent-orange); border-color:var(--accent-orange); }
        .modal-box.wide { max-width:600px; }
        .edit-tabs { display:flex; gap:2px; border-bottom:1px solid var(--border-primary); margin-bottom:20px; }
        .edit-tab { padding:10px 16px; font-size:13px; font-weight:500; color:var(--text-muted); cursor:pointer; border:none; background:none; font-family:inherit; border-bottom:2px solid transparent; transition:all .2s; }
        .edit-tab.active { color:var(--accent-blue); border-bottom-color:var(--accent-blue); font-weight:600; }
        .edit-panel { display:none; }
        .edit-panel.active { display:block; }
        .user-detail-row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border-primary); font-size:13px; }
        .user-detail-row:last-child { border-bottom:none; }
        .user-detail-row .udr-label { color:var(--text-muted); font-weight:600; }
        .user-detail-row .udr-value { color:var(--text-primary); font-weight:500; }
    </style>
</head>
<body class="admin-body">

<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/admin" class="sidebar-logo">
            <div class="sidebar-logo-icon">⚡</div>
            <div class="sidebar-logo-text">Admin Panel<span>Quản trị hệ thống</span></div>
        </a>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-title">Tổng quan</div>
        <a href="/admin" class="nav-item"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="/admin/operations" class="nav-item"><i class="bi bi-diagram-3-fill"></i> Quản trị mở rộng</a>
        <div class="nav-section-title">Quản lý</div>
        <a href="/admin/users" class="nav-item active"><i class="bi bi-people-fill"></i> Người dùng</a>
        <a href="/admin/transactions" class="nav-item"><i class="bi bi-receipt"></i> Giao dịch Bank</a>
        <a href="/admin/feedback" class="nav-item"><i class="bi bi-envelope-fill"></i> Góp ý</a>
        <a href="/admin/forum" class="nav-item"><i class="bi bi-chat-square-text-fill"></i> Diễn đàn</a>
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

<main class="admin-main">
    <header class="admin-topbar">
        <div class="topbar-left">
            <button class="topbar-btn btn-menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
            <div>
                <div class="topbar-title"><?php echo $page_title; ?></div>
                <div class="topbar-breadcrumb">Admin / Người dùng</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Tìm username hoặc tên nhân vật..." onkeyup="debounceSearch()">
            </div>
        </div>
    </header>

    <div class="admin-content">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-people-fill" style="margin-right:8px;color:var(--accent-blue)"></i>Danh sách tài khoản</div>
                <div id="userCount" style="color:var(--text-muted);font-size:13px"></div>
            </div>
            <div class="panel-body">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Nhân vật</th>
                            <th>Số dư VND</th>
                            <th>Tổng nạp</th>
                            <th>Trạng thái</th>
                            <th>Đăng nhập cuối</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable">
                        <tr><td colspan="8" style="text-align:center;padding:40px"><div class="spinner"></div></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>
</main>

<!-- Modal: Edit User (Enhanced) -->
<div class="modal-overlay" id="editModal">
    <div class="modal-box wide">
        <div class="modal-header">
            <div class="modal-title" id="editModalTitle">Chỉnh sửa người dùng</div>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="editUserId">

            <div class="edit-tabs">
                <button class="edit-tab active" onclick="switchEditTab('info',this)"><i class="bi bi-person"></i> Thông tin</button>
                <button class="edit-tab" onclick="switchEditTab('finance',this)"><i class="bi bi-wallet2"></i> Tài chính</button>
                <button class="edit-tab" onclick="switchEditTab('access',this)"><i class="bi bi-shield-lock"></i> Quyền & Bảo mật</button>
            </div>

            <!-- Tab: Info -->
            <div id="editTab-info" class="edit-panel active">
                <div class="user-detail-row">
                    <span class="udr-label">User ID</span>
                    <span class="udr-value" id="detailId">-</span>
                </div>
                <div class="user-detail-row">
                    <span class="udr-label">Username</span>
                    <span class="udr-value" id="detailUsername">-</span>
                </div>
                <div class="user-detail-row">
                    <span class="udr-label">Nhân vật</span>
                    <span class="udr-value" id="detailPlayer">-</span>
                </div>
                <div class="user-detail-row">
                    <span class="udr-label">IP Address</span>
                    <span class="udr-value" id="detailIP">-</span>
                </div>
                <div class="user-detail-row">
                    <span class="udr-label">Ngày tạo</span>
                    <span class="udr-value" id="detailCreated">-</span>
                </div>
                <div class="user-detail-row">
                    <span class="udr-label">Đăng nhập cuối</span>
                    <span class="udr-value" id="detailLastLogin">-</span>
                </div>
            </div>

            <!-- Tab: Finance -->
            <div id="editTab-finance" class="edit-panel">
                <div class="form-group">
                    <label class="form-label">Số dư VND</label>
                    <input type="number" class="form-input" id="editVnd">
                </div>
                <div class="form-group">
                    <label class="form-label">Tổng nạp</label>
                    <input type="number" class="form-input" id="editTongnap">
                </div>
                <hr style="border-color:var(--border-primary);margin:20px 0">
                <div class="form-group">
                    <label class="form-label">Cộng thêm VND (nhập số tiền)</label>
                    <div style="display:flex;gap:8px">
                        <input type="number" class="form-input" id="creditAmount" placeholder="Ví dụ: 50000" min="1">
                        <button class="btn btn-success btn-sm" onclick="manualCredit()" style="white-space:nowrap">Cộng VND</button>
                    </div>
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:8px">
                    <button class="btn btn-sm btn-ghost" onclick="document.getElementById('creditAmount').value='10000'">10K</button>
                    <button class="btn btn-sm btn-ghost" onclick="document.getElementById('creditAmount').value='50000'">50K</button>
                    <button class="btn btn-sm btn-ghost" onclick="document.getElementById('creditAmount').value='100000'">100K</button>
                    <button class="btn btn-sm btn-ghost" onclick="document.getElementById('creditAmount').value='500000'">500K</button>
                    <button class="btn btn-sm btn-ghost" onclick="document.getElementById('creditAmount').value='1000000'">1M</button>
                </div>
            </div>

            <!-- Tab: Access -->
            <div id="editTab-access" class="edit-panel">
                <div class="form-group">
                    <label class="form-label">Quyền Admin</label>
                    <select class="form-input" id="editAdmin">
                        <option value="0">Người dùng thường</option>
                        <option value="1">Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Trạng thái Ban</label>
                    <select class="form-input" id="editBan">
                        <option value="0">Hoạt động bình thường</option>
                        <option value="1">Bị cấm (Banned)</option>
                    </select>
                </div>
                <hr style="border-color:var(--border-primary);margin:20px 0">
                <div class="form-group">
                    <label class="form-label">Reset mật khẩu</label>
                    <div style="display:flex;gap:8px;align-items:center">
                        <button class="btn btn-danger btn-sm" onclick="resetPassword()"><i class="bi bi-key"></i> Reset về abc123</button>
                        <span style="font-size:12px;color:var(--text-muted)">Mật khẩu mới: <code>abc123</code></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeModal()">Hủy</button>
            <button class="btn btn-primary" onclick="saveUser()">Lưu thay đổi</button>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
let currentPage = 1;
let searchTimeout;
let currentEditUser = null;

function formatMoney(n) { return new Intl.NumberFormat('vi-VN').format(n); }
function formatTime(t) {
    if (!t || t === '2002-07-31 00:00:00') return '-';
    return new Date(t).toLocaleString('vi-VN', {day:'2-digit',month:'2-digit',year:'2-digit',hour:'2-digit',minute:'2-digit'});
}
function esc(v) { const d = document.createElement('div'); d.appendChild(document.createTextNode(v == null ? '' : String(v))); return d.innerHTML; }

function showToast(msg, type='info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

function switchEditTab(tab, btn) {
    document.querySelectorAll('.edit-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.edit-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('editTab-' + tab).classList.add('active');
    btn.classList.add('active');
}

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => { currentPage = 1; loadUsers(); }, 400);
}

function loadUsers() {
    const q = document.getElementById('searchInput').value;
    fetch(`/admin/api.php?action=search_users&q=${encodeURIComponent(q)}&page=${currentPage}`)
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') return;

            document.getElementById('userCount').textContent = `Tổng: ${res.total} tài khoản`;

            const tbody = document.getElementById('usersTable');
            if (res.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:40px">Không tìm thấy</td></tr>';
            } else {
                tbody.innerHTML = res.data.map(u => `
                    <tr>
                        <td class="text-white">#${u.id}</td>
                        <td><span class="text-username">${esc(u.username)}</span>${u.is_admin == 1 ? ' <span class="badge badge-danger">Admin</span>' : ''}</td>
                        <td>${u.player_name || '<span style="color:var(--text-muted)">-</span>'}</td>
                        <td><span class="text-money">${formatMoney(u.vnd)}đ</span></td>
                        <td>${formatMoney(u.tongnap)}đ</td>
                        <td>${u.ban == 1 ? '<span class="badge badge-danger">Bị cấm</span>' : u.active == 1 ? '<span class="badge badge-success">Hoạt động</span>' : '<span class="badge badge-warning">Chưa kích hoạt</span>'}</td>
                        <td>${formatTime(u.last_time_login)}</td>
                        <td>
                            <div class="user-actions">
                                <button class="act-edit" title="Sửa" onclick='openEdit(${JSON.stringify(u)})'><i class="bi bi-pencil-square"></i></button>
                                <button class="act-ban" title="${u.ban == 1 ? 'Unban' : 'Ban'}" onclick="quickBan(${u.id}, ${u.ban == 1 ? 0 : 1})"><i class="bi bi-${u.ban == 1 ? 'unlock' : 'ban'}"></i></button>
                                <button class="act-reset" title="Reset mật khẩu" onclick="quickReset(${u.id}, '${esc(u.username)}')"><i class="bi bi-key"></i></button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }

            // Pagination
            const pagDiv = document.getElementById('pagination');
            if (res.pages <= 1) { pagDiv.innerHTML = ''; return; }
            let html = '';
            if (currentPage > 1) html += `<button class="page-btn" onclick="currentPage=1;loadUsers()">«</button>`;
            for (let i = Math.max(1, res.page - 3); i <= Math.min(res.pages, res.page + 3); i++) {
                html += `<button class="page-btn ${i === res.page ? 'active' : ''}" onclick="currentPage=${i};loadUsers()">${i}</button>`;
            }
            if (currentPage < res.pages) html += `<button class="page-btn" onclick="currentPage=${res.pages};loadUsers()">»</button>`;
            pagDiv.innerHTML = html;
        });
}

function openEdit(u) {
    currentEditUser = u;
    document.getElementById('editUserId').value = u.id;
    document.getElementById('editModalTitle').textContent = 'Chỉnh sửa: ' + u.username;

    // Info tab
    document.getElementById('detailId').textContent = '#' + u.id;
    document.getElementById('detailUsername').textContent = u.username;
    document.getElementById('detailPlayer').textContent = u.player_name || '-';
    document.getElementById('detailIP').textContent = u.ip_address || '-';
    document.getElementById('detailCreated').textContent = formatTime(u.create_time);
    document.getElementById('detailLastLogin').textContent = formatTime(u.last_time_login);

    // Finance tab
    document.getElementById('editVnd').value = u.vnd;
    document.getElementById('editTongnap').value = u.tongnap;
    document.getElementById('creditAmount').value = '';

    // Access tab
    document.getElementById('editAdmin').value = u.is_admin;
    document.getElementById('editBan').value = u.ban;

    // Reset to first tab
    document.querySelectorAll('.edit-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.edit-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('editTab-info').classList.add('active');
    document.querySelectorAll('.edit-tab')[0].classList.add('active');

    document.getElementById('editModal').classList.add('show');
}

function closeModal() {
    document.getElementById('editModal').classList.remove('show');
    currentEditUser = null;
}

function saveUser() {
    const id = document.getElementById('editUserId').value;
    const fields = {
        vnd: document.getElementById('editVnd').value,
        tongnap: document.getElementById('editTongnap').value,
        is_admin: document.getElementById('editAdmin').value,
        ban: document.getElementById('editBan').value
    };

    const promises = Object.entries(fields).map(([field, value]) => {
        const fd = new FormData();
        fd.append('action', 'update_user');
        fd.append('user_id', id);
        fd.append('field', field);
        fd.append('value', value);
        return fetch('/admin/api.php', { method: 'POST', body: fd });
    });

    Promise.all(promises)
        .then(() => {
            showToast('Đã cập nhật thành công!', 'success');
            closeModal();
            loadUsers();
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function manualCredit() {
    const id = document.getElementById('editUserId').value;
    const amount = document.getElementById('creditAmount').value;
    if (!amount || amount <= 0) { showToast('Nhập số tiền hợp lệ', 'error'); return; }

    const fd = new FormData();
    fd.append('action', 'manual_credit');
    fd.append('user_id', id);
    fd.append('amount', amount);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                loadUsers();
                document.getElementById('creditAmount').value = '';
                const currentVnd = parseInt(document.getElementById('editVnd').value) || 0;
                document.getElementById('editVnd').value = currentVnd + parseInt(amount);
            } else {
                showToast(res.message, 'error');
            }
        });
}

function resetPassword() {
    const id = document.getElementById('editUserId').value;
    if (!confirm('Reset mật khẩu user này về abc123?')) return;

    const fd = new FormData();
    fd.append('action', 'reset_user_password');
    fd.append('user_id', id);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message, res.status === 'success' ? 'success' : 'error');
        });
}

// Quick actions from table
function quickBan(userId, banValue) {
    const action = banValue === 1 ? 'Ban' : 'Unban';
    if (!confirm(action + ' user #' + userId + '?')) return;

    const fd = new FormData();
    fd.append('action', 'update_user');
    fd.append('user_id', userId);
    fd.append('field', 'ban');
    fd.append('value', banValue);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message || 'Đã cập nhật', res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') loadUsers();
        });
}

function quickReset(userId, username) {
    if (!confirm('Reset mật khẩu của "' + username + '" về abc123?')) return;

    const fd = new FormData();
    fd.append('action', 'reset_user_password');
    fd.append('user_id', userId);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message, res.status === 'success' ? 'success' : 'error');
        });
}

document.addEventListener('DOMContentLoaded', loadUsers);
</script>

</body>
</html>

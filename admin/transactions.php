<?php
// admin/transactions.php - Bank Transaction Logs (Enhanced)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../connect.php';

if (!isset($_SESSION['username'])) { header('Location: /login'); exit; }
$stmt = $conn->prepare("SELECT id, username, is_admin FROM account WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$admin_user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$admin_user || $admin_user['is_admin'] != 1) { header('Location: /'); exit; }

$current_page = 'transactions';
$page_title = 'Giao dịch Bank';
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
        .filter-bar { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; align-items:center; }
        .filter-btn { background:rgba(15,23,42,0.03); color:var(--text-secondary); border:1px solid var(--border-primary); padding:7px 14px; border-radius:var(--radius-xs); font-size:12px; font-weight:600; cursor:pointer; transition:all .2s; font-family:inherit; }
        .filter-btn.active { background:var(--accent-blue); color:#fff; border-color:var(--accent-blue); box-shadow:0 2px 8px rgba(88,117,245,.3); }
        .filter-btn:hover:not(.active) { background:rgba(88,117,245,.08); color:var(--accent-blue); }
        .tx-actions { display:flex; gap:4px; }
        .tx-actions button { padding:5px 8px; border-radius:6px; border:1px solid var(--border-primary); background:var(--bg-glass); cursor:pointer; font-size:12px; color:var(--text-muted); transition:all .15s; }
        .tx-actions button:hover { color:var(--accent-blue); border-color:var(--accent-blue); background:rgba(88,117,245,.06); }
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
        <a href="/admin/users" class="nav-item"><i class="bi bi-people-fill"></i> Người dùng</a>
        <a href="/admin/transactions" class="nav-item active"><i class="bi bi-receipt"></i> Giao dịch Bank</a>
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
                <div class="topbar-breadcrumb">Admin / Giao dịch</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="search-bar" style="max-width:250px">
                <i class="bi bi-search"></i>
                <input type="text" id="txSearch" placeholder="Tìm mã GD, user..." onkeyup="debounceSearch()">
            </div>
            <button class="btn btn-primary btn-sm" onclick="checkATM()"><i class="bi bi-arrow-repeat"></i> Check ATM</button>
        </div>
    </header>

    <div class="admin-content">
        <div class="filter-bar">
            <button class="filter-btn active" onclick="setFilter(this, '')">Tất cả</button>
            <button class="filter-btn" onclick="setFilter(this, 'success')">✓ Thành công</button>
            <button class="filter-btn" onclick="setFilter(this, 'pending')">⏳ Đang xử lý</button>
            <button class="filter-btn" onclick="setFilter(this, 'ignored')">⊘ Bỏ qua</button>
            <button class="filter-btn" onclick="setFilter(this, 'failed')">✗ Thất bại</button>
            <div id="txCount" style="margin-left:auto;color:var(--text-muted);font-size:13px"></div>
        </div>

        <div class="panel">
            <div class="panel-body">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Thao tác</th>
                            <th>Mã GD</th>
                            <th>Số tiền</th>
                            <th>Người gửi</th>
                            <th>Nội dung</th>
                            <th>User khớp</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody id="txTable">
                        <tr><td colspan="8" style="text-align:center;padding:40px"><div class="spinner"></div></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>
</main>

<!-- Edit Transaction Modal -->
<div class="modal-overlay" id="txEditModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="txEditTitle">Sửa giao dịch</div>
            <button class="modal-close" onclick="closeTxModal()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="txEditId">
            <div class="form-group">
                <label class="form-label">Mã GD</label>
                <input type="text" class="form-input" id="txEditNum" readonly style="opacity:.6">
            </div>
            <div class="form-group">
                <label class="form-label">Số tiền (VND)</label>
                <input type="text" class="form-input" id="txEditAmount" readonly style="opacity:.6">
            </div>
            <div class="form-group">
                <label class="form-label">Nội dung CK</label>
                <input type="text" class="form-input" id="txEditDesc" readonly style="opacity:.6">
            </div>
            <hr style="border-color:var(--border-primary);margin:20px 0">
            <div class="form-group">
                <label class="form-label">Matched Username</label>
                <input type="text" class="form-input" id="txEditUsername" placeholder="Nhập username khớp">
            </div>
            <div class="form-group">
                <label class="form-label">Trạng thái</label>
                <select class="form-input" id="txEditStatus">
                    <option value="success">Thành công</option>
                    <option value="pending">Đang xử lý</option>
                    <option value="ignored">Bỏ qua</option>
                    <option value="failed">Thất bại</option>
                </select>
            </div>
            <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:var(--radius-xs);padding:12px;font-size:12px;color:var(--accent-orange)">
                <strong>⚠ Lưu ý:</strong> Nếu đổi status sang "Thành công" và có matched username, hệ thống sẽ tự động cộng VND cho user đó.
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeTxModal()">Hủy</button>
            <button class="btn btn-primary" onclick="saveTxEdit()">Lưu thay đổi</button>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
let currentPage = 1;
let currentStatus = '';
let searchTimeout;

function formatMoney(n) { return new Intl.NumberFormat('vi-VN').format(n); }
function formatTime(t) {
    if (!t) return '-';
    const d = new Date(t);
    if (isNaN(d.getTime())) return t;
    return d.toLocaleString('vi-VN', {day:'2-digit',month:'2-digit',year:'2-digit',hour:'2-digit',minute:'2-digit'});
}
function esc(v) { const d = document.createElement('div'); d.appendChild(document.createTextNode(v == null ? '' : String(v))); return d.innerHTML; }
function statusBadge(s) {
    const map = {
        'success': '<span class="badge badge-success">Thành công</span>',
        'ignored': '<span class="badge badge-muted">Bỏ qua</span>',
        'failed': '<span class="badge badge-danger">Thất bại</span>',
        'pending': '<span class="badge badge-warning">Đang xử lý</span>'
    };
    return map[s] || s;
}
function showToast(msg, type='info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => { currentPage = 1; loadTransactions(); }, 400);
}

function setFilter(btn, status) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentStatus = status;
    currentPage = 1;
    loadTransactions();
}

function loadTransactions() {
    fetch(`/admin/api.php?action=get_transactions&status=${currentStatus}&page=${currentPage}`)
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') return;

            document.getElementById('txCount').textContent = `Tổng: ${res.total} giao dịch`;

            const tbody = document.getElementById('txTable');
            const search = (document.getElementById('txSearch').value || '').toLowerCase().trim();

            let data = res.data;
            if (search) {
                data = data.filter(tx =>
                    String(tx.transaction_number || '').includes(search) ||
                    (tx.matched_username || '').toLowerCase().includes(search) ||
                    (tx.description || '').toLowerCase().includes(search) ||
                    (tx.sender_name || '').toLowerCase().includes(search)
                );
            }

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px;opacity:.3"></i>Không có giao dịch</td></tr>';
            } else {
                tbody.innerHTML = data.map(tx => `
                    <tr>
                        <td>
                            <div class="tx-actions">
                                <button title="Sửa" onclick='openTxEdit(${JSON.stringify(tx)})'><i class="bi bi-pencil-square"></i></button>
                            </div>
                        </td>
                        <td class="text-white"><strong>#${tx.transaction_number}</strong></td>
                        <td><span class="text-money">${formatMoney(tx.amount)}đ</span></td>
                        <td>${esc(tx.sender_name || '-')}</td>
                        <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${esc(tx.description || '')}">${esc(tx.description || '-')}</td>
                        <td>${tx.matched_username ? '<span class="text-username">' + esc(tx.matched_username) + '</span>' : '<span style="color:var(--text-muted)">-</span>'}</td>
                        <td>${statusBadge(tx.status)}</td>
                        <td>${formatTime(tx.created_at)}</td>
                    </tr>
                `).join('');
            }

            const pagDiv = document.getElementById('pagination');
            if (res.pages <= 1) { pagDiv.innerHTML = ''; return; }
            let html = '';
            if (currentPage > 1) html += `<button class="page-btn" onclick="currentPage=1;loadTransactions()">«</button>`;
            for (let i = Math.max(1, currentPage-3); i <= Math.min(res.pages, currentPage+3); i++) {
                html += `<button class="page-btn ${i === res.page ? 'active' : ''}" onclick="currentPage=${i};loadTransactions()">${i}</button>`;
            }
            if (currentPage < res.pages) html += `<button class="page-btn" onclick="currentPage=${res.pages};loadTransactions()">»</button>`;
            pagDiv.innerHTML = html;
        });
}

function openTxEdit(tx) {
    document.getElementById('txEditId').value = tx.id;
    document.getElementById('txEditTitle').textContent = 'Sửa GD #' + tx.transaction_number;
    document.getElementById('txEditNum').value = tx.transaction_number;
    document.getElementById('txEditAmount').value = formatMoney(tx.amount) + ' VND';
    document.getElementById('txEditDesc').value = tx.description || '';
    document.getElementById('txEditUsername').value = tx.matched_username || '';
    document.getElementById('txEditStatus').value = tx.status || 'pending';
    document.getElementById('txEditModal').classList.add('show');
}

function closeTxModal() {
    document.getElementById('txEditModal').classList.remove('show');
}

function saveTxEdit() {
    const id = document.getElementById('txEditId').value;
    const username = document.getElementById('txEditUsername').value.trim();
    const status = document.getElementById('txEditStatus').value;

    const promises = [];

    // Update matched_username
    if (username) {
        const fd1 = new FormData();
        fd1.append('action', 'update_transaction');
        fd1.append('tx_id', id);
        fd1.append('field', 'matched_username');
        fd1.append('value', username);
        promises.push(fetch('/admin/api.php', { method: 'POST', body: fd1 }));
    }

    // Update status
    const fd2 = new FormData();
    fd2.append('action', 'update_transaction');
    fd2.append('tx_id', id);
    fd2.append('field', 'status');
    fd2.append('value', status);
    promises.push(fetch('/admin/api.php', { method: 'POST', body: fd2 }));

    Promise.all(promises)
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(results => {
            const lastRes = results[results.length - 1];
            showToast(lastRes.message || 'Đã cập nhật', lastRes.status === 'success' ? 'success' : 'error');
            closeTxModal();
            loadTransactions();
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function checkATM() {
    showToast('Đang kiểm tra ATM...', 'info');
    fetch('/admin/api.php?action=check_atm')
        .then(r => r.json())
        .then(res => {
            showToast(res.message || 'Không có phản hồi', res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') loadTransactions();
        });
}

document.addEventListener('DOMContentLoaded', loadTransactions);
</script>

</body>
</html>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../connect.php';

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

$current_page = 'operations';
$page_title = 'Quan tri mo rong';
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
</head>
<body class="admin-body">

<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/admin" class="sidebar-logo">
            <div class="sidebar-logo-icon">⚡</div>
            <div class="sidebar-logo-text">Admin Panel<span>Quan tri he thong</span></div>
        </a>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-title">Tong quan</div>
        <a href="/admin" class="nav-item"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="/admin/operations" class="nav-item active"><i class="bi bi-diagram-3-fill"></i> Quan tri mo rong</a>
        <div class="nav-section-title">Quan ly</div>
        <a href="/admin/users" class="nav-item"><i class="bi bi-people-fill"></i> Nguoi dung</a>
        <a href="/admin/transactions" class="nav-item"><i class="bi bi-receipt"></i> Giao dich Bank</a>
        <a href="/admin/feedback" class="nav-item"><i class="bi bi-envelope-fill"></i> Gop y</a>
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

<main class="admin-main">
    <header class="admin-topbar">
        <div class="topbar-left">
            <button class="topbar-btn btn-menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <div class="topbar-title"><?php echo $page_title; ?></div>
                <div class="topbar-breadcrumb">Admin / Quan tri mo rong</div>
            </div>
        </div>
        <div class="topbar-right">
            <button class="btn btn-primary btn-sm" onclick="loadOperations()"><i class="bi bi-arrow-clockwise"></i> Lam moi</button>
        </div>
    </header>

    <div class="admin-content">
        <div class="panel" style="margin-bottom:20px;">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-rocket-takeoff-fill" style="margin-right:8px;color:var(--accent-blue)"></i>10 tinh nang quan ly du an game</div>
            </div>
            <div class="panel-body padded">
                <div class="ops-grid">
                    <div class="ops-card">
                        <div class="ops-label">1. Che do bao tri</div>
                        <div class="ops-value" id="maintenanceMode">-</div>
                        <button class="btn btn-sm btn-ghost" onclick="toggleMaintenance()">Bat/Tat</button>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">2. Trang thai ATM API</div>
                        <div class="ops-value" id="atmConfigured">-</div>
                        <button class="btn btn-sm btn-ghost" onclick="checkATM()">Check ATM</button>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">3. Tu khoa giao dich</div>
                        <div class="ops-value" id="atmPrefix">-</div>
                        <div style="display:flex;gap:6px;">
                            <input id="prefixInput" class="form-input" placeholder="chuyen tien">
                            <button class="btn btn-sm btn-ghost" onclick="savePrefix()">Luu</button>
                        </div>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">4. Gop y moi</div>
                        <div class="ops-value" id="feedbackCount">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/feedback">Xu ly</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">5. Giao dich pending</div>
                        <div class="ops-value" id="pendingCount">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/transactions">Mo danh sach</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">6. Loi giao dich 24h</div>
                        <div class="ops-value" id="failedCount">-</div>
                        <span class="badge badge-danger">Risk monitor</span>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">7. Bo qua giao dich 24h</div>
                        <div class="ops-value" id="ignoredCount">-</div>
                        <span class="badge badge-warning">Can review</span>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">8. User active 24h</div>
                        <div class="ops-value" id="activeCount">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/users">Xem user</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">9. User inactive 30d</div>
                        <div class="ops-value" id="inactiveCount">-</div>
                        <span class="badge badge-muted">Retention</span>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">10. Top nap + server</div>
                        <div class="ops-value" id="topPayer">-</div>
                        <button class="btn btn-sm btn-ghost" onclick="checkServer()">Check server</button>
                    </div>
                </div>
                <div style="margin-top:10px;font-size:12px;color:var(--text-muted);">
                    Server target: <strong id="serverTarget">-</strong> |
                    New users 7d: <strong id="newUsers7d">0</strong>
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-chat-left-text" style="margin-right:8px;color:var(--accent-purple)"></i>Gop y moi (10)</div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead>
                            <tr><th>User</th><th>Tieu de</th><th>Loai</th><th>Thoi gian</th></tr>
                        </thead>
                        <tbody id="feedbackRows"><tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px">Dang tai...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-clock-history" style="margin-right:8px;color:var(--accent-orange)"></i>Giao dich pending (10)</div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead>
                            <tr><th>So tien</th><th>Nguoi gui</th><th>Noi dung</th><th>Thoi gian</th></tr>
                        </thead>
                        <tbody id="pendingRows"><tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px">Dang tai...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid-2" style="margin-top:20px;">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-x-octagon" style="margin-right:8px;color:var(--accent-red)"></i>Failed 24h (10)</div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead>
                            <tr><th>So tien</th><th>So GD</th><th>Noi dung</th><th>Thoi gian</th></tr>
                        </thead>
                        <tbody id="failedRows"><tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px">Dang tai...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-exclamation-circle" style="margin-right:8px;color:var(--accent-cyan)"></i>Ignored 24h (10)</div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead>
                            <tr><th>So tien</th><th>So GD</th><th>Noi dung</th><th>Thoi gian</th></tr>
                        </thead>
                        <tbody id="ignoredRows"><tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px">Dang tai...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid-2" style="margin-top:20px;">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-lightning-charge" style="margin-right:8px;color:var(--accent-green)"></i>User active 24h (10)</div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead>
                            <tr><th>Username</th><th>VND</th><th>Tong nap</th><th>Last login</th></tr>
                        </thead>
                        <tbody id="activeRows"><tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px">Dang tai...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-person-dash" style="margin-right:8px;color:var(--accent-pink)"></i>User inactive 30d (10)</div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead>
                            <tr><th>Username</th><th>VND</th><th>Tong nap</th><th>Last login</th></tr>
                        </thead>
                        <tbody id="inactiveRows"><tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px">Dang tai...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel" style="margin-top:20px;">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-trophy-fill" style="margin-right:8px;color:var(--accent-orange)"></i>Top payer (10)</div>
            </div>
            <div class="panel-body">
                <table class="admin-table">
                    <thead>
                        <tr><th>#</th><th>Username</th><th>Tong nap</th><th>VND</th><th>Tao tai khoan</th></tr>
                    </thead>
                    <tbody id="topRows"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:30px">Dang tai...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="toast-container" id="toastContainer"></div>

<script>
let ops = null;

function formatMoney(n) { return new Intl.NumberFormat('vi-VN').format(Number(n || 0)); }
function formatTime(t) {
    if (!t) return '-';
    const d = new Date(t);
    if (isNaN(d.getTime())) return t;
    return d.toLocaleString('vi-VN', {day:'2-digit', month:'2-digit', hour:'2-digit', minute:'2-digit'});
}
function esc(v) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(v == null ? '' : String(v)));
    return d.innerHTML;
}
function showToast(msg, type = 'info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

function fillRows(id, rows, mapFn, cols) {
    const el = document.getElementById(id);
    if (!rows || rows.length === 0) {
        el.innerHTML = '<tr><td colspan="' + cols + '" style="text-align:center;color:var(--text-muted);padding:22px">Khong co du lieu</td></tr>';
        return;
    }
    el.innerHTML = rows.map(mapFn).join('');
}

function loadOperations() {
    fetch('/admin/api.php?action=get_operations_data')
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') {
                showToast(res.message || 'Khong tai duoc du lieu', 'error');
                return;
            }
            ops = res.data;
            document.getElementById('maintenanceMode').textContent = Number(ops.maintenance_mode || 0) === 1 ? 'Dang bat' : 'Dang tat';
            document.getElementById('atmConfigured').textContent = Number(ops.atm_configured || 0) === 1 ? 'San sang' : 'Thieu config';
            document.getElementById('atmPrefix').textContent = ops.atm_prefix || 'chuyen tien';
            document.getElementById('prefixInput').value = ops.atm_prefix || 'chuyen tien';
            document.getElementById('feedbackCount').textContent = formatMoney((ops.feedback_new || []).length);
            document.getElementById('pendingCount').textContent = formatMoney((ops.pending_txs || []).length);
            document.getElementById('failedCount').textContent = formatMoney(ops.failed_count_24h || 0);
            document.getElementById('ignoredCount').textContent = formatMoney(ops.ignored_count_24h || 0);
            document.getElementById('activeCount').textContent = formatMoney((ops.active_24h || []).length);
            document.getElementById('inactiveCount').textContent = formatMoney((ops.inactive_30d || []).length);
            document.getElementById('newUsers7d').textContent = formatMoney(ops.new_users_7d || 0);

            const top = (ops.top_payers && ops.top_payers[0]) ? ops.top_payers[0] : null;
            document.getElementById('topPayer').textContent = top ? (top.username + ' • ' + formatMoney(top.tongnap) + 'd') : '-';

            const server = ops.server_ip ? (ops.server_ip + ':' + (ops.server_port || 14445)) : 'chua cau hinh';
            document.getElementById('serverTarget').textContent = server;

            fillRows('feedbackRows', ops.feedback_new, f =>
                '<tr><td>' + esc(f.player_name || f.username) + '</td><td>' + esc(f.title) + '</td><td>' + esc(f.category) + '</td><td>' + formatTime(f.created_at) + '</td></tr>', 4);
            fillRows('pendingRows', ops.pending_txs, t =>
                '<tr><td>' + formatMoney(t.amount) + 'd</td><td>' + esc(t.sender_name || '-') + '</td><td>' + esc(t.description || '-') + '</td><td>' + formatTime(t.created_at) + '</td></tr>', 4);
            fillRows('failedRows', ops.failed_24h, t =>
                '<tr><td>' + formatMoney(t.amount) + 'd</td><td>' + esc(t.transaction_number) + '</td><td>' + esc(t.description || '-') + '</td><td>' + formatTime(t.created_at) + '</td></tr>', 4);
            fillRows('ignoredRows', ops.ignored_24h, t =>
                '<tr><td>' + formatMoney(t.amount) + 'd</td><td>' + esc(t.transaction_number) + '</td><td>' + esc(t.description || '-') + '</td><td>' + formatTime(t.created_at) + '</td></tr>', 4);
            fillRows('activeRows', ops.active_24h, u =>
                '<tr><td>' + esc(u.username) + '</td><td>' + formatMoney(u.vnd) + 'd</td><td>' + formatMoney(u.tongnap) + 'd</td><td>' + formatTime(u.last_time_login) + '</td></tr>', 4);
            fillRows('inactiveRows', ops.inactive_30d, u =>
                '<tr><td>' + esc(u.username) + '</td><td>' + formatMoney(u.vnd) + 'd</td><td>' + formatMoney(u.tongnap) + 'd</td><td>' + formatTime(u.last_time_login) + '</td></tr>', 4);
            fillRows('topRows', ops.top_payers, (u, i) =>
                '<tr><td>' + (i + 1) + '</td><td>' + esc(u.username) + '</td><td>' + formatMoney(u.tongnap) + 'd</td><td>' + formatMoney(u.vnd) + 'd</td><td>' + formatTime(u.create_time) + '</td></tr>', 5);
        })
        .catch(e => showToast('Loi ket noi: ' + e.message, 'error'));
}

function toggleMaintenance() {
    const target = ops && Number(ops.maintenance_mode || 0) === 1 ? 0 : 1;
    const fd = new FormData();
    fd.append('action', 'set_maintenance');
    fd.append('enabled', String(target));
    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message || 'Da cap nhat', res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') loadOperations();
        })
        .catch(e => showToast('Loi: ' + e.message, 'error'));
}

function savePrefix() {
    const v = document.getElementById('prefixInput').value.trim();
    if (!v) {
        showToast('Prefix khong duoc rong', 'error');
        return;
    }
    const fd = new FormData();
    fd.append('action', 'save_settings');
    fd.append('settings[ATM_PREFIX]', v);
    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message || 'Da luu prefix', res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') loadOperations();
        })
        .catch(e => showToast('Loi: ' + e.message, 'error'));
}

function checkATM() {
    fetch('/admin/api.php?action=check_atm')
        .then(r => r.json())
        .then(res => {
            showToast(res.message || 'Khong co phan hoi', res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') loadOperations();
        })
        .catch(e => showToast('Loi: ' + e.message, 'error'));
}

function checkServer() {
    if (!ops || !ops.server_ip) {
        showToast('Chua cau hinh GAME_SERVER_IP', 'error');
        return;
    }
    fetch('/admin/api.php?action=check_game_server&ip=' + encodeURIComponent(ops.server_ip) + '&port=' + encodeURIComponent(String(ops.server_port || 14445)))
        .then(r => r.json())
        .then(res => showToast(res.message || 'Khong co phan hoi', res.status === 'success' ? 'success' : 'error'))
        .catch(e => showToast('Loi: ' + e.message, 'error'));
}

document.addEventListener('DOMContentLoaded', loadOperations);
</script>

</body>
</html>

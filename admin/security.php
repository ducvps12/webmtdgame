<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../connect.php';
if (!isset($_SESSION['username'])) { header('Location: /login'); exit; }
$stmt = $conn->prepare("SELECT id, username, is_admin FROM account WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$admin_user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$admin_user || $admin_user['is_admin'] != 1) { header('Location: /'); exit; }
$admin_username = htmlspecialchars($admin_user['username']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bảo mật & Nhật ký</title>
    <link rel="stylesheet" href="/admin/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/images/favicon-48x48.ico">
    <style>
        .sec-tabs { display:flex; gap:4px; background:rgba(15,23,42,0.02); border:1px solid var(--border-primary); border-radius:var(--radius); padding:5px; margin-bottom:24px; overflow-x:auto; }
        .sec-tab { padding:10px 18px; border-radius:var(--radius-sm); font-size:13px; font-weight:500; color:var(--text-muted); cursor:pointer; border:none; background:none; font-family:inherit; display:flex; align-items:center; gap:6px; white-space:nowrap; transition:all .2s; }
        .sec-tab:hover { color:var(--text-secondary); background:rgba(15,23,42,0.03); }
        .sec-tab.active { background:var(--accent-blue); color:#fff; font-weight:600; box-shadow:0 2px 10px rgba(88,117,245,0.3); }
        .sec-panel { display:none; animation:fadeIn .3s ease; }
        .sec-panel.active { display:block; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }

        .score-ring { width:160px; height:160px; position:relative; margin:0 auto 20px; }
        .score-ring svg { width:100%; height:100%; transform:rotate(-90deg); }
        .score-ring circle { fill:none; stroke-width:10; stroke-linecap:round; }
        .score-ring .bg { stroke:rgba(15,23,42,0.08); }
        .score-ring .fg { stroke:var(--accent-green); transition:stroke-dashoffset .8s ease; }
        .score-text { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
        .score-text .num { font-size:42px; font-weight:800; letter-spacing:-1px; }
        .score-text .lbl { font-size:12px; color:var(--text-muted); font-weight:600; text-transform:uppercase; }

        .check-list { display:grid; gap:10px; }
        .check-item { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:var(--radius-sm); border:1px solid var(--border-primary); background:var(--bg-glass); transition:all .2s; }
        .check-item:hover { border-color:var(--border-glow); transform:translateY(-1px); }
        .check-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
        .check-icon.pass { background:rgba(16,185,129,0.12); color:var(--accent-green); }
        .check-icon.fail { background:rgba(239,68,68,0.12); color:var(--accent-red); }
        .check-info { flex:1; }
        .check-info h4 { font-size:14px; font-weight:600; margin:0 0 2px; }
        .check-info p { font-size:12px; color:var(--text-muted); margin:0; }

        .alert-cards { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:14px; margin-bottom:24px; }
        .alert-card { padding:18px; border-radius:var(--radius-sm); border:1px solid var(--border-primary); background:var(--bg-glass); text-align:center; transition:all .2s; }
        .alert-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
        .alert-card .ac-icon { font-size:28px; margin-bottom:8px; }
        .alert-card .ac-val { font-size:24px; font-weight:800; }
        .alert-card .ac-lbl { font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-top:4px; }

        .ip-actions { display:flex; gap:8px; margin-bottom:16px; }
        .ip-actions input { flex:1; }
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
        <a href="/admin/transactions" class="nav-item"><i class="bi bi-receipt"></i> Giao dịch Bank</a>
        <a href="/admin/feedback" class="nav-item"><i class="bi bi-envelope-fill"></i> Góp ý</a>
        <a href="/admin/forum" class="nav-item"><i class="bi bi-chat-square-text-fill"></i> Diễn đàn</a>
        <div class="nav-section-title">Phân tích</div>
        <a href="/admin/analytics" class="nav-item"><i class="bi bi-bar-chart-line-fill"></i> Báo cáo & Thống kê</a>
        <a href="/admin/payment-flow" class="nav-item"><i class="bi bi-credit-card-2-front"></i> Cơ chế thanh toán</a>
        <div class="nav-section-title">Hệ thống</div>
        <a href="/admin/settings" class="nav-item"><i class="bi bi-gear-fill"></i> Cài đặt</a>
        <a href="/admin/security" class="nav-item active"><i class="bi bi-shield-lock-fill"></i> Bảo mật & Nhật ký</a>
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
                <div class="topbar-title">Bảo mật & Nhật ký</div>
                <div class="topbar-breadcrumb">Admin / Bảo mật</div>
            </div>
        </div>
        <div class="topbar-right">
            <button class="btn btn-primary btn-sm" onclick="loadSecurity()"><i class="bi bi-arrow-clockwise"></i> Làm mới</button>
        </div>
    </header>

    <div class="admin-content">
        <div class="sec-tabs">
            <button class="sec-tab active" onclick="switchSecTab('overview',this)"><i class="bi bi-shield-check"></i> Tổng quan Bảo mật</button>
            <button class="sec-tab" onclick="switchSecTab('logins',this)"><i class="bi bi-person-lines-fill"></i> Nhật ký Đăng nhập</button>
            <button class="sec-tab" onclick="switchSecTab('ips',this)"><i class="bi bi-globe"></i> Quản lý IP</button>
            <button class="sec-tab" onclick="switchSecTab('audit',this)"><i class="bi bi-journal-text"></i> Audit Log</button>
        </div>

        <!-- TAB: Overview -->
        <div id="sec-overview" class="sec-panel active">
            <div class="alert-cards">
                <div class="alert-card">
                    <div class="ac-icon">🛡️</div>
                    <div class="ac-val" id="secScore">-</div>
                    <div class="ac-lbl">Điểm bảo mật</div>
                </div>
                <div class="alert-card">
                    <div class="ac-icon">👤</div>
                    <div class="ac-val" id="secAdmins">-</div>
                    <div class="ac-lbl">Tài khoản Admin</div>
                </div>
                <div class="alert-card">
                    <div class="ac-icon">🚫</div>
                    <div class="ac-val" id="secBlocked">-</div>
                    <div class="ac-lbl">IP đã block</div>
                </div>
                <div class="alert-card">
                    <div class="ac-icon">⚠️</div>
                    <div class="ac-val" id="secFailed">-</div>
                    <div class="ac-lbl">GD lỗi 24h</div>
                </div>
            </div>

            <div class="grid-2">
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title"><i class="bi bi-shield-fill-check" style="margin-right:8px;color:var(--accent-green)"></i>Điểm Bảo mật Hệ thống</div>
                    </div>
                    <div class="panel-body padded">
                        <div class="score-ring">
                            <svg viewBox="0 0 160 160">
                                <circle class="bg" cx="80" cy="80" r="70"></circle>
                                <circle class="fg" id="scoreCircle" cx="80" cy="80" r="70" stroke-dasharray="440" stroke-dashoffset="440"></circle>
                            </svg>
                            <div class="score-text">
                                <div class="num" id="scoreNum">0</div>
                                <div class="lbl">/ 100</div>
                            </div>
                        </div>
                        <div class="check-list" id="checkList"></div>
                    </div>
                </div>

                <div>
                    <div class="panel" style="margin-bottom:22px">
                        <div class="panel-header">
                            <div class="panel-title"><i class="bi bi-person-badge" style="margin-right:8px;color:var(--accent-purple)"></i>Tài khoản Admin</div>
                        </div>
                        <div class="panel-body">
                            <table class="admin-table">
                                <thead><tr><th>Username</th><th>IP</th><th>Đăng nhập cuối</th></tr></thead>
                                <tbody id="adminList"><tr><td colspan="3" style="text-align:center;padding:30px;color:var(--text-muted)">Đang tải...</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-header">
                            <div class="panel-title"><i class="bi bi-exclamation-triangle" style="margin-right:8px;color:var(--accent-orange)"></i>Đăng nhập Đáng ngờ (24h)</div>
                        </div>
                        <div class="panel-body">
                            <table class="admin-table">
                                <thead><tr><th>Username</th><th>Số IP khác nhau</th></tr></thead>
                                <tbody id="suspiciousList"><tr><td colspan="2" style="text-align:center;padding:30px;color:var(--text-muted)">Đang tải...</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: Login Logs -->
        <div id="sec-logins" class="sec-panel">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-person-lines-fill" style="margin-right:8px;color:var(--accent-blue)"></i>Nhật ký Đăng nhập</div>
                    <div class="search-bar" style="max-width:280px">
                        <i class="bi bi-search"></i>
                        <input type="text" id="loginSearch" placeholder="Tìm username hoặc IP..." onkeyup="debounceLogin()">
                    </div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead><tr><th>ID</th><th>Username</th><th>IP Address</th><th>VND</th><th>Tổng nạp</th><th>Admin</th><th>Ban</th><th>Thời gian</th></tr></thead>
                        <tbody id="loginTable"><tr><td colspan="8" style="text-align:center;padding:40px"><div class="spinner"></div></td></tr></tbody>
                    </table>
                </div>
                <div class="pagination" id="loginPagination"></div>
            </div>
        </div>

        <!-- TAB: IP Management -->
        <div id="sec-ips" class="sec-panel">
            <div class="panel" style="margin-bottom:22px">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-ban" style="margin-right:8px;color:var(--accent-red)"></i>Block IP</div>
                </div>
                <div class="panel-body padded">
                    <div class="ip-actions">
                        <input type="text" class="form-input" id="blockIpInput" placeholder="Nhập IP cần block (ví dụ: 103.1.2.3)">
                        <input type="text" class="form-input" id="blockIpReason" placeholder="Lý do" style="max-width:200px">
                        <button class="btn btn-danger btn-sm" onclick="blockIP()"><i class="bi bi-ban"></i> Block</button>
                    </div>
                    <table class="admin-table">
                        <thead><tr><th>IP</th><th>Lý do</th><th>Blocked bởi</th><th>Thời gian</th><th>Hành động</th></tr></thead>
                        <tbody id="blockedTable"><tr><td colspan="5" style="text-align:center;padding:30px;color:var(--text-muted)">Đang tải...</td></tr></tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-globe" style="margin-right:8px;color:var(--accent-cyan)"></i>IP → User Mapping</div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead><tr><th>IP Address</th><th>Số User</th><th>Usernames</th></tr></thead>
                        <tbody id="ipStatsTable"><tr><td colspan="3" style="text-align:center;padding:30px;color:var(--text-muted)">Đang tải...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB: Audit Log -->
        <div id="sec-audit" class="sec-panel">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-journal-text" style="margin-right:8px;color:var(--accent-purple)"></i>Nhật ký Hành động Admin</div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead><tr><th>Admin</th><th>Hành động</th><th>Loại</th><th>ID</th><th>Chi tiết</th><th>IP</th><th>Thời gian</th></tr></thead>
                        <tbody id="auditTable"><tr><td colspan="7" style="text-align:center;padding:40px"><div class="spinner"></div></td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="toast-container" id="toastContainer"></div>

<script>
let secData = null;
let loginPage = 1;
let loginTimeout;

function formatMoney(n) { return new Intl.NumberFormat('vi-VN').format(Number(n || 0)); }
function formatTime(t) {
    if (!t) return '-';
    const d = new Date(t);
    return isNaN(d.getTime()) ? t : d.toLocaleString('vi-VN', {day:'2-digit',month:'2-digit',year:'2-digit',hour:'2-digit',minute:'2-digit'});
}
function esc(v) { const d = document.createElement('div'); d.appendChild(document.createTextNode(v == null ? '' : String(v))); return d.innerHTML; }
function showToast(msg, type = 'info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

function switchSecTab(tab, btn) {
    document.querySelectorAll('.sec-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.sec-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('sec-' + tab).classList.add('active');
    btn.classList.add('active');
    if (tab === 'logins') loadLoginLogs();
}

function loadSecurity() {
    fetch('/admin/api.php?action=get_security_overview')
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') return;
            secData = res.data;

            // Score
            const score = secData.score || 0;
            document.getElementById('secScore').textContent = score + '/100';
            document.getElementById('scoreNum').textContent = score;
            const circumference = 2 * Math.PI * 70;
            const offset = circumference - (score / 100) * circumference;
            const circle = document.getElementById('scoreCircle');
            circle.style.strokeDasharray = circumference;
            circle.style.strokeDashoffset = offset;
            circle.style.stroke = score >= 80 ? '#10b981' : score >= 60 ? '#f59e0b' : '#ef4444';

            document.getElementById('secAdmins').textContent = (secData.admin_accounts || []).length;
            document.getElementById('secBlocked').textContent = (secData.blocked_ips || []).length;
            document.getElementById('secFailed').textContent = secData.failed_tx_24h || 0;

            // Checklist
            const checks = secData.checks || {};
            const checkNames = {
                db_password: ['Database Password', 'Mật khẩu MySQL đã được đặt và không phải mặc định'],
                db_local_only: ['MySQL Local Only', 'MySQL chỉ lắng nghe localhost (127.0.0.1)'],
                atm_configured: ['ATM API Configured', 'ACB Token và API URL đã được cấu hình'],
                env_protected: ['File .env Protected', 'File cấu hình .env không truy cập công khai'],
                maintenance_off: ['Bảo trì tắt', 'Trang web đang hoạt động bình thường']
            };
            let checkHtml = '';
            for (const [k, v] of Object.entries(checkNames)) {
                const pass = checks[k];
                checkHtml += `<div class="check-item">
                    <div class="check-icon ${pass ? 'pass' : 'fail'}"><i class="bi bi-${pass ? 'check-lg' : 'x-lg'}"></i></div>
                    <div class="check-info"><h4>${v[0]}</h4><p>${v[1]}</p></div>
                    <span class="badge ${pass ? 'badge-success' : 'badge-danger'}">${pass ? 'Đạt' : 'Cảnh báo'}</span>
                </div>`;
            }
            document.getElementById('checkList').innerHTML = checkHtml;

            // Admin accounts
            const admins = secData.admin_accounts || [];
            document.getElementById('adminList').innerHTML = admins.length ? admins.map(a =>
                `<tr><td><span class="text-username">${esc(a.username)}</span></td><td>${esc(a.ip_address || '-')}</td><td>${formatTime(a.last_time_login)}</td></tr>`
            ).join('') : '<tr><td colspan="3" style="text-align:center;padding:20px;color:var(--text-muted)">Không có</td></tr>';

            // Suspicious
            const susp = secData.suspicious_users || [];
            document.getElementById('suspiciousList').innerHTML = susp.length ? susp.map(s =>
                `<tr><td><span class="text-username">${esc(s.username)}</span></td><td><span class="badge badge-warning">${s.ip_count} IPs</span></td></tr>`
            ).join('') : '<tr><td colspan="2" style="text-align:center;padding:20px;color:var(--text-muted)">Không phát hiện bất thường</td></tr>';

            // Blocked IPs
            const blocked = secData.blocked_ips || [];
            document.getElementById('blockedTable').innerHTML = blocked.length ? blocked.map(b =>
                `<tr><td><strong>${esc(b.ip_address)}</strong></td><td>${esc(b.reason || '-')}</td><td>${esc(b.blocked_by || '-')}</td><td>${formatTime(b.created_at)}</td><td><button class="btn btn-sm btn-ghost" onclick="unblockIP('${esc(b.ip_address)}')"><i class="bi bi-unlock"></i> Unblock</button></td></tr>`
            ).join('') : '<tr><td colspan="5" style="text-align:center;padding:20px;color:var(--text-muted)">Chưa có IP nào bị block</td></tr>';

            // IP stats
            const ips = secData.ip_stats || [];
            document.getElementById('ipStatsTable').innerHTML = ips.length ? ips.map(ip =>
                `<tr><td><strong>${esc(ip.ip_address)}</strong></td><td><span class="badge ${ip.user_count > 3 ? 'badge-warning' : 'badge-info'}">${ip.user_count}</span></td><td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${esc(ip.usernames)}">${esc(ip.usernames)}</td></tr>`
            ).join('') : '<tr><td colspan="3" style="text-align:center;padding:20px;color:var(--text-muted)">Không có dữ liệu</td></tr>';

            // Audit logs
            const audit = secData.audit_logs || [];
            document.getElementById('auditTable').innerHTML = audit.length ? audit.map(a => {
                const actionBadge = a.action.includes('block') ? 'badge-danger' : a.action.includes('reset') ? 'badge-warning' : 'badge-info';
                return `<tr><td><span class="text-username">${esc(a.admin_username)}</span></td><td><span class="badge ${actionBadge}">${esc(a.action)}</span></td><td>${esc(a.target_type || '-')}</td><td>${a.target_id || '-'}</td><td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${esc(a.details)}">${esc(a.details || '-')}</td><td>${esc(a.ip_address || '-')}</td><td>${formatTime(a.created_at)}</td></tr>`;
            }).join('') : '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted)">Chưa có nhật ký</td></tr>';
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function debounceLogin() {
    clearTimeout(loginTimeout);
    loginTimeout = setTimeout(() => { loginPage = 1; loadLoginLogs(); }, 400);
}

function loadLoginLogs() {
    const q = document.getElementById('loginSearch').value;
    fetch(`/admin/api.php?action=get_login_logs&q=${encodeURIComponent(q)}&page=${loginPage}`)
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') return;
            const tbody = document.getElementById('loginTable');
            if (res.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">Không tìm thấy</td></tr>';
            } else {
                tbody.innerHTML = res.data.map(u => `<tr>
                    <td class="text-white">#${u.id}</td>
                    <td><span class="text-username">${esc(u.username)}</span>${u.is_admin == 1 ? ' <span class="badge badge-danger">Admin</span>' : ''}</td>
                    <td>${esc(u.ip_address || '-')}</td>
                    <td><span class="text-money">${formatMoney(u.vnd)}đ</span></td>
                    <td>${formatMoney(u.tongnap)}đ</td>
                    <td>${u.is_admin == 1 ? '<span class="badge badge-danger">Yes</span>' : '-'}</td>
                    <td>${u.ban == 1 ? '<span class="badge badge-danger">Banned</span>' : '<span class="badge badge-success">OK</span>'}</td>
                    <td>${formatTime(u.last_time_login)}</td>
                </tr>`).join('');
            }
            const p = document.getElementById('loginPagination');
            if (res.pages <= 1) { p.innerHTML = ''; return; }
            let html = '';
            for (let i = Math.max(1, res.page - 3); i <= Math.min(res.pages, res.page + 3); i++) {
                html += `<button class="page-btn ${i === res.page ? 'active' : ''}" onclick="loginPage=${i};loadLoginLogs()">${i}</button>`;
            }
            p.innerHTML = html;
        });
}

function blockIP() {
    const ip = document.getElementById('blockIpInput').value.trim();
    const reason = document.getElementById('blockIpReason').value.trim();
    if (!ip) { showToast('Nhập IP cần block', 'error'); return; }
    const fd = new FormData();
    fd.append('action', 'block_ip');
    fd.append('ip', ip);
    fd.append('reason', reason || 'Admin block');
    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message, res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') {
                document.getElementById('blockIpInput').value = '';
                document.getElementById('blockIpReason').value = '';
                loadSecurity();
            }
        });
}

function unblockIP(ip) {
    if (!confirm('Unblock IP: ' + ip + '?')) return;
    const fd = new FormData();
    fd.append('action', 'unblock_ip');
    fd.append('ip', ip);
    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            showToast(res.message, res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') loadSecurity();
        });
}

document.addEventListener('DOMContentLoaded', loadSecurity);
</script>
</body>
</html>

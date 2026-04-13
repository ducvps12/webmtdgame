<?php
// admin/index.php - Admin Dashboard
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

$current_page = 'dashboard';
$page_title = 'Dashboard';
$admin_username = htmlspecialchars($admin_user['username']);
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
        <a href="/admin" class="nav-item active">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="/admin/operations" class="nav-item">
            <i class="bi bi-diagram-3-fill"></i> Quản trị mở rộng
        </a>

        <div class="nav-section-title">Quản lý</div>
        <a href="/admin/users" class="nav-item">
            <i class="bi bi-people-fill"></i> Người dùng
        </a>
        <a href="/admin/transactions" class="nav-item">
            <i class="bi bi-receipt"></i> Giao dịch Bank
        </a>
        <a href="/admin/feedback" class="nav-item">
            <i class="bi bi-envelope-fill"></i> Góp ý
        </a>
        <a href="/admin/forum" class="nav-item">
            <i class="bi bi-chat-square-text-fill"></i> Diễn đàn
        </a>


        <div class="nav-section-title">Game Server</div>
        <a href="/admin/game-config" class="nav-item">
            <i class="bi bi-controller"></i> Cấu hình Game
        </a>
        <a href="/admin/events" class="nav-item">
            <i class="bi bi-calendar-event-fill"></i> Quản lý Sự Kiện
        </a>

        <div class="nav-section-title">Phân tích</div>
        <a href="/admin/analytics" class="nav-item"><i class="bi bi-bar-chart-line-fill"></i> Báo cáo & Thống kê</a>
        <a href="/admin/payment-flow" class="nav-item"><i class="bi bi-credit-card-2-front"></i> Cơ chế thanh toán</a>

        <div class="nav-section-title">Hệ thống</div>
        <a href="/admin/settings" class="nav-item">
            <i class="bi bi-gear-fill"></i> Cài đặt
        </a>
        <a href="/admin/security" class="nav-item"><i class="bi bi-shield-lock-fill"></i> Bảo mật & Nhật ký</a>
        <a href="/" class="nav-item">
            <i class="bi bi-box-arrow-left"></i> Về trang chủ
        </a>
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
            <button class="btn btn-primary btn-sm" id="btnCheckATM" onclick="checkATM()">
                <i class="bi bi-arrow-repeat"></i> Check ATM
            </button>
            <div class="topbar-btn" onclick="loadStats()" title="Làm mới">
                <i class="bi bi-arrow-clockwise"></i>
            </div>
        </div>
    </header>

    <div class="admin-content">
        <!-- ATM Status -->
        <div class="atm-status" id="atmStatus">
            <div class="pulse-dot"></div>
            <div class="atm-status-text">Auto ATM đang hoạt động — Tài khoản ACB: <strong>24488671</strong></div>
            <button class="btn btn-sm btn-ghost" style="margin-left:auto" onclick="checkATM()">Kiểm tra ngay</button>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-label">Tổng tài khoản</div>
                <div class="stat-value" id="statUsers">-</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
                <div class="stat-label">Tổng doanh thu</div>
                <div class="stat-value" id="statRevenue">-</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon"><i class="bi bi-controller"></i></div>
                <div class="stat-label">Tổng nhân vật</div>
                <div class="stat-value" id="statPlayers">-</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-icon"><i class="bi bi-lightning-fill"></i></div>
                <div class="stat-label">Nạp hôm nay</div>
                <div class="stat-value" id="statToday">-</div>
            </div>
        </div>

        <!-- Revenue Summary -->
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 20px;">
            <div class="stat-card green">
                <div class="stat-icon"><i class="bi bi-calendar-day"></i></div>
                <div class="stat-label">Doanh thu tuần</div>
                <div class="stat-value" id="statWeekly">-</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-icon"><i class="bi bi-calendar-month"></i></div>
                <div class="stat-label">Doanh thu tháng</div>
                <div class="stat-value" id="statMonthly">-</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-icon"><i class="bi bi-person-plus-fill"></i></div>
                <div class="stat-label">Tài khoản mới hôm nay</div>
                <div class="stat-value" id="statNewUsers">-</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid-3" style="margin-bottom:20px;">
            <div class="panel" style="grid-column: span 2;">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-graph-up-arrow" style="margin-right:8px;color:var(--accent-green)"></i>Doanh thu 7 ngày gần nhất</div>
                </div>
                <div class="panel-body padded">
                    <div style="height:280px">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-pie-chart-fill" style="margin-right:8px;color:var(--accent-orange)"></i>Tỷ lệ trạng thái GD (30 ngày)</div>
                </div>
                <div class="panel-body padded">
                    <div style="height:280px">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-3" style="margin-bottom:20px;">
            <div class="panel" style="grid-column: span 2;">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-bar-chart-fill" style="margin-right:8px;color:var(--accent-blue)"></i>Tài khoản mới 7 ngày gần nhất</div>
                </div>
                <div class="panel-body padded">
                    <div style="height:240px">
                        <canvas id="newUsersChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-kanban-fill" style="margin-right:8px;color:var(--accent-purple)"></i>Mở rộng quản trị</div>
                </div>
                <div class="panel-body padded" style="display:grid;gap:10px;">
                    <a class="btn btn-ghost" href="/admin/users"><i class="bi bi-people-fill"></i>&nbsp; Quản lý người dùng</a>
                    <a class="btn btn-ghost" href="/admin/transactions"><i class="bi bi-receipt"></i>&nbsp; Quản lý giao dịch</a>
                    <a class="btn btn-ghost" href="/admin/feedback"><i class="bi bi-chat-left-text"></i>&nbsp; Quản lý góp ý</a>
                    <a class="btn btn-ghost" href="/admin/operations"><i class="bi bi-diagram-3-fill"></i>&nbsp; Trung tâm vận hành</a>
                    <button class="btn btn-ghost" type="button" onclick="showToast('Sắp tới: Nhật ký hệ thống, phân quyền, quản lý nội dung.', 'info')">
                        <i class="bi bi-stars"></i>&nbsp; Tính năng sắp tới
                    </button>
                </div>
            </div>
        </div>

        <!-- Operations Hub: 10 Management Features -->
        <div class="panel" style="margin-bottom:20px;">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-diagram-3-fill" style="margin-right:8px;color:var(--accent-blue)"></i>Trung tâm quản trị dự án (10 tính năng)</div>
            </div>
            <div class="panel-body padded">
                <div class="ops-grid">
                    <div class="ops-card">
                        <div class="ops-label">1. Chế độ bảo trì</div>
                        <div class="ops-value" id="opsMaintenance">-</div>
                        <button class="btn btn-sm btn-ghost" onclick="toggleMaintenance()">Bật/Tắt</button>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">2. Cấu hình ATM API</div>
                        <div class="ops-value" id="opsAtmConfig">-</div>
                        <button class="btn btn-sm btn-ghost" onclick="checkATM()">Kiểm tra ATM</button>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">3. Từ khóa nạp ATM</div>
                        <div class="ops-value" id="opsAtmPrefix">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/settings">Sửa cấu hình</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">4. Góp ý mới chờ xử lý</div>
                        <div class="ops-value" id="opsFeedbackNew">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/feedback">Xử lý ngay</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">5. Giao dịch chờ xử lý</div>
                        <div class="ops-value" id="opsPendingTx">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/transactions">Mở giao dịch</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">6. Giao dịch lỗi 24h</div>
                        <div class="ops-value" id="opsFailed24h">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/transactions">Xem lỗi</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">7. Giao dịch bỏ qua 24h</div>
                        <div class="ops-value" id="opsIgnored24h">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/transactions">Xem bỏ qua</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">8. User hoạt động 24h</div>
                        <div class="ops-value" id="opsActive24h">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/users">Mở users</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">9. User không hoạt động 30d</div>
                        <div class="ops-value" id="opsInactive30d">-</div>
                        <a class="btn btn-sm btn-ghost" href="/admin/users">Theo dõi</a>
                    </div>
                    <div class="ops-card">
                        <div class="ops-label">10. Top nạp toàn hệ thống</div>
                        <div class="ops-value" id="opsTopPayer">-</div>
                        <button class="btn btn-sm btn-ghost" onclick="checkGameServer()">Check Server</button>
                    </div>
                </div>
                <div style="margin-top:10px;font-size:12px;color:var(--text-muted);">
                    Server game: <strong id="opsServerTarget">-</strong>
                </div>
            </div>
        </div>

        <!-- Panels Grid -->
        <div class="grid-3">
            <!-- Recent Transactions -->
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-receipt" style="margin-right:8px;color:var(--accent-blue)"></i>Giao dịch gần đây</div>
                    <a href="/admin/transactions" class="btn btn-sm btn-ghost">Xem tất cả</a>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Số tiền</th>
                                <th>Người gửi</th>
                                <th>Trạng thái</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody id="recentTransactions">
                            <tr><td colspan="4" class="empty-state" style="padding:40px"><div class="spinner"></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Users -->
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-trophy-fill" style="margin-right:8px;color:var(--accent-orange)"></i>Top nạp</div>
                </div>
                <div class="panel-body">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Tổng nạp</th>
                            </tr>
                        </thead>
                        <tbody id="topUsers">
                            <tr><td colspan="2" class="empty-state" style="padding:40px"><div class="spinner"></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-speedometer2" style="margin-right:8px;color:var(--accent-cyan)"></i>KPI nhanh</div>
                </div>
                <div class="panel-body padded">
                    <div style="display:grid;gap:10px;font-size:13px;color:var(--text-secondary)">
                        <div>Tổng giao dịch hôm nay: <strong id="kpiTodayTx" style="color:var(--text-primary)">0</strong></div>
                        <div>Tỷ lệ thành công 30 ngày: <strong id="kpiSuccessRate" style="color:var(--text-primary)">0%</strong></div>
                        <div>TB doanh thu/ngày (7 ngày): <strong id="kpiAvg7" style="color:var(--text-primary)">0đ</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart = null;
let statusChart = null;
let newUsersChart = null;
let latestStats = null;

function formatMoney(n) {
    return new Intl.NumberFormat('vi-VN').format(n);
}

function formatTime(t) {
    if (!t) return '-';
    const d = new Date(t);
    if (isNaN(d.getTime())) return t;
    return d.toLocaleString('vi-VN', {day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'});
}

function statusBadge(s) {
    const map = {
        'success': '<span class="badge badge-success">✓ Thành công</span>',
        'ignored': '<span class="badge badge-muted">— Bỏ qua</span>',
        'failed': '<span class="badge badge-danger">✗ Thất bại</span>',
        'pending': '<span class="badge badge-warning">⏳ Chờ</span>'
    };
    return map[s] || '<span class="badge badge-muted">' + s + '</span>';
}

function showToast(msg, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

function renderCharts(d) {
    if (typeof Chart === 'undefined') return;

    const textColor = '#334155';
    const gridColor = 'rgba(15,23,42,0.12)';

    if (revenueChart) revenueChart.destroy();
    if (statusChart) statusChart.destroy();
    if (newUsersChart) newUsersChart.destroy();

    const revCtx = document.getElementById('revenueChart');
    if (revCtx) {
        revenueChart = new Chart(revCtx, {
            type: 'line',
            data: {
                labels: d.rev7_labels || [],
                datasets: [{
                    label: 'Doanh thu (đ)',
                    data: d.rev7_values || [],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.15)',
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { labels: { color: textColor } } },
                scales: {
                    x: { ticks: { color: textColor }, grid: { color: gridColor } },
                    y: { ticks: { color: textColor }, grid: { color: gridColor } }
                }
            }
        });
    }

    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: d.status_labels || [],
                datasets: [{
                    data: d.status_values || [],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#64748b'],
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { color: textColor } } }
            }
        });
    }

    const userCtx = document.getElementById('newUsersChart');
    if (userCtx) {
        newUsersChart = new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: d.new_users_7_labels || [],
                datasets: [{
                    label: 'User mới',
                    data: d.new_users_7_values || [],
                    backgroundColor: 'rgba(88,117,245,0.7)',
                    borderRadius: 8
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { labels: { color: textColor } } },
                scales: {
                    x: { ticks: { color: textColor }, grid: { color: gridColor } },
                    y: { ticks: { color: textColor }, grid: { color: gridColor } }
                }
            }
        });
    }
}

function loadStats() {
    fetch('/admin/api.php?action=get_stats')
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') return;
            const d = res.data;
            latestStats = d;

            document.getElementById('statUsers').textContent = formatMoney(d.total_users);
            document.getElementById('statRevenue').textContent = formatMoney(d.total_revenue) + 'đ';
            document.getElementById('statPlayers').textContent = formatMoney(d.total_players);
            document.getElementById('statToday').textContent = formatMoney(d.today_revenue) + 'đ';
            document.getElementById('statWeekly').textContent = formatMoney(d.weekly_revenue || 0) + 'đ';
            document.getElementById('statMonthly').textContent = formatMoney(d.monthly_revenue || 0) + 'đ';
            document.getElementById('statNewUsers').textContent = formatMoney(d.new_users_today || 0);
            const totalStatus = (d.status_values || []).reduce((a, b) => a + Number(b || 0), 0);
            const successRate = totalStatus > 0 ? ((Number((d.status_values || [0])[0]) / totalStatus) * 100).toFixed(1) : '0.0';
            const avg7 = (d.rev7_values && d.rev7_values.length) ? Math.round((d.rev7_values.reduce((a, b) => a + Number(b || 0), 0)) / d.rev7_values.length) : 0;
            document.getElementById('kpiTodayTx').textContent = formatMoney(d.today_transactions || 0);
            document.getElementById('kpiSuccessRate').textContent = successRate + '%';
            document.getElementById('kpiAvg7').textContent = formatMoney(avg7) + 'đ';

            // Operations Hub (10 features)
            document.getElementById('opsMaintenance').textContent = Number(d.maintenance_mode || 0) === 1 ? 'Đang bật' : 'Đang tắt';
            document.getElementById('opsAtmConfig').textContent = Number(d.atm_configured || 0) === 1 ? 'Sẵn sàng' : 'Thiếu cấu hình';
            document.getElementById('opsAtmPrefix').textContent = d.atm_prefix || 'chuyen tien';
            document.getElementById('opsFeedbackNew').textContent = formatMoney(d.feedback_new || 0);
            document.getElementById('opsPendingTx').textContent = formatMoney(d.pending_transactions || 0);
            document.getElementById('opsFailed24h').textContent = formatMoney(d.failed_tx_24h || 0);
            document.getElementById('opsIgnored24h').textContent = formatMoney(d.ignored_tx_24h || 0);
            document.getElementById('opsActive24h').textContent = formatMoney(d.active_users_24h || 0);
            document.getElementById('opsInactive30d').textContent = formatMoney(d.inactive_users_30d || 0);
            document.getElementById('opsTopPayer').textContent = (d.top_user_month || '-') + ' • ' + formatMoney(d.top_user_month_amount || 0) + 'đ';
            const target = d.server_ip ? (d.server_ip + ':' + (d.server_port || 14445)) : 'Chưa cấu hình';
            document.getElementById('opsServerTarget').textContent = target;

            // Recent Transactions
            const tbody = document.getElementById('recentTransactions');
            if (d.recent_transactions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px">Chưa có giao dịch</td></tr>';
            } else {
                tbody.innerHTML = d.recent_transactions.map(tx => `
                    <tr>
                        <td><span class="text-money">${formatMoney(tx.amount)}đ</span></td>
                        <td>${tx.sender_name || tx.matched_username || '-'}</td>
                        <td>${statusBadge(tx.status)}</td>
                        <td>${formatTime(tx.created_at)}</td>
                    </tr>
                `).join('');
            }

            // Top Users
            const topBody = document.getElementById('topUsers');
            if (d.top_users.length === 0) {
                topBody.innerHTML = '<tr><td colspan="2" style="text-align:center;color:var(--text-muted);padding:30px">Chưa có dữ liệu</td></tr>';
            } else {
                topBody.innerHTML = d.top_users.map((u, i) => `
                    <tr>
                        <td><span class="text-username">${i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : '  '} ${u.username}</span></td>
                        <td><span class="text-money">${formatMoney(u.tongnap)}đ</span></td>
                    </tr>
                `).join('');
            }

            renderCharts(d);
        })
        .catch(e => console.error('Load stats error:', e));
}

function toggleMaintenance() {
    const enabled = latestStats && Number(latestStats.maintenance_mode || 0) === 1 ? 0 : 1;
    const fd = new FormData();
    fd.append('action', 'set_maintenance');
    fd.append('enabled', String(enabled));

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                loadStats();
            } else {
                showToast(res.message || 'Không thể cập nhật chế độ bảo trì', 'error');
            }
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function checkGameServer() {
    if (!latestStats || !latestStats.server_ip) {
        showToast('Chưa cấu hình GAME_SERVER_IP trong .env', 'error');
        return;
    }
    const ip = encodeURIComponent(latestStats.server_ip);
    const port = encodeURIComponent(String(latestStats.server_port || 14445));
    fetch('/admin/api.php?action=check_game_server&ip=' + ip + '&port=' + port)
        .then(r => r.json())
        .then(res => showToast(res.message || 'Không có phản hồi', res.status === 'success' ? 'success' : 'error'))
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function checkATM() {
    const btn = document.getElementById('btnCheckATM');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Đang kiểm tra...';

    fetch('/admin/api.php?action=check_atm')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                loadStats();
            } else {
                showToast(res.message || 'Lỗi không xác định', 'error');
            }
        })
        .catch(e => showToast('Lỗi kết nối: ' + e.message, 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Check ATM';
        });
}

// Auto check ATM every 30 seconds
let atmInterval = setInterval(checkATM, 30000);

// Load on page ready
document.addEventListener('DOMContentLoaded', () => {
    loadStats();
});
</script>

</body>
</html>


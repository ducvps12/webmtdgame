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
    <title>Admin - Phân tích & Báo cáo</title>
    <link rel="stylesheet" href="/admin/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/images/favicon-48x48.ico">
    <style>
        .an-tabs { display:flex; gap:4px; background:rgba(15,23,42,0.02); border:1px solid var(--border-primary); border-radius:var(--radius); padding:5px; margin-bottom:24px; overflow-x:auto; }
        .an-tab { padding:10px 18px; border-radius:var(--radius-sm); font-size:13px; font-weight:500; color:var(--text-muted); cursor:pointer; border:none; background:none; font-family:inherit; display:flex; align-items:center; gap:6px; white-space:nowrap; transition:all .2s; }
        .an-tab:hover { color:var(--text-secondary); background:rgba(15,23,42,0.03); }
        .an-tab.active { background:var(--accent-blue); color:#fff; font-weight:600; box-shadow:0 2px 10px rgba(88,117,245,0.3); }
        .an-panel { display:none; animation:fadeIn .3s ease; }
        .an-panel.active { display:block; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }

        .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
        .kpi-card { padding:22px; border-radius:var(--radius); border:1px solid var(--border-primary); background:var(--bg-glass); position:relative; overflow:hidden; transition:all .3s; }
        .kpi-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-lg); }
        .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
        .kpi-card.blue::before { background:var(--gradient-1); }
        .kpi-card.green::before { background:var(--gradient-2); }
        .kpi-card.orange::before { background:var(--gradient-3); }
        .kpi-card.purple::before { background:var(--gradient-4); }
        .kpi-label { font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
        .kpi-value { font-size:26px; font-weight:800; letter-spacing:-.5px; }
        .kpi-change { display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:600; margin-top:6px; padding:2px 8px; border-radius:20px; }
        .kpi-change.up { background:rgba(16,185,129,.12); color:var(--accent-green); }
        .kpi-change.down { background:rgba(239,68,68,.12); color:var(--accent-red); }

        .chart-panel { height:320px; }
        .heatmap { display:grid; grid-template-columns:repeat(24,1fr); gap:3px; }
        .heatmap-cell { aspect-ratio:1; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:600; color:rgba(255,255,255,.8); transition:transform .15s; cursor:default; position:relative; }
        .heatmap-cell:hover { transform:scale(1.3); z-index:1; }
        .heatmap-labels { display:flex; justify-content:space-between; margin-top:6px; font-size:10px; color:var(--text-muted); }
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
        <a href="/admin/analytics" class="nav-item active"><i class="bi bi-bar-chart-line-fill"></i> Báo cáo & Thống kê</a>
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
                <div class="topbar-title">Phân tích & Báo cáo</div>
                <div class="topbar-breadcrumb">Admin / Phân tích</div>
            </div>
        </div>
        <div class="topbar-right">
            <button class="btn btn-primary btn-sm" onclick="loadAnalytics()"><i class="bi bi-arrow-clockwise"></i> Làm mới</button>
        </div>
    </header>

    <div class="admin-content">
        <div class="an-tabs">
            <button class="an-tab active" onclick="switchAnTab('revenue',this)"><i class="bi bi-currency-dollar"></i> Doanh thu</button>
            <button class="an-tab" onclick="switchAnTab('users',this)"><i class="bi bi-people"></i> Người chơi</button>
            <button class="an-tab" onclick="switchAnTab('transactions',this)"><i class="bi bi-receipt-cutoff"></i> Giao dịch</button>
        </div>

        <!-- TAB: Revenue -->
        <div id="an-revenue" class="an-panel active">
            <div class="kpi-grid">
                <div class="kpi-card green">
                    <div class="kpi-label">Tuần này</div>
                    <div class="kpi-value" id="kpiThisWeek">-</div>
                    <div class="kpi-change" id="kpiWeekChange">-</div>
                </div>
                <div class="kpi-card blue">
                    <div class="kpi-label">Tuần trước</div>
                    <div class="kpi-value" id="kpiLastWeek">-</div>
                </div>
                <div class="kpi-card orange">
                    <div class="kpi-label">TB / Ngày (30d)</div>
                    <div class="kpi-value" id="kpiAvg30">-</div>
                </div>
                <div class="kpi-card purple">
                    <div class="kpi-label">Tổng 30 ngày</div>
                    <div class="kpi-value" id="kpiTotal30">-</div>
                </div>
            </div>

            <div class="grid-3" style="margin-bottom:22px">
                <div class="panel" style="grid-column:span 2">
                    <div class="panel-header">
                        <div class="panel-title"><i class="bi bi-graph-up-arrow" style="margin-right:8px;color:var(--accent-green)"></i>Doanh thu 30 ngày</div>
                    </div>
                    <div class="panel-body padded"><div class="chart-panel"><canvas id="revChart30"></canvas></div></div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title"><i class="bi bi-trophy-fill" style="margin-right:8px;color:var(--accent-orange)"></i>Top nạp tháng</div>
                    </div>
                    <div class="panel-body">
                        <table class="admin-table">
                            <thead><tr><th>#</th><th>User</th><th>Tổng</th></tr></thead>
                            <tbody id="top10Table"><tr><td colspan="3" style="text-align:center;padding:30px;color:var(--text-muted)">Đang tải...</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-clock" style="margin-right:8px;color:var(--accent-cyan)"></i>Doanh thu theo giờ (Hôm nay)</div>
                </div>
                <div class="panel-body padded">
                    <div class="heatmap" id="heatmap"></div>
                    <div class="heatmap-labels"><span>0h</span><span>6h</span><span>12h</span><span>18h</span><span>23h</span></div>
                </div>
            </div>
        </div>

        <!-- TAB: Users -->
        <div id="an-users" class="an-panel">
            <div class="kpi-grid">
                <div class="kpi-card blue">
                    <div class="kpi-label">Tổng User</div>
                    <div class="kpi-value" id="kuTotal">-</div>
                </div>
                <div class="kpi-card green">
                    <div class="kpi-label">Active 7 ngày</div>
                    <div class="kpi-value" id="kuActive7">-</div>
                </div>
                <div class="kpi-card orange">
                    <div class="kpi-label">Active 30 ngày</div>
                    <div class="kpi-value" id="kuActive30">-</div>
                </div>
                <div class="kpi-card purple">
                    <div class="kpi-label">Retention 7d</div>
                    <div class="kpi-value" id="kuRetention">-</div>
                </div>
            </div>

            <div class="grid-2" style="margin-bottom:22px">
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title"><i class="bi bi-graph-up" style="margin-right:8px;color:var(--accent-blue)"></i>Tăng trưởng User 30 ngày</div>
                    </div>
                    <div class="panel-body padded"><div class="chart-panel"><canvas id="userGrowthChart"></canvas></div></div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title"><i class="bi bi-pie-chart-fill" style="margin-right:8px;color:var(--accent-purple)"></i>Phân bổ User theo Mức nạp</div>
                    </div>
                    <div class="panel-body padded"><div class="chart-panel"><canvas id="userTierChart"></canvas></div></div>
                </div>
            </div>
        </div>

        <!-- TAB: Transactions -->
        <div id="an-transactions" class="an-panel">
            <div class="grid-2" style="margin-bottom:22px">
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title"><i class="bi bi-pie-chart" style="margin-right:8px;color:var(--accent-green)"></i>Trạng thái GD (30 ngày)</div>
                    </div>
                    <div class="panel-body padded"><div class="chart-panel"><canvas id="txStatusChart"></canvas></div></div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title"><i class="bi bi-bar-chart" style="margin-right:8px;color:var(--accent-orange)"></i>Phân bổ Mệnh giá Nạp</div>
                    </div>
                    <div class="panel-body padded"><div class="chart-panel"><canvas id="amtDistChart"></canvas></div></div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="bi bi-graph-up" style="margin-right:8px;color:var(--accent-cyan)"></i>Số lượng GD thành công / ngày (30 ngày)</div>
                </div>
                <div class="panel-body padded"><div class="chart-panel"><canvas id="txCountChart"></canvas></div></div>
            </div>
        </div>
    </div>
</main>

<div class="toast-container" id="toastContainer"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let charts = {};
function formatMoney(n) { return new Intl.NumberFormat('vi-VN').format(Number(n || 0)); }
function esc(v) { const d = document.createElement('div'); d.appendChild(document.createTextNode(v == null ? '' : String(v))); return d.innerHTML; }
function showToast(msg, type = 'info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

function switchAnTab(tab, btn) {
    document.querySelectorAll('.an-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.an-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('an-' + tab).classList.add('active');
    btn.classList.add('active');
}

function destroyChart(key) { if (charts[key]) { charts[key].destroy(); delete charts[key]; } }

function loadAnalytics() {
    fetch('/admin/api.php?action=get_analytics_data')
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') return;
            const d = res.data;
            const tc = '#334155';
            const gc = 'rgba(15,23,42,0.08)';

            // KPIs Revenue
            document.getElementById('kpiThisWeek').textContent = formatMoney(d.this_week) + 'đ';
            document.getElementById('kpiLastWeek').textContent = formatMoney(d.last_week) + 'đ';
            const total30 = (d.rev30_values || []).reduce((a, b) => a + Number(b), 0);
            const avg30 = Math.round(total30 / 30);
            document.getElementById('kpiAvg30').textContent = formatMoney(avg30) + 'đ';
            document.getElementById('kpiTotal30').textContent = formatMoney(total30) + 'đ';

            const weekDiff = d.last_week > 0 ? (((d.this_week - d.last_week) / d.last_week) * 100).toFixed(1) : '0';
            const weekUp = d.this_week >= d.last_week;
            document.getElementById('kpiWeekChange').className = 'kpi-change ' + (weekUp ? 'up' : 'down');
            document.getElementById('kpiWeekChange').innerHTML = `<i class="bi bi-${weekUp ? 'arrow-up' : 'arrow-down'}"></i> ${Math.abs(weekDiff)}% vs tuần trước`;

            // Revenue 30d chart
            destroyChart('rev30');
            charts.rev30 = new Chart(document.getElementById('revChart30'), {
                type: 'line',
                data: { labels: d.rev30_labels, datasets: [{ label: 'Doanh thu (đ)', data: d.rev30_values, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', tension: .35, fill: true, borderWidth: 2, pointRadius: 2 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { labels: { color: tc } } }, scales: { x: { ticks: { color: tc, maxTicksLimit: 10 }, grid: { color: gc } }, y: { ticks: { color: tc }, grid: { color: gc } } } }
            });

            // Top 10
            const top10 = d.top10_month || [];
            document.getElementById('top10Table').innerHTML = top10.length ? top10.map((u, i) =>
                `<tr><td>${i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : (i + 1)}</td><td><span class="text-username">${esc(u.username)}</span></td><td><span class="text-money">${formatMoney(u.total)}đ</span></td></tr>`
            ).join('') : '<tr><td colspan="3" style="text-align:center;padding:20px;color:var(--text-muted)">Chưa có</td></tr>';

            // Heatmap
            const hourly = d.hourly_revenue || new Array(24).fill(0);
            const maxH = Math.max(...hourly, 1);
            const heatEl = document.getElementById('heatmap');
            heatEl.innerHTML = hourly.map((v, i) => {
                const intensity = v / maxH;
                const bg = intensity > 0.7 ? 'rgba(16,185,129,0.9)' : intensity > 0.3 ? 'rgba(245,158,11,0.7)' : intensity > 0 ? 'rgba(88,117,245,0.4)' : 'rgba(15,23,42,0.06)';
                return `<div class="heatmap-cell" style="background:${bg}" title="${i}h: ${formatMoney(v)}đ">${i}</div>`;
            }).join('');

            // User KPIs
            document.getElementById('kuTotal').textContent = formatMoney(d.total_users);
            document.getElementById('kuActive7').textContent = formatMoney(d.active_7d);
            document.getElementById('kuActive30').textContent = formatMoney(d.active_30d);
            const ret = d.total_users > 0 ? ((d.active_7d / d.total_users) * 100).toFixed(1) : '0';
            document.getElementById('kuRetention').textContent = ret + '%';

            // User growth
            destroyChart('userGrowth');
            charts.userGrowth = new Chart(document.getElementById('userGrowthChart'), {
                type: 'bar',
                data: { labels: d.users30_labels, datasets: [{ label: 'User mới', data: d.users30_values, backgroundColor: 'rgba(88,117,245,0.7)', borderRadius: 6 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { labels: { color: tc } } }, scales: { x: { ticks: { color: tc, maxTicksLimit: 10 }, grid: { color: gc } }, y: { ticks: { color: tc }, grid: { color: gc } } } }
            });

            // User tiers
            const tiers = d.user_tiers || {};
            destroyChart('userTier');
            charts.userTier = new Chart(document.getElementById('userTierChart'), {
                type: 'doughnut',
                data: { labels: ['Chưa nạp (0đ)', '<50K', '50K-200K', '>200K'], datasets: [{ data: [tiers.zero, tiers.low, tiers.mid, tiers.high], backgroundColor: ['#64748b', '#f59e0b', '#10b981', '#8b5cf6'], borderWidth: 1 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: tc } } } }
            });

            // TX status
            const txSt = d.tx_status_30d || {};
            destroyChart('txStatus');
            charts.txStatus = new Chart(document.getElementById('txStatusChart'), {
                type: 'doughnut',
                data: { labels: ['Thành công', 'Đang xử lý', 'Thất bại', 'Bỏ qua'], datasets: [{ data: [txSt.success, txSt.pending, txSt.failed, txSt.ignored], backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#64748b'], borderWidth: 1 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: tc } } } }
            });

            // Amount distribution
            const amt = d.amount_distribution || {};
            destroyChart('amtDist');
            charts.amtDist = new Chart(document.getElementById('amtDistChart'), {
                type: 'bar',
                data: { labels: ['<10K', '10K-50K', '50K-200K', '200K-1M', '>1M'], datasets: [{ label: 'Số GD', data: [amt.lt10k, amt['10k_50k'], amt['50k_200k'], amt['200k_1m'], amt.gt1m], backgroundColor: ['#64748b', '#5875f5', '#10b981', '#f59e0b', '#8b5cf6'], borderRadius: 8 }] },
                options: { maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { ticks: { color: tc }, grid: { color: gc } }, y: { ticks: { color: tc }, grid: { display: false } } } }
            });

            // TX count daily
            destroyChart('txCount');
            charts.txCount = new Chart(document.getElementById('txCountChart'), {
                type: 'bar',
                data: { labels: d.rev30_labels, datasets: [{ label: 'Số GD thành công', data: d.rev30_counts, backgroundColor: 'rgba(6,182,212,0.6)', borderRadius: 4 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { labels: { color: tc } } }, scales: { x: { ticks: { color: tc, maxTicksLimit: 10 }, grid: { color: gc } }, y: { ticks: { color: tc }, grid: { color: gc } } } }
            });
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

document.addEventListener('DOMContentLoaded', loadAnalytics);
</script>
</body>
</html>

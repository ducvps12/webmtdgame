<?php
// admin/game-config.php - Game Server Configuration Panel
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

$current_page = 'game-config';
$page_title = 'Cấu Hình Game';
$admin_username = htmlspecialchars($admin_user['username']);

// Read current config
$config_path = 'C:/Users/Administrator/Downloads/NROTFT(2)/data/config/config.properties';
$config = [];
if (file_exists($config_path)) {
    $lines = file($config_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $config[trim($parts[0])] = trim($parts[1]);
        }
    }
}

$exp_rate = intval($config['server.expserver'] ?? 1);
$server_name = $config['server.name'] ?? 'Unknown';
$max_player = intval($config['server.maxplayer'] ?? 10000);
$drop_rate = intval($config['game.droprate'] ?? 1);
$gold_rate = intval($config['game.goldrate'] ?? 1);
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
        /* Quick Actions Grid */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .quick-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius);
            padding: 24px;
            text-align: center;
            transition: all 0.3s var(--cubic-apple);
            position: relative;
            overflow: hidden;
        }
        .quick-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: var(--border-glow);
        }
        .quick-card .qc-icon {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .quick-card .qc-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .quick-card .qc-value {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .quick-card .qc-value.exp { color: var(--accent-orange); }
        .quick-card .qc-value.drop { color: var(--accent-green); }
        .quick-card .qc-value.gold { color: var(--accent-purple); }
        .quick-card .qc-value.player { color: var(--accent-blue); }

        /* EXP Slider */
        .exp-control {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 16px;
        }
        .exp-control input[type="range"] {
            flex: 1;
            height: 8px;
            -webkit-appearance: none;
            appearance: none;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--accent-green) 0%, var(--accent-orange) 50%, var(--accent-red) 100%);
            outline: none;
        }
        .exp-control input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid var(--accent-blue);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            cursor: pointer;
        }
        .exp-display {
            font-size: 28px;
            font-weight: 800;
            color: var(--accent-orange);
            min-width: 60px;
            text-align: center;
        }

        /* Preset buttons */
        .preset-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }
        .preset-btn {
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border-primary);
            background: rgba(15,23,42,0.03);
            color: var(--text-secondary);
            transition: all 0.2s;
            font-family: inherit;
        }
        .preset-btn:hover {
            background: var(--accent-blue);
            color: #fff;
            border-color: var(--accent-blue);
        }
        .preset-btn.active {
            background: var(--accent-blue);
            color: #fff;
            border-color: var(--accent-blue);
            box-shadow: 0 2px 10px rgba(88,117,245,0.3);
        }

        /* GiftCode Table */
        .gc-actions {
            display: flex;
            gap: 8px;
        }
        .gc-code {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: var(--accent-purple);
            letter-spacing: 1px;
        }
        .gc-expired {
            color: var(--accent-red);
            font-weight: 600;
        }
        .gc-active {
            color: var(--accent-green);
            font-weight: 600;
        }

        /* Config Form Grid */
        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }
        .config-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            background: rgba(15,23,42,0.02);
            border: 1px solid var(--border-primary);
        }
        .config-item label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            min-width: 120px;
        }
        .config-item input {
            flex: 1;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-primary);
            background: #fff;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }
        .config-item input:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(88,117,245,0.12);
        }

        /* Dialog form */
        .dialog-form {
            display: grid;
            gap: 16px;
        }
        .dialog-form .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .dialog-form .form-row.full {
            grid-template-columns: 1fr;
        }
        .dialog-form textarea {
            width: 100%;
            min-height: 80px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            background: rgba(15,23,42,0.03);
            border: 1px solid var(--border-primary);
            color: var(--text-primary);
            font-size: 13px;
            font-family: 'Courier New', monospace;
            outline: none;
            resize: vertical;
        }
        .dialog-form textarea:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(88,117,245,0.12);
        }

        /* Status indicator */
        .status-live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(16,185,129,0.12);
            color: var(--accent-green);
        }
        .status-live .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent-green);
            animation: pulse 2s infinite;
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
        <a href="/admin" class="nav-item">
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
        <a href="/admin/game-config" class="nav-item active">
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
            <span class="status-live"><span class="dot"></span> Server: <?php echo htmlspecialchars($server_name); ?></span>
            <div class="topbar-btn" onclick="location.reload()" title="Làm mới">
                <i class="bi bi-arrow-clockwise"></i>
            </div>
        </div>
    </header>

    <div class="admin-content">

        <!-- Quick Stats -->
        <div class="quick-grid">
            <div class="quick-card">
                <div class="qc-icon">⚡</div>
                <div class="qc-label">EXP Rate</div>
                <div class="qc-value exp" id="currentExp">x<?php echo $exp_rate; ?></div>
            </div>
            <div class="quick-card">
                <div class="qc-icon">💎</div>
                <div class="qc-label">Drop Rate</div>
                <div class="qc-value drop">x<?php echo $drop_rate; ?></div>
            </div>
            <div class="quick-card">
                <div class="qc-icon">💰</div>
                <div class="qc-label">Gold Rate</div>
                <div class="qc-value gold">x<?php echo $gold_rate; ?></div>
            </div>
            <div class="quick-card">
                <div class="qc-icon">👥</div>
                <div class="qc-label">Max Player</div>
                <div class="qc-value player"><?php echo number_format($max_player); ?></div>
            </div>
        </div>

        <!-- EXP Rate Control -->
        <div class="panel" style="margin-bottom: 24px;">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-speedometer2" style="margin-right:8px;color:var(--accent-orange)"></i>Điều chỉnh EXP Rate (TNSM)</div>
                <span class="badge badge-warning">Cần restart server</span>
            </div>
            <div class="panel-body padded">
                <div class="exp-control">
                    <input type="range" id="expSlider" min="1" max="100" value="<?php echo $exp_rate; ?>" oninput="updateExpDisplay(this.value)">
                    <div class="exp-display" id="expDisplay">x<?php echo $exp_rate; ?></div>
                </div>
                <div class="preset-btns">
                    <button class="preset-btn <?php echo $exp_rate==1?'active':''; ?>" onclick="setExp(1)">x1 (Bình thường)</button>
                    <button class="preset-btn <?php echo $exp_rate==5?'active':''; ?>" onclick="setExp(5)">x5</button>
                    <button class="preset-btn <?php echo $exp_rate==10?'active':''; ?>" onclick="setExp(10)">x10</button>
                    <button class="preset-btn <?php echo $exp_rate==20?'active':''; ?>" onclick="setExp(20)">x20</button>
                    <button class="preset-btn <?php echo $exp_rate==36?'active':''; ?>" onclick="setExp(36)">x36 (Open Beta)</button>
                    <button class="preset-btn <?php echo $exp_rate==50?'active':''; ?>" onclick="setExp(50)">x50</button>
                    <button class="preset-btn <?php echo $exp_rate==100?'active':''; ?>" onclick="setExp(100)">x100 (Max)</button>
                </div>
                <div style="margin-top: 16px; display: flex; gap: 10px; align-items: center;">
                    <button class="btn btn-primary" onclick="saveExpRate()">
                        <i class="bi bi-save"></i> Lưu EXP Rate
                    </button>
                    <span id="expSaveStatus" style="font-size: 13px; color: var(--text-muted);"></span>
                </div>
            </div>
        </div>

        <!-- Server Config -->
        <div class="panel" style="margin-bottom: 24px;">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-gear-fill" style="margin-right:8px;color:var(--accent-blue)"></i>Cấu hình Server</div>
                <button class="btn btn-sm btn-primary" onclick="saveServerConfig()">
                    <i class="bi bi-save"></i> Lưu tất cả
                </button>
            </div>
            <div class="panel-body padded">
                <div class="config-grid">
                    <div class="config-item">
                        <label>Drop Rate</label>
                        <input type="number" id="cfgDropRate" value="<?php echo $drop_rate; ?>" min="1" max="100">
                    </div>
                    <div class="config-item">
                        <label>Gold Rate</label>
                        <input type="number" id="cfgGoldRate" value="<?php echo $gold_rate; ?>" min="1" max="100">
                    </div>
                    <div class="config-item">
                        <label>Max Player</label>
                        <input type="number" id="cfgMaxPlayer" value="<?php echo $max_player; ?>" min="1" max="100000">
                    </div>
                    <div class="config-item">
                        <label>Max Level</label>
                        <input type="number" id="cfgMaxLevel" value="<?php echo intval($config['game.maxlevel'] ?? 150); ?>" min="1" max="999">
                    </div>
                    <div class="config-item">
                        <label>Server Name</label>
                        <input type="text" id="cfgServerName" value="<?php echo htmlspecialchars($server_name); ?>">
                    </div>
                    <div class="config-item">
                        <label>Boss Respawn (s)</label>
                        <input type="number" id="cfgBossRespawn" value="<?php echo intval($config['game.bossrespawn'] ?? 600); ?>" min="60" max="9999">
                    </div>
                </div>
                <div id="configSaveStatus" style="margin-top: 12px; font-size: 13px;"></div>
            </div>
        </div>

        <!-- GiftCode Management -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-gift-fill" style="margin-right:8px;color:var(--accent-purple)"></i>Quản lý GiftCode</div>
                <div style="display:flex; gap: 8px;">
                    <button class="btn btn-sm btn-primary" onclick="showAddGiftCodeDialog()">
                        <i class="bi bi-plus-lg"></i> Thêm GiftCode
                    </button>
                    <button class="btn btn-sm btn-ghost" onclick="loadGiftCodes()">
                        <i class="bi bi-arrow-clockwise"></i> Làm mới
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Lượt còn</th>
                            <th>Phần thưởng</th>
                            <th>Hết hạn</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="giftcodeBody">
                        <tr><td colspan="7" class="empty-state" style="padding:40px"><div class="spinner"></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Add GiftCode Modal -->
<div class="modal-overlay" id="addGcModal">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title"><i class="bi bi-gift-fill" style="margin-right:8px"></i>Thêm GiftCode mới</div>
            <button class="modal-close" onclick="closeModal('addGcModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="dialog-form">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Mã Code</label>
                        <input type="text" class="form-input" id="gcCode" placeholder="VD: OPENBETA2026" style="text-transform: uppercase;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số lượt dùng</label>
                        <input type="number" class="form-input" id="gcCount" value="9999" min="1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Ngày hết hạn</label>
                        <input type="datetime-local" class="form-input" id="gcExpired">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Loại (0=tất cả, 1=1 lần/acc)</label>
                        <input type="number" class="form-input" id="gcType" value="0" min="0" max="1">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Chi tiết phần thưởng (JSON)</label>
                    <textarea id="gcDetail" placeholder='[{"temp_id":457,"quantity":200,"options":[{"id":30,"param":0}]}]'></textarea>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                        Mỗi item: {"temp_id": ID_ITEM, "quantity": SỐ_LƯỢNG, "options": [{"id": OPTION_ID, "param": GIÁ_TRỊ}]}
                    </div>
                </div>
                <div style="display: flex; gap: 8px; margin-top: 4px;">
                    <button class="btn btn-primary" onclick="saveGiftCode()"><i class="bi bi-save"></i> Tạo GiftCode</button>
                    <button class="btn btn-ghost" onclick="closeModal('addGcModal')">Hủy</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ===== TOAST =====
function showToast(msg, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// ===== EXP RATE =====
function updateExpDisplay(val) {
    document.getElementById('expDisplay').textContent = 'x' + val;
    document.getElementById('currentExp').textContent = 'x' + val;
    // Update preset buttons
    document.querySelectorAll('.preset-btn').forEach(btn => btn.classList.remove('active'));
}

function setExp(val) {
    document.getElementById('expSlider').value = val;
    updateExpDisplay(val);
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.classList.toggle('active', btn.textContent.includes('x' + val));
    });
}

function saveExpRate() {
    const val = document.getElementById('expSlider').value;
    const fd = new FormData();
    fd.append('action', 'save_game_config');
    fd.append('key', 'server.expserver');
    fd.append('value', val);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('✅ Đã lưu EXP Rate x' + val + '. Restart server để áp dụng!', 'success');
                document.getElementById('expSaveStatus').innerHTML = '<span style="color:var(--accent-green)">✅ Đã lưu x' + val + '</span>';
            } else {
                showToast(res.message || 'Lỗi', 'error');
            }
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

// ===== SERVER CONFIG =====
function saveServerConfig() {
    const configs = {
        'game.droprate': document.getElementById('cfgDropRate').value,
        'game.goldrate': document.getElementById('cfgGoldRate').value,
        'server.maxplayer': document.getElementById('cfgMaxPlayer').value,
        'game.maxlevel': document.getElementById('cfgMaxLevel').value,
        'server.name': document.getElementById('cfgServerName').value,
        'game.bossrespawn': document.getElementById('cfgBossRespawn').value
    };

    const fd = new FormData();
    fd.append('action', 'save_game_configs');
    fd.append('configs', JSON.stringify(configs));

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('✅ Đã lưu tất cả cấu hình. Restart server để áp dụng!', 'success');
                document.getElementById('configSaveStatus').innerHTML = '<span style="color:var(--accent-green)">✅ Đã lưu thành công</span>';
            } else {
                showToast(res.message || 'Lỗi', 'error');
            }
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

// ===== GIFTCODE =====
function loadGiftCodes() {
    fetch('/admin/api.php?action=get_giftcodes')
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') return;
            const tbody = document.getElementById('giftcodeBody');
            if (res.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:30px">Chưa có GiftCode nào</td></tr>';
                return;
            }
            tbody.innerHTML = res.data.map(gc => {
                const now = new Date();
                const exp = new Date(gc.expired);
                const isExpired = exp < now;
                const detail = gc.detail || '[]';
                let items = [];
                try {
                    items = JSON.parse(detail);
                } catch(e) {
                    items = [];
                }
                const rewardSummary = items.map(i => `#${i.temp_id} x${i.quantity}`).join(', ') || 'N/A';
                const truncReward = rewardSummary.length > 50 ? rewardSummary.substring(0, 47) + '...' : rewardSummary;

                return `<tr>
                    <td><strong>#${gc.id}</strong></td>
                    <td><span class="gc-code">${gc.code}</span></td>
                    <td><strong>${gc.count_left}</strong></td>
                    <td title="${rewardSummary}" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px;">${truncReward}</td>
                    <td>${formatDate(gc.expired)}</td>
                    <td>${isExpired ? '<span class="gc-expired">⛔ Hết hạn</span>' : '<span class="gc-active">✅ Hoạt động</span>'}</td>
                    <td>
                        <div class="gc-actions">
                            <button class="btn btn-sm btn-danger" onclick="deleteGiftCode(${gc.id}, '${gc.code}')"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        })
        .catch(e => console.error('Load giftcodes error:', e));
}

function formatDate(d) {
    if (!d) return '-';
    const dt = new Date(d);
    return dt.toLocaleDateString('vi-VN', {day:'2-digit', month:'2-digit', year:'numeric'}) + ' ' + dt.toLocaleTimeString('vi-VN', {hour:'2-digit', minute:'2-digit'});
}

function showAddGiftCodeDialog() {
    // Set default expiration to 30 days from now
    const exp = new Date();
    exp.setDate(exp.getDate() + 30);
    document.getElementById('gcExpired').value = exp.toISOString().slice(0, 16);
    document.getElementById('gcCode').value = '';
    document.getElementById('gcCount').value = '9999';
    document.getElementById('gcDetail').value = '';
    document.getElementById('gcType').value = '0';
    document.getElementById('addGcModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function saveGiftCode() {
    const code = document.getElementById('gcCode').value.trim().toUpperCase();
    const count = document.getElementById('gcCount').value;
    const expired = document.getElementById('gcExpired').value;
    const detail = document.getElementById('gcDetail').value.trim();
    const type = document.getElementById('gcType').value;

    if (!code) { showToast('Nhập mã code!', 'error'); return; }
    if (!detail) { showToast('Nhập chi tiết phần thưởng (JSON)!', 'error'); return; }

    // Validate JSON
    try { JSON.parse(detail); } catch(e) { showToast('JSON phần thưởng không hợp lệ!', 'error'); return; }

    const fd = new FormData();
    fd.append('action', 'add_giftcode');
    fd.append('code', code);
    fd.append('count_left', count);
    fd.append('expired', expired);
    fd.append('detail', detail);
    fd.append('type', type);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('✅ Đã tạo GiftCode: ' + code, 'success');
                closeModal('addGcModal');
                loadGiftCodes();
            } else {
                showToast(res.message || 'Lỗi', 'error');
            }
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

function deleteGiftCode(id, code) {
    if (!confirm('Xóa GiftCode "' + code + '"? Hành động này không thể hoàn tác!')) return;

    const fd = new FormData();
    fd.append('action', 'delete_giftcode');
    fd.append('id', id);

    fetch('/admin/api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('✅ Đã xóa GiftCode: ' + code, 'success');
                loadGiftCodes();
            } else {
                showToast(res.message || 'Lỗi', 'error');
            }
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}

// Load on ready
document.addEventListener('DOMContentLoaded', () => {
    loadGiftCodes();
});
</script>

</body>
</html>

<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../env.php';
require_once __DIR__ . '/../connect.php';
if (!isset($_SESSION['username'])) { header('Location: /login'); exit; }
$stmt = $conn->prepare("SELECT id, username, is_admin FROM account WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$admin_user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$admin_user || $admin_user['is_admin'] != 1) { header('Location: /'); exit; }
$admin_username = htmlspecialchars($admin_user['username']);

// Current config
$acb_account = $_ENV_CONFIG['ACB_ACCOUNT'] ?? '';
$acb_token = $_ENV_CONFIG['ACB_TOKEN'] ?? '';
$acb_api_url = $_ENV_CONFIG['ACB_API_URL'] ?? '';
$atm_prefix = $_ENV_CONFIG['ATM_PREFIX'] ?? 'chuyen tien';
$account_holder = $_ENV_CONFIG['ACCOUNT_HOLDER'] ?? '';
$min_deposit = $_ENV_CONFIG['MIN_DEPOSIT'] ?? '10000';
$cron_key = $_ENV_CONFIG['CRON_KEY'] ?? '';
$domain = $_ENV_CONFIG['DOMAIN'] ?? '';

// Last cron run
$last_tx = null;
$r = $conn->query("SELECT created_at FROM bank_transactions ORDER BY created_at DESC LIMIT 1");
if ($r) { $last_tx = $r->fetch_assoc()['created_at'] ?? null; }

// Stats
$r = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='success' THEN 1 ELSE 0 END) as success_count, SUM(CASE WHEN status='ignored' THEN 1 ELSE 0 END) as ignored_count FROM bank_transactions");
$tx_stats = $r ? $r->fetch_assoc() : ['total' => 0, 'success_count' => 0, 'ignored_count' => 0];
$success_rate = $tx_stats['total'] > 0 ? round(($tx_stats['success_count'] / $tx_stats['total']) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Cơ chế Thanh toán</title>
    <link rel="stylesheet" href="/admin/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/images/favicon-48x48.ico">
    <style>
        .flow-card { background:var(--bg-glass); border:1px solid var(--border-primary); border-radius:var(--radius); padding:28px; margin-bottom:22px; }
        .flow-title { font-size:18px; font-weight:700; margin:0 0 16px; display:flex; align-items:center; gap:10px; }
        .flow-title i { font-size:20px; }

        .flow-diagram { display:flex; align-items:center; flex-wrap:wrap; gap:8px; justify-content:center; padding:24px 0; }
        .flow-step { display:flex; flex-direction:column; align-items:center; gap:8px; text-align:center; padding:18px 14px; border-radius:var(--radius-sm); background:rgba(15,23,42,.02); border:1px solid var(--border-primary); min-width:130px; position:relative; transition:all .25s; }
        .flow-step:hover { transform:translateY(-3px); box-shadow:var(--shadow-md); border-color:var(--border-glow); }
        .flow-step .fs-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; color:#fff; flex-shrink:0; }
        .flow-step .fs-icon.blue { background:var(--gradient-1); }
        .flow-step .fs-icon.green { background:var(--gradient-2); }
        .flow-step .fs-icon.orange { background:var(--gradient-3); }
        .flow-step .fs-icon.purple { background:var(--gradient-4); }
        .flow-step .fs-label { font-size:12px; font-weight:700; color:var(--text-primary); }
        .flow-step .fs-sub { font-size:10px; color:var(--text-muted); line-height:1.4; max-width:120px; }
        .flow-arrow { font-size:24px; color:var(--accent-blue); font-weight:900; flex-shrink:0; }

        .config-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:14px; }
        .config-item { background:rgba(15,23,42,.02); border:1px solid var(--border-primary); border-radius:var(--radius-sm); padding:14px; }
        .config-item .ci-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:6px; }
        .config-item .ci-value { font-size:14px; font-weight:600; color:var(--text-primary); word-break:break-all; }
        .config-item .ci-value.masked { filter:blur(4px); user-select:none; cursor:pointer; transition:filter .3s; }
        .config-item .ci-value.masked.revealed { filter:none; user-select:auto; }
        .ci-status { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:600; padding:3px 10px; border-radius:20px; }
        .ci-status.ok { background:rgba(16,185,129,.12); color:var(--accent-green); }
        .ci-status.warn { background:rgba(245,158,11,.12); color:var(--accent-orange); }
        .ci-status.bad { background:rgba(239,68,68,.12); color:var(--accent-red); }

        .compare-table { width:100%; border-collapse:collapse; margin-top:12px; }
        .compare-table th { background:rgba(88,117,245,.08); color:var(--accent-blue); padding:12px 16px; font-size:12px; text-align:left; font-weight:700; text-transform:uppercase; border-bottom:1px solid var(--border-primary); }
        .compare-table td { padding:12px 16px; border-bottom:1px solid var(--border-primary); font-size:13px; color:var(--text-secondary); }
        .compare-table tr:last-child td { border-bottom:none; }

        .faq-item { border:1px solid var(--border-primary); border-radius:var(--radius-sm); margin-bottom:10px; overflow:hidden; transition:all .2s; }
        .faq-q { padding:16px 18px; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; background:rgba(15,23,42,.02); }
        .faq-q:hover { background:rgba(88,117,245,.05); }
        .faq-a { padding:0 18px; max-height:0; overflow:hidden; transition:all .3s ease; font-size:13px; color:var(--text-secondary); line-height:1.7; }
        .faq-item.open .faq-a { padding:14px 18px; max-height:500px; }
        .faq-item.open .faq-q i { transform:rotate(180deg); }

        .stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
        .pf-stat { text-align:center; padding:20px; border-radius:var(--radius-sm); border:1px solid var(--border-primary); background:var(--bg-glass); transition:all .2s; }
        .pf-stat:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
        .pf-stat .ps-val { font-size:28px; font-weight:800; }
        .pf-stat .ps-lbl { font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-top:4px; }
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
        <a href="/admin/payment-flow" class="nav-item active"><i class="bi bi-credit-card-2-front"></i> Cơ chế thanh toán</a>
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
                <div class="topbar-title">Cơ chế Thanh toán</div>
                <div class="topbar-breadcrumb">Admin / Cơ chế Thanh toán</div>
            </div>
        </div>
        <div class="topbar-right">
            <a href="/admin/settings" class="btn btn-ghost btn-sm"><i class="bi bi-gear"></i> Cài đặt ATM</a>
            <button class="btn btn-primary btn-sm" onclick="testAPI()"><i class="bi bi-arrow-clockwise"></i> Test API</button>
        </div>
    </header>

    <div class="admin-content">
        <!-- Stats Row -->
        <div class="stats-row">
            <div class="pf-stat">
                <div class="ps-val"><?php echo number_format($tx_stats['total']); ?></div>
                <div class="ps-lbl">Tổng giao dịch</div>
            </div>
            <div class="pf-stat">
                <div class="ps-val" style="color:var(--accent-green)"><?php echo number_format($tx_stats['success_count']); ?></div>
                <div class="ps-lbl">Thành công</div>
            </div>
            <div class="pf-stat">
                <div class="ps-val" style="color:var(--accent-orange)"><?php echo $success_rate; ?>%</div>
                <div class="ps-lbl">Tỷ lệ match</div>
            </div>
            <div class="pf-stat">
                <div class="ps-val"><?php echo $last_tx ? date('H:i d/m', strtotime($last_tx)) : '-'; ?></div>
                <div class="ps-lbl">GD cuối cùng</div>
            </div>
        </div>

        <!-- Flow Diagram -->
        <div class="flow-card">
            <div class="flow-title"><i class="bi bi-diagram-3-fill" style="color:var(--accent-blue)"></i> Sơ đồ Luồng Nạp ATM Tự động</div>
            <div class="flow-diagram">
                <div class="flow-step">
                    <div class="fs-icon blue"><i class="bi bi-person-fill"></i></div>
                    <div class="fs-label">1. Người chơi</div>
                    <div class="fs-sub">Mở trang /nap-atm<br>Chọn số tiền nạp</div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="fs-icon green"><i class="bi bi-qr-code"></i></div>
                    <div class="fs-label">2. Tạo QR</div>
                    <div class="fs-sub">VietQR API<br>Mã QR chứa STK + nội dung CK</div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="fs-icon orange"><i class="bi bi-bank"></i></div>
                    <div class="fs-label">3. Chuyển khoản</div>
                    <div class="fs-sub">User CK đúng nội dung:<br>"<?php echo htmlspecialchars($atm_prefix); ?> &lt;username&gt;"</div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="fs-icon purple"><i class="bi bi-arrow-repeat"></i></div>
                    <div class="fs-label">4. Cron Check</div>
                    <div class="fs-sub">cron-bank.php<br>Gọi ACB API lấy lịch sử</div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="fs-icon green"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="fs-label">5. Xác nhận</div>
                    <div class="fs-sub">Match nội dung CK<br>→ Tìm username</div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="fs-icon blue"><i class="bi bi-coin"></i></div>
                    <div class="fs-label">6. Cộng VND</div>
                    <div class="fs-sub">UPDATE account<br>SET vnd += amount</div>
                </div>
            </div>
        </div>

        <!-- Config Info -->
        <div class="flow-card">
            <div class="flow-title"><i class="bi bi-gear-fill" style="color:var(--accent-purple)"></i> Cấu hình ATM Hiện tại</div>
            <div class="config-grid">
                <div class="config-item">
                    <div class="ci-label">Ngân hàng</div>
                    <div class="ci-value">ACB - Ngân hàng Á Châu</div>
                </div>
                <div class="config-item">
                    <div class="ci-label">Số tài khoản</div>
                    <div class="ci-value"><?php echo htmlspecialchars($acb_account); ?></div>
                </div>
                <div class="config-item">
                    <div class="ci-label">Chủ tài khoản</div>
                    <div class="ci-value"><?php echo htmlspecialchars($account_holder); ?></div>
                </div>
                <div class="config-item">
                    <div class="ci-label">Từ khóa CK</div>
                    <div class="ci-value"><?php echo htmlspecialchars($atm_prefix); ?> &lt;username&gt;</div>
                </div>
                <div class="config-item">
                    <div class="ci-label">ACB Token</div>
                    <div class="ci-value masked" onclick="this.classList.toggle('revealed')"><?php echo htmlspecialchars($acb_token); ?></div>
                </div>
                <div class="config-item">
                    <div class="ci-label">ACB API URL</div>
                    <div class="ci-value masked" onclick="this.classList.toggle('revealed')"><?php echo htmlspecialchars($acb_api_url); ?></div>
                </div>
                <div class="config-item">
                    <div class="ci-label">Nạp tối thiểu</div>
                    <div class="ci-value"><?php echo number_format((int)$min_deposit); ?> VND</div>
                </div>
                <div class="config-item">
                    <div class="ci-label">API Status</div>
                    <div class="ci-value">
                        <?php if (!empty($acb_token) && !empty($acb_api_url)): ?>
                            <span class="ci-status ok"><i class="bi bi-check-circle-fill"></i> Đã cấu hình</span>
                        <?php else: ?>
                            <span class="ci-status bad"><i class="bi bi-x-circle-fill"></i> Thiếu config</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison -->
        <div class="flow-card">
            <div class="flow-title"><i class="bi bi-arrow-left-right" style="color:var(--accent-orange)"></i> So sánh: Java Server vs PHP Web</div>
            <table class="compare-table">
                <thead>
                    <tr><th>Thành phần</th><th>Java Server (in-game)</th><th>PHP Web (cron-bank)</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>File chính</strong></td>
                        <td>ChuyenKhoanManager.java, Input.java</td>
                        <td>api/cron-bank.php, nap-atm.php</td>
                    </tr>
                    <tr>
                        <td><strong>Bank API</strong></td>
                        <td>Hardcoded MBBank API (cần refactor)</td>
                        <td>Dynamic ACB API từ .env ✅</td>
                    </tr>
                    <tr>
                        <td><strong>Config source</strong></td>
                        <td>config.properties, zalo.properties</td>
                        <td>.env (C:/xampp/htdocs/.env)</td>
                    </tr>
                    <tr>
                        <td><strong>Matching logic</strong></td>
                        <td>So khớp nội dung CK với tên nhân vật</td>
                        <td>Regex: prefix + username → search DB</td>
                    </tr>
                    <tr>
                        <td><strong>QR Generation</strong></td>
                        <td>VietQR API (hardcoded bank code)</td>
                        <td>VietQR API, bank code 970416 (ACB)</td>
                    </tr>
                    <tr>
                        <td><strong>Trạng thái</strong></td>
                        <td><span class="badge badge-warning">Cần cập nhật</span></td>
                        <td><span class="badge badge-success">Hoạt động</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Cron Info -->
        <div class="flow-card">
            <div class="flow-title"><i class="bi bi-arrow-repeat" style="color:var(--accent-cyan)"></i> Cron Job — Tự động kiểm tra giao dịch</div>
            <div class="config-grid">
                <div class="config-item">
                    <div class="ci-label">Cron URL</div>
                    <div class="ci-value" style="font-size:12px">http://<?php echo htmlspecialchars($domain ?: 'localhost'); ?>/api/cron-bank.php?key=<?php echo htmlspecialchars($cron_key); ?></div>
                </div>
                <div class="config-item">
                    <div class="ci-label">Tần suất khuyến nghị</div>
                    <div class="ci-value">Mỗi 30–60 giây</div>
                </div>
                <div class="config-item">
                    <div class="ci-label">Cách thức</div>
                    <div class="ci-value">Windows Task Scheduler / curl cron</div>
                </div>
            </div>
            <div style="margin-top:16px;padding:14px;background:rgba(88,117,245,.05);border:1px solid rgba(88,117,245,.15);border-radius:var(--radius-sm);font-size:13px;color:var(--text-secondary);line-height:1.65">
                <strong style="color:var(--accent-blue)">Quy trình Cron:</strong><br>
                1. Gọi ACB API: <code>GET {ACB_API_URL}/{ACB_TOKEN}</code><br>
                2. Duyệt danh sách giao dịch → Lọc type = "IN" (tiền vào)<br>
                3. Kiểm tra trùng lặp bằng <code>transaction_number</code> trong DB<br>
                4. Parse nội dung CK bằng regex: <code>/<?php echo htmlspecialchars($atm_prefix); ?>\s+([A-Za-z0-9_]+)/i</code><br>
                5. Tìm username khớp trong bảng <code>account</code><br>
                6. Nếu khớp → UPDATE <code>vnd</code> và <code>tongnap</code> → SET status = success<br>
                7. Nếu không khớp → SET status = ignored
            </div>
        </div>

        <!-- FAQ -->
        <div class="flow-card">
            <div class="flow-title"><i class="bi bi-question-circle-fill" style="color:var(--accent-green)"></i> FAQ & Troubleshooting</div>

            <div class="faq-item" onclick="this.classList.toggle('open')">
                <div class="faq-q">User nạp tiền nhưng không nhận được VND? <i class="bi bi-chevron-down" style="transition:transform .3s"></i></div>
                <div class="faq-a">
                    <strong>Nguyên nhân phổ biến:</strong><br>
                    • Sai nội dung chuyển khoản (thiếu prefix hoặc sai username)<br>
                    • Cron chưa chạy hoặc bị lỗi kết nối API<br>
                    • ACB Token hết hạn hoặc sai<br><br>
                    <strong>Xử lý:</strong> Vào <a href="/admin/transactions">Giao dịch Bank</a>, tìm GD bị "ignored", click Edit để sửa matched_username và đổi status → success.
                </div>
            </div>

            <div class="faq-item" onclick="this.classList.toggle('open')">
                <div class="faq-q">Làm sao biết cron đang chạy đúng? <i class="bi bi-chevron-down" style="transition:transform .3s"></i></div>
                <div class="faq-a">
                    • Kiểm tra GD cuối cùng trong DB (hiển thị ở phần thống kê trên)<br>
                    • Click nút "Test API" ở trên để kiểm tra kết nối ACB API<br>
                    • Nếu dùng Task Scheduler, kiểm tra lịch sử chạy trong taskschd.msc
                </div>
            </div>

            <div class="faq-item" onclick="this.classList.toggle('open')">
                <div class="faq-q">ACB Token là gì? Lấy ở đâu? <i class="bi bi-chevron-down" style="transition:transform .3s"></i></div>
                <div class="faq-a">
                    ACB Token là mã xác thực API từ nhà cung cấp SieuThiCode.<br>
                    Đăng ký tại: <code>https://api.sieuthicode.net</code><br>
                    Token được cấp sau khi liên kết tài khoản ngân hàng ACB. Cần gia hạn định kỳ.
                </div>
            </div>

            <div class="faq-item" onclick="this.classList.toggle('open')">
                <div class="faq-q">Có thể cộng VND thủ công không? <i class="bi bi-chevron-down" style="transition:transform .3s"></i></div>
                <div class="faq-a">
                    Có 2 cách:<br>
                    • <strong>Từ Users:</strong> Vào <a href="/admin/users">Quản lý Người dùng</a> → Click Edit → "Cộng thêm VND"<br>
                    • <strong>Từ Transactions:</strong> Vào <a href="/admin/transactions">Giao dịch Bank</a> → Sửa GD bị ignored → Đặt matched_username + status = success
                </div>
            </div>

            <div class="faq-item" onclick="this.classList.toggle('open')">
                <div class="faq-q">Java server còn dùng MBBank — khi nào cần chuyển? <i class="bi bi-chevron-down" style="transition:transform .3s"></i></div>
                <div class="faq-a">
                    Hiện tại Java server (ChuyenKhoanManager.java) vẫn hardcode MBBank API. Nếu chỉ dùng web cron để xử lý nạp ATM thì không ảnh hưởng. <br>
                    Tuy nhiên nếu muốn in-game cũng hỗ trợ nạp, cần refactor Java code sang ACB API + đọc config từ .env hoặc DB.
                </div>
            </div>
        </div>
    </div>
</main>

<div class="toast-container" id="toastContainer"></div>

<script>
function showToast(msg, type = 'info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

function testAPI() {
    showToast('Đang kiểm tra kết nối API...', 'info');
    fetch('/admin/api.php?action=check_atm')
        .then(r => r.json())
        .then(res => {
            showToast(res.message || 'Không có phản hồi', res.status === 'success' ? 'success' : 'error');
        })
        .catch(e => showToast('Lỗi: ' + e.message, 'error'));
}
</script>
</body>
</html>

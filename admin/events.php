<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../connect.php';
if (!isset($_SESSION['username'])) { header('Location: /login'); exit; }
$stmt = $conn->prepare("SELECT id, username, is_admin FROM account WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']); $stmt->execute();
$admin_user = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$admin_user || $admin_user['is_admin'] != 1) { header('Location: /'); exit; }
$admin_username = htmlspecialchars($admin_user['username']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Quản lý Sự Kiện</title>
    <link rel="stylesheet" href="/admin/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/images/favicon-48x48.ico">
    <style>
        .vis-toggle {
            width: 44px; height: 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            position: relative;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
        }
        .vis-toggle.on { background: var(--accent-green); }
        .vis-toggle.off { background: #ccc; }
        .vis-toggle::after {
            content: '';
            width: 18px; height: 18px;
            border-radius: 50%;
            background: #fff;
            position: absolute;
            top: 3px;
            transition: left 0.3s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }
        .vis-toggle.on::after { left: 22px; }
        .vis-toggle.off::after { left: 4px; }
        .featured-star { color: #fbbf24; font-size: 18px; cursor: pointer; }
        .featured-star:hover { transform: scale(1.2); }
        .event-badge-preview {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .color-purple { background: rgba(99,102,241,0.12); color: #6366f1; }
        .color-orange { background: rgba(245,158,11,0.12); color: #d97706; }
        .color-red { background: rgba(239,68,68,0.12); color: #ef4444; }
        .color-blue { background: rgba(59,130,246,0.12); color: #3b82f6; }
        .color-green { background: rgba(16,185,129,0.12); color: #059669; }
        .edit-form { display: grid; gap: 14px; }
        .edit-form .row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .edit-form .row3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
        .edit-form textarea {
            width: 100%; min-height: 120px; padding: 10px 14px;
            border-radius: var(--radius-sm); background: rgba(15,23,42,0.03);
            border: 1px solid var(--border-primary); color: var(--text-primary);
            font-size: 13px; font-family: inherit; outline: none; resize: vertical;
        }
        .edit-form textarea:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(88,117,245,0.12);
        }
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
        <div class="nav-section-title">Game Server</div>
        <a href="/admin/game-config" class="nav-item"><i class="bi bi-controller"></i> Cấu hình Game</a>
        <a href="/admin/events" class="nav-item active"><i class="bi bi-calendar-event-fill"></i> Quản lý Sự Kiện</a>
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
                <div class="topbar-title">Quản lý Sự Kiện</div>
                <div class="topbar-breadcrumb">Admin / Sự Kiện</div>
            </div>
        </div>
        <div class="topbar-right">
            <a href="/su-kien" target="_blank" class="btn btn-sm btn-ghost"><i class="bi bi-eye"></i> Xem trang SK</a>
            <div class="topbar-btn" onclick="location.reload()"><i class="bi bi-arrow-clockwise"></i></div>
        </div>
    </header>

    <div class="admin-content">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-calendar-event-fill" style="margin-right:8px;color:var(--accent-purple)"></i>Danh sách Sự Kiện</div>
                <button class="btn btn-sm btn-primary" onclick="showEventForm()"><i class="bi bi-plus-lg"></i> Thêm Sự Kiện</button>
            </div>
            <div class="panel-body">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:40px">ID</th>
                            <th>Sự Kiện</th>
                            <th>Badge</th>
                            <th>Thời gian</th>
                            <th>GiftCode</th>
                            <th style="width:70px">Nổi bật</th>
                            <th style="width:70px">Hiển thị</th>
                            <th style="width:120px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="eventsBody">
                        <tr><td colspan="8" class="empty-state" style="padding:40px"><div class="spinner"></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Edit Event Modal -->
<div class="modal-overlay" id="eventModal">
    <div class="modal-box" style="max-width:680px; max-height:90vh; overflow-y:auto;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle"><i class="bi bi-calendar-event-fill" style="margin-right:8px"></i>Thêm Sự Kiện</div>
            <button class="modal-close" onclick="closeModal('eventModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="edit-form">
                <input type="hidden" id="evId" value="">
                <div class="form-group">
                    <label class="form-label">Tên Sự Kiện</label>
                    <input type="text" class="form-input" id="evTitle" placeholder="VD: Open Beta Test">
                </div>
                <div class="row2">
                    <div class="form-group">
                        <label class="form-label">Badge Text</label>
                        <input type="text" class="form-input" id="evBadge" placeholder="VD: OPEN BETA TEST">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Badge Color</label>
                        <select class="form-select" id="evColor">
                            <option value="purple">💜 Tím (Purple)</option>
                            <option value="blue">💙 Xanh (Blue)</option>
                            <option value="orange">🧡 Cam (Orange)</option>
                            <option value="red">❤️ Đỏ (Red)</option>
                            <option value="green">💚 Xanh lá (Green)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Mô tả ngắn</label>
                    <input type="text" class="form-input" id="evDesc" placeholder="Mô tả ngắn gọn sự kiện...">
                </div>
                <div class="row2">
                    <div class="form-group">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" class="form-input" id="evStart">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="date" class="form-input" id="evEnd">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nội dung HTML</label>
                    <textarea id="evContent" placeholder="<p>Nội dung sự kiện...</p>"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Highlights JSON</label>
                    <textarea id="evHighlights" style="min-height:60px; font-family: 'Courier New', monospace; font-size: 12px;" placeholder='[{"icon":"⚡","title":"EXP","desc":"Tăng tốc","value":"x36"}]'></textarea>
                </div>
                <div class="row3">
                    <div class="form-group">
                        <label class="form-label">GiftCode</label>
                        <input type="text" class="form-input" id="evGiftcode" placeholder="OPENBETA2026">
                    </div>
                    <div class="form-group">
                        <label class="form-label">GiftCode Desc</label>
                        <input type="text" class="form-input" id="evGiftcodeDesc" placeholder="Mô tả code...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Thứ tự</label>
                        <input type="number" class="form-input" id="evOrder" value="0" min="0">
                    </div>
                </div>
                <div style="display: flex; gap: 8px; margin-top: 4px;">
                    <button class="btn btn-primary" onclick="saveEvent()"><i class="bi bi-save"></i> Lưu</button>
                    <button class="btn btn-ghost" onclick="closeModal('eventModal')">Hủy</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
function showToast(msg, type='info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function loadEvents() {
    fetch('/admin/api.php?action=get_events')
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') return;
            const tbody = document.getElementById('eventsBody');
            if (res.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted)">Chưa có sự kiện nào</td></tr>';
                return;
            }
            tbody.innerHTML = res.data.map(ev => {
                const colorMap = {purple:'color-purple',orange:'color-orange',red:'color-red',blue:'color-blue',green:'color-green'};
                const colorClass = colorMap[ev.badge_color] || 'color-blue';
                const dateStr = ev.date_start ? formatDate(ev.date_start) + ' → ' + formatDate(ev.date_end) : '-';
                return `<tr>
                    <td><strong>#${ev.id}</strong></td>
                    <td><strong>${escHtml(ev.title)}</strong><br><span style="font-size:11px;color:var(--text-muted)">${escHtml(ev.slug)}</span></td>
                    <td><span class="event-badge-preview ${colorClass}">${escHtml(ev.badge_text || ev.title)}</span></td>
                    <td style="font-size:12px">${dateStr}</td>
                    <td>${ev.giftcode ? '<code style="font-weight:700;color:var(--accent-purple)">'+escHtml(ev.giftcode)+'</code>' : '-'}</td>
                    <td><span class="featured-star" onclick="toggleFeatured(${ev.id}, ${ev.is_featured})" title="Nổi bật">${ev.is_featured == 1 ? '⭐' : '☆'}</span></td>
                    <td><button class="vis-toggle ${ev.is_visible == 1 ? 'on' : 'off'}" onclick="toggleVisibility(${ev.id}, ${ev.is_visible})" title="${ev.is_visible == 1 ? 'Đang hiện' : 'Đang ẩn'}"></button></td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <button class="btn btn-sm btn-ghost" onclick="editEvent(${ev.id})"><i class="bi bi-pencil-fill"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteEvent(${ev.id}, '${escHtml(ev.title)}')"><i class="bi bi-trash-fill"></i></button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        });
}

function escHtml(s) { return s ? s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;') : ''; }
function formatDate(d) { return d ? new Date(d).toLocaleDateString('vi-VN', {day:'2-digit',month:'2-digit',year:'numeric'}) : '-'; }

function toggleVisibility(id, current) {
    const fd = new FormData();
    fd.append('action', 'toggle_event_visibility');
    fd.append('id', id);
    fd.append('is_visible', current == 1 ? 0 : 1);
    fetch('/admin/api.php', {method:'POST', body:fd})
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                loadEvents();
            } else showToast(res.message, 'error');
        });
}

function toggleFeatured(id, current) {
    const fd = new FormData();
    fd.append('action', 'toggle_event_featured');
    fd.append('id', id);
    fd.append('is_featured', current == 1 ? 0 : 1);
    fetch('/admin/api.php', {method:'POST', body:fd})
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                loadEvents();
            } else showToast(res.message, 'error');
        });
}

function showEventForm(data) {
    document.getElementById('evId').value = data ? data.id : '';
    document.getElementById('evTitle').value = data ? data.title : '';
    document.getElementById('evBadge').value = data ? data.badge_text : '';
    document.getElementById('evColor').value = data ? data.badge_color : 'blue';
    document.getElementById('evDesc').value = data ? data.description : '';
    document.getElementById('evStart').value = data ? data.date_start : '';
    document.getElementById('evEnd').value = data ? data.date_end : '';
    document.getElementById('evContent').value = data ? data.content : '';
    document.getElementById('evHighlights').value = data ? data.highlights : '';
    document.getElementById('evGiftcode').value = data ? data.giftcode : '';
    document.getElementById('evGiftcodeDesc').value = data ? data.giftcode_desc : '';
    document.getElementById('evOrder').value = data ? data.sort_order : 0;
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-calendar-event-fill" style="margin-right:8px"></i>' + (data ? 'Sửa Sự Kiện #' + data.id : 'Thêm Sự Kiện Mới');
    document.getElementById('eventModal').style.display = 'flex';
}

function editEvent(id) {
    fetch('/admin/api.php?action=get_events')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                const ev = res.data.find(e => e.id == id);
                if (ev) showEventForm(ev);
            }
        });
}

function saveEvent() {
    const fd = new FormData();
    const id = document.getElementById('evId').value;
    fd.append('action', id ? 'update_event' : 'create_event');
    if (id) fd.append('id', id);
    fd.append('title', document.getElementById('evTitle').value);
    fd.append('badge_text', document.getElementById('evBadge').value);
    fd.append('badge_color', document.getElementById('evColor').value);
    fd.append('description', document.getElementById('evDesc').value);
    fd.append('date_start', document.getElementById('evStart').value);
    fd.append('date_end', document.getElementById('evEnd').value);
    fd.append('content', document.getElementById('evContent').value);
    fd.append('highlights', document.getElementById('evHighlights').value);
    fd.append('giftcode', document.getElementById('evGiftcode').value);
    fd.append('giftcode_desc', document.getElementById('evGiftcodeDesc').value);
    fd.append('sort_order', document.getElementById('evOrder').value);

    fetch('/admin/api.php', {method:'POST', body:fd})
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('✅ ' + res.message, 'success');
                closeModal('eventModal');
                loadEvents();
            } else showToast(res.message, 'error');
        });
}

function deleteEvent(id, title) {
    if (!confirm('Xóa sự kiện "' + title + '"? Không thể hoàn tác!')) return;
    const fd = new FormData();
    fd.append('action', 'delete_event');
    fd.append('id', id);
    fetch('/admin/api.php', {method:'POST', body:fd})
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('✅ Đã xóa sự kiện', 'success');
                loadEvents();
            } else showToast(res.message, 'error');
        });
}

document.addEventListener('DOMContentLoaded', loadEvents);
</script>
</body>
</html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/connect.php';
include __DIR__ . '/head.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Góp Ý & Ð? Xu?t - Chú Bé R?ng Online</title>
    <meta name="description" content="G?i góp ý, báo l?i ho?c d? xu?t tính nang m?i cho Chú Bé R?ng Online. Admin luôn l?ng nghe ý ki?n t? c?ng d?ng." />
    <link rel="canonical" href="/gop-y" />
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
    <style>
    .feedback-hero {
        min-height: 40vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 120px 20px 40px;
    }
    .feedback-hero h1 {
        font-size: clamp(32px, 5vw, 52px);
        font-weight: 700;
        letter-spacing: -0.03em;
        margin: 0 0 12px;
        background: linear-gradient(135deg, #0071e3, #5856d6, #d53f8c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: fadeUp 1s var(--cubic-apple) forwards;
        opacity: 0;
    }
    .feedback-hero p {
        font-size: 17px;
        color: var(--text-secondary);
        max-width: 500px;
        animation: fadeUp 1s var(--cubic-apple) 0.15s forwards;
        opacity: 0;
    }

    .feedback-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px 60px;
    }

    /* Form Card */
    .feedback-form-card {
        background: rgba(255,255,255,0.65);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255,255,255,0.8);
        border-radius: 24px;
        padding: 36px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        margin-bottom: 40px;
    }
    .feedback-form-card h2 {
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 24px;
    }
    .form-row {
        margin-bottom: 20px;
    }
    .form-row label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }
    .form-row select,
    .form-row input,
    .form-row textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.1);
        background: rgba(255,255,255,0.8);
        font-size: 15px;
        font-family: inherit;
        outline: none;
        transition: all 0.2s;
    }
    .form-row select:focus,
    .form-row input:focus,
    .form-row textarea:focus {
        border-color: #0071e3;
        box-shadow: 0 0 0 4px rgba(0,113,227,0.1);
    }
    .form-row textarea {
        min-height: 120px;
        resize: vertical;
    }
    .form-row select {
        cursor: pointer;
    }
    .submit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 32px;
        border-radius: 9999px;
        background: #1d1d1f;
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s var(--cubic-apple);
        width: 100%;
    }
    .submit-btn:hover {
        background: #333;
        transform: scale(1.01);
    }
    .submit-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* Login prompt */
    .login-prompt {
        text-align: center;
        padding: 60px 20px;
    }
    .login-prompt p {
        color: var(--text-secondary);
        font-size: 16px;
        margin-bottom: 20px;
    }

    /* Feedback History */
    .history-card {
        background: rgba(255,255,255,0.65);
        backdrop-filter: blur(24px);
        border: 1px solid rgba(255,255,255,0.8);
        border-radius: 24px;
        padding: 36px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.06);
    }
    .history-card h2 {
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 20px;
    }

    .fb-item {
        background: #f5f5f7;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        transition: transform 0.2s;
    }
    .fb-item:hover { transform: translateX(4px); }
    .fb-item:last-child { margin-bottom: 0; }
    .fb-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .fb-title {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
    }
    .fb-meta {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .fb-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-new { background: #dbeafe; color: #1e40af; }
    .badge-read { background: #fef3c7; color: #92400e; }
    .badge-replied { background: #d1fae5; color: #065f46; }
    .badge-bug { background: #fce7f3; color: #9d174d; }
    .badge-suggestion { background: #e0e7ff; color: #3730a3; }
    .badge-other { background: #f3f4f6; color: #374151; }
    .fb-content {
        font-size: 14px;
        color: #555;
        line-height: 1.6;
        margin: 0 0 8px;
    }
    .fb-date {
        font-size: 12px;
        color: var(--text-secondary);
    }
    .fb-reply {
        margin-top: 12px;
        padding: 14px;
        background: #e8f5e9;
        border-radius: 12px;
        border-left: 3px solid #10b981;
    }
    .fb-reply strong {
        font-size: 13px;
        color: #065f46;
        display: block;
        margin-bottom: 4px;
    }
    .fb-reply p {
        font-size: 13px;
        color: #1b5e20;
        margin: 0;
        line-height: 1.5;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
    }
    .empty-state .empty-icon { font-size: 48px; margin-bottom: 12px; }

    /* Toast */
    .toast-msg {
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 14px 24px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 600;
        z-index: 9999;
        opacity: 0;
        transform: translateX(40px);
        transition: all 0.4s var(--cubic-apple);
        max-width: 350px;
    }
    .toast-msg.show {
        opacity: 1;
        transform: translateX(0);
    }
    .toast-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
    .toast-error { background: #fce7f3; color: #9d174d; border: 1px solid #f9a8d4; }

    @media (max-width: 768px) {
        .feedback-form-card, .history-card { padding: 24px; }
    }
    </style>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>

    <!-- Navigation -->
    <?php include __DIR__ . '/nav.php'; ?>

    <!-- Hero -->
    <section class="feedback-hero">
        <h1>?? Hòm Thu Góp Ý</h1>
        <p>Admin luôn l?ng nghe và s?n sàng c?i thi?n game d?a trên ý ki?n c?a c?ng d?ng.</p>
    </section>

    <!-- Toast -->
    <div class="toast-msg" id="toastMsg"></div>

    <div class="feedback-container">
        <?php if (!isset($_SESSION['username'])): ?>
            <!-- Not logged in -->
            <div class="feedback-form-card login-prompt">
                <div style="font-size: 48px; margin-bottom: 16px;">??</div>
                <p>B?n c?n dang nh?p d? g?i góp ý cho admin.</p>
                <a href="/login" class="btn-pill btn-black" style="text-decoration: none;">Ðang Nh?p</a>
            </div>
        <?php else: ?>
            <!-- Feedback Form -->
            <div class="feedback-form-card">
                <h2>?? G?i Góp Ý M?i</h2>
                <form id="feedbackForm" onsubmit="return submitFeedback(event)">
                    <div class="form-row">
                        <label for="fb-category">Lo?i góp ý</label>
                        <select id="fb-category" name="category">
                            <option value="suggestion">?? Ð? xu?t tính nang</option>
                            <option value="bug">?? Báo l?i (Bug)</option>
                            <option value="other">?? Khác</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="fb-title">Tiêu d?</label>
                        <input type="text" id="fb-title" name="title" placeholder="Mô t? ng?n g?n v?n d? ho?c d? xu?t..." maxlength="200" required>
                    </div>
                    <div class="form-row">
                        <label for="fb-content">N?i dung chi ti?t</label>
                        <textarea id="fb-content" name="content" placeholder="Mô t? chi ti?t v?n d?, cách tái t?o l?i, ho?c ý tu?ng c?a b?n..." required></textarea>
                    </div>
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span>??</span> G?i Góp Ý
                    </button>
                </form>
            </div>

            <!-- Feedback History -->
            <div class="history-card">
                <h2>?? L?ch S? Góp Ý C?a B?n</h2>
                <div id="feedbackHistory">
                    <div class="empty-state">
                        <div class="empty-icon">?</div>
                        <p>Ðang t?i...</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/footer.php'; ?>

    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
    <?php if (isset($_SESSION['username'])): ?>
    <script>
    function showToast(message, type) {
        var toast = document.getElementById('toastMsg');
        toast.textContent = message;
        toast.className = 'toast-msg toast-' + type + ' show';
        setTimeout(function() {
            toast.classList.remove('show');
        }, 4000);
    }

    function getCategoryLabel(cat) {
        switch(cat) {
            case 'bug': return '<span class="fb-badge badge-bug">?? Bug</span>';
            case 'suggestion': return '<span class="fb-badge badge-suggestion">?? Ð? xu?t</span>';
            default: return '<span class="fb-badge badge-other">?? Khác</span>';
        }
    }
    function getStatusLabel(status) {
        switch(status) {
            case 'new': return '<span class="fb-badge badge-new">M?i</span>';
            case 'read': return '<span class="fb-badge badge-read">Ðã xem</span>';
            case 'replied': return '<span class="fb-badge badge-replied">Ðã ph?n h?i</span>';
            default: return '';
        }
    }

    function submitFeedback(e) {
        e.preventDefault();
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span>?</span> Ðang g?i...';

        var data = {
            category: document.getElementById('fb-category').value,
            title: document.getElementById('fb-title').value,
            content: document.getElementById('fb-content').value
        };

        fetch('/api/feedback.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(function(res) { return res.json(); })
        .then(function(res) {
            if (res.success) {
                showToast(res.message, 'success');
                document.getElementById('feedbackForm').reset();
                loadHistory();
            } else {
                showToast(res.message, 'error');
            }
        })
        .catch(function() {
            showToast('Có l?i x?y ra, vui lòng th? l?i.', 'error');
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '<span>??</span> G?i Góp Ý';
        });
        return false;
    }

    function loadHistory() {
        fetch('/api/feedback.php')
        .then(function(res) { return res.json(); })
        .then(function(res) {
            var container = document.getElementById('feedbackHistory');
            if (!res.success || !res.data || res.data.length === 0) {
                container.innerHTML = '<div class="empty-state"><div class="empty-icon">??</div><p>Chua có góp ý nào. Hãy g?i góp ý d?u tiên!</p></div>';
                return;
            }
            var html = '';
            res.data.forEach(function(fb) {
                var date = new Date(fb.created_at);
                var dateStr = date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
                html += '<div class="fb-item">';
                html += '  <div class="fb-header">';
                html += '    <h4 class="fb-title">' + escapeHtml(fb.title) + '</h4>';
                html += '    <div class="fb-meta">' + getCategoryLabel(fb.category) + getStatusLabel(fb.status) + '</div>';
                html += '  </div>';
                html += '  <p class="fb-content">' + escapeHtml(fb.content) + '</p>';
                html += '  <span class="fb-date">? ' + dateStr + '</span>';
                if (fb.admin_reply) {
                    html += '  <div class="fb-reply">';
                    html += '    <strong>??? Ph?n h?i t? Admin:</strong>';
                    html += '    <p>' + escapeHtml(fb.admin_reply) + '</p>';
                    html += '  </div>';
                }
                html += '</div>';
            });
            container.innerHTML = html;
        })
        .catch(function() {
            document.getElementById('feedbackHistory').innerHTML = '<div class="empty-state"><div class="empty-icon">??</div><p>Không th? t?i d? li?u.</p></div>';
        });
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // Load on page ready
    document.addEventListener('DOMContentLoaded', loadHistory);
    </script>
    <?php endif; ?>
</body>
</html>



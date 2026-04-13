<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/connect.php';

// Fetch visible events from database
$events = [];
$r = $conn->query("SELECT * FROM events WHERE is_visible = 1 ORDER BY sort_order ASC, id DESC");
if ($r) { while ($row = $r->fetch_assoc()) { $events[] = $row; } }

// Find featured event
$featured = null;
$other_events = [];
foreach ($events as $ev) {
    if ($ev['is_featured'] && !$featured) {
        $featured = $ev;
    } else {
        $other_events[] = $ev;
    }
}
if (!$featured && !empty($events)) {
    $featured = $events[0];
    $other_events = array_slice($events, 1);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S? Ki?n - Chú Bé R?ng Online | mtdgame.com</title>
    <meta name="description" content="S? ki?n m?i nh?t t?i Chú Bé R?ng Online. GiftCode, khuy?n mãi và các s? ki?n d?c bi?t!" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
    <style>
        .event-page { padding-top: 84px; }

        /* Featured Hero */
        .beta-hero {
            text-align: center;
            padding: 50px 20px 40px;
            position: relative;
            overflow: hidden;
        }
        .beta-hero::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(ellipse at center, rgba(99,102,241,0.08) 0%, transparent 70%);
            animation: pulseGlow 4s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.1); }
        }

        .beta-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 24px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
            color: #fff;
        }
        .beta-tag.purple { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .beta-tag.orange { background: linear-gradient(135deg, #f59e0b, #ef4444); }
        .beta-tag.red { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .beta-tag.blue { background: linear-gradient(135deg, #3b82f6, #6366f1); }
        .beta-tag.green { background: linear-gradient(135deg, #10b981, #059669); }

        .pulse-dot {
            width: 8px; height: 8px;
            background: #fff;
            border-radius: 50%;
            animation: pulseDot 2s infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(255,255,255,0.6); }
            50% { opacity: 0.7; box-shadow: 0 0 0 6px rgba(255,255,255,0); }
        }

        .beta-hero h1 {
            font-size: clamp(28px, 5vw, 48px);
            font-weight: 800;
            margin: 20px 0 12px;
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .beta-hero .sub {
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 550px;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
        }
        .date-highlight {
            display: inline-block;
            background: linear-gradient(135deg, rgba(99,102,241,0.08), rgba(139,92,246,0.12));
            border: 1px solid rgba(99,102,241,0.2);
            padding: 10px 24px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 600;
            color: #6366f1;
            position: relative;
            z-index: 1;
        }

        /* Countdown */
        .countdown-row {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 28px;
            position: relative;
            z-index: 1;
        }
        .cd-block {
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            border-radius: 16px;
            padding: 16px 20px;
            min-width: 72px;
            box-shadow: 0 4px 20px rgba(99,102,241,0.3);
        }
        .cd-num {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }
        .cd-label {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            max-width: 800px;
            margin: 0 auto 32px;
            padding: 0 20px;
        }
        .info-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 20px;
            padding: 24px 16px;
            text-align: center;
            transition: all 0.3s;
        }
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .ic-icon { font-size: 36px; margin-bottom: 12px; }
        .info-card h3 { font-size: 15px; font-weight: 700; margin: 0 0 6px; }
        .info-card p { font-size: 13px; color: var(--text-secondary); line-height: 1.4; margin: 0 0 8px; }
        .highlight-value {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Events Container */
        .events-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .event-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 24px;
            overflow: hidden;
            margin-bottom: 24px;
            transition: all 0.3s;
        }
        .event-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        .event-card-body { padding: 28px; position: relative; }
        .event-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }
        .badge-beta { background: rgba(99,102,241,0.12); color: #6366f1; }
        .badge-hot { background: rgba(239,68,68,0.12); color: #ef4444; }
        .badge-special { background: rgba(16,185,129,0.12); color: #059669; }
        .badge-orange { background: rgba(245,158,11,0.12); color: #d97706; }
        .event-card-body h2 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px;
            letter-spacing: -0.01em;
        }
        .event-date {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 12px;
        }
        .event-content {
            font-size: 15px;
            line-height: 1.7;
            color: #333;
        }
        .event-content ul {
            padding-left: 20px;
        }
        .event-content li {
            margin-bottom: 6px;
        }

        /* Gift Code Box */
        .giftcode-box {
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 16px;
        }
        .giftcode-box .gc-label {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
        }
        .giftcode-box .gc-code {
            font-size: 28px;
            font-weight: 800;
            color: #fbbf24;
            letter-spacing: 3px;
            font-family: 'Courier New', monospace;
        }
        .gc-copy-btn {
            background: #fbbf24;
            color: #1e1b4b;
            border: none;
            padding: 12px 28px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .gc-copy-btn:hover { background: #f59e0b; transform: scale(1.05); }
        .gc-copy-btn.copied { background: #10b981; color: #fff; }

        /* Beta Notice */
        .beta-notice {
            background: linear-gradient(135deg, rgba(99,102,241,0.06), rgba(139,92,246,0.06));
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 16px;
            padding: 24px;
            margin-top: 24px;
        }
        .beta-notice h4 { color: #6366f1; font-size: 15px; font-weight: 700; margin: 0 0 8px; }
        .beta-notice p { font-size: 13px; color: #6b7280; line-height: 1.6; margin: 0; }

        /* No events */
        .no-events {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        .no-events i { font-size: 48px; opacity: 0.3; display: block; margin-bottom: 16px; }

        @media (max-width: 768px) {
            .event-card-body { padding: 20px; }
            .giftcode-box { flex-direction: column; text-align: center; }
            .giftcode-box .gc-code { font-size: 22px; letter-spacing: 2px; }
            .countdown-row { gap: 8px; }
            .cd-block { padding: 12px 14px; min-width: 60px; }
            .cd-block .cd-num { font-size: 24px; }
            .info-grid { grid-template-columns: 1fr; padding: 0 16px; }
        }
    </style>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>
    <?php include __DIR__ . '/nav.php'; ?>

    <div class="event-page">

        <?php if ($featured): ?>
        <!-- Featured Event Hero -->
        <section class="beta-hero">
            <div class="beta-tag <?php echo htmlspecialchars($featured['badge_color']); ?>">
                <span class="pulse-dot"></span>
                <?php echo htmlspecialchars($featured['badge_text'] ?: $featured['title']); ?>
            </div>
            <h1><?php echo htmlspecialchars($featured['title']); ?></h1>
            <p class="sub"><?php echo htmlspecialchars($featured['description']); ?></p>
            <?php if ($featured['date_start'] && $featured['date_end']): ?>
            <div class="date-highlight">
                ?? T? ngày <?php echo date('d/m', strtotime($featured['date_start'])); ?> d?n h?t ngày <?php echo date('d/m/Y', strtotime($featured['date_end'])); ?>
            </div>
            <?php endif; ?>

            <?php if ($featured['date_end']): ?>
            <div class="countdown-row" id="betaCountdown">
                <div class="cd-block"><div class="cd-num" id="cd-days">--</div><div class="cd-label">Ngày</div></div>
                <div class="cd-block"><div class="cd-num" id="cd-hours">--</div><div class="cd-label">Gi?</div></div>
                <div class="cd-block"><div class="cd-num" id="cd-mins">--</div><div class="cd-label">Phút</div></div>
                <div class="cd-block"><div class="cd-num" id="cd-secs">--</div><div class="cd-label">Giây</div></div>
            </div>
            <?php endif; ?>
        </section>

        <!-- Info Cards from featured highlights -->
        <?php
        $highlights = json_decode($featured['highlights'] ?? '[]', true);
        if (!empty($highlights)):
        ?>
        <div class="info-grid">
            <?php foreach ($highlights as $h): ?>
            <div class="info-card">
                <div class="ic-icon"><?php echo $h['icon'] ?? '?'; ?></div>
                <h3><?php echo htmlspecialchars($h['title'] ?? ''); ?></h3>
                <p><?php echo htmlspecialchars($h['desc'] ?? ''); ?></p>
                <span class="highlight-value"><?php echo htmlspecialchars($h['value'] ?? ''); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="events-container">
            <!-- Featured Event Detail -->
            <div class="event-card">
                <div class="event-card-body" style="padding-top: 36px;">
                    <span class="event-badge badge-beta">?? <?php echo htmlspecialchars($featured['badge_text']); ?></span>
                    <h2>?? <?php echo htmlspecialchars($featured['title']); ?></h2>
                    <?php if ($featured['date_start']): ?>
                    <p class="event-date">?? <?php echo date('d/m/Y', strtotime($featured['date_start'])); ?> — <?php echo date('d/m/Y', strtotime($featured['date_end'])); ?></p>
                    <?php endif; ?>
                    <div class="event-content"><?php echo $featured['content']; ?></div>

                    <?php if ($featured['giftcode']): ?>
                    <div class="giftcode-box" style="margin-top: 24px;">
                        <div>
                            <div class="gc-label">GIFTCODE</div>
                            <div class="gc-code" id="featured-code"><?php echo htmlspecialchars($featured['giftcode']); ?></div>
                            <?php if ($featured['giftcode_desc']): ?>
                            <div style="color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 6px;"><?php echo htmlspecialchars($featured['giftcode_desc']); ?></div>
                            <?php endif; ?>
                        </div>
                        <button class="gc-copy-btn" onclick="copyCode('featured-code', this)">?? Sao Chép</button>
                    </div>
                    <?php endif; ?>

                    <div class="beta-notice">
                        <h4>?? Luu ý</h4>
                        <p>Ðây là phiên b?n th? nghi?m. D? li?u trong giai do?n này có th? s? du?c reset khi server chính th?c ra m?t. M?i góp ý xin g?i t?i m?c Góp Ý.</p>
                    </div>
                </div>
            </div>

            <?php endif; ?>

            <!-- Other Visible Events -->
            <?php foreach ($other_events as $idx => $ev): ?>
            <div class="event-card">
                <div class="event-card-body">
                    <?php
                    $badge_class = 'badge-beta';
                    if ($ev['badge_color'] === 'red') $badge_class = 'badge-hot';
                    elseif ($ev['badge_color'] === 'orange') $badge_class = 'badge-orange';
                    elseif ($ev['badge_color'] === 'green') $badge_class = 'badge-special';
                    ?>
                    <span class="event-badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($ev['badge_text'] ?: $ev['title']); ?></span>
                    <h2><?php echo htmlspecialchars($ev['title']); ?></h2>
                    <?php if ($ev['date_start']): ?>
                    <p class="event-date">?? <?php echo date('d/m/Y', strtotime($ev['date_start'])); ?> — <?php echo date('d/m/Y', strtotime($ev['date_end'])); ?></p>
                    <?php endif; ?>
                    <div class="event-content"><?php echo $ev['content']; ?></div>

                    <?php if ($ev['giftcode']): ?>
                    <div class="giftcode-box" style="margin-top: 16px;">
                        <div>
                            <div class="gc-label">GIFTCODE</div>
                            <div class="gc-code" id="code-<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['giftcode']); ?></div>
                        </div>
                        <button class="gc-copy-btn" onclick="copyCode('code-<?php echo $ev['id']; ?>', this)">?? Sao Chép</button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($events)): ?>
            <div class="no-events">
                <span style="font-size:48px; opacity:0.3">??</span>
                <p>Chua có s? ki?n nào dang di?n ra. Hãy quay l?i sau!</p>
            </div>
            <?php endif; ?>

        </div>

        <!-- CTA -->
        <section style="text-align:center; padding: 40px 20px 60px; max-width: 600px; margin: 0 auto;">
            <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">S?n Sàng Tham Gia?</h2>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">T?i game ngay và tham gia các s? ki?n h?p d?n!</p>
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="/download/pc.rar" class="btn-pill btn-black">T?i Game cho PC</a>
                <a href="/download/adr.apk" class="btn-pill btn-outline">T?i cho Android</a>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="/view/static/js/antigravity.js?v=2.1"></script>
    <script>
    function copyCode(id, btn) {
        const code = document.getElementById(id).textContent;
        navigator.clipboard.writeText(code).then(() => {
            btn.textContent = '? Ðã Sao Chép!';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.textContent = '?? Sao Chép';
                btn.classList.remove('copied');
            }, 2000);
        });
    }

    <?php if ($featured && $featured['date_end']): ?>
    function updateCountdown() {
        const target = new Date('<?php echo date('Y-m-d', strtotime($featured['date_end'])); ?>T23:59:59+07:00');
        const now = new Date();
        const diff = target - now;
        if (diff <= 0) {
            ['cd-days','cd-hours','cd-mins','cd-secs'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '0';
            });
            return;
        }
        const d = Math.floor(diff / 864e5);
        const h = Math.floor((diff % 864e5) / 36e5);
        const m = Math.floor((diff % 36e5) / 6e4);
        const s = Math.floor((diff % 6e4) / 1000);
        document.getElementById('cd-days').textContent = d;
        document.getElementById('cd-hours').textContent = String(h).padStart(2, '0');
        document.getElementById('cd-mins').textContent = String(m).padStart(2, '0');
        document.getElementById('cd-secs').textContent = String(s).padStart(2, '0');
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
    <?php endif; ?>
    </script>
</body>
</html>

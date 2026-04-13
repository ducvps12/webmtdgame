<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/connect.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đua Top Nạp | Chú Bé Rồng Online</title>
    <meta name="description" content="Bảng xếp hạng đua top nạp Chú Bé Rồng Online — ai là người ủng hộ dự án nhiều nhất?" />
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=3.6">
    <style>
        .ranking-page { padding-top: 100px; padding-bottom: 40px; max-width: 700px; margin: 0 auto; padding-left: 20px; padding-right: 20px; }
        .ranking-page h1 { text-align: center; font-size: 36px; font-weight: 700; letter-spacing: -0.03em; margin-bottom: 8px; }
        .ranking-page .subtitle { text-align: center; color: var(--text-secondary); font-size: 15px; margin-bottom: 36px; }

        .rank-tabs { display: flex; gap: 8px; justify-content: center; margin-bottom: 32px; }
        .rank-tab {
            padding: 8px 20px; border-radius: 999px; font-size: 14px; font-weight: 500;
            background: #f5f5f7; color: var(--text-secondary); cursor: pointer;
            text-decoration: none; transition: all 0.2s;
        }
        .rank-tab:hover { background: #e5e5e7; }
        .rank-tab.active { background: var(--btn-black); color: #fff; }

        .top-podium { display: flex; justify-content: center; gap: 16px; margin-bottom: 40px; flex-wrap: wrap; }
        .podium-card {
            text-align: center; padding: 24px 16px; border-radius: 20px;
            min-width: 160px; transition: transform 0.3s var(--cubic-apple);
        }
        .podium-card:hover { transform: translateY(-6px); }
        .podium-card.gold { background: linear-gradient(135deg, #fef3c7, #fde68a); order: 2; }
        .podium-card.silver { background: linear-gradient(135deg, #f3f4f6, #d1d5db); order: 1; margin-top: 24px; }
        .podium-card.bronze { background: linear-gradient(135deg, #fed7aa, #fdba74); order: 3; margin-top: 24px; }
        .podium-medal { font-size: 36px; margin-bottom: 8px; }
        .podium-name { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .podium-label { font-size: 12px; color: var(--text-secondary); }

        .rank-list { display: flex; flex-direction: column; gap: 6px; }
        .rank-item {
            display: flex; align-items: center; gap: 16px;
            background: #f5f5f7; border-radius: 12px; padding: 12px 20px;
            transition: transform 0.2s;
        }
        .rank-item:hover { transform: translateX(4px); }
        .rank-pos { font-size: 16px; font-weight: 700; min-width: 36px; text-align: center; color: var(--text-secondary); }
        .rank-name { flex: 1; font-weight: 600; font-size: 15px; }

        .update-time { text-align: center; color: var(--text-secondary); font-size: 13px; margin-top: 24px; }

        @media (max-width: 640px) {
            .ranking-page { padding: 80px 12px 40px; }
            .ranking-page h1 { font-size: 26px; }
            .top-podium { gap: 10px; }
            .podium-card { min-width: 100px; padding: 16px 12px; }
        }
    </style>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="ranking-page">
        <h1>Đua Top Nạp</h1>
        <p class="subtitle">Vinh danh những người ủng hộ dự án — số tiền cụ thể được ẩn</p>

        <div class="rank-tabs">
            <a href="/bang-xep-hang" class="rank-tab">Sức Mạnh</a>
            <a href="/top-nap" class="rank-tab active">Đua Top Nạp</a>
            <a href="/ho-so" class="rank-tab">Hồ Sơ</a>
        </div>

        <?php
        $query = "SELECT p.name FROM account a JOIN player p ON a.id = p.account_id WHERE a.is_admin = 0 GROUP BY p.name ORDER BY SUM(a.tongnap) DESC LIMIT 50";
        $result = $conn->query($query);
        $players = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $players[] = $row;
            }
        }
        ?>

        <?php if (count($players) >= 3): ?>
        <!-- Top 3 Podium -->
        <div class="top-podium">
            <div class="podium-card silver">
                <div class="podium-medal">🥈</div>
                <div class="podium-name"><?php echo htmlspecialchars($players[1]['name']); ?></div>
                <div class="podium-label">Hạng 2</div>
            </div>
            <div class="podium-card gold">
                <div class="podium-medal">🥇</div>
                <div class="podium-name"><?php echo htmlspecialchars($players[0]['name']); ?></div>
                <div class="podium-label">Hạng 1</div>
            </div>
            <div class="podium-card bronze">
                <div class="podium-medal">🥉</div>
                <div class="podium-name"><?php echo htmlspecialchars($players[2]['name']); ?></div>
                <div class="podium-label">Hạng 3</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Remaining list (from rank 4+) -->
        <div class="rank-list">
            <?php
            if (count($players) > 3):
                for ($i = 3; $i < count($players); $i++):
            ?>
            <div class="rank-item">
                <div class="rank-pos"><?php echo $i + 1; ?></div>
                <div class="rank-name"><?php echo htmlspecialchars($players[$i]['name']); ?></div>
            </div>
            <?php endfor; endif; ?>

            <?php if (empty($players)): ?>
            <div style="text-align: center; padding: 60px; color: var(--text-secondary);">Chưa có dữ liệu xếp hạng.</div>
            <?php endif; ?>
        </div>

        <p class="update-time">Cập nhật lúc: <?php echo date('H:i d/m/Y'); ?></p>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
</body>
</html>
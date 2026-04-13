<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/connect.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Xếp Hạng Sức Mạnh | Chú Bé Rồng Online</title>
    <meta name="description" content="Top 100 chiến binh mạnh nhất Chú Bé Rồng Online — Bảng xếp hạng sức mạnh cập nhật realtime." />
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=3.6">
    <style>
        .ranking-page { padding-top: 100px; padding-bottom: 40px; max-width: 900px; margin: 0 auto; padding-left: 20px; padding-right: 20px; }
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

        .rank-list { display: flex; flex-direction: column; gap: 8px; }
        .rank-item {
            display: flex; align-items: center; gap: 16px;
            background: #f5f5f7; border-radius: 14px; padding: 14px 20px;
            transition: transform 0.2s var(--cubic-apple), box-shadow 0.2s;
        }
        .rank-item:hover { transform: translateX(4px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .rank-item.top-1 { background: linear-gradient(135deg, #fef3c7, #fde68a); }
        .rank-item.top-2 { background: linear-gradient(135deg, #e5e7eb, #d1d5db); }
        .rank-item.top-3 { background: linear-gradient(135deg, #fed7aa, #fdba74); }

        .rank-pos { font-size: 18px; font-weight: 700; min-width: 40px; text-align: center; }
        .rank-pos.medal { font-size: 24px; }
        .rank-name { flex: 1; font-weight: 600; font-size: 15px; }
        .rank-planet { font-size: 13px; color: var(--text-secondary); min-width: 60px; }
        .rank-power { font-weight: 700; font-size: 15px; color: #e65c00; min-width: 100px; text-align: right; }
        .rank-total { font-weight: 600; font-size: 14px; color: var(--text-secondary); min-width: 100px; text-align: right; }

        .update-time { text-align: center; color: var(--text-secondary); font-size: 13px; margin-top: 24px; }

        @media (max-width: 640px) {
            .ranking-page { padding: 80px 12px 40px; }
            .ranking-page h1 { font-size: 26px; }
            .rank-item { padding: 12px 14px; gap: 10px; }
            .rank-power, .rank-total { font-size: 13px; min-width: auto; }
            .rank-planet { display: none; }
        }
    </style>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="ranking-page">
        <h1>Bảng Xếp Hạng</h1>
        <p class="subtitle">Top 100 chiến binh mạnh nhất — cập nhật realtime</p>

        <div class="rank-tabs">
            <a href="/bang-xep-hang" class="rank-tab active">Sức Mạnh</a>
            <a href="/top-nap" class="rank-tab">Đua Top Nạp</a>
            <a href="/ho-so" class="rank-tab">Hồ Sơ</a>
        </div>

        <div class="rank-list" id="rankList">
            <?php
            $query = "
                SELECT
                    p.name, p.gender,
                    CAST(JSON_UNQUOTE(JSON_EXTRACT(p.data_point, '$[1]')) AS SIGNED) AS player_sm,
                    COALESCE(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(p.pet, '$[1]')), ',', 2), ',', -1) AS SIGNED), 0) AS detu_sm,
                    CAST(JSON_UNQUOTE(JSON_EXTRACT(p.data_point, '$[1]')) AS SIGNED) +
                    COALESCE(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(p.pet, '$[1]')), ',', 2), ',', -1) AS SIGNED), 0) AS tongdiem
                FROM player p
                JOIN account a ON p.account_id = a.id
                WHERE a.is_admin = 0
                ORDER BY tongdiem DESC LIMIT 100
            ";
            $data = mysqli_query($conn, $query);
            $gender_names = ['Trái Đất', 'Namếc', 'Xayda'];

            function fmtPower($val) {
                if ($val > 1000000000) return number_format($val / 1000000000, 1, '.', '') . ' tỷ';
                if ($val > 1000000) return number_format($val / 1000000, 1, '.', '') . ' tr';
                if ($val >= 1000) return number_format($val / 1000, 1, '.', '') . 'k';
                return number_format($val);
            }

            if ($data && mysqli_num_rows($data) > 0):
                $rank = 1;
                while ($row = mysqli_fetch_assoc($data)):
                    $cls = '';
                    $medal = '';
                    if ($rank === 1) { $cls = 'top-1'; $medal = '🥇'; }
                    elseif ($rank === 2) { $cls = 'top-2'; $medal = '🥈'; }
                    elseif ($rank === 3) { $cls = 'top-3'; $medal = '🥉'; }
            ?>
            <div class="rank-item <?php echo $cls; ?>">
                <div class="rank-pos <?php echo $medal ? 'medal' : ''; ?>"><?php echo $medal ?: $rank; ?></div>
                <div class="rank-name"><?php echo htmlspecialchars($row['name']); ?></div>
                <div class="rank-planet"><?php echo $gender_names[$row['gender']] ?? '?'; ?></div>
                <div class="rank-power"><?php echo fmtPower($row['player_sm']); ?></div>
                <div class="rank-total">Tổng: <?php echo fmtPower($row['tongdiem']); ?></div>
            </div>
            <?php $rank++; endwhile; else: ?>
            <div style="text-align: center; padding: 60px; color: var(--text-secondary);">Chưa có dữ liệu xếp hạng.</div>
            <?php endif; ?>
        </div>

        <p class="update-time">Cập nhật lúc: <?php echo date('H:i d/m/Y'); ?></p>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
    <script>
    // Auto-refresh every 30 seconds
    setInterval(() => {
        fetch(location.href)
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.getElementById('rankList');
                if (newList) document.getElementById('rankList').innerHTML = newList.innerHTML;
            });
    }, 30000);
    </script>
    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
</body>
</html>
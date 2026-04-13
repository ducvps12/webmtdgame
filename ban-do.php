<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/connect.php';
include __DIR__ . '/head.php';

// Map zone data
$zones = [
    ['id'=>'dao-kame','name'=>'Đảo Kame','bg'=>'b00','level'=>'1-10','type'=>'hub','desc'=>'Trung tâm chính. NPC: Satan, Shop VIP, Bà Hạt Mít.','screens'=>['2-1','2-3','2-5'],'color'=>'#0071e3'],
    ['id'=>'lang-plant','name'=>'Làng Plant','bg'=>'b10','level'=>'1-15','type'=>'start','desc'=>'Điểm xuất phát hành tinh Trái Đất.','screens'=>[],'color'=>'#8b5cf6'],
    ['id'=>'rung-nguyen-sinh','name'=>'Rừng Nguyên Sinh','bg'=>'b11','level'=>'10-25','type'=>'wild','desc'=>'Khu rừng hoang dã, quái cấp thấp.','screens'=>[],'color'=>'#10b981'],
    ['id'=>'rung-xayda','name'=>'Rừng Thông Xayda','bg'=>'b12','level'=>'20-40','type'=>'wild','desc'=>'Rừng thông cổ thụ, tuyến Xayda.','screens'=>[],'color'=>'#f59e0b'],
    ['id'=>'vach-nui','name'=>'Vách Núi Đen','bg'=>'b13','level'=>'30-50','type'=>'training','desc'=>'Khu huấn luyện trung cấp. Võ đài dưới 150tr SM.','screens'=>[],'color'=>'#6366f1'],
    ['id'=>'tp-vegeta','name'=>'Thành Phố Vegeta','bg'=>'b20','level'=>'40-60','type'=>'city','desc'=>'Boss Hirudegarn 22h. Drop đệ tử Mabư.','screens'=>['4-1','4-2'],'color'=>'#e53e3e'],
    ['id'=>'nappa','name'=>'Nappa','bg'=>'b21','level'=>'50-70','type'=>'boss','desc'=>'Tuyến Boss Nappa — Kuku, Mập Đầu Đinh, Rambo.','screens'=>[],'color'=>'#d53f8c'],
    ['id'=>'cold','name'=>'Cold','bg'=>'b22','level'=>'60-80','type'=>'boss','desc'=>'Boss Cold Form 1. Drop đồ Thần Linh cấp thấp.','screens'=>[],'color'=>'#805ad5'],
    ['id'=>'dao-balec','name'=>'Đảo Balec','bg'=>'b30','level'=>'15-30','type'=>'wild','desc'=>'Đảo phía Bắc, bắt đầu tuyến Bắc-Tây.','screens'=>[],'color'=>'#0ea5e9'],
    ['id'=>'cao-nguyen','name'=>'Cao Nguyên','bg'=>'b31','level'=>'25-45','type'=>'wild','desc'=>'Cao nguyên gió lớn, quái trung cấp.','screens'=>[],'color'=>'#14b8a6'],
    ['id'=>'tp-phia-bac','name'=>'TP Phía Bắc','bg'=>'b32','level'=>'35-55','type'=>'city','desc'=>'Thành phố trung tâm phía Bắc.','screens'=>[],'color'=>'#3b82f6'],
    ['id'=>'hang-bang','name'=>'Hang Băng','bg'=>'b41','level'=>'20-35','type'=>'wild','desc'=>'Hang động băng giá, tuyến Tuyết phía Nam.','screens'=>[],'color'=>'#06b6d4'],
    ['id'=>'nui-tuyet','name'=>'Núi Tuyết','bg'=>'b40','level'=>'25-45','type'=>'wild','desc'=>'Đỉnh núi tuyết phủ trắng.','screens'=>[],'color'=>'#8b5cf6'],
    ['id'=>'nui-khi','name'=>'Núi Khỉ Vàng','bg'=>'b42','level'=>'35-55','type'=>'wild','desc'=>'Núi Khỉ Vàng → Đỏ → Đen. Farm quái liên tục.','screens'=>[],'color'=>'#f97316'],
    ['id'=>'tp-nappa','name'=>'Thành Phố Nappa','bg'=>'b50','level'=>'50-70','type'=>'city','desc'=>'Khu vực Boss tuyến chính.','screens'=>['5-1','5-2'],'color'=>'#ef4444'],
    ['id'=>'dr-kore','name'=>'Dr. Kore Lab','bg'=>'b60','level'=>'Mọi cấp','type'=>'hub','desc'=>'Chế tạo SKH, Giáp, Mề đay, Chân Thiện Tử.','screens'=>['6-1','6-2'],'color'=>'#22c55e'],
    ['id'=>'dau-truong','name'=>'Đấu Trường','bg'=>'b70','level'=>'PvP','type'=>'pvp','desc'=>'PvP Guild vs Guild, 1v1. Phần thưởng hàng tuần.','screens'=>['7-1'],'color'=>'#f43f5e'],
    ['id'=>'thanh-dia','name'=>'Thánh Địa Thời Gian','bg'=>'b80','level'=>'70+','type'=>'endgame','desc'=>'Cổng vào Ma Giới và Địa Ngục.','screens'=>['8-2','8-3'],'color'=>'#a855f7'],
    ['id'=>'ma-gioi','name'=>'Ma Giới','bg'=>'b90','level'=>'80+','type'=>'endgame','desc'=>'Boss Janemba, Demigre. Drop đồ Hủy Diệt.','screens'=>['9-1','9-3'],'color'=>'#dc2626'],
    ['id'=>'yadrat','name'=>'Hành Tinh Yadrat','bg'=>'b100','level'=>'60+','type'=>'special','desc'=>'Hành tinh đặc biệt Yadrat 1-3.','screens'=>['10-1'],'color'=>'#eab308'],
    ['id'=>'vu-tru-11','name'=>'Vũ Trụ 11','bg'=>'b110','level'=>'90+','type'=>'endgame','desc'=>'Trại Huấn Luyện + Căn Cứ Pride Troopers.','screens'=>['11-2','11-3'],'color'=>'#7c3aed'],
    ['id'=>'trai-huan-luyen','name'=>'Trại Huấn Luyện','bg'=>'b111','level'=>'90+','type'=>'endgame','desc'=>'Boss mạnh nhất, trang bị Thiên sứ.','screens'=>[],'color'=>'#2563eb'],
    ['id'=>'pride-troopers','name'=>'Căn Cứ Pride Troopers','bg'=>'b112','level'=>'100+','type'=>'endgame','desc'=>'Endgame cuối cùng. Black Goku, Zamasu.','screens'=>['11-4'],'color'=>'#9333ea'],
    ['id'=>'dong-ho-tg','name'=>'Đồng Hồ Thời Gian','bg'=>'b120','level'=>'50+','type'=>'special','desc'=>'Cổng dẫn vào Thánh Địa Thời Gian.','screens'=>[],'color'=>'#0891b2'],
];

$typeLabels = ['hub'=>'NPC Hub','start'=>'Khởi Đầu','wild'=>'Hoang Dã','training'=>'Huấn Luyện','city'=>'Thành Phố','boss'=>'Boss','pvp'=>'PvP','endgame'=>'Endgame','special'=>'Đặc Biệt'];
$typeIcons = ['hub'=>'🏝️','start'=>'🌱','wild'=>'🌲','training'=>'⚔️','city'=>'🏙️','boss'=>'💀','pvp'=>'🏆','endgame'=>'🔥','special'=>'✨'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bản Đồ Thế Giới Game - Chú Bé Rồng Online</title>
    <meta name="description" content="Khám phá thế giới Chú Bé Rồng Online với bản đồ tương tác — Hình ảnh game thực tế, NPC, Boss và lộ trình chi tiết." />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=3.6">
    <style>
    :root {
        --map-accent: #ff6b35;
        --map-accent-2: #0071e3;
        --map-bg-soft: linear-gradient(180deg, #f7f9ff 0%, #eef2ff 45%, #f9fafc 100%);
        --map-glass: rgba(255,255,255,0.68);
        --map-border: rgba(255,255,255,0.92);
    }

    body.map-page {
        background: var(--map-bg-soft);
    }

    .map-hero {
        position: relative;
        min-height: 64vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
        padding: 120px 20px 58px;
    }
    .map-hero-bg {
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 12%, rgba(0,113,227,0.22), transparent 35%),
            radial-gradient(circle at 82% 22%, rgba(255,107,53,0.20), transparent 34%),
            url('/map-res?bg=map0&res=x4') center/cover no-repeat;
        filter: saturate(1.16);
        z-index: 1;
    }
    .map-hero-bg::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(10,14,22,0.33) 0%, rgba(10,14,22,0.72) 100%);
    }
    .map-hero-content {
        position: relative;
        z-index: 10;
        max-width: 850px;
        animation: fadeUp 0.95s var(--cubic-apple) forwards;
        opacity: 0;
    }
    .map-hero h1 {
        font-size: clamp(36px, 5.2vw, 62px);
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.03em;
        line-height: 1.08;
        margin: 0 0 16px;
        text-shadow: 0 8px 30px rgba(0,0,0,0.38);
    }
    .map-hero h1 span {
        background: linear-gradient(135deg, #ffbf00, #ff7c3a, #ff3d6e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .map-hero .subtitle {
        font-size: clamp(15px, 2vw, 19px);
        color: rgba(255,255,255,0.86);
        margin: 0 auto 30px;
        line-height: 1.62;
        max-width: 680px;
    }
    .hero-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        max-width: 540px;
        margin: 0 auto;
    }
    .hero-stat {
        text-align: center;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.22);
        border-radius: 16px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 14px 10px;
    }
    .hero-stat .num {
        font-size: 30px;
        font-weight: 800;
        color: #fff;
        display: block;
        line-height: 1.1;
    }
    .hero-stat .label {
        font-size: 11px;
        color: rgba(255,255,255,0.7);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .quick-nav-wrap {
        position: sticky;
        top: 66px;
        z-index: 100;
        padding: 14px 20px 0;
    }
    .quick-nav {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 0 auto 34px;
        max-width: 1220px;
        background: var(--map-glass);
        border: 1px solid var(--map-border);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        box-shadow: 0 8px 24px rgba(12, 23, 45, 0.08);
        padding: 10px;
        border-radius: 999px;
    }
    .quick-nav a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 9999px;
        color: #1d1d1f;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.28s var(--cubic-apple);
        border: 1px solid rgba(0,0,0,0.06);
        background: rgba(255,255,255,0.85);
    }
    .quick-nav a:hover {
        color: #fff;
        background: linear-gradient(135deg, var(--map-accent-2), #3b82f6);
        border-color: transparent;
        transform: translateY(-2px);
    }

    .map-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px 60px;
    }
    .section-hdr {
        text-align: center;
        margin-bottom: 28px;
        padding-top: 56px;
    }
    .section-hdr h2 {
        font-size: clamp(24px, 3.5vw, 34px);
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0 0 8px;
        color: #10131a;
    }
    .section-hdr p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
        max-width: 560px;
        margin-inline: auto;
    }

    .route-box,
    .tips-box {
        background: var(--map-glass);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid var(--map-border);
        box-shadow: 0 8px 32px rgba(0,0,0,0.06);
    }

    .zone-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
        margin-bottom: 48px;
    }
    .zone-card {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        aspect-ratio: 16/9;
        cursor: pointer;
        transition: all 0.35s var(--cubic-apple);
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }
    .zone-card:hover {
        transform: translateY(-5px) scale(1.01);
        box-shadow: 0 12px 36px rgba(0,0,0,0.20);
    }
    .zone-card-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        transition: transform 0.5s ease;
        image-rendering: auto;
    }
    .zone-card:hover .zone-card-bg { transform: scale(1.08); }
    .zone-card-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.02) 40%, rgba(0,0,0,0.82) 100%);
        z-index: 2;
    }
    .zone-card-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 12px 14px;
        z-index: 3;
    }
    .zone-card-info h3 {
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 5px;
        text-shadow: 0 1px 6px rgba(0,0,0,0.5);
        line-height: 1.2;
    }
    .zone-card-info .zone-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
    }
    .zone-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 700;
        color: #fff;
    }
    .zone-level {
        background: rgba(255,255,255,0.17);
        backdrop-filter: blur(10px);
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
    }
    .zone-card-glow {
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        border-radius: 16px;
        opacity: 0;
        transition: opacity 0.4s;
        z-index: 0;
    }
    .zone-card:hover .zone-card-glow { opacity: 1; }
    .zone-card-screenshots {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 3;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(10px);
        border-radius: 7px;
        padding: 3px 8px;
        font-size: 10px;
        color: #fff;
        font-weight: 600;
        display: none;
    }
    .zone-card.has-screens .zone-card-screenshots { display: flex; align-items: center; gap: 3px; }

    .route-section { margin-bottom: 48px; }
    .route-box {
        border-radius: 24px;
        padding: 28px;
        margin-bottom: 20px;
        border: 1px solid rgba(130, 146, 178, 0.24);
        background:
            radial-gradient(circle at 92% 10%, rgba(255,255,255,0.72), transparent 40%),
            linear-gradient(150deg, rgba(255,255,255,0.82), rgba(245,248,255,0.84));
        box-shadow: 0 14px 30px rgba(20, 26, 45, 0.06);
    }
    .route-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 16px;
    }
    .route-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .route-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.05em;
    }
    .route-meta {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .route-meta span {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        background: rgba(255,255,255,0.82);
        border: 1px solid rgba(140,160,190,0.34);
        border-radius: 999px;
        padding: 5px 12px;
    }
    .route-flow {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    .route-node {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 15px;
        background: #fff;
        border-radius: 12px;
        font-weight: 600;
        font-size: 13px;
        border: 1px solid rgba(0,0,0,0.08);
        transition: all 0.28s;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        line-height: 1.2;
    }
    .route-node::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: var(--nbg, linear-gradient(135deg, #0ea5e9, #3b82f6));
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 0.3s;
        image-rendering: auto;
    }
    .route-node.start {
        border-color: rgba(139,92,246,0.42);
        box-shadow: 0 0 0 2px rgba(139,92,246,0.08) inset;
    }
    .route-node.end {
        border-color: rgba(16,185,129,0.42);
        box-shadow: 0 0 0 2px rgba(16,185,129,0.08) inset;
    }
    .route-node.branch {
        border-style: dashed;
    }
    .route-node:hover {
        color: #fff;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .route-node:hover::before { opacity: 1; }
    .route-node:hover span {
        position: relative;
        z-index: 1;
        text-shadow: 0 1px 4px rgba(0,0,0,0.7);
    }
    .route-arrow { color: #9ca3af; font-size: 16px; font-weight: 700; }
    .route-branch-label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding-inline: 2px;
    }
    .route-note {
        margin: 12px 0 0;
        font-size: 13px;
        color: #4b5563;
    }
    .route-subflows {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }
    .route-subflow {
        background: rgba(255,255,255,0.62);
        border: 1px solid rgba(245, 158, 11, 0.24);
        border-radius: 14px;
        padding: 14px;
    }
    .route-subtitle {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #92400e;
        margin: 0 0 8px;
    }

    .zone-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.78);
        backdrop-filter: blur(12px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .zone-modal-overlay.active { display: flex; }
    .zone-modal {
        background: #fff;
        border-radius: 24px;
        max-width: 820px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: modalIn 0.35s var(--cubic-apple);
    }
    .zone-modal-hero {
        position: relative;
        height: 280px;
        overflow: hidden;
        border-radius: 24px 24px 0 0;
    }
    .zone-modal-hero img { width: 100%; height: 100%; object-fit: cover; image-rendering: auto; }
    .zone-modal-hero-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, transparent 30%, rgba(0,0,0,0.82) 100%); }
    .zone-modal-hero-info { position: absolute; bottom: 24px; left: 24px; z-index: 2; }
    .zone-modal-hero-info h2 { font-size: 28px; font-weight: 800; color: #fff; margin: 0 0 6px; }
    .zone-modal-hero-info .meta { font-size: 14px; color: rgba(255,255,255,0.84); }
    .zone-modal-close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(10px);
        border: none;
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .zone-modal-body { padding: 28px; }
    .zone-modal-body p { font-size: 15px; line-height: 1.7; color: #3f4758; margin: 0 0 20px; }
    .zone-modal-screens {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 16px;
    }
    .zone-modal-screens img { width: 100%; border-radius: 12px; cursor: pointer; transition: transform 0.3s; }
    .zone-modal-screens img:hover { transform: scale(1.03); }
    .modal-section-title { font-size: 16px; font-weight: 700; margin: 0 0 12px; color: #1d1d1f; }

    .boss-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
    .boss-card {
        background: rgba(255,255,255,0.78);
        border: 1px solid rgba(255,255,255,0.95);
        backdrop-filter: blur(16px);
        border-radius: 18px;
        padding: 24px;
        transition: all 0.3s;
        box-shadow: 0 4px 20px rgba(20,26,45,0.06);
    }
    .boss-card:hover { transform: translateY(-6px); box-shadow: 0 14px 34px rgba(20,26,45,0.12); }
    .boss-card h4 { font-size: 17px; font-weight: 700; margin: 0 0 8px; }
    .boss-card p { font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin: 0 0 10px; }
    .boss-tag { display: inline-block; padding: 3px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; background: #fce7f3; color: #9d174d; }

    .equip-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 14px;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin: 20px 0;
        background: #fff;
    }
    .equip-table th { background: #1d1d1f; color: #fff; padding: 14px 16px; text-align: left; font-weight: 600; font-size: 13px; }
    .equip-table td { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; }
    .equip-table tr:last-child td { border-bottom: none; }
    .equip-table tr:nth-child(even) td { background: #fafafa; }
    .equip-table tr:hover td { background: #f0f4ff; }

    .tips-box {
        border-radius: 18px;
        padding: 28px;
        margin: 24px 0;
        background:
            linear-gradient(145deg, rgba(255,237,184,0.8), rgba(255,228,140,0.82)),
            var(--map-glass);
        border: 1px solid rgba(245,158,11,0.26);
    }
    .tips-box h4 { font-size: 18px; font-weight: 700; margin: 0 0 14px; color: #92400e; }
    .tips-box ul { list-style: none; padding: 0; margin: 0; }
    .tips-box ul li { padding: 8px 0; font-size: 14px; color: #78350f; line-height: 1.6; border-bottom: 1px solid rgba(245,158,11,0.16); }
    .tips-box ul li:last-child { border-bottom: none; }
    .tips-box ul li::before { content: '•'; margin-right: 8px; }

    .map-cta { text-align: center; padding: 56px 20px 20px; max-width: 700px; margin: 0 auto; }
    .map-cta h2 { font-size: 32px; font-weight: 700; margin: 0 0 12px; }
    .map-cta p { font-size: 16px; color: var(--text-secondary); margin: 0 0 24px; }
    .cta-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

    /* Cute GIF mascot floating */
    .map-mascot {
        position: fixed;
        z-index: 90;
        pointer-events: none;
        filter: drop-shadow(0 6px 20px rgba(0,0,0,0.18));
        transition: transform 0.5s var(--cubic-apple);
    }
    .map-mascot img {
        width: 90px;
        height: auto;
        image-rendering: pixelated;
    }
    .map-mascot.left {
        bottom: 30px;
        left: 20px;
        animation: mascotBounceL 3s ease-in-out infinite;
    }
    .map-mascot.right {
        bottom: 30px;
        right: 20px;
        animation: mascotBounceR 3.5s ease-in-out infinite;
    }
    .map-mascot.center {
        bottom: 120px;
        left: 50%;
        transform: translateX(-50%);
        animation: mascotFloat 4s ease-in-out infinite;
    }
    @keyframes mascotBounceL {
        0%, 100% { transform: translateY(0) rotate(-3deg); }
        50% { transform: translateY(-18px) rotate(3deg); }
    }
    @keyframes mascotBounceR {
        0%, 100% { transform: translateY(0) rotate(3deg); }
        50% { transform: translateY(-14px) rotate(-3deg); }
    }
    @keyframes mascotFloat {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50% { transform: translateX(-50%) translateY(-20px); }
    }

    /* Zone card GIF overlay mascot */
    .zone-card-mascot {
        position: absolute;
        bottom: 40px;
        right: 8px;
        z-index: 4;
        width: 48px;
        height: auto;
        image-rendering: pixelated;
        filter: drop-shadow(0 3px 8px rgba(0,0,0,0.35));
        animation: mascotCardBounce 2.5s ease-in-out infinite;
        pointer-events: none;
    }
    @keyframes mascotCardBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    @keyframes fadeUp { from { opacity:0; transform:translateY(30px) } to { opacity:1; transform:translateY(0) } }
    @keyframes modalIn { from { opacity:0; transform:scale(0.95) translateY(20px) } to { opacity:1; transform:scale(1) translateY(0) } }
    .fade-in { opacity: 0; transform: translateY(24px); transition: opacity 0.7s, transform 0.7s var(--cubic-apple); }
    .fade-in.visible { opacity: 1; transform: translateY(0); }

    @media(max-width: 960px) {
        .quick-nav-wrap { top: 58px; padding-top: 10px; }
        .quick-nav { border-radius: 18px; }
        .hero-stats { max-width: 420px; }
        .hero-stat .num { font-size: 25px; }
        .map-mascot img { width: 70px; }
    }
    @media(max-width: 768px) {
        .map-hero { min-height: 58vh; padding-top: 106px; }
        .zone-grid { grid-template-columns: repeat(auto-fill, minmax(180px,1fr)); gap: 10px; }
        .route-box { padding: 24px; }
        .route-head { flex-direction: column; }
        .route-meta { justify-content: flex-start; }
        .route-arrow { font-size: 14px; }
        .zone-modal-hero { height: 200px; }
        .boss-grid { grid-template-columns: 1fr; }
        .hero-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
        .hero-stat { padding: 10px 6px; }
        .map-mascot img { width: 60px; }
        .map-mascot.center { bottom: 80px; }
    }
    @media(max-width: 480px) {
        .quick-nav-wrap { top: 54px; padding-inline: 12px; }
        .quick-nav { padding: 10px; border-radius: 16px; }
        .quick-nav a { padding: 8px 12px; font-size: 12px; }
        .zone-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .route-title { font-size: 18px; }
        .route-flow { gap: 6px; }
        .route-node { padding: 8px 12px; font-size: 12px; }
        .route-meta span { font-size: 11px; }
        .map-mascot.left, .map-mascot.right { display: none; }
        .map-mascot.center img { width: 50px; }
    }
</style>
</head>
<body class="map-page">
    <canvas id="antigravity-particles"></canvas>
    <?php include __DIR__ . '/nav.php'; ?>

    <!-- HERO -->
    <section class="map-hero">
        <div class="map-hero-bg"></div>
        <div class="map-hero-content">
            <h1>Khám Phá <span>Thế Giới Game</span></h1>
            <p class="subtitle">Trải nghiệm toàn bộ bản đồ Chú Bé Rồng Online — từ Đảo Kame đến Vũ Trụ 11, mỗi khu vực là một cuộc phiêu lưu.</p>
            <div class="hero-stats">
                <div class="hero-stat"><span class="num"><?php echo count($zones); ?></span><span class="label">Khu Vực</span></div>
                <div class="hero-stat"><span class="num">4</span><span class="label">Tuyến Đường</span></div>
                <div class="hero-stat"><span class="num">15+</span><span class="label">Boss</span></div>
            </div>
        </div>
    </section>

    <!-- QUICK NAV -->
    <div class="quick-nav fade-in">
        <a href="#zone-explorer">🗺️ Bản Đồ</a>
        <a href="#routes">🛤️ Lộ Trình</a>
        <a href="#boss-system">👹 Boss</a>
        <a href="#equipment">⚔️ Trang Bị</a>
        <a href="#skills">✨ Kỹ Năng</a>
        <a href="#newbie-tips">📖 Newbie</a>
    </div>

    <div class="map-content">
        <!-- ZONE EXPLORER -->
        <section id="zone-explorer" class="fade-in">
            <div class="section-hdr">
                <h2>🗺️ Khám Phá Khu Vực</h2>
                <p>Click vào từng khu vực để xem chi tiết — hình ảnh game thực tế, NPC, Boss và thông tin hữu ích.</p>
            </div>
            <div class="zone-grid">
                <?php
                // Map zone IDs to their mascot GIFs for cute decoration
                $zoneMascots = [
                    'dao-kame' => 'gif_supber_kame.gif',
                    'tp-vegeta' => 'gif_gif_Saiyain.gif',
                    'nappa' => 'gif_gif_Saiyain_VIP.gif',
                    'thanh-dia' => 'gif_maphongba.gif',
                    'ma-gioi' => 'gif_maphongba_VIP.gif',
                    'dau-truong' => 'gif_supber_kame_VIP.gif',
                ];
                foreach($zones as $i => $z):
                    // Check if GIF version exists for the zone background
                    $gifPath = __DIR__ . '/images/map-zones/' . $z['id'] . '.gif';
                    $bgExt = file_exists($gifPath) ? 'gif' : 'png';
                    $hasMascot = isset($zoneMascots[$z['id']]);
                ?>
                <div class="zone-card <?php echo !empty($z['screens'])?'has-screens':''; ?>"
                     data-zone="<?php echo $z['id']; ?>"
                     data-name="<?php echo htmlspecialchars($z['name']); ?>"
                     data-desc="<?php echo htmlspecialchars($z['desc']); ?>"
                     data-level="<?php echo $z['level']; ?>"
                     data-type="<?php echo $z['type']; ?>"
                     data-bg="<?php echo $z['bg']; ?>"
                     data-screens="<?php echo htmlspecialchars(json_encode($z['screens'])); ?>"
                     data-color="<?php echo $z['color']; ?>"
                     data-img-ext="<?php echo $bgExt; ?>"
                     style="animation-delay:<?php echo $i*0.05; ?>s">
                    <div class="zone-card-glow" style="background:linear-gradient(135deg,<?php echo $z['color']; ?>44,transparent);"></div>
                    <div class="zone-card-bg" style="background-image:url('/images/map-zones/<?php echo $z['id']; ?>.<?php echo $bgExt; ?>')"></div>
                    <div class="zone-card-overlay"></div>
                    <div class="zone-card-screenshots">📸 <?php echo count($z['screens']); ?></div>
                    <?php if($hasMascot): ?>
                    <img class="zone-card-mascot" src="/images/gif/<?php echo $zoneMascots[$z['id']]; ?>" alt="mascot" loading="lazy">
                    <?php endif; ?>
                    <div class="zone-card-info">
                        <h3><?php echo $typeIcons[$z['type']]; ?> <?php echo htmlspecialchars($z['name']); ?></h3>
                        <div class="zone-meta">
                            <span class="zone-badge" style="background:<?php echo $z['color']; ?>"><?php echo $typeLabels[$z['type']]; ?></span>
                            <span class="zone-level">Lv. <?php echo $z['level']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ROUTE FLOW -->
        <section id="routes" class="route-section fade-in">
            <div class="section-hdr">
                <h2>🛤️ Lộ Trình Di Chuyển</h2>
                <p>Đi theo từng tuyến để lên cấp ổn định. Mỗi tuyến có mốc cấp độ và nhánh rẽ rõ ràng.</p>
            </div>
            <div class="route-box">
                <div class="route-head">
                    <div class="route-title"><span class="route-badge" style="background:#8b5cf6">HÀNH TINH</span> Con Đường Plant</div>
                    <div class="route-meta"><span>Lv 1-80</span><span>1 nhánh rẽ</span></div>
                </div>
                <div class="route-flow">
                    <span class="route-node start" style="--nbg:url('/map-res?bg=b10&res=x2')"><span>Làng Plant</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Rừng Nguyên Sinh</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Rừng Thông Xayda</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Vách Núi Đen</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>TP Vegeta</span></span><span class="route-arrow">→</span>
                    <span class="route-node branch end"><span>Nappa</span></span><span class="route-branch-label">hoặc</span><span class="route-node branch end"><span>Cold</span></span>
                </div>
                <p class="route-note">Tuyến chính cho người mới: đi thẳng đến TP Vegeta rồi chọn farm boss Nappa hoặc Cold.</p>
            </div>
            <div class="route-box">
                <div class="route-head">
                    <div class="route-title"><span class="route-badge" style="background:#0071e3">PHÍA BẮC</span> Con Đường Phía Bắc - Tây</div>
                    <div class="route-meta"><span>Lv 15-55</span><span>Tuyến phụ mở rộng</span></div>
                </div>
                <div class="route-flow">
                    <span class="route-node start"><span>Đảo Balec</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Cao Nguyên</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>TP Phía Bắc</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>TP Phía Tây</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Ngọn Núi Phía Bắc</span></span><span class="route-arrow">→</span>
                    <span class="route-node end"><span>Thị Trấn Ginder</span></span>
                </div>
                <p class="route-note">Nhánh này phù hợp để đổi không khí farm và gom tài nguyên trước khi vào boss tuyến cao.</p>
            </div>
            <div class="route-box">
                <div class="route-head">
                    <div class="route-title"><span class="route-badge" style="background:#10b981">PHÍA NAM</span> Con Đường Tuyết</div>
                    <div class="route-meta"><span>Lv 20-65</span><span>Tuyến farm dài</span></div>
                </div>
                <div class="route-flow">
                    <span class="route-node start"><span>Hang Băng</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Núi Tuyết</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Cánh Đồng Tuyết</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Núi Khỉ Vàng</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Hang Khỉ Đen</span></span><span class="route-arrow">→</span>
                    <span class="route-node"><span>Khe Núi Tử Thần</span></span><span class="route-arrow">→</span>
                    <span class="route-node end"><span>Đồi Cây Frieza</span></span>
                </div>
                <p class="route-note">Tuyến quái đông, nhịp cày đều, thích hợp cho tổ đội muốn tăng cấp liên tục.</p>
            </div>
            <div class="route-box">
                <div class="route-head">
                    <div class="route-title"><span class="route-badge" style="background:#f59e0b">ĐẶC BIỆT</span> Khu Vực Đặc Biệt</div>
                    <div class="route-meta"><span>Lv 50+</span><span>Endgame</span></div>
                </div>
                <div class="route-subflows">
                    <div class="route-subflow">
                        <div class="route-subtitle">Yadrat</div>
                        <div class="route-flow">
                            <span class="route-node start"><span>Hành Tinh Yadrat</span></span><span class="route-arrow">→</span>
                            <span class="route-node"><span>Yadrat 1</span></span><span class="route-arrow">→</span>
                            <span class="route-node"><span>Yadrat 2</span></span><span class="route-arrow">→</span>
                            <span class="route-node end"><span>Yadrat 3</span></span>
                        </div>
                    </div>
                    <div class="route-subflow">
                        <div class="route-subtitle">Thời Gian</div>
                        <div class="route-flow">
                            <span class="route-node start"><span>Đồng Hồ Thời Gian</span></span><span class="route-arrow">→</span>
                            <span class="route-node"><span>Thánh Địa Thời Gian</span></span><span class="route-arrow">→</span>
                            <span class="route-node branch end"><span>Ma Giới</span></span><span class="route-branch-label">hoặc</span><span class="route-node branch end"><span>Địa Ngục</span></span>
                        </div>
                    </div>
                    <div class="route-subflow">
                        <div class="route-subtitle">Vũ Trụ 11</div>
                        <div class="route-flow">
                            <span class="route-node start"><span>Vũ Trụ 11</span></span><span class="route-arrow">→</span>
                            <span class="route-node"><span>Trại Huấn Luyện</span></span><span class="route-arrow">→</span>
                            <span class="route-node end"><span>Căn Cứ Pride Troopers</span></span>
                        </div>
                    </div>
                </div>
                <p class="route-note">Nhóm khu đặc biệt yêu cầu lực chiến cao, nên đi tổ đội để mở khóa và farm hiệu quả hơn.</p>
            </div>
        </section>
        <!-- BOSS SYSTEM -->
        <section id="boss-system" class="fade-in">
            <div class="section-hdr">
                <h2>👹 Hệ Thống Boss</h2>
                <p>Boss được chia theo tuyến đường, tiến triển từ dễ đến khó theo cốt truyện Dragon Ball.</p>
            </div>
            <div class="boss-grid">
                <div class="boss-card"><h4>🔮 Tuyến Xayda</h4><p><strong>Broly</strong> → <strong>Super Broly</strong>. Drop trứng đệ tử + trang bị hiếm.</p><span class="boss-tag">Boss Path</span></div>
                <div class="boss-card"><h4>💀 Tuyến Nappa</h4><p><strong>Kuku</strong> → <strong>Mập Đầu Đinh</strong> → <strong>Rambo</strong> → <strong>Ginyu</strong> → <strong>Frieza 1-3</strong></p><span class="boss-tag">Boss Path</span></div>
                <div class="boss-card"><h4>🤖 Tuyến Tương Lai</h4><p><strong>Gohan TL</strong> → <strong>Android 19, 20</strong> → <strong>Cell Form 1, 2, Perfect</strong> → <strong>Cell Jr</strong></p><span class="boss-tag">Boss Path</span></div>
                <div class="boss-card"><h4>❄️ Cold &amp; Cooler</h4><p>Cold (Form 1) → Cooler (Form 1-2). Drop đồ Thần Linh cấp thấp.</p><span class="boss-tag">Endgame</span></div>
                <div class="boss-card"><h4>👿 Janemba &amp; Demigre</h4><p>Boss Ma Giới. Drop trang bị Hủy diệt. Yêu cầu sức mạnh cao.</p><span class="boss-tag">Endgame</span></div>
                <div class="boss-card"><h4>⚡ Black Goku &amp; Zamasu</h4><p>Boss endgame cực mạnh. Drop nguyên liệu Thiên sứ và trang bị Phase 2.</p><span class="boss-tag">Top Tier</span></div>
            </div>
        </section>

        <!-- EQUIPMENT -->
        <section id="equipment" class="fade-in">
            <div class="section-hdr">
                <h2>⚔️ Hệ Thống Trang Bị</h2>
                <p>Phase 1 → Phase 2, hệ thống nâng sao từ 1 đến 10.</p>
            </div>
            <div class="route-box">
                <table class="equip-table">
                    <thead><tr><th>Phase</th><th>Loại</th><th>Yêu Cầu</th><th>Nâng Sao</th></tr></thead>
                    <tbody>
                        <tr><td><strong>Phase 1</strong></td><td>Đồ Thần Linh</td><td>Boss Cold/Cooler</td><td>Tối đa 8★</td></tr>
                        <tr><td><strong>Phase 1</strong></td><td>Đồ Hủy Diệt</td><td>Nâng từ Thần Linh</td><td>Tối đa 8★</td></tr>
                        <tr><td><strong>Phase 2</strong></td><td>Đồ Thiên Sứ</td><td>80 tỷ SM + Angel Stones</td><td>Tối đa 10★</td></tr>
                        <tr><td><strong>Phase 2</strong></td><td>Tinh Luyện SPL Cấp 2</td><td>Nguyên liệu endgame</td><td>10★ + Bonus</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- SKILLS -->
        <section id="skills" class="fade-in">
            <div class="section-hdr"><h2>✨ Biến Hình (Bọc Khí)</h2></div>
            <div class="route-box">
                <table class="equip-table">
                    <thead><tr><th>Cấp</th><th>Tên</th><th>Hiệu Ứng</th><th>CD</th></tr></thead>
                    <tbody>
                        <tr><td>1</td><td>Bọc Khí Cơ Bản</td><td>+10% HP, KI, Damage</td><td>6 phút</td></tr>
                        <tr><td>2</td><td>Bọc Khí Nâng Cao</td><td>+20% HP, KI, Damage</td><td>6 phút</td></tr>
                        <tr><td>3</td><td>Super Saiyan / Đại Namếc</td><td>+35% HP, KI, Damage</td><td>6 phút</td></tr>
                        <tr><td>4</td><td>SSJ2 / Siêu Namếc</td><td>+50% HP, KI, Damage</td><td>6 phút</td></tr>
                        <tr><td>5</td><td>Ultra (SSJ3 / God)</td><td>+75% HP, KI, Damage</td><td>6 phút</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- NEWBIE -->
        <section id="newbie-tips" class="fade-in">
            <div class="section-hdr"><h2>📖 Hướng Dẫn Cho Người Mới</h2></div>
            <div class="tips-box">
                <h4>⚡ Mẹo Vàng Cho Newbie</h4>
                <ul>
                    <li>Luôn mang theo Đậu Thần khi farm và đánh Boss</li>
                    <li>Boss Hirudegarn 22h tối — dù yếu cũng nên nhóm lớn</li>
                    <li>Ưu tiên Nhiệm vụ Bulma mỗi ngày</li>
                    <li>Không nâng sao khi chưa có đồ Thần Linh</li>
                    <li>Tham gia Đại Hội Võ Thuật mỗi ngày dù thua</li>
                    <li>Chat hỏi người chơi lâu năm trong bang hội</li>
                </ul>
            </div>
        </section>

        <!-- CTA -->
        <section class="map-cta fade-in">
            <h2>Sẵn Sàng Khám Phá?</h2>
            <p>Tải game và bắt đầu hành trình chinh phục thế giới Chú Bé Rồng Online!</p>
            <div class="cta-btns">
                <a href="/register" class="btn-pill btn-black">Đăng Ký Miễn Phí</a>
                <a href="/download/pc.rar" class="btn-pill btn-outline">Tải Game</a>
            </div>
        </section>
    </div>

    <!-- ZONE MODAL -->
    <div class="zone-modal-overlay" id="zoneModal">
        <div class="zone-modal">
            <div class="zone-modal-hero">
                <img id="modalHeroImg" src="" alt="">
                <div class="zone-modal-hero-overlay"></div>
                <div class="zone-modal-hero-info">
                    <h2 id="modalTitle"></h2>
                    <div class="meta" id="modalMeta"></div>
                </div>
                <button class="zone-modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="zone-modal-body">
                <p id="modalDesc"></p>
                <div id="modalScreens"></div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>
    <script src="/view/static/js/antigravity.js?v=2.1"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var obs = new IntersectionObserver(function(e) {
            e.forEach(function(en) { if(en.isIntersecting) en.target.classList.add('visible'); });
        }, {threshold:0.08});
        document.querySelectorAll('.fade-in').forEach(function(s) { obs.observe(s); });

        document.querySelectorAll('.quick-nav a').forEach(function(l) {
            l.addEventListener('click', function(e) {
                e.preventDefault();
                var t = document.querySelector(this.getAttribute('href'));
                if(t) t.scrollIntoView({behavior:'smooth', block:'start'});
            });
        });

        document.querySelectorAll('.zone-card').forEach(function(card) {
            card.addEventListener('click', function() {
                var d = this.dataset;
                var imgExt = d.imgExt || 'png';
                document.getElementById('modalHeroImg').src = '/images/map-zones/' + d.zone + '.' + imgExt;
                document.getElementById('modalTitle').textContent = d.name;
                document.getElementById('modalMeta').innerHTML = '<span class="zone-badge" style="background:' + d.color + '">' + d.type.toUpperCase() + '</span> &nbsp; Lv. ' + d.level;
                document.getElementById('modalDesc').textContent = d.desc;

                var screens = JSON.parse(d.screens);
                var html = '';
                if(screens.length > 0) {
                    html = '<div class="modal-section-title">Hình Ảnh Trong Game</div><div class="zone-modal-screens">';
                    screens.forEach(function(s) { html += '<img src="/images/screen/' + s + '.png" alt="' + d.name + '" loading="lazy">'; });
                    html += '</div>';
                } else {
                    html = '<div class="modal-section-title">Ảnh Minh Hoạ Map</div><div class="zone-modal-screens"><img src="/images/map-zones/' + d.zone + '.' + imgExt + '" alt="' + d.name + '" loading="lazy"></div>';
                }
                document.getElementById('modalScreens').innerHTML = html;
                document.getElementById('zoneModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });
    });

    function closeModal() {
        document.getElementById('zoneModal').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.getElementById('zoneModal').addEventListener('click', function(e) {
        if(e.target === this) closeModal();
    });
    document.addEventListener('keydown', function(e) { if(e.key === 'Escape') closeModal(); });
    </script>
</body>
</html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới Thiệu - Chú Bé Rồng Online | Game Ngọc Rồng Mobile Huyền Thoại</title>
    <meta name="description" content="Khám phá thế giới Chú Bé Rồng Online – Game nhập vai Dragon Ball huyền thoại. 3 hành tinh, hàng trăm chiêu thức, hệ thống Boss đa dạng, Đại Hội Võ Thuật và nhiều tính năng hấp dẫn đang chờ bạn." />
    <meta name="keywords" content="Chú Bé Rồng Online, ngọc rồng online, game ngoc rong, Dragon Ball, game 7 viên ngọc rồng, game nhập vai, MMORPG mobile, NRO" />
    <meta name="robots" content="INDEX,FOLLOW" />
    <meta property="og:title" content="Giới Thiệu - Chú Bé Rồng Online" />
    <meta property="og:description" content="Game nhập vai Dragon Ball huyền thoại – 3 Hành Tinh, hàng trăm kỹ năng, Boss sử thi và cộng đồng sôi động." />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="/images/dragon_ball_showcase.png" />
    <link rel="canonical" href="/gioi-thieu" />

    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=3.6">

    <style>
        /* ===== INTRO PAGE SPECIFIC STYLES ===== */
        .intro-hero {
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 20px 60px;
            position: relative;
            overflow: hidden;
        }
        .intro-hero-content {
            z-index: 10;
            max-width: 800px;
            animation: fadeUp 1s var(--cubic-apple) forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        .intro-hero h1 {
            font-size: clamp(36px, 5vw, 64px);
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin: 0 0 16px;
            background: linear-gradient(135deg, #ff6b35, #e53e3e, #d53f8c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .intro-hero .subtitle {
            font-size: clamp(16px, 2vw, 22px);
            color: var(--text-secondary);
            margin: 0 0 32px;
            line-height: 1.5;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Glass Content Card */
        .content-section {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }
        .glass-content {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
            margin-bottom: 40px;
        }
        .glass-content h2 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 20px;
            letter-spacing: -0.02em;
        }
        .glass-content h3 {
            font-size: 20px;
            font-weight: 600;
            margin: 30px 0 12px;
            color: #0071e3;
        }
        .glass-content p, .glass-content li {
            font-size: 15px;
            line-height: 1.7;
            color: #444;
        }
        .glass-content ul {
            padding-left: 20px;
        }
        .glass-content ul li {
            margin-bottom: 6px;
        }

        /* Planet Cards (3 columns) */
        .planet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin: 30px 0;
        }
        .planet-card {
            background: #f5f5f7;
            border-radius: 20px;
            padding: 28px;
            text-align: center;
            transition: transform 0.35s var(--cubic-apple), box-shadow 0.35s;
        }
        .planet-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }
        .planet-card .planet-emoji {
            width: 64px;
            height: 64px;
            margin: 0 auto 12px;
        }
        .planet-card .planet-emoji img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .planet-card h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 8px;
        }
        .planet-card p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin: 0;
        }

        /* Feature Grid */
        .intro-feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .intro-feature-card {
            background: #f5f5f7;
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            transition: transform 0.3s var(--cubic-apple);
        }
        .intro-feature-card:hover {
            transform: translateY(-6px);
        }
        .intro-feature-card .feat-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 10px;
        }
        .intro-feature-card .feat-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .intro-feature-card h4 {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 6px;
        }
        .intro-feature-card p {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
            margin: 0;
        }

        /* Screenshot Gallery */
        .screenshot-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .screenshot-gallery figure {
            margin: 0;
            border-radius: 18px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transition: transform 0.35s var(--cubic-apple), box-shadow 0.35s;
            background: #f5f5f7;
        }
        .screenshot-gallery figure:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 16px 40px rgba(0,0,0,0.15);
        }
        .screenshot-gallery img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            transition: transform 0.4s;
        }
        .screenshot-gallery figure:hover img {
            transform: scale(1.08);
        }
        .screenshot-gallery figcaption {
            padding: 14px 16px;
            background: #fff;
        }
        .screenshot-gallery figcaption strong {
            display: block;
            font-size: 15px;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 4px;
        }
        .screenshot-gallery figcaption span {
            font-size: 13px;
            color: #86868b;
            line-height: 1.4;
        }

        /* ===== LIGHTBOX MODAL ===== */
        .lightbox-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 10000;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.35s;
        }
        .lightbox-overlay.active {
            display: flex;
            opacity: 1;
        }
        .lightbox-content {
            position: relative;
            max-width: 90vw;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: lightboxIn 0.4s var(--cubic-apple) forwards;
        }
        @keyframes lightboxIn {
            from { opacity: 0; transform: scale(0.92) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .lightbox-content img {
            max-width: 100%;
            max-height: 72vh;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            object-fit: contain;
        }
        .lightbox-caption {
            text-align: center;
            margin-top: 16px;
            max-width: 600px;
        }
        .lightbox-caption strong {
            display: block;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }
        .lightbox-caption span {
            font-size: 14px;
            color: rgba(255,255,255,0.7);
            line-height: 1.5;
        }
        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            font-size: 22px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            z-index: 10001;
        }
        .lightbox-close:hover { background: rgba(255,255,255,0.3); }
        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, transform 0.2s;
            z-index: 10001;
        }
        .lightbox-nav:hover { background: rgba(255,255,255,0.3); transform: translateY(-50%) scale(1.1); }
        .lightbox-prev { left: 20px; }
        .lightbox-next { right: 20px; }
        .lightbox-counter {
            position: absolute;
            top: 24px;
            left: 24px;
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            font-weight: 500;
            z-index: 10001;
        }

        /* CTA Bottom Banner */
        .cta-banner {
            text-align: center;
            padding: 60px 20px;
            max-width: 700px;
            margin: 0 auto 40px;
        }
        .cta-banner h2 {
            font-size: 36px;
            font-weight: 700;
            margin: 0 0 12px;
            letter-spacing: -0.02em;
        }
        .cta-banner p {
            color: var(--text-secondary);
            font-size: 17px;
            margin: 0 0 28px;
        }
        .cta-banner .cta-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Arena list */
        .arena-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin: 16px 0;
            list-style: none;
            padding: 0;
        }
        .arena-list li {
            background: #f0f4ff;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 500;
            color: #1d1d1f;
            border-left: 3px solid #0071e3;
        }

        /* Fade-in on scroll */
        .fade-section {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s, transform 0.7s var(--cubic-apple);
        }
        .fade-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Footer */
        .intro-footer {
            text-align: center;
            padding: 30px;
            color: var(--text-secondary);
            font-size: 13px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .glass-content { padding: 24px; }
            .planet-grid { grid-template-columns: 1fr; }
            .intro-feature-grid { grid-template-columns: repeat(2, 1fr); }
            .screenshot-gallery { grid-template-columns: repeat(2, 1fr); }
            .cta-banner h2 { font-size: 28px; }
        }
        @media (max-width: 480px) {
            .intro-feature-grid { grid-template-columns: 1fr; }
            .screenshot-gallery { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>

    <!-- Navigation -->
    <?php include __DIR__ . '/nav.php'; ?>

    <!-- ===== HERO ===== -->
    <section class="intro-hero">
        <div class="intro-hero-content">
            <h1>Khám Phá Thế Giới<br>Chú Bé Rồng Online</h1>
            <p class="subtitle">Game nhập vai trực tuyến lấy cảm hứng từ bộ truyện tranh huyền thoại Dragon Ball – chiến đấu, khám phá và chinh phục trên đa nền tảng.</p>
            <div class="hero-buttons">
                <a href="/register" class="btn-pill btn-black">Tạo Tài Khoản Miễn Phí</a>
                <a href="/download/pc.rar" class="btn-pill btn-outline">Tải Game Ngay</a>
            </div>
        </div>
    </section>

    <!-- ===== SƠ LƯỢC ===== -->
    <article class="content-section">
        <section class="glass-content fade-section">
            <h2><img src="/images/icons/dragon.png" alt="" style="width:32px;height:32px;vertical-align:middle;margin-right:8px;">Sơ Lược Về Game</h2>
            <p>Chú Bé Rồng Online là game nhập vai trực tuyến với cốt truyện và nhân vật dựa trên bộ truyện tranh nổi tiếng Nhật Bản <strong>Dragon Ball</strong> – từng làm say lòng bao thế hệ độc giả Việt Nam.</p>
            <p>Bạn sẽ chọn theo một trong ba hành tinh: <strong>Trái Đất</strong>, <strong>Namếc</strong> hay <strong>Xayda</strong>. Cuộc hành trình tìm kiếm ngọc rồng và chống kẻ hung ác bắt đầu nằm trong tay bạn!</p>
            <p>Cùng với sự hướng dẫn của các bậc tiền bối, bạn có thể đạt đến sức mạnh kinh hoàng, trở thành chiến binh siêu hạng. Bạn sẽ không chiến đấu đơn độc – xung quanh bạn là những chiến binh cùng chí hướng, cùng hỗ trợ lẫn nhau đối đầu với thế lực hắc ám.</p>

            <h3><img src="/images/icons/multiplatform.png" alt="" style="width:24px;height:24px;vertical-align:middle;margin-right:6px;">Đa Nền Tảng</h3>
            <p>Chơi được trên <strong>PC Windows, iPhone, Android, Windows Phone</strong> và cả bản Java trên Nokia S40/S60 cũ. Chất lượng cao, tốc độ mượt mà trên ADSL, 3G, GPRS.</p>
            <p>Trò chơi thích hợp với mọi lứa tuổi. Điều khiển trực tiếp nhân vật dễ dàng trên màn hình cảm ứng hoặc bàn phím.</p>
        </section>

        <!-- ===== 3 HÀNH TINH ===== -->
        <section class="fade-section">
            <h2 style="text-align:center; font-size:32px; font-weight:700; margin-bottom:8px;">Chọn Hành Tinh Của Bạn</h2>
            <p style="text-align:center; color:var(--text-secondary); margin-bottom:30px;">Mỗi hành tinh mang đến phong cách chiến đấu và kỹ năng riêng biệt.</p>
            <div class="planet-grid">
                <div class="planet-card">
                    <div class="planet-emoji"><img src="/images/icons/earth.png" alt="Trái Đất"></div>
                    <h3>Trái Đất</h3>
                    <p>Sở hữu những kỹ năng đặc biệt khó chịu, rất mạnh khi đi theo nhóm. Đại diện: <strong>Gohan, Krillin, Yamcha</strong>. Chiêu thức huyền thoại: Quả cầu năng lượng, Kaioken, Kamejoko.</p>
                </div>
                <div class="planet-card">
                    <div class="planet-emoji"><img src="/images/icons/namec.png" alt="Namếc"></div>
                    <h3>Namếc</h3>
                    <p>Khả năng tái tạo và hỗ trợ đồng đội đáng kinh ngạc. Đại diện: <strong>Ốc Tiêu, Piccolo, Kami</strong>. Chiêu thức huyền thoại: Makankosappo, Đề trùng, Trị thương.</p>
                </div>
                <div class="planet-card">
                    <div class="planet-emoji"><img src="/images/icons/saiyan.png" alt="Xayda"></div>
                    <h3>Xayda</h3>
                    <p>Sức mạnh kinh hoàng khi chiến đấu đơn độc. Đại diện: <strong>Vegeta, Raditz, Kakarot</strong>. Chiêu thức huyền thoại: Biến hình, Tự phát nổ, Galick Gun.</p>
                </div>
            </div>
        </section>

        <!-- ===== TÍNH NĂNG NỔI BẬT ===== -->
        <section class="glass-content fade-section">
            <h2><img src="/images/icons/lightning.png" alt="" style="width:32px;height:32px;vertical-align:middle;margin-right:8px;">Tính Năng Nổi Bật</h2>
            <div class="intro-feature-grid">
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/house.png" alt="Hệ Thống Nhà"></div>
                    <h4>Hệ Thống Nhà</h4>
                    <p>Đậu Thần hồi phục HP/KI có thể nâng cấp. Rương đồ chứa tài sản quý giá an toàn.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/map.png" alt="Map & NPC"></div>
                    <h4>Map & NPC Đa Dạng</h4>
                    <p>NPC gắn liền cốt truyện Dragon Ball: Thượng Đế, Thần Mèo, Thần Vũ Trụ giúp tăng sức mạnh.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/energy.png" alt="Chiêu Thức"></div>
                    <h4>Chiêu Thức Đa Dạng</h4>
                    <p>Mỗi hành tinh có hệ thống chiêu thức riêng. Cân bằng chỉ số bản thân và chiêu thức để trở thành huyền thoại.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/quest.png" alt="Nhiệm Vụ"></div>
                    <h4>Nhiệm Vụ Phong Phú</h4>
                    <p>Nhiệm vụ chính tuyến, hàng ngày, thành tựu – kiếm ngọc hồng qua hoạt động online chăm chỉ.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/items.png" alt="Vật Phẩm"></div>
                    <h4>Vật Phẩm Đa Dạng</h4>
                    <p>Trang bị từ đánh quái, hạ Boss. Sách kỹ năng, cải trang biến hóa tạo nên sự độc nhất.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/pet.png" alt="Đệ Tử"></div>
                    <h4>Đệ Tử & Thú Cưỡi</h4>
                    <p>Hạ Boss Broly để nhận đệ tử. Thú cưỡi phục hồi KI khi bay, option đặc biệt tăng HP/KI.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/crystal.png" alt="Pha Lê"></div>
                    <h4>Trang Bị Pha Lê</h4>
                    <p>Nâng cấp bằng 5 loại đá quý. Pha lê hóa trang bị tại đảo Kame với 9 loại pha lê độc đáo.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/fusion.png" alt="Hợp Thể"></div>
                    <h4>Lưỡng Long Nhất Thể</h4>
                    <p>4 hình thức hợp thể: Fusion Dance, Bông tai Porata, hợp thể Namếc và hợp thể vĩnh viễn.</p>
                </div>
            </div>
        </section>

        <!-- ===== HỆ THỐNG VÕ ĐÀI ===== -->
        <section class="glass-content fade-section">
            <h2><img src="/images/icons/trophy.png" alt="" style="width:32px;height:32px;vertical-align:middle;margin-right:8px;">Hệ Thống Võ Đài</h2>
            <p>Chứng minh sức mạnh của bạn tại các giải đấu hấp dẫn diễn ra thường xuyên:</p>
            <ul class="arena-list">
                <li><strong>Đại Hội Võ Thuật</strong> – 5 giải, mỗi giờ trong ngày</li>
                <li><strong>Võ Đài Bà Hạt Mít</strong> – Dưới 150tr sức mạnh</li>
                <li><strong>Võ Đài Lần Thứ 23</strong> – Hạ Boss theo thứ tự</li>
                <li><strong>Giải Siêu Hạng</strong> – Top 100 nhận hồng ngọc</li>
                <li><strong>Võ Đài Liên Server</strong> – 12h & 20h hàng ngày</li>
                <li><strong>Boss Hằng Ngày</strong> – Hệ thống Boss đa dạng</li>
            </ul>
        </section>

        <!-- ===== HỆ THỐNG BOSS ===== -->
        <section class="glass-content fade-section">
            <h2><img src="/images/icons/boss.png" alt="" style="width:32px;height:32px;vertical-align:middle;margin-right:8px;">Hệ Thống Boss Đa Dạng</h2>
            <p>Hệ thống Boss đi theo lối truyện từ các nhân vật phe phản diện: <strong>Xên, Fide, Tiểu đội Sát Thủ, Android 19 & 20</strong> và nhiều hơn nữa. Đánh bại Boss để nhận trang bị vật phẩm hiếm!</p>
            <p>Mỗi ngày từ <strong>22h tối</strong>, Boss Hirudegarn xuất hiện tại Thành phố Vegeta – hạ gục để nhận <strong>Quả Trứng Đệ Tử Mabư</strong> với sức mạnh sẵn 1.5 triệu!</p>
        </section>

        <!-- ===== SCREENSHOT GALLERY ===== -->
        <section class="fade-section">
            <h2 style="text-align:center; font-size:28px; font-weight:700; margin-bottom:8px;"><img src="/images/icons/camera.png" alt="" style="width:28px;height:28px;vertical-align:middle;margin-right:8px;">Hình Ảnh Trong Game</h2>
            <p style="text-align:center; color:var(--text-secondary); margin-bottom:30px;">Click vào ảnh để xem chi tiết với kích thước lớn</p>
            <div class="screenshot-gallery">
                <figure onclick="openLightbox(0)">
                    <img src="/images/screen/2-10.png" alt="Hệ thống nhà trong Chú Bé Rồng Online" loading="lazy">
                    <figcaption>
                        <strong>Hệ Thống Nhà</strong>
                        <span>Trồng Đậu Thần hồi phục HP/KI, rương đồ chứa vật phẩm quý giá an toàn. Nâng cấp nhà để mở thêm tính năng.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(1)">
                    <img src="/images/screen/2-1.png" alt="Map thế giới Chú Bé Rồng Online" loading="lazy">
                    <figcaption>
                        <strong>Map & NPC</strong>
                        <span>Thế giới rộng lớn với hàng chục bản đồ. NPC gắn liền cốt truyện Dragon Ball: Thượng Đế, Thần Mèo, Thần Vũ Trụ.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(2)">
                    <img src="/images/screen/4-1.png" alt="Chiêu thức Xayda" loading="lazy">
                    <figcaption>
                        <strong>Chiêu Thức Xayda</strong>
                        <span>Sức mạnh kinh hoàng khi chiến đấu đơn độc. Biến hình, Tự phát nổ, Galick Gun – chiêu thức huyền thoại của tộc Xayda.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(3)">
                    <img src="/images/screen/4-2.png" alt="Chiêu thức Trái Đất" loading="lazy">
                    <figcaption>
                        <strong>Chiêu Thức Trái Đất</strong>
                        <span>Quả cầu năng lượng, Kaioken, Kamejoko – những kỹ năng đặc biệt khó chịu, cực mạnh khi chiến đấu theo nhóm.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(4)">
                    <img src="/images/screen/5-1.png" alt="Nhiệm vụ chính tuyến" loading="lazy">
                    <figcaption>
                        <strong>Nhiệm Vụ</strong>
                        <span>Hệ thống nhiệm vụ chính tuyến, hàng ngày và thành tựu phong phú. Kiếm ngọc hồng qua hoạt động online.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(5)">
                    <img src="/images/screen/6-1.png" alt="Vật phẩm trang bị" loading="lazy">
                    <figcaption>
                        <strong>Trang Bị</strong>
                        <span>Thu thập trang bị từ đánh quái và hạ Boss. Sách kỹ năng, cải trang biến hóa tạo nên sự độc nhất cho nhân vật.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(6)">
                    <img src="/images/screen/9-1.png" alt="Đại Hội Võ Thuật" loading="lazy">
                    <figcaption>
                        <strong>Đại Hội Võ Thuật</strong>
                        <span>5 giải đấu mỗi ngày, Võ Đài Liên Server lúc 12h & 20h. Chứng minh bạn là chiến binh mạnh nhất!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(7)">
                    <img src="/images/screen/10-1.png" alt="Lưỡng Long Nhất Thể - Hợp Thể" loading="lazy">
                    <figcaption>
                        <strong>Lưỡng Long Nhất Thể</strong>
                        <span>4 kiểu hợp thể: Fusion Dance, Bông tai Porata, hợp thể Namếc và hợp thể vĩnh viễn. Sức mạnh nhân đôi!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(8)">
                    <img src="/images/screen/11-2.png" alt="Nâng cấp pha lê" loading="lazy">
                    <figcaption>
                        <strong>Pha Lê Hóa</strong>
                        <span>Nâng cấp bằng 5 loại đá quý. Pha lê hóa trang bị tại đảo Kame với 9 loại pha lê độc đáo.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(9)">
                    <img src="/images/screen/8-2.png" alt="Đệ tử Broly" loading="lazy">
                    <figcaption>
                        <strong>Đệ Tử & Thú Cưỡi</strong>
                        <span>Hạ Boss Broly để nhận đệ tử. Thú cưỡi phục hồi KI khi bay, option đặc biệt tăng HP/KI cho nhân vật.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(10)">
                    <img src="/images/screen/14-1.png" alt="Boss trong game" loading="lazy">
                    <figcaption>
                        <strong>Hệ Thống Boss</strong>
                        <span>Boss đi theo cốt truyện: Xên, Fide, Android 19 & 20. Boss Hirudegarn 22h tối – nhận Trứng Đệ Tử Mabư!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(11)">
                    <img src="/images/screen/12-1.png" alt="Phó bản hàng ngày" loading="lazy">
                    <figcaption>
                        <strong>Phó Bản</strong>
                        <span>Hệ thống phó bản hàng ngày với phần thưởng hấp dẫn. Phối hợp đồng đội để vượt qua thử thách cam go.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(12)">
                    <img src="/images/screen/14-3.png" alt="Boss Frieza" loading="lazy">
                    <figcaption>
                        <strong>Boss Frieza</strong>
                        <span>Frieza – kẻ thống trị vũ trụ với sức mạnh khủng khiếp. Hạ gục để nhận vật phẩm huyền thoại.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(13)">
                    <img src="/images/screen/2-4.png" alt="Rừng Nguyên Sinh" loading="lazy">
                    <figcaption>
                        <strong>Rừng Nguyên Sinh</strong>
                        <span>Khu vực săn quái luyện level với cảnh quan xanh mát. Nơi khởi đầu hành trình của mọi chiến binh.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(14)">
                    <img src="/images/screen/9-4.png" alt="Võ Đài Liên Server" loading="lazy">
                    <figcaption>
                        <strong>Võ Đài Liên Server</strong>
                        <span>Đấu trường PvP giữa các server. Lúc 12h và 20h hàng ngày – phần thưởng khủng cho người thắng!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(15)">
                    <img src="/images/screen/8-4.png" alt="Đệ tử nâng cấp" loading="lazy">
                    <figcaption>
                        <strong>Nâng Cấp Đệ Tử</strong>
                        <span>Huấn luyện đệ tử trở nên mạnh mẽ hơn. Mỗi đệ tử có skill riêng hỗ trợ chiến đấu hiệu quả.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(16)">
                    <img src="/images/screen/14-6.png" alt="Boss Cell" loading="lazy">
                    <figcaption>
                        <strong>Boss Cell</strong>
                        <span>Cell hoàn hảo – thử thách khó khăn nhất cho các chiến binh. Drop vật phẩm cực hiếm!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(17)">
                    <img src="/images/screen/12-5.png" alt="Phó bản cao cấp" loading="lazy">
                    <figcaption>
                        <strong>Phó Bản Cao Cấp</strong>
                        <span>Thử thách phó bản khó với phần thưởng tương xứng. Cần đội hình phối hợp ăn ý.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(18)">
                    <img src="/images/screen/2-7.png" alt="Vách núi" loading="lazy">
                    <figcaption>
                        <strong>Vách Núi Hiểm Trở</strong>
                        <span>Khu vực farm quái cấp cao với địa hình hiểm trở. Chiến binh mạnh mới dám mạo hiểm!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(19)">
                    <img src="/images/screen/5-2.png" alt="Nhiệm vụ hàng ngày" loading="lazy">
                    <figcaption>
                        <strong>Nhiệm Vụ Hàng Ngày</strong>
                        <span>Hoàn thành nhiệm vụ hàng ngày để nhận Ngọc Rồng và vật phẩm giá trị.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(20)">
                    <img src="/images/screen/6-2.png" alt="Kho vật phẩm" loading="lazy">
                    <figcaption>
                        <strong>Kho Vật Phẩm</strong>
                        <span>Hệ thống vật phẩm đa dạng: trang bị, nguyên liệu, sách kỹ năng và vật phẩm sự kiện.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(21)">
                    <img src="/images/screen/14-7.png" alt="Boss Mabư" loading="lazy">
                    <figcaption>
                        <strong>Boss Mabư</strong>
                        <span>Majin Buu – sức mạnh phép thuật kinh hoàng. Cần cả đội mới có thể chiến thắng!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(22)">
                    <img src="/images/screen/12-7.png" alt="Phó bản nhóm" loading="lazy">
                    <figcaption>
                        <strong>Phó Bản Nhóm</strong>
                        <span>Dungeon đặc biệt dành cho nhóm chiến binh. Vượt qua hàng loạt thử thách để nhận phần thưởng siêu khủng.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(23)">
                    <img src="/images/screen/11-3.png" alt="Nâng cấp pha lê" loading="lazy">
                    <figcaption>
                        <strong>Pha Lê Nâng Cao</strong>
                        <span>Hệ thống nâng cấp pha lê cấp cao, tạo ra trang bị tối thượng cho chiến binh.</span>
                    </figcaption>
                </figure>
            </div>
        </section>
    </article>

    <!-- ===== LIGHTBOX MODAL ===== -->
    <div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox(event)">
        <button class="lightbox-close" onclick="closeLightbox(event)" title="Đóng">✕</button>
        <span class="lightbox-counter" id="lightboxCounter"></span>
        <button class="lightbox-nav lightbox-prev" onclick="navLightbox(event, -1)" title="Ảnh trước">⮘</button>
        <div class="lightbox-content" onclick="event.stopPropagation()">
            <img id="lightboxImg" src="" alt="">
            <div class="lightbox-caption">
                <strong id="lightboxTitle"></strong>
                <span id="lightboxDesc"></span>
            </div>
        </div>
        <button class="lightbox-nav lightbox-next" onclick="navLightbox(event, 1)" title="Ảnh tiếp">⮚</button>
    </div>

    <!-- ===== CTA ===== -->
    <section class="cta-banner fade-section">
        <h2>Sẵn Sàng Chiến Đấu?</h2>
        <p>Tham gia cùng hàng vạn chiến binh trong thế giới Chú Bé Rồng Online ngay hôm nay.</p>
        <div class="cta-buttons">
            <a href="/register" class="btn-pill btn-black">Đăng Ký Miễn Phí</a>
            <a href="/download/pc.rar" class="btn-pill btn-outline">Tải cho Windows</a>
            <a href="/download/adr.apk" class="btn-pill btn-outline">Tải cho Android</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="intro-footer">
        Bản quyền thuộc về Chú Bé Rồng Online – Thiết kế bởi Antigravity UI
    </footer>

    <!-- Scripts -->
    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
    <script>
        // Fade-in on scroll
        document.addEventListener('DOMContentLoaded', function() {
            var sections = document.querySelectorAll('.fade-section');
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });
            sections.forEach(function(s) { observer.observe(s); });
        });

        // ===== LIGHTBOX =====
        var galleryData = [
            { src: '/images/screen/2-10.png', title: '🏠 Hệ Thống Nhà', desc: 'Trồng Đậu Thần hồi phục HP/KI, rương đồ chứa vật phẩm quý giá an toàn. Nâng cấp nhà để mở thêm tính năng đặc biệt.' },
            { src: '/images/screen/2-1.png', title: '🗺️ Map & NPC', desc: 'Thế giới rộng lớn với hàng chục bản đồ. NPC gắn liền cốt truyện Dragon Ball: Thượng Đế, Thần Mèo, Thần Vũ Trụ.' },
            { src: '/images/screen/4-1.png', title: '🔥 Chiêu Thức Xayda', desc: 'Sức mạnh kinh hoàng khi chiến đấu đơn độc. Biến hình, Tự phát nổ, Galick Gun – chiêu thức huyền thoại của tộc Xayda.' },
            { src: '/images/screen/4-2.png', title: '⚡ Chiêu Thức Trái Đất', desc: 'Quả cầu năng lượng, Kaioken, Kamejoko – những kỹ năng đặc biệt khó chịu, cực mạnh khi chiến đấu theo nhóm.' },
            { src: '/images/screen/5-1.png', title: '📋 Nhiệm Vụ', desc: 'Hệ thống nhiệm vụ chính tuyến, hàng ngày và thành tựu phong phú. Kiếm ngọc hồng qua hoạt động online hàng ngày.' },
            { src: '/images/screen/6-1.png', title: '🎒 Trang Bị', desc: 'Thu thập trang bị từ đánh quái và hạ Boss. Sách kỹ năng, cải trang biến hóa tạo nên sự độc nhất cho nhân vật.' },
            { src: '/images/screen/9-1.png', title: '🏆 Đại Hội Võ Thuật', desc: '5 giải đấu mỗi ngày, Võ Đài Liên Server lúc 12h & 20h. Chứng minh bạn là chiến binh mạnh nhất vũ trụ!' },
            { src: '/images/screen/10-1.png', title: '🤝 Lưỡng Long Nhất Thể', desc: '4 kiểu hợp thể: Fusion Dance, Bông tai Porata, hợp thể Namếc và hợp thể vĩnh viễn. Sức mạnh nhân đôi!' },
            { src: '/images/screen/11-2.png', title: '💎 Pha Lê Hóa', desc: 'Nâng cấp bằng 5 loại đá quý tại đảo Kame. 9 loại pha lê độc đáo, tạo nên trang bị thần thoại.' },
            { src: '/images/screen/8-2.png', title: '🐾 Đệ Tử & Thú Cưỡi', desc: 'Hạ Boss Broly để nhận đệ tử. Thú cưỡi phục hồi KI khi bay, option đặc biệt tăng HP/KI cho nhân vật.' },
            { src: '/images/screen/14-1.png', title: '👹 Hệ Thống Boss', desc: 'Boss đi theo cốt truyện: Xên, Fide, Android 19 & 20. Boss Hirudegarn 22h tối – nhận Trứng Đệ Tử Mabư!' },
            { src: '/images/screen/12-1.png', title: '🏰 Phó Bản', desc: 'Hệ thống phó bản hàng ngày với phần thưởng hấp dẫn. Phối hợp đồng đội để vượt qua thử thách cam go.' },
            { src: '/images/screen/14-3.png', title: '👹 Boss Frieza', desc: 'Frieza – kẻ thống trị vũ trụ với sức mạnh khủng khiếp. Hạ gục để nhận vật phẩm huyền thoại.' },
            { src: '/images/screen/2-4.png', title: '🌳 Rừng Nguyên Sinh', desc: 'Khu vực săn quái luyện level với cảnh quan xanh mát. Nơi khởi đầu hành trình của mọi chiến binh.' },
            { src: '/images/screen/9-4.png', title: '⚔️ Võ Đài Liên Server', desc: 'Đấu trường PvP giữa các server. Lúc 12h và 20h hàng ngày – phần thưởng khủng cho người thắng!' },
            { src: '/images/screen/8-4.png', title: '🐾 Nâng Cấp Đệ Tử', desc: 'Huấn luyện đệ tử trở nên mạnh mẽ hơn. Mỗi đệ tử có skill riêng hỗ trợ chiến đấu hiệu quả.' },
            { src: '/images/screen/14-6.png', title: '👹 Boss Cell', desc: 'Cell hoàn hảo – thử thách khó khăn nhất. Drop vật phẩm cực hiếm!' },
            { src: '/images/screen/12-5.png', title: '🏰 Phó Bản Cao Cấp', desc: 'Thử thách phó bản khó với phần thưởng tương xứng. Cần đội hình phối hợp ăn ý.' },
            { src: '/images/screen/2-7.png', title: '🏔️ Vách Núi', desc: 'Khu vực farm quái cấp cao với địa hình hiểm trở. Chiến binh mạnh mới dám mạo hiểm!' },
            { src: '/images/screen/5-2.png', title: '📋 Nhiệm Vụ Hàng Ngày', desc: 'Hoàn thành nhiệm vụ hàng ngày để nhận Ngọc Rồng và vật phẩm giá trị.' },
            { src: '/images/screen/6-2.png', title: '🎒 Kho Vật Phẩm', desc: 'Hệ thống vật phẩm đa dạng: trang bị, nguyên liệu, sách kỹ năng và vật phẩm sự kiện.' },
            { src: '/images/screen/14-7.png', title: '👹 Boss Mabư', desc: 'Majin Buu – sức mạnh phép thuật kinh hoàng. Cần cả đội mới có thể chiến thắng!' },
            { src: '/images/screen/12-7.png', title: '🏰 Phó Bản Nhóm', desc: 'Dungeon đặc biệt dành cho nhóm chiến binh. Phần thưởng siêu khủng!' },
            { src: '/images/screen/11-3.png', title: '💎 Pha Lê Nâng Cao', desc: 'Hệ thống nâng cấp pha lê cấp cao, tạo ra trang bị tối thượng cho chiến binh.' }
        ];
        var currentLightbox = 0;

        function openLightbox(idx) {
            currentLightbox = idx;
            updateLightbox();
            var overlay = document.getElementById('lightboxOverlay');
            overlay.style.display = 'flex';
            requestAnimationFrame(function() { overlay.classList.add('active'); });
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox(e) {
            if (e) e.stopPropagation();
            var overlay = document.getElementById('lightboxOverlay');
            overlay.classList.remove('active');
            setTimeout(function() { overlay.style.display = 'none'; }, 350);
            document.body.style.overflow = '';
        }

        function navLightbox(e, dir) {
            e.stopPropagation();
            currentLightbox = (currentLightbox + dir + galleryData.length) % galleryData.length;
            updateLightbox();
        }

        function updateLightbox() {
            var item = galleryData[currentLightbox];
            document.getElementById('lightboxImg').src = item.src;
            document.getElementById('lightboxTitle').textContent = item.title;
            document.getElementById('lightboxDesc').textContent = item.desc;
            document.getElementById('lightboxCounter').textContent = (currentLightbox + 1) + ' / ' + galleryData.length;
        }

        // Keyboard support
        document.addEventListener('keydown', function(e) {
            var overlay = document.getElementById('lightboxOverlay');
            if (!overlay.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox(e);
            if (e.key === 'ArrowLeft') navLightbox(e, -1);
            if (e.key === 'ArrowRight') navLightbox(e, 1);
        });
    </script>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>

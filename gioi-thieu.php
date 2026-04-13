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
    <title>Gi?i Thi?u - Chú Bé R?ng Online | Game Ng?c R?ng Mobile Huy?n Tho?i</title>
    <meta name="description" content="Khám phá th? gi?i Chú Bé R?ng Online – Game nh?p vai Dragon Ball huy?n tho?i. 3 hành tinh, hàng tram chiêu th?c, h? th?ng Boss da d?ng, Ð?i H?i Võ Thu?t và nhi?u tính nang h?p d?n dang ch? b?n." />
    <meta name="keywords" content="Chú Bé R?ng Online, ng?c r?ng online, game ngoc rong, Dragon Ball, game 7 viên ng?c r?ng, game nh?p vai, MMORPG mobile, NRO" />
    <meta name="robots" content="INDEX,FOLLOW" />
    <meta property="og:title" content="Gi?i Thi?u - Chú Bé R?ng Online" />
    <meta property="og:description" content="Game nh?p vai Dragon Ball huy?n tho?i – 3 Hành Tinh, hàng tram k? nang, Boss s? thi và c?ng d?ng sôi d?ng." />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="/images/dragon_ball_showcase.png" />
    <link rel="canonical" href="/gioi-thieu" />

    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">

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
            <h1>Khám Phá Th? Gi?i<br>Chú Bé R?ng Online</h1>
            <p class="subtitle">Game nh?p vai tr?c tuy?n l?y c?m h?ng t? b? truy?n tranh huy?n tho?i Dragon Ball – chi?n d?u, khám phá và chinh ph?c trên da n?n t?ng.</p>
            <div class="hero-buttons">
                <a href="/register" class="btn-pill btn-black">T?o Tài Kho?n Mi?n Phí</a>
                <a href="/download/pc.rar" class="btn-pill btn-outline">T?i Game Ngay</a>
            </div>
        </div>
    </section>

    <!-- ===== SO LU?C ===== -->
    <article class="content-section">
        <section class="glass-content fade-section">
            <h2><img src="/images/icons/dragon.png" alt="" style="width:32px;height:32px;vertical-align:middle;margin-right:8px;">So Lu?c V? Game</h2>
            <p>Chú Bé R?ng Online là game nh?p vai tr?c tuy?n v?i c?t truy?n và nhân v?t d?a trên b? truy?n tranh n?i ti?ng Nh?t B?n <strong>Dragon Ball</strong> – t?ng làm say lòng bao th? h? d?c gi? Vi?t Nam.</p>
            <p>B?n s? ch?n theo m?t trong ba hành tinh: <strong>Trái Ð?t</strong>, <strong>Nam?c</strong> hay <strong>Xayda</strong>. Cu?c hành trình tìm ki?m ng?c r?ng và ch?ng k? hung ác b?t d?u n?m trong tay b?n!</p>
            <p>Cùng v?i s? hu?ng d?n c?a các b?c ti?n b?i, b?n có th? d?t d?n s?c m?nh kinh hoàng, tr? thành chi?n binh siêu h?ng. B?n s? không chi?n d?u don d?c – xung quanh b?n là nh?ng chi?n binh cùng chí hu?ng, cùng h? tr? l?n nhau d?i d?u v?i th? l?c h?c ám.</p>

            <h3><img src="/images/icons/multiplatform.png" alt="" style="width:24px;height:24px;vertical-align:middle;margin-right:6px;">Ða N?n T?ng</h3>
            <p>Choi du?c trên <strong>PC Windows, iPhone, Android, Windows Phone</strong> và c? b?n Java trên Nokia S40/S60 cu. Ch?t lu?ng cao, t?c d? mu?t mà trên ADSL, 3G, GPRS.</p>
            <p>Trò choi thích h?p v?i m?i l?a tu?i. Ði?u khi?n tr?c ti?p nhân v?t d? dàng trên màn hình c?m ?ng ho?c bàn phím.</p>
        </section>

        <!-- ===== 3 HÀNH TINH ===== -->
        <section class="fade-section">
            <h2 style="text-align:center; font-size:32px; font-weight:700; margin-bottom:8px;">Ch?n Hành Tinh C?a B?n</h2>
            <p style="text-align:center; color:var(--text-secondary); margin-bottom:30px;">M?i hành tinh mang d?n phong cách chi?n d?u và k? nang riêng bi?t.</p>
            <div class="planet-grid">
                <div class="planet-card">
                    <div class="planet-emoji"><img src="/images/icons/earth.png" alt="Trái Ð?t"></div>
                    <h3>Trái Ð?t</h3>
                    <p>S? h?u nh?ng k? nang d?c bi?t khó ch?u, r?t m?nh khi di theo nhóm. Ð?i di?n: <strong>Gohan, Krillin, Yamcha</strong>. Chiêu th?c huy?n tho?i: Qu? c?u nang lu?ng, Kaioken, Kamejoko.</p>
                </div>
                <div class="planet-card">
                    <div class="planet-emoji"><img src="/images/icons/namec.png" alt="Nam?c"></div>
                    <h3>Nam?c</h3>
                    <p>Kh? nang tái t?o và h? tr? d?ng d?i dáng kinh ng?c. Ð?i di?n: <strong>?c Tiêu, Piccolo, Kami</strong>. Chiêu th?c huy?n tho?i: Makankosappo, Ð? trùng, Tr? thuong.</p>
                </div>
                <div class="planet-card">
                    <div class="planet-emoji"><img src="/images/icons/saiyan.png" alt="Xayda"></div>
                    <h3>Xayda</h3>
                    <p>S?c m?nh kinh hoàng khi chi?n d?u don d?c. Ð?i di?n: <strong>Vegeta, Raditz, Kakarot</strong>. Chiêu th?c huy?n tho?i: Bi?n hình, T? phát n?, Galick Gun.</p>
                </div>
            </div>
        </section>

        <!-- ===== TÍNH NANG N?I B?T ===== -->
        <section class="glass-content fade-section">
            <h2><img src="/images/icons/lightning.png" alt="" style="width:32px;height:32px;vertical-align:middle;margin-right:8px;">Tính Nang N?i B?t</h2>
            <div class="intro-feature-grid">
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/house.png" alt="H? Th?ng Nhà"></div>
                    <h4>H? Th?ng Nhà</h4>
                    <p>Ð?u Th?n h?i ph?c HP/KI có th? nâng c?p. Ruong d? ch?a tài s?n quý giá an toàn.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/map.png" alt="Map & NPC"></div>
                    <h4>Map & NPC Ða D?ng</h4>
                    <p>NPC g?n li?n c?t truy?n Dragon Ball: Thu?ng Ð?, Th?n Mèo, Th?n Vu Tr? giúp tang s?c m?nh.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/energy.png" alt="Chiêu Th?c"></div>
                    <h4>Chiêu Th?c Ða D?ng</h4>
                    <p>M?i hành tinh có h? th?ng chiêu th?c riêng. Cân b?ng ch? s? b?n thân và chiêu th?c d? tr? thành huy?n tho?i.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/quest.png" alt="Nhi?m V?"></div>
                    <h4>Nhi?m V? Phong Phú</h4>
                    <p>Nhi?m v? chính tuy?n, hàng ngày, thành t?u – ki?m ng?c h?ng qua ho?t d?ng online cham ch?.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/items.png" alt="V?t Ph?m"></div>
                    <h4>V?t Ph?m Ða D?ng</h4>
                    <p>Trang b? t? dánh quái, h? Boss. Sách k? nang, c?i trang bi?n hóa t?o nên s? d?c nh?t.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/pet.png" alt="Ð? T?"></div>
                    <h4>Ð? T? & Thú Cu?i</h4>
                    <p>H? Boss Broly d? nh?n d? t?. Thú cu?i ph?c h?i KI khi bay, option d?c bi?t tang HP/KI.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/crystal.png" alt="Pha Lê"></div>
                    <h4>Trang B? Pha Lê</h4>
                    <p>Nâng c?p b?ng 5 lo?i dá quý. Pha lê hóa trang b? t?i d?o Kame v?i 9 lo?i pha lê d?c dáo.</p>
                </div>
                <div class="intro-feature-card">
                    <div class="feat-icon"><img src="/images/icons/fusion.png" alt="H?p Th?"></div>
                    <h4>Lu?ng Long Nh?t Th?</h4>
                    <p>4 hình th?c h?p th?: Fusion Dance, Bông tai Porata, h?p th? Nam?c và h?p th? vinh vi?n.</p>
                </div>
            </div>
        </section>

        <!-- ===== H? TH?NG VÕ ÐÀI ===== -->
        <section class="glass-content fade-section">
            <h2><img src="/images/icons/trophy.png" alt="" style="width:32px;height:32px;vertical-align:middle;margin-right:8px;">H? Th?ng Võ Ðài</h2>
            <p>Ch?ng minh s?c m?nh c?a b?n t?i các gi?i d?u h?p d?n di?n ra thu?ng xuyên:</p>
            <ul class="arena-list">
                <li><strong>Ð?i H?i Võ Thu?t</strong> – 5 gi?i, m?i gi? trong ngày</li>
                <li><strong>Võ Ðài Bà H?t Mít</strong> – Du?i 150tr s?c m?nh</li>
                <li><strong>Võ Ðài L?n Th? 23</strong> – H? Boss theo th? t?</li>
                <li><strong>Gi?i Siêu H?ng</strong> – Top 100 nh?n h?ng ng?c</li>
                <li><strong>Võ Ðài Liên Server</strong> – 12h & 20h hàng ngày</li>
                <li><strong>Boss H?ng Ngày</strong> – H? th?ng Boss da d?ng</li>
            </ul>
        </section>

        <!-- ===== H? TH?NG BOSS ===== -->
        <section class="glass-content fade-section">
            <h2><img src="/images/icons/boss.png" alt="" style="width:32px;height:32px;vertical-align:middle;margin-right:8px;">H? Th?ng Boss Ða D?ng</h2>
            <p>H? th?ng Boss di theo l?i truy?n t? các nhân v?t phe ph?n di?n: <strong>Xên, Fide, Ti?u d?i Sát Th?, Android 19 & 20</strong> và nhi?u hon n?a. Ðánh b?i Boss d? nh?n trang b? v?t ph?m hi?m!</p>
            <p>M?i ngày t? <strong>22h t?i</strong>, Boss Hirudegarn xu?t hi?n t?i Thành ph? Vegeta – h? g?c d? nh?n <strong>Qu? Tr?ng Ð? T? Mabu</strong> v?i s?c m?nh s?n 1.5 tri?u!</p>
        </section>

        <!-- ===== SCREENSHOT GALLERY ===== -->
        <section class="fade-section">
            <h2 style="text-align:center; font-size:28px; font-weight:700; margin-bottom:8px;"><img src="/images/icons/camera.png" alt="" style="width:28px;height:28px;vertical-align:middle;margin-right:8px;">Hình ?nh Trong Game</h2>
            <p style="text-align:center; color:var(--text-secondary); margin-bottom:30px;">Click vào ?nh d? xem chi ti?t v?i kích thu?c l?n</p>
            <div class="screenshot-gallery">
                <figure onclick="openLightbox(0)">
                    <img src="/images/screen/2-10.png" alt="H? th?ng nhà trong Chú Bé R?ng Online" loading="lazy">
                    <figcaption>
                        <strong>H? Th?ng Nhà</strong>
                        <span>Tr?ng Ð?u Th?n h?i ph?c HP/KI, ruong d? ch?a v?t ph?m quý giá an toàn. Nâng c?p nhà d? m? thêm tính nang.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(1)">
                    <img src="/images/screen/2-1.png" alt="Map th? gi?i Chú Bé R?ng Online" loading="lazy">
                    <figcaption>
                        <strong>Map & NPC</strong>
                        <span>Th? gi?i r?ng l?n v?i hàng ch?c b?n d?. NPC g?n li?n c?t truy?n Dragon Ball: Thu?ng Ð?, Th?n Mèo, Th?n Vu Tr?.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(2)">
                    <img src="/images/screen/4-1.png" alt="Chiêu th?c Xayda" loading="lazy">
                    <figcaption>
                        <strong>Chiêu Th?c Xayda</strong>
                        <span>S?c m?nh kinh hoàng khi chi?n d?u don d?c. Bi?n hình, T? phát n?, Galick Gun – chiêu th?c huy?n tho?i c?a t?c Xayda.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(3)">
                    <img src="/images/screen/4-2.png" alt="Chiêu th?c Trái Ð?t" loading="lazy">
                    <figcaption>
                        <strong>Chiêu Th?c Trái Ð?t</strong>
                        <span>Qu? c?u nang lu?ng, Kaioken, Kamejoko – nh?ng k? nang d?c bi?t khó ch?u, c?c m?nh khi chi?n d?u theo nhóm.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(4)">
                    <img src="/images/screen/5-1.png" alt="Nhi?m v? chính tuy?n" loading="lazy">
                    <figcaption>
                        <strong>Nhi?m V?</strong>
                        <span>H? th?ng nhi?m v? chính tuy?n, hàng ngày và thành t?u phong phú. Ki?m ng?c h?ng qua ho?t d?ng online.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(5)">
                    <img src="/images/screen/6-1.png" alt="V?t ph?m trang b?" loading="lazy">
                    <figcaption>
                        <strong>Trang B?</strong>
                        <span>Thu th?p trang b? t? dánh quái và h? Boss. Sách k? nang, c?i trang bi?n hóa t?o nên s? d?c nh?t cho nhân v?t.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(6)">
                    <img src="/images/screen/9-1.png" alt="Ð?i H?i Võ Thu?t" loading="lazy">
                    <figcaption>
                        <strong>Ð?i H?i Võ Thu?t</strong>
                        <span>5 gi?i d?u m?i ngày, Võ Ðài Liên Server lúc 12h & 20h. Ch?ng minh b?n là chi?n binh m?nh nh?t!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(7)">
                    <img src="/images/screen/10-1.png" alt="Lu?ng Long Nh?t Th? - H?p Th?" loading="lazy">
                    <figcaption>
                        <strong>Lu?ng Long Nh?t Th?</strong>
                        <span>4 ki?u h?p th?: Fusion Dance, Bông tai Porata, h?p th? Nam?c và h?p th? vinh vi?n. S?c m?nh nhân dôi!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(8)">
                    <img src="/images/screen/11-2.png" alt="Nâng c?p pha lê" loading="lazy">
                    <figcaption>
                        <strong>Pha Lê Hóa</strong>
                        <span>Nâng c?p b?ng 5 lo?i dá quý. Pha lê hóa trang b? t?i d?o Kame v?i 9 lo?i pha lê d?c dáo.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(9)">
                    <img src="/images/screen/8-2.png" alt="Ð? t? Broly" loading="lazy">
                    <figcaption>
                        <strong>Ð? T? & Thú Cu?i</strong>
                        <span>H? Boss Broly d? nh?n d? t?. Thú cu?i ph?c h?i KI khi bay, option d?c bi?t tang HP/KI cho nhân v?t.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(10)">
                    <img src="/images/screen/14-1.png" alt="Boss trong game" loading="lazy">
                    <figcaption>
                        <strong>H? Th?ng Boss</strong>
                        <span>Boss di theo c?t truy?n: Xên, Fide, Android 19 & 20. Boss Hirudegarn 22h t?i – nh?n Tr?ng Ð? T? Mabu!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(11)">
                    <img src="/images/screen/12-1.png" alt="Phó b?n hàng ngày" loading="lazy">
                    <figcaption>
                        <strong>Phó B?n</strong>
                        <span>H? th?ng phó b?n hàng ngày v?i ph?n thu?ng h?p d?n. Ph?i h?p d?ng d?i d? vu?t qua th? thách cam go.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(12)">
                    <img src="/images/screen/14-3.png" alt="Boss Frieza" loading="lazy">
                    <figcaption>
                        <strong>Boss Frieza</strong>
                        <span>Frieza – k? th?ng tr? vu tr? v?i s?c m?nh kh?ng khi?p. H? g?c d? nh?n v?t ph?m huy?n tho?i.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(13)">
                    <img src="/images/screen/2-4.png" alt="R?ng Nguyên Sinh" loading="lazy">
                    <figcaption>
                        <strong>R?ng Nguyên Sinh</strong>
                        <span>Khu v?c san quái luy?n level v?i c?nh quan xanh mát. Noi kh?i d?u hành trình c?a m?i chi?n binh.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(14)">
                    <img src="/images/screen/9-4.png" alt="Võ Ðài Liên Server" loading="lazy">
                    <figcaption>
                        <strong>Võ Ðài Liên Server</strong>
                        <span>Ð?u tru?ng PvP gi?a các server. Lúc 12h và 20h hàng ngày – ph?n thu?ng kh?ng cho ngu?i th?ng!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(15)">
                    <img src="/images/screen/8-4.png" alt="Ð? t? nâng c?p" loading="lazy">
                    <figcaption>
                        <strong>Nâng C?p Ð? T?</strong>
                        <span>Hu?n luy?n d? t? tr? nên m?nh m? hon. M?i d? t? có skill riêng h? tr? chi?n d?u hi?u qu?.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(16)">
                    <img src="/images/screen/14-6.png" alt="Boss Cell" loading="lazy">
                    <figcaption>
                        <strong>Boss Cell</strong>
                        <span>Cell hoàn h?o – th? thách khó khan nh?t cho các chi?n binh. Drop v?t ph?m c?c hi?m!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(17)">
                    <img src="/images/screen/12-5.png" alt="Phó b?n cao c?p" loading="lazy">
                    <figcaption>
                        <strong>Phó B?n Cao C?p</strong>
                        <span>Th? thách phó b?n khó v?i ph?n thu?ng tuong x?ng. C?n d?i hình ph?i h?p an ý.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(18)">
                    <img src="/images/screen/2-7.png" alt="Vách núi" loading="lazy">
                    <figcaption>
                        <strong>Vách Núi Hi?m Tr?</strong>
                        <span>Khu v?c farm quái c?p cao v?i d?a hình hi?m tr?. Chi?n binh m?nh m?i dám m?o hi?m!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(19)">
                    <img src="/images/screen/5-2.png" alt="Nhi?m v? hàng ngày" loading="lazy">
                    <figcaption>
                        <strong>Nhi?m V? Hàng Ngày</strong>
                        <span>Hoàn thành nhi?m v? hàng ngày d? nh?n Ng?c R?ng và v?t ph?m giá tr?.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(20)">
                    <img src="/images/screen/6-2.png" alt="Kho v?t ph?m" loading="lazy">
                    <figcaption>
                        <strong>Kho V?t Ph?m</strong>
                        <span>H? th?ng v?t ph?m da d?ng: trang b?, nguyên li?u, sách k? nang và v?t ph?m s? ki?n.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(21)">
                    <img src="/images/screen/14-7.png" alt="Boss Mabu" loading="lazy">
                    <figcaption>
                        <strong>Boss Mabu</strong>
                        <span>Majin Buu – s?c m?nh phép thu?t kinh hoàng. C?n c? d?i m?i có th? chi?n th?ng!</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(22)">
                    <img src="/images/screen/12-7.png" alt="Phó b?n nhóm" loading="lazy">
                    <figcaption>
                        <strong>Phó B?n Nhóm</strong>
                        <span>Dungeon d?c bi?t dành cho nhóm chi?n binh. Vu?t qua hàng lo?t th? thách d? nh?n ph?n thu?ng siêu kh?ng.</span>
                    </figcaption>
                </figure>
                <figure onclick="openLightbox(23)">
                    <img src="/images/screen/11-3.png" alt="Nâng c?p pha lê" loading="lazy">
                    <figcaption>
                        <strong>Pha Lê Nâng Cao</strong>
                        <span>H? th?ng nâng c?p pha lê c?p cao, t?o ra trang b? t?i thu?ng cho chi?n binh.</span>
                    </figcaption>
                </figure>
            </div>
        </section>
    </article>

    <!-- ===== LIGHTBOX MODAL ===== -->
    <div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox(event)">
        <button class="lightbox-close" onclick="closeLightbox(event)" title="Ðóng">?</button>
        <span class="lightbox-counter" id="lightboxCounter"></span>
        <button class="lightbox-nav lightbox-prev" onclick="navLightbox(event, -1)" title="?nh tru?c">?</button>
        <div class="lightbox-content" onclick="event.stopPropagation()">
            <img id="lightboxImg" src="" alt="">
            <div class="lightbox-caption">
                <strong id="lightboxTitle"></strong>
                <span id="lightboxDesc"></span>
            </div>
        </div>
        <button class="lightbox-nav lightbox-next" onclick="navLightbox(event, 1)" title="?nh ti?p">?</button>
    </div>

    <!-- ===== CTA ===== -->
    <section class="cta-banner fade-section">
        <h2>S?n Sàng Chi?n Ð?u?</h2>
        <p>Tham gia cùng hàng v?n chi?n binh trong th? gi?i Chú Bé R?ng Online ngay hôm nay.</p>
        <div class="cta-buttons">
            <a href="/register" class="btn-pill btn-black">Ðang Ký Mi?n Phí</a>
            <a href="/download/pc.rar" class="btn-pill btn-outline">T?i cho Windows</a>
            <a href="/download/adr.apk" class="btn-pill btn-outline">T?i cho Android</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="intro-footer">
        B?n quy?n thu?c v? Chú Bé R?ng Online – Thi?t k? b?i Antigravity UI
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
            { src: '/images/screen/2-10.png', title: '?? H? Th?ng Nhà', desc: 'Tr?ng Ð?u Th?n h?i ph?c HP/KI, ruong d? ch?a v?t ph?m quý giá an toàn. Nâng c?p nhà d? m? thêm tính nang d?c bi?t.' },
            { src: '/images/screen/2-1.png', title: '??? Map & NPC', desc: 'Th? gi?i r?ng l?n v?i hàng ch?c b?n d?. NPC g?n li?n c?t truy?n Dragon Ball: Thu?ng Ð?, Th?n Mèo, Th?n Vu Tr?.' },
            { src: '/images/screen/4-1.png', title: '?? Chiêu Th?c Xayda', desc: 'S?c m?nh kinh hoàng khi chi?n d?u don d?c. Bi?n hình, T? phát n?, Galick Gun – chiêu th?c huy?n tho?i c?a t?c Xayda.' },
            { src: '/images/screen/4-2.png', title: '? Chiêu Th?c Trái Ð?t', desc: 'Qu? c?u nang lu?ng, Kaioken, Kamejoko – nh?ng k? nang d?c bi?t khó ch?u, c?c m?nh khi chi?n d?u theo nhóm.' },
            { src: '/images/screen/5-1.png', title: '?? Nhi?m V?', desc: 'H? th?ng nhi?m v? chính tuy?n, hàng ngày và thành t?u phong phú. Ki?m ng?c h?ng qua ho?t d?ng online hàng ngày.' },
            { src: '/images/screen/6-1.png', title: '?? Trang B?', desc: 'Thu th?p trang b? t? dánh quái và h? Boss. Sách k? nang, c?i trang bi?n hóa t?o nên s? d?c nh?t cho nhân v?t.' },
            { src: '/images/screen/9-1.png', title: '?? Ð?i H?i Võ Thu?t', desc: '5 gi?i d?u m?i ngày, Võ Ðài Liên Server lúc 12h & 20h. Ch?ng minh b?n là chi?n binh m?nh nh?t vu tr?!' },
            { src: '/images/screen/10-1.png', title: '?? Lu?ng Long Nh?t Th?', desc: '4 ki?u h?p th?: Fusion Dance, Bông tai Porata, h?p th? Nam?c và h?p th? vinh vi?n. S?c m?nh nhân dôi!' },
            { src: '/images/screen/11-2.png', title: '?? Pha Lê Hóa', desc: 'Nâng c?p b?ng 5 lo?i dá quý t?i d?o Kame. 9 lo?i pha lê d?c dáo, t?o nên trang b? th?n tho?i.' },
            { src: '/images/screen/8-2.png', title: '?? Ð? T? & Thú Cu?i', desc: 'H? Boss Broly d? nh?n d? t?. Thú cu?i ph?c h?i KI khi bay, option d?c bi?t tang HP/KI cho nhân v?t.' },
            { src: '/images/screen/14-1.png', title: '?? H? Th?ng Boss', desc: 'Boss di theo c?t truy?n: Xên, Fide, Android 19 & 20. Boss Hirudegarn 22h t?i – nh?n Tr?ng Ð? T? Mabu!' },
            { src: '/images/screen/12-1.png', title: '?? Phó B?n', desc: 'H? th?ng phó b?n hàng ngày v?i ph?n thu?ng h?p d?n. Ph?i h?p d?ng d?i d? vu?t qua th? thách cam go.' },
            { src: '/images/screen/14-3.png', title: '?? Boss Frieza', desc: 'Frieza – k? th?ng tr? vu tr? v?i s?c m?nh kh?ng khi?p. H? g?c d? nh?n v?t ph?m huy?n tho?i.' },
            { src: '/images/screen/2-4.png', title: '?? R?ng Nguyên Sinh', desc: 'Khu v?c san quái luy?n level v?i c?nh quan xanh mát. Noi kh?i d?u hành trình c?a m?i chi?n binh.' },
            { src: '/images/screen/9-4.png', title: '?? Võ Ðài Liên Server', desc: 'Ð?u tru?ng PvP gi?a các server. Lúc 12h và 20h hàng ngày – ph?n thu?ng kh?ng cho ngu?i th?ng!' },
            { src: '/images/screen/8-4.png', title: '?? Nâng C?p Ð? T?', desc: 'Hu?n luy?n d? t? tr? nên m?nh m? hon. M?i d? t? có skill riêng h? tr? chi?n d?u hi?u qu?.' },
            { src: '/images/screen/14-6.png', title: '?? Boss Cell', desc: 'Cell hoàn h?o – th? thách khó khan nh?t. Drop v?t ph?m c?c hi?m!' },
            { src: '/images/screen/12-5.png', title: '?? Phó B?n Cao C?p', desc: 'Th? thách phó b?n khó v?i ph?n thu?ng tuong x?ng. C?n d?i hình ph?i h?p an ý.' },
            { src: '/images/screen/2-7.png', title: '??? Vách Núi', desc: 'Khu v?c farm quái c?p cao v?i d?a hình hi?m tr?. Chi?n binh m?nh m?i dám m?o hi?m!' },
            { src: '/images/screen/5-2.png', title: '?? Nhi?m V? Hàng Ngày', desc: 'Hoàn thành nhi?m v? hàng ngày d? nh?n Ng?c R?ng và v?t ph?m giá tr?.' },
            { src: '/images/screen/6-2.png', title: '?? Kho V?t Ph?m', desc: 'H? th?ng v?t ph?m da d?ng: trang b?, nguyên li?u, sách k? nang và v?t ph?m s? ki?n.' },
            { src: '/images/screen/14-7.png', title: '?? Boss Mabu', desc: 'Majin Buu – s?c m?nh phép thu?t kinh hoàng. C?n c? d?i m?i có th? chi?n th?ng!' },
            { src: '/images/screen/12-7.png', title: '?? Phó B?n Nhóm', desc: 'Dungeon d?c bi?t dành cho nhóm chi?n binh. Ph?n thu?ng siêu kh?ng!' },
            { src: '/images/screen/11-3.png', title: '?? Pha Lê Nâng Cao', desc: 'H? th?ng nâng c?p pha lê c?p cao, t?o ra trang b? t?i thu?ng cho chi?n binh.' }
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

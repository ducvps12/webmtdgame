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
    <title>Hu?ng D?n Choi B?n Java - Chú Bé R?ng Online</title>
    <meta name="description" content="Hu?ng d?n t?i và cài d?t gi? l?p AngelChip MicroEmulator d? choi Chú Bé R?ng Online phiên b?n Java trên PC. H? tr? proxy, treo nhi?u tab." />
    <meta name="keywords" content="Chú Bé R?ng Online, b?n Java, MicroEmulator, AngelChip, gi? l?p, proxy, hu?ng d?n cài d?t" />
    <meta name="robots" content="INDEX,FOLLOW" />

    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">

    <style>
        /* ===== GUIDE PAGE STYLES ===== */
        .guide-hero {
            min-height: 50vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 20px 50px;
            position: relative;
        }
        .guide-hero-content {
            z-index: 10;
            max-width: 750px;
            animation: fadeUp 1s var(--cubic-apple) forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        .guide-hero h1 {
            font-size: clamp(32px, 5vw, 56px);
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin: 0 0 16px;
            background: linear-gradient(135deg, #f7971e, #ffd200, #ff6b35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .guide-hero .subtitle {
            font-size: clamp(15px, 2vw, 20px);
            color: var(--text-secondary);
            margin: 0 0 32px;
            line-height: 1.6;
        }

        /* Download Banner */
        .download-banner {
            max-width: 900px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }
        .download-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            border-radius: 28px;
            padding: 44px 40px;
            display: flex;
            align-items: center;
            gap: 36px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }
        .download-card::before {
            content: '';
            position: absolute;
            top: -60%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,107,53,0.15), transparent 70%);
            border-radius: 50%;
        }
        .download-card-icon {
            width: 90px;
            height: 90px;
            border-radius: 22px;
            background: linear-gradient(135deg, #ff6b35, #ffd200);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(255,107,53,0.3);
        }
        .download-card-icon img {
            width: 52px;
            height: 52px;
            filter: brightness(10);
        }
        .download-card-info {
            flex: 1;
            z-index: 1;
        }
        .download-card-info h2 {
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            margin: 0 0 6px;
        }
        .download-card-info p {
            font-size: 14px;
            color: rgba(255,255,255,0.65);
            margin: 0 0 18px;
            line-height: 1.5;
        }
        .download-card-info .file-meta {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            margin: 0 0 14px;
        }
        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: linear-gradient(135deg, #ff6b35, #e53e3e);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(255,107,53,0.4);
        }
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(255,107,53,0.5);
        }

        /* Steps Section */
        .guide-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }
        .section-label {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #ff6b35;
            margin: 0 0 8px;
            text-align: center;
        }
        .section-heading {
            font-size: 32px;
            font-weight: 700;
            text-align: center;
            margin: 0 0 40px;
            letter-spacing: -0.02em;
        }

        /* Step Cards */
        .steps-timeline {
            position: relative;
        }
        .steps-timeline::before {
            content: '';
            position: absolute;
            left: 28px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, #ff6b35, #ffd200, #0071e3);
            border-radius: 2px;
        }
        .step-card {
            position: relative;
            margin-bottom: 28px;
            padding-left: 72px;
        }
        .step-number {
            position: absolute;
            left: 10px;
            top: 0;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b35, #e53e3e);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(255,107,53,0.3);
            z-index: 1;
        }
        .step-content {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s var(--cubic-apple), box-shadow 0.3s;
        }
        .step-content:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.08);
        }
        .step-content h3 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 10px;
            color: var(--text-primary);
        }
        .step-content p {
            font-size: 14px;
            line-height: 1.7;
            color: #555;
            margin: 0 0 8px;
        }
        .step-content p:last-child { margin-bottom: 0; }
        .step-content code {
            background: #f0f0f5;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 13px;
            color: #e53e3e;
            font-family: 'SF Mono', Consolas, monospace;
        }
        .step-content .step-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #0071e3;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }
        .step-content .step-link:hover { color: #005bb5; }

        /* Proxy Section */
        .proxy-section {
            background: linear-gradient(135deg, #f8f9ff, #eef1ff);
            border-radius: 24px;
            padding: 40px;
            margin-top: 50px;
            border: 1px solid rgba(0,113,227,0.1);
        }
        .proxy-section h2 {
            font-size: 26px;
            font-weight: 700;
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .proxy-section > p {
            font-size: 15px;
            color: #555;
            line-height: 1.7;
            margin: 0 0 24px;
        }
        .proxy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .proxy-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
            transition: transform 0.3s var(--cubic-apple), box-shadow 0.3s;
            border: 1px solid rgba(0,0,0,0.04);
        }
        .proxy-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 36px rgba(0,0,0,0.1);
        }
        .proxy-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 8px;
        }
        .proxy-card p {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin: 0 0 16px;
        }
        .proxy-card .proxy-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            background: #0071e3;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: background 0.2s, transform 0.2s;
        }
        .proxy-card .proxy-link:hover {
            background: #005bb5;
            transform: translateY(-2px);
        }
        .proxy-card .tag {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .proxy-card .tag.free { background: #e8f5e9; color: #2e7d32; }
        .proxy-card .tag.pro { background: #e3f2fd; color: #0071e3; }

        /* Tips Box */
        .tips-box {
            background: linear-gradient(135deg, #fff8e1, #fff3cd);
            border-radius: 18px;
            padding: 28px;
            margin-top: 36px;
            border: 1px solid rgba(255,165,0,0.2);
        }
        .tips-box h3 {
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tips-box ul {
            padding-left: 18px;
            margin: 0;
        }
        .tips-box ul li {
            font-size: 14px;
            line-height: 1.7;
            color: #555;
            margin-bottom: 6px;
        }

        /* Fade animation */
        .fade-section {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.7s, transform 0.7s var(--cubic-apple);
        }
        .fade-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .download-card {
                flex-direction: column;
                text-align: center;
                padding: 32px 24px;
                gap: 20px;
            }
            .steps-timeline::before { left: 22px; }
            .step-card { padding-left: 60px; }
            .step-number { left: 4px; width: 34px; height: 34px; font-size: 14px; }
            .step-content { padding: 22px; }
            .proxy-section { padding: 28px 20px; }
            .proxy-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>

    <!-- Navigation -->
    <?php include __DIR__ . '/nav.php'; ?>

    <!-- ===== HERO ===== -->
    <section class="guide-hero">
        <div class="guide-hero-content">
            <h1>Phiên B?n Java<br>Trên PC</h1>
            <p class="subtitle">Choi Chú Bé R?ng Online b?n Java ngay trên máy tính v?i gi? l?p AngelChip MicroEmulator. H? tr? proxy, treo nhi?u tab, ti?t ki?m tài nguyên.</p>
        </div>
    </section>

    <!-- ===== DOWNLOAD BANNER ===== -->
    <div class="download-banner fade-section">
        <div class="download-card">
            <div class="download-card-icon">
                <img src="/images/jar.png" alt="Java JAR">
            </div>
            <div class="download-card-info">
                <h2>T?i File Game Java (.jar)</h2>
                <p>File game d?nh d?ng .jar dùng d? m? trong gi? l?p MicroEmulator. Không c?n cài d?t, ch? c?n kéo th? vào gi? l?p.</p>
                <div class="file-meta">?? File: VPS(2).jar &bull; Dung lu?ng: ~1.6 MB</div>
                <a href="/download/VPS(2).jar" class="download-btn">? T?i File JAR</a>
            </div>
        </div>
    </div>

    <!-- ===== HU?NG D?N CÀI Ð?T ===== -->
    <div class="guide-container">
        <div class="fade-section">
            <p class="section-label">Hu?ng d?n t?ng bu?c</p>
            <h2 class="section-heading">Cài Ð?t AngelChip MicroEmulator</h2>

            <div class="steps-timeline">
                <!-- Step 1 -->
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>T?i gi? l?p AngelChip MicroEmulator</h3>
                        <p>Truy c?p trang chính th?c c?a AngelChip d? t?i phiên b?n MicroEmulator m?i nh?t. Phiên b?n này dã tích h?p s?n proxy và ch? d? t? ng? ti?t ki?m CPU/GPU.</p>
                        <a href="https://angelchip.net/share-microemulator-tich-hop-su-dung-proxy/" target="_blank" class="step-link">
                            ?? T?i AngelChip MicroEmulator ?
                        </a>
                        <p style="margin-top:8px; font-size:13px; color:#888;">Ho?c t?i tr?c ti?p file JAR: <a href="https://angelchip.net/files/share/AngelChipEmulator_V2Proxy.jar" target="_blank" style="color:#0071e3; font-weight:600;">AngelChipEmulator_V2Proxy.jar</a></p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Cài d?t Java Runtime (n?u chua có)</h3>
                        <p>MicroEmulator yêu c?u <strong>Java Runtime Environment (JRE)</strong> d? ch?y. N?u máy b?n chua có Java, hãy t?i và cài d?t t? trang chính th?c:</p>
                        <a href="https://www.java.com/download/" target="_blank" class="step-link">
                            ? T?i Java Runtime ?
                        </a>
                        <p style="margin-top:8px;">Sau khi cài Java xong, kh?i d?ng l?i máy tính d? hoàn t?t.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>M? gi? l?p và n?p file game</h3>
                        <p>Ch?y file <code>AngelChipEmulator_V2Proxy.jar</code> (click dúp). Giao di?n gi? l?p s? m? ra.</p>
                        <p>Vào menu <strong>File ? Open MIDlet File</strong> r?i ch?n file game <code>VPS(2).jar</code> dã t?i ? trên. Game s? kh?i d?ng ngay trên gi? l?p!</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Ðang nh?p và choi</h3>
                        <p>Nh?p tài kho?n và m?t kh?u dã dang ký trên website. N?u chua có tài kho?n, hãy <a href="/register" style="color:#0071e3; font-weight:600;">dang ký mi?n phí</a> ngay.</p>
                        <p>Ch?n server, t?o nhân v?t và b?t d?u cu?c phiêu luu!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== PROXY SECTION ===== -->
        <section class="proxy-section fade-section">
            <h2>?? C?n Treo Nhi?u Tab? S? D?ng Proxy!</h2>
            <p>N?u b?n mu?n treo nhi?u nick cùng lúc trên m?t máy, b?n c?n s? d?ng proxy d? tránh b? gi?i h?n IP. AngelChip MicroEmulator dã tích h?p s?n tính nang cài d?t proxy r?t ti?n l?i.</p>

            <div class="proxy-grid">
                <div class="proxy-card">
                    <span class="tag pro">? Khuyên dùng</span>
                    <h3>MTD Proxy</h3>
                    <p>Proxy dân cu Vi?t Nam ch?t lu?ng cao, t?c d? ?n d?nh. H? tr? SOCKS5, phù h?p choi game treo nhi?u tab. Giá t? 800d/proxy.</p>
                    <a href="https://mtdproxy.com/" target="_blank" class="proxy-link">Mua Proxy MTD ?</a>
                </div>
                <div class="proxy-card">
                    <span class="tag free">?? Mi?n phí</span>
                    <h3>Proxy mi?n phí</h3>
                    <p>B?n có th? tìm proxy SOCKS4/SOCKS5 mi?n phí trên m?ng. Tuy nhiên t?c d? thu?ng ch?m và không ?n d?nh, th?i gian s? d?ng ng?n.</p>
                    <a href="https://www.google.com/search?q=free+socks5+proxy+list" target="_blank" class="proxy-link" style="background:#6c757d;">Tìm Proxy Free ?</a>
                </div>
            </div>

            <!-- Proxy Setup Steps -->
            <div class="tips-box" style="background: linear-gradient(135deg, #e8f0fe, #dbe5f8); border-color: rgba(0,113,227,0.15); margin-top: 24px;">
                <h3>?? Cách cài d?t Proxy trong AngelChip</h3>
                <ul>
                    <li>M? gi? l?p ? vào menu <strong>"AngelChip"</strong> trên thanh MenuBar ? ch?n <strong>"Cài d?t Proxy"</strong>.</li>
                    <li><strong>Proxy Host:</strong> Ði?n d?a ch? IP ho?c domain proxy (ngu?i bán cung c?p).</li>
                    <li><strong>Proxy Port:</strong> Ði?n port proxy.</li>
                    <li><strong>User / Password:</strong> Ði?n n?u proxy yêu c?u, b? tr?ng n?u không.</li>
                    <li>Tích ch?n <strong>"B?t s? d?ng Proxy"</strong> ? nh?n <strong>OK</strong> ? kh?i d?ng l?i Emulator.</li>
                    <li>Các tab m? sau dó s? t? d?ng dùng proxy. B? tích d? t?t, không c?n xóa cài d?t.</li>
                </ul>
            </div>
        </section>

        <!-- ===== TIPS ===== -->
        <div class="tips-box fade-section" style="margin-top: 36px;">
            <h3>?? M?o H?u Ích</h3>
            <ul>
                <li><strong>Ch? d? t? ng?:</strong> Vào menu "AngelChip" ? tích "Ch? d? t? ng?". Gi? l?p s? t? ng? sau 5 phút không thao tác, gi?m tiêu hao CPU/GPU. Nh?n phím b?t k? d? dánh th?c.</li>
                <li><strong>Ng? ngay l?p t?c:</strong> B?m phím <code>\</code> trên bàn phím d? kích ho?t ch? d? ng? ngay.</li>
                <li><strong>Treo nhi?u tab:</strong> M? nhi?u c?a s? gi? l?p, m?i c?a s? dùng m?t proxy khác nhau d? không b? gi?i h?n IP.</li>
                <li><strong>Lo?i proxy:</strong> Khi thuê proxy, ch?n lo?i <strong>SOCKS4</strong> ho?c <strong>SOCKS5</strong> d? tuong thích v?i gi? l?p.</li>
                <li><strong>Proxy VN:</strong> Nên mua proxy server Vi?t Nam d? có t?c d? k?t n?i t?t nh?t khi choi game.</li>
            </ul>
        </div>

        <!-- ===== CTA ===== -->
        <section class="fade-section" style="text-align:center; padding: 50px 0 20px;">
            <h2 style="font-size:32px; font-weight:700; margin:0 0 12px;">B?t Ð?u Ngay!</h2>
            <p style="color:var(--text-secondary); font-size:16px; margin:0 0 28px;">T?i game, cài gi? l?p và chi?n d?u cùng hàng v?n chi?n binh.</p>
            <div style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap;">
                <a href="/download/VPS(2).jar" class="btn-pill btn-black" style="text-decoration:none;">? T?i File Game JAR</a>
                <a href="https://angelchip.net/share-microemulator-tich-hop-su-dung-proxy/" target="_blank" class="btn-pill btn-outline" style="text-decoration:none;">T?i AngelChip Emulator</a>
                <a href="/register" class="btn-pill btn-outline" style="text-decoration:none;">Ðang Ký Tài Kho?n</a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/footer.php'; ?>

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
    </script>
</body>
</html>

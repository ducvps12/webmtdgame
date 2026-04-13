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
    <title>Hướng Dẫn Chơi Bản Java - Chú Bé Rồng Online</title>
    <meta name="description" content="Hướng dẫn tải và cài đặt giả lập AngelChip MicroEmulator để chơi Chú Bé Rồng Online phiên bản Java trên PC. Hỗ trợ proxy, treo nhiều tab." />
    <meta name="keywords" content="Chú Bé Rồng Online, bản Java, MicroEmulator, AngelChip, giả lập, proxy, hướng dẫn cài đặt" />
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
            <h1>Phiên Bản Java<br>Trên PC</h1>
            <p class="subtitle">Chơi Chú Bé Rồng Online bản Java ngay trên máy tính với giả lập AngelChip MicroEmulator. Hỗ trợ proxy, treo nhiều tab, tiết kiệm tài nguyên.</p>
        </div>
    </section>

    <!-- ===== DOWNLOAD BANNER ===== -->
    <div class="download-banner fade-section">
        <div class="download-card">
            <div class="download-card-icon">
                <img src="/images/jar.png" alt="Java JAR">
            </div>
            <div class="download-card-info">
                <h2>Tải File Game Java (.jar)</h2>
                <p>File game định dạng .jar dùng để mở trong giả lập MicroEmulator. Không cần cài đặt, chỉ cần kéo thả vào giả lập.</p>
                <div class="file-meta">📦 File: VPS(2).jar &bull; Dung lượng: ~1.6 MB</div>
                <a href="/download/VPS(2).jar" class="download-btn">⬇ Tải File JAR</a>
            </div>
        </div>
    </div>

    <!-- ===== HƯỚNG DẪN CÀI ĐẶT ===== -->
    <div class="guide-container">
        <div class="fade-section">
            <p class="section-label">Hướng dẫn từng bước</p>
            <h2 class="section-heading">Cài Đặt AngelChip MicroEmulator</h2>

            <div class="steps-timeline">
                <!-- Step 1 -->
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Tải giả lập AngelChip MicroEmulator</h3>
                        <p>Truy cập trang chính thức của AngelChip để tải phiên bản MicroEmulator mới nhất. Phiên bản này đã tích hợp sẵn proxy và chế độ tự ngủ tiết kiệm CPU/GPU.</p>
                        <a href="https://angelchip.net/share-microemulator-tich-hop-su-dung-proxy/" target="_blank" class="step-link">
                            🔗 Tải AngelChip MicroEmulator →
                        </a>
                        <p style="margin-top:8px; font-size:13px; color:#888;">Hoặc tải trực tiếp file JAR: <a href="https://angelchip.net/files/share/AngelChipEmulator_V2Proxy.jar" target="_blank" style="color:#0071e3; font-weight:600;">AngelChipEmulator_V2Proxy.jar</a></p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Cài đặt Java Runtime (nếu chưa có)</h3>
                        <p>MicroEmulator yêu cầu <strong>Java Runtime Environment (JRE)</strong> để chạy. Nếu máy bạn chưa có Java, hãy tải và cài đặt từ trang chính thức:</p>
                        <a href="https://www.java.com/download/" target="_blank" class="step-link">
                            ☕ Tải Java Runtime →
                        </a>
                        <p style="margin-top:8px;">Sau khi cài Java xong, khởi động lại máy tính để hoàn tất.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Mở giả lập và nạp file game</h3>
                        <p>Chạy file <code>AngelChipEmulator_V2Proxy.jar</code> (click đúp). Giao diện giả lập sẽ mở ra.</p>
                        <p>Vào menu <strong>File → Open MIDlet File</strong> rồi chọn file game <code>VPS(2).jar</code> đã tải ở trên. Game sẽ khởi động ngay trên giả lập!</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Đăng nhập và chơi</h3>
                        <p>Nhập tài khoản và mật khẩu đã đăng ký trên website. Nếu chưa có tài khoản, hãy <a href="/register" style="color:#0071e3; font-weight:600;">đăng ký miễn phí</a> ngay.</p>
                        <p>Chọn server, tạo nhân vật và bắt đầu cuộc phiêu lưu!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== PROXY SECTION ===== -->
        <section class="proxy-section fade-section">
            <h2>🌐 Cần Treo Nhiều Tab? Sử Dụng Proxy!</h2>
            <p>Nếu bạn muốn treo nhiều nick cùng lúc trên một máy, bạn cần sử dụng proxy để tránh bị giới hạn IP. AngelChip MicroEmulator đã tích hợp sẵn tính năng cài đặt proxy rất tiện lợi.</p>

            <div class="proxy-grid">
                <div class="proxy-card">
                    <span class="tag pro">⭐ Khuyên dùng</span>
                    <h3>MTD Proxy</h3>
                    <p>Proxy dân cư Việt Nam chất lượng cao, tốc độ ổn định. Hỗ trợ SOCKS5, phù hợp chơi game treo nhiều tab. Giá từ 800đ/proxy.</p>
                    <a href="https://mtdproxy.com/" target="_blank" class="proxy-link">Mua Proxy MTD →</a>
                </div>
                <div class="proxy-card">
                    <span class="tag free">🆓 Miễn phí</span>
                    <h3>Proxy miễn phí</h3>
                    <p>Bạn có thể tìm proxy SOCKS4/SOCKS5 miễn phí trên mạng. Tuy nhiên tốc độ thường chậm và không ổn định, thời gian sử dụng ngắn.</p>
                    <a href="https://www.google.com/search?q=free+socks5+proxy+list" target="_blank" class="proxy-link" style="background:#6c757d;">Tìm Proxy Free →</a>
                </div>
            </div>

            <!-- Proxy Setup Steps -->
            <div class="tips-box" style="background: linear-gradient(135deg, #e8f0fe, #dbe5f8); border-color: rgba(0,113,227,0.15); margin-top: 24px;">
                <h3>⚙️ Cách cài đặt Proxy trong AngelChip</h3>
                <ul>
                    <li>Mở giả lập → vào menu <strong>"AngelChip"</strong> trên thanh MenuBar → chọn <strong>"Cài đặt Proxy"</strong>.</li>
                    <li><strong>Proxy Host:</strong> Điền địa chỉ IP hoặc domain proxy (người bán cung cấp).</li>
                    <li><strong>Proxy Port:</strong> Điền port proxy.</li>
                    <li><strong>User / Password:</strong> Điền nếu proxy yêu cầu, bỏ trống nếu không.</li>
                    <li>Tích chọn <strong>"Bật sử dụng Proxy"</strong> → nhấn <strong>OK</strong> → khởi động lại Emulator.</li>
                    <li>Các tab mở sau đó sẽ tự động dùng proxy. Bỏ tích để tắt, không cần xóa cài đặt.</li>
                </ul>
            </div>
        </section>

        <!-- ===== TIPS ===== -->
        <div class="tips-box fade-section" style="margin-top: 36px;">
            <h3>💡 Mẹo Hữu Ích</h3>
            <ul>
                <li><strong>Chế độ tự ngủ:</strong> Vào menu "AngelChip" → tích "Chế độ tự ngủ". Giả lập sẽ tự ngủ sau 5 phút không thao tác, giảm tiêu hao CPU/GPU. Nhấn phím bất kỳ để đánh thức.</li>
                <li><strong>Ngủ ngay lập tức:</strong> Bấm phím <code>\</code> trên bàn phím để kích hoạt chế độ ngủ ngay.</li>
                <li><strong>Treo nhiều tab:</strong> Mở nhiều cửa sổ giả lập, mỗi cửa sổ dùng một proxy khác nhau để không bị giới hạn IP.</li>
                <li><strong>Loại proxy:</strong> Khi thuê proxy, chọn loại <strong>SOCKS4</strong> hoặc <strong>SOCKS5</strong> để tương thích với giả lập.</li>
                <li><strong>Proxy VN:</strong> Nên mua proxy server Việt Nam để có tốc độ kết nối tốt nhất khi chơi game.</li>
            </ul>
        </div>

        <!-- ===== CTA ===== -->
        <section class="fade-section" style="text-align:center; padding: 50px 0 20px;">
            <h2 style="font-size:32px; font-weight:700; margin:0 0 12px;">Bắt Đầu Ngay!</h2>
            <p style="color:var(--text-secondary); font-size:16px; margin:0 0 28px;">Tải game, cài giả lập và chiến đấu cùng hàng vạn chiến binh.</p>
            <div style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap;">
                <a href="/download/VPS(2).jar" class="btn-pill btn-black" style="text-decoration:none;">⬇ Tải File Game JAR</a>
                <a href="https://angelchip.net/share-microemulator-tich-hop-su-dung-proxy/" target="_blank" class="btn-pill btn-outline" style="text-decoration:none;">Tải AngelChip Emulator</a>
                <a href="/register" class="btn-pill btn-outline" style="text-decoration:none;">Đăng Ký Tài Khoản</a>
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

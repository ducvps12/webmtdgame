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
    <title>Hướng Dẫn Cài Game IPA iPhone - Chú Bé Rồng Online</title>
    <meta name="description" content="Hướng dẫn chi tiết cách tải và cài đặt file IPA game Chú Bé Rồng Online trên iPhone/iPad không cần jailbreak. Sử dụng AltStore hoặc Sideloadly." />
    <meta name="keywords" content="Chú Bé Rồng Online, cài IPA, iPhone, iOS, AltStore, Sideloadly, hướng dẫn cài game iPhone" />
    <meta name="robots" content="INDEX,FOLLOW" />

    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">

    <style>
        /* ===== IOS GUIDE PAGE STYLES ===== */
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
            background: linear-gradient(135deg, #007aff, #5856d6, #af52de);
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
            background: linear-gradient(135deg, #1c1c1e 0%, #2c2c2e 50%, #3a3a3c 100%);
            border-radius: 28px;
            padding: 44px 40px;
            display: flex;
            align-items: center;
            gap: 36px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
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
            background: radial-gradient(circle, rgba(0,122,255,0.15), transparent 70%);
            border-radius: 50%;
        }
        .download-card-icon {
            width: 90px;
            height: 90px;
            border-radius: 22px;
            background: linear-gradient(135deg, #007aff, #5856d6);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(0,122,255,0.35);
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
            background: linear-gradient(135deg, #007aff, #5856d6);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(0,122,255,0.4);
        }
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,122,255,0.5);
        }
        .download-btn.disabled {
            background: linear-gradient(135deg, #636366, #48484a);
            cursor: not-allowed;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        .download-btn.disabled:hover {
            transform: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        .coming-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: rgba(255,159,10,0.2);
            color: #ff9f0a;
            margin-left: 12px;
        }

        /* Video Section */
        .video-section {
            max-width: 900px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }
        .video-card {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        }
        .video-card-header {
            padding: 24px 28px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        .video-card-header h2 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .video-card-header p {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
        }
        .video-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 */
            background: #000;
        }
        .video-wrapper iframe {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            border: none;
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
            color: #007aff;
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
            background: linear-gradient(180deg, #007aff, #5856d6, #af52de);
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
            background: linear-gradient(135deg, #007aff, #5856d6);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,122,255,0.3);
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
            color: #5856d6;
            font-family: 'SF Mono', Consolas, monospace;
        }
        .step-content .step-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #007aff;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }
        .step-content .step-link:hover { color: #0056b3; }

        /* Method Cards */
        .method-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 32px;
        }
        .method-card {
            background: #fff;
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
            transition: transform 0.3s var(--cubic-apple), box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }
        .method-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            border-radius: 20px 20px 0 0;
        }
        .method-card.recommended::before {
            background: linear-gradient(90deg, #007aff, #5856d6);
        }
        .method-card.alternative::before {
            background: linear-gradient(90deg, #ff9f0a, #ff6723);
        }
        .method-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 36px rgba(0,0,0,0.1);
        }
        .method-card .tag {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 14px;
        }
        .method-card .tag.rec { background: rgba(0,122,255,0.1); color: #007aff; }
        .method-card .tag.alt { background: rgba(255,159,10,0.12); color: #d97706; }
        .method-card h3 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px;
        }
        .method-card p {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin: 0 0 16px;
        }
        .method-card .method-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            background: #007aff;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: background 0.2s, transform 0.2s;
        }
        .method-card .method-link:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        .method-card.alternative .method-link {
            background: #ff9f0a;
        }
        .method-card.alternative .method-link:hover {
            background: #e68a00;
        }

        /* Warning / Tips Box */
        .warning-box {
            background: linear-gradient(135deg, #fff3cd, #fff8e1);
            border-radius: 18px;
            padding: 28px;
            margin-top: 36px;
            border: 1px solid rgba(255,159,10,0.25);
        }
        .warning-box h3 {
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #92400e;
        }
        .warning-box ul {
            padding-left: 18px;
            margin: 0;
        }
        .warning-box ul li {
            font-size: 14px;
            line-height: 1.7;
            color: #78350f;
            margin-bottom: 6px;
        }

        .tips-box {
            background: linear-gradient(135deg, #e8f4fd, #dbeafe);
            border-radius: 18px;
            padding: 28px;
            margin-top: 36px;
            border: 1px solid rgba(0,122,255,0.15);
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

        /* Alternative methods section */
        .alt-section {
            background: linear-gradient(135deg, #f8f9ff, #eef1ff);
            border-radius: 24px;
            padding: 40px;
            margin-top: 50px;
            border: 1px solid rgba(0,122,255,0.1);
        }
        .alt-section h2 {
            font-size: 26px;
            font-weight: 700;
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alt-section > p {
            font-size: 15px;
            color: #555;
            line-height: 1.7;
            margin: 0 0 24px;
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
            .alt-section { padding: 28px 20px; }
            .method-grid { grid-template-columns: 1fr; }
            .video-card-header { padding: 18px 20px; }
            .coming-badge { margin-left: 0; margin-top: 6px; display: flex; }
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
            <h1>Cài Game Trên<br>iPhone / iPad</h1>
            <p class="subtitle">Hướng dẫn chi tiết cách tải và cài đặt file IPA game Chú Bé Rồng Online trên iPhone/iPad. Không cần jailbreak, dễ dàng thực hiện.</p>
        </div>
    </section>

    <!-- ===== DOWNLOAD BANNER ===== -->
    <div class="download-banner fade-section">
        <div class="download-card">
            <div class="download-card-icon">
                <img src="/images/ip.png" alt="iPhone IPA">
            </div>
            <div class="download-card-info">
                <h2>Tải File Game IPA
                    <span class="coming-badge">⏳ Sắp ra mắt</span>
                </h2>
                <p>File game định dạng .ipa dành cho iPhone/iPad. Cài đặt thông qua AltStore hoặc Sideloadly trên máy tính.</p>
                <div class="file-meta">📦 File: ChuBeRong.ipa &bull; Đang cập nhật...</div>
                <a href="#" class="download-btn disabled" onclick="return false;">⏳ Đang Cập Nhật</a>
            </div>
        </div>
    </div>

    <!-- ===== VIDEO HƯỚNG DẪN ===== -->
    <div class="video-section fade-section">
        <div class="video-card">
            <div class="video-card-header">
                <h2>🎬 Video Hướng Dẫn Chi Tiết</h2>
                <p>Xem video hướng dẫn từng bước cài đặt game IPA trên iPhone / iPad</p>
            </div>
            <div class="video-wrapper">
                <iframe 
                    src="https://www.youtube.com/embed/ESvItgNkEtM" 
                    title="Hướng dẫn cài game IPA trên iPhone" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    allowfullscreen
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>

    <!-- ===== HƯỚNG DẪN CÀI ĐẶT ===== -->
    <div class="guide-container">
        <div class="fade-section">
            <p class="section-label">Hướng dẫn từng bước</p>
            <h2 class="section-heading">Cài Đặt IPA Trên iPhone</h2>

            <div class="steps-timeline">
                <!-- Step 1 -->
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Tải AltStore lên iPhone</h3>
                        <p>AltStore là công cụ miễn phí để cài file IPA lên iPhone mà không cần jailbreak. Bạn cần một máy tính (Windows hoặc Mac) để cài AltStore lên điện thoại.</p>
                        <p><strong>Trên máy tính:</strong></p>
                        <p>① Tải và cài đặt <a href="https://www.apple.com/itunes/" target="_blank" style="color:#007aff; font-weight:600;">iTunes</a> (bản từ Apple, không phải Microsoft Store).</p>
                        <p>② Tải và cài đặt <a href="https://support.apple.com/en-us/102858" target="_blank" style="color:#007aff; font-weight:600;">iCloud</a> (bản từ Apple).</p>
                        <p>③ Tải <strong>AltServer</strong> từ trang chính thức:</p>
                        <a href="https://altstore.io/" target="_blank" class="step-link">
                            🔗 Truy cập AltStore.io →
                        </a>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Cài AltStore lên iPhone qua AltServer</h3>
                        <p>Kết nối iPhone với máy tính bằng cáp USB. Tin cậy máy tính trên iPhone nếu được hỏi.</p>
                        <p>Mở <strong>AltServer</strong> trên máy tính → nhấp vào icon AltServer trên thanh taskbar → chọn <strong>"Install AltStore"</strong> → chọn iPhone của bạn.</p>
                        <p>Nhập <strong>Apple ID</strong> và mật khẩu khi được yêu cầu (dùng để ký ứng dụng, hoàn toàn an toàn).</p>
                        <p style="font-size:13px; color:#888;">⚠️ Nên sử dụng một Apple ID phụ nếu bạn lo ngại bảo mật.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Tin cậy chứng chỉ trên iPhone</h3>
                        <p>Sau khi cài xong AltStore, vào <strong>Cài đặt → Cài đặt chung → VPN & Quản lý thiết bị</strong> trên iPhone.</p>
                        <p>Tìm mục liên quan đến Apple ID bạn vừa nhập → nhấn <strong>"Tin Cậy"</strong>.</p>
                        <p>Bây giờ bạn có thể mở AltStore từ màn hình chính.</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Tải file IPA game về iPhone</h3>
                        <p>Tải file <code>ChuBeRong.ipa</code> về iPhone (qua Safari hoặc truyền từ máy tính).</p>
                        <p>Bạn cũng có thể tải file IPA trên máy tính rồi AirDrop hoặc dùng cáp USB để copy sang iPhone.</p>
                        <p style="font-size:13px; color:#888;">📌 File IPA đang được cập nhật, vui lòng quay lại sau để tải.</p>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="step-card">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <h3>Cài file IPA qua AltStore</h3>
                        <p>Mở ứng dụng <strong>AltStore</strong> trên iPhone → chuyển sang tab <strong>"My Apps"</strong> → nhấn dấu <strong>"+"</strong> ở góc trên.</p>
                        <p>Chọn file <code>ChuBeRong.ipa</code> đã tải → AltStore sẽ tự động cài đặt game lên iPhone.</p>
                        <p><strong>Lưu ý:</strong> iPhone phải đang kết nối cùng mạng WiFi với máy tính chạy AltServer, hoặc kết nối qua cáp USB.</p>
                    </div>
                </div>

                <!-- Step 6 -->
                <div class="step-card">
                    <div class="step-number">6</div>
                    <div class="step-content">
                        <h3>Mở game và đăng nhập</h3>
                        <p>Game sẽ xuất hiện trên màn hình chính iPhone. Mở game, nhập tài khoản đã đăng ký trên website.</p>
                        <p>Nếu chưa có tài khoản, hãy <a href="/register" style="color:#007aff; font-weight:600;">đăng ký miễn phí</a> ngay.</p>
                        <p>Chọn server, tạo nhân vật và bắt đầu phiêu lưu!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== PHƯƠNG PHÁP THAY THẾ ===== -->
        <section class="alt-section fade-section">
            <h2>📱 Công Cụ Cài IPA Khác</h2>
            <p>Ngoài AltStore, bạn cũng có thể sử dụng các công cụ sau để cài file IPA lên iPhone. Chọn phương pháp phù hợp nhất với bạn.</p>

            <div class="method-grid">
                <div class="method-card recommended">
                    <span class="tag rec">⭐ Khuyên dùng</span>
                    <h3>AltStore</h3>
                    <p>Miễn phí, phổ biến nhất, hoạt động ổn định. Cần máy tính để cài lần đầu. Tự động gia hạn chữ ký mỗi 7 ngày khi kết nối cùng WiFi.</p>
                    <a href="https://altstore.io/" target="_blank" class="method-link">Truy cập AltStore →</a>
                </div>
                <div class="method-card alternative">
                    <span class="tag alt">🔧 Thay thế</span>
                    <h3>Sideloadly</h3>
                    <p>Công cụ miễn phí trên Windows/Mac, giao diện đơn giản. Kết nối iPhone qua USB, chọn file IPA và bấm Start. Không cần cài thêm gì trên iPhone.</p>
                    <a href="https://sideloadly.io/" target="_blank" class="method-link">Truy cập Sideloadly →</a>
                </div>
            </div>
        </section>

        <!-- ===== LƯU Ý QUAN TRỌNG ===== -->
        <div class="warning-box fade-section">
            <h3>⚠️ Lưu Ý Quan Trọng</h3>
            <ul>
                <li><strong>Gia hạn chữ ký:</strong> Với tài khoản Apple ID miễn phí, ứng dụng chỉ hoạt động 7 ngày. Sau đó bạn cần gia hạn bằng cách kết nối lại với AltServer.</li>
                <li><strong>Không cần jailbreak:</strong> Tất cả phương pháp trên đều không yêu cầu jailbreak iPhone.</li>
                <li><strong>Apple ID:</strong> Nên tạo một Apple ID phụ riêng để sử dụng cho việc ký ứng dụng, tránh ảnh hưởng tài khoản chính.</li>
                <li><strong>Phiên bản iOS:</strong> Yêu cầu iOS 14.0 trở lên. Khuyến nghị sử dụng phiên bản iOS mới nhất.</li>
                <li><strong>File IPA:</strong> Chỉ tải file IPA từ trang chủ chính thức. Không sử dụng file từ nguồn không rõ ràng.</li>
            </ul>
        </div>

        <!-- ===== TIPS ===== -->
        <div class="tips-box fade-section">
            <h3>💡 Mẹo Hữu Ích</h3>
            <ul>
                <li><strong>Tự động gia hạn:</strong> Để AltServer chạy nền trên máy tính và kết nối iPhone cùng WiFi. AltStore sẽ tự động gia hạn chữ ký khi còn 1-2 ngày.</li>
                <li><strong>Lỗi không cài được:</strong> Thử xóa AltStore trên iPhone, gỡ AltServer trên máy tính, cài lại từ đầu. Đảm bảo iTunes và iCloud đúng phiên bản từ Apple.</li>
                <li><strong>Kết nối USB:</strong> Nếu WiFi không ổn định, dùng cáp Lightning/USB-C để kết nối trực tiếp iPhone với máy tính khi gia hạn.</li>
                <li><strong>Sao lưu dữ liệu:</strong> Tài khoản game được lưu trên server, nên khi cài lại game bạn chỉ cần đăng nhập lại là được.</li>
            </ul>
        </div>

        <!-- ===== CTA ===== -->
        <section class="fade-section" style="text-align:center; padding: 50px 0 20px;">
            <h2 style="font-size:32px; font-weight:700; margin:0 0 12px;">Sẵn Sàng Chiến Đấu?</h2>
            <p style="color:var(--text-secondary); font-size:16px; margin:0 0 28px;">Cài game ngay trên iPhone và tham gia cùng hàng vạn chiến binh!</p>
            <div style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap;">
                <a href="/register" class="btn-pill btn-black" style="text-decoration:none;">Đăng Ký Tài Khoản</a>
                <a href="/" class="btn-pill btn-outline" style="text-decoration:none;">Về Trang Chủ</a>
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

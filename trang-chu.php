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
    <title>Chú Bé Rồng Online - Đẳng Cấp PC</title>
    <meta name="description" content="Website chính thức của Chú Bé Rồng Online – Game Bay Vien Ngoc Rong Mobile nhập vai trực tuyến trên máy tính và điện thoại về Game 7 Viên Ngọc Rồng hấp dẫn nhất hiện nay!" />
    
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    
    <!-- Link CSS Mới Chuẩn Apple -->
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
</head>
<body>
    <!-- Background Particles -->
    <canvas id="antigravity-particles"></canvas>

    <!-- Navigation Header -->
    <nav class="apple-nav">
        <div class="nav-left">
            <a href="/"><img src="/images/logo_sk_he.png" alt="Logo" class="nav-logo"></a>
            <ul class="nav-links">
                <li><a href="/" class="active">Trang Chủ</a></li>
                <li><a href="/gioi-thieu">Giới Thiệu</a></li>
                <li><a href="/forum">Diễn Đàn</a></li>
                <li><a href="https://zalo.me/g/atqsvzxmfalbhc3n4d7d" target="_blank">Cộng Đồng</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="user-balance" style="margin-right: 15px;">Chào <b><?php echo htmlspecialchars($_SESSION['player_name'] ?? $_SESSION['username']); ?></b> | Số dư: <span style="color: #e65c00; font-weight: bold;"><?php echo number_format($_SESSION['coin'] ?? 0); ?> đ</span></span>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <a href="/admin" class="btn-pill small" style="background: #e53e3e; color: #fff; border: none; margin-right: 6px;">🛡️ Admin</a>
                <?php endif; ?>
                <a href="/app/logout" class="btn-pill btn-outline small">Đăng xuất</a>
            <?php else: ?>
                <a href="/login" class="btn-pill btn-outline small">Đăng Nhập</a>
                <a href="/register" class="btn-pill btn-black small">Đăng Ký</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Hero Landing -->
    <section class="apple-hero">
        <div class="hero-content">
            <h1>Sống lại Huyền Thoại.<br>Trải nghiệm siêu việt.</h1>
            <p>Khám phá thế giới giả tưởng bất tận cùng đồng đội. Hỗ trợ đồ họa HD mượt mà trên đa nền tảng PC, Android và iOS.</p>
            
            <div class="hero-buttons">
                <a href="/download/pc.rar" class="btn-pill btn-black">Tải xuống cho Windows</a>
                <a href="/gioi-thieu" class="btn-pill btn-outline">Tìm hiểu thêm</a>
            </div>

            <!-- Download Platforms -->
            <div class="platforms-grid">
                <a href="/huong-dan-java" class="platform-card">
                    <div class="platform-icon"><img src="/images/jar.png" alt="Java"></div>
                    <span>Phiên bản Java</span>
                </a>
                <a href="/download/adr.apk" class="platform-card">
                    <div class="platform-icon"><img src="/images/play.png" alt="Android"></div>
                    <span>Android APK</span>
                </a>
                <a href="/download/pc.rar" class="platform-card">
                    <div class="platform-icon"><img src="/images/pc.png" alt="PC"></div>
                    <span>Bản PC Trực Tiếp</span>
                </a>
                <a href="https://testflight.apple.com/join/Jj9kBWMa" target="_blank" class="platform-card">
                    <div class="platform-icon"><img src="/images/ip.png" alt="iOS"></div>
                    <span>iPhone / iOS (TestFlight)</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="landing-section">
        <h2 class="section-title">Khám Phá Tính Năng<br>Làm Nên Thời Đại Mới</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">✨</div>
                <h3>Đồ Hoa Cải Tiến</h3>
                <p>Nâng cấp texture, độ phân giải HD cùng hiệu ứng kĩ năng mượt mà 60 FPS, mang lại trải nghiệm thị giác tuyệt vời.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🤝</div>
                <h3>Giao Dịch Tự Do</h3>
                <p>Hệ thống kinh tế cân bằng, chợ ảo liên server giúp người chơi trao đổi vật phẩm hoàn toàn tự do và minh bạch.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚔️</div>
                <h3>Cộng Đồng Sôi Động</h3>
                <p>Gặp gỡ, kết giao hàng vạn đồng đạo trên các hội nhóm, thử sức săn Boss đa dạng với tính chiến thuật cao.</p>
            </div>
        </div>
    </section>

    <!-- Showcase Section -->
    <section class="landing-section" style="padding-top: 20px;">
        <div class="showcase-section">
            <img src="/images/dragon_ball_showcase.png" alt="Epic Environment" class="showcase-bg">
            <div class="showcase-content">
                <h2>Bắt Đầu Chuyến Hành Trình</h2>
                <p>Hàng vạn cư dân đang chờ đón bạn.</p>
                <br>
                <a href="/register" class="btn-pill btn-white" style="color: #000; background: #fff; margin-top: 20px; text-decoration: none;">Đăng kí miễn phí</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="text-align: center; padding: 40px; color: var(--text-secondary); font-size: 14px;">
        Bản quyền thuộc về Chú Bé Rồng Online - Thiết kế bởi Antigravity UI
    </footer>

    <!-- JS Particles Effect -->
    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
</body>
</html>

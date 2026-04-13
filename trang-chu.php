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
    <title>Chú Bé R?ng Online - Ð?ng C?p PC</title>
    <meta name="description" content="Website chính th?c c?a Chú Bé R?ng Online – Game Bay Vien Ngoc Rong Mobile nh?p vai tr?c tuy?n trên máy tính và di?n tho?i v? Game 7 Viên Ng?c R?ng h?p d?n nh?t hi?n nay!" />
    
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    
    <!-- Link CSS M?i Chu?n Apple -->
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
                <li><a href="/" class="active">Trang Ch?</a></li>
                <li><a href="/gioi-thieu">Gi?i Thi?u</a></li>
                <li><a href="/forum">Di?n Ðàn</a></li>
                <li><a href="https://zalo.me/g/atqsvzxmfalbhc3n4d7d" target="_blank">C?ng Ð?ng</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="user-balance" style="margin-right: 15px;">Chào <b><?php echo htmlspecialchars($_SESSION['player_name'] ?? $_SESSION['username']); ?></b> | S? du: <span style="color: #e65c00; font-weight: bold;"><?php echo number_format($_SESSION['coin'] ?? 0); ?> d</span></span>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <a href="/admin" class="btn-pill small" style="background: #e53e3e; color: #fff; border: none; margin-right: 6px;">??? Admin</a>
                <?php endif; ?>
                <a href="/app/logout" class="btn-pill btn-outline small">Ðang xu?t</a>
            <?php else: ?>
                <a href="/login" class="btn-pill btn-outline small">Ðang Nh?p</a>
                <a href="/register" class="btn-pill btn-black small">Ðang Ký</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Hero Landing -->
    <section class="apple-hero">
        <div class="hero-content">
            <h1>S?ng l?i Huy?n Tho?i.<br>Tr?i nghi?m siêu vi?t.</h1>
            <p>Khám phá th? gi?i gi? tu?ng b?t t?n cùng d?ng d?i. H? tr? d? h?a HD mu?t mà trên da n?n t?ng PC, Android và iOS.</p>
            
            <div class="hero-buttons">
                <a href="/download/pc.rar" class="btn-pill btn-black">T?i xu?ng cho Windows</a>
                <a href="/gioi-thieu" class="btn-pill btn-outline">Tìm hi?u thêm</a>
            </div>

            <!-- Download Platforms -->
            <div class="platforms-grid">
                <a href="/huong-dan-java" class="platform-card">
                    <div class="platform-icon"><img src="/images/jar.png" alt="Java"></div>
                    <span>Phiên b?n Java</span>
                </a>
                <a href="/download/adr.apk" class="platform-card">
                    <div class="platform-icon"><img src="/images/play.png" alt="Android"></div>
                    <span>Android APK</span>
                </a>
                <a href="/download/pc.rar" class="platform-card">
                    <div class="platform-icon"><img src="/images/pc.png" alt="PC"></div>
                    <span>B?n PC Tr?c Ti?p</span>
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
        <h2 class="section-title">Khám Phá Tính Nang<br>Làm Nên Th?i Ð?i M?i</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">?</div>
                <h3>Ð? Hoa C?i Ti?n</h3>
                <p>Nâng c?p texture, d? phân gi?i HD cùng hi?u ?ng ki nang mu?t mà 60 FPS, mang l?i tr?i nghi?m th? giác tuy?t v?i.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">??</div>
                <h3>Giao D?ch T? Do</h3>
                <p>H? th?ng kinh t? cân b?ng, ch? ?o liên server giúp ngu?i choi trao d?i v?t ph?m hoàn toàn t? do và minh b?ch.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">??</div>
                <h3>C?ng Ð?ng Sôi Ð?ng</h3>
                <p>G?p g?, k?t giao hàng v?n d?ng d?o trên các h?i nhóm, th? s?c san Boss da d?ng v?i tính chi?n thu?t cao.</p>
            </div>
        </div>
    </section>

    <!-- Showcase Section -->
    <section class="landing-section" style="padding-top: 20px;">
        <div class="showcase-section">
            <img src="/images/dragon_ball_showcase.png" alt="Epic Environment" class="showcase-bg">
            <div class="showcase-content">
                <h2>B?t Ð?u Chuy?n Hành Trình</h2>
                <p>Hàng v?n cu dân dang ch? dón b?n.</p>
                <br>
                <a href="/register" class="btn-pill btn-white" style="color: #000; background: #fff; margin-top: 20px; text-decoration: none;">Ðang kí mi?n phí</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="text-align: center; padding: 40px; color: var(--text-secondary); font-size: 14px;">
        B?n quy?n thu?c v? Chú Bé R?ng Online - Thi?t k? b?i Antigravity UI
    </footer>

    <!-- JS Particles Effect -->
    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
</body>
</html>

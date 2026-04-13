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
    <title>Chú Bé Rồng Online | Game Ngọc Rồng Mobile MMORPG Huyền Thoại</title>
    <meta name="description" content="Trang chủ chính thức Chú Bé Rồng Online – Game nhập vai Dragon Ball huyền thoại. Tải game miễn phí cho PC, Android, iOS. Đồ họa HD, giao dịch tự do, cộng đồng sôi động." />
    <meta name="keywords" content="Chú Bé Rồng Online, ngọc rồng online, game ngoc rong, Dragon Ball, game nhập vai, MMORPG, NRO, tải game ngọc rồng" />
    <meta name="robots" content="INDEX,FOLLOW" />
    <meta property="og:title" content="Chú Bé Rồng Online | Game Ngọc Rồng Huyền Thoại" />
    <meta property="og:description" content="Game nhập vai Dragon Ball huyền thoại — đồ họa HD, giao dịch tự do, 3 hành tinh, hàng trăm chiêu thức." />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="/images/dragon_ball_showcase.png" />
    <link rel="canonical" href="/" />
    
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    
    <!-- Link CSS Mới Chuẩn Apple -->
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
</head>
<body>

    <!-- Navigation Header -->
    <?php include __DIR__ . '/nav.php'; ?>

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
                <a href="/download/VPS(2).jar" class="platform-card-img">
                    <div class="card-img-wrap"><img src="/images/card_java.jpg" alt="Phiên bản Java" loading="lazy"></div>
                    <div class="card-info">
                        <span class="card-name">Phiên bản Java</span>
                        <span class="card-action">Tải xuống ↓</span>
                    </div>
                </a>
                <a href="/download/adr.apk" class="platform-card-img">
                    <div class="card-img-wrap"><img src="/images/card_android.jpg" alt="Android APK" loading="lazy"></div>
                    <div class="card-info">
                        <span class="card-name">Android APK</span>
                        <span class="card-action">Tải xuống ↓</span>
                    </div>
                </a>
                <a href="/download/pc.rar" class="platform-card-img">
                    <div class="card-img-wrap"><img src="/images/card_pc.jpg" alt="Bản PC" loading="lazy"></div>
                    <div class="card-info">
                        <span class="card-name">Bản PC</span>
                        <span class="card-action">Tải xuống ↓</span>
                    </div>
                </a>
                <a href="/huong-dan-iphone" class="platform-card-img">
                    <div class="card-img-wrap"><img src="/images/card_ios.jpg" alt="iPhone / iOS" loading="lazy"></div>
                    <div class="card-info">
                        <span class="card-name">iPhone / iOS</span>
                        <span class="card-action">Hướng dẫn →</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="landing-section">
        <h2 class="section-title">Khám Phá Tính Năng<br>Làm Nên Thời Đại Mới</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon"><img src="/images/icons/sparkle.png" alt="Đồ Hoa" style="width:48px;height:48px;"></div>
                <h3>Đồ Hoa Cải Tiến</h3>
                <p>Nâng cấp texture, độ phân giải HD cùng hiệu ứng kĩ năng mượt mà 60 FPS, mang lại trải nghiệm thị giác tuyệt vời.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><img src="/images/icons/trade.png" alt="Giao Dịch" style="width:48px;height:48px;"></div>
                <h3>Giao Dịch Tự Do</h3>
                <p>Hệ thống kinh tế cân bằng, chợ ảo liên server giúp người chơi trao đổi vật phẩm hoàn toàn tự do và minh bạch.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><img src="/images/icons/community.png" alt="Cộng Đồng" style="width:48px;height:48px;"></div>
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

    <!-- GiftCode Section -->
    <section class="giftcode-section" id="giftcodeSection">
        <div class="giftcode-header" onclick="toggleGiftCodes()">
            <h2 class="giftcode-title">
                <span class="giftcode-icon">🎁</span>
                GiftCode Đang Hoạt Động
                <span class="giftcode-count" id="gcCount"></span>
            </h2>
            <button class="giftcode-toggle" id="gcToggleBtn" aria-label="Toggle Gift Code">
                <svg class="toggle-chevron" id="gcChevron" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
        </div>
        <div class="giftcode-body" id="gcBody">
            <div class="giftcode-grid" id="gcGrid">
                <div class="gc-loading"><div class="gc-spinner"></div>Đang tải...</div>
            </div>
        </div>
    </section>


    <!-- Event Banner Carousel -->
    <section class="event-section">
        <h2 class="section-title">Sự Kiện Đang Diễn Ra<br>Phần Thưởng Hấp Dẫn</h2>
        <div class="event-carousel" id="eventCarousel">
            <button class="event-nav-btn prev" onclick="slideEvent(-1)">‹</button>
            <div class="event-track" id="eventTrack">
                <div class="event-slide"><img src="/images/forum/sukienhe.jpg" alt="Sự Kiện Ẩm Thực Mùa Hè" loading="lazy"></div>
                <div class="event-slide"><img src="/images/forum/sukienthang5.jpg" alt="Làm Nhiệm Vụ Nhận Quà" loading="lazy"></div>
                <div class="event-slide"><img src="/images/forum/sukienvuahung.jpg" alt="Giỗ Tổ Hùng Vương" loading="lazy"></div>
                <div class="event-slide"><img src="/images/forum/sukiengoku.jpg" alt="Sự Kiện Goku" loading="lazy"></div>
            </div>
            <button class="event-nav-btn next" onclick="slideEvent(1)">›</button>
        </div>
        <div class="event-dots" id="eventDots">
            <button class="event-dot active" onclick="goEvent(0)"></button>
            <button class="event-dot" onclick="goEvent(1)"></button>
            <button class="event-dot" onclick="goEvent(2)"></button>
            <button class="event-dot" onclick="goEvent(3)"></button>
        </div>
    </section>

    <!-- Screenshots Gallery -->
    <section class="screenshots-section">
        <h2 class="section-title">Hình Ảnh Trong Game<br>Trải Nghiệm Thực Tế</h2>
        <div class="screenshots-grid">
            <div class="screenshot-card"><img src="/images/screen/2-3.png" alt="Thế giới game" loading="lazy"><span class="ss-caption">Rừng Xayda</span></div>
            <div class="screenshot-card"><img src="/images/screen/7-1.png" alt="Đấu trường" loading="lazy"><span class="ss-caption">Đấu Trường</span></div>
            <div class="screenshot-card"><img src="/images/screen/14-2.png" alt="Boss Broly" loading="lazy"><span class="ss-caption">Boss Broly</span></div>
            <div class="screenshot-card"><img src="/images/screen/12-3.png" alt="Phó bản" loading="lazy"><span class="ss-caption">Phó Bản</span></div>
            <div class="screenshot-card"><img src="/images/screen/2-5.png" alt="Map game" loading="lazy"><span class="ss-caption">Bản Đồ Thế Giới</span></div>
            <div class="screenshot-card"><img src="/images/screen/9-3.png" alt="Võ đài" loading="lazy"><span class="ss-caption">Đại Hội Võ Thuật</span></div>
            <div class="screenshot-card"><img src="/images/screen/14-5.png" alt="Boss Cell" loading="lazy"><span class="ss-caption">Boss Đặc Biệt</span></div>
            <div class="screenshot-card"><img src="/images/screen/8-3.png" alt="Đệ tử" loading="lazy"><span class="ss-caption">Đệ Tử</span></div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/footer.php'; ?>


    <!-- Event Carousel JS -->
    <script>
    let currentEvent = 0;
    const totalEvents = 4;
    let eventInterval;

    function goEvent(idx) {
        currentEvent = idx;
        document.getElementById('eventTrack').style.transform = `translateX(-${idx * 100}%)`;
        document.querySelectorAll('.event-dot').forEach((d, i) => d.classList.toggle('active', i === idx));
    }

    function slideEvent(dir) {
        currentEvent = (currentEvent + dir + totalEvents) % totalEvents;
        goEvent(currentEvent);
        resetAutoSlide();
    }

    function resetAutoSlide() {
        clearInterval(eventInterval);
        eventInterval = setInterval(() => slideEvent(1), 4000);
    }

    // Auto-play 
    eventInterval = setInterval(() => slideEvent(1), 4000);

    // Pause on hover
    const carousel = document.getElementById('eventCarousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', () => clearInterval(eventInterval));
        carousel.addEventListener('mouseleave', resetAutoSlide);
    }

    // ========= GIFTCODE SECTION =========
    let gcVisible = true;

    function toggleGiftCodes() {
        gcVisible = !gcVisible;
        const body = document.getElementById('gcBody');
        const chevron = document.getElementById('gcChevron');
        if (gcVisible) {
            body.style.maxHeight = body.scrollHeight + 'px';
            body.style.opacity = '1';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            body.style.maxHeight = '0';
            body.style.opacity = '0';
            chevron.style.transform = 'rotate(-90deg)';
        }
    }

    function copyCode(code, btn) {
        navigator.clipboard.writeText(code).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Đã copy!';
            btn.classList.add('copied');
            setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('copied'); }, 1800);
        });
    }

    function loadGiftCodes() {
        fetch('/api/giftcodes.php')
            .then(r => r.json())
            .then(data => {
                const grid = document.getElementById('gcGrid');
                const countBadge = document.getElementById('gcCount');
                const codes = data.data || [];

                if (codes.length === 0) {
                    grid.innerHTML = '<div class="gc-empty">Hiện chưa có GiftCode nào hoạt động</div>';
                    countBadge.textContent = '';
                    return;
                }

                countBadge.textContent = codes.length;
                let html = '';
                codes.forEach(gc => {
                    const expDate = new Date(gc.expired);
                    const now = new Date();
                    const daysLeft = Math.ceil((expDate - now) / (1000 * 60 * 60 * 24));
                    const urgentClass = daysLeft <= 3 ? 'gc-urgent' : '';
                    const typeLabel = gc.type === 1 ? 'VIP' : '';

                    html += `
                    <div class="gc-card ${urgentClass}">
                        <div class="gc-card-top">
                            <div class="gc-code-wrap">
                                <code class="gc-code">${gc.code}</code>
                                ${typeLabel ? '<span class="gc-type-badge">' + typeLabel + '</span>' : ''}
                            </div>
                            <button class="gc-copy-btn" onclick="event.stopPropagation(); copyCode('${gc.code}', this)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                Copy
                            </button>
                        </div>
                        <div class="gc-card-bottom">
                            <span class="gc-uses">🎫 Còn ${gc.count_left} lượt</span>
                            <span class="gc-expires">${daysLeft > 0 ? '⏳ ' + daysLeft + ' ngày' : '⚠️ Hết hạn'}</span>
                        </div>
                    </div>`;
                });
                grid.innerHTML = html;

                // Set initial max-height
                const body = document.getElementById('gcBody');
                body.style.maxHeight = body.scrollHeight + 'px';
            })
            .catch(err => {
                document.getElementById('gcGrid').innerHTML = '<div class="gc-empty">Không thể tải GiftCode</div>';
            });
    }

    // Load on page ready
    loadGiftCodes();
    </script>
</body>
</html>

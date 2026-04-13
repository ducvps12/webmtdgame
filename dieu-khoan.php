<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điều Khoản Sử Dụng | Chú Bé Rồng Online</title>
    <meta name="description" content="Điều khoản sử dụng dịch vụ Chú Bé Rồng Online — dự án phi thương mại với mục đích học tập lập trình." />
    <meta name="robots" content="INDEX,FOLLOW" />
    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
    <style>
        .legal-page { padding-top: 100px; padding-bottom: 40px; max-width: 800px; margin: 0 auto; padding-left: 20px; padding-right: 20px; }
        .legal-page h1 { font-size: 36px; font-weight: 700; letter-spacing: -0.03em; margin-bottom: 8px; }
        .legal-page .update-date { color: var(--text-secondary); font-size: 14px; margin-bottom: 40px; }
        .legal-page h2 { font-size: 20px; font-weight: 600; margin: 36px 0 12px 0; letter-spacing: -0.01em; }
        .legal-page p, .legal-page li { font-size: 15px; line-height: 1.8; color: #424245; }
        .legal-page ul { padding-left: 20px; }
        .legal-page ul li { margin-bottom: 6px; }
        .legal-highlight {
            background: #f5f5f7; border-radius: 14px; padding: 20px 24px;
            border-left: 4px solid #e65c00; margin: 20px 0;
        }
        .legal-highlight p { margin: 0; font-size: 14px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="legal-page">
        <h1>Điều Khoản Sử Dụng</h1>
        <p class="update-date">Cập nhật lần cuối: <?php echo date('d/m/Y'); ?></p>

        <div class="legal-highlight">
            <p><strong>Tuyên bố quan trọng:</strong> Chú Bé Rồng Online là dự án <strong>phi thương mại</strong>, được phát triển với mục đích <strong>học tập và nghiên cứu công nghệ lập trình game</strong>. Dự án không liên kết, được tài trợ hay ủy quyền bởi Akira Toriyama, Shueisha, Toei Animation, Bandai Namco, hay bất kỳ tổ chức sở hữu thương hiệu Dragon Ball nào.</p>
        </div>

        <h2>1. Chấp nhận điều khoản</h2>
        <p>Bằng việc truy cập, đăng ký tài khoản hoặc sử dụng các dịch vụ do Chú Bé Rồng Online cung cấp (bao gồm website, game client, diễn đàn), bạn đồng ý tuân thủ toàn bộ các Điều Khoản Sử Dụng này. Nếu bạn không đồng ý với bất kỳ điều khoản nào, vui lòng ngừng sử dụng dịch vụ.</p>

        <h2>2. Mục đích của dự án</h2>
        <p>Dự án được tạo ra nhằm:</p>
        <ul>
            <li>Phục vụ mục đích <strong>nghiên cứu và học tập</strong> trong lĩnh vực lập trình game, thiết kế web, quản trị cơ sở dữ liệu và vận hành hệ thống.</li>
            <li>Tạo môi trường thực hành cho các nhà phát triển trẻ trong cộng đồng lập trình.</li>
            <li><strong>Không nhằm mục đích thương mại</strong>, không kinh doanh, không tìm kiếm lợi nhuận từ thương hiệu Dragon Ball hoặc bất kỳ tài sản trí tuệ nào của bên thứ ba.</li>
        </ul>

        <h2>3. Quyền sở hữu trí tuệ</h2>
        <p>Tất cả nhân vật, hình ảnh, âm thanh, cốt truyện liên quan đến Dragon Ball là tài sản trí tuệ thuộc về <strong>Akira Toriyama / Bird Studio / Shueisha / Toei Animation / Bandai Namco Entertainment</strong>. Dự án này sử dụng các yếu tố đó với mục đích <strong>phi thương mại, fan-made</strong> và hoàn toàn tôn trọng quyền sở hữu gốc.</p>
        <p>Nếu bất kỳ bên sở hữu nào yêu cầu gỡ bỏ, dự án sẽ tuân thủ ngay lập tức.</p>

        <h2>4. Tài khoản người dùng</h2>
        <ul>
            <li>Mỗi người chỉ được tạo một số lượng tài khoản hợp lý.</li>
            <li>Bạn chịu trách nhiệm bảo mật thông tin đăng nhập của mình.</li>
            <li>Nghiêm cấm mua bán, trao đổi tài khoản.</li>
            <li>Quản trị viên có quyền khóa hoặc xóa tài khoản vi phạm mà không cần thông báo trước.</li>
        </ul>

        <h2>5. Hành vi bị cấm</h2>
        <p>Người dùng không được thực hiện các hành vi sau:</p>
        <ul>
            <li>Sử dụng phần mềm gian lận (hack, cheat, bot, auto).</li>
            <li>Khai thác lỗi hệ thống (bug exploit) để trục lợi.</li>
            <li>Quấy rối, xúc phạm, đe dọa người chơi khác.</li>
            <li>Phát tán nội dung vi phạm pháp luật, đồi trụy, hoặc gây hại cho cộng đồng.</li>
            <li>Tấn công hệ thống (DDoS, SQL injection, brute-force, v.v.).</li>
            <li>Giao dịch vật phẩm, tài khoản game bằng tiền thật ngoài hệ thống chính thức.</li>
        </ul>

        <h2>6. Nạp tiền và giao dịch nội bộ</h2>
        <ul>
            <li>Tiền nạp vào hệ thống là <strong>khoản đóng góp tự nguyện</strong> để hỗ trợ chi phí vận hành máy chủ, tên miền và phát triển dự án.</li>
            <li>Khoản đóng góp <strong>không được hoàn trả</strong> dưới bất kỳ hình thức nào.</li>
            <li>VND trong game là đơn vị ảo, không có giá trị quy đổi ra tiền thật.</li>
            <li>Quản trị viên có quyền điều chỉnh số dư và vật phẩm để đảm bảo cân bằng hệ thống.</li>
        </ul>

        <h2>7. Miễn trừ trách nhiệm</h2>
        <ul>
            <li>Dịch vụ được cung cấp theo nguyên tắc "nguyên trạng" (as-is) không kèm bất kỳ bảo đảm nào.</li>
            <li>Chúng tôi không chịu trách nhiệm về mất mát dữ liệu, gián đoạn dịch vụ, hoặc thiệt hại phát sinh từ việc sử dụng dịch vụ.</li>
            <li>Dự án có thể ngừng hoạt động bất cứ lúc nào mà không cần thông báo trước.</li>
        </ul>

        <h2>8. Thay đổi điều khoản</h2>
        <p>Chúng tôi có quyền cập nhật Điều Khoản Sử Dụng bất cứ lúc nào. Các thay đổi sẽ có hiệu lực ngay khi được đăng trên trang này. Việc tiếp tục sử dụng dịch vụ đồng nghĩa với việc bạn chấp nhận phiên bản mới nhất của Điều Khoản.</p>

        <h2>9. Liên hệ</h2>
        <p>Mọi câu hỏi, khiếu nại hoặc yêu cầu liên quan đến Điều Khoản Sử Dụng, vui lòng liên hệ qua nhóm cộng đồng Zalo hoặc diễn đàn chính thức của dự án.</p>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
</body>
</html>

<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách Bảo Mật | Chú Bé Rồng Online</title>
    <meta name="description" content="Chính sách bảo mật và thu thập dữ liệu của Chú Bé Rồng Online — dự án phi thương mại với mục đích học tập." />
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
            border-left: 4px solid #0071e3; margin: 20px 0;
        }
        .legal-highlight p { margin: 0; font-size: 14px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="legal-page">
        <h1>Chính Sách Bảo Mật</h1>
        <p class="update-date">Cập nhật lần cuối: <?php echo date('d/m/Y'); ?></p>

        <div class="legal-highlight">
            <p><strong>Tuyên bố:</strong> Chú Bé Rồng Online là dự án phi thương mại, được phát triển và vận hành hoàn toàn với mục đích <strong>học tập, nghiên cứu lập trình game và công nghệ web</strong>. Dự án không nhằm mục đích kinh doanh hay cạnh tranh với bất kỳ sản phẩm thương mại nào.</p>
        </div>

        <h2>1. Thông tin chúng tôi thu thập</h2>
        <p>Khi bạn sử dụng dịch vụ, chúng tôi có thể thu thập các thông tin sau:</p>
        <ul>
            <li><strong>Thông tin tài khoản:</strong> Tên đăng nhập, mật khẩu (được mã hóa), địa chỉ email (nếu cung cấp).</li>
            <li><strong>Thông tin kỹ thuật:</strong> Địa chỉ IP, thời gian đăng nhập, thiết bị sử dụng.</li>
            <li><strong>Dữ liệu trong game:</strong> Tiến trình nhân vật, vật phẩm, lịch sử giao dịch nội bộ.</li>
        </ul>

        <h2>2. Mục đích sử dụng dữ liệu</h2>
        <p>Dữ liệu thu thập được sử dụng cho các mục đích:</p>
        <ul>
            <li>Vận hành và duy trì dịch vụ game.</li>
            <li>Xác thực và bảo vệ tài khoản người dùng.</li>
            <li>Phát hiện, ngăn chặn gian lận và vi phạm quy định.</li>
            <li>Nghiên cứu và cải thiện trải nghiệm người chơi.</li>
            <li>Liên lạc với người dùng khi cần thiết (hỗ trợ kỹ thuật, thông báo hệ thống).</li>
        </ul>

        <h2>3. Bảo vệ dữ liệu</h2>
        <p>Chúng tôi cam kết bảo vệ thông tin cá nhân của bạn bằng các biện pháp kỹ thuật hợp lý, bao gồm:</p>
        <ul>
            <li>Mã hóa mật khẩu trước khi lưu trữ trong cơ sở dữ liệu.</li>
            <li>Giới hạn quyền truy cập dữ liệu chỉ cho quản trị viên được ủy quyền.</li>
            <li>Không chia sẻ, bán hoặc cho thuê thông tin cá nhân cho bên thứ ba.</li>
        </ul>

        <h2>4. Cookie và theo dõi</h2>
        <p>Website sử dụng cookie phiên (session cookie) để duy trì trạng thái đăng nhập. Chúng tôi không sử dụng cookie theo dõi của bên thứ ba hay công cụ phân tích hành vi người dùng.</p>

        <h2>5. Quyền của người dùng</h2>
        <p>Bạn có quyền:</p>
        <ul>
            <li>Yêu cầu xem, chỉnh sửa hoặc xóa dữ liệu cá nhân của mình.</li>
            <li>Rút lại sự đồng ý sử dụng dữ liệu bất cứ lúc nào.</li>
            <li>Xóa tài khoản và toàn bộ dữ liệu liên quan.</li>
        </ul>
        <p>Để thực hiện các quyền trên, vui lòng liên hệ quản trị viên qua kênh hỗ trợ chính thức.</p>

        <h2>6. Dữ liệu trẻ em</h2>
        <p>Dịch vụ không dành cho trẻ em dưới 13 tuổi. Chúng tôi không cố ý thu thập thông tin cá nhân từ trẻ em. Nếu phát hiện tài khoản thuộc về trẻ em dưới 13 tuổi, chúng tôi sẽ xóa dữ liệu liên quan.</p>

        <h2>7. Thay đổi chính sách</h2>
        <p>Chúng tôi có quyền cập nhật Chính Sách Bảo Mật này bất cứ lúc nào. Mọi thay đổi sẽ được công bố trên trang này với ngày cập nhật mới. Việc tiếp tục sử dụng dịch vụ sau khi chính sách được cập nhật đồng nghĩa với việc bạn chấp nhận các thay đổi đó.</p>

        <h2>8. Liên hệ</h2>
        <p>Nếu bạn có câu hỏi hoặc thắc mắc về Chính Sách Bảo Mật, vui lòng liên hệ qua nhóm Zalo cộng đồng hoặc diễn đàn chính thức.</p>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
</body>
</html>

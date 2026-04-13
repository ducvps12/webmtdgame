<!-- footer.php — Shared Footer Component -->
<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-brand">
                    <img src="/images/logo_sk_he.png" alt="Logo" class="footer-logo">
                    <p>Dự án game nhập vai dựa trên Dragon Ball — phát triển với mục đích học tập và nghiên cứu lập trình.</p>
                </div>
            </div>
            <div class="footer-col">
                <h4>Trang Web</h4>
                <ul>
                    <li><a href="/">Trang Chủ</a></li>
                    <li><a href="/gioi-thieu">Giới Thiệu</a></li>
                    <li><a href="/ban-do">Bản Đồ Game</a></li>
                    <li><a href="/forum">Diễn Đàn</a></li>
                    <li><a href="/nap-atm">Nạp ATM Bank</a></li>
                    <li><a href="/gop-y">Góp Ý</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Xếp Hạng</h4>
                <ul>
                    <li><a href="/bang-xep-hang">BXH Sức Mạnh</a></li>
                    <li><a href="/top-nap">Đua Top Nạp</a></li>
                    <li><a href="/ho-so">Hồ Sơ Cá Nhân</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Pháp Lý</h4>
                <ul>
                    <li><a href="/chinh-sach">Chính Sách Bảo Mật</a></li>
                    <li><a href="/dieu-khoan">Điều Khoản Sử Dụng</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Chú Bé Rồng Online — Dự án phi thương mại, mục đích học tập.</p>
            <p class="footer-disclaimer">Dragon Ball là thương hiệu của Akira Toriyama / Shueisha / Toei Animation. Dự án này không liên kết với bất kỳ tổ chức thương mại nào.</p>
        </div>
    </div>
</footer>

<!-- Zalo Floating Box -->
<div class="zalo-floating-box">
    <a href="https://zalo.me/g/irufas657" target="_blank" class="zalo-btn">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Icon_of_Zalo.svg/1024px-Icon_of_Zalo.svg.png" alt="Zalo Box 1">
        <span>Box Zalo 1</span>
    </a>
    <a href="https://zalo.me/g/aejkmp483" target="_blank" class="zalo-btn">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Icon_of_Zalo.svg/1024px-Icon_of_Zalo.svg.png" alt="Zalo Box 2">
        <span>Box Zalo 2</span>
    </a>
</div>

<style>
.zalo-floating-box {
    position: fixed;
    bottom: 20px;
    left: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    z-index: 9999;
}
.zalo-btn {
    display: flex;
    align-items: center;
    background: #0068ff;
    color: white;
    padding: 8px 16px 8px 8px;
    border-radius: 50px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0, 104, 255, 0.3);
    transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
}
.zalo-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0, 104, 255, 0.4);
    background: #005ce6;
    color: white;
}
.zalo-btn img {
    width: 32px;
    height: 32px;
    margin-right: 10px;
    border-radius: 50%;
    background: white;
    padding: 2px;
}
.site-footer {
    background: #1d1d1f;
    color: rgba(255,255,255,0.7);
    padding: 60px 40px 30px;
    margin-top: 80px;
}
.footer-container { max-width: 1200px; margin: 0 auto; }
.footer-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}
.footer-logo { height: 36px; margin-bottom: 12px; }
.footer-brand p { font-size: 13px; line-height: 1.6; color: rgba(255,255,255,0.5); }
.footer-col h4 { color: #fff; font-size: 14px; font-weight: 600; margin: 0 0 16px 0; letter-spacing: 0.02em; }
.footer-col ul { list-style: none; padding: 0; margin: 0; }
.footer-col ul li { margin-bottom: 10px; }
.footer-col ul li a {
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-size: 13px;
    transition: color 0.2s;
}
.footer-col ul li a:hover { color: #fff; }
.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 24px;
    text-align: center;
}
.footer-bottom p { font-size: 13px; margin: 0 0 6px 0; color: rgba(255,255,255,0.45); }
.footer-disclaimer { font-size: 11px !important; color: rgba(255,255,255,0.3) !important; font-style: italic; }

@media (max-width: 768px) {
    .site-footer { padding: 40px 20px 24px; }
    .footer-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
}
@media (max-width: 480px) {
    .footer-grid { grid-template-columns: 1fr; }
}
</style>

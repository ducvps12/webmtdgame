<?php
// nav.php — Shared Navigation with User Dropdown
// This file should be included in all Apple UI pages.
// Requires: session started, optionally $conn available.

// Load user data from session if available
$_nav_logged_in = isset($_SESSION['username']);
$_nav_username = $_SESSION['username'] ?? '';
$_nav_player_name = $_SESSION['player_name'] ?? $_nav_username;
$_nav_is_admin = $_SESSION['is_admin'] ?? 0;
$_nav_avatar = $_SESSION['user_avatar'] ?? '/images/avatar/0.png';
$_nav_coin = $_SESSION['coin'] ?? 0;

// Determine active page from URL
$_nav_current = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if ($_nav_current === '' || $_nav_current === '/') $_nav_current = 'index';
?>

<nav class="apple-nav" id="mainNav">
    <div class="nav-left">
        <a href="/"><img src="/images/logo_sk_he.png" alt="Logo" class="nav-logo"></a>
        <ul class="nav-links">
            <li><a href="/" class="<?php echo $_nav_current === 'index' ? 'active' : ''; ?>">Trang Chủ</a></li>
            <li><a href="/gioi-thieu" class="<?php echo $_nav_current === 'gioi-thieu' ? 'active' : ''; ?>">Giới Thiệu</a></li>
            <li><a href="/ban-do" class="<?php echo $_nav_current === 'ban-do' ? 'active' : ''; ?>">Bản Đồ</a></li>
            <li><a href="/su-kien" class="<?php echo $_nav_current === 'su-kien' ? 'active' : ''; ?>" style="color: #e53e3e; font-weight: 600;">🔥 Sự Kiện</a></li>
            <li><a href="/forum" class="<?php echo $_nav_current === 'forum' ? 'active' : ''; ?>">Diễn Đàn</a></li>
            <li><a href="/nap-atm" class="<?php echo $_nav_current === 'nap-atm' ? 'active' : ''; ?>">Nạp ATM</a></li>
            <li><a href="/gop-y" class="<?php echo $_nav_current === 'gop-y' ? 'active' : ''; ?>">Góp Ý</a></li>
        </ul>
    </div>
    <div class="nav-right">
        <?php if ($_nav_logged_in): ?>
            <!-- User Dropdown -->
            <div class="user-dropdown-wrap" id="userDropdown">
                <div class="user-trigger" onclick="toggleUserDropdown()">
                    <img src="<?php echo htmlspecialchars($_nav_avatar); ?>" alt="Avatar" class="user-trigger-avatar" onerror="this.onerror=null;this.src='/images/avatar/0.png';">
                    <span class="user-trigger-name"><?php echo htmlspecialchars($_nav_player_name ?: $_nav_username); ?></span>
                    <span class="user-trigger-arrow">▼</span>
                </div>
                <div class="user-dropdown-menu">
                    <!-- Header -->
                    <div class="dropdown-header">
                        <img src="<?php echo htmlspecialchars($_nav_avatar); ?>" alt="Avatar" class="dropdown-header-avatar" onerror="this.onerror=null;this.src='/images/avatar/0.png';">
                        <div class="dropdown-header-info">
                            <h4><?php echo htmlspecialchars($_nav_player_name ?: $_nav_username); ?></h4>
                            <p>@<?php echo htmlspecialchars($_nav_username); ?></p>
                        </div>
                    </div>

                    <!-- Balance -->
                    <div class="dropdown-balance">
                        <span>Số dư</span>
                        <strong><?php echo number_format($_nav_coin); ?> đ</strong>
                    </div>

                    <!-- Links -->
                    <a href="/ho-so" class="dropdown-item">
                        <span class="dd-icon"><img src="/images/icons/saiyan.png" alt="" class="dd-icon-img"></span> Hồ sơ cá nhân
                    </a>
                    <a href="/bang-xep-hang" class="dropdown-item">
                        <span class="dd-icon"><img src="/images/icons/trophy.png" alt="" class="dd-icon-img"></span> Bảng xếp hạng
                    </a>
                    <a href="/nap-atm" class="dropdown-item">
                        <span class="dd-icon"><img src="/images/icons/crystal.png" alt="" class="dd-icon-img"></span> Nạp VND
                    </a>
                    <a href="/top-nap" class="dropdown-item">
                        <span class="dd-icon"><img src="/images/icons/energy.png" alt="" class="dd-icon-img"></span> Đua Top Nạp
                    </a>

                    <div class="dropdown-divider"></div>

                    <?php if ($_nav_is_admin == 1): ?>
                    <a href="/admin" class="dropdown-item admin-link">
                        <span class="dd-icon"><img src="/images/icons/dragon.png" alt="" class="dd-icon-img"></span> Quản trị Admin
                    </a>
                    <div class="dropdown-divider"></div>
                    <?php endif; ?>

                    <a href="/app/logout" class="dropdown-item danger">
                        <span class="dd-icon"><img src="/images/icons/lightning.png" alt="" class="dd-icon-img"></span> Đăng xuất
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="/login" class="btn-pill btn-outline small">Đăng Nhập</a>
            <a href="/register" class="btn-pill btn-black small">Đăng Ký</a>
        <?php endif; ?>
    </div>
</nav>

<script>
function toggleUserDropdown() {
    var wrap = document.getElementById('userDropdown');
    wrap.classList.toggle('open');
}
// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('userDropdown');
    if (wrap && !wrap.contains(e.target)) {
        wrap.classList.remove('open');
    }
});
</script>

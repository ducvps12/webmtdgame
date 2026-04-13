<?php
require_once 'settings.php';
require_once 'forum_data.php';
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Chú Bé Rồng Onlines - Ngọc Rồng Online</title>
    <meta name="keywords" content="Chú Bé Rồng Online,ngoc rong mobile, game ngoc rong, game 7 vien ngoc rong, game bay vien ngoc rong" />
    <meta name="description" content="Website chính thức của Chú Bé Rồng Online – Game Bay Vien Ngọc Rồng Mobile nhập vai trực tuyến trên máy tính và điện thoại về Game 7 Viên Ngọc Rồng hấp dẫn nhất hiện nay!" />
    <meta http-equiv="refresh" content="600" />
    <meta name="robots" content="INDEX,FOLLOW" />

    <link rel="apple-touch-icon" href="/images/favicon-48x48.ico" />
    <link rel="icon" href='/images/favicon-48x48.ico' type="image/x-icon" />
    <link rel="shortcut icon" href='/images/favicon-48x48.ico' type="image/x-icon" />
    <link rel="icon" href="/images/favicon-48x48.ico">
    <link rel="icon" type="image/png" href="/images/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/images/favicon-64x64.png" sizes="64x64">
    <link rel="icon" type="image/png" href="/images/favicon-128x128.png" sizes="128x128">
    <link rel="icon" type="image/png" href="/images/favicon-48x48.png" sizes="48x48">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/view/static/css/template.css?v=1.10">
    <link rel="stylesheet" href="/view/static/css/eff.css?v=1.00">
    <link rel="stylesheet" href="/view/static/css/w3.css?v=1.01">
    <link rel="stylesheet" href="/view/static/css/styleSheet.css?v=1.1">
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
    <link rel="stylesheet" href="/view/static/css/forum.css?v=1.2">
    <script src="https://www.google.com/recaptcha/api.js?render="></script>
	<script src="/view/static/js/disable_devtools.js"></script>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>

    <!-- Navigation Header -->
    <?php include __DIR__ . '/nav.php'; ?>
    <div style="padding-top: 100px; max-width: 900px; margin: 0 auto; min-height: 100vh;">
        <div class="body" style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 20px;">
            <div id="box_forums" class="beta_test">
                <div class="box_list_chuyenmuc">
                                                        <?php if (!empty($pinned_posts)) : ?>
                                                            <div id="stick">
                                                                <?php foreach ($pinned_posts as $post) : ?>
                                                                    <div class="forum-post-item">
                                                                        <div class="post-avatar-wrapper">
                                                                            <img src="<?php echo htmlspecialchars($post['avatar_url']); ?>" alt="Post Avatar" onerror="this.onerror=null;this.src='/images/avatar/0.png';">
                                                                        </div>
                                                                        <div class="post-content">
                                                                            <a class="post-title-link" href="bai-viet.php?id=<?php echo htmlspecialchars($post['id']); ?>" title="<?php echo htmlspecialchars($post['tieude']); ?>">
                                                                                <?php echo htmlspecialchars($post['tieude']); ?> <img src="/images/gif/hot.gif" class="hot-icon" alt="Hot">
                                                                            </a>
                                                                            <div class="post-meta-info">
                                                                                bởi <a href="javascript:void(0)"><?php echo htmlspecialchars($post['username']); ?></a>
                                                                                <span style="color:red">☆</span>
                                                                                <i>
                                                                                    <?php echo date_format(date_create($post['created_at']), 'd/m/Y H:i'); ?>
                                                                                </i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                            <br>
                                                        <?php endif; ?>

                                                        <?php if (!empty($unpinned_posts)) : ?>
                                                            <?php foreach ($unpinned_posts as $post) : ?>
                                                                <div class="forum-post-item">
                                                                    <div class="post-avatar-wrapper">
                                                                        <img src="<?php echo htmlspecialchars($post['avatar_url']); ?>" alt="User Avatar" onerror="this.onerror=null;this.src='/images/avatar/0.png';">
                                                                    </div>
                                                                    <div class="post-content">
                                                                        <a class="post-title-link" href="bai-viet.php?id=<?php echo htmlspecialchars($post['id']); ?>" title="<?php echo htmlspecialchars($post['tieude']); ?>">
                                                                            <?php echo htmlspecialchars($post['tieude']); ?>
                                                                        </a>
                                                                        <div class="post-meta-info">
                                                                            bởi <a href="javascript:void(0)"><?php echo htmlspecialchars($post['username']); ?></a>
                                                                            <span style="color:red">☆</span>
                                                                            <i>
                                                                                <?php echo date_format(date_create($post['created_at']), 'd/m/Y H:i'); ?>
                                                                            </i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>

                                                        <?php if ($total_pages > 1) : ?>
                                                        <div class="pagination-container">
                                                            <div class="pagination">
                                                                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                                                    <a href="?page=<?php echo $i; ?>" class="pagination-item <?php echo ($i == $current_page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                     <div class="post-button-section">
                                                        <hr>
                                                        <a href="dang-bai.php">
                                                            <span>Đăng Bài</span>
                                                        </a>
                                                        <hr>
                                                    </div>
                                                </div> <!-- end box_forums -->
                                            </div> <!-- end body -->
                                        </div> <!-- end padding container -->
                                            <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
                                            
                                            <script>
                                                $(document).ready(function() {
                                                    var lastPostTime = 0;
                                                    $("form[name='loginform'], form[name='registerform']").submit(function(event) {
                                                        event.preventDefault();
                                                        var now = Date.now();
                                                        if (now - lastPostTime < 10000) {
                                                            var secondsLeft = Math.ceil((10000 - (now - lastPostTime)) / 1000);
                                                            $("#comment_error").css("color", "red").text("Bạn chỉ có thể post mỗi 10 giây. Vui lòng chờ " + secondsLeft + " giây.");
                                                            return;
                                                        }

                                                        var form = $(this);
                                                        var formData = form.serialize();
                                                        var action = form.attr("id");
                                                        var csrfToken = $("#csrf_token").val();

                                                        formData += '&csrf_token=' + csrfToken + '&action=' + action;

                                                        $.post("/Api/Auth", formData)
                                                            .done(function(response) {
                                                                if (response.status === "success") {
                                                                    $("#comment_error").css("color", "green").text(response.message);
                                                                    lastPostTime = Date.now();
                                                                    if ($("#authModal").length) {
                                                                        $("#authModal").modal("hide");
                                                                    }
                                                                    setTimeout(function() {
                                                                        window.location.reload();
                                                                    }, 2000);
                                                                } else {
                                                                    $("#comment_error").css("color", "red").text(response.message);
                                                                }
                                                            })
                                                            .fail(function(jqXHR) {
                                                                var errorMessage = (jqXHR.responseJSON && jqXHR.responseJSON.message) || "Vui lòng thử lại trong ít phút nữa.";
                                                                $("#comment_error").css("color", "red").text(errorMessage);
                                                            });
                                                    });
                                                });
                                                function showTab(tab) {
                                                }
                                            </script>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
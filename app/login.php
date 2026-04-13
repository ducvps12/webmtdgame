<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    header('Location: /forum');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ðang nh?p - Chú Bé R?ng Online</title>
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
    <link rel="shortcut icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
    <style>
        .message { margin-top: 15px; padding: 12px; border-radius: 8px; font-weight: 500; font-size: 14px; display: none; }
        .success { background-color: rgba(52, 199, 89, 0.1); color: #248a3d; border: 1px solid rgba(52, 199, 89, 0.3); }
        .error { background-color: rgba(255, 59, 48, 0.1); color: #d70015; border: 1px solid rgba(255, 59, 48, 0.3); }
        /* Password toggle */
        .input-password-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-password-wrap .apple-input {
            padding-right: 44px;
            width: 100%;
        }
        .btn-eye {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #86868b;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-eye:hover { color: #1d1d1f; }
        .btn-eye svg { width: 20px; height: 20px; }
        /* CAPTCHA */
        .captcha-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 4px;
        }
        .captcha-question {
            background: linear-gradient(135deg, #1d1d1f, #3a3a3c);
            color: #fff;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
            user-select: none;
            min-width: 100px;
            text-align: center;
            flex-shrink: 0;
        }
        .captcha-input {
            flex: 1;
        }
        .captcha-refresh {
            background: none;
            border: none;
            cursor: pointer;
            color: #0071e3;
            font-size: 20px;
            padding: 4px;
            transition: transform 0.3s;
            flex-shrink: 0;
        }
        .captcha-refresh:hover { transform: rotate(180deg); }
    </style>
</head>
<body>
    <canvas id="antigravity-particles"></canvas>
    
    <nav class="apple-nav">
        <div class="nav-left">
            <a href="/"><img src="/images/logo_sk_he.png" alt="Logo" class="nav-logo"></a>
            <ul class="nav-links">
                <li><a href="/">Trang Ch?</a></li>
                <li><a href="/gioi-thieu">Gi?i Thi?u</a></li>
                <li><a href="/forum">Di?n Ðàn</a></li>
                <li><a href="https://zalo.me/g/atqsvzxmfalbhc3n4d7d" target="_blank">C?ng Ð?ng</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <a href="/register" class="btn-pill btn-black small">Ðang Ký</a>
        </div>
    </nav>

    <div class="auth-wrapper">
        <div class="glass-card">
            <h2>Ðang Nh?p</h2>
            <form id="loginForm" method="POST" name="login">
                <input type="hidden" name="action" value="login" />
                <input type="hidden" name="keySig" value="a511129a7ce15460414e6fe318eebc2b" />
                <input type="hidden" name="nav" value="" readonly="readonly" />
                <input type="hidden" name="checkru" value="d3540b1767470e0a87215174bd0ed85d" />
                <input type="hidden" name="captcha_hash" id="captcha_hash" value="" />
                
                <div class="form-group">
                    <label>Tài kho?n</label>
                    <input name="user" type="text" class="apple-input" placeholder="Nh?p tên dang nh?p" required />
                </div>
                <div class="form-group">
                    <label>M?t kh?u</label>
                    <div class="input-password-wrap">
                        <input name="pass" type="password" class="apple-input" placeholder="Nh?p m?t kh?u" required />
                        <button type="button" class="btn-eye" onclick="togglePassword(this)" title="Hi?n/?n m?t kh?u">
                            <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-closed" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mã xác th?c</label>
                    <div class="captcha-group">
                        <span class="captcha-question" id="captchaQuestion"></span>
                        <input name="captcha_answer" type="number" class="apple-input captcha-input" placeholder="= ?" required />
                        <button type="button" class="captcha-refresh" onclick="generateCaptcha()" title="Ð?i mã m?i">&#8635;</button>
                    </div>
                </div>
                
                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="radio" name="server" value="1" required checked id="sv1"/> 
                    <label for="sv1" style="margin: 0; cursor: pointer;">Tham gia Máy ch? 1 (Vu Tr? 1)</label>
                </div>

                <div id="loginMessage" class="message"></div>
                
                <button type="submit" class="btn-pill btn-black" style="width: 100%; margin-top: 10px;">Ðang Nh?p</button>
            </form>
            
            <div style="margin-top: 20px; font-size: 14px; color: var(--text-secondary);">
                Chua có tài kho?n? <a href="/register" style="color: #0071e3; text-decoration: none; font-weight: 500;">T?o ngay mi?n phí</a>
            </div>
        </div>
    </div>

    <script>
    // --- Password toggle ---
    function togglePassword(btn) {
        var wrap = btn.closest('.input-password-wrap');
        var input = wrap.querySelector('input');
        var open = btn.querySelector('.eye-open');
        var closed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            open.style.display = 'none';
            closed.style.display = 'block';
        } else {
            input.type = 'password';
            open.style.display = 'block';
            closed.style.display = 'none';
        }
    }

    // --- CAPTCHA ---
    var captchaAnswer = 0;
    function md5cycle(x, k) {
        var a = x[0], b = x[1], c = x[2], d = x[3];
        a = ff(a,b,c,d,k[0],7,-680876936);d = ff(d,a,b,c,k[1],12,-389564586);c = ff(c,d,a,b,k[2],17,606105819);b = ff(b,c,d,a,k[3],22,-1044525330);a = ff(a,b,c,d,k[4],7,-176418897);d = ff(d,a,b,c,k[5],12,1200080426);c = ff(c,d,a,b,k[6],17,-1473231341);b = ff(b,c,d,a,k[7],22,-45705983);a = ff(a,b,c,d,k[8],7,1770035416);d = ff(d,a,b,c,k[9],12,-1958414417);c = ff(c,d,a,b,k[10],17,-42063);b = ff(b,c,d,a,k[11],22,-1990404162);a = ff(a,b,c,d,k[12],7,1804603682);d = ff(d,a,b,c,k[13],12,-40341101);c = ff(c,d,a,b,k[14],17,-1502002290);b = ff(b,c,d,a,k[15],22,1236535329);
        a = gg(a,b,c,d,k[1],5,-165796510);d = gg(d,a,b,c,k[6],9,-1069501632);c = gg(c,d,a,b,k[11],14,643717713);b = gg(b,c,d,a,k[0],20,-373897302);a = gg(a,b,c,d,k[5],5,-701558691);d = gg(d,a,b,c,k[10],9,38016083);c = gg(c,d,a,b,k[15],14,-660478335);b = gg(b,c,d,a,k[4],20,-405537848);a = gg(a,b,c,d,k[9],5,568446438);d = gg(d,a,b,c,k[14],9,-1019803690);c = gg(c,d,a,b,k[3],14,-187363961);b = gg(b,c,d,a,k[8],20,1163531501);a = gg(a,b,c,d,k[13],5,-1444681467);d = gg(d,a,b,c,k[2],9,-51403784);c = gg(c,d,a,b,k[7],14,1735328473);b = gg(b,c,d,a,k[12],20,-1926607734);
        a = hh(a,b,c,d,k[5],4,-378558);d = hh(d,a,b,c,k[8],11,-2022574463);c = hh(c,d,a,b,k[11],16,1839030562);b = hh(b,c,d,a,k[14],23,-35309556);a = hh(a,b,c,d,k[1],4,-1530992060);d = hh(d,a,b,c,k[4],11,1272893353);c = hh(c,d,a,b,k[7],16,-155497632);b = hh(b,c,d,a,k[10],23,-1094730640);a = hh(a,b,c,d,k[13],4,681279174);d = hh(d,a,b,c,k[0],11,-358537222);c = hh(c,d,a,b,k[3],16,-722521979);b = hh(b,c,d,a,k[6],23,76029189);a = hh(a,b,c,d,k[9],4,-640364487);d = hh(d,a,b,c,k[12],11,-421815835);c = hh(c,d,a,b,k[15],16,530742520);b = hh(b,c,d,a,k[2],23,-995338651);
        a = ii(a,b,c,d,k[0],6,-198630844);d = ii(d,a,b,c,k[7],10,1126891415);c = ii(c,d,a,b,k[14],15,-1416354905);b = ii(b,c,d,a,k[5],21,-57434055);a = ii(a,b,c,d,k[12],6,1700485571);d = ii(d,a,b,c,k[3],10,-1894986606);c = ii(c,d,a,b,k[10],15,-1051523);b = ii(b,c,d,a,k[1],21,-2054922799);a = ii(a,b,c,d,k[8],6,1873313359);d = ii(d,a,b,c,k[15],10,-30611744);c = ii(c,d,a,b,k[6],15,-1560198380);b = ii(b,c,d,a,k[13],21,1309151649);a = ii(a,b,c,d,k[4],6,-145523070);d = ii(d,a,b,c,k[11],10,-1120210379);c = ii(c,d,a,b,k[2],15,718787259);b = ii(b,c,d,a,k[7],21,-343485551);
        x[0] = add32(a,x[0]);x[1] = add32(b,x[1]);x[2] = add32(c,x[2]);x[3] = add32(d,x[3]);
    }
    function cmn(q,a,b,x,s,t){a = add32(add32(a,q),add32(x,t));return add32((a<<s)|(a>>>(32-s)),b);}
    function ff(a,b,c,d,x,s,t){return cmn((b&c)|((~b)&d),a,b,x,s,t);}
    function gg(a,b,c,d,x,s,t){return cmn((b&d)|(c&(~d)),a,b,x,s,t);}
    function hh(a,b,c,d,x,s,t){return cmn(b^c^d,a,b,x,s,t);}
    function ii(a,b,c,d,x,s,t){return cmn(c^(b|(~d)),a,b,x,s,t);}
    function md5blk(s){var md5blks=[],i;for(i=0;i<64;i+=4){md5blks[i>>2]=s.charCodeAt(i)+(s.charCodeAt(i+1)<<8)+(s.charCodeAt(i+2)<<16)+(s.charCodeAt(i+3)<<24);}return md5blks;}
    function md5str(s){var n=s.length,state=[1732584193,-271733879,-1732584194,271733878],i;for(i=64;i<=n;i+=64){md5cycle(state,md5blk(s.substring(i-64,i)));}s=s.substring(i-64);var tail=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];for(i=0;i<s.length;i++){tail[i>>2]|=s.charCodeAt(i)<<((i%4)<<3);}tail[i>>2]|=0x80<<((i%4)<<3);if(i>55){md5cycle(state,tail);for(i=0;i<16;i++)tail[i]=0;}tail[14]=n*8;md5cycle(state,tail);return state;}
    function add32(a,b){return (a+b)&0xFFFFFFFF;}
    function hex(x){var s='',i;for(i=0;i<4;i++){s+='0123456789abcdef'.charAt((x>>(i*8+4))&0x0F)+'0123456789abcdef'.charAt((x>>(i*8))&0x0F);}return s;}
    function md5(s){var st=md5str(s);return hex(st[0])+hex(st[1])+hex(st[2])+hex(st[3]);}

    function generateCaptcha() {
        var ops = ['+', '-', '×'];
        var op = ops[Math.floor(Math.random() * ops.length)];
        var a, b;
        if (op === '-') {
            a = Math.floor(Math.random() * 20) + 5;
            b = Math.floor(Math.random() * a);
        } else if (op === '×') {
            a = Math.floor(Math.random() * 9) + 2;
            b = Math.floor(Math.random() * 9) + 1;
        } else {
            a = Math.floor(Math.random() * 30) + 1;
            b = Math.floor(Math.random() * 30) + 1;
        }
        switch(op) {
            case '+': captchaAnswer = a + b; break;
            case '-': captchaAnswer = a - b; break;
            case '×': captchaAnswer = a * b; break;
        }
        document.getElementById('captchaQuestion').textContent = a + ' ' + op + ' ' + b;
        var hashVal = md5('nrotft_captcha_2026' + captchaAnswer);
        document.getElementById('captcha_hash').value = hashVal;
        var inp = document.querySelector('input[name="captcha_answer"]');
        if (inp) inp.value = '';
    }
    generateCaptcha();

    // --- AJAX Login ---
    $(document).ready(function() {
        $('#loginForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: "POST",
                url: '/app/auth_process.php',
                data: form.serialize(),
                dataType: "json",
                success: function(response) {
                    var messageDiv = $('#loginMessage');
                    messageDiv.css('display', 'block').removeClass('success error');
                    if (response.status === 'success') {
                        messageDiv.addClass('success').text(response.message);
                        if (response.redirect) {
                            setTimeout(function() { window.location.href = response.redirect; }, 1000);
                        }
                    } else {
                        messageDiv.addClass('error').text(response.message);
                        generateCaptcha();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loginMessage').css('display', 'block').addClass('error').text('Ðã x?y ra l?i k?t n?i máy ch?. Vui lòng th? l?i.');
                    generateCaptcha();
                }
            });
        });
    });
    </script>
    
    <script src="/view/static/js/antigravity.js?v=2.1" type="text/javascript"></script>
</body>
</html>
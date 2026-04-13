<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/connect.php';

$acb_account = $_ENV_CONFIG['ACB_ACCOUNT'] ?? '';
$bank_name = $_ENV_CONFIG['BANK_NAME'] ?? 'ACB - Ngan hang A Chau';
$account_holder = $_ENV_CONFIG['ACCOUNT_HOLDER'] ?? 'MAI XUAN ANH';
$atm_prefix_raw = trim($_ENV_CONFIG['ATM_PREFIX'] ?? 'chuyen tien');
$atm_prefix = $atm_prefix_raw !== '' ? $atm_prefix_raw : 'chuyen tien';
$min_deposit_raw = intval($_ENV_CONFIG['MIN_DEPOSIT'] ?? 10000);
$max_deposit_raw = intval($_ENV_CONFIG['MAX_DEPOSIT'] ?? 10000000);
$min_deposit = number_format($min_deposit_raw);
$max_deposit = number_format($max_deposit_raw);

$logged_in_username = $_SESSION['username'] ?? null;
$player_name = $_SESSION['player_name'] ?? $logged_in_username;
$transfer_syntax = $logged_in_username ? ($atm_prefix . ' ' . $logged_in_username) : ($atm_prefix . ' <ten-tai-khoan>');

$recent_deposits = [];
if ($logged_in_username && isset($conn)) {
    $stmt = $conn->prepare(
        "SELECT id, transaction_number, amount, status, type, description, created_at
         FROM bank_transactions
         WHERE matched_username = ?
         ORDER BY created_at DESC
         LIMIT 100"
    );
    if ($stmt) {
        $stmt->bind_param("s", $logged_in_username);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recent_deposits[] = $row;
        }
        $stmt->close();
    }
}

include __DIR__ . '/head.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nạp ATM Bank | Chú Bé Rồng Online</title>
    <meta name="description" content="Nạp VND qua chuyển khoản ngân hàng tự động cộng tiền." />
    <link rel="icon" href="/images/favicon-48x48.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/view/static/css/apple_ui.css?v=4.0">
    <style>
        .atm-wrap {
            max-width: 1160px;
            margin: 0 auto;
            padding: 98px 18px 70px;
        }

        .atm-panel {
            background: #f4f6f9;
            border: 1px solid #e7ebf1;
            border-radius: 18px;
            padding: 24px;
        }

        .atm-title {
            margin: 0;
            color: #212a3a;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.03em;
            text-transform: uppercase;
        }

        .atm-sub {
            margin: 7px 0 24px;
            color: #6a7384;
            font-size: 16px;
        }

        .atm-grid {
            display: grid;
            grid-template-columns: 1.25fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .bank-select {
            background: #dbeafe;
            border: 2px solid #5b8ff3;
            border-radius: 14px;
            padding: 14px;
        }

        .bank-head {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .bank-badge {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(160deg, #2fb344, #1d8f38);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bank-name {
            margin: 0;
            font-size: 21px;
            color: #1c2638;
            font-weight: 700;
        }

        .bank-line {
            margin: 7px 0;
            color: #3f4c66;
            font-size: 15px;
        }

        .bank-line b { color: #1f2d49; }
        .bank-line .v {
            color: #265dcf;
            font-weight: 700;
        }

        .bank-range {
            margin-top: 12px;
            border-top: 1px solid #c5d8fa;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            color: #4a5c7f;
            font-size: 13px;
        }

        .bank-range strong {
            display: block;
            font-size: 26px;
            color: #233152;
            line-height: 1.1;
            margin-top: 2px;
        }

        .warn-box {
            background: #fff3df;
            border: 1px solid #ffe0aa;
            border-radius: 12px;
            padding: 14px;
            color: #b85c00;
            font-weight: 700;
            font-size: 17px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .amount-row {
            display: flex;
            margin-bottom: 10px;
        }

        .amount-row input {
            flex: 1;
            border: 1px solid #d4dbe6;
            border-right: none;
            border-radius: 10px 0 0 10px;
            height: 50px;
            padding: 0 14px;
            font-size: 17px;
            background: #fff;
            color: #1f2937;
        }

        .amount-row .currency {
            width: 96px;
            border: 1px solid #d4dbe6;
            border-radius: 0 10px 10px 0;
            background: #f7f9fc;
            color: #2a3550;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quick-values {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .quick-values button {
            border: 1px solid #d7deea;
            background: #fff;
            border-radius: 8px;
            font-size: 13px;
            color: #33405d;
            padding: 6px 10px;
            cursor: pointer;
            transition: all .15s ease;
        }

        .quick-values button:hover {
            border-color: #7ea6f8;
            color: #1f53c7;
            background: #eef4ff;
        }

        .create-btn {
            width: 100%;
            height: 49px;
            border: none;
            border-radius: 10px;
            background: #202b3f;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: opacity .15s ease;
        }

        .create-btn:hover { opacity: .92; }

        .toolbar {
            margin-top: 18px;
            display: grid;
            grid-template-columns: 84px 160px 1fr 1fr 1.1fr;
            gap: 10px;
            align-items: center;
        }

        .toolbar select,
        .toolbar input {
            height: 40px;
            border: 1px solid #d7deea;
            border-radius: 9px;
            background: #fff;
            color: #35425e;
            padding: 0 11px;
            font-size: 14px;
        }

        .table-box {
            margin-top: 14px;
            border: 1px solid #e2e8f2;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .atm-table {
            width: 100%;
            border-collapse: collapse;
        }

        .atm-table th {
            background: #f0f3f8;
            color: #2f3a53;
            padding: 12px 10px;
            font-size: 13px;
            font-weight: 700;
            text-align: left;
            border-bottom: 1px solid #e0e7f2;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .atm-table td {
            padding: 11px 10px;
            border-bottom: 1px solid #eef2f7;
            color: #334155;
            font-size: 14px;
            vertical-align: middle;
        }

        .atm-table tr:last-child td { border-bottom: none; }

        .money { font-weight: 700; color: #1f2d49; }
        .green { color: #16a34a; font-weight: 700; }

        .status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .st-success { background: #dcfce7; color: #166534; }
        .st-pending { background: #fef3c7; color: #92400e; }
        .st-failed, .st-ignored { background: #fee2e2; color: #991b1b; }

        .action-btn {
            border: 1px solid #d5dceb;
            border-radius: 8px;
            background: #fff;
            width: 32px;
            height: 30px;
            cursor: pointer;
            color: #4a5a7c;
        }

        .empty {
            text-align: center;
            padding: 30px 16px;
            color: #67728a;
            font-size: 14px;
            background: #fff;
        }

        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid #c9d5ee;
            border-radius: 8px;
            background: #eff5ff;
            color: #24479f;
            padding: 3px 9px;
            margin-left: 7px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .copy-btn.copied {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        .login-prompt {
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 16px;
            margin-bottom: 14px;
            font-size: 14px;
        }

        .login-prompt a { color: #1d4ed8; font-weight: 700; }

        .qr-modal {
            position: fixed;
            inset: 0;
            z-index: 1200;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, 0.6);
            padding: 16px;
        }

        .qr-modal.show {
            display: flex;
        }

        .qr-dialog {
            width: min(96vw, 520px);
            max-height: 92vh;
            overflow: auto;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #d7deea;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.3);
            padding: 14px;
        }

        .qr-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .qr-title {
            margin: 0;
            font-size: 18px;
            color: #1f2d49;
            font-weight: 800;
        }

        .qr-close {
            border: 1px solid #d5dceb;
            background: #fff;
            border-radius: 8px;
            width: 34px;
            height: 32px;
            cursor: pointer;
            color: #4a5a7c;
            font-size: 18px;
            line-height: 1;
        }

        .qr-image-wrap {
            display: flex;
            justify-content: center;
            border: 1px dashed #d7deea;
            border-radius: 12px;
            background: #f8fafc;
            padding: 10px;
            margin-bottom: 10px;
        }

        .qr-image-wrap img {
            width: 100%;
            max-width: 320px;
            border-radius: 10px;
            background: #fff;
        }

        .qr-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px;
            color: #334155;
            font-size: 14px;
            line-height: 1.55;
        }

        .qr-info strong {
            color: #1f2d49;
        }

        @media (max-width: 980px) {
            .atm-grid { grid-template-columns: 1fr; }
            .toolbar { grid-template-columns: 1fr 1fr; }
            .toolbar .full { grid-column: 1 / -1; }
            .atm-table-wrap { overflow-x: auto; }
            .atm-table { min-width: 930px; }
        }

        @media (max-width: 640px) {
            .atm-wrap { padding: 82px 12px 50px; }
            .atm-panel { padding: 16px; }
            .atm-title { font-size: 24px; }
            .bank-range strong { font-size: 20px; }
            .toolbar { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>

<main class="atm-wrap">
    <section class="atm-panel">
        <h1 class="atm-title">Nạp Tiền Tài Khoản</h1>
        <p class="atm-sub">Thanh toán nhanh chóng và an toàn</p>

        <?php if (!$logged_in_username): ?>
        <div class="login-prompt">
            Bạn cần <a href="/login">đăng nhập</a> để tạo hóa đơn nạp tiền theo tài khoản của bạn.
        </div>
        <?php endif; ?>

        <div class="atm-grid">
            <div>
                <div class="bank-select">
                    <div class="bank-head">
                        <div class="bank-badge">V</div>
                        <h2 class="bank-name"><?php echo htmlspecialchars($bank_name); ?></h2>
                    </div>
                    <div class="bank-line">Số tài khoản: <span class="v" id="bank-account"><?php echo htmlspecialchars($acb_account); ?></span>
                        <button class="copy-btn" onclick="copyText('<?php echo htmlspecialchars($acb_account); ?>', this)">Sao chép</button>
                    </div>
                    <div class="bank-line">Chủ tài khoản: <b><?php echo htmlspecialchars($account_holder); ?></b></div>
                    <div class="bank-line">Nội dung CK: <span class="v" id="transfer-syntax"><?php echo htmlspecialchars($transfer_syntax); ?></span>
                        <?php if ($logged_in_username): ?>
                        <button class="copy-btn" onclick="copyText('<?php echo htmlspecialchars($transfer_syntax); ?>', this)">Sao chép</button>
                        <?php endif; ?>
                    </div>
                    <?php if ($logged_in_username && $player_name !== $logged_in_username): ?>
                    <div class="bank-line" style="font-size:12px;color:#64748b">Nhân vật: <?php echo htmlspecialchars($player_name); ?> - Tài khoản: <?php echo htmlspecialchars($logged_in_username); ?></div>
                    <?php endif; ?>

                    <div class="bank-range">
                        <div>
                            Tối thiểu
                            <strong><?php echo $min_deposit; ?></strong>
                        </div>
                        <div style="text-align:right">
                            Tối đa
                            <strong><?php echo $max_deposit; ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="warn-box">⚠ Lưu ý!!</div>
                <div style="margin-top:12px;color:#6b7280;font-size:13px;line-height:1.6;background:#fff;border:1px dashed #e2e8f0;border-radius:12px;padding:12px;">
                    Ghi đúng nội dung chuyển khoản để hệ thống nhận diện tự động.<br>
                    Sai nội dung hoặc nạp ngoài giới hạn có thể bị treo xử lý.
                </div>
            </div>
        </div>

        <div class="amount-row">
            <input type="text" id="amountInput" placeholder="Nhập số tiền cần nạp" inputmode="numeric" autocomplete="off">
            <div class="currency">VND</div>
        </div>

        <div class="quick-values">
            <button type="button" onclick="setQuickAmount(50000)">50,000</button>
            <button type="button" onclick="setQuickAmount(100000)">100,000</button>
            <button type="button" onclick="setQuickAmount(300000)">300,000</button>
            <button type="button" onclick="setQuickAmount(500000)">500,000</button>
            <button type="button" onclick="setQuickAmount(1000000)">1,000,000</button>
            <button type="button" onclick="setQuickAmount(3000000)">3,000,000</button>
            <button type="button" onclick="setQuickAmount(5000000)">5,000,000</button>
            <button type="button" onclick="setQuickAmount(10000000)">10,000,000</button>
        </div>

        <button class="create-btn" type="button" onclick="createInvoice()">TẠO HÓA ĐƠN</button>

        <div class="toolbar">
            <select id="limitFilter">
                <option value="15">15</option>
                <option value="30">30</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>

            <select id="statusFilter">
                <option value="">(?) Trạng thái</option>
                <option value="success">Thành công</option>
                <option value="pending">Đang xử lý</option>
                <option value="failed">Thất bại</option>
                <option value="ignored">Đã bỏ qua</option>
            </select>

            <input class="full" type="datetime-local" id="dateFrom" placeholder="Từ ngày">
            <input class="full" type="datetime-local" id="dateTo" placeholder="Đến ngày">
            <input class="full" type="text" id="searchFilter" placeholder="Tìm kiếm mã hóa đơn...">
        </div>

        <div class="table-box atm-table-wrap">
            <table class="atm-table" id="atmTable">
                <thead>
                    <tr>
                        <th>Thao tác</th>
                        <th>Mã hóa đơn</th>
                        <th>Cổng thanh toán</th>
                        <th>Số tiền</th>
                        <th>Thực nhận</th>
                        <th>Thưởng</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody id="historyBody">
                <?php if (!empty($recent_deposits)): ?>
                    <?php foreach ($recent_deposits as $dep):
                        $status = strtolower((string)($dep['status'] ?? 'pending'));
                        $status_class = 'st-pending';
                        $status_label = 'Đang xử lý';
                        if ($status === 'success') { $status_class = 'st-success'; $status_label = 'Thành công'; }
                        elseif ($status === 'failed') { $status_class = 'st-failed'; $status_label = 'Thất bại'; }
                        elseif ($status === 'ignored') { $status_class = 'st-ignored'; $status_label = 'Đã bỏ qua'; }
                        $created_raw = $dep['created_at'] ?? '';
                        $created_at = $created_raw ? strtotime($created_raw) : time();
                        $transaction_code = !empty($dep['transaction_number']) ? ('NDHx' . $dep['transaction_number']) : ('NDHx' . str_pad((string)$dep['id'], 6, '0', STR_PAD_LEFT));
                    ?>
                    <tr
                        data-status="<?php echo htmlspecialchars($status); ?>"
                        data-created="<?php echo (int)$created_at; ?>"
                        data-code="<?php echo htmlspecialchars(strtolower($transaction_code)); ?>"
                    >
                        <td><button class="action-btn" type="button" title="Chi tiết">👁</button></td>
                        <td><strong><?php echo htmlspecialchars($transaction_code); ?></strong></td>
                        <td><?php echo htmlspecialchars($bank_name); ?></td>
                        <td class="money"><?php echo number_format((int)$dep['amount']); ?></td>
                        <td class="green">+ <?php echo number_format((int)$dep['amount']); ?></td>
                        <td>0</td>
                        <td><span class="status <?php echo $status_class; ?>"><?php echo $status_label; ?></span></td>
                        <td><?php echo date('Y-m-d H:i:s', $created_at); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty">Chưa có giao dịch nạp tiền nào.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>
<div class="qr-modal" id="qrModal" onclick="closeQrModal(event)">
    <div class="qr-dialog" role="dialog" aria-modal="true" aria-labelledby="qrModalTitle">
        <div class="qr-head">
            <h3 class="qr-title" id="qrModalTitle">Quét QR để nạp tiền</h3>
            <button type="button" class="qr-close" onclick="closeQrModal()">&times;</button>
        </div>
        <div class="qr-info" style="margin-bottom:10px">
            <div>Ngân hàng: <strong id="qrBankName"><?php echo htmlspecialchars($bank_name); ?></strong></div>
            <div>Số tài khoản: <strong id="qrAccountNumber"><?php echo htmlspecialchars($acb_account); ?></strong></div>
            <div>Chủ tài khoản: <strong id="qrAccountHolder"><?php echo htmlspecialchars($account_holder); ?></strong></div>
            <div>Nội dung CK: <strong id="qrTransferSyntax"><?php echo htmlspecialchars($transfer_syntax); ?></strong></div>
            <div>Số tiền: <strong id="qrAmount">0 VND</strong></div>
        </div>
        <div class="qr-image-wrap">
            <img id="qrImage" src="" alt="VietQR Nap ATM">
        </div>
    </div>
</div>
<script>
const minDeposit = <?php echo (int)$min_deposit_raw; ?>;
const maxDeposit = <?php echo (int)$max_deposit_raw; ?>;
const transferSyntax = <?php echo json_encode($transfer_syntax, JSON_UNESCAPED_UNICODE); ?>;
const accountNumber = <?php echo json_encode($acb_account, JSON_UNESCAPED_UNICODE); ?>;
const accountHolder = <?php echo json_encode($account_holder, JSON_UNESCAPED_UNICODE); ?>;
const bankName = <?php echo json_encode($bank_name, JSON_UNESCAPED_UNICODE); ?>;

function formatMoneyInput(v) {
    const num = String(v || '').replace(/[^\d]/g, '');
    if (!num) return '';
    return Number(num).toLocaleString('en-US');
}

function parseMoneyInput(v) {
    return Number(String(v || '').replace(/[^\d]/g, '')) || 0;
}

function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const old = btn.textContent;
        btn.textContent = 'Đã chép';
        btn.classList.add('copied');
        setTimeout(() => {
            btn.textContent = old === 'Đã chép' ? 'Sao chép' : old;
            btn.classList.remove('copied');
        }, 1300);
    });
}

function setQuickAmount(amount) {
    const input = document.getElementById('amountInput');
    input.value = formatMoneyInput(amount);
}

function createInvoice() {
    <?php if (!$logged_in_username): ?>
    window.location.href = '/login';
    return;
    <?php endif; ?>

    const amount = parseMoneyInput(document.getElementById('amountInput').value);
    if (!amount) {
        alert('Vui lòng nhập số tiền cần nạp.');
        return;
    }
    if (amount < minDeposit) {
        alert('Số tiền tối thiểu là ' + minDeposit.toLocaleString('vi-VN') + ' VND.');
        return;
    }
    if (amount > maxDeposit) {
        alert('Số tiền tối đa là ' + maxDeposit.toLocaleString('vi-VN') + ' VND.');
        return;
    }

    const qrUrl =
        'https://img.vietqr.io/image/970416-' + encodeURIComponent(accountNumber) + '-print.png' +
        '?amount=' + amount +
        '&addInfo=' + encodeURIComponent(transferSyntax) +
        '&accountName=' + encodeURIComponent(accountHolder);
    const qrImage = document.getElementById('qrImage');
    const qrModal = document.getElementById('qrModal');
    document.getElementById('qrBankName').textContent = bankName;
    document.getElementById('qrAccountNumber').textContent = accountNumber;
    document.getElementById('qrAccountHolder').textContent = accountHolder;
    document.getElementById('qrTransferSyntax').textContent = transferSyntax;
    document.getElementById('qrAmount').textContent = amount.toLocaleString('vi-VN') + ' VND';
    qrImage.src = qrUrl;
    qrModal.classList.add('show');
}
function closeQrModal(event) {
    const modal = document.getElementById('qrModal');
    if (event && event.target && event.target.id !== 'qrModal') return;
    modal.classList.remove('show');
}
function applyFilters() {
    const limit = Number(document.getElementById('limitFilter').value || 15);
    const status = document.getElementById('statusFilter').value.trim();
    const from = document.getElementById('dateFrom').value ? Math.floor(new Date(document.getElementById('dateFrom').value).getTime() / 1000) : null;
    const to = document.getElementById('dateTo').value ? Math.floor(new Date(document.getElementById('dateTo').value).getTime() / 1000) : null;
    const search = document.getElementById('searchFilter').value.trim().toLowerCase();

    const rows = Array.from(document.querySelectorAll('#historyBody tr[data-status]'));
    let shown = 0;

    rows.forEach(row => {
        const rowStatus = row.dataset.status || '';
        const rowCreated = Number(row.dataset.created || 0);
        const rowCode = row.dataset.code || '';

        const okStatus = !status || rowStatus === status;
        const okFrom = !from || rowCreated >= from;
        const okTo = !to || rowCreated <= to;
        const okSearch = !search || rowCode.includes(search);

        const ok = okStatus && okFrom && okTo && okSearch && shown < limit;
        row.style.display = ok ? '' : 'none';
        if (ok) shown += 1;
    });
}

(function init() {
    const amountInput = document.getElementById('amountInput');
    amountInput.addEventListener('input', function() {
        const pos = this.selectionStart;
        this.value = formatMoneyInput(this.value);
        this.setSelectionRange(this.value.length, this.value.length);
    });

    ['limitFilter', 'statusFilter', 'dateFrom', 'dateTo', 'searchFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('input', applyFilters);
        el.addEventListener('change', applyFilters);
    });

    applyFilters();
})();
</script>
<script src="/view/static/js/antigravity.js?v=2.1"></script>
</body>
</html>



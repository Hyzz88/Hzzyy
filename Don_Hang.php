<?php
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();
include "Trang_Chu_Includes/connect_db.inc";

if (!isset($_SESSION['user'])) {
    header("Location: Dang_nhap.php");
    exit();
}

$maND        = $_SESSION['user']['MaND'] ?? ($_SESSION['user']['id'] ?? 0);
$maDH_detail = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int)$_GET['id'] : 0;

function statusKey($status) {
    $status = trim((string)$status);
    $map = [
        'Cho xac nhan' => 'cho_xac_nhan',
        'Cho xu ly' => 'cho_xac_nhan',
        'Chờ xử lý' => 'cho_xac_nhan',
        'Chờ xác nhận' => 'cho_xac_nhan',
        'Chờ xác nhận' => 'cho_xac_nhan',
        'Dang giao' => 'dang_giao',
        'Đang giao' => 'dang_giao',
        'Äang giao' => 'dang_giao',
        'Hoan thanh' => 'hoan_thanh',
        'Hoàn thành' => 'hoan_thanh',
        'Hoàn thành' => 'hoan_thanh',
        'Da huy' => 'da_huy',
        'Đã hủy' => 'da_huy',
        'ÄÃ£ há»§y' => 'da_huy',
    ];
    $statusLower = strtolower($status);
    if (strpos($statusLower, 'xu') !== false && strpos($statusLower, 'ly') !== false) {
        return 'cho_xac_nhan';
    }
    if ((strpos($statusLower, 'xac') !== false && strpos($statusLower, 'nhan') !== false) || strpos($statusLower, 'chờ xác nhận') !== false) {
        return 'cho_xac_nhan';
    }
    if (strpos($statusLower, 'giao') !== false) return 'dang_giao';
    if (strpos($statusLower, 'huy') !== false || strpos($statusLower, 'hủy') !== false) return 'da_huy';
    if (strpos($statusLower, 'hoan') !== false || strpos($statusLower, 'hoàn') !== false || strpos($statusLower, 'thanh') !== false || strpos($statusLower, 'thành') !== false) {
        return 'hoan_thanh';
    }
    $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $statusLower);
    if ($ascii === false) $ascii = $statusLower;
    $ascii = strtolower($ascii);
    $compact = preg_replace('/[^a-z0-9]+/', '', $ascii);
    if (
        strpos($compact, 'choxacnhan') !== false ||
        strpos($compact, 'xacnhan') !== false ||
        strpos($compact, 'choxuly') !== false ||
        strpos($compact, 'xuly') !== false
    ) return 'cho_xac_nhan';
    if (strpos($compact, 'danggiao') !== false || strpos($compact, 'giao') !== false) return 'dang_giao';
    if (strpos($compact, 'dahuy') !== false || strpos($compact, 'huy') !== false) return 'da_huy';
    if (strpos($compact, 'hoanthanh') !== false || strpos($compact, 'thanhcong') !== false) return 'hoan_thanh';
    return $map[$status] ?? $status;
}

/* ===== ACTION FIX ===== */
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];

    $checkFixed = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT MaDH, TrangThai, NgayDat, GhiChu FROM donhang WHERE MaDH=$id AND MaND=$maND"
    ));

    if ($checkFixed) {
        $statusFixed = statusKey($checkFixed['TrangThai']);

        if ($_GET['action'] === 'huy' && $statusFixed === 'cho_xac_nhan') {
            $lyDo = isset($_GET['lydo'])
                ? mysqli_real_escape_string($conn, urldecode($_GET['lydo']))
                : 'Khong co ly do';
            $daYeuCauHuy = preg_match('/\[(Yeu cau huy|Yeu cau huy luc):/i', $checkFixed['GhiChu'] ?? '') === 1;
            if (!$daYeuCauHuy) {
                mysqli_query($conn,
                    "UPDATE donhang
                     SET GhiChu=CONCAT(
                         IFNULL(GhiChu,''),
                         ' [Yeu cau huy: $lyDo]',
                         ' [Yeu cau huy luc: ', NOW(), ']'
                     )
                     WHERE MaDH=$id"
                );
            }
        }

        if ($_GET['action'] === 'nhan' && $statusFixed === 'dang_giao') {
            $daCoMocHoanThanh = preg_match('/\[Hoan thanh luc: [^\]]+\]/i', $checkFixed['GhiChu'] ?? '') === 1;
            if ($daCoMocHoanThanh) {
                mysqli_query($conn, "UPDATE donhang SET TrangThai='Hoan thanh' WHERE MaDH=$id");
            } else {
                mysqli_query($conn,
                    "UPDATE donhang
                     SET TrangThai='Hoan thanh',
                         GhiChu=CONCAT(IFNULL(GhiChu,''), ' [Hoan thanh luc: ', NOW(), ']')
                     WHERE MaDH=$id"
                );
            }
        }

        if ($_GET['action'] === 'boihoan' && $statusFixed === 'hoan_thanh') {
            $lyDo = isset($_GET['lydo'])
                ? mysqli_real_escape_string($conn, urldecode($_GET['lydo']))
                : 'Khong co ly do';

            preg_match('/\[Hoan thanh luc: ([0-9:\-\s]+)\]/i', $checkFixed['GhiChu'] ?? '', $mDoneAt);
            $doneTs = !empty($mDoneAt[1]) ? strtotime($mDoneAt[1]) : false;
            if ($doneTs === false) $doneTs = strtotime($checkFixed['NgayDat'] ?? '');
            $soNgay = ($doneTs !== false) ? (int)floor((time() - $doneTs) / 86400) : 9999;
            $daYeuCau = preg_match('/\[(Yeu cau boi hoan|Yeu cau boi hoan luc):/i', $checkFixed['GhiChu'] ?? '') === 1;

            if ($soNgay <= 7 && !$daYeuCau) {
                mysqli_query($conn,
                    "UPDATE donhang
                     SET GhiChu=CONCAT(
                         IFNULL(GhiChu,''),
                         ' [Yeu cau boi hoan: $lyDo]',
                         ' [Yeu cau boi hoan luc: ', NOW(), ']'
                     )
                     WHERE MaDH=$id"
                );
            }
        }
    }

    header("Location: Don_Hang.php?id=$id");
    exit();
}

/* ===== ACTION ===== */
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];

    $check = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT MaDH, TrangThai FROM donhang WHERE MaDH=$id AND MaND=$maND"
    ));

    if ($check) {
        if ($_GET['action'] === 'huy' && $check['TrangThai'] === 'Chờ xác nhận') {
            $lyDo = isset($_GET['lydo'])
                ? mysqli_real_escape_string($conn, urldecode($_GET['lydo']))
                : 'Khong co ly do';
            // Luu ly do vao GhiChu vi DB chua co cot LyDoHuy
            mysqli_query($conn,
                "UPDATE donhang
                 SET TrangThai='Da huy',
                     GhiChu=CONCAT(IFNULL(GhiChu,''), ' [Lý do hủy: $lyDo]')
                 WHERE MaDH=$id"
            );
        }
        if ($_GET['action'] === 'huy' && $check['TrangThai'] === 'Chờ xác nhận') {
            $lyDo = isset($_GET['lydo'])
                ? mysqli_real_escape_string($conn, urldecode($_GET['lydo']))
                : 'Không có lý do';
            mysqli_query($conn,
                "UPDATE donhang
                 SET TrangThai='Đã hủy',
                     GhiChu=CONCAT(IFNULL(GhiChu,''), ' [Lý do hủy: $lyDo]')
                 WHERE MaDH=$id"
            );
        }
        if ($_GET['action'] === 'nhan' && $check['TrangThai'] === 'Đang giao') {
            mysqli_query($conn, "UPDATE donhang SET TrangThai='Hoàn thành' WHERE MaDH=$id");
        }
    }

    header("Location: Don_Hang.php?id=$id");
    exit();
}

/* ===== HELPER: BADGE CLASS ===== */
function badgeClass($status) {
    $normalizedStatus = statusKey($status);
    if ($normalizedStatus === 'cho_xac_nhan') return 'badge-cho';
    if ($normalizedStatus === 'dang_giao')    return 'badge-giao';
    if ($normalizedStatus === 'hoan_thanh')   return 'badge-hoan';
    if ($normalizedStatus === 'da_huy')       return 'badge-huy';
    if ($status === 'Chờ xác nhận') return 'badge-cho';
    if ($status === 'Đang giao')    return 'badge-giao';
    if ($status === 'Hoàn thành')   return 'badge-hoan';
    if ($status === 'Đã hủy')       return 'badge-huy';
    return 'badge-cho';
}

function statusView($status, $ghiChu = '') {
    if (preg_match('/\[Admin chap nhan boi hoan luc:/i', $ghiChu ?? '')) {
        return ['class' => 'badge-refund-ok', 'label' => 'Da boi hoan'];
    }
    if (preg_match('/\[Admin tu choi boi hoan luc:/i', $ghiChu ?? '')) {
        return ['class' => 'badge-refund-no', 'label' => 'Tu choi boi hoan'];
    }

    $key = statusKey($status);
    if ($key === 'cho_xac_nhan') return ['class' => 'badge-cho', 'label' => 'Cho xac nhan'];
    if ($key === 'dang_giao')    return ['class' => 'badge-giao', 'label' => 'Dang giao'];
    if ($key === 'hoan_thanh')   return ['class' => 'badge-hoan', 'label' => 'Hoan thanh'];
    if ($key === 'da_huy')       return ['class' => 'badge-huy', 'label' => 'Da huy'];

    return ['class' => badgeClass($status), 'label' => (string)$status];
}

/* ===== HELPER: TIMELINE ===== */
function buildTimeline($status) {
    $normalizedStatus = statusKey($status);
    $steps = [
        ['label' => 'Chờ xác nhận', 'icon' => '🕐', 'desc' => 'Đơn hàng đang chờ shop xác nhận'],
        ['label' => 'Đang giao',    'icon' => '🚚', 'desc' => 'Đơn hàng đang trên đường giao đến bạn'],
        ['label' => 'Hoàn thành',   'icon' => '✅', 'desc' => 'Đơn hàng đã được giao thành công'],
    ];
    $current = -1;
    if ($normalizedStatus === 'cho_xac_nhan') $current = 0;
    if ($normalizedStatus === 'dang_giao')    $current = 1;
    if ($normalizedStatus === 'hoan_thanh')   $current = 2;
    if ($current >= 0) return [$steps, $current];
    if ($status === 'Chờ xác nhận') $current = 0;
    if ($status === 'Đang giao')    $current = 1;
    if ($status === 'Hoàn thành')   $current = 2;
    return [$steps, $current];
}

/* ===== HELPER: ẢNH ===== */
function buildImageList($raw) {
    if (!$raw) return [];
    $arr = array_filter(array_map('trim', explode("\n", $raw)));
    $result = [];
    foreach ($arr as $img) {
        $img = str_replace("\r", '', $img);
        if (strpos($img, 'C:\\') !== false) {
            $img = str_replace('C:\\xampp\\htdocs\\Bai_Thi_Cuoi_Ki\\', '', $img);
            $img = str_replace('\\', '/', $img);
        }
        if (strpos($img, '/') === false) $img = 'uploads/' . $img;
        if (substr($img, 0, 1) !== '/')  $img = '/Bai_Thi_Cuoi_Ki/' . $img;
        $result[] = $img;
    }
    return $result;
}

$rs = mysqli_query($conn,
    "SELECT * FROM donhang WHERE MaND=$maND ORDER BY MaDH DESC"
);
?>

<style>
body { background:#f0f2f5; }

.page-title {
    font-weight:800; font-size:1.5rem; color:#1a1a2e;
    margin-bottom:24px; display:flex; align-items:center; gap:10px;
}

/* ---- Table ---- */
.order-card {
    background:#fff; border-radius:16px;
    box-shadow:0 4px 24px rgba(0,0,0,.08); overflow:hidden;
}
.order-card thead {
    background:linear-gradient(135deg,#1a1a2e,#16213e); color:#fff;
}
.order-card th { padding:14px 16px; font-weight:600; }
.order-card td { padding:13px 16px; vertical-align:middle; }
.order-card tbody tr:hover { background:#f5f7ff; }

.badge-status {
    display:inline-block; padding:4px 12px;
    border-radius:20px; font-size:12px; font-weight:600;
}
.badge-cho  { background:#fff3cd; color:#856404; }
.badge-giao { background:#cfe2ff; color:#084298; }
.badge-hoan { background:#d1e7dd; color:#0f5132; }
.badge-huy  { background:#f8d7da; color:#842029; }
.badge-refund-ok { background:#d4edda; color:#155724; }
.badge-refund-no { background:#f8d7da; color:#721c24; }

/* ---- Overlay ---- */
.overlay {
    position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(10,10,30,.65); backdrop-filter:blur(4px);
    display:none; justify-content:center; align-items:flex-start;
    z-index:9999; padding:24px 12px; overflow-y:auto;
}
.modal-box {
    background:#fff; width:100%; max-width:860px;
    border-radius:20px; overflow:hidden;
    box-shadow:0 30px 80px rgba(0,0,0,.25);
    animation:slideUp .28s ease; margin:auto;
}
@keyframes slideUp {
    from { transform:translateY(36px); opacity:0; }
    to   { transform:translateY(0);    opacity:1; }
}

/* Modal header */
.mhd {
    background:linear-gradient(135deg,#1a1a2e,#0d6efd);
    color:#fff; padding:18px 22px;
    display:flex; justify-content:space-between; align-items:center;
}
.mhd h5 { margin:0; font-weight:700; font-size:1rem; }
.btn-x {
    background:rgba(255,255,255,.2); border:none; color:#fff;
    border-radius:50%; width:32px; height:32px; cursor:pointer;
    font-size:16px; display:flex; align-items:center; justify-content:center;
}
.btn-x:hover { background:rgba(255,255,255,.35); }

.mbody { padding:22px; }

/* Info grid */
.info-grid {
    display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:22px;
}
@media(max-width:576px){ .info-grid{ grid-template-columns:1fr; } }
.info-item {
    background:#f8f9fa; border-radius:12px; padding:11px 15px;
}
.info-item label {
    font-size:11px; color:#999; text-transform:uppercase;
    letter-spacing:.4px; display:block; margin-bottom:3px;
}
.info-item span { font-weight:600; font-size:14px; color:#1a1a2e; }

/* Timeline */
.tl-title {
    font-weight:700; font-size:14px; color:#1a1a2e;
    margin-bottom:18px; display:flex; align-items:center; gap:8px;
}
.tl-wrap {
    display:flex; justify-content:space-between;
    position:relative; padding:0 8px; margin-bottom:8px;
}
.tl-wrap::before {
    content:''; position:absolute; top:21px; left:28px; right:28px;
    height:3px; background:#e0e0e0; z-index:0;
}
.tl-prog {
    position:absolute; top:21px; left:28px;
    height:3px; background:linear-gradient(90deg,#28a745,#20c997);
    z-index:1; transition:width .5s ease;
}
.tl-step {
    display:flex; flex-direction:column; align-items:center;
    position:relative; z-index:2; flex:1;
}
.tl-dot {
    width:44px; height:44px; border-radius:50%;
    background:#e9ecef; border:3px solid #ddd;
    display:flex; align-items:center; justify-content:center; font-size:18px;
}
.tl-step.done .tl-dot {
    background:linear-gradient(135deg,#28a745,#20c997);
    border-color:#28a745;
    box-shadow:0 4px 12px rgba(40,167,69,.3);
}
.tl-step.current .tl-dot {
    background:linear-gradient(135deg,#0d6efd,#0dcaf0);
    border-color:#0d6efd;
    animation:pulse 1.6s infinite;
}
@keyframes pulse {
    0%,100% { box-shadow:0 4px 12px rgba(13,110,253,.4); }
    50%      { box-shadow:0 4px 22px rgba(13,110,253,.7); }
}
.tl-lbl {
    margin-top:7px; font-size:11px; font-weight:600;
    color:#bbb; text-align:center; max-width:76px; line-height:1.3;
}
.tl-step.done .tl-lbl,
.tl-step.current .tl-lbl { color:#1a1a2e; }
.tl-desc {
    text-align:center; font-size:13px; color:#555;
    background:#f0f7ff; border-radius:10px;
    padding:9px 14px; margin-bottom:20px;
}

/* Cancel banner */
.cancel-banner {
    background:linear-gradient(135deg,#ff6b6b,#ee5a24);
    color:#fff; border-radius:12px; padding:14px 18px;
    display:flex; align-items:center; gap:12px;
    margin-bottom:20px; font-size:14px;
}

/* Products */
.sec-title {
    font-weight:700; font-size:14px; color:#1a1a2e;
    margin-bottom:12px; display:flex; align-items:center; gap:8px;
}
.product-row {
    display:flex; gap:14px; padding:13px;
    border:1px solid #f0f0f0; border-radius:12px;
    margin-bottom:10px; transition:border-color .2s;
}
.product-row:hover { border-color:#c0d4ff; background:#f8f9ff; }
.main-img { width:78px; height:78px; border-radius:10px; object-fit:cover; flex-shrink:0; }
.pname  { font-weight:700; font-size:14px; margin-bottom:3px; }
.pmeta  { font-size:13px; color:#666; }
.pprice { font-size:15px; font-weight:700; color:#e74c3c; margin-top:4px; }

/* Modal footer */
.mfoot {
    background:#f8f9fa; border-top:1px solid #eee;
    padding:16px 22px; display:flex;
    justify-content:space-between; align-items:center;
    flex-wrap:wrap; gap:12px;
}
.total-lbl { font-size:11px; color:#999; margin-bottom:2px; }
.total-amt { font-size:1.3rem; font-weight:800; color:#e74c3c; }

.btn-huy {
    background:linear-gradient(135deg,#e74c3c,#c0392b);
    color:#fff; border:none; border-radius:25px;
    padding:9px 20px; font-size:13px; font-weight:600;
    cursor:pointer; display:inline-flex; align-items:center; gap:6px;
}
.btn-huy:hover { opacity:.88; }
.btn-nhan {
    background:linear-gradient(135deg,#28a745,#20c997);
    color:#fff; border:none; border-radius:25px;
    padding:9px 20px; font-size:13px; font-weight:600;
    cursor:pointer; display:inline-flex; align-items:center; gap:6px;
    text-decoration:none;
}
.btn-nhan:hover { opacity:.88; color:#fff; }
.btn-boihoan {
    background:linear-gradient(135deg,#ff9800,#f57c00);
    color:#fff; border:none; border-radius:25px;
    padding:9px 20px; font-size:13px; font-weight:600;
    cursor:pointer; display:inline-flex; align-items:center; gap:6px;
}
.btn-boihoan:hover { opacity:.9; }
.refund-banner {
    background:linear-gradient(135deg,#ffb74d,#ff9800);
    color:#fff; border-radius:12px; padding:14px 18px;
    display:flex; align-items:center; gap:12px;
    margin-bottom:20px; font-size:14px;
}

/* Cancel confirm modal */
.cm-wrap {
    position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,.6); display:none;
    justify-content:center; align-items:center; z-index:10000;
}
.cm-box {
    background:#fff; border-radius:16px; padding:26px;
    max-width:420px; width:90%;
    box-shadow:0 20px 60px rgba(0,0,0,.2);
    animation:slideUp .2s ease;
}
.cm-box h6 { font-weight:700; margin-bottom:14px; }
.reason-list { list-style:none; padding:0; margin:0 0 16px; }
.reason-list li {
    padding:10px 14px; border:2px solid #eee;
    border-radius:10px; margin-bottom:7px;
    cursor:pointer; font-size:13px; transition:all .18s;
}
.reason-list li:hover,
.reason-list li.selected {
    border-color:#e74c3c; background:#fff5f5;
    color:#e74c3c; font-weight:600;
}
.cm-btns { display:flex; gap:10px; }
.cm-btns button {
    flex:1; border-radius:10px; padding:10px;
    font-weight:600; border:none; cursor:pointer; font-size:13px;
}
.cm-back    { background:#eee; color:#333; }
.cm-confirm { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; }
</style>

<div class="container py-5">

<h2 class="page-title">📦 Lịch sử đơn hàng</h2>

<!-- ===== DANH SÁCH ===== -->
<div class="order-card">
<table class="table mb-0">
<thead>
<tr>
  <th>Mã đơn</th>
  <th>Ngày đặt</th>
  <th>Địa chỉ giao</th>
  <th>Tổng tiền</th>
  <th>Trạng thái</th>
  <th style="width:110px"></th>
</tr>
</thead>
<tbody>
<?php
$count = 0;
while ($r = mysqli_fetch_assoc($rs)):
    $count++;
    $sv = statusView($r['TrangThai'], $r['GhiChu'] ?? '');
?>
<tr>
  <td><strong>#<?= $r['MaDH'] ?></strong></td>
  <td><?= date('d/m/Y H:i', strtotime($r['NgayDat'])) ?></td>
  <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
    <?= htmlspecialchars($r['DiaChiGiaoHang']) ?>
  </td>
  <td class="text-danger fw-bold"><?= number_format($r['TongTien']) ?>đ</td>
  <td>
    <span class="badge-status <?= $sv['class'] ?>">
      <?= $sv['label'] ?>
    </span>
  </td>
  <td>
    <a href="?id=<?= $r['MaDH'] ?>" class="btn btn-primary btn-sm rounded-pill px-3">
      Xem chi tiết
    </a>
  </td>
</tr>
<?php endwhile; ?>
<?php if ($count === 0): ?>
<tr>
  <td colspan="6" class="text-center text-muted py-4">Bạn chưa có đơn hàng nào.</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

<!-- ===== MODAL CHI TIẾT ===== -->
<div id="overlay" class="overlay" onclick="handleBg(event)">
<div class="modal-box" onclick="event.stopPropagation()">

<?php if ($maDH_detail > 0):

    /* ---- Query đúng tên cột theo schema ---- */
    $dh = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT  dh.MaDH,
                dh.NgayDat,
                dh.TongTien,
                dh.TrangThai,
                dh.DiaChiGiaoHang,
                dh.GhiChu,
                nd.HoTen,
                nd.DienThoai,
                nd.Email
        FROM    donhang dh
        LEFT JOIN nguoidung nd ON dh.MaND = nd.MaND
        WHERE   dh.MaDH = $maDH_detail
          AND   dh.MaND = $maND
    "));

    if (!$dh): ?>

    <div class="mhd">
      <h5>⚠️ Không tìm thấy đơn hàng</h5>
      <button class="btn-x" onclick="closeModal()">✕</button>
    </div>
    <div class="mbody">
      <p class="text-muted">Đơn hàng không tồn tại hoặc không thuộc tài khoản của bạn.</p>
    </div>

    <?php else:

    $isCancelled = ($dh['TrangThai'] === 'Đã hủy');
    $statusDH = statusKey($dh['TrangThai']);
    $svDetail = statusView($dh['TrangThai'], $dh['GhiChu'] ?? '');
    $isCancelled = ($statusDH === 'da_huy');
    preg_match('/\[Hoan thanh luc: ([0-9:\-\s]+)\]/i', $dh['GhiChu'] ?? '', $mHoanThanh);
    $hoanThanhTs = !empty($mHoanThanh[1]) ? strtotime($mHoanThanh[1]) : false;
    if ($hoanThanhTs === false) {
        // Fallback for old orders that do not have completion timestamp yet
        $hoanThanhTs = strtotime($dh['NgayDat'] ?? '');
    }
    $soNgayHoanThanh = ($hoanThanhTs !== false) ? (int)floor((time() - $hoanThanhTs) / 86400) : 9999;
    $isCancelRequested = preg_match('/\[(Yeu cau huy|Yeu cau huy luc):/i', $dh['GhiChu'] ?? '') === 1;
    $isRefundRequested = preg_match('/\[(Yeu cau boi hoan|Yeu cau boi hoan luc):/i', $dh['GhiChu'] ?? '') === 1;
    $isRefundApproved = preg_match('/\[Admin chap nhan boi hoan luc:/i', $dh['GhiChu'] ?? '') === 1;
    $isRefundRejected = preg_match('/\[Admin tu choi boi hoan luc:/i', $dh['GhiChu'] ?? '') === 1;
    $canRequestRefund = (
        $statusDH === 'hoan_thanh' &&
        $soNgayHoanThanh <= 7 &&
        !$isRefundRequested &&
        !$isRefundApproved &&
        !$isRefundRejected
    );
    $refundExpired = (
        $statusDH === 'hoan_thanh' &&
        $soNgayHoanThanh > 7 &&
        !$isRefundRequested &&
        !$isRefundApproved &&
        !$isRefundRejected
    );
    list($steps, $current) = buildTimeline($dh['TrangThai']);

    /* ---- Lấy chi tiết sản phẩm ---- */
    $res_ct = mysqli_query($conn, "
        SELECT  ct.SoLuong,
                ct.GiaBanLucMua,
                sp.TenSP,
                sp.AnhDaiDien,
                bt.KichCo,
                bt.MauSac
        FROM    chitietdonhang ct
        JOIN    bienthesp bt ON ct.MaBienThe = bt.MaBienThe
        JOIN    sanpham   sp ON bt.MaSP      = sp.MaSP
        WHERE   ct.MaDH = $maDH_detail
    ");

    $items  = [];
    $tongSP = 0;
    while ($row = mysqli_fetch_assoc($res_ct)) {
        $items[]  = $row;
        $tongSP  += $row['SoLuong'];
    }

    $pct = 0;
    if ($current === 1) $pct = 50;
    if ($current === 2) $pct = 100;
    ?>

    <!-- Header -->
    <div class="mhd">
      <h5>📦 Chi tiết đơn #<?= $maDH_detail ?></h5>
      <button class="btn-x" onclick="closeModal()">✕</button>
    </div>

    <div class="mbody">

      <!-- INFO GRID -->
      <div class="info-grid">
        <div class="info-item">
          <label>📅 Ngày đặt hàng</label>
          <span><?= date('d/m/Y H:i', strtotime($dh['NgayDat'])) ?></span>
        </div>
        <div class="info-item">
          <label>📋 Trạng thái</label>
          <span>
            <span class="badge-status <?= $svDetail['class'] ?>">
              <?= $svDetail['label'] ?>
            </span>
          </span>
        </div>
        <?php if (!empty($dh['HoTen'])): ?>
        <div class="info-item">
          <label>👤 Người đặt</label>
          <span><?= htmlspecialchars($dh['HoTen']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($dh['DienThoai'])): ?>
        <div class="info-item">
          <label>📞 Số điện thoại</label>
          <span><?= htmlspecialchars($dh['DienThoai']) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-item" style="grid-column:1/-1">
          <label>📍 Địa chỉ giao hàng</label>
          <span><?= htmlspecialchars($dh['DiaChiGiaoHang']) ?></span>
        </div>
        <?php
        // Hiển thị GhiChu nhưng ẩn phần lý do hủy kỹ thuật
        $ghiChuHienThi = preg_replace('/\s*\[(Lý do hủy|Ly do huy):.*?\]/', '', $dh['GhiChu'] ?? '');
        $ghiChuHienThi = preg_replace('/\s*\[(Yeu cau huy|Yeu cau huy luc):.*?\]/i', '', $ghiChuHienThi);
        $ghiChuHienThi = preg_replace('/\s*\[(Yeu cau boi hoan|Yeu cau boi hoan luc):.*?\]/i', '', $ghiChuHienThi);
        $ghiChuHienThi = preg_replace('/\s*\[Hoan thanh luc:.*?\]/i', '', $ghiChuHienThi);
        $ghiChuHienThi = preg_replace('/\s*\[(Admin chap nhan boi hoan luc|Admin tu choi boi hoan luc):.*?\]/i', '', $ghiChuHienThi);
        if (trim($ghiChuHienThi) !== ''):
        ?>
        <div class="info-item" style="grid-column:1/-1">
          <label>📝 Ghi chú</label>
          <span><?= nl2br(htmlspecialchars(trim($ghiChuHienThi))) ?></span>
        </div>
        <?php endif; ?>
      </div>

      <!-- TRACKING hoặc HỦY BANNER -->
      <?php if ($isCancelled):
          preg_match('/\[(Lý do hủy|Ly do huy): (.+?)\]/', $dh['GhiChu'] ?? '', $m);
      ?>
      <div class="cancel-banner">
        <span style="font-size:24px">❌</span>
        <div>
          <strong>Đơn hàng đã bị hủy</strong>
          <?php if (!empty($m[2])): ?>
          <div style="font-size:13px;margin-top:3px;opacity:.9">
            Lý do: <?= htmlspecialchars($m[2]) ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php else: ?>

      <div class="tl-title">🚚 Theo dõi đơn hàng</div>
      <div class="tl-wrap">
        <div class="tl-prog" style="width:<?= $pct ?>%"></div>
        <?php foreach ($steps as $i => $s):
            $cls = '';
            if ($i < $current)   $cls = 'done';
            if ($i === $current) $cls = 'done current';
        ?>
        <div class="tl-step <?= $cls ?>">
          <div class="tl-dot"><?= $s['icon'] ?></div>
          <div class="tl-lbl"><?= $s['label'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if ($current >= 0): ?>
      <div class="tl-desc">💬 <?= $steps[$current]['desc'] ?></div>
      <?php endif; ?>

      <?php endif; ?>

      <?php if ($isCancelRequested): ?>
      <?php preg_match('/\[Yeu cau huy: (.+?)\]/i', $dh['GhiChu'] ?? '', $rc); ?>
      <div class="info-item" style="grid-column:1/-1;margin-bottom:16px">
        <label>YEU CAU HUY DON</label>
        <span>
          Yeu cau huy don da duoc gui cho admin.
          <?php if (!empty($rc[1])): ?> Ly do: <?= htmlspecialchars($rc[1]) ?>.<?php endif; ?>
        </span>
      </div>
      <?php endif; ?>

      <?php if ($isRefundRequested): ?>
      <?php preg_match('/\[Yeu cau boi hoan: (.+?)\]/i', $dh['GhiChu'] ?? '', $rb); ?>
      <div class="refund-banner">
        <span style="font-size:24px">↩️</span>
        <div>
          <strong>Yeu cau boi hoan da duoc gui</strong>
          <?php if (!empty($rb[1])): ?>
          <div style="font-size:13px;margin-top:3px;opacity:.95">Ly do: <?= htmlspecialchars($rb[1]) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <?php elseif ($isRefundApproved): ?>
      <div class="info-item" style="grid-column:1/-1;margin-bottom:16px;background:#eafaf1;border:1px solid #cfead9">
        <label>BOI HOAN</label>
        <span style="color:#0f5132">Don hang da duoc boi hoan.</span>
      </div>
      <?php elseif ($isRefundRejected): ?>
      <div class="info-item" style="grid-column:1/-1;margin-bottom:16px;background:#fff5f5;border:1px solid #ffd6d6">
        <label>BOI HOAN</label>
        <span style="color:#842029">Yeu cau boi hoan khong du dieu kien hoac da bi tu choi.</span>
      </div>
      <?php elseif ($refundExpired): ?>
      <div class="info-item" style="grid-column:1/-1;margin-bottom:16px">
        <label>BOI HOAN</label>
        <span>Don hang da qua 7 ngay nen khong con trong thoi gian boi hoan.</span>
      </div>
      <?php endif; ?>

      <!-- DANH SÁCH SẢN PHẨM -->
      <div class="sec-title">
        🛍️ Sản phẩm
        <span style="background:#e9ecef;border-radius:20px;padding:2px 10px;font-size:12px">
          <?= $tongSP ?> sản phẩm
        </span>
      </div>

      <?php if (empty($items)): ?>
        <p class="text-muted" style="font-size:13px;padding:10px">
          Không có sản phẩm nào trong đơn hàng này.
        </p>
      <?php endif; ?>

      <?php foreach ($items as $item):
          $imgs      = buildImageList($item['AnhDaiDien']);
          $imgSrc    = !empty($imgs) ? $imgs[0] : 'https://placehold.co/78x78?text=No+Img';
          $thanhTien = $item['SoLuong'] * $item['GiaBanLucMua'];
      ?>
      <div class="product-row">
        <img src="<?= $imgSrc ?>" class="main-img"
             alt="<?= htmlspecialchars($item['TenSP']) ?>"
             onerror="this.src='https://placehold.co/78x78?text=No+Img'">
        <div style="flex:1">
          <div class="pname"><?= htmlspecialchars($item['TenSP']) ?></div>
          <div class="pmeta">
            Phân loại: <?= htmlspecialchars($item['KichCo']) ?>
            &nbsp;–&nbsp; <?= htmlspecialchars($item['MauSac']) ?>
          </div>
          <div class="pmeta">Số lượng: <strong><?= $item['SoLuong'] ?></strong></div>
          <div class="pprice">
            <?= number_format($item['GiaBanLucMua']) ?>đ
            <?php if ($item['SoLuong'] > 1): ?>
              <span style="color:#999;font-size:12px;font-weight:400">
                × <?= $item['SoLuong'] ?> = <?= number_format($thanhTien) ?>đ
              </span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

    </div><!-- end mbody -->

    <!-- FOOTER -->
<div class="mfoot">

    <div>
        <div class="total-lbl">TỔNG THANH TOÁN</div>
        <div class="total-amt">
            <?= number_format($dh['TongTien']) ?>đ
        </div>
    </div>

    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">

        <!-- =========================
             CHỜ XÁC NHẬN
        ========================== -->
        <?php if ($statusDH === 'cho_xac_nhan'): ?>

            <?php if (!$isCancelRequested): ?>

                <button class="btn-huy"
                        onclick="openCancelModal(<?= $maDH_detail ?>)">
                    ❌ Hủy đơn hàng
                </button>

            <?php else: ?>

                <button class="btn btn-secondary btn-sm rounded-pill px-3"
                        disabled>
                    Đã gửi yêu cầu hủy
                </button>

            <?php endif; ?>

        <?php endif; ?>


        <!-- =========================
             ĐANG GIAO
        ========================== -->
        <?php if ($statusDH === 'dang_giao'): ?>

            <!-- Đã nhận hàng -->
            <a href="?action=nhan&id=<?= $maDH_detail ?>"
               class="btn-nhan">
                ✅ Đã nhận được hàng
            </a>

            <!-- Hủy đơn -->
            <?php if (!$isCancelRequested): ?>

                <button class="btn-huy"
                        onclick="openCancelModal(<?= $maDH_detail ?>)">
                    ❌ Hủy đơn hàng
                </button>

            <?php else: ?>

                <button class="btn btn-secondary btn-sm rounded-pill px-3"
                        disabled>
                    Đã gửi yêu cầu hủy
                </button>

            <?php endif; ?>

        <?php endif; ?>


        <!-- =========================
             HOÀN THÀNH
        ========================== -->
        <?php if ($canRequestRefund): ?>

            <button class="btn-boihoan"
                    onclick="openRefundModal(<?= $maDH_detail ?>)">
                ↩️ Yêu cầu bồi hoàn
            </button>

        <?php endif; ?>


        <!-- =========================
             ĐÓNG
        ========================== -->
        <button onclick="closeModal()"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            Đóng
        </button>

    </div>

</div>
    </div>

    <?php endif; // end $dh
endif; // end $maDH_detail ?>

</div><!-- modal-box -->
</div><!-- overlay -->

</div><!-- container -->

<!-- ===== MODAL XÁC NHẬN HỦY ===== -->
<div id="cmWrap" class="cm-wrap" onclick="this.style.display='none'">
<div class="cm-box" onclick="event.stopPropagation()">
  <h6>❌ Xác nhận hủy đơn hàng</h6>
  <p style="font-size:13px;color:#666;margin-bottom:12px">
    Chọn lý do hủy để giúp shop cải thiện dịch vụ:
  </p>
  <ul class="reason-list" id="reasonList">
    <li onclick="pickReason(this)">Tôi muốn thay đổi địa chỉ giao hàng</li>
    <li onclick="pickReason(this)">Tôi muốn thay đổi sản phẩm hoặc số lượng</li>
    <li onclick="pickReason(this)">Tôi tìm được giá tốt hơn ở nơi khác</li>
    <li onclick="pickReason(this)">Tôi không còn nhu cầu mua nữa</li>
    <li onclick="pickReason(this)">Đặt nhầm sản phẩm</li>
    <li onclick="pickReason(this)">Lý do khác</li>
  </ul>
  <div class="cm-btns">
    <button class="cm-back" onclick="document.getElementById('cmWrap').style.display='none'">
      Quay lại
    </button>
    <button class="cm-confirm" onclick="doCancel()">Xác nhận hủy</button>
  </div>
</div>
</div>

<!-- ===== MODAL YEU CAU BOI HOAN ===== -->
<div id="rmWrap" class="cm-wrap" onclick="this.style.display='none'">
<div class="cm-box" onclick="event.stopPropagation()">
  <h6>↩️ Yeu cau boi hoan</h6>
  <p style="font-size:13px;color:#666;margin-bottom:12px">
    Don hoan thanh chi duoc gui yeu cau boi hoan trong 7 ngay.
  </p>
  <ul class="reason-list" id="refundReasonList">
    <li onclick="pickRefundReason(this)">San pham bi loi</li>
    <li onclick="pickRefundReason(this)">San pham khong dung nhu mo ta</li>
    <li onclick="pickRefundReason(this)">Khach hang khong con nhu cau</li>
    <li onclick="pickRefundReason(this)">Ly do khac</li>
  </ul>
  <div class="cm-btns">
    <button class="cm-back" onclick="document.getElementById('rmWrap').style.display='none'">
      Quay lai
    </button>
    <button class="cm-confirm" onclick="doRefund()">Gui yeu cau</button>
  </div>
</div>
</div>

<script>
var _cid    = 0;
var _reason = '';
var _rid    = 0;
var _rreason = '';

<?php if ($maDH_detail > 0): ?>
window.addEventListener('load', function () {
    document.getElementById('overlay').style.display = 'flex';
});
<?php endif; ?>

function closeModal() {
    document.getElementById('overlay').style.display = 'none';
    history.replaceState(null, '', 'Don_Hang.php');
}
function handleBg(e) {
    if (e.target === document.getElementById('overlay')) closeModal();
}

function openCancelModal(id) {
    _cid    = id;
    _reason = '';
    document.querySelectorAll('#reasonList li').forEach(function(li){
        li.classList.remove('selected');
    });
    document.getElementById('cmWrap').style.display = 'flex';
}
function pickReason(el) {
    document.querySelectorAll('#reasonList li').forEach(function(li){
        li.classList.remove('selected');
    });
    el.classList.add('selected');
    _reason = el.textContent.trim();
}
function doCancel() {
    if (!_reason) { alert('Vui lòng chọn lý do hủy đơn!'); return; }
    if (confirm('Bạn chắc chắn muốn hủy đơn #' + _cid + '?')) {
        window.location.href = 'Don_Hang.php?action=huy&id=' + _cid
            + '&lydo=' + encodeURIComponent(_reason);
    }
}
function openRefundModal(id) {
    _rid = id;
    _rreason = '';
    document.querySelectorAll('#refundReasonList li').forEach(function(li){
        li.classList.remove('selected');
    });
    document.getElementById('rmWrap').style.display = 'flex';
}
function pickRefundReason(el) {
    document.querySelectorAll('#refundReasonList li').forEach(function(li){
        li.classList.remove('selected');
    });
    el.classList.add('selected');
    _rreason = el.textContent.trim();
}
function doRefund() {
    if (!_rreason) { alert('Vui long chon ly do boi hoan!'); return; }
    if (confirm('Gui yeu cau boi hoan don #' + _rid + '?')) {
        window.location.href = 'Don_Hang.php?action=boihoan&id=' + _rid
            + '&lydo=' + encodeURIComponent(_rreason);
    }
}
function doCancel() {
    if (!_reason) { alert('Vui long chon ly do huy don!'); return; }
    if (confirm('Gui yeu cau huy don #' + _cid + '?')) {
        window.location.href = 'Don_Hang.php?action=huy&id=' + _cid
            + '&lydo=' + encodeURIComponent(_reason);
    }
}
</script>

<?php
$content = ob_get_clean();
include "Trang_Chu_Includes/Layout.php";
?>

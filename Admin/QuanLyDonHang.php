<?php
ob_start();
include 'check_admin.php';
include '../Trang_Chu_Includes/connect_db.inc';

/* =====================================
   HÀM XỬ LÝ ẢNH (FIX FULL)
===================================== */
function getImage($path) {
    $project = '/Bai_Thi_Cuoi_Ki/';
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . $project . 'uploads/';
    $uploadURL = $project . 'uploads/';

    if (!empty($path)) {

        // 1. Nếu có dấu , → dữ liệu chuẩn
        if (strpos($path, ',') !== false) {
            $images = explode(',', $path);
        } 
        // 2. Nếu không → xử lý ảnh bị dính (images/)
        else {
            $images = preg_split('/(?=images\\/)/', $path);
        }

        // Lấy ảnh đầu tiên
        $firstImage = trim($images[0]);

        // 3. Kiểm tra ảnh trong thư mục images
        $sqlPath = $_SERVER['DOCUMENT_ROOT'] . $project . $firstImage;
        if (file_exists($sqlPath)) {
            return $project . $firstImage;
        }

        // 4. Thử lấy trong uploads
        $fileName = basename($firstImage);
        if (file_exists($uploadDir . $fileName)) {
            return $uploadURL . $fileName;
        }
    }

    // 5. Ảnh mặc định
    return $uploadURL . 'no-image.png';
}

function isPendingOrderStatus($status) {
    $s = strtolower(trim((string)$status));
    return (strpos($s, 'xac') !== false && strpos($s, 'nhan') !== false)
        || (strpos($s, 'xu') !== false && strpos($s, 'ly') !== false);
}

function adminStatusView($trangThai, $ghiChu = '') {
    $ghiChu = (string)$ghiChu;
    $trangThai = (string)$trangThai;

    if (preg_match('/\[Admin chap nhan boi hoan luc:/i', $ghiChu)) {
        return ['label' => 'Da boi hoan', 'class' => 'bg-success'];
    }
    if (preg_match('/\[Admin tu choi boi hoan luc:/i', $ghiChu)) {
        return ['label' => 'Tu choi boi hoan', 'class' => 'bg-danger'];
    }

    $s = strtolower($trangThai);
    if ((strpos($s, 'xac') !== false && strpos($s, 'nhan') !== false) || (strpos($s, 'xu') !== false && strpos($s, 'ly') !== false)) {
        return ['label' => 'Cho xac nhan', 'class' => 'bg-warning text-dark'];
    }
    if (strpos($s, 'giao') !== false) return ['label' => 'Dang giao', 'class' => 'bg-primary'];
    if ((strpos($s, 'hoan') !== false || strpos($s, 'hoÃ') !== false) && (strpos($s, 'thanh') !== false || strpos($s, 'thÃ') !== false)) {
        return ['label' => 'Hoan thanh', 'class' => 'bg-success'];
    }
    if (strpos($s, 'huy') !== false || strpos($s, 'há»§y') !== false) return ['label' => 'Da huy', 'class' => 'bg-danger'];

    return ['label' => $trangThai, 'class' => 'bg-secondary'];
}

/* =====================================
   UPDATE TRẠNG THÁI
===================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $maDH = (int)$_POST['MaDH'];
    $trangThaiMoi = $_POST['TrangThai'];

    $check = $conn->prepare("SELECT GhiChu, TrangThai FROM donhang WHERE MaDH = ? LIMIT 1");
    $check->bind_param("i", $maDH);
    $check->execute();
    $old = $check->get_result()->fetch_assoc();
    $check->close();

    $ghiChuMoi = $old['GhiChu'] ?? '';
    $trangThaiMoiLower = strtolower((string)$trangThaiMoi);
    $laHoanThanh = (strpos($trangThaiMoiLower, 'hoan') !== false || strpos($trangThaiMoiLower, 'hoÃ n') !== false)
        && (strpos($trangThaiMoiLower, 'thanh') !== false || strpos($trangThaiMoiLower, 'thÃ nh') !== false);
    $daCoMocHoanThanh = preg_match('/\[Hoan thanh luc: [^\]]+\]/i', $ghiChuMoi) === 1;

    if ($laHoanThanh && !$daCoMocHoanThanh) {
        $ghiChuMoi = trim($ghiChuMoi . ' [Hoan thanh luc: ' . date('Y-m-d H:i:s') . ']');
    }

    $stmt = $conn->prepare("UPDATE donhang SET TrangThai = ?, GhiChu = ? WHERE MaDH = ?");
    $stmt->bind_param("ssi", $trangThaiMoi, $ghiChuMoi, $maDH);
    $stmt->execute();
    $stmt->close();

    header("Location: QuanLyDonHang.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_request'])) {
    $maDH = (int)($_POST['MaDH'] ?? 0);
    $requestType = $_POST['request_type'] ?? '';
    $decision = $_POST['decision'] ?? '';

    $q = $conn->prepare("SELECT TrangThai, GhiChu FROM donhang WHERE MaDH = ? LIMIT 1");
    $q->bind_param("i", $maDH);
    $q->execute();
    $don = $q->get_result()->fetch_assoc();
    $q->close();

    if ($don) {
        $trangThai = $don['TrangThai'] ?? '';
        $ghiChu = $don['GhiChu'] ?? '';
        $trangThaiMoi = $trangThai;

        if ($requestType === 'cancel') {
            $coYeuCau = preg_match('/\[(Yeu cau huy|Yeu cau huy luc):/i', $ghiChu) === 1;
            if ($coYeuCau) {
                if ($decision === 'approve' && isPendingOrderStatus($trangThai)) {
                    $trangThaiMoi = 'Da huy';
                    $ghiChu .= ' [Admin chap nhan huy luc: ' . date('Y-m-d H:i:s') . ']';
                } else {
                    $ghiChu .= ' [Admin tu choi huy luc: ' . date('Y-m-d H:i:s') . ']';
                }
                $ghiChu = preg_replace('/\s*\[(Yeu cau huy|Yeu cau huy luc):.*?\]/i', '', $ghiChu);
            }
        }

        if ($requestType === 'refund') {
            $coYeuCau = preg_match('/\[(Yeu cau boi hoan|Yeu cau boi hoan luc):/i', $ghiChu) === 1;
            if ($coYeuCau) {
                if ($decision === 'approve') {
                    $ghiChu .= ' [Admin chap nhan boi hoan luc: ' . date('Y-m-d H:i:s') . ']';
                } else {
                    $ghiChu .= ' [Admin tu choi boi hoan luc: ' . date('Y-m-d H:i:s') . ']';
                }
                $ghiChu = preg_replace('/\s*\[(Yeu cau boi hoan|Yeu cau boi hoan luc):.*?\]/i', '', $ghiChu);
            }
        }

        $ghiChu = trim(preg_replace('/\s+/', ' ', $ghiChu));

        $u = $conn->prepare("UPDATE donhang SET TrangThai = ?, GhiChu = ? WHERE MaDH = ?");
        $u->bind_param("ssi", $trangThaiMoi, $ghiChu, $maDH);
        $u->execute();
        $u->close();
    }

    header("Location: QuanLyDonHang.php");
    exit();
}

/* =====================================
   TRUY VẤN
===================================== */
$viewID = isset($_GET['view']) ? (int)$_GET['view'] : 0;

$sql_all = "SELECT dh.*, nd.HoTen 
            FROM donhang dh 
            JOIN nguoidung nd ON dh.MaND = nd.MaND 
            ORDER BY dh.MaDH DESC";
$res_all = mysqli_query($conn, $sql_all);
?>

<div class="container-fluid px-4">
    <h2 class="mt-4 mb-4 fw-bold text-uppercase">Hệ thống quản lý đơn hàng</h2>

    <?php if ($viewID > 0): ?>
        <?php
        $sql_dh = "SELECT dh.*, nd.HoTen, nd.DienThoai, nd.Email 
                   FROM donhang dh 
                   JOIN nguoidung nd ON dh.MaND = nd.MaND 
                   WHERE dh.MaDH = $viewID";
        $res_dh = mysqli_query($conn, $sql_dh);
        $donhang = mysqli_fetch_assoc($res_dh);

        if ($donhang):
            $statusDetail = adminStatusView($donhang['TrangThai'], $donhang['GhiChu'] ?? '');
            $sql_ct = "SELECT ct.*, sp.TenSP, sp.AnhDaiDien, bt.MauSac, bt.KichCo 
                       FROM chitietdonhang ct
                       JOIN bienthesp bt ON ct.MaBienThe = bt.MaBienThe
                       JOIN sanpham sp ON bt.MaSP = sp.MaSP
                       WHERE ct.MaDH = $viewID";
            $res_ct = mysqli_query($conn, $sql_ct);
        ?>

        <div class="card shadow mb-5 border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <h5 class="mb-0">CHI TIẾT ĐƠN HÀNG #<?= $viewID ?></h5>
                <a href="QuanLyDonHang.php" class="btn btn-light btn-sm">Đóng lại ×</a>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Khách hàng:</strong> <?= htmlspecialchars($donhang['HoTen']) ?></p>
                        <p><strong>Điện thoại:</strong> <?= htmlspecialchars($donhang['DienThoai']) ?></p>
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($donhang['DiaChiGiaoHang']) ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($donhang['NgayDat'])) ?></p>
                        <p><strong>Trạng thái:</strong> 
                            <span class="badge <?= $statusDetail['class'] ?>"><?= htmlspecialchars($statusDetail['label']) ?></span>
                        </p>
                    </div>
                </div>

                <table class="table table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($item = mysqli_fetch_assoc($res_ct)): ?>
                        <tr>
                            <td>
                                <img src="<?= getImage($item['AnhDaiDien']) ?>" width="50" class="me-2">
                                <strong><?= htmlspecialchars($item['TenSP']) ?></strong> 
                                (<?= htmlspecialchars($item['MauSac']) ?>/<?= htmlspecialchars($item['KichCo']) ?>)
                            </td>

                            <td class="text-center"><?= $item['SoLuong'] ?></td>
                            <td class="text-end"><?= number_format($item['GiaBanLucMua'], 0, ',', '.') ?> ₫</td>
                            <td class="text-end fw-bold">
                                <?= number_format($item['SoLuong'] * $item['GiaBanLucMua'], 0, ',', '.') ?> ₫
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>

                    <tfoot class="fw-bold bg-light">
                        <tr>
                            <td colspan="3" class="text-end">TỔNG CỘNG:</td>
                            <td class="text-end text-danger fs-5">
                                <?= number_format($donhang['TongTien'], 0, ',', '.') ?> ₫
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php endif; ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white fw-bold">Danh sách đơn đặt hàng</div>

        <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th class="text-start">Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Yeu cau KH</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = mysqli_fetch_assoc($res_all)): ?>
                    <?php 
                        $status_class = 'bg-secondary';
                        $coYeuCauHuy = preg_match('/\[(Yeu cau huy|Yeu cau huy luc):/i', $row['GhiChu'] ?? '') === 1;
                        $coYeuCauBoiHoan = preg_match('/\[(Yeu cau boi hoan|Yeu cau boi hoan luc):/i', $row['GhiChu'] ?? '') === 1;
                        $lyDoHuy = '';
                        $lyDoBoiHoan = '';
                        if (preg_match('/\[Yeu cau huy: (.+?)\]/i', $row['GhiChu'] ?? '', $mh)) $lyDoHuy = $mh[1];
                        if (preg_match('/\[Yeu cau boi hoan: (.+?)\]/i', $row['GhiChu'] ?? '', $mr)) $lyDoBoiHoan = $mr[1];
                        $statusView = adminStatusView($row['TrangThai'], $row['GhiChu'] ?? '');
                        if($row['TrangThai'] == 'Chờ xác nhận') $status_class = 'bg-warning text-dark';
                        if($row['TrangThai'] == 'Đang giao') $status_class = 'bg-primary';
                        if($row['TrangThai'] == 'Hoàn thành') $status_class = 'bg-success';
                        if($row['TrangThai'] == 'Đã hủy') $status_class = 'bg-danger';
                    ?>
                    <tr>
                        <td>#<?= $row['MaDH'] ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['HoTen']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['NgayDat'])) ?></td>
                        <td class="text-danger fw-bold"><?= number_format($row['TongTien'], 0, ',', '.') ?> ₫</td>

                        <td>
                            <div class="mb-1">
                                <span class="badge <?= $statusView['class'] ?>"><?= htmlspecialchars($statusView['label']) ?></span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="MaDH" value="<?= $row['MaDH'] ?>">
                                <input type="hidden" name="update_status" value="1">

                                <select name="TrangThai"
                                    class="form-select form-select-sm fw-bold <?= $status_class ?> text-white"
                                    onchange="this.form.submit()">
                                    <option value="Chờ xác nhận" <?= $row['TrangThai']=='Chờ xác nhận'?'selected':'' ?>>Chờ xác nhận</option>
                                    <option value="Đang giao" <?= $row['TrangThai']=='Đang giao'?'selected':'' ?>>Đang giao</option>
                                    <option value="Hoàn thành" <?= $row['TrangThai']=='Hoàn thành'?'selected':'' ?>>Hoàn thành</option>
                                    <option value="Đã hủy" <?= $row['TrangThai']=='Đã hủy'?'selected':'' ?>>Đã hủy</option>
                                </select>
                            </form>
                        </td>

                        <td class="text-start" style="min-width:220px">
                            <?php if ($coYeuCauHuy): ?>
                                <div class="badge bg-danger mb-1">Cho duyet huy don</div>
                                <div class="small text-muted">Ly do: <?= htmlspecialchars($lyDoHuy ?: 'Khong ro') ?></div>
                            <?php endif; ?>
                            <?php if ($coYeuCauBoiHoan): ?>
                                <div class="badge bg-warning text-dark mb-1">Cho duyet boi hoan</div>
                                <div class="small text-muted">Ly do: <?= htmlspecialchars($lyDoBoiHoan ?: 'Khong ro') ?></div>
                            <?php endif; ?>
                            <?php if (!$coYeuCauHuy && !$coYeuCauBoiHoan): ?>
                                <span class="text-muted small">Khong co</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="?view=<?= $row['MaDH'] ?>" class="btn btn-sm btn-info text-white">
                                Xem chi tiết
                            </a>
                            <?php if ($coYeuCauHuy): ?>
                                <form method="POST" class="d-inline-block ms-1">
                                    <input type="hidden" name="process_request" value="1">
                                    <input type="hidden" name="MaDH" value="<?= $row['MaDH'] ?>">
                                    <input type="hidden" name="request_type" value="cancel">
                                    <input type="hidden" name="decision" value="approve">
                                    <button class="btn btn-sm btn-danger">Duyet huy</button>
                                </form>
                                <form method="POST" class="d-inline-block ms-1">
                                    <input type="hidden" name="process_request" value="1">
                                    <input type="hidden" name="MaDH" value="<?= $row['MaDH'] ?>">
                                    <input type="hidden" name="request_type" value="cancel">
                                    <input type="hidden" name="decision" value="reject">
                                    <button class="btn btn-sm btn-outline-danger">Tu choi</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($coYeuCauBoiHoan): ?>
                                <form method="POST" class="d-inline-block ms-1 mt-1">
                                    <input type="hidden" name="process_request" value="1">
                                    <input type="hidden" name="MaDH" value="<?= $row['MaDH'] ?>">
                                    <input type="hidden" name="request_type" value="refund">
                                    <input type="hidden" name="decision" value="approve">
                                    <button class="btn btn-sm btn-warning">Duyet boi hoan</button>
                                </form>
                                <form method="POST" class="d-inline-block ms-1 mt-1">
                                    <input type="hidden" name="process_request" value="1">
                                    <input type="hidden" name="MaDH" value="<?= $row['MaDH'] ?>">
                                    <input type="hidden" name="request_type" value="refund">
                                    <input type="hidden" name="decision" value="reject">
                                    <button class="btn btn-sm btn-outline-warning">Tu choi</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>

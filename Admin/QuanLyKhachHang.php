<?php
ob_start();
include 'check_admin.php';
include '../Trang_Chu_Includes/connect_db.inc';

/* ======================
   TÌM KIẾM + PHÂN TRANG
====================== */
$keyword = trim($_GET['keyword'] ?? '');
$param = "%$keyword%";

$limit = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

/* ======================
   DANH SÁCH KHÁCH HÀNG (Sửa MaKH -> MaND)
===================== */
$sql = "
    SELECT *
    FROM nguoidung
    WHERE HoTen LIKE ? OR Email LIKE ? OR DienThoai LIKE ?
    ORDER BY MaND DESC
    LIMIT ?, ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $param, $param, $param, $offset, $limit);
$stmt->execute();
$listKH = $stmt->get_result();

/* ======================
   ĐẾM KHÁCH HÀNG
====================== */
$countSql = "
    SELECT COUNT(*) AS total
    FROM nguoidung
    WHERE HoTen LIKE ? OR Email LIKE ? OR DienThoai LIKE ?
";
$cstmt = $conn->prepare($countSql);
$cstmt->bind_param("sss", $param, $param, $param);
$cstmt->execute();
$totalKH = $cstmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalKH / $limit);

/* ======================
   BIỂU ĐỒ DOANH THU (Sửa logic TrangThai)
====================== */
// Lấy doanh thu từ những đơn hàng có trạng thái là 'Hoàn thành'
$chartDT = mysqli_query($conn, "
    SELECT MONTH(dh.NgayDat) AS thang,
           SUM(dh.TongTien) AS doanhthu
    FROM donhang dh
    WHERE dh.TrangThai = 'Hoàn thành'
    GROUP BY MONTH(dh.NgayDat)
");

$dt_labels = [];
$dt_data = [];
$totalRevenue = 0;
while ($r = mysqli_fetch_assoc($chartDT)) {
    $dt_labels[] = 'Tháng ' . $r['thang'];
    $dt_data[] = (int)$r['doanhthu'];
    $totalRevenue += $r['doanhthu'];
}

/* ======================
   TOP KHÁCH HÀNG (Dựa trên tổng tiền đơn hàng)
====================== */
$topKH = mysqli_query($conn, "
    SELECT nd.HoTen, nd.Email,
           SUM(dh.TongTien) AS tongtien
    FROM nguoidung nd
    JOIN donhang dh ON nd.MaND = dh.MaND
    WHERE dh.TrangThai = 'Hoàn thành'
    GROUP BY nd.MaND
    ORDER BY tongtien DESC
    LIMIT 5
");
?>

<div class="container-fluid px-4 py-4">
    <h3 class="fw-bold mb-4 text-uppercase text-primary"><i class="bi bi-speedometer2"></i> Dashboard Khách hàng & Doanh thu</h3>

    <div class="row g-3 mb-4 text-center">
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 bg-primary text-white p-3">
                <div class="small text-uppercase opacity-75">Tổng khách hàng</div>
                <h2 class="fw-bold mb-0"><?= number_format($totalKH) ?></h2>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 bg-success text-white p-3">
                <div class="small text-uppercase opacity-75">Doanh thu hoàn tất</div>
                <h2 class="fw-bold mb-0"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</h2>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 p-3">
        <form class="row g-2">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0"
                           placeholder="Tìm tên, email, điện thoại..."
                           value="<?= htmlspecialchars($keyword) ?>">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary px-4">Tìm kiếm</button>
            </div>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold border-0 pt-3">
                    <i class="bi bi-graph-up text-success me-2"></i>BIỂU ĐỒ DOANH THU THEO THÁNG (VNĐ)
                </div>
                <div class="card-body">
                    <canvas id="dtChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-warning text-dark fw-bold border-0 pt-3">
                    <i class="bi bi-star-fill me-2"></i>TOP 5 KHÁCH HÀNG CHI TIÊU
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <tbody class="small">
                        <?php while ($row = mysqli_fetch_assoc($topKH)): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['HoTen']) ?></div>
                                    <div class="text-muted"><?= htmlspecialchars($row['Email']) ?></div>
                                </td>
                                <td class="text-end text-danger fw-bold">
                                    <?= number_format($row['tongtien'],0,',','.') ?>đ
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white fw-bold border-0 pt-3">
                    <i class="bi bi-people-fill me-2"></i>DANH SÁCH NGƯỜI DÙNG MỚI
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 small">
                        <thead>
                            <tr class="table-light">
                                <th>Họ tên</th><th>Email</th><th>Điện thoại</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($kh = $listKH->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($kh['HoTen']) ?></td>
                            <td><?= htmlspecialchars($kh['Email']) ?></td>
                            <td><?= htmlspecialchars($kh['DienThoai']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('dtChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($dt_labels) ?>,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= json_encode($dt_data) ?>,
            borderColor: '#198754',
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#198754'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>

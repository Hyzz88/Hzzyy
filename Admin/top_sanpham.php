<?php
ob_start();
include 'check_admin.php';
include '../Trang_Chu_Includes/connect_db.inc';

/* =====================================
   TOP 5 SẢN PHẨM BÁN CHẠY (SỬA THEO SQL CỦA BẠN)
===================================== */
// Sửa bảng: bienthe -> bienthesp
// Xử lý TrangThai: 'Hoàn thành' (Bạn hãy kiểm tra trong DB xem là 'Hoàn thành' hay 'Đã giao')
$sql = "
    SELECT 
        sp.MaSP,
        sp.TenSP,
        sp.AnhDaiDien,
        SUM(ct.SoLuong) AS TongSoLuong,
        SUM(ct.SoLuong * ct.GiaBanLucMua) AS DoanhThu
    FROM chitietdonhang ct
    JOIN donhang dh ON ct.MaDH = dh.MaDH
    JOIN bienthesp bt ON ct.MaBienThe = bt.MaBienThe
    JOIN sanpham sp ON bt.MaSP = sp.MaSP
    WHERE dh.TrangThai = 'Hoàn thành' 
    GROUP BY sp.MaSP
    ORDER BY TongSoLuong DESC
    LIMIT 5
";

$result = mysqli_query($conn, $sql);

$labels = [];
$data   = [];
$rows   = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['TenSP'];
        $data[]   = (int)$row['TongSoLuong'];
        $rows[]   = $row;
    }
}
?>

<h2 class="fw-bold mb-4">🏆 Top sản phẩm bán chạy nhất</h2>

<div class="row g-4">

    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header fw-bold bg-dark text-white py-3">
                <i class="bi bi-list-stars"></i> Danh sách Top 5 sản phẩm
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th width="80">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th class="text-center">Đã bán</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $i => $r): ?>
                                <?php 
                                    // XỬ LÝ ẢNH: Tách lấy ảnh đầu tiên và sửa đường dẫn
                                    $raw_img = trim($r['AnhDaiDien']);
                                    $img_parts = preg_split('/\s+/', $raw_img);
                                    $first_img = !empty($img_parts[0]) ? $img_parts[0] : '';
                                    
                                    // Trong SQL của bạn đã có 'img/', nên chỉ cần thêm '../'
                                    $path = !empty($first_img) ? "../" . $first_img : "https://placehold.co/60x60?text=No+Image";
                                ?>
                                <tr>
                                    <td class="fw-bold"><?= $i + 1 ?></td>

                                    <td>
                                        <img src="<?= $path ?>" 
                                             width="60" height="60" 
                                             class="rounded shadow-sm border" 
                                             style="object-fit:cover"
                                             onerror="this.src='https://placehold.co/60x60?text=Error';">
                                    </td>

                                    <td class="fw-bold text-primary">
                                        <?= htmlspecialchars($r['TenSP']) ?>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-success px-3">
                                            <?= number_format($r['TongSoLuong']) ?>
                                        </span>
                                    </td>

                                    <td class="text-danger fw-bold text-end">
                                        <?= number_format($r['DoanhThu'], 0, ',', '.') ?> đ
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Chưa có dữ liệu bán hàng với trạng thái "Hoàn thành"
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header fw-bold bg-primary text-white py-3">
                <i class="bi bi-bar-chart-fill"></i> Biểu đồ sản lượng
            </div>
            <div class="card-body d-flex align-items-center">
                <canvas id="topProductChart"></canvas>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('topProductChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Số lượng sản phẩm đã bán',
            data: <?= json_encode($data) ?>,
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(250, 204, 21, 0.8)',
                'rgba(249, 115, 22, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderColor: [
                '#3b82f6', '#22c55e', '#facc15', '#f97316', '#ef4444'
            ],
            borderWidth: 1,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
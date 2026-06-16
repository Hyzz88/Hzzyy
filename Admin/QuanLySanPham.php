<?php
ob_start();
include 'check_admin.php';
include '../Trang_Chu_Includes/connect_db.inc';

/* =========================
   TÌM KIẾM
========================= */

$keyword = trim($_GET['keyword'] ?? '');
$param = "%$keyword%";

/* =========================
   LẤY SẢN PHẨM + TỔNG TỒN KHO
========================= */

$sql = "
SELECT 
    sp.MaSP,
    sp.TenSP,
    sp.GiaBan,
    sp.AnhDaiDien,
    dm.TenDM,

    /* TỔNG SỐ LƯỢNG */
    COALESCE(SUM(bt.SoLuongTon),0) AS TongSoLuong

FROM sanpham sp

LEFT JOIN danhmuc dm 
ON sp.MaDM = dm.MaDM

LEFT JOIN bienthesp bt
ON sp.MaSP = bt.MaSP

WHERE sp.TenSP LIKE ?

GROUP BY 
    sp.MaSP,
    sp.TenSP,
    sp.GiaBan,
    sp.AnhDaiDien,
    dm.TenDM

ORDER BY sp.MaSP DESC
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $param);

$stmt->execute();

$result = $stmt->get_result();

?>

<h2 class="mb-4 fw-bold">
    Quản lý Sản phẩm
</h2>

<div class="card shadow-sm border-0 rounded-4 mb-4">

    <div class="card-body d-flex justify-content-between flex-wrap gap-3">

        <form method="GET" class="d-flex gap-2">

            <input
                type="text"
                name="keyword"
                class="form-control"
                placeholder="Tìm sản phẩm..."
                value="<?= htmlspecialchars($keyword) ?>"
                style="min-width:260px;"
            >

            <button class="btn btn-dark px-4">
                Tìm
            </button>

        </form>

        <a href="sp_add.php" class="btn btn-success px-4">
            + Thêm mới
        </a>

    </div>

</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead class="table-dark">

<tr>

    <th>ID</th>

    <th>Ảnh</th>

    <th>Tên sản phẩm</th>

    <th>Giá bán</th>

    <th>Danh mục</th>

    <th>Số lượng</th>

    <th>Trạng thái</th>

    <th width="160">
        Thao tác
    </th>

</tr>

</thead>

<tbody>

<?php if ($result->num_rows > 0): ?>

<?php while ($row = $result->fetch_assoc()): ?>

<?php

/* =========================
   XỬ LÝ ẢNH
========================= */

$default_img = "https://placehold.co/70x70?text=No+Image";

$raw = trim($row['AnhDaiDien']);

$raw = str_replace('\\', '/', $raw);

$list = preg_split('/\r\n|\r|\n|\s+/', $raw);

$first = trim($list[0] ?? '');

$img_path = $default_img;

if (!empty($first)) {

    if (file_exists(__DIR__ . "/../" . $first)) {

        $img_path = "../" . $first;

    } else {

        $filename = basename($first);

        $upload_path = "../uploads/" . $filename;

        if (file_exists(__DIR__ . "/" . $upload_path)) {

            $img_path = $upload_path;

            mysqli_query($conn, "
                UPDATE sanpham 
                SET AnhDaiDien = 'uploads/$filename'
                WHERE MaSP = " . (int)$row['MaSP']
            );

        } else {

            $upload_dir = __DIR__ . "/../uploads/";

            if (is_dir($upload_dir)) {

                foreach (scandir($upload_dir) as $file) {

                    if ($file == "." || $file == "..") continue;

                    if (
                        stripos(
                            $file,
                            pathinfo($filename, PATHINFO_FILENAME)
                        ) !== false
                    ) {

                        $img_path = "../uploads/" . $file;

                        mysqli_query($conn, "
                            UPDATE sanpham 
                            SET AnhDaiDien = 'uploads/$file'
                            WHERE MaSP = " . (int)$row['MaSP']
                        );

                        break;
                    }
                }
            }
        }
    }
}

/* =========================
   TỒN KHO
========================= */

$stock = (int)$row['TongSoLuong'];

$status_text = 'Còn hàng';
$status_class = 'success';

if($stock <= 0){

    $status_text = 'Hết hàng';
    $status_class = 'danger';

}
elseif($stock <= 5){

    $status_text = 'Sắp hết';
    $status_class = 'warning';

}

?>

<tr>

    <td class="fw-bold">
        #<?= $row['MaSP'] ?>
    </td>

    <td>

        <img
            src="<?= htmlspecialchars($img_path) ?>"
            width="75"
            height="75"
            style="
                object-fit:cover;
                border-radius:12px;
                border:1px solid #eee;
            "
            onerror="this.src='https://placehold.co/70x70?text=Error';"
        >

    </td>

    <td>

        <div class="fw-semibold mb-1">
            <?= htmlspecialchars($row['TenSP']) ?>
        </div>

    </td>

    <td class="text-danger fw-bold">

        <?= number_format($row['GiaBan']) ?>đ

    </td>

    <td>

        <span class="badge bg-light text-dark border px-3 py-2">

            <?= htmlspecialchars($row['TenDM'] ?? 'Chưa có') ?>

        </span>

    </td>

    <!-- SỐ LƯỢNG -->

    <td>

        <span class="fw-bold fs-6">

            <?= $stock ?>

        </span>

    </td>

    <!-- TRẠNG THÁI -->

    <td>

        <span class="badge bg-<?= $status_class ?> px-3 py-2">

            <?= $status_text ?>

        </span>

    </td>

    <!-- ACTION -->

    <td>

        <div class="d-flex gap-2">

            <a
                href="sp_edit.php?id=<?= $row['MaSP'] ?>"
                class="btn btn-warning btn-sm"
            >
                Sửa
            </a>

            <a
                href="sp_delete.php?id=<?= $row['MaSP'] ?>"
                onclick="return confirm('Xóa sản phẩm?')"
                class="btn btn-danger btn-sm"
            >
                Xóa
            </a>

        </div>

    </td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

    <td colspan="8" class="text-center py-5 text-muted">

        Không có dữ liệu

    </td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<?php

$content = ob_get_clean();

include 'layout_admin.php';

?>
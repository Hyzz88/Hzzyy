<?php
session_start();
include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";

/* ===== LẤY MaDM TỪ URL ===== */
$maDM = isset($_GET['madm']) ? (int)$_GET['madm'] : 0;

if ($maDM <= 0) {
    die("❌ Danh mục không hợp lệ");
}

/* ===== LẤY TÊN DANH MỤC ===== */
$sqlDM = "SELECT TenDM FROM danhmuc WHERE MaDM = $maDM";
$rsDM  = mysqli_query($conn, $sqlDM);
$danhmuc = mysqli_fetch_assoc($rsDM);

$tenDM = $danhmuc['TenDM'] ?? 'Danh mục';

/* ===== LẤY SẢN PHẨM THEO MaDM ===== */
$sqlSP = "
    SELECT MaSP, TenSP, GiaBan, AnhDaiDien
    FROM sanpham
    WHERE MaDM = $maDM
";
$sanpham = mysqli_query($conn, $sqlSP);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $tenDM ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .product-card {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            height: 100%;
        }
        .product-card img {
            width: 100%;
            height: 260px;
            object-fit: cover;
        }
        .price {
            color: #d0021b;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include __DIR__ . "/Trang_Chu_Includes/Header.php"; ?>

<div class="container my-4">
    <h3 class="mb-4"><?= htmlspecialchars($tenDM) ?></h3>

    <div class="row g-4">

        <?php if ($sanpham && mysqli_num_rows($sanpham) > 0): ?>
            <?php while ($sp = mysqli_fetch_assoc($sanpham)): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="product-card">
                        <img src="Images/<?= htmlspecialchars($sp['AnhDaiDien']) ?>">

                        <h6 class="mt-2">
                            <?= htmlspecialchars($sp['TenSP']) ?>
                        </h6>

                        <p class="price">
                            <?= number_format($sp['GiaBan'], 0, ',', '.') ?>đ
                        </p>

                        <a href="ChiTietSP.php?id=<?= $sp['MaSP'] ?>"
                           class="btn btn-outline-dark btn-sm w-100">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>❌ Không có sản phẩm trong danh mục này</p>
        <?php endif; ?>

    </div>
</div>
<?php include 'chatbot.php'; ?>
<?php include __DIR__ . "/Trang_Chu_Includes/Footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

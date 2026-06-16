<?php
session_start();

// 1. Kết nối cơ sở dữ liệu
include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";
mysqli_set_charset($conn, 'utf8mb4');

/* 2. BỘ LỌC GIÁ */
$filter_price = isset($_GET['price_range']) ? $_GET['price_range'] : 'all';
$where_clause = "WHERE MaDM = 3";

if ($filter_price == 'under_500k') {
    $where_clause .= " AND GiaBan < 500000";
} elseif ($filter_price == 'under_5tr') {
    $where_clause .= " AND GiaBan < 5000000";
} elseif ($filter_price == 'above_10tr') {
    $where_clause .= " AND GiaBan > 10000000";
}

/* 3. QUERY */
$sqlSP = "SELECT MaSP, TenSP, GiaBan, AnhDaiDien 
          FROM sanpham $where_clause 
          ORDER BY NgayCapNhat DESC";

$result = mysqli_query($conn, $sqlSP);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trang Phục Quần - Lotusé</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color: #f8f9fa; color: #333; }
.category-banner { background: #000; color: #fff; padding: 30px 0; margin-bottom: 20px; text-align: center; }

.filter-section { background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.form-select-vstyle { border-radius: 20px; border: 1px solid #ddd; padding: 8px 20px; font-size: 0.9rem; }

.product-item { background: #fff; border-radius: 12px; transition: 0.3s; height: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
.product-item:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

.product-img { width: 100%; height: 320px; object-fit: cover; }

.product-info { padding: 15px; text-align: center; }

.product-name {
    font-size: 1rem;
    font-weight: 600;
    color: #222;
    text-decoration: none;
    height: 45px;
    display: block;
    overflow: hidden;
}

.product-price {
    color: #d9534f;
    font-weight: 700;
    font-size: 1.1rem;
    margin: 10px 0;
}

.btn-detail {
    background: #000;
    color: #fff;
    border-radius: 20px;
    font-size: 0.8rem;
    border: 1px solid #000;
    padding: 8px 20px;
    text-decoration: none;
    display: inline-block;
    transition: 0.3s;
}
.btn-detail:hover {
    background: #333;
    color: #fff;
}
</style>
</head>

<body>

<?php if(file_exists(__DIR__ . "/Trang_Chu_Includes/Header.php")) include __DIR__ . "/Trang_Chu_Includes/Header.php"; ?>

<div class="category-banner">
    <h1 class="text-uppercase fw-bold m-0">Bộ Sưu Tập Quần Nam</h1>
</div>

<div class="container mb-5">

<div class="filter-section d-flex justify-content-between align-items-center flex-wrap">
    <div><strong>Lọc theo giá:</strong></div>
    <form method="GET" class="d-flex gap-2">
        <select name="price_range" class="form-select form-select-vstyle" onchange="this.form.submit()">
            <option value="all" <?= $filter_price=='all'?'selected':'' ?>>Tất cả mức giá</option>
            <option value="under_500k" <?= $filter_price=='under_500k'?'selected':'' ?>>Dưới 500.000đ</option>
            <option value="under_5tr" <?= $filter_price=='under_5tr'?'selected':'' ?>>Dưới 5.000.000đ</option>
            <option value="above_10tr" <?= $filter_price=='above_10tr'?'selected':'' ?>>Trên 10.000.000đ</option>
        </select>
    </form>
</div>

<div class="row g-4">

<?php 
if ($result && mysqli_num_rows($result) > 0):
while ($row = mysqli_fetch_assoc($result)):

// ===== XỬ LÝ ẢNH THÔNG MINH =====
$path_anh = "img/no-image.jpg";

$raw = trim($row['AnhDaiDien']);
$list = preg_split('/\r\n|\r|\n/', $raw);
$first = trim($list[0] ?? '');
$first = str_replace('\\', '/', $first);

$found = false;

// 1. ảnh DB
if (!empty($first) && file_exists(__DIR__ . "/" . $first)) {
    $path_anh = $first;
    $found = true;
}

// 2. fallback uploads
if (!$found && !empty($first)) {
    $filename = basename($first);
    $upload_path = "uploads/" . $filename;

    if (file_exists(__DIR__ . "/" . $upload_path)) {
        $path_anh = $upload_path;
        $found = true;

        mysqli_query($conn, "
            UPDATE sanpham 
            SET AnhDaiDien = '$upload_path'
            WHERE MaSP = " . (int)$row['MaSP']
        );
    }
}

// 3. quét uploads
if (!$found && is_dir(__DIR__ . "/uploads")) {
    foreach (scandir(__DIR__ . "/uploads") as $file) {
        if ($file == "." || $file == "..") continue;

        if (strpos($file, (string)$row['MaSP']) !== false) {
            $path_anh = "uploads/" . $file;

            mysqli_query($conn, "
                UPDATE sanpham 
                SET AnhDaiDien = '$path_anh'
                WHERE MaSP = " . (int)$row['MaSP']
            );
            break;
        }
    }
}
?>

<div class="col-6 col-md-4 col-lg-3">
    <div class="product-item">
        <a href="chitiet_sanpham.php?id=<?= $row['MaSP'] ?>">
            <img src="<?= htmlspecialchars($path_anh) ?>" 
                 class="product-img"
                 alt="<?= htmlspecialchars($row['TenSP']) ?>"
                 onerror="this.src='img/no-image.jpg'">
        </a>

        <div class="product-info">
            <a href="chitiet_sanpham.php?id=<?= $row['MaSP'] ?>" class="product-name">
                <?= htmlspecialchars($row['TenSP']) ?>
            </a>

            <div class="product-price">
                <?= number_format($row['GiaBan'],0,',','.') ?>đ
            </div>

            <a href="chitiet_sanpham.php?id=<?= $row['MaSP'] ?>" class="btn btn-detail">
                XEM CHI TIẾT
            </a>
        </div>
    </div>
</div>

<?php endwhile; else: ?>

<div class="col-12 text-center py-5">
    <p class="text-muted">Không tìm thấy mẫu quần nào.</p>
    <a href="TrangPhuc_Quan.php" class="btn btn-outline-dark btn-sm">Xem tất cả sản phẩm</a>
</div>

<?php endif; ?>

</div>
</div>

<?php if(file_exists(__DIR__ . "/Trang_Chu_Includes/Footer.php")) include __DIR__ . "/Trang_Chu_Includes/Footer.php"; ?>
<?php include_once 'chatbot.php'; ?>

</body>
</html>
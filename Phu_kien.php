<?php
session_start();

// 1. Kết nối
include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";
mysqli_set_charset($conn, 'utf8mb4');

/* 2. FILTER GIÁ (đã sửa đồng bộ option) */
$filter_price = isset($_GET['price_range']) ? $_GET['price_range'] : 'all';
$price_condition = "";

if ($filter_price == 'under_500k') {
    $price_condition = " AND GiaBan < 500000";
} elseif ($filter_price == 'under_5tr') {
    $price_condition = " AND GiaBan < 5000000";
} elseif ($filter_price == 'above_10tr') {
    $price_condition = " AND GiaBan > 10000000";
}

/* 3. QUERY */
$resDep = mysqli_query($conn, "SELECT * FROM sanpham WHERE MaDM = 4 $price_condition ORDER BY MaSP DESC");
$resGiay = mysqli_query($conn, "SELECT * FROM sanpham WHERE MaDM = 5 $price_condition ORDER BY MaSP DESC");

/* 4. HÀM ẢNH THÔNG MINH */
function getSmartImage($row, $conn) {

    $path = "img/no-image.jpg";

    $raw = trim($row['AnhDaiDien']);
    $list = preg_split('/\r\n|\r|\n/', $raw);
    $first = trim($list[0] ?? '');
    $first = str_replace('\\', '/', $first);

    $found = false;

    // 1. DB
    if (!empty($first) && file_exists(__DIR__ . "/" . $first)) {
        $path = $first;
        $found = true;
    }

    // 2. uploads
    if (!$found && !empty($first)) {
        $filename = basename($first);
        $upload_path = "uploads/" . $filename;

        if (file_exists(__DIR__ . "/" . $upload_path)) {
            $path = $upload_path;
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
                $path = "uploads/" . $file;

                mysqli_query($conn, "
                    UPDATE sanpham 
                    SET AnhDaiDien = '$path'
                    WHERE MaSP = " . (int)$row['MaSP']
                );
                break;
            }
        }
    }

    return $path;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Phụ Kiện Nam - Lotusé</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color: #f8f9fa; color: #333; }
.category-banner { background: #1a1a1a; color: #fff; padding: 40px 0; margin-bottom: 20px; text-align: center; }

.filter-section { background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.form-select-vstyle { border-radius: 20px; border: 1px solid #ddd; padding: 8px 20px; font-size: 0.9rem; width: auto; min-width: 200px; }

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
    border: none;
    padding: 8px 20px;
}
</style>
</head>

<body>

<?php if(file_exists(__DIR__ . "/Trang_Chu_Includes/Header.php")) include __DIR__ . "/Trang_Chu_Includes/Header.php"; ?>

<div class="category-banner text-uppercase">
    <h1 class="fw-bold m-0">Giày & Dép Nam</h1>
</div>

<div class="container mb-5">

<div class="filter-section d-flex justify-content-between align-items-center flex-wrap">
    <div><strong>Lọc theo giá:</strong></div>
    <form method="GET">
        <select name="price_range" class="form-select form-select-vstyle" onchange="this.form.submit()">
            <option value="all" <?= $filter_price=='all'?'selected':'' ?>>Tất cả mức giá</option>
            <option value="under_500k" <?= $filter_price=='under_500k'?'selected':'' ?>>Dưới 500.000đ</option>
            <option value="under_5tr" <?= $filter_price=='under_5tr'?'selected':'' ?>>Dưới 5.000.000đ</option>
            <option value="above_10tr" <?= $filter_price=='above_10tr'?'selected':'' ?>>Trên 10.000.000đ</option>
        </select>
    </form>
</div>

<h2>Dép Nam</h2>
<div class="row g-4 mb-5">
<?php displayProducts($resDep, $conn); ?>
</div>

<h2>Giày Nam</h2>
<div class="row g-4 mb-5">
<?php displayProducts($resGiay, $conn); ?>
</div>

</div>

<?php 
function displayProducts($result, $conn) {
    if ($result && mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
?>
<div class="col-6 col-md-4 col-lg-3">
    <div class="product-item">
        <a href="chitiet_sanpham.php?id=<?= $row['MaSP'] ?>">
            <img src="<?= htmlspecialchars(getSmartImage($row, $conn)) ?>" 
                 class="product-img"
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
<?php
        endwhile;
    else:
        echo '<div class="col-12 text-muted ps-3">Không có sản phẩm.</div>';
    endif;
}

if(file_exists(__DIR__ . "/Trang_Chu_Includes/Footer.php")) include __DIR__ . "/Trang_Chu_Includes/Footer.php"; 
?>
<?php include_once 'chatbot.php'; ?>

</body>
</html>
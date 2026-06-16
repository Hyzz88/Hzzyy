<?php
session_start();
include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";
mysqli_set_charset($conn, 'utf8mb4');

/* 1. FILTER */
$filter_price = isset($_GET['price_range']) ? $_GET['price_range'] : 'all';
$sort_order   = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

/* 2. ĐIỀU KIỆN GIÁ */
$price_condition = "";
if ($filter_price == 'under_500k') {
    $price_condition = " AND GiaBan < 500000";
} elseif ($filter_price == '500k_2tr') {
    $price_condition = " AND GiaBan BETWEEN 500000 AND 2000000";
} elseif ($filter_price == 'above_2tr') {
    $price_condition = " AND GiaBan > 2000000";
}

/* 3. SẮP XẾP */
$order_sql = " ORDER BY MaSP DESC";
if ($sort_order == 'price_asc') {
    $order_sql = " ORDER BY GiaBan ASC";
} elseif ($sort_order == 'price_desc') {
    $order_sql = " ORDER BY GiaBan DESC";
}

/* 4. QUERY */
$resHe   = mysqli_query($conn, "SELECT * FROM sanpham WHERE MaDM = 1 $price_condition $order_sql");
$resDong = mysqli_query($conn, "SELECT * FROM sanpham WHERE MaDM = 2 $price_condition $order_sql");
$resQuan = mysqli_query($conn, "SELECT * FROM sanpham WHERE MaDM = 3 $price_condition $order_sql");
$resDep  = mysqli_query($conn, "SELECT * FROM sanpham WHERE MaDM = 4 $price_condition $order_sql");
$resGiay = mysqli_query($conn, "SELECT * FROM sanpham WHERE MaDM = 5 $price_condition $order_sql");

/* 5. HÀM ẢNH THÔNG MINH */
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

ob_start();
?>

<style>
body { background-color: #f8f9fa; color: #333; }

.category-banner {
    background: #000;
    color: #fff;
    padding: 40px 0;
    margin-bottom: 30px;
    text-align: center;
}

.filter-section {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.form-select-vstyle {
    border-radius: 20px;
    border: 1px solid #ddd;
    padding: 8px 35px 8px 20px;
    font-size: 0.9rem;
    min-width: 180px;
    background-position: right 12px center;
}

.product-item {
    background: #fff;
    border-radius: 12px;
    transition: 0.3s;
    height: 100%;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.product-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.product-img {
    width: 100%;
    height: 320px;
    object-fit: cover;
}

.product-info {
    padding: 15px;
    text-align: center;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #222;
    text-decoration: none;
    height: 45px;
    overflow: hidden;
}

.product-price {
    color: #d9534f;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 15px;
}

.btn-detail {
    background: #000;
    color: #fff;
    border-radius: 25px;
    font-size: 0.8rem;
    padding: 10px 20px;
    text-decoration: none;
}

.section-title-vstyle {
    font-weight: 800;
    text-transform: uppercase;
    border-left: 5px solid #000;
    padding-left: 15px;
    margin: 50px 0 25px;
}
</style>

<div class="category-banner">
    <h1 class="text-uppercase fw-bold m-0">Tất Cả Bộ Sưu Tập</h1>
</div>

<div class="container mb-5">

<div class="filter-section d-flex justify-content-center">
<form method="GET" class="d-flex gap-3 flex-wrap">

<select name="price_range" class="form-select form-select-vstyle" onchange="this.form.submit()">
    <option value="all" <?= $filter_price=='all'?'selected':'' ?>>Tất cả mức giá</option>
    <option value="under_500k" <?= $filter_price=='under_500k'?'selected':'' ?>>Dưới 500.000đ</option>
    <option value="500k_2tr" <?= $filter_price=='500k_2tr'?'selected':'' ?>>500k - 2tr</option>
</select>

<select name="sort" class="form-select form-select-vstyle" onchange="this.form.submit()">
    <option value="newest" <?= $sort_order=='newest'?'selected':'' ?>>Mới nhất</option>
    <option value="price_asc" <?= $sort_order=='price_asc'?'selected':'' ?>>Giá tăng</option>
    <option value="price_desc" <?= $sort_order=='price_desc'?'selected':'' ?>>Giá giảm</option>
</select>

<a href="Bo_Suu_Tap.php" class="btn btn-outline-dark rounded-pill px-3">Làm mới</a>

</form>
</div>

<?php
renderCategory("Xuân Hè", $resHe, $conn);
renderCategory("Thu Đông", $resDong, $conn);
renderCategory("Quần", $resQuan, $conn);
renderCategory("Dép", $resDep, $conn);
renderCategory("Giày", $resGiay, $conn);
?>

</div>

<?php
function renderCategory($title, $result, $conn) {
    if ($result && mysqli_num_rows($result) > 0): ?>
        <h3 class="section-title-vstyle"><?= $title ?></h3>
        <div class="row g-4 mb-5">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
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
                        <div>
                            <div class="product-price">
                                <?= number_format($row['GiaBan'],0,',','.') ?>đ
                            </div>
                            <a href="chitiet_sanpham.php?id=<?= $row['MaSP'] ?>" class="btn btn-detail">
                                XEM CHI TIẾT
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
<?php endif;
}

$content = ob_get_clean();
include "Trang_Chu_Includes/Layout.php";
?>
<?php include_once 'chatbot.php'; ?>

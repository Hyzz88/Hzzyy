<?php
session_start();
include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";
mysqli_set_charset($conn, 'utf8mb4');

/* =========================
   AJAX GỢI Ý (THÊM MỚI)
========================= */
if (isset($_GET['ajax']) && $_GET['ajax'] == 'suggest') {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);

    $sql = "SELECT MaSP, TenSP, AnhDaiDien, GiaBan 
            FROM sanpham 
            WHERE TenSP LIKE '%$keyword%' 
            LIMIT 5";

    $result = mysqli_query($conn, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}

/* =========================
   TÌM KIẾM (GIỮ NGUYÊN)
========================= */
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';

$sql = "SELECT sp.*, dm.TenDM 
        FROM sanpham sp
        INNER JOIN danhmuc dm ON sp.MaDM = dm.MaDM
        WHERE sp.TenSP LIKE '%$keyword%' 
        OR dm.TenDM LIKE '%$keyword%'
        ORDER BY dm.MaDM ASC, sp.NgayCapNhat DESC";

$result = mysqli_query($conn, $sql);
$count = mysqli_num_rows($result);

/* =========================
   FIX ẢNH (GIỮ NGUYÊN)
========================= */
function fixImagePath($rawData) {
    if (empty($rawData)) return "https://via.placeholder.com/400x550?text=No+Image";
    
    $images = preg_split('/\s+/', trim($rawData));
    $file = str_replace('\\', '/', $images[0]);

    $dirs = ['', 'img/', 'img/aoxuanhe/', 'img/aothudong/', 'img/quan/', 'img/giaydep/'];

    foreach ($dirs as $dir) {
        $fullPath = $dir . $file;
        if (file_exists(__DIR__ . "/" . $fullPath)) {
            return $fullPath;
        }
    }
    return "https://via.placeholder.com/400x550?text=Lotuse+Fashion";
}

$title = "Tìm kiếm: " . $keyword;
ob_start();
?>

<!-- =========================
     Ô SEARCH (CHỈ THÊM BOX GỢI Ý)
========================= -->
<div class="container mt-4">
    <div class="position-relative" style="max-width:400px; margin:auto;">
        <input type="text" id="searchBox" class="form-control"
               placeholder="Tìm sản phẩm..."
               value="<?= htmlspecialchars($keyword) ?>"
               autocomplete="off">

        <!-- BOX GỢI Ý -->
        <div id="suggestBox" class="list-group position-absolute w-100"></div>
    </div>
</div>

<!-- =========================
     KẾT QUẢ (GIỮ NGUYÊN)
========================= -->
<div class="container mt-5">
    <div class="search-header text-center mb-5">
        <h2 class="fw-bold text-uppercase">Kết quả tìm kiếm</h2>
        <p class="text-muted">
            Tìm thấy <b><?= $count ?></b> sản phẩm cho 
            "<span class="text-danger"><?= htmlspecialchars($keyword) ?></span>"
        </p>
    </div>

    <div class="row g-4">
        <?php if ($count > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $path_anh = fixImagePath($row['AnhDaiDien']);
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm product-search-card">

                    <div style="height:350px; overflow:hidden;">
                        <img src="<?= $path_anh ?>" class="w-100 h-100" style="object-fit:cover;">
                    </div>

                    <div class="card-body text-center">
                        <small><?= $row['TenDM'] ?></small>
                        <h6 class="fw-bold text-truncate"><?= $row['TenSP'] ?></h6>
                        <p class="text-danger fw-bold">
                            <?= number_format($row['GiaBan'], 0, ',', '.') ?>đ
                        </p>
                        <a href="chitiet_sanpham.php?id=<?= $row['MaSP'] ?>" class="btn btn-dark btn-sm w-100">
                            CHI TIẾT
                        </a>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<!-- =========================
     CSS GỢI Ý (NHẸ, KHÔNG ẢNH HƯỞNG BACKGROUND)
========================= -->
<style>
#suggestBox {
    top: 100%;
    z-index: 999;
    max-height: 250px;
    overflow-y: auto;
    border: 1px solid #ddd;
    background: #fff;
}

#suggestBox a {
    display: flex;
    gap: 10px;
    padding: 10px;
    text-decoration: none;
    color: black;
}

#suggestBox a:hover {
    background: #f5f5f5;
}

#suggestBox img {
    width: 45px;
    height: 45px;
    object-fit: cover;
}
</style>

<!-- =========================
     JS GỢI Ý
========================= -->
<script>
let delay;

document.getElementById("searchBox").addEventListener("keyup", function () {

    clearTimeout(delay);
    let keyword = this.value;

    delay = setTimeout(() => {

        if (keyword.length < 2) {
            document.getElementById("suggestBox").innerHTML = "";
            return;
        }

        fetch(`?ajax=suggest&keyword=${keyword}`)
        .then(res => res.json())
        .then(data => {

            let html = "";

            if (data.length > 0) {
                data.forEach(item => {
                    html += `
                        <a href="chitiet_sanpham.php?id=${item.MaSP}">
                            <img src="img/${item.AnhDaiDien}">
                            <div>
                                <div>${item.TenSP}</div>
                                <small class="text-danger">
                                    ${Number(item.GiaBan).toLocaleString()}đ
                                </small>
                            </div>
                        </a>
                    `;
                });
            } else {
                html = `<div class="p-2">Không có kết quả</div>`;
            }

            document.getElementById("suggestBox").innerHTML = html;
        });

    }, 300);
});
</script>

<?php
$content = ob_get_clean();
include "Trang_Chu_Includes/Layout.php";
?>
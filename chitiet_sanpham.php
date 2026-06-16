<?php
session_start();

include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";

mysqli_set_charset($conn, "utf8mb4");

/* =========================
   LẤY ID SẢN PHẨM
========================= */

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: Index.php");
    exit;
}

/* =========================
   KIỂM TRA ĐĂNG NHẬP
========================= */

$is_logged_in = isset($_SESSION['user']);

$ten_nguoi_dung = $_SESSION['user']['name'] ?? "Thành viên";

/* =========================
   XỬ LÝ THÍCH
========================= */

if (isset($_POST['action']) && $_POST['action'] == 'like') {

    mysqli_query(
        $conn,
        "UPDATE sanpham 
         SET LuotTym = LuotTym + 1 
         WHERE MaSP = $id"
    );

    header("Location: chitiet_sanpham.php?id=$id");
    exit;
}

/* =========================
   GỬI BÌNH LUẬN
========================= */

if (isset($_POST['submit_comment']) && $is_logged_in) {

    $comment = trim($_POST['comment'] ?? '');

    $rating = (int)($_POST['rating'] ?? 5);

    if (!empty($comment)) {

        $comment = mysqli_real_escape_string($conn, $comment);

        $sql_comment = "
            INSERT INTO binhluan
            (
                MaSP,
                HoTen,
                NoiDung,
                SoSao,
                NgayBL
            )
            VALUES
            (
                $id,
                '$ten_nguoi_dung',
                '$comment',
                $rating,
                NOW()
            )
        ";

        mysqli_query($conn, $sql_comment);
    }

    header("Location: chitiet_sanpham.php?id=$id");
    exit;
}

/* =========================
   LẤY THÔNG TIN SẢN PHẨM
========================= */

$sql_product = "
    SELECT sp.*, dm.TenDM
    FROM sanpham sp
    LEFT JOIN danhmuc dm ON sp.MaDM = dm.MaDM
    WHERE sp.MaSP = $id
";

$result_product = mysqli_query($conn, $sql_product);

$sp = mysqli_fetch_assoc($result_product);

if (!$sp) {
    header("Location: Index.php");
    exit;
}

/* =========================
   BIẾN THỂ + TỒN KHO
========================= */

$sql_variant = "
    SELECT DISTINCT KichCo, MauSac, SoLuongTon
    FROM bienthesp
    WHERE MaSP = $id
";

$result_variant = mysqli_query($conn, $sql_variant);

$sizes = [];
$colors = [];

$total_stock = 0;

while ($row = mysqli_fetch_assoc($result_variant)) {

    if (!empty($row['KichCo'])) {
        $sizes[] = $row['KichCo'];
    }

    if (!empty($row['MauSac'])) {
        $colors[] = $row['MauSac'];
    }

    $total_stock += (int)$row['SoLuongTon'];
}

$sizes = array_unique($sizes);
$colors = array_unique($colors);

/* =========================
   XỬ LÝ ẢNH
========================= */

$anh_list = [];

$raw = trim($sp['AnhDaiDien'] ?? '');

$raw = str_replace("\\", "/", $raw);

if (!empty($raw)) {

    $images = preg_split('/\s+/', $raw);

    foreach ($images as $img) {

        $img = trim($img);

        if (empty($img)) continue;

        if (
            strpos($img, 'img/') === 0 &&
            file_exists(__DIR__ . '/' . $img)
        ) {

            $anh_list[] = $img;
        }

        else {

            $filename = basename($img);

            $upload_path = "uploads/" . $filename;

            if (file_exists(__DIR__ . '/' . $upload_path)) {
                $anh_list[] = $upload_path;
            }
        }
    }
}

if (empty($anh_list)) {
    $anh_list[] = "img/no-image.jpg";
}

$title = htmlspecialchars($sp['TenSP'] ?? 'Chi tiết sản phẩm');

ob_start();
?>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>

body{
    background:#f5f5f5;
    font-family:'Be Vietnam Pro', sans-serif;
    color:#222;
}

/* =========================
   PRODUCT
========================= */

.product-container{
    margin-top:40px;
    margin-bottom:60px;
}

.product-gallery{
    position:sticky;
    top:20px;
}

.img-display{
    border-radius:24px;
    overflow:hidden;
    background:#fff;
    padding:15px;
    border:1px solid #eee;
    box-shadow:0 8px 30px rgba(0,0,0,.05);
    transition:.3s;
}

.img-display:hover{
    transform:translateY(-3px);
    box-shadow:0 14px 40px rgba(0,0,0,.08);
}

.img-display img{
    width:100%;
    height:620px;
    object-fit:cover;
    border-radius:18px;
    transition:.35s;
}

.img-display:hover img{
    transform:scale(1.03);
}

.thumb-list{
    margin-top:18px;
    display:flex;
    gap:12px;
    flex-wrap:wrap;
}

.thumb-list img{
    width:82px;
    height:102px;
    object-fit:cover;
    border-radius:14px;
    cursor:pointer;
    border:2px solid transparent;
    transition:.25s;
}

.thumb-list img:hover{
    border-color:#000;
    transform:translateY(-3px);
}

.active-thumb{
    border-color:#000 !important;
}

/* =========================
   INFO
========================= */

.product-info{
    background:#fff;
    border-radius:24px;
    padding:32px;
    border:1px solid #eee;
    box-shadow:0 8px 30px rgba(0,0,0,.04);
    transition:.3s;
}

.product-info:hover{
    transform:translateY(-3px);
    box-shadow:0 14px 40px rgba(0,0,0,.08);
}

.category-text{
    font-size:.82rem;
    color:#888;
    text-transform:uppercase;
    letter-spacing:1.2px;
    margin-bottom:12px;
    font-weight:500;
}

.product-title{
    font-size:2rem;
    line-height:1.5;
    margin-bottom:20px;
    font-weight:600;
}

.price-large{
    color:#d70018;
    font-size:2rem;
    font-weight:700;
    margin:24px 0 14px;
}

.stock-text{
    font-size:.95rem;
    color:#666;
    margin-bottom:25px;
}

.like-btn{
    border-radius:50px;
    padding:10px 18px;
    font-weight:500;
}

/* =========================
   VARIANT
========================= */

.variant-group{
    margin-bottom:28px;
}

.variant-title{
    display:block;
    margin-bottom:14px;
    font-size:.92rem;
    font-weight:600;
}

.variant-wrap{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
}

.variant-input{
    display:none;
}

.variant-label{
    min-width:72px;
    text-align:center;
    padding:12px 18px;
    border:1px solid #ddd;
    border-radius:14px;
    cursor:pointer;
    background:#fff;
    transition:.25s;
    font-weight:500;
}

.variant-label:hover{
    transform:translateY(-3px);
    border-color:#111;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
}

.variant-input:checked + .variant-label{
    background:#111;
    color:#fff;
    border-color:#111;
}

/* =========================
   QUANTITY
========================= */

.quantity-input{
    width:140px;
    height:50px;
    border-radius:14px;
    text-align:center;
    font-size:1rem;
    font-weight:500;
    transition:.25s;
}

.quantity-input:focus{
    border-color:#111;
    box-shadow:0 0 0 4px rgba(0,0,0,.08);
}

.stock-warning{
    margin-top:12px;
    color:#d70018;
    font-size:.92rem;
    display:none;
}

/* =========================
   BUTTON
========================= */

.action-row{
    display:flex;
    gap:15px;
    margin-top:35px;
}

.btn-cart,
.btn-buy{
    flex:1;
    padding:15px;
    border-radius:16px;
    transition:.3s;
    font-weight:600;
}

.btn-cart{
    background:#fff;
    border:2px solid #111;
}

.btn-cart:hover{
    background:#111;
    color:#fff;
    transform:translateY(-3px);
}

.btn-buy{
    background:#111;
    color:#fff;
    border:none;
}

.btn-buy:hover{
    background:#222;
    transform:translateY(-3px);
}

/* =========================
   DESCRIPTION
========================= */

.desc-box{
    margin-top:35px;
    background:#fff;
    border-radius:24px;
    padding:35px;
    border:1px solid #eee;
    box-shadow:0 8px 30px rgba(0,0,0,.04);
    transition:.3s;
}

.desc-box:hover{
    transform:translateY(-3px);
    box-shadow:0 14px 40px rgba(0,0,0,.08);
}

.desc-title{
    font-size:1.2rem;
    font-weight:600;
    margin-bottom:24px;
    padding-bottom:14px;
    border-bottom:1px solid #eee;
}

.desc-content{
    color:#444;
    line-height:2;
    font-size:1rem;
    font-weight:400;
}

/* PHẦN NỘI DUNG */

.desc-content p{
    margin-bottom:18px;
    position:relative;
    padding-left:24px;
}

/* ICON ĐẦU DÒNG */

.desc-content p::before{
    content:"◆";
    position:absolute;
    left:0;
    top:1px;
    color:#111;
    font-size:.7rem;
}

/* IMAGE */

.desc-content img{
    max-width:100%;
    border-radius:16px;
    margin:18px 0;
}

/* LIST */

.desc-content ul{
    padding-left:22px;
}

.desc-content li{
    margin-bottom:10px;
}

/* BOLD */

.desc-content strong{
    color:#111;
    font-weight:600;
}

/* =========================
   COMMENT
========================= */

.comment-section{
    margin-top:45px;
    background:#fff;
    border-radius:24px;
    padding:35px;
    border:1px solid #eee;
    box-shadow:0 8px 30px rgba(0,0,0,.04);
    transition:.3s;
}

.comment-section:hover{
    transform:translateY(-3px);
    box-shadow:0 14px 40px rgba(0,0,0,.08);
}

.comment-title{
    font-size:1.4rem;
    font-weight:600;
    margin-bottom:30px;
}

.review-form{
    background:#fafafa;
    border-radius:22px;
    padding:26px;
    border:1px solid #eee;
    margin-bottom:30px;
    transition:.3s;
}

.review-form:hover{
    box-shadow:0 10px 24px rgba(0,0,0,.05);
}

.comment-item{
    background:#fafafa;
    border-radius:20px;
    padding:22px;
    margin-bottom:18px;
    border:1px solid #eee;
    transition:.3s;
}

.comment-item:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,.06);
}

.star-color{
    color:#ffb400;
    letter-spacing:2px;
}

/* =========================
   MOBILE
========================= */

@media(max-width:991px){

    .product-gallery{
        position:relative;
        top:auto;
    }

    .img-display img{
        height:420px;
    }

    .action-row{
        flex-direction:column;
    }

    .product-title{
        font-size:1.6rem;
    }

    .product-info,
    .desc-box,
    .comment-section{
        padding:22px;
    }

}

</style>

<div class="container product-container">

    <div class="row g-4">

        <!-- LEFT -->

        <div class="col-lg-6">

            <div class="product-gallery">

                <div class="img-display">

                    <img
                        id="mainImg"
                        src="<?= htmlspecialchars($anh_list[0]) ?>"
                    >

                </div>

                <div class="thumb-list">

                    <?php foreach($anh_list as $index => $img): ?>

                        <img
                            src="<?= htmlspecialchars($img) ?>"
                            class="<?= $index == 0 ? 'active-thumb' : '' ?>"
                            onclick="changeImage(this)"
                        >

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

        <!-- RIGHT -->

        <div class="col-lg-6">

            <div class="product-info">

                <div class="category-text">
                    <?= htmlspecialchars($sp['TenDM'] ?? '') ?>
                </div>

                <h1 class="product-title">
                    <?= htmlspecialchars($sp['TenSP'] ?? '') ?>
                </h1>

                <form method="POST" class="mb-3">

                    <button
                        type="submit"
                        name="action"
                        value="like"
                        class="btn btn-outline-danger like-btn"
                    >
                        <i class="fa-solid fa-heart"></i>
                        <?= number_format($sp['LuotTym'] ?? 0) ?> lượt thích
                    </button>

                </form>

                <div class="price-large">
                    <?= number_format($sp['GiaBan'] ?? 0, 0, ',', '.') ?> VNĐ
                </div>

                <div class="stock-text">

                    Còn lại:
                    <strong><?= $total_stock ?></strong>
                    sản phẩm

                </div>

                <form method="POST" action="xu_ly_gio_hang.php">

                    <input type="hidden" name="MaSP" value="<?= $sp['MaSP'] ?>">

                    <input type="hidden" name="TenSP" value="<?= htmlspecialchars($sp['TenSP']) ?>">

                    <input type="hidden" name="GiaBan" value="<?= $sp['GiaBan'] ?>">

                    <input type="hidden" name="AnhDaiDien" value="<?= $anh_list[0] ?>">

                    <!-- COLOR -->

                    <div class="variant-group">

                        <span class="variant-title">
                            Màu sắc
                        </span>

                        <div class="variant-wrap">

                            <?php foreach($colors as $i => $color): ?>

                                <input
                                    type="radio"
                                    name="MauSac"
                                    value="<?= htmlspecialchars($color) ?>"
                                    id="c-<?= $i ?>"
                                    class="variant-input"
                                    <?= $i == 0 ? 'checked' : '' ?>
                                >

                                <label
                                    for="c-<?= $i ?>"
                                    class="variant-label"
                                >
                                    <?= htmlspecialchars($color) ?>
                                </label>

                            <?php endforeach; ?>

                        </div>

                    </div>

                    <!-- SIZE -->

                    <div class="variant-group">

                        <span class="variant-title">
                            Kích cỡ
                        </span>

                        <div class="variant-wrap">

                            <?php foreach($sizes as $i => $size): ?>

                                <input
                                    type="radio"
                                    name="KichCo"
                                    value="<?= htmlspecialchars($size) ?>"
                                    id="s-<?= $i ?>"
                                    class="variant-input"
                                    <?= $i == 0 ? 'checked' : '' ?>
                                >

                                <label
                                    for="s-<?= $i ?>"
                                    class="variant-label"
                                >
                                    <?= htmlspecialchars($size) ?>
                                </label>

                            <?php endforeach; ?>

                        </div>

                    </div>

                    <!-- QUANTITY -->

                    <div class="variant-group">

                        <span class="variant-title">
                            Số lượng
                        </span>

                        <input
                            type="number"
                            name="SoLuong"
                            value="1"
                            min="1"
                            class="form-control quantity-input"
                            id="quantityInput"
                        >

                        <div class="stock-warning" id="stockWarning">
                            Số lượng vượt quá hàng tồn kho.
                        </div>

                    </div>

                    <!-- BUTTON -->

                    <div class="action-row">

                        <?php if($total_stock > 0): ?>

                            <button
                                type="submit"
                                name="add_to_cart"
                                class="btn-cart"
                            >
                                <i class="fa-solid fa-cart-shopping"></i>
                                Thêm giỏ hàng
                            </button>

                            <button
                                type="submit"
                                name="buy_now"
                                class="btn-buy"
                            >
                                Mua ngay
                            </button>

                        <?php else: ?>

                            <button
                                type="button"
                                class="btn btn-secondary w-100"
                                disabled
                            >
                                Tạm hết hàng
                            </button>

                        <?php endif; ?>

                    </div>

                </form>

            </div>

            <!-- DESCRIPTION -->

            <div class="desc-box">

                <div class="desc-title">
                    Mô tả sản phẩm
                </div>

                <div class="desc-content">

                    <?=
                        html_entity_decode(
                            $sp['MoTaChiTiet'] ?? $sp['MoTa'] ?? 'Đang cập nhật...',
                            ENT_QUOTES | ENT_HTML5,
                            'UTF-8'
                        )
                    ?>

                </div>

            </div>

        </div>

    </div>

    <!-- COMMENT -->

    <div class="comment-section">

        <div class="comment-title">
            <i class="fa-solid fa-comments"></i>
            Đánh giá sản phẩm
        </div>

        <?php if($is_logged_in): ?>

            <div class="review-form">

                <form method="POST">

                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">

                        <span>
                            Chào,
                            <strong><?= htmlspecialchars($ten_nguoi_dung) ?></strong>
                        </span>

                        <select name="rating" class="form-select w-auto">

                            <option value="5">⭐⭐⭐⭐⭐ Tuyệt vời</option>
                            <option value="4">⭐⭐⭐⭐ Tốt</option>
                            <option value="3">⭐⭐⭐ Bình thường</option>
                            <option value="2">⭐⭐ Kém</option>
                            <option value="1">⭐ Rất tệ</option>

                        </select>

                    </div>

                    <textarea
                        name="comment"
                        class="form-control mb-3"
                        rows="4"
                        placeholder="Chia sẻ trải nghiệm của bạn..."
                        required
                    ></textarea>

                    <button
                        type="submit"
                        name="submit_comment"
                        class="btn btn-dark px-4 py-2 rounded-4"
                    >
                        Gửi đánh giá
                    </button>

                </form>

            </div>

        <?php endif; ?>

        <?php

        $sql_comment_list = "
            SELECT *
            FROM binhluan
            WHERE MaSP = $id
            ORDER BY NgayBL DESC
        ";

        $result_comment = mysqli_query($conn, $sql_comment_list);

        ?>

        <?php while($cm = mysqli_fetch_assoc($result_comment)): ?>

            <div class="comment-item">

                <div class="d-flex justify-content-between mb-2">

                    <span class="fw-semibold">

                        <?= htmlspecialchars($cm['HoTen']) ?>

                    </span>

                    <small class="text-muted">

                        <?= date('d/m/Y', strtotime($cm['NgayBL'])) ?>

                    </small>

                </div>

                <div class="star-color mb-2">

                    <?php
                    for($i = 1; $i <= $cm['SoSao']; $i++){
                        echo '★';
                    }
                    ?>

                </div>

                <div style="line-height:1.8;color:#555;">

                    <?= nl2br(htmlspecialchars($cm['NoiDung'])) ?>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

</div>

<script>

function changeImage(el){

    document.getElementById('mainImg').src = el.src;

    document.querySelectorAll('.thumb-list img').forEach(img=>{
        img.classList.remove('active-thumb');
    });

    el.classList.add('active-thumb');
}

/* =========================
   GIỚI HẠN TỒN KHO
========================= */

const quantityInput = document.getElementById('quantityInput');

const stockWarning = document.getElementById('stockWarning');

const maxStock = <?= $total_stock ?>;

quantityInput.addEventListener('input', function(){

    let value = parseInt(this.value);

    if(value > maxStock){

        stockWarning.style.display = 'block';

        this.value = maxStock;

    }else{

        stockWarning.style.display = 'none';
    }

    if(value < 1 || isNaN(value)){
        this.value = 1;
    }

});

</script>
<?php include_once 'chatbot.php'; ?>
<?php

$content = ob_get_clean();

$show_slider = false;

include __DIR__ . "/Trang_Chu_Includes/Layout.php";

?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style></style>
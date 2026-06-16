<?php

session_start();

include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";

/* =========================
   FIX CART ERROR
========================= */

if(isset($_SESSION['cart'])){

    $_SESSION['cart'] = array_filter(

        $_SESSION['cart'],

        function($item){

            return is_array($item);
        }
    );
}

/* =========================
   GIỎ HÀNG
========================= */

if (isset($_GET['action'])) {

    $key = $_GET['key'] ?? '';

    /* =========================
       XÓA
    ========================= */

    if ($_GET['action'] == 'delete') {

        if (isset($_SESSION['cart'][$key])) {

            unset($_SESSION['cart'][$key]);
        }
    }

    /* =========================
       GIẢM
    ========================= */

    if ($_GET['action'] == 'decrease') {

        if (
            isset($_SESSION['cart'][$key])
            &&
            is_array($_SESSION['cart'][$key])
        ) {

            $_SESSION['cart'][$key]['soluong']--;

            if ($_SESSION['cart'][$key]['soluong'] <= 0) {

                unset($_SESSION['cart'][$key]);
            }
        }
    }

    /* =========================
       TĂNG
    ========================= */

    if ($_GET['action'] == 'increase') {

        if (
            isset($_SESSION['cart'][$key])
            &&
            is_array($_SESSION['cart'][$key])
        ) {

            $_SESSION['cart'][$key]['soluong']++;
        }
    }

    header("Location: Gio_Hang.php");
    exit();
}

$title = "Giỏ hàng - Men's Wear";

ob_start();

$cart = $_SESSION['cart'] ?? [];

$total_money = 0;

?>

<style>

/* ================= BODY ================= */

body{
    background:#f5f7fb;
    font-family:'Segoe UI',sans-serif;
}

/* ================= CONTAINER ================= */

.cart-container{
    padding-top:40px;
    padding-bottom:60px;
}

/* ================= TITLE ================= */

.cart-title{
    font-size:2rem;
    font-weight:800;
    margin-bottom:30px;
    color:#111827;
}

/* ================= TABLE ================= */

.cart-table{
    background:#fff;
    border-radius:24px;
    overflow:hidden;
    box-shadow:0 10px 40px rgba(0,0,0,.05);
}

.cart-table table{
    margin:0;
}

.cart-table thead{
    background:#111827;
    color:#fff;
}

.cart-table th{
    padding:20px 15px;
    border:none;
    font-size:.95rem;
}

.cart-table td{
    padding:20px 15px;
    vertical-align:middle;
}

/* ================= PRODUCT ================= */

.product-box{
    display:flex;
    align-items:center;
    gap:15px;
}

.product-img{
    width:90px;
    height:110px;
    object-fit:cover;
    border-radius:14px;
    border:1px solid #eee;
}

.product-name{
    font-weight:700;
    color:#111827;
    margin-bottom:6px;
}

.product-info{
    color:#6b7280;
    font-size:.9rem;
}

/* ================= PRICE ================= */

.price{
    font-weight:700;
    color:#111827;
}

/* ================= QTY ================= */

.qty-box{
    display:flex;
    align-items:center;
    gap:10px;
}

.qty-btn{
    width:34px;
    height:34px;
    border:none;
    border-radius:10px;
    background:#f3f4f6;
    font-weight:700;
    transition:.25s;
}

.qty-btn:hover{
    background:#dc2626;
    color:#fff;
}

.qty-number{
    min-width:25px;
    text-align:center;
    font-weight:700;
}

/* ================= DELETE ================= */

.delete-btn{
    border:none;
    background:#fee2e2;
    color:#dc2626;
    width:40px;
    height:40px;
    border-radius:12px;
    transition:.3s;
}

.delete-btn:hover{
    background:#dc2626;
    color:#fff;
}

/* ================= SUMMARY ================= */

.summary-card{
    background:#fff;
    border-radius:24px;
    padding:30px;
    box-shadow:0 10px 40px rgba(0,0,0,.05);
    position:sticky;
    top:100px;
}

.summary-title{
    font-size:1.2rem;
    font-weight:800;
    margin-bottom:25px;
    color:#111827;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    margin-bottom:15px;
    color:#4b5563;
}

.summary-total{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:20px;
}

.total-price{
    font-size:1.8rem;
    font-weight:800;
    color:#dc2626;
}

/* ================= BUTTON ================= */

.checkout-btn{
    width:100%;
    border:none;
    border-radius:16px;
    padding:16px;
    font-weight:800;
    font-size:1rem;
    margin-top:25px;
    background:linear-gradient(135deg,#dc2626,#991b1b);
    color:#fff;
    transition:.3s;
}

.checkout-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 30px rgba(220,38,38,.35);
}

/* ================= EMPTY ================= */

.empty-cart{
    background:#fff;
    border-radius:24px;
    padding:70px 20px;
    text-align:center;
    box-shadow:0 10px 40px rgba(0,0,0,.05);
}

.empty-cart i{
    font-size:4rem;
    color:#d1d5db;
    margin-bottom:20px;
}

.empty-cart h4{
    font-weight:800;
    margin-bottom:12px;
}

.empty-cart p{
    color:#6b7280;
    margin-bottom:25px;
}

.back-btn{
    padding:14px 30px;
    border-radius:14px;
    font-weight:700;
}

/* ================= RESPONSIVE ================= */

@media(max-width:991px){

    .summary-card{
        margin-top:25px;
        position:relative;
        top:0;
    }
}

@media(max-width:768px){

    .cart-table{
        overflow-x:auto;
    }

    .product-box{
        min-width:250px;
    }

    .cart-title{
        font-size:1.6rem;
    }
}

</style>

<div class="container cart-container">

    <h2 class="cart-title">

        🛒 Giỏ hàng của bạn

    </h2>

    <?php if (empty($cart)): ?>

        <div class="empty-cart">

            <i class="bi bi-cart-x"></i>

            <h4>

                Giỏ hàng đang trống

            </h4>

            <p>

                Hãy thêm sản phẩm yêu thích vào giỏ hàng của bạn

            </p>

            <a
                href="Index.php"
                class="btn btn-dark back-btn"
            >

                Tiếp tục mua sắm

            </a>

        </div>

    <?php else: ?>

        <div class="row g-4">

            <!-- LEFT -->
            <div class="col-lg-8">

                <div class="cart-table">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tổng</th>
                                <th></th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($cart as $key => $item):

                            if (!is_array($item)) {
                            continue;
                                    }

                            $gia =
                                intval($item['gia'] ?? 0);

                            $soluong =
                                intval($item['soluong'] ?? 1);

                            $sum =
                                $gia * $soluong;

                            $total_money += $sum;

                        ?>

                            <tr>

                                <!-- PRODUCT -->
                                <td>

                                    <div class="product-box">

                                        <img
                                            src="<?= htmlspecialchars($item['anh'] ?? 'uploads/no-image.png') ?>"
                                            class="product-img"
                                        >

                                        <div>

                                            <div class="product-name">

                                                <?= htmlspecialchars($item['ten'] ?? '') ?>

                                            </div>

                                            <div class="product-info">

                                                Size:
                                                <?= htmlspecialchars($item['kichco'] ?? '') ?>

                                                <br>

                                                Màu:
                                                <?= htmlspecialchars($item['mausac'] ?? '') ?>

                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <!-- PRICE -->
                                <td class="price">

                                    <?= number_format($gia,0,',','.') ?>đ

                                </td>

                                <!-- QTY -->
                                <td>

                                    <div class="qty-box">

                                        <a href="?action=decrease&key=<?= $key ?>">

                                            <button
                                                type="button"
                                                class="qty-btn"
                                            >

                                                -

                                            </button>

                                        </a>

                                        <span class="qty-number">

                                            <?= $soluong ?>

                                        </span>

                                        <a href="?action=increase&key=<?= $key ?>">

                                            <button
                                                type="button"
                                                class="qty-btn"
                                            >

                                                +

                                            </button>

                                        </a>

                                    </div>

                                </td>

                                <!-- TOTAL -->
                                <td class="price">

                                    <?= number_format($sum,0,',','.') ?>đ

                                </td>

                                <!-- DELETE -->
                                <td>

                                    <a
                                        href="?action=delete&key=<?= $key ?>"
                                        onclick="return confirm('Xóa sản phẩm này?')"
                                    >

                                        <button
                                            type="button"
                                            class="delete-btn"
                                        >

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="col-lg-4">

                <div class="summary-card">

                    <div class="summary-title">

                        🧾 Tổng đơn hàng

                    </div>

                    <div class="summary-row">

                        <span>Tạm tính</span>

                        <strong>

                            <?= number_format($total_money,0,',','.') ?>đ

                        </strong>

                    </div>

                    <div class="summary-row">

                        <span>Phí vận chuyển</span>

                        <strong>

                            Miễn phí

                        </strong>

                    </div>

                    <hr>

                    <div class="summary-total">

                        <div>

                            <strong>

                                Tổng cộng

                            </strong>

                        </div>

                        <div class="total-price">

                            <?= number_format($total_money,0,',','.') ?>đ

                        </div>

                    </div>

                    <a href="Thanh_Toan.php">

                        <button
                            type="button"
                            class="checkout-btn"
                        >

                            THANH TOÁN NGAY

                        </button>

                    </a>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php

$content = ob_get_clean();

/* =========================
   ẨN FOOTER
========================= */

$hide_footer = true;

$show_slider = false;

include __DIR__ . "/Trang_Chu_Includes/Layout.php";

?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


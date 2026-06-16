<?php

session_start();

/* =========================
   CHECK SESSION
========================= */

if (
    !isset($_SESSION['bien_lai'])
    ||
    !isset($_SESSION['bien_lai_products'])
) {

    header("Location: Index.php");
    exit();
}

/* =========================
   GET DATA
========================= */

$bill =
    $_SESSION['bien_lai']
    ?? [];

$products =
    $_SESSION['bien_lai_products']
    ?? [];

$title = "Biên lai đơn hàng";

/* =========================
   TOTAL
========================= */

$tong_tien =
    $bill['tong_tien']
    ?? 0;

?>

<style>

body{
    background:#f3f4f6;
    font-family:'Segoe UI',sans-serif;
}

/* =========================
   WRAPPER
========================= */

.bill-wrapper{
    padding:60px 0;
}

/* =========================
   CARD
========================= */

.bill-card{
    background:#fff;
    border-radius:28px;
    overflow:hidden;
    box-shadow:
    0 10px 40px rgba(0,0,0,.08);
}

/* =========================
   HEADER
========================= */

.bill-header{
    background:#111827;
    color:#fff;
    padding:45px;
    position:relative;
}

.bill-header h1{
    font-size:2.2rem;
    font-weight:800;
    margin-bottom:10px;
}

.bill-header p{
    color:#d1d5db;
    margin:0;
}

.bill-badge{
    position:absolute;
    right:40px;
    top:40px;
    background:#22c55e;
    color:#fff;
    padding:10px 18px;
    border-radius:999px;
    font-weight:700;
    font-size:.9rem;
}

/* =========================
   CONTENT
========================= */

.bill-content{
    padding:45px;
}

/* =========================
   INFO GRID
========================= */

.info-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:20px;
    margin-bottom:35px;
}

.info-card{
    background:#f9fafb;
    border-radius:18px;
    padding:20px;
}

.info-card span{
    display:block;
    font-size:.9rem;
    color:#6b7280;
    margin-bottom:8px;
}

.info-card strong{
    color:#111827;
    font-size:1rem;
}

/* =========================
   ADDRESS
========================= */

.address-box{
    background:#f9fafb;
    border-radius:18px;
    padding:22px;
    margin-bottom:35px;
}

.address-box h5{
    font-weight:800;
    margin-bottom:12px;
    color:#111827;
}

/* =========================
   TABLE
========================= */

.table-wrap{
    border-radius:22px;
    overflow:hidden;
    border:1px solid #e5e7eb;
}

.table{
    margin:0;
}

.table thead{
    background:#111827;
    color:#fff;
}

.table th{
    padding:18px;
    font-size:.95rem;
    font-weight:700;
    border:none;
}

.table td{
    padding:18px;
    vertical-align:middle;
    border-color:#f1f1f1;
}

.product-name{
    font-weight:700;
    color:#111827;
}

.price-red{
    color:#dc2626;
    font-weight:700;
}

/* =========================
   TOTAL
========================= */

.total-box{
    margin-top:35px;
    background:#111827;
    color:#fff;
    border-radius:24px;
    padding:28px;
}

.total-price{
    font-size:2.3rem;
    font-weight:800;
}

/* =========================
   ACTIONS
========================= */

.bill-actions{
    margin-top:40px;
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.btn-dark-custom{
    border:none;
    border-radius:18px;
    padding:15px 24px;
    background:#111827;
    color:#fff;
    font-weight:700;
    text-decoration:none;
    transition:.25s;
}

.btn-dark-custom:hover{
    background:#000;
    color:#fff;
    transform:translateY(-2px);
}

.btn-light-custom{
    border:none;
    border-radius:18px;
    padding:15px 24px;
    background:#f3f4f6;
    color:#111827;
    font-weight:700;
    text-decoration:none;
    transition:.25s;
}

.btn-light-custom:hover{
    background:#e5e7eb;
    color:#111827;
}

/* =========================
   FOOTER
========================= */

.bill-footer{
    margin-top:40px;
    text-align:center;
    color:#6b7280;
    line-height:1.8;
    font-size:.95rem;
}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    .bill-header{
        padding:30px;
    }

    .bill-content{
        padding:25px;
    }

    .info-grid{
        grid-template-columns:1fr;
    }

    .bill-badge{
        position:relative;
        right:auto;
        top:auto;
        display:inline-block;
        margin-top:15px;
    }

    .total-price{
        font-size:1.8rem;
    }
}

/* =========================
   PRINT
========================= */

@media print{

    body{
        background:#fff;
    }

    .bill-actions{
        display:none;
    }

    .bill-card{
        box-shadow:none;
    }
}

</style>

<div class="container bill-wrapper">

    <div class="row justify-content-center">

        <div class="col-lg-11">

            <div class="bill-card">

                <!-- HEADER -->
                <div class="bill-header">

                    <h1>
                        🎉 Đặt hàng thành công
                    </h1>

                    <p>
                        Cảm ơn bạn đã mua hàng tại
                        Men's Wear
                    </p>

                    <div class="bill-badge">

                        ✔ Đã tiếp nhận đơn hàng

                    </div>

                </div>

                <!-- CONTENT -->
                <div class="bill-content">

                    <!-- INFO -->
                    <div class="info-grid">

                        <div class="info-card">

                            <span>Mã đơn hàng</span>

                            <strong>
                                #<?= $bill['ma_dh'] ?? '' ?>
                            </strong>

                        </div>

                        <div class="info-card">

                            <span>Ngày đặt</span>

                            <strong>
                                <?= $bill['ngay_dat'] ?? '' ?>
                            </strong>

                        </div>

                        <div class="info-card">

                            <span>Khách hàng</span>

                            <strong>
                                <?= htmlspecialchars(
                                    $bill['ho_ten']
                                    ?? ''
                                ) ?>
                            </strong>

                        </div>

                        <div class="info-card">

                            <span>Email</span>

                            <strong>
                                <?= htmlspecialchars(
                                    $bill['email']
                                    ?? ''
                                ) ?>
                            </strong>

                        </div>

                        <div class="info-card">

                            <span>Số điện thoại</span>

                            <strong>
                                <?= htmlspecialchars(
                                    $bill['sdt']
                                    ?? ''
                                ) ?>
                            </strong>

                        </div>

                        <div class="info-card">

                            <span>Thanh toán</span>

                            <strong>
                                <?= htmlspecialchars(
                                    $bill['phuong_thuc']
                                    ?? ''
                                ) ?>
                            </strong>

                        </div>

                    </div>

                    <!-- ADDRESS -->
                    <div class="address-box">

                        <h5>
                            📍 Địa chỉ giao hàng
                        </h5>

                        <div>

                            <?= htmlspecialchars(
                                $bill['dia_chi']
                                ?? ''
                            ) ?>

                        </div>

                    </div>

                    <!-- TABLE -->
                    <div class="table-wrap">

                        <div class="table-responsive">

                            <table class="table align-middle">

                                <thead>

                                    <tr>

                                        <th>
                                            Sản phẩm
                                        </th>

                                        <th>
                                            Size
                                        </th>

                                        <th>
                                            Màu
                                        </th>

                                        <th>
                                            SL
                                        </th>

                                        <th>
                                            Đơn giá
                                        </th>

                                        <th>
                                            Thành tiền
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php foreach(
                                    $products
                                    as $item
                                ): ?>

                                    <?php

                                    $sl =
                                        $item['soluong'];

                                    $gia =
                                        $item['gia'];

                                    $thanh_tien =
                                        $sl * $gia;

                                    ?>

                                    <tr>

                                        <td>

                                            <div class="product-name">

                                                <?= htmlspecialchars(
                                                    $item['ten']
                                                ) ?>

                                            </div>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $item['kichco']
                                            ) ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $item['mausac']
                                            ) ?>

                                        </td>

                                        <td>

                                            <?= $sl ?>

                                        </td>

                                        <td>

                                            <?= number_format(
                                                $gia,
                                                0,
                                                ',',
                                                '.'
                                            ) ?>đ

                                        </td>

                                        <td class="price-red">

                                            <?= number_format(
                                                $thanh_tien,
                                                0,
                                                ',',
                                                '.'
                                            ) ?>đ

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <!-- TOTAL -->
                    <div class="total-box">

                        <div
                        class="
                        d-flex
                        justify-content-between
                        align-items-center
                        "
                        >

                            <span
                            class="
                            fs-4
                            fw-bold
                            "
                            >

                                TỔNG THANH TOÁN

                            </span>

                            <span
                            class="total-price"
                            >

                                <?= number_format(
                                    $tong_tien,
                                    0,
                                    ',',
                                    '.'
                                ) ?>đ

                            </span>

                        </div>

                    </div>

                    <!-- ACTION -->
                    <div class="bill-actions">

                        <button
                            onclick="window.print()"
                            class="btn-dark-custom"
                        >

                            🖨 In biên lai

                        </button>

                        <a
                            href="Index.php"
                            class="btn-light-custom"
                        >

                            🛍 Tiếp tục mua sắm

                        </a>

                    </div>

                    <!-- FOOTER -->
                    <div class="bill-footer">

                        Cảm ơn bạn đã tin tưởng
                        và mua sắm tại
                        <strong>Men's Wear</strong>.

                        <br>

                        Chúng tôi sẽ liên hệ
                        xác nhận đơn hàng
                        trong thời gian sớm nhất.

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

/* =========================
   CLEAR SESSION
========================= */

unset($_SESSION['cart']);

unset($_SESSION['bien_lai_products']);

?>
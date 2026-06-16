<?php
session_start();
// 1. Kết nối cơ sở dữ liệu
include "Trang_Chu_Includes/connect_db.inc";



// 3. Lấy dữ liệu Danh mục (MaDM 1, 2, 3) từ SQL
$sql_dm = "SELECT * FROM danhmuc WHERE MaDM IN (1, 2, 3)";
$result_dm = mysqli_query($conn, $sql_dm);

$title = "Trang chủ";
ob_start();
?>

<style>
   
    /* --- PHẦN 2: KHÁM PHÁ TRANG PHỤC (DỮ LIỆU SQL) --- */
    .section-discovery {
        padding-top: 50px;
        padding-bottom: 50px;
        background: #fff;
    }

    .section-title-vstyle {
        font-weight: 800;
        text-align: center;
        margin-bottom: 40px;
        letter-spacing: 2px;
    }

    .cate-card {
        display: block;
        text-decoration: none !important;
        color: #1a1a1a;
        transition: 0.3s;
        overflow: hidden;
    }

    .cate-img-wrapper {
        width: 100%;
        height: 500px; /* Chiều cao cố định để các cột đều nhau */
        overflow: hidden;
    }

    .cate-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.6s ease;
    }

    .cate-card:hover .cate-img-wrapper img {
        transform: scale(1.05);
    }

    .cate-title-text {
        text-align: center;
        padding: 20px;
        font-weight: 700;
        background: #fff;
    }

    @media (max-width: 768px) {
        .carousel-inner { height: 60vw; }
        .cate-img-wrapper { height: 350px; }
        .carousel-caption { display: none; }
    }
</style>



<section class="container section-discovery">
    <h2 class="section-title-vstyle text-uppercase">Khám phá trang phục</h2>
    <div class="row">
        <?php 
        if ($result_dm):
            while($row = mysqli_fetch_assoc($result_dm)): 
                // Logic điều hướng file: Nếu MaDM là 1 thì nhảy tới TrangPhuc_He.php
                $link_dich = "DanhMucSanPham.php?id=" . $row['MaDM'];
                if($row['MaDM'] == 1) $link_dich = "TrangPhuc_He.php";
                if($row['MaDM'] == 2) $link_dich = "TrangPhuc_Dong.php";
                if($row['MaDM'] == 3) $link_dich = "TrangPhuc_Quan.php";

                
                // Gán ảnh tương ứng cho danh mục
                $anh_tmp = "https://via.placeholder.com/400x550";
                if($row['MaDM'] == 1) $anh_tmp = "img/aoxuanhe/sanpham1.1.jpg"; //
                if($row['MaDM'] == 2) $anh_tmp = "img/aothudong/sanpham1.1.jpg"; //
                if($row['MaDM'] == 3) $anh_tmp = "img/quan/sanpham1.1.jpg"; //
        ?>
            <div class="col-md-4 mb-4">
                <a href="<?= $link_dich ?>" class="cate-card shadow-sm">
                    <div class="cate-img-wrapper">
                        <img src="<?= $anh_tmp ?>" alt="<?= htmlspecialchars($row['TenDM']) ?>">
                    </div>
                    <div class="cate-title-text text-uppercase">
                        <?= htmlspecialchars($row['TenDM']) ?>
                    </div>
                </a>
            </div>
        <?php endwhile; endif; ?>
    </div>
</section>
<?php include 'chatbot.php'; ?>
<?php
$content = ob_get_clean();
include "Trang_Chu_Includes/Layout.php";
?>

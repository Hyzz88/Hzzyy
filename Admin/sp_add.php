<?php
ob_start();

include 'check_admin.php';
include '../Trang_Chu_Includes/connect_db.inc';

mysqli_set_charset($conn, "utf8mb4");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ===== DỮ LIỆU =====
    $ten  = trim($_POST['TenSP'] ?? '');
    $gia  = (float)($_POST['GiaBan'] ?? 0);
    $mota = trim($_POST['MoTa'] ?? '');
    $maDM = (int)($_POST['MaDM'] ?? 0);

    // ===== SIZE =====
    $sizeQuanAo = trim($_POST['SizeQuanAo'] ?? '');
    $sizeGiay   = trim($_POST['SizeGiay'] ?? '');

    // Chỉ lấy 1 loại size
    $size = !empty($sizeQuanAo)
        ? $sizeQuanAo
        : $sizeGiay;

    // ===== MÀU =====
    $mauSac = trim($_POST['MauSac'] ?? 'Mặc định');

    if ($mauSac == '') {
        $mauSac = 'Mặc định';
    }

    // ===== SỐ LƯỢNG =====
    $soLuong = max(0, (int)($_POST['SoLuong'] ?? 0));

    // ===== VALIDATE =====
    if ($ten == '') {
        $error = "Tên sản phẩm không được để trống";
    }

    // ===== NHÀ CUNG CẤP =====
    $maNCC = 1;

    $rs = mysqli_query(
        $conn,
        "SELECT MaNCC FROM nhacungcap LIMIT 1"
    );

    if ($rs && mysqli_num_rows($rs) > 0) {

        $row = mysqli_fetch_assoc($rs);

        $maNCC = (int)$row['MaNCC'];
    }

    // ===== UPLOAD ẢNH =====
    $anh = "";

    if (
        isset($_FILES['AnhDaiDien']) &&
        $_FILES['AnhDaiDien']['error'] == 0
    ) {

        $uploadDir = __DIR__ . '/../uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmp  = $_FILES['AnhDaiDien']['tmp_name'];
        $fileName = basename($_FILES['AnhDaiDien']['name']);
        $fileSize = $_FILES['AnhDaiDien']['size'];

        $fileName = preg_replace(
            '/[^A-Za-z0-9.\-_]/',
            '_',
            $fileName
        );

        $ext = strtolower(
            pathinfo($fileName, PATHINFO_EXTENSION)
        );

        $allow = ['jpg','jpeg','png','gif','webp'];

        if (!in_array($ext, $allow)) {
            $error = "Chỉ cho phép JPG, PNG, GIF, WEBP";
        }

        if ($fileSize > 2 * 1024 * 1024) {
            $error = "Ảnh tối đa 2MB";
        }

        if ($error == "") {

            $newName =
                time() . '_' . uniqid() . '.' . $ext;

            if (
                move_uploaded_file(
                    $fileTmp,
                    $uploadDir . $newName
                )
            ) {

                $anh = 'uploads/' . $newName;

            } else {

                $error = "Upload ảnh thất bại";
            }
        }
    }

    // ===== INSERT =====
    if ($error == "") {

        $sql = "
        INSERT INTO sanpham
        (
            TenSP,
            MaDM,
            MaNCC,
            MoTaChiTiet,
            GiaBan,
            AnhDaiDien
        )
        VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die('SQL lỗi: ' . $conn->error);
        }

        $stmt->bind_param(
            "siisds",
            $ten,
            $maDM,
            $maNCC,
            $mota,
            $gia,
            $anh
        );

        if ($stmt->execute()) {

            $maSP = $stmt->insert_id;

            $stmt->close();

            // ===== UPDATE SIZE =====
            $hasSizeColumn =
                mysqli_num_rows(
                    mysqli_query(
                        $conn,
                        "SHOW COLUMNS FROM sanpham LIKE 'Size'"
                    )
                ) > 0;

            $hasSoLuongColumn =
                mysqli_num_rows(
                    mysqli_query(
                        $conn,
                        "SHOW COLUMNS FROM sanpham LIKE 'SoLuong'"
                    )
                ) > 0;

            if (
                $hasSizeColumn &&
                $hasSoLuongColumn
            ) {

                $stockStmt = $conn->prepare(
                    "
                    UPDATE sanpham
                    SET Size = ?, SoLuong = ?
                    WHERE MaSP = ?
                    "
                );

                $stockStmt->bind_param(
                    "sii",
                    $size,
                    $soLuong,
                    $maSP
                );

                $stockStmt->execute();

                $stockStmt->close();
            }

            // ===== BIẾN THỂ =====
            $checkVariantTable = mysqli_query(
                $conn,
                "SHOW TABLES LIKE 'bienthesp'"
            );

            if (
                $size != '' &&
                mysqli_num_rows($checkVariantTable) > 0
            ) {

                $variantSql = "
                INSERT INTO bienthesp
                (
                    MaSP,
                    KichCo,
                    MauSac,
                    SoLuongTon
                )
                VALUES (?, ?, ?, ?)
                ";

                $variantStmt =
                    $conn->prepare($variantSql);

                if ($variantStmt) {

                    $variantStmt->bind_param(
                        "issi",
                        $maSP,
                        $size,
                        $mauSac,
                        $soLuong
                    );

                    $variantStmt->execute();

                    $variantStmt->close();
                }
            }

            header("Location: QuanLySanPham.php");
            exit();

        } else {

            $error = "Lỗi execute: " . $stmt->error;
        }
    }
}
?>

<style>

.card-custom{
    border:none;
    border-radius:18px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.form-control,
.form-select{
    border-radius:12px;
    padding:10px;
}

.preview-img{
    width:140px;
    height:140px;
    object-fit:cover;
    border-radius:14px;
    border:1px solid #ddd;
    display:none;
}

.btn-success{
    border-radius:12px;
    padding:10px 24px;
}

.disabled-input{
    background:#e9ecef !important;
    pointer-events:none;
}

</style>

<div class="container py-4">

    <div class="card card-custom p-4">

        <h3 class="mb-4 text-success">
            ➕ Thêm sản phẩm mới
        </h3>

        <?php if($error != ""): ?>

            <div class="alert alert-danger">
                <?= $error ?>
            </div>

        <?php endif; ?>

        <form
            method="post"
            enctype="multipart/form-data"
        >

            <!-- TÊN -->
            <div class="mb-3">

                <label class="form-label fw-bold">
                    Tên sản phẩm
                </label>

                <input
                    type="text"
                    name="TenSP"
                    class="form-control"
                    required
                >

            </div>

            <!-- GIÁ + DANH MỤC -->
            <div class="row mb-3">

                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Giá bán
                    </label>

                    <input
                        type="number"
                        name="GiaBan"
                        class="form-control"
                        required
                    >

                </div>

                <!-- DANH MỤC -->
                <div class="col-md-6">

                    <label class="form-label fw-bold">
                        Danh mục
                    </label>

                    <select
                        name="MaDM"
                        id="danhMuc"
                        class="form-select"
                    >

                        <?php

                        $dms = mysqli_query(
                            $conn,
                            "SELECT * FROM danhmuc"
                        );

                        while($dm = mysqli_fetch_assoc($dms)) {

                            // ===== TÊN DANH MỤC =====
                            $tenDM =
                                mb_strtolower(
                                    $dm['TenDM'],
                                    'UTF-8'
                                );

                            // ===== MẶC ĐỊNH =====
                            $loai = "khac";

                            // ===== QUẦN ÁO =====
                            if(

                                strpos($tenDM, 'trang phục') !== false

                                ||

                                strpos($tenDM, 'áo') !== false

                                ||

                                strpos($tenDM, 'quần') !== false

                                ||

                                strpos($tenDM, 'đông') !== false

                                ||

                                strpos($tenDM, 'hè') !== false

                            ){
                                $loai = "quanao";
                            }

                            // ===== GIÀY DÉP =====
                            if(

                                strpos($tenDM, 'giày') !== false

                                ||

                                strpos($tenDM, 'dép') !== false

                            ){
                                $loai = "giay";
                            }

                            echo "
                            <option
                                value='{$dm['MaDM']}'
                                data-loai='{$loai}'
                            >
                                {$dm['TenDM']}
                            </option>
                            ";
                        }

                        ?>

                    </select>

                </div>

            </div>

            <!-- SIZE -->
            <div class="row mb-3">

                <!-- SIZE QUẦN ÁO -->
                <div class="col-md-3">

                    <label class="form-label fw-bold">
                        Size quần áo
                    </label>

                    <select
                        name="SizeQuanAo"
                        id="sizeQuanAo"
                        class="form-select"
                    >

                        <option value="">
                            -- Chọn size --
                        </option>

                        <option>S</option>
                        <option>M</option>
                        <option>L</option>
                        <option>XL</option>
                        <option>XXL</option>

                    </select>

                </div>

                <!-- SIZE GIÀY -->
                <div class="col-md-3">

                    <label class="form-label fw-bold">
                        Size giày
                    </label>

                    <input
                        type="number"
                        name="SizeGiay"
                        id="sizeGiay"
                        class="form-control"
                        placeholder="VD: 38"
                    >

                </div>

                <!-- MÀU -->
                <div class="col-md-3">

                    <label class="form-label fw-bold">
                        Màu sắc
                    </label>

                    <input
                        type="text"
                        name="MauSac"
                        class="form-control"
                        value="Mặc định"
                    >

                </div>

                <!-- TỒN -->
                <div class="col-md-3">

                    <label class="form-label fw-bold">
                        Tồn kho
                    </label>

                    <input
                        type="number"
                        name="SoLuong"
                        class="form-control"
                        value="0"
                    >

                </div>

            </div>

            <!-- ẢNH -->
            <div class="mb-3">

                <label class="form-label fw-bold">
                    Ảnh đại diện
                </label>

                <input
                    type="file"
                    name="AnhDaiDien"
                    class="form-control"
                    accept="image/*"
                    onchange="previewImage(event)"
                >

                <img
                    id="preview"
                    class="preview-img mt-3"
                >

            </div>

            <!-- MÔ TẢ -->
            <div class="mb-4">

                <label class="form-label fw-bold">
                    Mô tả sản phẩm
                </label>

                <textarea
                    name="MoTa"
                    class="form-control"
                    rows="5"
                ></textarea>

            </div>

            <button
                type="submit"
                class="btn btn-success"
            >
                💾 Lưu sản phẩm
            </button>

        </form>

    </div>

</div>

<script>

// ===== PREVIEW ẢNH =====
function previewImage(event){

    const preview =
        document.getElementById('preview');

    preview.src =
        URL.createObjectURL(
            event.target.files[0]
        );

    preview.style.display = 'block';
}

// ===== DOM =====
const danhMuc =
    document.getElementById('danhMuc');

const sizeQuanAo =
    document.getElementById('sizeQuanAo');

const sizeGiay =
    document.getElementById('sizeGiay');

// ===== AUTO SIZE =====
function toggleSizeFields(){

    // ===== OPTION ĐANG CHỌN =====
    const selectedOption =

        danhMuc.options[
            danhMuc.selectedIndex
        ];

    // ===== LẤY data-loai =====
    const loai =

        selectedOption.getAttribute(
            'data-loai'
        );

    console.log(loai);

    // ===== QUẦN ÁO =====
    if(loai === 'quanao'){

        // MỞ SIZE QUẦN ÁO
        sizeQuanAo.disabled = false;

        // KHÓA SIZE GIÀY
        sizeGiay.disabled = true;

        // RESET
        sizeGiay.value = '';

        // CSS
        sizeGiay.classList.add(
            'disabled-input'
        );

        sizeQuanAo.classList.remove(
            'disabled-input'
        );
    }

    // ===== GIÀY DÉP =====
    else if(loai === 'giay'){

        // MỞ SIZE GIÀY
        sizeGiay.disabled = false;

        // KHÓA SIZE QUẦN ÁO
        sizeQuanAo.disabled = true;

        // RESET
        sizeQuanAo.value = '';

        // CSS
        sizeQuanAo.classList.add(
            'disabled-input'
        );

        sizeGiay.classList.remove(
            'disabled-input'
        );
    }

    // ===== KHÁC =====
    else{

        sizeQuanAo.disabled = false;
        sizeGiay.disabled = false;

        sizeQuanAo.classList.remove(
            'disabled-input'
        );

        sizeGiay.classList.remove(
            'disabled-input'
        );
    }
}

// ===== CHANGE =====
danhMuc.addEventListener(
    'change',
    toggleSizeFields
);

// ===== LOAD =====
window.onload = toggleSizeFields;

</script>

<?php
$content = ob_get_clean();

include 'layout_admin.php';
?>
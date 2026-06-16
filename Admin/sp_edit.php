<?php
ob_start();
include 'check_admin.php';
include __DIR__ . '/../Trang_Chu_Includes/connect_db.inc';

/* ===== LẤY ID SẢN PHẨM ===== */
if (!isset($_GET['id'])) {
    header("Location: QuanLySanPham.php");
    exit();
}
$maSP = intval($_GET['id']);

/* ===== LẤY THÔNG TIN SẢN PHẨM ===== */
$stmtSP = $conn->prepare("SELECT * FROM sanpham WHERE MaSP = ? LIMIT 1");
$stmtSP->bind_param("i", $maSP);
$stmtSP->execute();
$sp = $stmtSP->get_result()->fetch_assoc();
$stmtSP->close();

if (!$sp) {
    echo "<div class='alert alert-danger'>Không tìm thấy sản phẩm</div>";
    exit();
}

/* ===== KHỞI TẠO BIẾN ===== */
$tenSP   = $sp['TenSP'];
$moTa    = $sp['MoTaChiTiet'];
$giaBan  = $sp['GiaBan'];
$maDM    = $sp['MaDM'];
$anhCu   = $sp['AnhDaiDien'];
$anhPreview = $anhCu ? '../' . ltrim(str_replace('\\', '/', $anhCu), '/') : 'https://placehold.co/200x200?text=No+Image';

$errors = [];

/* ===== XỬ LÝ SUBMIT ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tenSP  = $_POST['TenSP'] ?? '';
    $moTa   = $_POST['MoTaChiTiet'] ?? '';
    $giaBan = (float)($_POST['GiaBan'] ?? 0);
    $maDM   = (int)($_POST['MaDM'] ?? 0);

    /* VALIDATE */
    if (empty($tenSP)) $errors['TenSP'] = "Tên sản phẩm không được để trống.";
    if (empty($moTa)) $errors['MoTa'] = "Mô tả không được để trống.";
    if ($giaBan < 1) $errors['GiaBan'] = "Giá phải lớn hơn 0.";

    /* ===== UPLOAD ẢNH ===== */
    $anh = $anhCu;

    if (!empty($_FILES['Anh']['name'])) {

        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = preg_replace('/[^A-Za-z0-9.\-_]/', '_', basename($_FILES['Anh']['name']));
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allow = ['jpg','jpeg','png','gif','webp'];

        if (!in_array($ext, $allow)) {
            $errors['Anh'] = "Chi cho phep JPG, PNG, GIF, WEBP";
        }

        $filename = time() . '_' . uniqid() . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (empty($errors['Anh']) && move_uploaded_file($_FILES['Anh']['tmp_name'], $targetPath)) {
            $anh = 'uploads/' . $filename;
        } elseif (empty($errors['Anh'])) {
            $errors['Anh'] = "Upload ảnh thất bại";
        }
    }

    /* ===== UPDATE ===== */
    if (empty($errors)) {

        $sqlUpdate = "UPDATE sanpham SET TenSP = ?, MoTaChiTiet = ?, GiaBan = ?, MaDM = ?, AnhDaiDien = ? WHERE MaSP = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ssdisi", $tenSP, $moTa, $giaBan, $maDM, $anh, $maSP);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        header("Location: QuanLySanPham.php");
        exit();
    }
}
?>

<script src="ckeditor/ckeditor.js"></script>

<div class="container">
    <h4 class="text-center mt-4">CHỈNH SỬA SẢN PHẨM</h4>
    <hr>

    <form method="POST" enctype="multipart/form-data">

        <!-- TÊN -->
        <div class="form-group row">
            <label class="col-md-2">Tên sản phẩm</label>
            <div class="col-md-10">
                <input type="text" name="TenSP" class="form-control" value="<?= htmlspecialchars($tenSP) ?>">
                <span class="text-danger"><?= $errors['TenSP'] ?? '' ?></span>
            </div>
        </div>

        <!-- MÔ TẢ -->
        <div class="form-group row">
            <label class="col-md-2">Mô tả</label>
            <div class="col-md-10">
                <textarea name="MoTaChiTiet" id="mota" class="form-control"><?= htmlspecialchars($moTa) ?></textarea>
                <span class="text-danger"><?= $errors['MoTa'] ?? '' ?></span>
            </div>
        </div>

        <!-- ẢNH -->
        <div class="form-group row">
            <label class="col-md-2">Ảnh</label>
            <div class="col-md-10">
                <input type="file" name="Anh" id="AnhInput" class="form-control" accept="image/*">

                <img id="preview"
                     src="<?= htmlspecialchars($anhPreview) ?>"
                     style="margin-top:10px; max-width:200px; border:1px solid #ccc;">

                <span class="text-danger"><?= $errors['Anh'] ?? '' ?></span>
            </div>
        </div>

        <!-- GIÁ -->
        <div class="form-group row">
            <label class="col-md-2">Giá</label>
            <div class="col-md-10">
                <input type="number" name="GiaBan" class="form-control" value="<?= $giaBan ?>">
                <span class="text-danger"><?= $errors['GiaBan'] ?? '' ?></span>
            </div>
        </div>

        <!-- DANH MỤC -->
        <div class="form-group row">
            <label class="col-md-2">Danh mục</label>
            <div class="col-md-10">
                <select name="MaDM" class="form-control">
                    <?php
                    $dm = mysqli_query($conn, "SELECT * FROM danhmuc");
                    while ($r = mysqli_fetch_assoc($dm)) {
                        $sel = ($maDM == $r['MaDM']) ? "selected" : "";
                        echo "<option value='{$r['MaDM']}' $sel>{$r['TenDM']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <input type="submit" class="btn btn-primary" value="Cập nhật">
        <a href="QuanLySanPham.php" class="btn btn-secondary">Trở về</a>
    </form>
</div>

<script>
CKEDITOR.replace('mota');

// preview ảnh
document.getElementById('AnhInput').onchange = e => {
    document.getElementById('preview').src = URL.createObjectURL(e.target.files[0]);
};
</script>

<?php
$content = ob_get_clean();
$title = "Sửa sản phẩm";
include 'layout_admin.php';
?>

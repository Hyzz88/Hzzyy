<?php
// BẮT BUỘC: Phải có ob_start() ở đầu trang để dùng được ob_get_clean() ở cuối trang
ob_start(); 
?>
<script src="ckeditor/ckeditor.js"></script>
<script src="ckfinder/ckfinder.js"></script>
<?php
include '../Trang_Chu_Includes/connect_db.inc';

// Khởi tạo các biến để tránh lỗi "Undefined variable" khi load trang lần đầu
$tenSach = $moTa = $ngayCapNhat = $soLuongBan = $giaBan = $maCD = $maNXB = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenSach = $_POST['TenSach'] ?? '';
    $moTa = $_POST['MoTa'] ?? '';
    $ngayCapNhat = $_POST['NgayCapNhat'];
    $soLuongBan = $_POST['SoLuongBan'] ?? '';
    $giaBan = $_POST['GiaBan'] ?? '';
    $maCD = $_POST['MaCD'] ?? '';
    $maNXB = $_POST['MaNXB'] ?? '';

    // Kiểm tra lỗi
    if (empty($tenSach)) $errors['TenSach'] = "Tên sách không được để trống.";
    if (empty($moTa)) $errors['MoTa'] = "Mô tả không được để trống.";
    if (empty($soLuongBan) || $soLuongBan < 1) $errors['SoLuongBan'] = "Số lượng phải lớn hơn 0.";
    if (empty($giaBan) || $giaBan < 1) $errors['GiaBan'] = "Giá bán phải lớn hơn 0.";
    if ($maCD == 0) $errors['ChuDe'] = "Hãy chọn Chủ đề.";
    if ($maNXB == 0) $errors['NhaXuatBan'] = "Hãy chọn Nhà xuất bản.";

   
$uploadDir = __DIR__ . '/../Images/'; 
$filename = time() . '_' . basename($_FILES['AnhBia']['name']);
$targetPath = $uploadDir . $filename;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (move_uploaded_file($_FILES['AnhBia']['tmp_name'], $targetPath)) {
    $anhBia = $filename;
} else {
    $errors['AnhBia'] = "Upload ảnh thất bại";
}


    // Nếu không có lỗi thì INSERT 
    if (empty($errors)) {
        $sql = "INSERT INTO Sach (TenSach, MoTa, AnhBia, NgayCapNhat, SoLuongBan, GiaBan, MaCD, MaNXB) 
                VALUES ('$tenSach', '$moTa', '$anhBia', '$ngayCapNhat', '$soLuongBan', '$giaBan', '$maCD', '$maNXB')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<div class='alert alert-success'>Thêm mới thành công!</div>";
        } else {
            echo "<div class='alert alert-danger'>Lỗi SQL: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<div class="container">
    <h4 class="text-center mt-4">THÊM MỚI SÁCH</h4>
    <hr />
    <form class="form-horizontal" method="POST" action="" enctype="multipart/form-data">
        <div class="form-group row">
            <label class="control-label col-md-2">Tên sách</label>
            <div class="col-md-10">
                <input type="text" name="TenSach" class="form-control" value="<?= htmlspecialchars($tenSach) ?>">
                <span class="text-danger"><?= $errors['TenSach'] ?? '' ?></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-md-2">Mô tả</label>
            <div class="col-md-10">
                <textarea name="MoTa" id="mota" class="form-control"><?= htmlspecialchars($moTa) ?></textarea>
                <script>
                    CKEDITOR.replace('mota',{
                        filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
                        filebrowserUploadUrl:
                        '../ckfinder/core/connector/php/connector/php?command=QuickUpload&type=Files',
                        filebrowserImageUploadUrl:
                        '../ckfinder/core/connector/php/connector/php?command=QuickUpload&type=Images'
                    });
                </script>

                <span class="text-danger"><?= $errors['MoTa'] ?? '' ?></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-md-2">Ảnh bìa</label>
            <div class="col-md-10">
                <input type="file" name="AnhBia" id="AnhBiaInput" class="form-control" accept="image/*">
                
                <div style="margin-top: 10px;">
                    <img id="HinhXemTruoc" src="#" alt="Hình xem trước" style="display:none; max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                </div>
                
                <span class="text-danger"><?= $errors['AnhBia'] ?? '' ?></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-md-2">Ngày cập nhật</label>
            <div class="col-md-10">
                <input type="date" name="NgayCapNhat" class="form-control" value="<?= $ngayCapNhat ?: date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-md-2">Số lượng</label>
            <div class="col-md-10">
                <input type="number" name="SoLuongBan" class="form-control" min="1" value="<?= htmlspecialchars($soLuongBan ?: '1') ?>">
                <span class="text-danger"><?= $errors['SoLuongBan'] ?? '' ?></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-md-2">Giá bán</label>
            <div class="col-md-10">
                <input type="number" name="GiaBan" class="form-control" min="1" value="<?= htmlspecialchars($giaBan ?: '1') ?>">
                <span class="text-danger"><?= $errors['GiaBan'] ?? '' ?></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-md-2">Chủ đề</label>
            <div class="col-md-10">
                <select name="MaCD" class="form-control">
                    <option value="0">Chọn chủ đề</option>
                    <?php
                    $cdQuery = "SELECT MaCD, TenChuDe FROM ChuDe";
                    $cdResult = mysqli_query($conn, $cdQuery);
                    while ($row = mysqli_fetch_assoc($cdResult)) {
                        $selected = ($maCD == $row['MaCD']) ? "selected" : "";
                        echo "<option value='".$row['MaCD']."' $selected>".$row['TenChuDe']."</option>";
                    }
                    ?>
                </select>
                <span class="text-danger"><?= $errors['ChuDe'] ?? '' ?></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-md-2">Nhà xuất bản</label>
            <div class="col-md-10">
                <select name="MaNXB" class="form-control">
                    <option value="0">Chọn NXB</option>
                    <?php
                    $nxbQuery = "SELECT MaNXB, TenNXB FROM NhaXuatBan";
                    $nxbResult = mysqli_query($conn, $nxbQuery);
                    while ($row = mysqli_fetch_assoc($nxbResult)) {
                        $selected = ($maNXB == $row['MaNXB']) ? "selected" : "";
                        echo "<option value='".$row['MaNXB']."' $selected>".$row['TenNXB']."</option>";
                    }
                    ?>
                </select>
                <span class="text-danger"><?= $errors['NhaXuatBan'] ?? '' ?></span>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-offset-2 col-md-10">
                <input type="submit" value="Lưu" class="btn btn-success">
            </div>
        </div>
    </form>
    <hr>
    <a href="quanlysach.php" class="btn btn-link">Trở về</a>
</div>

<script>
    const input = document.getElementById('AnhBiaInput');
    const preview = document.getElementById('HinhXemTruoc');

    input.onchange = evt => {
        const [file] = input.files;
        if (file) {
            // Tạo đường dẫn tạm thời cho file vừa chọn và hiển thị lên thẻ img
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    }
</script>

<?php
$content = ob_get_clean(); 
$title = "Thêm sách mới";
include "layout_volethanhkiet_admin.php"; 
?>
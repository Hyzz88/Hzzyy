<?php
ob_start();
include 'check_admin.php';
include '../Trang_Chu_Includes/connect_db.inc';

$error = '';
$success = '';

/* 1. XỬ LÝ LƯU DỮ LIỆU (THÊM & SỬA) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnLuu'])) {
    $tenDM = trim($_POST['TenDM']);
    $maDM  = isset($_POST['MaDM']) ? (int)$_POST['MaDM'] : 0;
    $maDMCong = (!empty($_POST['MaDMCong'])) ? (int)$_POST['MaDMCong'] : null;

    if (empty($tenDM)) {
        $error = "Bạn chưa nhập tên danh mục!";
    } else {
        if ($maDM > 0) {
            // Cập nhật
            $sql = "UPDATE danhmuc SET TenDM = ?, MaDMCong = ? WHERE MaDM = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $tenDM, $maDMCong, $maDM);
        } else {
            // Thêm mới
            $sql = "INSERT INTO danhmuc (TenDM, MaDMCong) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $tenDM, $maDMCong);
        }

        if ($stmt->execute()) {
            $success = "Thao tác thành công!";
            // Reset form sau khi thêm
            if ($maDM == 0) { $tenDM = ""; $maDMCong = null; }
        } else {
            $error = "Lỗi hệ thống: " . $stmt->error;
        }
    }
}

/* 2. XỬ LÝ XÓA */
if (isset($_GET['delete'])) {
    $idDel = (int)$_GET['delete'];
    $conn->query("DELETE FROM danhmuc WHERE MaDM = $idDel");
    header("Location: QuanLyDanhMuc.php");
    exit;
}

/* 3. LẤY DỮ LIỆU ĐỂ SỬA */
$editData = ['MaDM' => '', 'TenDM' => '', 'MaDMCong' => ''];
if (isset($_GET['edit'])) {
    $idEdit = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM danhmuc WHERE MaDM = $idEdit");
    if ($res->num_rows > 0) $editData = $res->fetch_assoc();
}

/* 4. LẤY DANH SÁCH HIỂN THỊ (ID TĂNG DẦN) */
$all_dm = $conn->query("SELECT d1.*, d2.TenDM as TenCha FROM danhmuc d1 LEFT JOIN danhmuc d2 ON d1.MaDMCong = d2.MaDM ORDER BY d1.MaDM ASC");
?>

<div class="container-fluid px-4">
    <h2 class="mt-4 mb-4 fw-bold text-uppercase">Quản lý Danh mục</h2>

    <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>
    <?php if($success): ?> <div class="alert alert-success"><?= $success ?></div> <?php endif; ?>

    <div class="card mb-4 border-primary shadow-sm">
        <div class="card-header <?= $editData['MaDM'] ? 'bg-warning text-dark' : 'bg-primary text-white' ?> fw-bold">
            <i class="bi bi-plus-circle-fill"></i> 
            <?= $editData['MaDM'] ? "ĐANG SỬA DANH MỤC (ID: ".$editData['MaDM'].")" : "THÊM DANH MỤC MỚI" ?>
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3 align-items-end">
                <input type="hidden" name="MaDM" value="<?= $editData['MaDM'] ?>">
                
                <div class="col-md-5">
                    <label class="form-label fw-bold">Tên danh mục</label>
                    <input type="text" name="TenDM" class="form-control" placeholder="Nhập tên danh mục..." 
                           value="<?= htmlspecialchars($editData['TenDM']) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Danh mục cha</label>
                    <select name="MaDMCong" class="form-select">
                        <option value="">-- Là danh mục gốc (không có cha) --</option>
                        <?php 
                        $list_cha = $conn->query("SELECT * FROM danhmuc WHERE MaDMCong IS NULL");
                        while($cha = $list_cha->fetch_assoc()): 
                            if($editData['MaDM'] == $cha['MaDM']) continue;
                            $sel = ($editData['MaDMCong'] == $cha['MaDM']) ? 'selected' : '';
                        ?>
                            <option value="<?= $cha['MaDM'] ?>" <?= $sel ?>><?= $cha['TenDM'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" name="btnLuu" class="btn <?= $editData['MaDM'] ? 'btn-warning' : 'btn-primary' ?> w-100 fw-bold">
                        <i class="bi bi-save"></i> <?= $editData['MaDM'] ? "CẬP NHẬT" : "THÊM MỚI" ?>
                    </button>
                    <?php if($editData['MaDM']): ?>
                        <a href="QuanLyDanhMuc.php" class="btn btn-link w-100 text-decoration-none mt-2">Hủy lệnh sửa</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white fw-bold">Danh sách danh mục (ID từ nhỏ đến lớn)</div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th width="70">ID</th>
                        <th>Tên danh mục</th>
                        <th>Danh mục cha</th>
                        <th width="160">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $all_dm->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center fw-bold"><?= $row['MaDM'] ?></td>
                        <td class="fw-bold text-primary"><?= htmlspecialchars($row['TenDM']) ?></td>
                        <td>
                            <?php if($row['TenCha']): ?>
                                <span class="badge bg-info text-dark"><?= htmlspecialchars($row['TenCha']) ?></span>
                            <?php else: ?>
                                <span class="text-muted small">Danh mục gốc</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="?edit=<?= $row['MaDM'] ?>" class="btn btn-sm btn-outline-warning">Sửa</a>
                            <a href="?delete=<?= $row['MaDM'] ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Xóa danh mục này sẽ ảnh hưởng đến các sản phẩm bên trong. Xóa?')">Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "layout_admin.php";
?>

<?php
include "check_admin.php";
include '../Trang_Chu_Includes/connect_db.inc';

// Kiểm tra id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: QuanLySanPham.php");
    exit();
}

$id = intval($_GET['id']);

// 1. Lấy danh sách MaBienThe thuộc sản phẩm
$result = mysqli_query($conn, "SELECT MaBienThe FROM bienthesp WHERE MaSP = $id");

while ($row = mysqli_fetch_assoc($result)) {
    $maBienThe = $row['MaBienThe'];

    // 2. Xóa chi tiết đơn hàng trước
    mysqli_query($conn, "DELETE FROM chitietdonhang WHERE MaBienThe = $maBienThe");
}

// 3. Xóa biến thể sản phẩm
mysqli_query($conn, "DELETE FROM bienthesp WHERE MaSP = $id");

// 4. Xóa sản phẩm
mysqli_query($conn, "DELETE FROM sanpham WHERE MaSP = $id");

header("Location: QuanLySanPham.php");
exit();
?>

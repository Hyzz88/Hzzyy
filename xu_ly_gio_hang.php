<?php
session_start();

// Xử lý Thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['add_to_cart']) || isset($_POST['buy_now']))) {
    $id = intval($_POST['MaSP']);
    $ten = $_POST['TenSP'];
    $gia = floatval($_POST['GiaBan']);
    $anh = $_POST['AnhDaiDien'];
    $soluong = intval($_POST['SoLuong'] ?? 1);
    
    // Đồng bộ tên biến: Viết thường hoàn toàn để tránh nhầm lẫn
    $kichco = $_POST['KichCo'] ?? 'N/A'; 
    $mausac = $_POST['MauSac'] ?? 'N/A';

    // Tạo key duy nhất để phân biệt cùng 1 SP nhưng khác Size/Màu
    $cart_key = $id . "_" . md5($kichco . $mausac);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['soluong'] += $soluong;
    } else {
        $_SESSION['cart'][$cart_key] = [
            'id' => $id,
            'ten' => $ten,
            'gia' => $gia,
            'anh' => $anh,
            'soluong' => $soluong,
            'kichco' => $kichco,
            'mausac' => $mausac
        ];
    }

    // Nếu chọn Mua ngay thì đi thẳng tới thanh toán, ngược lại về giỏ hàng
    $redirect = isset($_POST['buy_now']) ? "Thanh_Toan.php" : "Gio_Hang.php";
    header("Location: $redirect");
    exit();
}

// Xử lý Xóa sản phẩm
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $key = $_GET['id'];
    if (isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
    }
    header("Location: Gio_Hang.php");
    exit();
}
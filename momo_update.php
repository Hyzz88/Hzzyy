<?php
include "Trang_Chu_Includes/connect_db.inc";

$MaDH = $_POST['MaDH'];

mysqli_query($conn,"
    UPDATE donhang 
    SET TrangThai='Đã thanh toán'
    WHERE MaDH='$MaDH'
");

echo "OK";
?>
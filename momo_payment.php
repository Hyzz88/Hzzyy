<?php
include "Trang_Chu_Includes/connect_db.inc";

$MaDH = $_GET['MaDH'];
$amount = $_GET['amount'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Thanh toán MoMo</title>

<style>
body{
    font-family: Arial;
    background:#f5f5f5;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.box{
    background:#fff;
    padding:25px;
    border-radius:12px;
    text-align:center;
    width:350px;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}

.qr{
    margin:15px 0;
}

.status{
    color:#ff5722;
    font-weight:bold;
}

.success{
    color:green;
    font-weight:bold;
}

button{
    background:#ff5722;
    color:#fff;
    border:none;
    padding:10px 15px;
    border-radius:6px;
    cursor:pointer;
}
</style>

</head>
<body>

<div class="box">

<h2>Thanh toán MoMo</h2>

<p>Số tiền: <b><?= number_format($amount) ?> đ</b></p>

<!-- QR FAKE -->
<div class="qr">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=ThanhToan_<?= $MaDH ?>">
</div>

<p class="status" id="status">Đang chờ thanh toán...</p>

</div>

<script>

// GIẢ LẬP THANH TOÁN SAU 4 GIÂY
setTimeout(()=>{
    
    fetch("momo_update.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"MaDH=<?= $MaDH ?>"
    })
    .then(r=>r.text())
    .then(res=>{
        document.getElementById("status").innerHTML="✅ Thanh toán thành công";
        document.getElementById("status").className="success";

        // chuyển trang sau 2s
        setTimeout(()=>{
            window.location="thankyou.php?MaDH=<?= $MaDH ?>";
        },2000);
    });

},4000);

</script>

</body>
</html>
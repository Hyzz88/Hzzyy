<?php
session_start();

date_default_timezone_set('Asia/Ho_Chi_Minh');

include "Trang_Chu_Includes/connect_db.inc";

if(!isset($_SESSION['reset_email'])){

    header("Location: Quen_Mat_Khau.php");
    exit();
}

$msg = "";

if(isset($_POST['btn_xacnhan'])){

    $otp = trim($_POST['otp']);

    $email = $_SESSION['reset_email'];

    /* =========================
       THỜI GIAN HIỆN TẠI
    ========================= */

    $currentTime = date("Y-m-d H:i:s");

    /* =========================
       KIỂM TRA OTP
    ========================= */

    $sql = "
    SELECT *
    FROM nguoidung
    WHERE Email = ?
    AND otp_code = ?
    AND otp_expire >= ?
    ";

    $stmt = mysqli_prepare($conn,$sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $email,
        $otp,
        $currentTime
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){

        $_SESSION['otp_verified'] = true;

        header("Location: Doi_Mat_Khau.php");
        exit();

    }else{

        $msg = "Mã OTP không chính xác hoặc đã hết hạn";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Xác nhận OTP</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    background:
    linear-gradient(
        135deg,
        #0f0f0f,
        #1c1c1c,
        #2b2b2b
    );

    font-family:'Be Vietnam Pro',sans-serif;

    overflow:hidden;
    position:relative;
}

/* =========================
   BACKGROUND EFFECT
========================= */

.bg-circle{
    position:absolute;
    border-radius:50%;
    filter:blur(120px);
    opacity:0.4;
}

.bg1{
    width:320px;
    height:320px;
    background:#8b5cf6;
    top:-120px;
    left:-120px;
}

.bg2{
    width:320px;
    height:320px;
    background:#ec4899;
    bottom:-120px;
    right:-120px;
}

/* =========================
   CARD
========================= */

.box{

    width:440px;

    background:
    rgba(255,255,255,0.08);

    backdrop-filter:blur(20px);

    border:
    1px solid rgba(255,255,255,0.12);

    border-radius:30px;

    padding:42px;

    box-shadow:
    0 10px 40px rgba(0,0,0,0.35);

    position:relative;
    z-index:10;
}

/* =========================
   ICON
========================= */

.logo{

    width:80px;
    height:80px;

    margin:auto;

    border-radius:24px;

    background:
    linear-gradient(
        135deg,
        #8b5cf6,
        #ec4899
    );

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:34px;
    color:white;

    margin-bottom:25px;

    box-shadow:
    0 10px 25px rgba(236,72,153,0.35);
}

/* =========================
   TITLE
========================= */

h2{
    color:white;
    text-align:center;
    font-size:32px;
    font-weight:700;
    margin-bottom:12px;
}

.desc{
    text-align:center;
    color:rgba(255,255,255,0.7);
    font-size:14px;
    line-height:1.7;
    margin-bottom:35px;
}

/* =========================
   OTP INPUT
========================= */

.form-group{
    margin-bottom:25px;
}

.label{
    display:block;
    color:white;
    margin-bottom:12px;
    font-size:14px;
    font-weight:500;
}

.input-box{
    position:relative;
}

.input-box input{

    width:100%;
    height:60px;

    border:none;
    outline:none;

    border-radius:18px;

    background:
    rgba(255,255,255,0.08);

    border:
    1px solid rgba(255,255,255,0.08);

    padding:0 22px;

    color:white;

    font-size:22px;
    font-weight:600;
    letter-spacing:8px;

    text-align:center;

    transition:0.3s;
}

.input-box input:focus{

    border:
    1px solid #a855f7;

    box-shadow:
    0 0 0 4px rgba(168,85,247,0.18);
}

.input-box input::placeholder{
    color:rgba(255,255,255,0.35);
    letter-spacing:2px;
    font-size:16px;
}

/* =========================
   BUTTON
========================= */

button{

    width:100%;
    height:58px;

    border:none;
    outline:none;

    border-radius:18px;

    background:
    linear-gradient(
        135deg,
        #8b5cf6,
        #ec4899
    );

    color:white;

    font-size:16px;
    font-weight:600;

    cursor:pointer;

    transition:0.3s;

    box-shadow:
    0 10px 25px rgba(236,72,153,0.35);
}

button:hover{
    transform:translateY(-2px);
    opacity:0.95;
}

/* =========================
   ERROR
========================= */

.error{

    margin-top:20px;

    background:
    rgba(239,68,68,0.12);

    border:
    1px solid rgba(239,68,68,0.25);

    color:#fca5a5;

    padding:14px;

    border-radius:14px;

    text-align:center;

    font-size:14px;
}

/* =========================
   INFO
========================= */

.info{
    margin-top:22px;
    text-align:center;
    color:rgba(255,255,255,0.55);
    font-size:13px;
    line-height:1.7;
}

/* =========================
   FOOTER
========================= */

.footer{
    margin-top:28px;
    text-align:center;
    color:rgba(255,255,255,0.4);
    font-size:13px;
}

/* =========================
   MOBILE
========================= */

@media(max-width:480px){

    .box{
        width:92%;
        padding:32px 24px;
    }

    h2{
        font-size:28px;
    }

    .input-box input{
        letter-spacing:4px;
    }
}

</style>

</head>
<body>

<div class="bg-circle bg1"></div>
<div class="bg-circle bg2"></div>

<div class="box">

<div class="logo">
🔐
</div>

<h2>Xác nhận OTP</h2>

<p class="desc">
Chúng tôi đã gửi mã OTP đến email của bạn.
Vui lòng nhập mã xác minh để tiếp tục đổi mật khẩu.
</p>

<form method="POST">

<div class="form-group">

<label class="label">
Mã OTP
</label>

<div class="input-box">

<input type="text"
       name="otp"
       placeholder="------"
       maxlength="6"
       required>

</div>

</div>

<button name="btn_xacnhan">
    Xác nhận OTP
</button>

</form>

<?php
if($msg != ''){
?>

<div class="error">
    <?= $msg ?>
</div>

<?php
}
?>

<div class="info">
OTP có hiệu lực trong 5 phút.
</div>

<div class="footer">
© 2026 Fashion Store
</div>

</div>

</body>
</html>
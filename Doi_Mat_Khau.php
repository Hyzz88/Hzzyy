<?php
session_start();

date_default_timezone_set('Asia/Ho_Chi_Minh');

include "Trang_Chu_Includes/connect_db.inc";

if(!isset($_SESSION['otp_verified'])){

    header("Location: Quen_Mat_Khau.php");
    exit();
}

$msg = "";

if(isset($_POST['btn_doimatkhau'])){

    $mk1 = trim($_POST['matkhau']);
    $mk2 = trim($_POST['nhaplai']);

    /* =========================
       KIỂM TRA MẬT KHẨU
    ========================= */

    if($mk1 != $mk2){

        $msg = "Mật khẩu không khớp";

    }elseif(strlen($mk1) < 6){

        $msg = "Mật khẩu phải từ 6 ký tự";

    }else{

        /* =========================
           HASH PASSWORD
        ========================= */

        $hash = password_hash(
            $mk1,
            PASSWORD_DEFAULT
        );

        $email = $_SESSION['reset_email'];

        /* =========================
           UPDATE PASSWORD
        ========================= */

        $sql = "
        UPDATE nguoidung
        SET MatKhau = ?,
            otp_code = NULL,
            otp_expire = NULL
        WHERE Email = ?
        ";

        $stmt = mysqli_prepare($conn,$sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $hash,
            $email
        );

        mysqli_stmt_execute($stmt);

        /* =========================
           XÓA SESSION OTP
        ========================= */

        unset($_SESSION['otp_verified']);
        unset($_SESSION['reset_email']);

        /* =========================
           CHUYỂN VỀ LOGIN
        ========================= */

        header("Location: Dang_nhap.php?reset=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Đổi mật khẩu</title>

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
    align-items:center;
    justify-content:center;
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
   BACKGROUND BLUR
========================= */

.bg-circle{
    position:absolute;
    border-radius:50%;
    filter:blur(120px);
    opacity:0.4;
}

.bg1{
    width:300px;
    height:300px;
    background:#8b5cf6;
    top:-100px;
    left:-100px;
}

.bg2{
    width:300px;
    height:300px;
    background:#ec4899;
    bottom:-120px;
    right:-100px;
}

/* =========================
   CARD
========================= */

.box{
    width:420px;
    padding:40px;
    border-radius:28px;

    background:
    rgba(255,255,255,0.08);

    backdrop-filter:blur(20px);

    border:
    1px solid rgba(255,255,255,0.12);

    box-shadow:
    0 10px 40px rgba(0,0,0,0.35);

    position:relative;
    z-index:10;
}

/* =========================
   TITLE
========================= */

.logo{
    width:70px;
    height:70px;
    margin:auto;
    border-radius:20px;

    background:
    linear-gradient(
        135deg,
        #8b5cf6,
        #ec4899
    );

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:28px;
    color:white;
    margin-bottom:25px;

    box-shadow:
    0 10px 25px rgba(236,72,153,0.35);
}

h2{
    color:white;
    text-align:center;
    font-size:30px;
    font-weight:700;
    margin-bottom:10px;
}

.desc{
    color:rgba(255,255,255,0.7);
    text-align:center;
    font-size:14px;
    line-height:1.6;
    margin-bottom:35px;
}

/* =========================
   INPUT
========================= */

.form-group{
    margin-bottom:20px;
}

.label{
    display:block;
    color:white;
    margin-bottom:10px;
    font-size:14px;
    font-weight:500;
}

.input-box{
    position:relative;
}

.input-box input{
    width:100%;
    height:55px;

    border:none;
    outline:none;

    border-radius:16px;

    background:
    rgba(255,255,255,0.08);

    padding:0 18px;

    color:white;
    font-size:15px;

    border:
    1px solid rgba(255,255,255,0.08);

    transition:0.3s;
}

.input-box input:focus{

    border:
    1px solid #a855f7;

    box-shadow:
    0 0 0 4px rgba(168,85,247,0.18);
}

.input-box input::placeholder{
    color:rgba(255,255,255,0.45);
}

/* =========================
   BUTTON
========================= */

button{
    width:100%;
    height:56px;

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

    margin-top:10px;

    box-shadow:
    0 10px 25px rgba(236,72,153,0.35);
}

button:hover{
    transform:translateY(-2px);
    opacity:0.95;
}

/* =========================
   MESSAGE
========================= */

.error{
    margin-top:18px;

    background:
    rgba(239,68,68,0.12);

    border:
    1px solid rgba(239,68,68,0.3);

    color:#fca5a5;

    padding:14px;

    border-radius:14px;

    text-align:center;

    font-size:14px;
}

/* =========================
   FOOTER
========================= */

.footer{
    margin-top:28px;
    text-align:center;
    color:rgba(255,255,255,0.45);
    font-size:13px;
}

/* =========================
   MOBILE
========================= */

@media(max-width:480px){

    .box{
        width:92%;
        padding:30px 22px;
    }

    h2{
        font-size:26px;
    }
}

</style>

</head>
<body>

<div class="bg-circle bg1"></div>
<div class="bg-circle bg2"></div>

<div class="box">

<div class="logo">
🔒
</div>

<h2>Đổi mật khẩu</h2>

<p class="desc">
Tạo mật khẩu mới để bảo vệ tài khoản của bạn an toàn hơn.
</p>

<form method="POST">

<div class="form-group">

<label class="label">
Mật khẩu mới
</label>

<div class="input-box">

<input type="password"
       name="matkhau"
       placeholder="Nhập mật khẩu mới"
       required>

</div>

</div>

<div class="form-group">

<label class="label">
Nhập lại mật khẩu
</label>

<div class="input-box">

<input type="password"
       name="nhaplai"
       placeholder="Nhập lại mật khẩu"
       required>

</div>

</div>

<button name="btn_doimatkhau">
    Đổi mật khẩu
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

<div class="footer">
© 2026 Fashion Store
</div>

</div>

</body>
</html>
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Quên mật khẩu</title>

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

    width:430px;

    background:
    rgba(255,255,255,0.08);

    backdrop-filter:blur(20px);

    border:
    1px solid rgba(255,255,255,0.12);

    border-radius:28px;

    padding:42px;

    box-shadow:
    0 10px 40px rgba(0,0,0,0.35);

    position:relative;
    z-index:10;
}

/* =========================
   LOGO
========================= */

.logo{

    width:75px;
    height:75px;

    margin:auto;

    border-radius:22px;

    background:
    linear-gradient(
        135deg,
        #8b5cf6,
        #ec4899
    );

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:30px;

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
   FORM
========================= */

.form-group{
    margin-bottom:22px;
}

.label{
    display:block;
    color:white;
    font-size:14px;
    margin-bottom:10px;
    font-weight:500;
}

.input-box{
    position:relative;
}

.input-box input{

    width:100%;
    height:58px;

    border:none;
    outline:none;

    border-radius:18px;

    background:
    rgba(255,255,255,0.08);

    border:
    1px solid rgba(255,255,255,0.08);

    padding:0 20px;

    color:white;
    font-size:15px;

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

    margin-top:10px;

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

    margin-top:18px;

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
        padding:32px 24px;
    }

    h2{
        font-size:28px;
    }
}

</style>

</head>
<body>

<div class="bg-circle bg1"></div>
<div class="bg-circle bg2"></div>

<div class="box">

<div class="logo">
✉️
</div>

<h2>Quên mật khẩu</h2>

<p class="desc">
Nhập email đã đăng ký để nhận mã OTP và đặt lại mật khẩu tài khoản của bạn.
</p>

<form action="Gui_OTP.php" method="POST">

<div class="form-group">

<label class="label">
Email đăng ký
</label>

<div class="input-box">

<input type="email"
       name="email"
       placeholder="Nhập email của bạn"
       required>

</div>

</div>

<button type="submit">
    Gửi mã OTP
</button>

</form>

<?php

if(isset($_SESSION['error'])){

?>

<div class="error">
    <?= $_SESSION['error']; ?>
</div>

<?php

unset($_SESSION['error']);

}
?>

<div class="footer">
© 2026 Fashion Store
</div>

</div>

</body>
</html>
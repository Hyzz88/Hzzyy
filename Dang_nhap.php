<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

$title = "Đăng Nhập";

include "Trang_Chu_Includes/connect_db.inc";

$error = "";

/* ================= LOGIN ================= */

if (isset($_POST["dang_nhap"])) {

    $taikhoan = mysqli_real_escape_string($conn, trim($_POST['username']));
    $matkhau = $_POST['password'] ?? "";

    $sql = "SELECT * FROM nguoidung
            WHERE Email = '$taikhoan'
            OR HoTen = '$taikhoan'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {

        if (password_verify($matkhau, $row['MatKhau'])) {

            $_SESSION['user'] = [
                'id'   => $row['MaND'],
                'name' => $row['HoTen'],
                'role' => $row['PhanQuyen'] ?? 'user'
            ];

            // ADMIN
            if (
                !empty($row['PhanQuyen']) &&
                (
                    $row['PhanQuyen'] == 1 ||
                    $row['PhanQuyen'] == 'admin'
                )
            ) {

                $_SESSION['admin'] = [
                    'id' => $row['MaND'],
                    'PhanQuyen' => 1
                ];

                header("Location: Admin/admin.php");
                exit();

            } else {

                header("Location: Index.php");
                exit();
            }

        } else {

            $error = "Mật khẩu không chính xác!";
        }

    } else {

        $error = "Tài khoản hoặc Email không tồn tại!";
    }
}
?>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

body{
    margin:0;
    font-family:'Segoe UI',system-ui,sans-serif;

    background:
    linear-gradient(rgba(15,23,42,.75),rgba(15,23,42,.8)),
    url('img/banner-login.jpg') center/cover no-repeat fixed;
}

/* WRAPPER */
.auth-wrapper{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 15px;
}

/* CARD */
.login-card{
    width:100%;
    max-width:470px;

    background:#fff;

    border-radius:24px;

    overflow:hidden;

    box-shadow:0 30px 90px rgba(0,0,0,.45);

    animation:fadeUp .7s ease;

    position:relative;
}

/* HEADER */
.login-header{
    background:linear-gradient(135deg,#dc2626,#991b1b);

    color:#fff;

    text-align:center;

    padding:38px 25px;
}

.login-header .icon{
    font-size:3rem;
    margin-bottom:10px;
}

.login-header h2{
    margin:0;
    letter-spacing:3px;
    font-weight:800;
}

.login-header p{
    margin-top:8px;
    opacity:.9;
    font-size:.95rem;
}

/* BODY */
.login-body{
    padding:35px;
}

/* LABEL */
.form-label{
    font-size:.78rem;
    font-weight:700;
    color:#6b7280;
    letter-spacing:1px;
    margin-bottom:8px;
}

/* INPUT */
.input-group{
    border-radius:16px;
    overflow:hidden;

    border:1px solid #e5e7eb;

    transition:.3s;
}

.input-group:focus-within{
    border-color:#dc2626;
    box-shadow:0 0 0 4px rgba(220,38,38,.12);
}

.input-group-text{
    background:#f9fafb;
    border:none;
    padding:0 16px;
    color:#6b7280;
}

.form-control{
    border:none;
    background:#f9fafb;
    padding:15px;
    font-size:.95rem;
}

.form-control:focus{
    box-shadow:none;
    background:#fff;
}

/* BUTTON */
.btn-login{
    width:100%;

    border:none;

    border-radius:16px;

    padding:16px;

    font-size:1rem;

    font-weight:800;

    color:#fff;

    background:linear-gradient(135deg,#dc2626,#b91c1c);

    transition:.35s;

    margin-top:10px;
}

.btn-login:hover{
    transform:translateY(-2px);

    box-shadow:0 14px 30px rgba(220,38,38,.4);
}

/* ALERT */
.alert{
    border-radius:14px;
    font-size:.92rem;
}

/* FOOTER */
.login-footer{
    margin-top:25px;
    text-align:center;
}

/* FORGOT */
.forgot-password{
    margin-bottom:18px;
}

.forgot-password a{
    display:inline-flex;
    align-items:center;
    gap:8px;

    text-decoration:none;

    color:#dc2626;

    font-weight:700;

    transition:.3s;
}

.forgot-password a:hover{
    color:#991b1b;
    transform:translateY(-1px);
}

/* DIVIDER */
.divider{
    display:flex;
    align-items:center;
    gap:12px;
    margin:18px 0;
}

.divider span{
    flex:1;
    height:1px;
    background:#e5e7eb;
}

.divider p{
    margin:0;
    font-size:.72rem;
    font-weight:700;
    color:#9ca3af;
    letter-spacing:2px;
}

/* REGISTER */
.register-link{
    font-size:.93rem;
    color:#4b5563;
}

.register-link a{
    color:#dc2626;
    font-weight:800;
    text-decoration:none;
}

.register-link a:hover{
    text-decoration:underline;
}

/* MODAL */
.register-modal{
    position:fixed;
    inset:0;

    background:rgba(0,0,0,.7);

    backdrop-filter:blur(5px);

    display:none;

    align-items:center;
    justify-content:center;

    z-index:99999;

    padding:20px;
}

.register-modal.active{
    display:flex;
}

.register-card{
    width:100%;
    max-width:520px;

    background:#fff;

    border-radius:24px;

    overflow:hidden;

    position:relative;

    animation:fadeUp .4s ease;
}

.close-register{
    position:absolute;
    top:15px;
    right:15px;

    width:42px;
    height:42px;

    border:none;

    border-radius:50%;

    background:#fff;

    cursor:pointer;

    z-index:10;
}

/* ANIMATION */
@keyframes fadeUp{

    from{
        opacity:0;
        transform:translateY(35px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* MOBILE */
@media(max-width:576px){

    .login-body{
        padding:25px 22px;
    }

    .login-header{
        padding:30px 20px;
    }

    .login-header h2{
        font-size:1.5rem;
    }
}

</style>

<!-- ================= LOGIN ================= -->

<div class="auth-wrapper">

    <div class="login-card">

        <!-- HEADER -->
        <div class="login-header">

            <div class="icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <h2>ĐĂNG NHẬP</h2>

            <p>
                Chào mừng quay trở lại hệ thống
            </p>

        </div>

        <!-- BODY -->
        <div class="login-body">

            <?php if($error): ?>

                <div class="alert alert-danger text-center mb-4">
                    <?= $error ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <!-- USER -->
                <div class="mb-3">

                    <label class="form-label">
                        EMAIL / HỌ TÊN
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            placeholder="Nhập email hoặc họ tên"
                            required
                        >

                    </div>

                </div>

                <!-- PASS -->
                <div class="mb-4">

                    <label class="form-label">
                        MẬT KHẨU
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Nhập mật khẩu"
                            required
                        >

                    </div>

                </div>

                <!-- BTN -->
                <button
                    type="submit"
                    name="dang_nhap"
                    class="btn-login"
                >

                    <i class="bi bi-box-arrow-in-right"></i>

                    ĐĂNG NHẬP

                </button>

                <!-- FOOTER -->
                <div class="login-footer">

                    <!-- FORGOT -->
                    <div class="forgot-password">

                        <a href="quen_mat_khau.php">

                            <i class="bi bi-key-fill"></i>

                            Quên mật khẩu?

                        </a>

                    </div>

                    <!-- DIVIDER -->
                    <div class="divider">

                        <span></span>

                        <p>TÀI KHOẢN MỚI</p>

                        <span></span>

                    </div>

                    <!-- REGISTER -->
                    <div class="register-link">

                        Chưa có tài khoản?

                        <a href="#"
                           onclick="openRegisterModal()">

                            Đăng ký ngay

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- ================= REGISTER MODAL ================= -->

<div class="register-modal"
     id="registerModal">

    <div class="register-card">

        <!-- CLOSE -->
        <button class="close-register"
                onclick="closeRegisterModal()">

            <i class="bi bi-x-lg"></i>

        </button>

        <!-- HEADER -->
        <div class="login-header">

            <div class="icon">
                <i class="bi bi-person-plus-fill"></i>
            </div>

            <h2>ĐĂNG KÝ</h2>

            <p>
                Tạo tài khoản mới
            </p>

        </div>

        <!-- BODY -->
        <div class="login-body">

            <form method="POST"
                  action="Dang_ky.php">

                <!-- FULLNAME -->
                <div class="mb-3">

                    <label class="form-label">
                        Họ và tên
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>

                        <input
                            type="text"
                            name="fullname"
                            class="form-control"
                            placeholder="Nhập họ tên"
                            required
                        >

                    </div>

                </div>

                <!-- EMAIL -->
                <div class="mb-3">

                    <label class="form-label">
                        Email
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-envelope-fill"></i>
                        </span>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Nhập email"
                            required
                        >

                    </div>

                </div>

                <!-- PHONE -->
                <div class="mb-3">

                    <label class="form-label">
                        Số điện thoại
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-telephone-fill"></i>
                        </span>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            placeholder="Nhập số điện thoại"
                            required
                        >

                    </div>

                </div>

                <!-- ADDRESS -->
                <div class="mb-3">

                    <label class="form-label">
                        Địa chỉ
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-geo-alt-fill"></i>
                        </span>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="2"
                            placeholder="Nhập địa chỉ"
                            required
                        ></textarea>

                    </div>

                </div>

                <!-- PASS -->
                <div class="mb-3">

                    <label class="form-label">
                        Mật khẩu
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Nhập mật khẩu"
                            required
                        >

                    </div>

                </div>

                <!-- CONFIRM -->
                <div class="mb-4">

                    <label class="form-label">
                        Xác nhận mật khẩu
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-shield-lock-fill"></i>
                        </span>

                        <input
                            type="password"
                            name="confirm_password"
                            class="form-control"
                            placeholder="Nhập lại mật khẩu"
                            required
                        >

                    </div>

                </div>

                <!-- BTN -->
                <button
                    type="submit"
                    class="btn-login"
                >

                    <i class="bi bi-person-plus-fill"></i>

                    TẠO TÀI KHOẢN

                </button>

            </form>

        </div>

    </div>

</div>

<script>

function openRegisterModal(){

    document
    .getElementById('registerModal')
    .classList
    .add('active');
}

function closeRegisterModal(){

    document
    .getElementById('registerModal')
    .classList
    .remove('active');
}

// CLOSE OUTSIDE
window.onclick = function(event){

    let modal =
        document.getElementById('registerModal');

    if(event.target == modal){

        closeRegisterModal();
    }
}

</script>

<?php

$content = ob_get_clean();

$show_slider = false;

include __DIR__ . "/Trang_Chu_Includes/Layout.php";

?>

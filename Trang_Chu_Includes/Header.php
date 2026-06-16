<?php
// SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CART
$cart = $_SESSION['cart'] ?? [];
$cart_count = 0;

foreach ($cart as $item) {
    $cart_count += $item['soluong'];
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

/* ================= NAVBAR ================= */

.navbar-brand{
    font-size:1.5rem;
    letter-spacing:1px;
}

/* SEARCH */
.search-input{
    border-radius:999px 0 0 999px !important;
}

.search-btn{
    border-radius:0 999px 999px 0 !important;
}

/* LOGIN BUTTON */
.btn-login-main{
    border-radius:999px;
    padding:8px 18px;
    font-weight:700;
}

/* ================= MODAL ================= */

.login-modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.65);
    backdrop-filter:blur(5px);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:99999;
    padding:20px;
}

.login-modal.active{
    display:flex;
}

.login-box{
    width:100%;
    max-width:470px;
    max-height:90vh;
    overflow-y:auto;
    background:#fff;
    border-radius:24px;
    position:relative;
    animation:showLogin .35s ease;
    box-shadow:0 30px 90px rgba(0,0,0,.45);
}

/* CUSTOM SCROLL */
.login-box::-webkit-scrollbar{
    width:8px;
}

.login-box::-webkit-scrollbar-thumb{
    background:#dc2626;
    border-radius:999px;
}

/* CLOSE */
.close-login{
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

/* HEADER */
.login-header{
    background:linear-gradient(135deg,#dc2626,#991b1b);
    color:#fff;
    text-align:center;
    padding:38px 25px;
}

.login-icon{
    font-size:3rem;
    margin-bottom:10px;
}

.login-header h2{
    margin:0;
    font-weight:800;
    letter-spacing:2px;
}

.login-header p{
    margin-top:8px;
    opacity:.9;
}

/* BODY */
.login-body{
    padding:35px;
}

/* INPUT */
.login-body .form-label{
    font-size:.78rem;
    font-weight:700;
    color:#6b7280;
    letter-spacing:1px;
}

.login-body .input-group{
    border-radius:16px;
    overflow:hidden;
    border:1px solid #e5e7eb;
}

.login-body .input-group:focus-within{
    border-color:#dc2626;
    box-shadow:0 0 0 4px rgba(220,38,38,.12);
}

.login-body .input-group-text{
    background:#f9fafb;
    border:none;
    padding:0 16px;
}

.login-body .form-control{
    border:none;
    background:#f9fafb;
    padding:15px;
}

.login-body .form-control:focus{
    box-shadow:none;
    background:#fff;
}

/* BUTTON */
.btn-login-modal{
    width:100%;
    border:none;
    border-radius:16px;
    padding:15px;
    font-weight:800;
    color:#fff;
    background:linear-gradient(135deg,#dc2626,#991b1b);
    transition:.3s;
}

.btn-login-modal:hover{
    transform:translateY(-2px);
    box-shadow:0 14px 30px rgba(220,38,38,.35);
}

/* FOOTER */
.login-footer{
    margin-top:22px;
    text-align:center;
}

.forgot-password{
    margin-bottom:18px;
}

.forgot-password a{
    color:#dc2626;
    text-decoration:none;
    font-weight:700;
}

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

.register-link{
    color:#4b5563;
}

.register-link a{
    color:#dc2626;
    text-decoration:none;
    font-weight:700;
}

/* PASSWORD STRENGTH */
.password-strength{
    margin-top:10px;
}

.strength-bar{
    width:100%;
    height:8px;
    border-radius:999px;
    background:#e5e7eb;
    overflow:hidden;
}

.strength-fill{
    height:100%;
    width:0%;
    transition:.3s;
    border-radius:999px;
}

.strength-text{
    font-size:.82rem;
    margin-top:6px;
    font-weight:700;
}

/* ANIMATION */
@keyframes showLogin{

    from{
        opacity:0;
        transform:translateY(25px) scale(.95);
    }

    to{
        opacity:1;
        transform:translateY(0) scale(1);
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

<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm">

    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand fw-bold"
           href="Index.php">

            Men's Wear

        </a>

        <!-- MOBILE CART -->
        <div class="d-flex d-lg-none ms-auto me-2">

            <a class="nav-link px-2 position-relative"
               href="Gio_Hang.php">

                <i class="bi bi-cart3 fs-4"></i>

                <?php if ($cart_count > 0): ?>

                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="font-size:0.6rem;">

                        <?= $cart_count ?>

                    </span>

                <?php endif; ?>

            </a>

        </div>

        <!-- TOGGLE -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <!-- NAV -->
        <div class="collapse navbar-collapse"
             id="navbarNav">

            <!-- MENU -->
            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link" href="Index.php">Trang chủ</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="TrangPhuc_He.php">Mùa hè</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="TrangPhuc_Dong.php">Mùa đông</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="TrangPhuc_Quan.php">Quần</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="Phu_Kien.php">Phụ kiện</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="Bo_Suu_Tap.php">Bộ sưu tập</a>
                </li>

            </ul>

            <!-- RIGHT -->
            <div class="d-flex align-items-center">

                <!-- SEARCH -->
                <form class="d-flex me-3"
                      action="Tim_Kiem.php"
                      method="GET">

                    <div class="input-group input-group-sm">

                        <input
                            class="form-control border-end-0 search-input"
                            type="search"
                            name="keyword"
                            placeholder="Tìm kiếm..."
                        >

                        <button
                            class="btn btn-outline-secondary border-start-0 search-btn"
                            type="submit">

                            <i class="bi bi-search"></i>

                        </button>

                    </div>

                </form>

                <!-- CART -->
                <a class="nav-link px-3 d-none d-lg-block position-relative"
                   href="Gio_Hang.php">

                    <i class="bi bi-cart3 fs-5"></i>

                    <?php if ($cart_count > 0): ?>

                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              style="font-size:0.6rem;">

                            <?= $cart_count ?>

                        </span>

                    <?php endif; ?>

                </a>

                <!-- USER -->
<?php if (isset($_SESSION['user'])): ?>

<?php

/* =========================
   GET USER INFO
========================= */

include_once "Trang_Chu_Includes/connect_db.inc";

$maND = (int)($_SESSION['user']['id'] ?? 0);

$thongBaoProfile = "";

/* =========================
   UPDATE PROFILE
========================= */

if (isset($_POST['btn_update_profile'])) {

    $hoTen   = mysqli_real_escape_string($conn, $_POST['hoTen']);
    $dienThoai = mysqli_real_escape_string($conn, $_POST['dienThoai']);
    $diaChi  = mysqli_real_escape_string($conn, $_POST['diaChi']);

    $sqlAvatar = "";

    if (!empty($_FILES['avatar']['name'])) {

        $uploadDir = "uploads/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);

        $fileName =
            "avatar_" .
            $maND .
            "_" .
            time() .
            "." .
            $ext;

        if (
            move_uploaded_file(
                $_FILES['avatar']['tmp_name'],
                $uploadDir . $fileName
            )
        ) {

            $sqlAvatar =
                ", AnhDaiDien='$fileName'";
        }
    }

    $sqlUpdate = "
        UPDATE nguoidung
        SET
            HoTen='$hoTen',
            DienThoai='$dienThoai',
            DiaChi='$diaChi'
            $sqlAvatar
        WHERE MaND=$maND
    ";

    if (mysqli_query($conn, $sqlUpdate)) {

        $_SESSION['user']['name'] = $hoTen;

        $thongBaoProfile = "
            <div class='profile-alert success'>
                ✔ Cập nhật thông tin thành công
            </div>
        ";
    }
}

/* =========================
   LOAD USER
========================= */

$sqlUser = "
    SELECT *
    FROM nguoidung
    WHERE MaND = $maND
    LIMIT 1
";

$resultUser = mysqli_query($conn, $sqlUser);

$userProfile = mysqli_fetch_assoc($resultUser);

/* =========================
   AVATAR
========================= */

$avatar = (
    !empty($userProfile['AnhDaiDien']) &&
    file_exists("uploads/" . $userProfile['AnhDaiDien'])
)
? "uploads/" . $userProfile['AnhDaiDien']
: "https://ui-avatars.com/api/?name="
    . urlencode($userProfile['HoTen'] ?? 'User')
    . "&background=dc3545"
    . "&color=ffffff"
    . "&size=400"
    . "&bold=true";

?>

<style>

/* ================= PROFILE MODAL ================= */

.profile-modal{

    position: fixed;

    inset: 0;

    background: rgba(0,0,0,.75);

    backdrop-filter: blur(8px);

    display: none;

    align-items: center;

    justify-content: center;

    z-index: 999999;

    padding: 20px;

    overflow-y: auto;
}

.profile-modal.active{

    display: flex;
}

.profile-box{

    width: 100%;

    max-width: 950px;

    border-radius: 30px;

    overflow: hidden;

    background: #111827;

    box-shadow:
        0 40px 120px rgba(0,0,0,.6);

    animation: profileShow .35s ease;
}

/* HEADER */

.profile-top{

    position: relative;

    padding: 60px 40px 120px;

    text-align: center;

    background:
        linear-gradient(
            135deg,
            #dc2626,
            #7f1d1d
        );
}

.profile-tag{

    display: inline-block;

    padding: 8px 18px;

    border-radius: 999px;

    background: rgba(255,255,255,.14);

    border: 1px solid rgba(255,255,255,.15);

    color: #fff;

    font-size: 13px;

    font-weight: 700;

    margin-bottom: 20px;
}

.profile-name{

    font-size: 42px;

    font-weight: 800;

    color: #fff;

    margin-bottom: 10px;
}

.profile-email{

    color: rgba(255,255,255,.85);

    font-size: 15px;
}

/* AVATAR */

.profile-avatar{

    width: 180px;

    height: 180px;

    border-radius: 50%;

    overflow: hidden;

    border: 6px solid #111827;

    position: absolute;

    left: 50%;

    bottom: -90px;

    transform: translateX(-50%);

    box-shadow:
        0 20px 50px rgba(0,0,0,.45);
}

.profile-avatar img{

    width: 100%;

    height: 100%;

    object-fit: cover;
}

.avatar-upload{

    position: absolute;

    bottom: 10px;

    right: 10px;

    width: 42px;

    height: 42px;

    border-radius: 50%;

    background: #dc2626;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #fff;

    cursor: pointer;

    border: 3px solid #111827;
}

/* BODY */

.profile-content{

    padding: 120px 35px 35px;

    background: #111827;
}

/* ALERT */

.profile-alert{

    padding: 15px;

    border-radius: 16px;

    margin-bottom: 25px;

    text-align: center;

    font-weight: 700;
}

.profile-alert.success{

    background: rgba(34,197,94,.15);

    border: 1px solid rgba(34,197,94,.35);

    color: #4ade80;
}

/* STATS */

.profile-stats{

    display: grid;

    grid-template-columns: repeat(3,1fr);

    gap: 20px;

    margin-bottom: 30px;
}

.profile-stat{

    background: rgba(255,255,255,.05);

    border: 1px solid rgba(255,255,255,.08);

    border-radius: 22px;

    padding: 30px;

    text-align: center;
}

.profile-stat h3{

    color: #fff;

    font-size: 28px;

    font-weight: 800;

    margin-bottom: 10px;
}

.profile-stat span{

    color: rgba(255,255,255,.6);

    font-size: 14px;
}

/* FORM */

.profile-form{

    display: flex;

    flex-direction: column;

    gap: 22px;
}

.profile-group label{

    display: block;

    color: rgba(255,255,255,.65);

    margin-bottom: 10px;

    font-size: 14px;

    font-weight: 600;
}

.profile-input{

    width: 100%;

    border: 1px solid rgba(255,255,255,.1);

    background: rgba(255,255,255,.05);

    color: #fff;

    border-radius: 18px;

    padding: 16px 18px;

    outline: none;

    transition: .3s;
}

.profile-input:focus{

    border-color: #dc2626;

    box-shadow:
        0 0 0 4px rgba(220,38,38,.15);
}

textarea.profile-input{

    resize: none;

    min-height: 120px;
}

/* BUTTON */

.profile-actions{

    margin-top: 15px;

    display: flex;

    justify-content: flex-end;

    gap: 15px;
}

.btn-profile{

    border: none;

    padding: 14px 28px;

    border-radius: 999px;

    font-weight: 700;

    text-decoration: none;

    transition: .3s;
}

.btn-edit{

    background:
        linear-gradient(
            135deg,
            #dc2626,
            #991b1b
        );

    color: #fff;
}

.btn-edit:hover{

    transform: translateY(-3px);

    color: #fff;
}

.btn-close-profile{

    background: rgba(255,255,255,.08);

    color: #fff;
}

/* ANIMATION */

@keyframes profileShow{

    from{

        opacity: 0;

        transform:
            translateY(25px)
            scale(.95);
    }

    to{

        opacity: 1;

        transform:
            translateY(0)
            scale(1);
    }
}

/* MOBILE */

@media(max-width:768px){

    .profile-top{

        padding: 45px 20px 110px;
    }

    .profile-name{

        font-size: 28px;
    }

    .profile-content{

        padding: 110px 18px 25px;
    }

    .profile-stats{

        grid-template-columns: 1fr;
    }

    .profile-actions{

        flex-direction: column;
    }

    .btn-profile{

        width: 100%;

        text-align: center;
    }
}

</style>

<div class="dropdown ms-2">

    <a class="nav-link dropdown-toggle fw-bold"
       href="#"
       data-bs-toggle="dropdown">

        <?= htmlspecialchars($_SESSION['user']['name']) ?>

    </a>

    <ul class="dropdown-menu dropdown-menu-end shadow border-0">

        <li>

            <a class="dropdown-item"
               href="#"
               onclick="openProfileModal()">

                👤 Thông tin

            </a>

        </li>

        <li>

            <a class="dropdown-item"
               href="Don_Hang.php">

                🧾 Đơn hàng

            </a>

        </li>

        <li>
            <hr class="dropdown-divider">
        </li>

        <li>

            <a class="dropdown-item text-danger fw-bold"
               href="Dang_Xuat.php">

                🚪 Đăng xuất

            </a>

        </li>

    </ul>

</div>

<!-- PROFILE MODAL -->

<div class="profile-modal"
     id="profileModal">

    <div class="profile-box">

        <form method="POST"
              enctype="multipart/form-data">

            <!-- TOP -->

            <div class="profile-top">

                <div class="profile-tag">

                    ✦ PREMIUM MEMBER

                </div>

                <div class="profile-name">

                    <?= htmlspecialchars($userProfile['HoTen']) ?>

                </div>

                <div class="profile-email">

                    <?= htmlspecialchars($userProfile['Email']) ?>

                </div>

                <div class="profile-avatar">

                    <img
                        src="<?= htmlspecialchars($avatar) ?>"
                        id="previewAvatar"
                        alt="Avatar"
                    >

                    <label
                        for="avatarInput"
                        class="avatar-upload"
                    >

                        <i class="bi bi-camera-fill"></i>

                    </label>

                    <input
                        type="file"
                        id="avatarInput"
                        name="avatar"
                        hidden
                        onchange="previewAvatar(event)"
                    >

                </div>

            </div>

            <!-- CONTENT -->

            <div class="profile-content">

                <?= $thongBaoProfile ?>

                <!-- STATS -->

                <div class="profile-stats">

                    <div class="profile-stat">

                        <h3>

                            #<?= (int)$userProfile['MaND'] ?>

                        </h3>

                        <span>

                            Customer ID

                        </span>

                    </div>

                    <div class="profile-stat">

                        <h3>

                            <?= ($userProfile['PhanQuyen'] == 1)
                                ? 'ADMIN'
                                : 'MEMBER'
                            ?>

                        </h3>

                        <span>

                            Account Type

                        </span>

                    </div>

                    <div class="profile-stat">

                        <h3 style="color:#4ade80;">

                            ACTIVE

                        </h3>

                        <span>

                            Account Status

                        </span>

                    </div>

                </div>

                <!-- FORM -->

                <div class="profile-form">

                    <div class="profile-group">

                        <label>

                            Họ và tên

                        </label>

                        <input
                            type="text"
                            name="hoTen"
                            class="profile-input"
                            value="<?= htmlspecialchars($userProfile['HoTen']) ?>"
                            required
                        >

                    </div>

                    <div class="profile-group">

                        <label>

                            Email

                        </label>

                        <input
                            type="email"
                            class="profile-input"
                            value="<?= htmlspecialchars($userProfile['Email']) ?>"
                            readonly
                        >

                    </div>

                    <div class="profile-group">

                        <label>

                            Số điện thoại

                        </label>

                        <input
                            type="text"
                            name="dienThoai"
                            class="profile-input"
                            value="<?= htmlspecialchars($userProfile['DienThoai']) ?>"
                        >

                    </div>

                    <div class="profile-group">

                        <label>

                            Địa chỉ

                        </label>

                        <textarea
                            name="diaChi"
                            class="profile-input"
                        ><?= htmlspecialchars($userProfile['DiaChi']) ?></textarea>

                    </div>

                </div>

                <!-- ACTION -->

                <div class="profile-actions">

                    <button
                        type="button"
                        class="btn-profile btn-close-profile"
                        onclick="closeProfileModal()"
                    >

                        Đóng

                    </button>

                    <button
                        type="submit"
                        name="btn_update_profile"
                        class="btn-profile btn-edit"
                    >

                        💾 Lưu thay đổi

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<script>

function openProfileModal(){

    document
    .getElementById('profileModal')
    .classList
    .add('active');
}

function closeProfileModal(){

    document
    .getElementById('profileModal')
    .classList
    .remove('active');
}

window.addEventListener('click', function(e){

    const profileModal =
        document.getElementById('profileModal');

    if(e.target === profileModal){

        closeProfileModal();
    }
});

/* PREVIEW AVATAR */

function previewAvatar(event){

    const reader = new FileReader();

    reader.onload = function(){

        document
        .getElementById('previewAvatar')
        .src = reader.result;
    };

    reader.readAsDataURL(event.target.files[0]);
}

</script>

<?php else: ?>

                    <!-- LOGIN BUTTON -->
                    <button
                        class="btn btn-dark btn-sm btn-login-main ms-2"
                        onclick="openLoginModal()"
                    >

                        <i class="bi bi-person-circle me-1"></i>

                        Đăng nhập

                    </button>

                <?php endif; ?>

            </div>

        </div>

    </div>

</nav>

<!-- ================= LOGIN MODAL ================= -->

<div class="login-modal"
     id="loginModal">

    <div class="login-box">

        <button class="close-login"
                onclick="closeLoginModal()">

            <i class="bi bi-x-lg"></i>

        </button>

        <div class="login-header">

            <div class="login-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <h2>ĐĂNG NHẬP</h2>

            <p>Chào mừng quay trở lại</p>

        </div>

        <div class="login-body">

            <form method="POST"
                  action="Dang_Nhap.php">

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

                <button
                    type="submit"
                    name="dang_nhap"
                    class="btn-login-modal"
                >

                    <i class="bi bi-box-arrow-in-right"></i>

                    ĐĂNG NHẬP

                </button>

                <div class="login-footer">

                    <div class="forgot-password">

                        <a href="quen_mat_khau.php">

                            <i class="bi bi-key-fill"></i>

                            Quên mật khẩu?

                        </a>

                    </div>

                    <div class="divider">

                        <span></span>

                        <p>TÀI KHOẢN MỚI</p>

                        <span></span>

                    </div>

                    <div class="register-link">

                        Chưa có tài khoản?

                        <a href="#"
                           onclick="switchToRegister()">

                            Đăng ký ngay

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- ================= REGISTER MODAL ================= -->

<div class="login-modal"
     id="registerModal">

    <div class="login-box">

        <button class="close-login"
                onclick="closeRegisterModal()">

            <i class="bi bi-x-lg"></i>

        </button>

        <div class="login-header">

            <div class="login-icon">
                <i class="bi bi-person-plus-fill"></i>
            </div>

            <h2>ĐĂNG KÝ</h2>

            <p>Tạo tài khoản mới</p>

        </div>

        <div class="login-body">

            <form method="POST"
                  action="Dang_ky.php">

                <!-- NAME -->
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
                            maxlength="10"
                            pattern="[0-9]{10}"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                            required
                        >

                    </div>

                    <small class="text-muted">
                        Số điện thoại phải đủ 10 số
                    </small>

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

                <!-- PASSWORD -->
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
                            id="registerPassword"
                            name="password"
                            class="form-control"
                            placeholder="Nhập mật khẩu"
                            onkeyup="checkPasswordStrength()"
                            required
                        >

                    </div>

                    <!-- STRENGTH -->
                    <div class="password-strength">

                        <div class="strength-bar">

                            <div class="strength-fill"
                                 id="strengthFill"></div>

                        </div>

                        <div class="strength-text"
                             id="strengthText">

                            Chưa nhập mật khẩu

                        </div>

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

                <!-- BUTTON -->
                <button
                    type="submit"
                    class="btn-login-modal"
                >

                    <i class="bi bi-person-plus-fill"></i>

                    TẠO TÀI KHOẢN

                </button>

            </form>

        </div>

    </div>

</div>

<!-- ================= SCRIPT ================= -->

<script>

function openLoginModal(){

    document
    .getElementById('loginModal')
    .classList
    .add('active');
}

function closeLoginModal(){

    document
    .getElementById('loginModal')
    .classList
    .remove('active');
}

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

function switchToRegister(){

    closeLoginModal();

    openRegisterModal();
}

/* PASSWORD STRENGTH */

function checkPasswordStrength(){

    let password =
        document.getElementById("registerPassword").value;

    let strengthFill =
        document.getElementById("strengthFill");

    let strengthText =
        document.getElementById("strengthText");

    let strength = 0;

    if(password.length >= 6){
        strength++;
    }

    if(/[A-Z]/.test(password)){
        strength++;
    }

    if(/[0-9]/.test(password)){
        strength++;
    }

    if(/[^A-Za-z0-9]/.test(password)){
        strength++;
    }

    if(password.length == 0){

        strengthFill.style.width = "0%";
        strengthFill.style.background = "#e5e7eb";
        strengthText.innerHTML = "Chưa nhập mật khẩu";
    }

    else if(strength <= 1){

        strengthFill.style.width = "25%";
        strengthFill.style.background = "#dc2626";
        strengthText.innerHTML = "Mật khẩu yếu";
        strengthText.style.color = "#dc2626";
    }

    else if(strength == 2){

        strengthFill.style.width = "50%";
        strengthFill.style.background = "#f59e0b";
        strengthText.innerHTML = "Mật khẩu trung bình";
        strengthText.style.color = "#f59e0b";
    }

    else if(strength == 3){

        strengthFill.style.width = "75%";
        strengthFill.style.background = "#3b82f6";
        strengthText.innerHTML = "Mật khẩu mạnh";
        strengthText.style.color = "#3b82f6";
    }

    else{

        strengthFill.style.width = "100%";
        strengthFill.style.background = "#16a34a";
        strengthText.innerHTML = "Mật khẩu rất mạnh";
        strengthText.style.color = "#16a34a";
    }
}

/* CLOSE OUTSIDE */

window.onclick = function(event){

    let loginModal =
        document.getElementById('loginModal');

    let registerModal =
        document.getElementById('registerModal');

    if(event.target == loginModal){

        loginModal.classList.remove('active');
    }

    if(event.target == registerModal){

        registerModal.classList.remove('active');
    }
}

</script>

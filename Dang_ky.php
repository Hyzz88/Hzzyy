<?php
session_start();
$title = "Đăng Ký Tài Khoản";
include "Trang_Chu_Includes/connect_db.inc";

/* ================= XỬ LÝ ĐĂNG KÝ ================= */
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = trim($_POST['fullname'] ?? "");
    $email    = trim($_POST['email'] ?? "");
    $phone    = trim($_POST['phone'] ?? "");
    $address  = trim($_POST['address'] ?? "");
    $password = $_POST['password'] ?? "";
    $confirm  = $_POST['confirm_password'] ?? "";

    if ($fullname === "" || $email === "" || $phone === "" || $address === "" || $password === "" || $confirm === "") {
        $error = "Vui lòng nhập đầy đủ thông tin";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ";
    } elseif (!preg_match('/^[0-9]{9,11}$/', $phone)) {
        $error = "Số điện thoại không hợp lệ";
    } elseif ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp";
    } else {

        $check = mysqli_query($conn, "SELECT MaND FROM nguoidung WHERE Email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email đã được sử dụng";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO nguoidung(HoTen, Email, DienThoai, DiaChi, MatKhau)
                    VALUES ('$fullname', '$email', '$phone', '$address', '$hash')";

            if (mysqli_query($conn, $sql)) {

                $_SESSION['user'] = [
                    'id'   => mysqli_insert_id($conn),
                    'name' => $fullname
                ];

                header("Location: Index.php");
                exit();
            } else {
                $error = "Đăng ký thất bại, vui lòng thử lại";
            }
        }
    }
}

ob_start();
?>

<!-- ================= STYLE VIP PRO ================= -->
<style>
body{
    margin:0;
    font-family:'Segoe UI',system-ui,sans-serif;
    background:linear-gradient(135deg,#111827,#1f2937);
}
.auth-container{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 15px;
}
.auth-card{
    width:100%;
    max-width:520px;
    background:#fff;
    border-radius:22px;
    box-shadow:0 30px 80px rgba(0,0,0,.35);
    overflow:hidden;
    animation:fadeUp .6s ease;
}
.auth-header{
    background:linear-gradient(135deg,#dc2626,#b91c1c);
    color:#fff;
    text-align:center;
    padding:35px 20px;
}
.auth-header h1{
    margin:0;
    letter-spacing:3px;
    font-weight:800;
}
.auth-body{
    padding:35px 35px 40px;
}
.form-label{
    font-weight:600;
    color:#374151;
}
.form-control{
    border-radius:14px;
    padding:14px 16px;
    border:1px solid #d1d5db;
    font-size:.95rem;
    transition:.3s;
}
.form-control:focus{
    border-color:#dc2626;
    box-shadow:0 0 0 4px rgba(220,38,38,.15);
}
.btn-register{
    width:100%;
    border:none;
    border-radius:16px;
    padding:16px;
    font-size:1.05rem;
    font-weight:700;
    color:#fff;
    background:linear-gradient(135deg,#dc2626,#b91c1c);
    transition:.35s;
}
.btn-register:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(220,38,38,.45);
}
.auth-footer{
    text-align:center;
    margin-top:20px;
}
.auth-footer a{
    color:#dc2626;
    font-weight:700;
    text-decoration:none;
}
.auth-footer a:hover{ text-decoration:underline; }

.alert{
    border-radius:14px;
    font-size:.95rem;
}

@keyframes fadeUp{
    from{opacity:0;transform:translateY(30px)}
    to{opacity:1;transform:translateY(0)}
}

@media(max-width:576px){
    .auth-body{padding:25px 20px}
}
</style>

<!-- ================= HTML ================= -->
<div class="auth-container">
    <div class="auth-card">

        <div class="auth-header">
            <h1>ĐĂNG KÝ</h1>
        </div>

        <div class="auth-body">

            <?php if($error): ?>
                <div class="alert alert-danger text-center mb-3">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="fullname" class="form-control"
                           value="<?= htmlspecialchars($fullname ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control"
                           value="<?= htmlspecialchars($phone ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <textarea name="address" class="form-control" rows="2" required><?= htmlspecialchars($address ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>

                <button type="submit" class="btn-register">
                    TẠO TÀI KHOẢN
                </button>
            </form>

            <div class="auth-footer">
                <p>Đã có tài khoản?
                    <a href="Dang_nhap.php">Đăng nhập</a>
                </p>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$show_slider = false;
include "Trang_Chu_Includes/Layout.php";
?>

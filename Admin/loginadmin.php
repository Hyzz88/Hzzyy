<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../Trang_Chu_Includes/connect_db.inc';

$error = "";

function admin_password_matches($inputPassword, $storedPassword) {
    $storedPassword = (string)$storedPassword;

    if (password_get_info($storedPassword)['algo'] !== 0 && password_verify($inputPassword, $storedPassword)) {
        return true;
    }

    return hash_equals(md5($inputPassword), $storedPassword) || hash_equals($inputPassword, $storedPassword);
}

function admin_role_matches($role) {
    $role = strtolower(trim((string)$role));
    return $role === 'admin' || $role === '1';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Vui lòng nhập đầy đủ Email và mật khẩu!";
    } else {

        $sql = "SELECT * FROM nguoidung WHERE Email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {

            if (admin_password_matches($password, $user['MatKhau']) && admin_role_matches($user['PhanQuyen'])) {

                $_SESSION['admin'] = [
                    'MaND'      => $user['MaND'],
                    'HoTen'     => $user['HoTen'],
                    'Email'     => $user['Email'],
                    'PhanQuyen' => $user['PhanQuyen']
                ];

                header("Location: admin.php");
                exit();

            } else {
                $error = "Sai mật khẩu hoặc không có quyền Admin!";
            }

        } else {
            $error = "Email không tồn tại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height:100vh">

<div class="card p-4 shadow" style="width:420px">
    <h3 class="text-center text-primary mb-3">ADMIN LOGIN</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">ĐĂNG NHẬP</button>
    </form>
</div>

</body>
</html>

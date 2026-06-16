<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
include "Trang_Chu_Includes/connect_db.inc";

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

mysqli_set_charset($conn,"utf8mb4");

/* =========================
   LẤY EMAIL
========================= */

$email = trim($_POST['email'] ?? '');

if($email == ''){

    $_SESSION['error'] = "Vui lòng nhập email";

    header("Location: Quen_Mat_Khau.php");
    exit();
}

/* =========================
   KIỂM TRA EMAIL
========================= */

$sql = "
SELECT *
FROM nguoidung
WHERE Email = ?
";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $email
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) <= 0){

    $_SESSION['error'] = "Email không tồn tại";

    header("Location: Quen_Mat_Khau.php");
    exit();
}

/* =========================
   TẠO OTP
========================= */

$otp = rand(100000,999999);

$expire = date(
    "Y-m-d H:i:s",
    strtotime("+5 minutes")
);

/* =========================
   LƯU OTP
========================= */

$sqlUpdate = "
UPDATE nguoidung
SET otp_code = ?,
    otp_expire = ?
WHERE Email = ?
";

$stmtUpdate = mysqli_prepare(
    $conn,
    $sqlUpdate
);

mysqli_stmt_bind_param(
    $stmtUpdate,
    "sss",
    $otp,
    $expire,
    $email
);

mysqli_stmt_execute($stmtUpdate);

/* =========================
   GỬI MAIL
========================= */

$mail = new PHPMailer(true);

try{

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'haune336789@gmail.com';

    $mail->Password = 'eijw wuml cgrr vjxu';

    $mail->SMTPSecure = 'tls';

    $mail->Port = 587;

    $mail->CharSet = 'UTF-8';

    $mail->setFrom(
        'haune336789@gmail.com',
        'Web Thời Trang'
    );

    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject = 'Mã OTP đặt lại mật khẩu';

    $mail->Body = "
        <h2>Mã OTP của bạn là:</h2>
        <h1>$otp</h1>
        <p>OTP có hiệu lực trong 5 phút.</p>
    ";

    $mail->send();

}catch(Exception $e){

    $_SESSION['error'] = "Không gửi được email";

    header("Location: Quen_Mat_Khau.php");
    exit();
}

/* =========================
   SESSION
========================= */

$_SESSION['reset_email'] = $email;

/* =========================
   CHUYỂN TRANG
========================= */

header("Location: Xac_Nhan_OTP.php");
exit();
?>
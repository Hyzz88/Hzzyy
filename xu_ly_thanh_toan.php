<?php

session_start();

include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";

/* =========================
   PHPMailer
========================= */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-6.12.0/src/Exception.php';
require __DIR__ . '/PHPMailer-6.12.0/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-6.12.0/src/SMTP.php';

/* =========================
   CHECK LOGIN
========================= */

if (!isset($_SESSION['user'])) {

    header("Location: Dang_Nhap.php");
    exit();
}

/* =========================
   CHECK CART
========================= */

if (empty($_SESSION['cart'])) {

    header("Location: Gio_Hang.php");
    exit();
}

/* =========================
   GET DATA
========================= */

$ma_nd = intval(
    $_SESSION['user']['id']
    ?? $_SESSION['user']['MaND']
    ?? 0
);

$ho_ten = mysqli_real_escape_string(
    $conn,
    trim($_POST['HoTen'] ?? '')
);

$email = mysqli_real_escape_string(
    $conn,
    trim($_POST['Email'] ?? '')
);

$sdt = mysqli_real_escape_string(
    $conn,
    trim($_POST['SDT'] ?? '')
);

$dia_chi = mysqli_real_escape_string(
    $conn,
    trim($_POST['DiaChi'] ?? '')
);

$pttt = mysqli_real_escape_string(
    $conn,
    $_POST['PhuongThuc'] ?? 'COD'
);

$ma_ck = mysqli_real_escape_string(
    $conn,
    $_POST['MaCK'] ?? ''
);

/* =========================
   TÍNH TỔNG TIỀN
========================= */

$tong_tien = 0;

foreach ($_SESSION['cart'] as $item) {

    $tong_tien += (
        $item['gia'] * $item['soluong']
    );
}

/* =========================
   TRẠNG THÁI
========================= */

if ($pttt == "COD") {

    $trang_thai = "Chờ xác nhận";
}
else{

    $trang_thai = "Chờ thanh toán";
}

/* =========================
   TRANSACTION
========================= */

mysqli_begin_transaction($conn);

try{

    /* =========================
       GHI CHÚ
    ========================= */

    $ghi_chu = "
        PTTT: $pttt
        | Mã CK: $ma_ck
        | SDT: $sdt
    ";

    /* =========================
       INSERT ĐƠN HÀNG
    ========================= */

    $sql_order = "
        INSERT INTO donhang
        (
            MaND,
            NgayDat,
            TongTien,
            TrangThai,
            DiaChiGiaoHang,
            GhiChu
        )

        VALUES
        (
            '$ma_nd',
            NOW(),
            '$tong_tien',
            '$trang_thai',
            '$dia_chi',
            '$ghi_chu'
        )
    ";

    $query_order = mysqli_query(
        $conn,
        $sql_order
    );

    if(!$query_order){

        throw new Exception(
            mysqli_error($conn)
        );
    }

    /* =========================
       MÃ ĐƠN HÀNG
    ========================= */

    $ma_dh = mysqli_insert_id($conn);

    /* =========================
       HTML MAIL
    ========================= */

    $html_sp = "";

    /* =========================
       INSERT CHI TIẾT
    ========================= */

    foreach($_SESSION['cart'] as $item){

        $ma_sp = intval($item['id']);

        $kichco = mysqli_real_escape_string(
            $conn,
            $item['kichco']
        );

        $mausac = mysqli_real_escape_string(
            $conn,
            $item['mausac']
        );

        $soluong = intval(
            $item['soluong']
        );

        $gia = floatval(
            $item['gia']
        );

        /* =========================
           TÌM BIẾN THỂ + TỒN KHO
        ========================= */

        $sql_bt = "
            SELECT MaBienThe, SoLuongTon
            FROM bienthesp
            WHERE
            MaSP = '$ma_sp'
            AND KichCo = '$kichco'
            AND MauSac = '$mausac'
            LIMIT 1
        ";

        $query_bt = mysqli_query(
            $conn,
            $sql_bt
        );

        $bt = mysqli_fetch_assoc(
            $query_bt
        );

        if(!$bt){

            throw new Exception(
                "Không tìm thấy biến thể sản phẩm!"
            );
        }

        $ma_bt = $bt['MaBienThe'];

        $so_luong_ton = intval(
            $bt['SoLuongTon']
        );

        /* =========================
           KIỂM TRA TỒN KHO
        ========================= */

        if($so_luong_ton < $soluong){

            throw new Exception(
                "Sản phẩm '{$item['ten']}' không đủ tồn kho!"
            );
        }

        /* =========================
           INSERT CHI TIẾT ĐƠN
        ========================= */

        $sql_ct = "
            INSERT INTO chitietdonhang
            (
                MaDH,
                MaBienThe,
                SoLuong,
                GiaBanLucMua
            )

            VALUES
            (
                '$ma_dh',
                '$ma_bt',
                '$soluong',
                '$gia'
            )
        ";

        $query_ct = mysqli_query(
            $conn,
            $sql_ct
        );

        if(!$query_ct){

            throw new Exception(
                mysqli_error($conn)
            );
        }

        /* =========================
           TRỪ SỐ LƯỢNG TỒN KHO
        ========================= */

        $sql_update_stock = "
            UPDATE bienthesp
            SET SoLuongTon = SoLuongTon - $soluong
            WHERE MaBienThe = '$ma_bt'
        ";

        $query_update_stock = mysqli_query(
            $conn,
            $sql_update_stock
        );

        if(!$query_update_stock){

            throw new Exception(
                "Không thể cập nhật tồn kho!"
            );
        }

        /* =========================
           HTML SẢN PHẨM
        ========================= */

        $html_sp .= "

        <tr>

            <td
            style='padding:14px;border-bottom:1px solid #eee;'
            >
                {$item['ten']}
            </td>

            <td
            style='
            padding:14px;
            text-align:center;
            border-bottom:1px solid #eee;
            '
            >
                $kichco
            </td>

            <td
            style='
            padding:14px;
            text-align:center;
            border-bottom:1px solid #eee;
            '
            >
                $mausac
            </td>

            <td
            style='
            padding:14px;
            text-align:center;
            border-bottom:1px solid #eee;
            '
            >
                $soluong
            </td>

            <td
            style='
            padding:14px;
            text-align:right;
            border-bottom:1px solid #eee;
            color:#dc2626;
            font-weight:bold;
            '
            >
                " . number_format(
                    $gia,
                    0,
                    ',',
                    '.'
                ) . "đ
            </td>

        </tr>
        ";
    }

    /* =========================
       COMMIT
    ========================= */

    mysqli_commit($conn);

    /* =========================
       SESSION BIÊN LAI
    ========================= */

    $_SESSION['bien_lai'] = [

        'ma_dh' => $ma_dh,

        'ho_ten' => $ho_ten,

        'email' => $email,

        'sdt' => $sdt,

        'dia_chi' => $dia_chi,

        'phuong_thuc' => $pttt,

        'tong_tien' => $tong_tien,

        'ngay_dat' => date('d/m/Y H:i:s')
    ];

    $_SESSION['bien_lai_products']
        = $_SESSION['cart'];

    /* =========================
       GỬI MAIL
    ========================= */

    try{

        $mail = new PHPMailer(true);

        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';

        $mail->SMTPAuth = true;

        $mail->Username =
            'Haune336789@gmail.com';

        $mail->Password =
            'vrxz rndp dyah fqlg';

        $mail->SMTPSecure =
            PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            'Haune336789@gmail.com',
            'Men\'s Wear'
        );

        $mail->addAddress(
            $email,
            $ho_ten
        );

        $mail->isHTML(true);

        $mail->Subject =
            "Xác nhận đơn hàng #$ma_dh";

        $mail->Body = "

<div
style='
background:#f4f6f9;
padding:40px 20px;
font-family:Arial,sans-serif;
'
>

    <div
    style='
    max-width:700px;
    margin:auto;
    background:#ffffff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    '
    >

        <div
        style='
        background:#111827;
        padding:35px;
        text-align:center;
        color:#fff;
        '
        >

            <h1
            style='
            margin:0;
            font-size:32px;
            '
            >
                Men's Wear
            </h1>

            <p
            style='
            margin-top:10px;
            font-size:15px;
            color:#d1d5db;
            '
            >
                Xác nhận đơn hàng thành công
            </p>

        </div>

        <div style='padding:40px;'>

            <h2
            style='
            margin-top:0;
            color:#111827;
            '
            >

                Xin chào $ho_ten 👋

            </h2>

            <p
            style='
            color:#4b5563;
            line-height:1.8;
            font-size:15px;
            '
            >

                Cảm ơn bạn đã mua sắm tại
                <strong>Men's Wear</strong>.

                Đơn hàng của bạn đã được tiếp nhận
                và đang chờ xử lý.

            </p>

            <table
            width='100%'
            cellspacing='0'
            cellpadding='0'
            style='
            border-collapse:collapse;
            overflow:hidden;
            border-radius:14px;
            '
            >

                <tbody>

                    $html_sp

                </tbody>

            </table>

            <div
            style='
            margin-top:35px;
            background:#111827;
            color:#fff;
            padding:25px;
            border-radius:18px;
            '
            >

                <table width='100%'>

                    <tr>

                        <td
                        style='
                        font-size:18px;
                        font-weight:bold;
                        '
                        >
                            Tổng thanh toán
                        </td>

                        <td
                        style='
                        text-align:right;
                        font-size:28px;
                        font-weight:800;
                        '
                        >

                            " . number_format(
                                $tong_tien,
                                0,
                                ',',
                                '.'
                            ) . "đ

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

";

        $mail->send();

    }catch(Exception $mail_error){

        error_log(
            "MAIL ERROR: " .
            $mail_error->getMessage()
        );
    }

    /* =========================
       CHUYỂN QUA BIÊN LAI
    ========================= */

    header(
        "Location: Bien_Lai.php"
    );

    exit();

}catch(Exception $e){

    mysqli_rollback($conn);

    echo "

    <div
    style='
    padding:30px;
    font-family:Arial;
    '
    >

        <h2>
            Lỗi xử lý thanh toán
        </h2>

        <p>
            ".$e->getMessage()."
        </p>

        <a href='Thanh_Toan.php'>

            Quay lại thanh toán

        </a>

    </div>
    ";
}
?>
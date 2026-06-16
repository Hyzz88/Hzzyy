<?php
ob_start();

include 'check_admin.php';
include '../Trang_Chu_Includes/connect_db.inc';

mysqli_set_charset($conn, "utf8mb4");

date_default_timezone_set('Asia/Ho_Chi_Minh');

/* =========================
   THỐNG KÊ TỔNG QUAN
========================= */

$countSP = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM sanpham"
))['total'];

$countDM = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM danhmuc"
))['total'];

$countND = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM nguoidung"
))['total'];

$countDH = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM donhang"
))['total'];

/* =========================
   DOANH THU 7 NGÀY
========================= */

$labels = [];
$data = [];

for($i = 6; $i >= 0; $i--){

    $date = date(
        'Y-m-d',
        strtotime("-$i days")
    );

    /* =========================
       QUERY DOANH THU
    ========================= */

    $sqlRevenue = "
        SELECT 
            COALESCE(SUM(TongTien),0) AS doanhthu
        FROM donhang
        WHERE DATE(NgayDat) = ?
        AND (
            LOWER(TRIM(TrangThai)) = 'hoàn thành'
            OR LOWER(TRIM(TrangThai)) = 'đã hoàn thành'
            OR LOWER(TRIM(TrangThai)) = 'hoan thanh'
            OR LOWER(TRIM(TrangThai)) = 'da hoan thanh'
            OR LOWER(TRIM(TrangThai)) = 'thành công'
            OR LOWER(TRIM(TrangThai)) = 'thanh cong'
        )
    ";

    $stmtRevenue = mysqli_prepare(
        $conn,
        $sqlRevenue
    );

    mysqli_stmt_bind_param(
        $stmtRevenue,
        "s",
        $date
    );

    mysqli_stmt_execute($stmtRevenue);

    $resultRevenue = mysqli_stmt_get_result(
        $stmtRevenue
    );

    $rowRevenue = mysqli_fetch_assoc(
        $resultRevenue
    );

    $labels[] = date(
        'd/m',
        strtotime($date)
    );

    $data[] = (float)$rowRevenue['doanhthu'];
}

/* =========================
   TỔNG DOANH THU
========================= */

$totalRevenue = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "
    SELECT COALESCE(SUM(TongTien),0) AS total
    FROM donhang
    WHERE (
        LOWER(TRIM(TrangThai)) = 'hoàn thành'
        OR LOWER(TRIM(TrangThai)) = 'đã hoàn thành'
        OR LOWER(TRIM(TrangThai)) = 'hoan thanh'
        OR LOWER(TRIM(TrangThai)) = 'da hoan thanh'
        OR LOWER(TRIM(TrangThai)) = 'thành công'
        OR LOWER(TRIM(TrangThai)) = 'thanh cong'
    )
    "
))['total'];

?>

<style>

body{
    background:
    linear-gradient(
        135deg,
        #0f172a,
        #1e293b,
        #111827
    );

    min-height:100vh;

    font-family:'Segoe UI',sans-serif;
}

/* =========================
   TITLE
========================= */

.dashboard-title{
    color:#fff;
    font-size:34px;
    font-weight:700;
    margin-bottom:35px;
}

/* =========================
   CARD
========================= */

.dashboard-card{

    border-radius:24px;

    padding:28px;

    color:#fff;

    overflow:hidden;

    position:relative;

    transition:.35s ease;

    box-shadow:
    0 10px 30px rgba(0,0,0,.25);

    height:100%;
}

.dashboard-card:hover{
    transform:translateY(-6px);
}

.dashboard-card::before{

    content:'';

    position:absolute;

    width:140px;
    height:140px;

    border-radius:50%;

    background:
    rgba(255,255,255,.12);

    top:-50px;
    right:-50px;
}

.dashboard-card h6{
    font-size:16px;
    margin-bottom:12px;
    opacity:.9;
}

.dashboard-card h2{
    font-size:40px;
    font-weight:700;
    margin:0;
}

.dashboard-card .icon{
    position:absolute;
    right:20px;
    bottom:15px;
    font-size:55px;
    opacity:.18;
}

/* =========================
   COLORS
========================= */

.bg-primary-custom{
    background:
    linear-gradient(
        135deg,
        #2563eb,
        #3b82f6
    );
}

.bg-success-custom{
    background:
    linear-gradient(
        135deg,
        #059669,
        #10b981
    );
}

.bg-warning-custom{
    background:
    linear-gradient(
        135deg,
        #d97706,
        #f59e0b
    );
}

.bg-danger-custom{
    background:
    linear-gradient(
        135deg,
        #dc2626,
        #ef4444
    );
}

.bg-purple-custom{
    background:
    linear-gradient(
        135deg,
        #7c3aed,
        #a855f7
    );
}

/* =========================
   CHART CARD
========================= */

.chart-card{

    margin-top:40px;

    background:
    rgba(255,255,255,.08);

    border-radius:28px;

    overflow:hidden;

    backdrop-filter:blur(12px);

    box-shadow:
    0 8px 30px rgba(0,0,0,.35);
}

.chart-header{

    padding:24px 28px;

    color:#fff;

    font-size:22px;
    font-weight:600;

    border-bottom:
    1px solid rgba(255,255,255,.08);
}

.chart-body{
    padding:30px;
}

canvas{
    height:420px !important;
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:768px){

    .dashboard-title{
        font-size:28px;
    }

    .dashboard-card h2{
        font-size:32px;
    }
}

</style>

<div class="container-fluid py-4">

    <h2 class="dashboard-title">
        📊 Bảng Điều Khiển Hệ Thống
    </h2>

    <div class="row g-4">

        <!-- SẢN PHẨM -->

        <div class="col-md-3">

            <div class="dashboard-card bg-primary-custom">

                <h6>Tổng sản phẩm</h6>

                <h2>
                    <?= number_format($countSP) ?>
                </h2>

                <div class="icon">
                    📦
                </div>

            </div>

        </div>

        <!-- DANH MỤC -->

        <div class="col-md-3">

            <div class="dashboard-card bg-success-custom">

                <h6>Danh mục</h6>

                <h2>
                    <?= number_format($countDM) ?>
                </h2>

                <div class="icon">
                    🗂️
                </div>

            </div>

        </div>

        <!-- NGƯỜI DÙNG -->

        <div class="col-md-3">

            <div class="dashboard-card bg-warning-custom">

                <h6>Người dùng</h6>

                <h2>
                    <?= number_format($countND) ?>
                </h2>

                <div class="icon">
                    👤
                </div>

            </div>

        </div>

        <!-- ĐƠN HÀNG -->

        <div class="col-md-3">

            <div class="dashboard-card bg-danger-custom">

                <h6>Đơn hàng</h6>

                <h2>
                    <?= number_format($countDH) ?>
                </h2>

                <div class="icon">
                    🛒
                </div>

            </div>

        </div>

        <!-- TỔNG DOANH THU -->

        <div class="col-md-12">

            <div class="dashboard-card bg-purple-custom">

                <h6>Tổng doanh thu hoàn thành</h6>

                <h2>
                    <?= number_format($totalRevenue,0,',','.') ?> đ
                </h2>

                <div class="icon">
                    💰
                </div>

            </div>

        </div>

    </div>

    <!-- CHART -->

    <div class="chart-card">

        <div class="chart-header">
            💹 Doanh Thu 7 Ngày Gần Nhất
        </div>

        <div class="chart-body">

            <canvas id="revenueChart"></canvas>

        </div>

    </div>

</div>

<script>

const revenueChart = document.getElementById(
    'revenueChart'
);

new Chart(revenueChart, {

    type: 'line',

    data: {

        labels: <?= json_encode($labels) ?>,

        datasets: [{

            label: 'Doanh thu',

            data: <?= json_encode($data) ?>,

            borderColor: '#60a5fa',

            backgroundColor: 'rgba(96,165,250,.18)',

            borderWidth: 4,

            fill: true,

            tension: .4,

            pointRadius: 5,

            pointHoverRadius: 8,

            pointBackgroundColor:'#fff',

            pointBorderColor:'#60a5fa',

            pointBorderWidth:3

        }]
    },

    options: {

        responsive:true,

        maintainAspectRatio:false,

        interaction:{
            intersect:false,
            mode:'index'
        },

        plugins:{

            legend:{
                display:false
            },

            tooltip:{

                backgroundColor:'#111827',

                titleColor:'#fff',

                bodyColor:'#fff',

                borderColor:'#374151',

                borderWidth:1,

                callbacks:{

                    label:function(context){

                        let value = context.raw;

                        return ' ' +
                        new Intl.NumberFormat(
                            'vi-VN'
                        ).format(value)
                        + ' đ';
                    }
                }
            }
        },

        scales:{

            x:{

                ticks:{
                    color:'#fff'
                },

                grid:{
                    color:'rgba(255,255,255,.08)'
                }
            },

            y:{

                beginAtZero:true,

                ticks:{

                    color:'#fff',

                    callback:function(value){

                        if(value >= 1000000){

                            return (
                                value / 1000000
                            ).toFixed(1) + 'M';
                        }

                        return value.toLocaleString(
                            'vi-VN'
                        );
                    }
                },

                grid:{
                    color:'rgba(255,255,255,.08)'
                }
            }
        }
    }
});

console.log(
    "Labels:",
    <?= json_encode($labels) ?>
);

console.log(
    "Data:",
    <?= json_encode($data) ?>
);

</script>

<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
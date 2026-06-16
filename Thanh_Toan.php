<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";

mysqli_set_charset($conn, "utf8");

/* =====================================================
   🔒 BẮT BUỘC ĐĂNG NHẬP
===================================================== */
if (!isset($_SESSION['user'])) {

    $_SESSION['redirect_after_login'] = "Thanh_Toan.php";

    echo "
    <script>
        alert('Vui lòng đăng nhập để thanh toán!');
        window.location.href='Dang_Nhap.php';
    </script>
    ";

    exit();
}

/* =====================================================
   🛒 GIỎ HÀNG
===================================================== */
if (empty($_SESSION['cart'])) {

    header("Location: Index.php");
    exit;
}

/* =====================================================
   🔥 LẤY THÔNG TIN USER
===================================================== */

$user_id = $_SESSION['user']['MaND']
    ?? $_SESSION['user']['id']
    ?? 0;

if (!$user_id) {

    die("Không tìm thấy thông tin người dùng!");
}

$sql_user = mysqli_query(
    $conn,
    "SELECT * FROM nguoidung WHERE MaND = '$user_id' LIMIT 1"
);

if (!$sql_user) {

    die("Lỗi truy vấn user: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($sql_user);

if (!$user) {

    die("Không tìm thấy dữ liệu người dùng!");
}

/* =====================================================
   💰 TÍNH TỔNG TIỀN
===================================================== */

$total_money = 0;

foreach ($_SESSION['cart'] as $item) {

    $total_money += ($item['gia'] * $item['soluong']);
}

/* =====================================================
   🔥 MÃ CHUYỂN KHOẢN
===================================================== */

$ma_ck = "MW" . strtoupper(substr(md5(time()), 0, 6));

$title = "Thanh toán - Men's Wear";

ob_start();

?>

<!-- LEAFLET -->
<link
rel="stylesheet"
href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>

<script
src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
</script>

<style>

body{
    background:#f5f5f5;
}

.checkout-wrapper{
    padding:40px 0 80px;
}

.checkout-card{
    background:#fff;
    border-radius:26px;
    padding:35px;
    box-shadow:0 10px 35px rgba(0,0,0,.08);
    border:none;
}

.checkout-title{
    font-size:1.4rem;
    font-weight:800;
    color:#111827;
    margin-bottom:25px;
}

.form-label{
    font-weight:700;
    color:#374151;
    margin-bottom:8px;
}

.form-control{
    border-radius:16px;
    border:1px solid #d1d5db;
    padding:14px 16px;
}

.form-control:focus{
    border-color:#111827;
    box-shadow:0 0 0 4px rgba(17,24,39,.08);
}

.btn-dark-custom{
    width:100%;
    border:none;
    border-radius:18px;
    padding:16px;
    background:#111827;
    color:#fff;
    font-weight:800;
    transition:.3s;
}

.btn-dark-custom:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(0,0,0,.18);
}

.location-tools{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:15px;
}

.location-btn{
    border:none;
    border-radius:14px;
    background:#111827;
    color:#fff;
    padding:12px 18px;
    font-weight:700;
    transition:.25s;
}

.location-btn:hover{
    background:#000;
}

#map{
    width:100%;
    height:350px;
    border-radius:18px;
    margin-top:15px;
    overflow:hidden;
    border:2px solid #e5e7eb;
}

.payment-option{
    border:2px solid #e5e7eb;
    border-radius:18px;
    padding:18px;
    margin-bottom:15px;
    cursor:pointer;
    transition:.25s;
}

.payment-option:hover{
    border-color:#111827;
    background:#f9fafb;
}

.payment-option.active{
    border-color:#111827;
    background:#f9fafb;
}

.qr-box{
    display:none;
    text-align:center;
    padding:25px;
    border-radius:20px;
    background:#f9fafb;
    margin-bottom:25px;
}

.qr-box img{
    width:230px;
    max-width:100%;
    border-radius:14px;
    background:#fff;
    padding:10px;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}

.order-summary{
    position:sticky;
    top:100px;
    z-index:1;
}

.order-item{
    display:flex;
    align-items:center;
    gap:15px;
    padding-bottom:18px;
    margin-bottom:18px;
    border-bottom:1px solid #f1f1f1;
}

.order-item:last-child{
    border-bottom:none;
}

.order-item img{
    width:75px;
    height:95px;
    object-fit:cover;
    border-radius:14px;
}

.order-info{
    flex:1;
}

.order-info h6{
    font-size:.95rem;
    font-weight:700;
    margin-bottom:6px;
    color:#111827;
}

.order-info small{
    color:#6b7280;
}

.total-box{
    background:#111827;
    border-radius:22px;
    padding:22px;
    color:#fff;
    margin-top:20px;
}

.total-box .price{
    font-size:2rem;
    font-weight:800;
}

.order-scroll{
    max-height:420px;
    overflow-y:auto;
    padding-right:5px;
}

@media(max-width:991px){

    .order-summary{
        position:relative;
        top:0;
        margin-top:20px;
    }
}

@media(max-width:768px){

    .checkout-card{
        padding:22px;
    }

    #map{
        height:280px;
    }

    .checkout-wrapper{
        padding:25px 0 60px;
    }
}

</style>

<div class="container checkout-wrapper">

    <div class="row g-4">

        <div class="col-lg-7">

            <div class="checkout-card">

                <h3 class="checkout-title">
                    🚚 THÔNG TIN GIAO HÀNG
                </h3>

                <form
                    action="xu_ly_thanh_toan.php"
                    method="POST"
                    id="checkoutForm"
                >

                    <input
                        type="hidden"
                        name="TongTien"
                        value="<?= $total_money ?>"
                    >

                    <div class="mb-3">

                        <label class="form-label">
                            Họ và tên
                        </label>

                        <input
                            type="text"
                            name="HoTen"
                            class="form-control"
                            required
                            value="<?= htmlspecialchars($user['HoTen'] ?? '') ?>"
                        >

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="Email"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($user['Email'] ?? '') ?>"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Số điện thoại
                            </label>

                            <input
                                type="text"
                                name="SDT"
                                class="form-control"
                                maxlength="10"
                                pattern="[0-9]{10}"
                                required
                                value="<?= htmlspecialchars($user['DienThoai'] ?? '') ?>"
                            >

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Địa chỉ giao hàng
                        </label>

                        <textarea
                            name="DiaChi"
                            id="addressInput"
                            class="form-control"
                            rows="3"
                            required
                        ><?= htmlspecialchars($user['DiaChi'] ?? '') ?></textarea>

                    </div>

                    <div class="location-tools">

                        <button
                            type="button"
                            class="location-btn"
                            onclick="getCurrentLocation()"
                        >
                            📍 Lấy vị trí hiện tại
                        </button>

                        <button
                            type="button"
                            class="location-btn"
                            onclick="toggleMap()"
                        >
                            🗺 Chọn trên bản đồ
                        </button>

                    </div>

                    <input type="hidden" name="Latitude" id="latitude">
                    <input type="hidden" name="Longitude" id="longitude">

                    <div id="map" style="display:none;"></div>

                    <hr class="my-4">

                    <h3 class="checkout-title">
                        💳 PHƯƠNG THỨC THANH TOÁN
                    </h3>

                    <div class="payment-option active">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="PhuongThuc"
                                value="COD"
                                checked
                            >

                            <label class="form-check-label">
                                💵 Thanh toán khi nhận hàng
                            </label>

                        </div>

                    </div>

                    <div class="payment-option">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="PhuongThuc"
                                value="VietQR"
                            >

                            <label class="form-check-label">
                                🏦 VietQR Ngân hàng
                            </label>

                        </div>

                    </div>

                    <div class="payment-option">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="PhuongThuc"
                                value="Momo"
                            >

                            <label class="form-check-label">
                                📱 Ví điện tử Momo
                            </label>

                        </div>

                    </div>

                    <div class="qr-box" id="qrBox">

                        <h5
                            class="fw-bold mb-3"
                            id="qrTitle"
                        >
                            QUÉT MÃ THANH TOÁN
                        </h5>

                        <img id="qrImg" src="">

                        <div class="mt-3">

                            <span class="badge bg-danger p-3 fs-6">
                                <?= $ma_ck ?>
                            </span>

                        </div>

                    </div>

                    <input
                        type="hidden"
                        name="MaCK"
                        value="<?= $ma_ck ?>"
                    >

                    <button
                        type="submit"
                        class="btn-dark-custom"
                    >
                        🛍 XÁC NHẬN ĐẶT HÀNG
                    </button>

                </form>

            </div>

        </div>

        <div class="col-lg-5">

            <div class="checkout-card order-summary">

                <h3 class="checkout-title">
                    🧾 ĐƠN HÀNG CỦA BẠN
                </h3>

                <div class="order-scroll">

                    <?php foreach($_SESSION['cart'] as $item): ?>

                        <div class="order-item">

                            <img src="<?= $item['anh'] ?>">

                            <div class="order-info">

                                <h6>
                                    <?= htmlspecialchars($item['ten']) ?>
                                </h6>

                                <small>
                                    Size:
                                    <?= $item['kichco'] ?>
                                </small>

                                <br>

                                <small>
                                    SL:
                                    <?= $item['soluong'] ?>
                                </small>

                            </div>

                            <div class="fw-bold text-danger">

                                <?= number_format($item['gia'],0,',','.') ?>đ

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">

                    <span>Tạm tính</span>

                    <strong>
                        <?= number_format($total_money,0,',','.') ?>đ
                    </strong>

                </div>

                <div class="d-flex justify-content-between text-success mb-3">

                    <span>Vận chuyển</span>

                    <strong>Miễn phí</strong>

                </div>

                <div class="total-box">

                    <div class="d-flex justify-content-between align-items-center">

                        <span class="fw-bold fs-5">
                            TỔNG CỘNG
                        </span>

                        <span class="price">
                            <?= number_format($total_money,0,',','.') ?>đ
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

document.querySelectorAll('.payment-option')
.forEach(option=>{

    option.addEventListener('click',function(){

        document.querySelectorAll('.payment-option')
        .forEach(el=>el.classList.remove('active'));

        this.classList.add('active');

        this.querySelector('input').checked = true;

        updateQR();
    });
});

function updateQR(){

    const method =
        document.querySelector('input[name="PhuongThuc"]:checked').value;

    const qrBox =
        document.getElementById('qrBox');

    const qrImg =
        document.getElementById('qrImg');

    const qrTitle =
        document.getElementById('qrTitle');

    const amount =
        <?= $total_money ?>;

    const note =
        "<?= $ma_ck ?>";

    if(method === "VietQR"){

        qrBox.style.display = "block";

        qrTitle.innerHTML =
            "🏦 QUÉT MÃ VIETQR";

        qrImg.src =
            `https://img.vietqr.io/image/mbbank-123456789-compact2.png?amount=${amount}&addInfo=${note}`;
    }

    else if(method === "Momo"){

        qrBox.style.display = "block";

        qrTitle.innerHTML =
            "📱 QUÉT MÃ MOMO";

        qrImg.src =
            `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=MOMO|${note}|${amount}`;
    }

    else{

        qrBox.style.display = "none";
    }
}

function getCurrentLocation(){

    if(navigator.geolocation){

        navigator.geolocation.getCurrentPosition(

            function(position){

                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`
                )
                .then(res=>res.json())
                .then(data=>{

                    if(data.display_name){

                        document.getElementById('addressInput').value =
                            data.display_name;
                    }
                });

                alert("Đã lấy vị trí hiện tại!");
            },

            function(){

                alert("Không thể lấy vị trí!");
            }
        );
    }
}

let map;
let marker;

function toggleMap(){

    const mapDiv =
        document.getElementById('map');

    if(mapDiv.style.display === "none"){

        mapDiv.style.display = "block";

        if(!map){

            map = L.map('map')
            .setView([10.8231,106.6297],13);

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    attribution:'© OpenStreetMap'
                }
            ).addTo(map);

            map.on('click',function(e){

                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                if(marker){

                    map.removeLayer(marker);
                }

                marker = L.marker([lat,lng])
                .addTo(map);

                fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`
                )
                .then(res=>res.json())
                .then(data=>{

                    if(data.display_name){

                        document.getElementById('addressInput').value =
                            data.display_name;
                    }
                });
            });
        }

        setTimeout(()=>{

            map.invalidateSize();

        },300);
    }

    else{

        mapDiv.style.display = "none";
    }
}

</script>

<?php

$content = ob_get_clean();

$show_slider = false;

$hide_footer = true;

include __DIR__ . "/Trang_Chu_Includes/Layout.php";

?>
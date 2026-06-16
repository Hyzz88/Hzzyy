<?php
// 1. Kết nối cơ sở dữ liệu
include __DIR__ . "/connect_db.inc";

// 2. Truy vấn slider
$sql_slider = "SELECT * FROM slider";
$result_slider = mysqli_query($conn, $sql_slider);

$sliders = [];
if ($result_slider) {
    while ($row = mysqli_fetch_assoc($result_slider)) {
        $sliders[] = $row;
    }
}
?>

<!-- BOOTSTRAP CSS (BẮT BUỘC) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
#vstyleHeroCarousel {
    width: 100%;
    overflow: hidden;
    background: #f8f8f8;
}

.carousel-item {
    position: relative;
    width: 100%;
}

.vstyle-img-slider {
    width: 100%;
    height: auto;
    max-height: 700px;
    object-fit: contain;
    display: block;
}

.carousel-item::after {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.2);
    z-index: 1;
}

.carousel-caption {
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    text-align: left;
}

.caption-box {
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(8px);
    padding: 40px;
    border-left: 6px solid #da251d;
    max-width: 550px;
}

.caption-box h1 {
    font-size: 3rem;
    font-weight: 800;
    color: #fff;
}

.caption-box p {
    color: #eee;
}

@media (max-width: 768px) {
    .carousel-caption {
        display: none !important;
    }
}
</style>

<div id="vstyleHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

    <!-- INDICATORS -->
    <div class="carousel-indicators">
        <?php foreach ($sliders as $index => $item): ?>
            <button type="button"
                data-bs-target="#vstyleHeroCarousel"
                data-bs-slide-to="<?= $index ?>"
                class="<?= ($index === 0) ? 'active' : '' ?>">
            </button>
        <?php endforeach; ?>
    </div>

    <!-- SLIDES -->
    <div class="carousel-inner">
        <?php foreach ($sliders as $index => $item):
            $path_anh = "img/" . $item['AnhSL'];
        ?>
        <div class="carousel-item <?= ($index === 0) ? 'active' : '' ?>">
            <img src="<?= htmlspecialchars($path_anh) ?>"
                 class="vstyle-img-slider"
                 alt="<?= htmlspecialchars($item['TenSL']) ?>">

            <div class="container">
                <div class="carousel-caption d-none d-md-block">
                    <div class="caption-box">
                        <h1><?= htmlspecialchars($item['TenSL']) ?></h1>
                        <p>New Collection 2025 - Khám phá phong cách thời thượng.</p>
                        <a href="TrangPhuc_He.php" class="btn btn-danger btn-lg rounded-0 px-5">
                            MUA NGAY
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- CONTROLS -->
    <button class="carousel-control-prev" type="button" data-bs-target="#vstyleHeroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#vstyleHeroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- BOOTSTRAP JS (QUAN TRỌNG NHẤT ĐỂ AUTO RUN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ÉP AUTO RUN (CHẮC CHẮN 100%) -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const carousel = document.querySelector('#vstyleHeroCarousel');

    if (carousel) {
        new bootstrap.Carousel(carousel, {
            interval: 5000,
            ride: "carousel",
            pause: false,
            wrap: true
        });
    }
});
</script>
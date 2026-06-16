<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Men's Wear <?php echo $title; ?></title>
        
    <link rel="stylesheet" href="css/Trang_chu.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --vstyle-red: #da251d;
            --vstyle-yellow: #ffcd00; 
            --vstyle-dark: #1a1a1a;
        }
        body { font-family: 'Arial', sans-serif; color: var(--vstyle-dark); overflow-x: hidden; }
        
       .top-bar { background: #f8f8f8; font-size: 0.9rem; padding: 8px 0; border-bottom: 1px solid #eee; }
       .hotline-text { color: var(--vstyle-red); font-weight: bold; }

       .navbar-brand img { height: 50px; }
       .nav-link { font-weight: 600; text-transform: uppercase; padding: 0.5rem 1rem!important; transition: 0.3s; }
       .nav-link:hover { color: var(--vstyle-red)!important; }

       .vstyle-slider {
            height: 80vh;
            background-color: #333;
        }
       .carousel-item {
            height: 80vh;
            background-size: cover;
            background-position: center;
            position: relative;
        }
       .carousel-item::after {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
        }
       .carousel-caption {
            z-index: 10;
            bottom: 30%;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
        }
       .carousel-caption h1 {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: 2px;
        }
       .carousel-control-prev-icon,.carousel-control-next-icon {
            filter: invert(1) sepia(1) saturate(5) hue-rotate(0deg); 
        }

       .section-title { text-align: center; margin-bottom: 40px; font-weight: bold; position: relative; padding-bottom: 10px; }
       .section-title::after { content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 80px; height: 3px; background: var(--vstyle-red); }
       .cate-card { border: none; overflow: hidden; position: relative; margin-bottom: 20px; transition: 0.4s; cursor: pointer; }
       .cate-card:hover { transform: scale(1.02); }
       .cate-title { position: absolute; bottom: 20px; left: 0; right: 0; background: rgba(0,0,0,0.6); color: #fff; padding: 10px; text-align: center; }

        footer { background: #111; color: #bbb; padding: 50px 0 20px; }
        footer h5 { color: #fff; margin-bottom: 20px; font-size: 1.1rem; }
       .footer-link { color: #bbb; text-decoration: none; display: block; margin-bottom: 8px; }
       .footer-link:hover { color: var(--vstyle-yellow); }
    </style>
</head>
<body>

<?php include __DIR__ . "/Header.php"; ?>


<?php 

if (!isset($show_slider) || $show_slider !== false) {
    if(file_exists(__DIR__ . "/Slider.php")) {
        include __DIR__ . "/Slider.php"; 
    }
}
?>

<?php echo $content; ?>


<?php include __DIR__ . "/Footer.php"; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        const myCarousel = document.querySelector('#vstyleHeroCarousel');
        const carousel = new bootstrap.Carousel(myCarousel, {
            interval: 4000, 
            wrap: true,     
            touch: true     
        });

        console.log("Hệ thống Slider V'style đã khởi chạy.");
    });
</script>
<script src="js/Trang_Chu.js"></script>
</body>

</html>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moii Store Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #f1f5f9; transition: all 0.3s; }
        .sidebar { 
            width: 260px; 
            min-height: 100vh; 
            background: #020617; 
            position: fixed; /* Giữ sidebar cố định để không bị trôi */
        }
        .sidebar a { 
            color: #cbd5e1; 
            padding: 15px 25px; 
            display: flex; 
            align-items: center; 
            text-decoration: none;
            transition: 0.2s;
        }
        .sidebar a i { margin-right: 12px; font-size: 1.2rem; }
        .sidebar a:hover, .sidebar a.active { 
            background: #1e293b; 
            color: #fff;
            border-left: 4px solid #3b82f6;
        }
        .main-wrapper { margin-left: 260px; width: calc(100% - 260px); }
        .topbar { background: #fff; padding: 12px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .content { padding: 30px; min-height: 100vh; }
        .dashboard-card { border-radius: 12px; padding: 25px; color: white; margin-bottom: 20px; }
        
        /* Dark Mode */
        body.dark { background: #0f172a; color: #f8fafc; }
        body.dark .topbar { background: #1e293b; border-bottom: 1px solid #334155; }
        body.dark .content { background: #0f172a; }
        body.dark .card { background: #1e293b; border: 1px solid #334155; color: #fff; }
    </style>
</head>
<body class="<?= isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'on' ? 'dark' : '' ?>">

<div class="d-flex">
    <div class="sidebar shadow">
        <div class="text-white text-center py-4 border-bottom border-secondary mb-3">
            <h4 class="fw-bold m-0"><i class="bi bi-shield-lock"></i> MOII ADMIN</h4>
        </div>
        
        <a href="admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="QuanLySanPham.php"><i class="bi bi-box-seam"></i> Sản phẩm</a>
        <a href="QuanLyDanhMuc.php"><i class="bi bi-tags"></i> Danh mục</a>
        <a href="QuanLyDonHang.php"><i class="bi bi-cart-check"></i> Đơn hàng</a>
        <a href="QuanLyKhachHang.php"><i class="bi bi-people"></i> Khách hàng</a>
        <a href="top_sanpham.php"><i class="bi bi-graph-up-arrow"></i> Thống kê</a>
        
        <div class="mt-5 border-top border-secondary pt-3">
            <a href="../index.php" target="_blank" class="text-info"><i class="bi bi-house-door"></i> Xem Website</a>
            <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="fw-medium text-muted">Hệ thống quản trị cửa hàng</div>
            <div class="d-flex align-items-center">
                <button id="darkToggle" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-moon-stars"></i>
                </button>
                <span class="badge bg-soft-primary text-primary border border-primary px-3 py-2">
                    <i class="bi bi-person-circle me-1"></i> Admin
                </span>
            </div>
        </div>

        <div class="content">
            <?= $content ?? '<div class="alert alert-warning">Không có nội dung hiển thị</div>' ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const toggle = document.getElementById('darkToggle');
    toggle.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        const isDark = document.body.classList.contains('dark');
        document.cookie = "darkMode=" + (isDark ? 'on' : 'off') + ";path=/";
    });
</script>
</body>
</html>
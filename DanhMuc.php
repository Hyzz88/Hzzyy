<?php
// Kết nối CSDL (đúng đường dẫn dự án của bạn)
include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";

// Lấy danh mục CHA (MadmCong = NULL)
$sqlCha = "SELECT MaDM, TenDM 
           FROM danhmuc 
           WHERE MadmCong IS NULL";
$dmCha = mysqli_query($conn, $sqlCha);
?>

<div class="container my-4">
    <h3 class="mb-4 fw-bold">📂 Danh mục sản phẩm</h3>

    <div class="row">

        <?php if ($dmCha && mysqli_num_rows($dmCha) > 0): ?>
            <?php while ($cha = mysqli_fetch_assoc($dmCha)): ?>

                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">

                        <!-- TÊN DANH MỤC CHA -->
                        <h5 class="fw-bold text-danger">
                            <?= htmlspecialchars($cha['TenDM']) ?>
                        </h5>

                        <?php
                        $idCha = (int)$cha['MaDM'];

                        // Lấy danh mục CON
                        $sqlCon = "SELECT MaDM, TenDM 
                                   FROM danhmuc 
                                   WHERE MaDMCong = $idCha";
                        $dmCong = mysqli_query($conn, $sqlCong);
                        ?>

                        <?php if ($dmCong && mysqli_num_rows($dmCong) > 0): ?>
                            <!-- CÓ DANH MỤC CON -->
                            <ul class="list-unstyled mt-2">
                                <?php while ($con = mysqli_fetch_assoc($dmCong)): ?>
                                    <li class="mb-1">
                                        <a href="SanPham.php?madm=<?= $con['MaDM'] ?>"
                                           class="text-decoration-none text-dark">
                                            ▸ <?= htmlspecialchars($con['TenDM']) ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <!-- KHÔNG CÓ DANH MỤC CON -->
                            <a href="SanPham.php?madm=<?= $cha['MaDM'] ?>"
                               class="btn btn-outline-danger btn-sm mt-2">
                                Xem sản phẩm
                            </a>
                        <?php endif; ?>

                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <p>❌ Chưa có danh mục nào</p>
        <?php endif; ?>

    </div>
</div>

<footer>
    <style>
        /* ===== FOOTER ===== */
        footer {
            background: linear-gradient(135deg, #1a1a1a, #2c2c2c);
            color: #f1f1f1;
            padding: 40px 0 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        footer h5 {
            font-weight: 600;
            margin-bottom: 15px;
            color: #ffc107;
        }

        footer p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 8px;
            color: #ccc;
        }

        .footer-link {
            display: block;
            color: #ddd;
            text-decoration: none;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            color: #ffc107;
            padding-left: 5px;
        }

        .text-warning {
            font-weight: bold;
            color: #ffc107 !important;
        }

        footer .border-top {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        footer small {
            color: #aaa;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            footer {
                text-align: center;
            }

            .footer-link:hover {
                padding-left: 0;
            }
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5>Lotusé</h5>
                <p>Thiết kế và bán sẵn các dòng cổ phục Việt Nam từ Áo Ngũ Thân, Áo Tấc đến Nhật Bình.</p>
                <p>Mở cửa: 8h00 - 19h00 hàng ngày</p>
            </div>

            <div class="col-lg-4 mb-4">
                <h5>THÔNG TIN LIÊN HỆ</h5>
                <p>Địa chỉ: Xã Thuận Giao Thành Phố Hồ Chí Minh</p>
                <p>Hotline: <span class="text-warning">0982.848.525</span></p>
                <p>Email: Nhathau336789@gmail.com</p>
            </div>

            <div class="col-lg-4">
                <h5>DANH MỤC NHANH</h5>
                <a href="TrangPhuc_He.php" class="footer-link">Trang Phục Hè</a>
                <a href="TrangPhuc_Dong.php" class="footer-link">Trang Phục Đông</a>
                <a href="Phu_Kien.php" class="footer-link">Phụ Kiện</a>
            </div>
        </div>

        <div class="text-center border-top pt-3 mt-4">
            <small>&copy; Men's Wear. Bảo lưu mọi quyền.</small>
        </div>
    </div>
</footer>
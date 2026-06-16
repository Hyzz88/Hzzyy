-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 29, 2025 lúc 07:06 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `webthoitrang_moii`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bienthesp`
--

CREATE TABLE `bienthesp` (
  `MaBienThe` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `KichCo` varchar(10) NOT NULL,
  `MauSac` varchar(50) NOT NULL,
  `SoLuongTon` int(11) NOT NULL CHECK (`SoLuongTon` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bienthesp`
--

INSERT INTO `bienthesp` (`MaBienThe`, `MaSP`, `KichCo`, `MauSac`, `SoLuongTon`) VALUES
(4, 1, 'L', 'Đen', 30),
(5, 2, 'M', 'Xanh/Đen', 36),
(6, 3, 'XL', 'Nâu sọc', 33),
(7, 4, 'XL', 'Trắng/Be', 12),
(8, 5, 'L', 'Đen/Xám', 12),
(10, 5, 'M', 'Đen/Xám', 12),
(11, 5, 'XL', 'Đen/Xám', 13),
(12, 5, '2XL', 'Đen/Xám', 20),
(13, 1, 'M', 'Đen', 19),
(14, 1, 'XL', 'Đen', 21),
(15, 1, '2XL', 'Đen', 22),
(16, 2, 'L', 'Xanh/Đen', 22),
(17, 2, 'XL', 'Xanh/Đen', 33),
(18, 2, '2XL', 'Xanh/Đen', 19),
(19, 3, 'M', 'Nâu sọc', 23),
(20, 3, 'L', 'Nâu sọc', 33),
(21, 3, '2XL', 'Nâu sọc', 11),
(22, 4, 'M', 'Trắng/Be', 23),
(23, 4, 'L', 'Trắng/Be', 33),
(24, 4, '2XL', 'Trắng/Be', 12),
(25, 6, 'M', 'Xám/Đen', 23),
(26, 6, 'L', 'Xám/Đen', 33),
(27, 6, 'XL', 'Xám/Đen', 18),
(28, 6, '2XL', 'Xám/Đen', 25),
(29, 7, 'M', 'Trắng/Đen', 12),
(30, 7, 'L', 'Trắng/Đen', 23),
(31, 7, 'XL', 'Trắng/Đen', 33),
(32, 7, '2XL', 'Trắng/Đen', 24),
(33, 8, 'M', 'Đen/Nâu', 45),
(34, 8, 'L', 'Đen/Nâu', 23),
(35, 8, 'XL', 'Đen/Nâu', 45),
(36, 8, '2XL', 'Đen/Nâu', 12),
(37, 9, 'M', 'Xanh nhạt', 12),
(38, 9, 'L', 'Xanh Nhạt', 23),
(39, 9, 'XL', 'Xanh nhạt', 24),
(40, 9, '2XL', 'Xanh nhạt', 13),
(41, 10, 'M', 'Xám/Đen', 45),
(42, 10, 'L', 'Xám/Đen', 23),
(43, 10, 'XL', 'Xám/Đen', 24),
(44, 10, '2XL', 'Xám/Đen', 12),
(45, 11, 'M', 'Xanh đậm', 55),
(46, 11, 'L', 'Xanh Đậm', 23),
(47, 11, 'XL', 'Xanh đậm', 55),
(48, 11, '2XL', 'Xanh đậm', 23),
(49, 12, 'M', 'Đen/Xám', 40),
(50, 12, 'L', 'Đen/Xám', 36),
(51, 12, 'XL', 'Đen/Xám', 37),
(52, 12, '2XL', 'Đen/Xám', 38),
(53, 13, '40', 'Đen', 36),
(54, 14, '41', 'Đen', 39),
(55, 13, '42', 'Đen', 40),
(56, 13, '43', 'Đen', 32),
(57, 14, '40', 'Đen/Nâu', 36),
(58, 14, '41', 'Đen/nâu', 39),
(59, 14, '42', 'Đen/nâu', 40),
(60, 14, '43', 'Đen/nâu', 32),
(61, 15, '40', 'Đen/xám', 36),
(62, 15, '41', 'Đen/xám', 50),
(63, 15, '42', 'Đen/xám', 30),
(64, 15, '43', 'Đưn\\/xám', 39),
(65, 16, '40', 'Trắng/trắng sọc đen', 36),
(66, 16, '41', 'Trắng/trắng sọc đen', 50),
(67, 16, '42', 'Trắng/trắng sọc đen', 30),
(68, 16, '43', 'Trắng/trắng sọc đen', 39),
(69, 17, '40', 'Trắng', 33),
(70, 17, '41', 'Trắng', 20),
(71, 17, '42', 'Trắng', 30),
(72, 17, '43', 'Trắng', 39),
(73, 18, '40', 'Trắng sọc đen', 33),
(74, 18, '41', 'Trắng sọc đen', 20),
(75, 18, '42', 'Trắng sọc đen', 30),
(76, 18, '43', 'Trắng sọc đen', 39);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binhluan`
--

CREATE TABLE `binhluan` (
  `MaBL` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `HoTen` varchar(255) NOT NULL,
  `NoiDung` text NOT NULL,
  `NgayBL` datetime DEFAULT current_timestamp(),
  `SoSao` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `binhluan`
--

INSERT INTO `binhluan` (`MaBL`, `MaSP`, `HoTen`, `NoiDung`, `NgayBL`, `SoSao`) VALUES
(1, 2, 'vi', 'quá tuyệt vời', '2025-12-29 22:12:08', 5),
(2, 2, 'Array', 'ok', '2025-12-29 22:26:34', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `MaCTDH` int(11) NOT NULL,
  `MaDH` int(11) NOT NULL,
  `MaBienThe` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL CHECK (`SoLuong` > 0),
  `GiaBanLucMua` decimal(10,2) NOT NULL CHECK (`GiaBanLucMua` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

CREATE TABLE `danhmuc` (
  `MaDM` int(11) NOT NULL,
  `TenDM` varchar(100) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `MaDMCong` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`MaDM`, `TenDM`, `MoTa`, `MaDMCong`) VALUES
(1, 'Trang Phục Hè', '? Bộ sưu  SƠ MI XUÂN HÈ – THỜI THƯỢNG, NHẸ TÊNH & THOÁNG MÁT\r\nChào đón mùa mới với những thiết kế sơ mi nằm trong bộ sưu tập Xuân Hè mới nhất. Đây không chỉ là một chiếc áo, mà là giải pháp giúp bạn luôn giữ được vẻ ngoài chỉn chu, thanh lịch mà vẫn cực kỳ thoải mái dưới cái nắng rực rỡ.\r\n\r\n', 1),
(2, 'Trang Phục Đông', '❄️ BST ÁO THU ĐÔNG – ẤM ÁP, SANG TRỌNG & ĐẲNG CẤP\r\nKhi những cơn gió lạnh đầu mùa ùa về, một chiếc áo không chỉ cần đẹp mà còn phải là \"lá chắn\" bảo vệ sức khỏe của bạn. Bộ sưu tập Thu Đông năm nay tập trung vào những chất liệu dày dặn, giữ nhiệt tối ưu nhưng vẫn đảm bảo sự nhẹ nhàng, không gây cảm giác nặng nề khi mặc nhiều lớp.\r\n\r\n', 3),
(3, 'Phụ Kiện', '? Bộ sưu tập QUẦN DÀI NAM – FORM DÁNG CHUẨN, TỰ TIN MỖI NGÀY\r\nMột chiếc quần dài hoàn hảo không chỉ nằm ở chất liệu mà còn ở cách nó tôn lên vóc dáng của người mặc. Bộ sưu tập quần dài năm nay được thiết kế để mang lại sự cân bằng tuyệt vời giữa phong cách lịch lãm và sự thoải mái tuyệt đối cho mọi hoạt động.\r\n\r\n', 2),
(4, 'Dép', 'Các đôi dép đều thuộc dòng dép quai ngang tối giản, thiết kế hiện đại, phù hợp sử dụng đi trong nhà, đi chơi, đi dạo hoặc mang hằng ngày.\r\nPhong cách chủ đạo là basic – nam tính – dễ phối đồ, sử dụng tông màu trung tính như đen, xám, phù hợp nhiều độ tuổi.', NULL),
(5, 'Giầy', 'Các sản phẩm là dòng giày sneaker nam phong cách hiện đại, thiết kế trẻ trung, dễ phối đồ, phù hợp cho đi học, đi chơi, dạo phố hoặc sử dụng hằng ngày.\r\nTông màu chủ đạo là trắng – kem – nâu, tạo cảm giác sạch sẽ, thời trang và không lỗi mốt.', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `MaDH` int(11) NOT NULL,
  `MaND` int(11) DEFAULT NULL,
  `NgayDat` datetime DEFAULT current_timestamp(),
  `TongTien` decimal(10,2) NOT NULL CHECK (`TongTien` >= 0),
  `TrangThai` varchar(50) NOT NULL,
  `DiaChiGiaoHang` varchar(255) NOT NULL,
  `GhiChu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `MaND` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `DienThoai` varchar(15) DEFAULT NULL,
  `PhanQuyen` varchar(20) NOT NULL DEFAULT 'user',
  `NgayDangKy` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`MaND`, `HoTen`, `Email`, `MatKhau`, `DiaChi`, `DienThoai`, `PhanQuyen`, `NgayDangKy`) VALUES
(1, 'vuongnhathau', 'haucho3636@gmail.com', '123123', 'Phú Giáo,, Bình Dương', '0123456789', 'admin', '2023-12-01 13:20:29'),
(2, 'vicho', 'nhathau336789@gmail.com', '$2y$10$ge4TYlWaudg/N65cv0KuceWd2G9jYDxL58BZ1On7GGMwcTgN.oF9a', NULL, NULL, 'user', '2025-12-29 17:47:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacungcap`
--

CREATE TABLE `nhacungcap` (
  `MaNCC` int(11) NOT NULL,
  `TenNCC` varchar(100) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `DienThoai` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhacungcap`
--

INSERT INTO `nhacungcap` (`MaNCC`, `TenNCC`, `DiaChi`, `DienThoai`) VALUES
(1, 'Nhà cung cấp Áo xuân hè', 'Bến Cát, Bình Dương', '012012012'),
(2, 'Nhà cung cấp Áo thu đông', 'Thuận An, Bình Dương', '01230123'),
(3, 'Nhà cung cấp Quần ', 'Thủ Dầu Một, Bình Dương', '0123401234'),
(4, 'Công ty thông dụng Thuận an', 'Phường An phú, Thành phố Hồ Chí Minh', '3004197500'),
(5, 'Nhà cung cấp Tổng hợp phía nam', 'Phường chợ lớn, Thành phố Hồ Chí Minh', '0209194500');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `MaSP` int(11) NOT NULL,
  `TenSP` varchar(200) NOT NULL,
  `MaDM` int(11) NOT NULL,
  `MaNCC` int(11) NOT NULL,
  `MoTaChiTiet` text DEFAULT NULL,
  `GiaGoc` decimal(10,2) DEFAULT NULL CHECK (`GiaGoc` > 0),
  `GiaBan` decimal(10,2) DEFAULT NULL CHECK (`GiaBan` > 0),
  `AnhDaiDien` varchar(255) DEFAULT NULL,
  `NgayCapNhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `LuotTym` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`MaSP`, `TenSP`, `MaDM`, `MaNCC`, `MoTaChiTiet`, `GiaGoc`, `GiaBan`, `AnhDaiDien`, `NgayCapNhat`, `LuotTym`) VALUES
(1, 'Áo sơ mi Denim', 1, 1, 'Sơ Mi Denim.\r\nPhong cách: Cá tính, bụi bặm nhưng vẫn chỉn chu.\r\n\r\nTên sản phẩm: Áo Sơ Mi Denim Wash Soft – Phóng Khoáng & Nam Tính.\r\n\r\nChất liệu: Vải denim mỏng nhẹ, đã qua xử lý wash mềm để không gây thô ráp, đảm bảo sự thông thoáng cho mùa hè.\r\n\r\nThiết kế: Form Loose-fit thoải mái, túi ngực vuông vức tạo điểm nhấn khỏe khoắn. Đường chỉ may nổi bật trên nền vải đen/xám khói.\r\n\r\nGợi ý phối đồ: Mặc khoác ngoài áo thun trắng bên trong hoặc cài cúc kín phối cùng quần jean cùng tông màu (Denim-on-denim).', 159.00, 239.00, 'img/aoxuanhe/sanpham1.1.jpg', '2025-12-18 14:14:16', 0),
(2, 'Áo Sơ Mi Trơn Basic - Dài Tay', 1, 1, 'Áo Sơ Mi Trơn Basic - Dài Tay\r\nPhong cách: Thanh lịch, đa năng (Work-to-Leisure).\r\n\r\nTên sản phẩm: Minimalist Cotton Shirt – Đỉnh Cao Của Sự Đơn Giản.\r\n\r\nMàu sắc: Navy sâu lắng và Đen huyền bí.\r\n\r\nChất liệu: Cotton lụa cao cấp, bề mặt vải mịn mượt, hạn chế nhăn và thấm hút mồ hôi cực tốt.\r\n\r\nĐặc điểm: Cổ đức (Spread Collar) cứng cáp, phù hợp cho cả đi làm công sở lẫn đi chơi tối. Form dáng ôm vừa vặn, tôn đường nét cơ thể.\r\n\r\nỨng dụng: Là món đồ \"phải có\" trong tủ đồ, dễ dàng kết hợp với mọi loại quần từ quần Tây, Chinos đến Short.', 199.00, 259.00, 'img/aoxuanhe/sanpham2.1.jpg\r\nimg/aoxuanhe/sanpham2.2.jpg\r\n\r\n', '2025-12-29 22:11:47', 1),
(3, 'Áo Sơ Mi Caro', 1, 1, 'Áo Sơ Mi Caro (Flannel mỏng)\r\nPhong cách: Trẻ trung, năng động, đậm chất Streetwear.\r\n\r\nTên sản phẩm: Plaid Shirt Heritage – Họa Tiết Caro Không Bao Giờ Lỗi Mốt.\r\n\r\nHọa tiết: Caro bản lớn phối tông màu nâu - đen - be (Earth tone) mang lại cảm giác ấm áp nhưng vẫn rất \"mát mắt\" dưới nắng hè.\r\n\r\nThiết kế: Vạt bầu nhẹ, có thể mặc buông ngoài quần tạo vẻ ngoài sành điệu.\r\n\r\nĐiểm cộng: Chất vải có độ bền cao, giữ màu tốt sau nhiều lần giặt.\r\n\r\nPhù hợp: Đi học, đi làm sáng tạo hoặc những buổi dã ngoại cuối tuần.', 299.00, 349.00, 'img/aoxuanhe/sanpham3.1.jpg', '2025-12-29 18:34:34', 0),
(4, 'Áo Sơ Mi Đũi/Linen Tone Sáng', 1, 1, 'Áo Sơ Mi Đũi/Linen Tone Sáng\r\nPhong cách: Nhẹ nhàng, thư thái, chuẩn \"Gentleman\" mùa hè.\r\n\r\nTên sản phẩm: Breathable Linen Shirt – \"Cỗ Máy Điều Hòa\" Cho Cơ Thể.\r\n\r\nMàu sắc: Trắng tinh khôi và Be (Beige) thanh nhã.\r\n\r\nChất liệu: Vải Linen (đũi) tự nhiên, đặc tính siêu thoáng khí, càng mặc càng mềm mại. Đây là chất liệu \"vàng\" cho thời tiết nắng nóng.\r\n\r\nCảm giác: Nhẹ bẫng, mát lạnh khi chạm vào da, giúp bạn luôn thoải mái dù hoạt động ngoài trời cả ngày.\r\n\r\nMix & Match: Rất hợp với quần short trắng hoặc quần kaki màu sáng và một đôi giày lười (Loafers).', 159.00, 209.00, 'img/aoxuanhe/sanpham4.1.jpg\r\nimg/aoxuanhe/sanpham4.2.jpg', '2025-12-29 18:34:34', 0),
(5, 'Áo Sơ Mi Cotton Lụa Premium ', 2, 2, ' Áo Sơ Mi Cotton Lụa Premium \r\nPhong cách: Thanh lịch, sang trọng và chuyên nghiệp dành cho quý ông hiện đại.\r\n\r\nTên sản phẩm: Luxury Silk Cotton Shirt – Bản Lĩnh Quý Ông.\r\n\r\nChất liệu: Vải Cotton Silk cao cấp với đặc tính mướt mịn, bề mặt có độ bóng mờ sang trọng. Chất liệu này không chỉ thấm hút mồ hôi cực tốt mà còn có khả năng kháng khuẩn, giữ cho cơ thể luôn khô thoáng suốt ngày dài.\r\n\r\nThiết kế: Form Regular-fit được tinh chỉnh chuẩn xác, tôn dáng vai và ngực nhưng vẫn thoải mái vùng bụng. Cổ áo được ép keo cứng cáp, giữ form hoàn hảo ngay cả khi không thắt cà vạt.\r\n\r\nGợi ý phối đồ: Kết hợp cùng quần Tây tối màu và giày Loafers cho môi trường công sở, hoặc quần Chinos màu be cho những buổi tiệc tối lịch lãm.', 239000.00, 269000.00, 'img/aothudong/sanpham1.1.jpg\r\nimg/aothudong/sanpham1.2.jpg', '2025-12-29 18:34:34', 0),
(6, 'Áo Sơ Mi Caro (Plaid) Hiện Đại ', 2, 2, 'Áo Sơ Mi Caro (Plaid) Hiện Đại \r\nPhong cách: Trẻ trung, năng động và mang hơi thở thời trang đường phố (Streetstyle).\r\n\r\nTên sản phẩm: Modern Plaid Shirt – Năng Động & Cá Tính.\r\n\r\nChất liệu: Sợi vải dệt mật độ cao giúp áo bền bỉ, hạn chế nhăn nhàu tối đa và giữ màu cực tốt sau nhiều lần giặt. Vải có độ dày vừa phải, phù hợp cho mọi thời điểm trong năm.\r\n\r\nThiết kế: Họa tiết caro phối màu Ombre tinh tế. Điểm nhấn nằm ở các đường vạt bầu nhẹ nhàng và túi ngực được canh kẻ đối xứng tỉ mỉ, tạo nên sự cân đối và chỉn chu cho người mặc.\r\n\r\nGợi ý phối đồ: Rất hợp khi mặc khoác ngoài áo thun trơn, phối cùng quần Jean và một đôi Sneaker trắng để tạo diện mạo sành điệu.', 159.00, 269.00, 'img/aothudong/sanpham2.1.jpg\r\nimg/aothudong/sanpham2.2.jpg', '2025-12-29 18:34:34', 0),
(7, 'Áo Sơ Mi Linen/Đũi Giải Nhiệt ', 2, 2, 'Áo Sơ Mi Linen/Đũi Giải Nhiệt \r\nPhong cách: Phóng khoáng, tự dovà đậm chất nghỉ dưỡng cao cấp.\r\n\r\nTên sản phẩm: Pure Linen Air-Cool – Sảng Khoái Ngày Hè.\r\n\r\nChất liệu: 100% Linen tự nhiên với đặc tính \"biết thở\", siêu nhẹ và nhanh khô. Khả năng tản nhiệt tuyệt vời giúp bạn luôn cảm thấy mát lạnh ngay khi chạm vào vải dưới nắng gắt.\r\n\r\nThiết kế: Tông màu Trắng tinh khôi và Be cát nhẹ nhàng mang lại cảm giác dễ chịu. Form áo suông nhẹ, tối giản túi ngực để làm nổi bật nét lãng tử và tinh tế của chất liệu vải đũi.\r\n\r\nGợi ý phối đồ: Hoàn hảo khi phối cùng quần Short Kaki hoặc quần dài Linen cùng tông cho những chuyến du lịch biển hoặc dạo phố cuối tuần.', 199.00, 239.00, 'img/aothudong/sanpham3.1.jpg\r\nimg/aothudong/sanpham3.2.jpg', '2025-12-29 18:34:34', 0),
(8, 'Áo Jacket Kaki/Dạ ', 2, 2, 'Áo Jacket Kaki/Dạ \r\nPhong cách: Cứng cáp, mạnh mẽ và đầy khí chất.\r\n\r\nTên sản phẩm: Essential Work Jacket – Vững Vàng Trước Gió Lạnh.\r\n\r\nChất liệu: Vải Kaki hoặc Dạ dày dặn, khả năng cản gió và giữ nhiệt tối ưu. Chất vải bền bỉ, chống mài mòn, giúp áo bền đẹp theo thời gian.\r\n\r\nThiết kế: Form áo vuông vức giúp tôn dáng vai, hệ thống túi hộp và nút cài phía trước tạo vẻ ngoài khỏe khoắn. Đường may kép tại các vị trí chịu lực đảm bảo độ bền tuyệt đối cho sản phẩm.\r\n\r\nGợi ý phối đồ: Mặc layer bên ngoài áo sơ mi hoặc áo len cổ lọ, kết hợp cùng quần Jeans tối màu và giày Boots cho phong cách cực \"ngầu\".', 259.00, 299.00, 'img/aothudong/sanpham4.1.jpg\r\nimg/aothudong/sanpham4.2.jpg', '2025-12-29 18:34:34', 0),
(9, 'Quần Jeans Wash Sáng ', 3, 3, ' Quần Jeans Wash Sáng \r\nPhong cách: Trẻ trung, năng động và mang hơi hướng Retro.\r\n\r\nTên sản phẩm: Light Wash Straight Jeans – Nét Phóng Khoáng Hiện Đại.\r\n\r\nChất liệu: Denim Cotton 100% bền bỉ, được xử lý wash sáng màu tạo độ mềm cho vải nhưng vẫn giữ được độ đứng form cần thiết.\r\n\r\nThiết kế: Dáng suông (Straight-fit) thoải mái, điểm nhấn là các vết mài nhẹ tạo hiệu ứng bắt sáng, giúp đôi chân trông thon gọn hơn.\r\n\r\nGợi ý phối đồ: Rất hợp với áo thun trắng hoặc sơ mi caro khoác ngoài cho outfit dạo phố.', 199.00, 249.00, 'img/quan/sanpham1.1.jpg', '2025-12-29 18:34:34', 0),
(10, 'Quần Tây/Chinos Ghi Sáng và Đen ', 3, 3, 'Quần Tây/Chinos Ghi Sáng và Đen \r\nPhong cách: Thanh lịch, nhẹ nhàng và cực kỳ tinh tế.\r\n\r\nTên sản phẩm: Light Grey Smart Slacks – Quý Ông Lịch Lãm.\r\n\r\nChất liệu: Vải Kaki Cotton pha Spandex cao cấp, có độ co giãn nhẹ, bề mặt vải lì, mịn và cực kỳ hạn chế nhăn.\r\n\r\nThiết kế: Form dáng Slim-fit ôm vừa vặn, tôn chiều cao. Cạp quần được gia công chắc chắn, túi mổ sau tối giản chuẩn phong cách Minimalism.\r\n\r\nGợi ý phối đồ: Phối cùng sơ mi màu xanh Navy hoặc đen để tạo sự tương phản nổi bật.', 199.00, 249.00, 'img/quan/sanpham2.1.jpg\r\nimg/quan/sanpham2.2.jpg', '2025-12-29 18:34:34', 0),
(11, 'Quần Jeans Xanh Trung Tính ', 3, 3, 'Quần Jeans Xanh Trung Tính \r\nPhong cách: Bụi bặm, nam tính và phong trần.\r\n\r\nTên sản phẩm: Classic Blue Wash Jeans – Chất Denim Nguyên Bản.\r\n\r\nChất liệu: Denim dày dặn, được wash màu xanh trung tính cổ điển, cực kỳ bền bỉ và không lỗi mốt theo thời gian.\r\n\r\nThiết kế: Form dáng suông rộng vừa phải, đường chỉ may nổi bật tạo cảm giác khỏe khoắn.\r\n\r\nGợi ý phối đồ: Mặc cùng áo sơ mi Denim (Denim-on-denim) hoặc áo Hoodie cho những ngày trời lạnh.', 159.00, 249.00, 'img/quan/sanpham3.1.jpg\r\nimg/quan/sanpham3.2.jpg', '2025-12-29 18:34:34', 0),
(12, 'Quần Jogger/Thun Ống Suông ', 3, 3, 'Quần Jogger/Thun Ống Suông \r\nPhong cách: Thoải mái, phóng khoáng và đậm chất thể thao.\r\n\r\nTên sản phẩm: Comfort Grey Sweats – Tự Do Chuyển Động.\r\n\r\nChất liệu: Vải nỉ chân cua mềm mại, thấm hút mồ hôi tốt, tạo cảm giác cực kỳ êm ái khi tiếp xúc với da.\r\n\r\nThiết kế: Lưng thun co giãn có dây rút linh hoạt, ống quần suông rộng thời thượng giúp cử động hoàn toàn tự do.\r\n\r\nGợi ý phối đồ: Hoàn hảo để mặc đi tập, đi bay hoặc phối cùng áo Sweater cho outfit ở nhà/dạo phố thoải mái.', 159.00, 219.00, 'img/quan/sanpham4.1.jpg\r\nimg/quan/sanpham4.2.jpg', '2025-12-29 20:53:52', 0),
(13, 'Dép quai ngang', 4, 4, 'Dòng dép quai ngang nam thiết kế tối giản, form dép chắc chắn, quai bản to ôm chân tốt.\r\nMàu đen nam tính, phù hợp sử dụng đi trong nhà, đi chơi, đi dạo ngắn.\r\nĐế dép có vân chống trượt, giúp di chuyển an toàn trên nhiều bề mặt.', 249.00, 309.00, 'img/giaydep/dep1.1.jpg\r\nimg/giaydep/dep1.2.jpg', '2025-12-29 18:34:34', 0),
(14, 'Dép hihi', 4, 4, 'Dép quai ngang phong cách nhẹ nhàng – hiện đại, với hai tông màu đen và xám trung tính.\r\nThiết kế gọn gàng, mang lại cảm giác thoải mái khi sử dụng lâu.\r\nPhù hợp cho người thích phong cách đơn giản nhưng vẫn lịch sự.', 239.00, 279.00, 'img/giaydep/dep2.1.jpg\r\nimg/giaydep/dep2.2.jpg', '2025-12-29 18:34:34', 0),
(15, 'Dép quai chéo cách điệu', 4, 4, 'Dòng dép quai chéo cách điệu, tạo điểm nhấn thời trang so với dép quai ngang truyền thống.\r\nThiết kế trẻ trung, form dép thanh thoát, mang lại cảm giác chắc chân khi di chuyển.\r\nMàu đen và xám đậm dễ phối đồ, phù hợp nhiều hoàn cảnh.', 229.00, 269.00, 'img/giaydep/dep3.1.jpg\r\nimg/giaydep/dep3.2.jpg', '2025-12-29 18:34:34', 0),
(16, 'Giày sneaker nam', 5, 5, 'Dòng giày sneaker nam màu trắng, thiết kế trẻ trung, năng động.\r\nKiểu dáng cổ thấp, dễ mang, dễ phối với quần jeans, kaki hoặc quần short.\r\nPhù hợp cho đi học, đi chơi, sử dụng hằng ngày.', 319.00, 369.00, 'img/giaydep/giay1.1.jpg\r\nimg/giaydep/giay1.2.jpg', '2025-12-29 19:13:43', 0),
(17, 'Giày tây', 5, 5, 'Giày sneaker trắng phối đế nâu, tạo điểm nhấn thời trang và hiện đại.\r\nĐế cao su chắc chắn, có vân chống trượt, mang lại cảm giác êm chân khi di chuyển.\r\nThiết kế phù hợp cho người thích phong cách casual, lịch sự.', 329.00, 379.00, 'img/giaydep/giay2.1.jpg\r\nimg/giaydep/giay2.2.jpg', '2025-12-29 19:13:54', 0),
(18, 'Giầy cổ cao', 5, 5, 'Dòng giày sneaker phối màu trắng – đen – nâu, form giày khỏe khoắn, nam tính.\r\nThiết kế nổi bật, đường may chắc chắn, phù hợp cho người thích phong cách cá tính.\r\nDễ phối đồ streetwear hoặc trang phục thường ngày.', 329.00, 379.00, 'img/giaydep/giay3.1.jpg\r\nimg/giaydep/giay3.2.jpg', '2025-12-29 19:13:25', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `slider`
--

CREATE TABLE `slider` (
  `MaSL` int(11) NOT NULL,
  `TenSL` varchar(50) NOT NULL,
  `AnhSL` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `slider`
--

INSERT INTO `slider` (`MaSL`, `TenSL`, `AnhSL`) VALUES
(1, 'Áo Mùa Thu Đông', 'quangcao1.jpg'),
(2, 'Áo Xuân Hè', 'quangcao2.jpg'),
(3, 'Phụ Kiện', 'quangcao3.jpg');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bienthesp`
--
ALTER TABLE `bienthesp`
  ADD PRIMARY KEY (`MaBienThe`),
  ADD UNIQUE KEY `MaSP` (`MaSP`,`KichCo`,`MauSac`);

--
-- Chỉ mục cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  ADD PRIMARY KEY (`MaBL`);

--
-- Chỉ mục cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`MaCTDH`),
  ADD UNIQUE KEY `MaDH` (`MaDH`,`MaBienThe`),
  ADD KEY `MaBienThe` (`MaBienThe`);

--
-- Chỉ mục cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`MaDM`),
  ADD UNIQUE KEY `TenDM` (`TenDM`),
  ADD KEY `MaDMCong` (`MaDMCong`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`MaDH`),
  ADD KEY `MaND` (`MaND`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`MaND`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Chỉ mục cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`MaNCC`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`MaSP`),
  ADD KEY `MaDM` (`MaDM`),
  ADD KEY `MaNCC` (`MaNCC`);

--
-- Chỉ mục cho bảng `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`MaSL`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bienthesp`
--
ALTER TABLE `bienthesp`
  MODIFY `MaBienThe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  MODIFY `MaBL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `MaCTDH` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `MaDM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `donhang`
--
ALTER TABLE `donhang`
  MODIFY `MaDH` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `MaND` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  MODIFY `MaNCC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `MaSP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `slider`
--
ALTER TABLE `slider`
  MODIFY `MaSL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bienthesp`
--
ALTER TABLE `bienthesp`
  ADD CONSTRAINT `bienthesp_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`);

--
-- Các ràng buộc cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`MaDH`) REFERENCES `donhang` (`MaDH`),
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`MaBienThe`) REFERENCES `bienthesp` (`MaBienThe`);

--
-- Các ràng buộc cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD CONSTRAINT `danhmuc_ibfk_1` FOREIGN KEY (`MaDMCong`) REFERENCES `danhmuc` (`MaDM`);

--
-- Các ràng buộc cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`MaND`) REFERENCES `nguoidung` (`MaND`);

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`MaDM`) REFERENCES `danhmuc` (`MaDM`),
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`MaNCC`) REFERENCES `nhacungcap` (`MaNCC`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

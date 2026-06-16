/**
 * Khởi tạo các tính năng khi trang đã tải xong
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Cấu hình Slider tự động chuyển ảnh
    // Kiểm tra xem trang có Slider không mới chạy
    const mainSlider = document.querySelector('#mainSlider');
    if (mainSlider) {
        new bootstrap.Carousel(mainSlider, {
            interval: 2000, 
            ride: 'carousel',
            pause: 'hover'
        });
        console.log("Slider V'style đã khởi động.");
    }

    // 2. Xử lý logic đăng nhập nhanh (Header)
    const loginForm = document.querySelector('.header-login');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input');
            if (!inputs[0].value || !inputs[1].value) {
                e.preventDefault();
                alert("Vui lòng nhập đầy đủ tài khoản và mật khẩu!");
            }
        });
    }

    // 3. Hiệu ứng loading cho Auth Form (Đăng nhập/Đăng ký trang riêng)
    // SỬA LỖI: Thêm kiểm tra if (authForm) để tránh lỗi trang Tìm kiếm
    const authForm = document.querySelector('.auth-form');
    if (authForm) {
        authForm.addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-submit');
            if (btn) {
                btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Đang xử lý...';
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';
            }
        });
    }

    console.log("Giao diện V'style đã khởi chạy thành công.");
});

/**
 * Hiện/Ẩn mật khẩu (Hàm này gọi trực tiếp từ HTML nên không cần bọc DOMContentLoaded)
 */
function togglePassword(id) {
    const input = document.getElementById(id);
    // Sử dụng window.event để lấy icon nếu không truyền tham số
    const icon = event.target;
    
    if (input) {
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
}

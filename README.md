# Yumio — Website đặt đồ ăn nhanh (CNWeb)

Website đặt đồ ăn nhanh, viết bằng **PHP thuần theo mô hình MVC** và **MySQL**.
Giao diện tham khảo [leon.co](https://leon.co/), gồm nền kem, logo ở giữa, ảnh lớn, thẻ món có nền màu và nút bo tròn dạng viên thuốc.
Phần chuyển động theo skill `apple-design` (`.claude/skills/apple-design`).

## Chức năng

**Khách hàng**
- Trang chủ có banner, carousel món nổi bật kéo được, khối combo, danh mục, cửa hàng và lời mời đăng ký thành viên.
- Thực đơn theo danh mục, trang chi tiết món, tìm kiếm món theo tên.
- Giỏ hàng lưu trong session:
  - Thêm món bằng AJAX, có popover xác nhận.
  - Tăng giảm số lượng không cần tải lại trang.
  - Có thanh tiến độ tới mức miễn phí giao hàng.
- Đăng ký, đăng nhập; form kiểm tra lỗi ngay khi rời ô nhập, và kiểm tra lại ở server.
- Thanh toán (COD hoặc chuyển khoản), lịch sử đơn, theo dõi trạng thái, huỷ đơn khi còn chờ xác nhận.

**Quản trị (`/admin`)**
- Tổng quan: doanh thu, số đơn, đơn chờ xác nhận, món bán chạy.
- CRUD món ăn và danh mục:
  - Upload ảnh JPG, PNG hoặc WebP; kiểm tra MIME thật của file.
  - Chọn màu nền thẻ, xem trước ảnh.
- Lọc đơn hàng theo trạng thái và cập nhật trạng thái.
- Danh sách người dùng kèm số đơn và tổng chi tiêu.

**Bảo mật**
- PDO prepared statement.
- Token CSRF cho mọi form POST.
- Escape đầu ra (`e()`).
- Mật khẩu băm bằng `password_hash`, `session_regenerate_id` khi đăng nhập.

## Yêu cầu

- PHP >= 8.1 (extension `pdo_mysql`, `fileinfo`, `mbstring`)
- MySQL 8+ hoặc MariaDB 10.5+

## Cài đặt

```bash
# 1. Tạo CSDL và dữ liệu mẫu (6 danh mục, 30 món, 8 đơn hàng mẫu)
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql

# 2. Sửa thông tin kết nối trong config/database.php (hoặc dùng biến môi trường)

# 3. Chạy server
php -S localhost:8000 -t public
# hoặc: DB_PASSWORD=secret DB_PORT=3306 php -S localhost:8000 -t public
```

Nếu dùng XAMPP: đặt thư mục dự án vào `htdocs` rồi đổi `url` trong `config/app.php`
thành `http://localhost/Food-Ordering-Web-CNWeb` (hoặc đặt biến `APP_URL`).

**Tài khoản demo**

| Vai trò | Email | Mật khẩu |
| --- | --- | --- |
| Quản trị | `admin@yumio.vn` | `admin123` |
| Khách hàng | `khach@yumio.vn` | `123456` |

## Thiết kế và chuyển động

| Nguyên tắc (apple-design) | Áp dụng |
| --- | --- |
| Phản hồi ngay khi nhấn | `:active { transform: scale(.97) }` cho nút, chip, stepper |
| Spring, có thể ngắt giữa chừng | Lớp `Spring` (damping ratio + response) trong `main.js` |
| Kéo bám tay 1:1 | Carousel dùng Pointer Events + `setPointerCapture`, giữ đúng vị trí nắm |
| Chuyển giao vận tốc, đà quán tính | Thả tay thì spring nhận vận tốc; dự đoán điểm dừng bằng `0.998` |
| Rubber-band ở mép | Kéo quá biên bị cản dần, không dừng cứng |
| Nhất quán không gian | Popover giỏ hàng bung ra từ icon giỏ hàng; menu mobile vào và ra cùng phía trái |
| Lớp kính mờ | Header `backdrop-filter`, dải mờ ở mép thay cho đường kẻ |
| Typography | Tracking âm cho chữ lớn, leading rộng hơn cho dấu tiếng Việt |
| Trợ năng | `prefers-reduced-motion`, `prefers-reduced-transparency`, `prefers-contrast` |

Font: Be Vietnam Pro (chữ thường), Playfair Display (đoạn serif), Anton (tiêu đề lớn).
Tất cả đều là Google Fonts có hỗ trợ tiếng Việt.

> Hình ảnh sản phẩm lấy từ website KFC và McDonald's, **chỉ dùng cho mục đích học tập**.
> Cần thay bằng ảnh của riêng bạn trước khi đưa website lên mạng công khai.

## Cấu trúc thư mục

```
Food-Ordering-Web-CNWeb/
├── app/
│   ├── bootstrap.php            # Autoload, cấu hình lỗi, session
│   ├── Core/                    # "Framework" MVC tự xây
│   │   ├── Router.php           # Định tuyến URL -> Controller@action, tham số {slug}
│   │   ├── Controller.php       # view, json, redirect, validate, notFound
│   │   ├── Model.php            # all, find, where, create, update, delete
│   │   ├── View.php             # Render view trong layout
│   │   ├── Validator.php        # required|email|phone|min|max|in|same...
│   │   ├── Upload.php           # Upload ảnh an toàn
│   │   └── Database.php         # Kết nối PDO (singleton)
│   ├── Controllers/             # C: nhận request, gọi Model, trả View
│   │   ├── HomeController.php   # Trang chủ, giới thiệu, cửa hàng
│   │   ├── FoodController.php   # Thực đơn, danh mục, chi tiết, tìm kiếm
│   │   ├── CartController.php   # Giỏ hàng (HTML + JSON)
│   │   ├── OrderController.php  # Thanh toán, lịch sử, huỷ đơn
│   │   ├── AuthController.php
│   │   └── Admin/               # Dashboard, Food, Category, Order, User
│   ├── Models/                  # M: User, Category, Food, Order, OrderItem, Cart
│   ├── Views/                   # V: layouts/, partials/, home/, food/, cart/, order/, auth/, admin/
│   ├── Middlewares/             # Auth, Admin, Csrf
│   └── Helpers/functions.php    # e(), url(), image_url(), format_price(), icon(), old(), error()...
├── config/                      # app.php, database.php
├── routes/web.php               # Khai báo toàn bộ route
├── database/                    # schema.sql, seed.sql
├── public/                      # Document root
│   ├── index.php                # Front controller
│   ├── assets/css/              # style.css (khách hàng), admin.css
│   ├── assets/js/               # main.js (spring, carousel, giỏ hàng, validate), admin.js
│   ├── assets/images/           # Ảnh món mẫu (WebP), banner, favicon
│   └── uploads/                 # Ảnh do admin upload (không commit)
├── storage/logs/
└── .claude/skills/apple-design/ # Skill thiết kế dùng cho dự án
```

## Luồng xử lý một request

```
Trình duyệt -> public/index.php -> Router (routes/web.php)
            -> Middleware (Auth/Admin/Csrf) -> Controller -> Model <-> MySQL
            -> View (layout + partials) -> HTML / JSON trả về trình duyệt
```

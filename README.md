# Yumio — Website đặt đồ ăn nhanh (CNWeb)

Website đặt đồ ăn nhanh, viết bằng **PHP thuần theo mô hình MVC** và **MySQL**.
Giao diện **Modern Playful Maximalism**: trắng, đỏ, be, vàng và đen, typography lớn, thẻ món có chiều sâu, poster hero và About kể chuyện. Giữ cấu trúc MVC và nghiệp vụ của nhóm. Tham khảo định hướng motion/storytelling của Henrietta, Agrumea Farm và Neutomni; không sao chép tài nguyên từ các trang đó.
Motion dùng CSS/JavaScript nhẹ, IntersectionObserver và parallax giới hạn, hỗ trợ `prefers-reduced-motion`. Xanh lá chỉ làm điểm nhấn trạng thái/minh hoạ.

## Chức năng

**Khách hàng**
- Trang chủ có banner, carousel món nổi bật kéo được, khối combo, danh mục, cửa hàng và lời mời đăng ký thành viên.
- `/menu` hiển thị toàn bộ món đang bán, chia section theo danh mục có món; tab sticky cuộn nhanh và tự active theo vị trí đọc. URL danh mục cũ dẫn tới section tương ứng. Giữ trang chi tiết món và tìm kiếm theo tên.
- Giỏ hàng lưu trong session:
  - Thêm món bằng AJAX, có popover xác nhận.
  - Tăng giảm số lượng không cần tải lại trang.
  - Có thanh tiến độ tới mức miễn phí giao hàng.
- Đăng ký, đăng nhập; form kiểm tra lỗi ngay khi rời ô nhập, và kiểm tra lại ở server.
- Đặt COD, lưu đơn/chi tiết/lịch sử trạng thái bằng transaction MySQL. Chống gửi trùng checkout và tính tiền tại server.
- Lịch sử đơn, tiến trình giao hàng, huỷ đơn khi còn chờ xác nhận.
- Checkout hiển thị địa chỉ → bản đồ tự mở → ghi chú → xem lại đơn. Gợi ý địa chỉ Việt Nam bằng Photon/OpenStreetMap có debounce/cache/giới hạn request; chọn gợi ý cập nhật ghim và zoom. Sửa địa chỉ bỏ ghim cũ; bản đồ/API lỗi vẫn nhập tay để đặt COD.
- Checkout hai bước: nhập thông tin → xem lại → chủ động xác nhận. Enter trong ô nhập không tạo đơn, kể cả khi tắt JavaScript.

**Quản trị (`/admin`)**
- Tổng quan: doanh thu, số đơn, đơn chờ xác nhận, món bán chạy.
- CRUD món ăn và danh mục:
  - Upload ảnh JPG, PNG hoặc WebP; kiểm tra MIME thật của file.
  - Chọn màu nền thẻ, xem trước ảnh.
- Lọc đơn hàng, cập nhật theo thứ tự trạng thái, phân công nhân viên giao hàng.
- Danh sách người dùng kèm số đơn và tổng chi tiêu.
- Cấp vai trò giao hàng cho tài khoản khách hàng; không tự cấp vai trò admin qua đăng ký.

**Nhân viên giao hàng (`/shipper`)**
- Xem các đơn được phân công, địa chỉ, ghi chú và bản đồ.
- Nhận món từ bếp, bắt đầu giao và xác nhận giao thành công.
- Không truy cập được quản trị hoặc đơn của tài xế khác.

**Bảo mật**
- PDO prepared statement.
- Token CSRF cho mọi form POST.
- Escape đầu ra (`e()`).
- Mật khẩu băm bằng `password_hash`, `session_regenerate_id` khi đăng nhập.

## Yêu cầu

- PHP >= 8.1 (extension `pdo_mysql`, `fileinfo`, `mbstring`)
- MySQL 8+ hoặc MariaDB 10.4+; đã chạy thực tế trên XAMPP MariaDB 10.4.32.
- Extension `curl` dùng cho gợi ý địa chỉ và script kiểm thử HTTP. Thiếu extension này thì đặt hàng nhập tay vẫn hoạt động.

## Cài đặt

**XAMPP/phpMyAdmin:** xem [hướng dẫn import và cấu hình MySQL](docs/MYSQL_SETUP.md). Dùng schema cho database mới; không chạy lại seed trên database có dữ liệu.

Database đã import thì **không chạy lại schema hoặc seed**. Bật MySQL trong XAMPP, chạy từ thư mục repository bằng PowerShell:

```powershell
cd 'C:\Bai_tap_DH\Nam_3\Cong_nghe_web\Food-Ordering-Web-CNWeb'
$env:DB_HOST = '127.0.0.1'
$env:DB_PORT = '3306'
$env:DB_DATABASE = 'yumio'
$env:DB_USERNAME = 'root'
$env:DB_PASSWORD = ''
$env:APP_URL = 'http://localhost:8000'
& 'C:\xampp\php\php.exe' -S localhost:8000 -t public public/router.php
```

Mở **http://localhost:8000**. Nếu cổng 8000 đang có server dự án chạy, dùng lại server đó. Có PHP trong PATH thì thay đường dẫn trên bằng `php`. Không cần chuyển repository vào `htdocs`; Apache chỉ cần cho phpMyAdmin. Dự án không dùng Java, Composer, npm hay bước build.

Tên biến đúng là `DB_DATABASE`, `DB_USERNAME`; ứng dụng chưa tự nạp `.env`. Tài khoản/mật khẩu MySQL khác nên được đặt qua biến môi trường, không đưa vào source. Muốn chạy Apache thay built-in server, đặt DocumentRoot vào `public/`, bật `mod_rewrite`/`AllowOverride` và cấu hình `APP_URL` tương ứng.

**Tài khoản demo**

| Vai trò | Email | Mật khẩu |
| --- | --- | --- |
| Quản trị | `admin@yumio.vn` | `admin123` |
| Khách hàng | `khach@yumio.vn` | `123456` |
| Giao hàng | `shipper@yumio.vn` | `123456` |

Luồng demo: khách đặt COD → admin xác nhận → chuẩn bị → phân công tài xế → tài xế bắt đầu giao → hoàn thành → khách mở chi tiết để xem lịch sử.

## Tài liệu và kiểm thử

- [Kiến trúc MVC, frontend/backend/database](docs/ARCHITECTURE.md)
- [Cấu hình và import MySQL bằng phpMyAdmin](docs/MYSQL_SETUP.md)
- [Kết quả kiểm thử, dữ liệu test và giới hạn](docs/TEST_REPORT.md)
- [Báo cáo các file thay đổi](docs/CHANGE_REPORT.md)
- [Thiết kế, checkout an toàn, geocoding và kiểm thử UI mới](docs/UI_UX.md)

Kiểm thử tích hợp khi server đã chạy:

```powershell
php scripts/smoke.php --write
```

Script dùng dữ liệu seed/demo, tạo tài khoản/đơn/danh mục/món và ảnh thử có nhãn riêng, giữ lại dữ liệu sau test; chỉ nên chạy khi muốn tạo thêm dữ liệu kiểm thử. `scripts/setup.php --seed` chỉ dành cho database trống; `scripts/migrate.php` dành cho schema cũ. Không cần hai lệnh đó trên database vừa import schema mới.

Chưa có GPS tài xế thời gian thực, định tuyến đường bộ hoặc thanh toán online. Gợi ý địa chỉ phụ thuộc dữ liệu Photon/OSM và có thể không có đầy đủ số nhà hoặc tên hành chính cũ. Tiến trình được làm mới khi tải trang; bản đồ dùng khoảng cách đường chim bay và phí cố định. Các đơn `bank` cũ vẫn hiển thị trong lịch sử, đơn mới chỉ nhận COD.

## Thiết kế và chuyển động

| Kỹ thuật tương tác | Áp dụng |
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
└── .claude/skills/apple-design/ # Tài liệu kỹ thuật cũ, được giữ lại
```

## Luồng xử lý một request

```
Trình duyệt -> public/index.php -> Router (routes/web.php)
            -> Middleware (Auth/Admin/Csrf) -> Controller -> Model <-> MySQL
            -> View (layout + partials) -> HTML / JSON trả về trình duyệt
```

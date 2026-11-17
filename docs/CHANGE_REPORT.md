# Báo cáo thay đổi và mức độ hoàn thiện

**Lượt UI/UX mới:** Modern Playful Maximalism, About kể chuyện/SVG/motion nhẹ, combobox Photon Việt Nam, checkout xem lại ở server và chặn Enter. Chi tiết file, chính sách API và 111 kiểm tra hành vi mới tại [UI_UX.md](UI_UX.md). Schema/database và Model tính tiền/đặt đơn không thay đổi trong lượt UI này.

Con số file thay đổi không tương đương số chức năng đã xong. Khi bắt đầu lượt tiếp tục, Git thực tế liệt kê 31 file: 20 file đã theo dõi bị sửa và 11 file mới. Con số 32 từ giao diện có thể khác cách đếm; danh sách bên dưới và phần kiểm thử là căn cứ đánh giá. Các thay đổi sẵn có được giữ và hoàn thiện tiếp; không commit/push.

## Trạng thái chức năng hiện tại

| Chức năng | Trạng thái | Đã xác minh |
| --- | --- | --- |
| Kết nối DB, khởi động Windows | Hoàn thiện cho XAMPP hiện tại | PHP/MariaDB kết nối thật, trang chạy với router đã thêm |
| Thực đơn, danh mục, chi tiết, tìm kiếm | Chức năng nhóm được giữ, đã kiểm tra | HTTP 200 và render không lỗi |
| Giỏ hàng | Hoàn thiện | Thêm/xóa/tăng/giảm, số lượng hợp lệ, tổng/phí, thao tác nhanh trên Chrome |
| Đăng ký/đăng nhập/phân quyền | Hoàn thiện mức demo | Hash mật khẩu, CSRF, kiểm tra chủ đơn, admin/shipper, đổi vai trò |
| Checkout COD/MySQL | Hoàn thiện | Đơn/chi tiết/sự kiện thật, transaction, chống trùng, chống giả tiền, xử lý lỗi |
| Quản lý đơn, giao hàng cơ bản | Hoàn thiện | Xác nhận, chuẩn bị, phân công, bắt đầu giao, hoàn thành, lịch sử |
| Admin món/danh mục | Chức năng cũ được giữ, sửa kiểm tra đầu vào | Tạo/sửa, upload PNG, ngừng bán; chưa chạy thao tác xóa |
| Bản đồ | Hoàn thiện mức cơ bản | Leaflet/OSM, ghim, lưu toạ độ, đường chim bay, lỗi bản đồ vẫn COD |
| UI/UX | Hoàn thiện các màn hình chính | Màu/typography, thêm nhanh, thông tin phí, timeline, mobile/desktop Chrome |
| Tài liệu MVC và cách chạy | Đã viết | `ARCHITECTURE.md`, `MYSQL_SETUP.md`, `TEST_REPORT.md`, README |
| GPS live, routing, thanh toán online | Chưa triển khai | Không tính vào chức năng đã hoàn thiện |
| Migration database cũ/MySQL 8 | Có mã, chưa chạy thực tế | Chỉ đọc và kiểm tra cú pháp; database hiện tại không cần migration |

## Các file đã có từ lượt sửa trước

| File | Vai trò và trạng thái hiện tại |
| --- | --- |
| `.gitignore` | Bỏ qua `.runtime/`, giữ dữ liệu thử và log ngoài Git |
| `README.md` | Cập nhật lệnh Windows, 3 tài khoản demo, luồng giao và liên kết tài liệu |
| `app/Controllers/Admin/FoodController.php` | Kiểm tra giá nguyên dương, danh mục tồn tại; đã tạo/sửa món thật |
| `app/Controllers/Admin/OrderController.php` | Endpoint cập nhật đúng bước/phân công; đã kiểm tra |
| `app/Controllers/Admin/UserController.php` | Cấp shipper cho khách, không cấp admin; đã kiểm tra 3 ca riêng |
| `app/Controllers/AuthController.php` | Điều hướng theo vai trò, đổi session, xử lý email trùng; đăng nhập/đăng ký đã kiểm tra |
| `app/Controllers/OrderController.php` | Token/snapshot checkout, COD, lỗi giữ giỏ, lịch sử/hủy; đã kiểm tra |
| `app/Core/Validator.php` | Kiểu scalar, số nguyên dương, kiểm tra dữ liệu mảng; đã kiểm tra |
| `app/Middlewares/AuthMiddleware.php` | Nạp lại vai trò từ DB; session nhận vai trò mới đã kiểm tra |
| `app/Middlewares/CsrfMiddleware.php` | Token đúng kiểu và hash_equals; đã kiểm tra CSRF bị thiếu |
| `app/Models/Order.php` | Transaction đặt đơn, tổng tiền server, khoá trạng thái/phân công, sự kiện; đã kiểm tra |
| `app/Models/OrderItem.php` | Hiển thị tên snapshot, tương thích đơn mẫu cũ; đã kiểm tra |
| `app/Models/User.php` | Vai trò và danh sách shipper; đã dùng thực tế |
| `app/Views/admin/user/index.php` | Hiển thị shipper, nút cấp vai trò; trang đã render |
| `app/Views/order/checkout.php` | COD và ghim tuỳ chọn; trước đó thiếu partial, nay đã hoàn thiện/test Chrome |
| `app/Views/order/detail.php` | 5 bước, tài xế, lịch sử, bản đồ; đã render đơn mới/cũ |
| `app/Views/partials/header.php` | Điều hướng shipper; bỏ truy vấn DB khỏi View |
| `database/schema.sql` | Schema mới đủ 6 bảng; người dùng import thành công |
| `database/seed.sql` | Bỏ TRUNCATE, thêm shipper demo; người dùng nạp thành công |
| `routes/web.php` | Routes phân công, cấp vai trò, shipper; đã gọi HTTP |
| `app/Controllers/ShipperController.php` | Kiểm tra tài xế sở hữu đơn; đã giao thành công qua UI |
| `app/Middlewares/ShipperMiddleware.php` | Chặn truy cập sai vai trò; đã kiểm tra |
| `app/Models/Delivery.php` | Kiểm tra toạ độ, Haversine; đã kiểm tra và lưu MySQL |
| `app/Views/partials/order_events.php` | Lịch sử có thời gian và nút làm mới; đã render |
| `app/Views/shipper/index.php` | Danh sách được phân công; đã kiểm tra |
| `app/Views/shipper/detail.php` | Nhận/giao/hoàn thành, địa chỉ/bản đồ; đã kiểm tra UI |
| `config/delivery.php` | Điểm xuất phát demo và URL tile cấu hình được |
| `database/migrations/001_delivery.sql` | Nâng schema gốc, không xóa dữ liệu; chưa chạy trên DB hiện tại |
| `docs/MYSQL_SETUP.md` | Hướng dẫn phpMyAdmin, cấu hình đã xác minh, cập nhật trạng thái test |
| `scripts/migrate.php` | Nâng cấp từng cột có kiểm tra tồn tại; cú pháp đạt, chưa thực thi migration |
| `scripts/setup.php` | Khởi tạo database trống và từ chối DB có bảng; cú pháp đạt, chưa thực thi trên DB này |

## File bổ sung/hoàn thiện trong lượt tiếp tục

- `public/router.php`: sửa lỗi lệnh chạy đang dùng; giữ file tĩnh trong document root.
- `app/Controllers/CartController.php`, `public/assets/js/main.js`, `app/Views/cart/index.php`: kiểm tra số lượng, phản hồi JSON có giá trị server, hàng đợi cập nhật, checkout đợi lưu giỏ, chặn thêm trùng khi request đang chạy.
- `app/Core/Controller.php`, `app/Core/Database.php`: Controller nạp dữ liệu điều hướng, giữ đầu vào cũ đúng kiểu; strict SQL mode riêng từng kết nối.
- `app/Views/admin/order/detail.php`: form phân công thật, chỉ hiện bước trạng thái hợp lệ, lịch sử/bản đồ.
- `app/Views/admin/food/_form.php`: đồng bộ giá nguyên dương với backend.
- `app/Views/partials/delivery_map.php`, `public/assets/js/delivery-map.js`, `public/vendor/leaflet/`: hoàn thiện bản đồ còn thiếu; JS/CSS local và giấy phép.
- `public/assets/css/style.css`, `app/Views/home/index.php`, `app/Views/home/about.php`, `app/Views/partials/carousel.php`: hoàn thiện màu sắc, thẻ món, thêm nhanh, phí giao, responsive; bỏ các lời hứa chưa có như GPS/thực đơn đặt lại một chạm.
- `scripts/smoke.php`: 61 ca kiểm tra HTTP/MySQL có ghi, giữ bản ghi thử để đối chiếu.
- `docs/ARCHITECTURE.md`, `docs/TEST_REPORT.md`, tài liệu này: giải thích MVC, bằng chứng test và giới hạn.

`public/.htaccess` giữ nguyên nội dung chức năng của nhóm; trạng thái file có thể phản ánh chuẩn hoá xuống dòng trên Windows. Không thay đổi routing Apache hay cấu trúc thư mục chính.

## Phần chưa hoàn thành

Chưa có định vị tài xế liên tục, tuyến đường bộ, phí theo khoảng cách hoặc thanh toán online. Gợi ý địa chỉ đã được bổ sung qua Photon, phụ thuộc độ phủ dữ liệu và khả dụng của dịch vụ. Trạng thái cần làm mới trang. Kiểm thử chưa bao gồm tải lớn, nhiều thiết bị/trình duyệt, migration database cũ và thao tác xóa dữ liệu. Các giới hạn chi tiết và ID dữ liệu thử ở [TEST_REPORT.md](TEST_REPORT.md) và [UI_UX.md](UI_UX.md).

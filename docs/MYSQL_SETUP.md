# Tạo và import database Yumio bằng XAMPP

## Cấu hình đã xác minh trên máy

Tại thời điểm kiểm tra ngày 17/11/2026:

- Apache: XAMPP, cổng 80.
- Database server: MariaDB 10.4.32 của XAMPP, `127.0.0.1:3306`.
- PHP PDO kết nối thành công với username `root`, mật khẩu trống.
- Lần đầu kiểm tra database `yumio` đã tồn tại nhưng chưa có bảng; người dùng đã import thành công trước giai đoạn kiểm thử tiếp theo.
- Dịch vụ MySQL80 riêng đang dừng. Không cần khởi động dịch vụ đó để chạy Yumio bằng XAMPP.

Đây là kết quả đọc trạng thái trên máy, không phải cấu hình được ghi thêm vào source. Không có database hiện tại nào bị sửa hoặc xoá trong bước kiểm tra này.

## Các file SQL

| File | Mục đích | Khi nào dùng |
| --- | --- | --- |
| `database/schema.sql` | Tạo database `yumio`, 6 bảng và khoá ngoại | Database mới, chưa có bảng |
| `database/seed.sql` | Thêm 3 tài khoản demo, 6 danh mục, 30 món, 8 đơn mẫu và chi tiết đơn | Sau khi import schema, chỉ nạp một lần |
| `database/migrations/001_delivery.sql` | Thêm phần giao hàng cho schema gốc của nhóm | Chỉ nâng cấp database theo phiên bản cũ |

Schema mới đã bao gồm `shipper`, trạng thái `preparing`, phân công giao hàng, toạ độ, token checkout và bảng `order_events`. Không import migration sau khi đã dùng schema mới.

File seed ban đầu có `TRUNCATE` để xoá dữ liệu trước khi nạp; bản hiện tại đã bỏ các lệnh này. Seed vẫn chỉ dùng cho database mới vì có ID cố định; chạy lại sẽ gặp trùng khoá. Không sửa bằng cách xoá bảng hoặc xoá database đang dùng.

## Import bằng phpMyAdmin

1. Mở **XAMPP Control Panel**. Kiểm tra **Apache** và **MySQL** đang chạy. MySQL trong XAMPP có thể hiển thị tên sản phẩm là MariaDB.
2. Mở `http://localhost/phpmyadmin/`.
3. Chọn database **yumio** ở cột bên trái. Trên máy hiện tại database này đã tồn tại và trống. Nếu máy khác chưa có, chọn **New / Mới**, đặt tên `yumio`, collation `utf8mb4_unicode_ci`, rồi chọn **Create / Tạo**.
4. Kiểm tra database chưa có bảng. Nếu đã có bảng hoặc dữ liệu, dừng import schema/seed và báo lại cấu trúc hiện tại; không dùng **Drop**, **Empty** hoặc xoá bảng.
5. Chọn tab **Import / Nhập** → **Choose File / Chọn tệp** → chọn `database/schema.sql` trong repository → định dạng **SQL**, bộ ký tự **utf-8** → **Import / Go / Thực hiện**.
6. Sau khi thành công, phải thấy 6 bảng: `users`, `categories`, `foods`, `orders`, `order_items`, `order_events`.
7. Vẫn chọn database `yumio`, vào **Import** lần nữa và chọn `database/seed.sql` → **Thực hiện**. Chỉ nạp một lần.
8. Kiểm tra số dòng: `users` = 3, `categories` = 6, `foods` = 30, `orders` = 8. Bảng `order_events` ban đầu trống; ứng dụng sẽ ghi khi có đơn/cập nhật mới.

Nếu import schema bị lỗi giữa chừng, giữ nguyên thông báo lỗi và các bảng đã tạo, không chạy lại seed hay xoá dữ liệu để thử lại.

## Cấu hình PHP

Ứng dụng đọc biến môi trường trong `config/database.php`:

| Biến ứng dụng đọc | Giá trị cho XAMPP hiện tại |
| --- | --- |
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `yumio` |
| `DB_USERNAME` | `root` |
| `DB_PASSWORD` | Chuỗi rỗng |

`DB_NAME` và `DB_USER` không phải tên biến mà code hiện tại đọc. Dự án chưa có bộ đọc `.env`; tạo `.env` riêng sẽ không tự nạp cấu hình.

Các giá trị mặc định hiện có trong repository trùng cấu hình XAMPP vừa xác minh, nên chưa cần sửa `config/database.php`. Khi dùng tài khoản khác, đặt biến môi trường, không ghi mật khẩu thật vào source.

Ví dụ chạy bằng PowerShell tại thư mục repository:

```powershell
$env:DB_HOST = '127.0.0.1'
$env:DB_PORT = '3306'
$env:DB_DATABASE = 'yumio'
$env:DB_USERNAME = 'root'
$env:DB_PASSWORD = ''
$env:APP_URL = 'http://localhost:8000'
php -S localhost:8000 -t public public/router.php
```

Mở `http://localhost:8000`. PHP có sẵn tại `C:\xampp\php\php.exe`; nếu chưa thêm PATH thì thay `php` bằng `& 'C:\xampp\php\php.exe'` trong PowerShell.

Apache phục vụ phpMyAdmin; PHP built-in server phục vụ dự án trực tiếp từ thư mục hiện tại, không cần chuyển repository vào `htdocs`. Chỉ `public/` là document root.

## Tài khoản dữ liệu demo

| Vai trò | Email | Mật khẩu demo |
| --- | --- | --- |
| Quản trị | `admin@yumio.vn` | `admin123` |
| Khách hàng | `khach@yumio.vn` | `123456` |
| Giao hàng | `shipper@yumio.vn` | `123456` |

Đây là tài khoản ứng dụng trong dữ liệu mẫu, khác tài khoản kết nối MySQL. Password ứng dụng được lưu dưới dạng hash trong SQL.

## Trạng thái kiểm thử

Kết nối PHP–MariaDB và các thao tác đặt COD, lưu đơn/chi tiết, phân công, giao hàng đã được kiểm thử thực tế trên database người dùng import. Không chạy lại seed/migration trên database này.

Chi tiết bằng chứng, các bản ghi kiểm thử được giữ lại và phần chưa kiểm chứng: [TEST_REPORT.md](TEST_REPORT.md). Kiến trúc: [ARCHITECTURE.md](ARCHITECTURE.md).

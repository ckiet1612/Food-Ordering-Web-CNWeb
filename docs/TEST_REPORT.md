# Báo cáo kiểm thử Yumio

Ngày kiểm tra: 17/11/2026, múi giờ Asia/Ho_Chi_Minh.

## Kiểm tra trước khi đưa thay đổi lên GitHub

- Đạt 64 kiểm tra HTTP/MySQL qua `scripts/smoke.php --write`: giỏ hàng, xem lại đơn, COD, tính tiền tại server, chống trùng, phân quyền, phân công và cập nhật giao hàng, quản trị món/danh mục.
- Đạt 9 kiểm tra Chrome bổ sung: Back/anchor và tab tự active khi cuộn, bản đồ checkout tự mở ở 1440/390/320px, chặn Enter, bản đồ chi tiết đơn vẫn hoạt động; không có exception JavaScript.
- Đạt kiểm tra cú pháp 86 file PHP, 6 file JavaScript ứng dụng và `git diff --check`.
- Server cũ đã dừng nên lần gọi HTTP đầu không kết nối được; đã khởi động lại bằng lệnh trong README rồi chạy lại thành công. Không phải lỗi kết nối MySQL.
- Lần hồi quy thành công: `YUMIO-E2E-20261117-185232-d639`; giữ lại đơn #27–#28, tài khoản/danh mục/món thử có nhãn riêng. Không xóa dữ liệu. Kết quả local trong `.runtime/` và ảnh upload thử không thuộc commit.
- Lượt này không chạy lại toàn bộ ca geocoding/lỗi API đã ghi ở `UI_UX.md`; kết quả bên dưới là lịch sử các đợt trước.

**Cập nhật UI/UX tiếp theo:** xem [UI_UX.md](UI_UX.md): 64 kiểm tra HTTP/MySQL, 27 kiểm tra UI và 20 kiểm tra biên đã đạt; PHP hiện có 86 file. Checkout đã có trang xem lại và geocoding Photon. Nội dung bên dưới ghi lại đợt kiểm thử nền tảng trước cải thiện UI, nên số liệu và giới hạn geocoding cũ mang tính lịch sử.

## Môi trường đã chạy thực tế

- Windows, PHP 8.1.25 tại `C:\xampp\php\php.exe`.
- XAMPP MariaDB 10.4.32, `127.0.0.1:3306`, database `yumio` do người dùng import.
- Website `http://localhost:8000`, PHP built-in server với `public/router.php`; dùng lại tiến trình server người dùng đã mở, không khởi động server PHP thứ hai trên cổng 8000.
- Chrome headless 157.0.8103.79, desktop 1440 × 1000 và mobile 390 × 844.
- Không dùng database riêng, không import lại SQL/seed, không commit hoặc push.

Lỗi khởi động tìm thấy: tiến trình PHP chạy `php -S localhost:8000 -t public public/router.php` nhưng file router chưa tồn tại. Trang trả HTTP 200 kèm lỗi PHP. Đã thêm router và kiểm tra nội dung HTML, không chỉ dựa vào HTTP 200.

## Kết quả đợt kiểm thử nền tảng

| Nhóm | Kết quả | Bằng chứng |
| --- | --- | --- |
| Cú pháp PHP | 83 file đạt | `php -l` trên `app/`, `config/`, `routes/`, `scripts/`, `public/` |
| Cú pháp JS | Đạt | `node --check` cho `main.js`, `admin.js`, `delivery-map.js` |
| HTTP + MySQL | 61 kiểm tra đạt | `php scripts/smoke.php --write`; kết quả lần đầy đủ cuối: `.runtime/YUMIO-E2E-20261117-171351-6ab6/result.json` |
| Trình duyệt | 16 kiểm tra đạt; không có exception JS chưa xử lý | `.runtime/browser-result.json`; nền OSM tải thành công |
| Cấp vai trò | 3 kiểm tra đạt | Chặn cấp admin; cấp shipper cho tài khoản thử; session đang đăng nhập nhận vai trò mới ở request tiếp theo |
| Whitespace/diff | Đạt | `git diff --check` |

Tổng 80 mục kiểm tra hành vi thuộc ba nhóm HTTP/MySQL, trình duyệt và cấp vai trò. Các lần chạy trung gian không cộng lại vào tổng này. File `.runtime/` là dữ liệu kiểm thử local, được Git bỏ qua.

## Nội dung đã xác minh

1. Trang chủ, danh mục, thực đơn, chi tiết món, tìm kiếm và giỏ render không có lỗi PHP.
2. Thêm/xóa/tăng/giảm giỏ; từ chối số lượng 0, âm, lớn hơn 50, số thập phân và mảng. Phí 15.000₫; miễn phí từ 200.000₫.
3. Thêm nhanh bằng AJAX cập nhật badge/popover. Bấm tăng liên tục rồi checkout ngay giữ đủ số lượng. Tăng rồi xóa không tạo lại món đã xóa.
4. Đăng ký ghi người dùng và hash mật khẩu thật, không chấp nhận quyền admin do client chèn vào. Đã đăng nhập các tài khoản demo khách/admin/shipper qua HTTP và Chrome.
5. CSRF bắt buộc; khách chưa đăng nhập bị chuyển tới login. Khách không vào được admin/shipper, không đọc/hủy đơn của người khác. Shipper không đọc được đơn chưa được phân công.
6. Checkout từ chối điện thoại/toạ độ/dữ liệu dạng mảng không hợp lệ; trang báo lỗi không phát sinh cảnh báo PHP. Token tab checkout cũ bị từ chối.
7. COD ghi `orders`, `order_items`, `order_events`; tiền do server tính, bỏ qua tổng tiền giả từ client. Gửi lại cùng token không thêm đơn. Giỏ chỉ xoá sau khi lưu thành công.
8. Model từ chối giỏ có giá đã thay đổi, rollback và không lưu đơn lỗi. Đây là ca kiểm thử lỗi giá ở Model, chưa phải kiểm thử đồng thời nhiều tiến trình hoặc mô phỏng mất điện MySQL.
9. Không cho bỏ bước trạng thái hoặc mở lại đơn đã hoàn thành. Khách không hủy sau xác nhận. Chưa có tài xế thì không bắt đầu giao.
10. Admin xác nhận, chuẩn bị và phân công; shipper nhận món, bắt đầu giao, hoàn thành. Lịch sử có thời điểm/người thao tác trong MySQL.
11. Tạo/sửa danh mục và món thử; upload ảnh PNG bằng HTTP multipart, kiểm tra file thật được lưu; ngừng bán món thử và từ chối đặt món này.
12. Leaflet chọn ghim được trên mobile, có attribution; server lưu toạ độ và khoảng cách. Nền OpenStreetMap đã tải được.
13. Chặn tải `leaflet.js`/tile bằng Chrome: xuất hiện hướng dẫn dùng địa chỉ, đơn COD không có toạ độ vẫn được lưu.
14. Trang chủ desktop, thực đơn và checkout mobile không tràn ngang. Đã xem ảnh chụp thực tế, gồm bản đồ và quản trị phân công.

## Dữ liệu kiểm thử được giữ lại

Trước kiểm thử: 3 users, 6 categories, 30 foods, 8 orders, 20 order_items, 0 order_events. Không sửa các đơn gốc #1–#8; kiểm tra lại trạng thái/tổng tiền của chúng sau test.

| Đơn | Nội dung | Kết quả cuối |
| --- | --- | --- |
| #9, #13, #15 | HTTP, một phần gà 72.000₫ + phí 15.000₫ | 87.000₫ COD, hoàn thành qua tài xế demo |
| #10, #14, #16 | HTTP, ba phần gà và ghim bản đồ | 216.000₫, phí 0, hủy khi pending |
| #11, #17 | Chrome, tăng nhanh tới bảy phần gà, chọn ghim | 504.000₫, phí 0, hoàn thành bằng UI admin/shipper |
| #12, #18 | Chrome, chặn thư viện bản đồ | 87.000₫ COD, pending, không có toạ độ |

Các đơn/tài khoản có nhãn `YUMIO-E2E-*` hoặc `YUMIO-BROWSER-*`. Tạo thêm 3 tài khoản thử; chỉ tài khoản thử của lần chạy cuối được cấp shipper để kiểm tra đổi vai trò. Danh mục thử #7–#8 và món thử #31–#32 được giữ lại; hai món đã ngừng bán. Một ảnh PNG thử nằm trong `public/uploads/foods/` và được Git bỏ qua. Không chạy các thao tác xóa dữ liệu để dọn test.

Ảnh chụp local: `.runtime/home-desktop.png`, `.runtime/menu-mobile.png`, `.runtime/checkout-mobile-map.png`, `.runtime/admin-order-desktop.png`.

## Giới hạn và phần chưa thực hiện

- Chưa có GPS tài xế liên tục, geocoding/tìm địa chỉ tự động, tuyến đường bộ hoặc tính phí theo quãng đường. Khoảng cách đang là đường chim bay từ một điểm xuất phát demo.
- Theo dõi đơn bằng tải lại trang/nút làm mới, chưa tự cập nhật bằng polling/WebSocket.
- Chỉ đơn mới COD. Không có cổng thanh toán hay xác nhận chuyển khoản; đơn `bank` mẫu cũ vẫn được hiển thị.
- Chưa kiểm tra định vị bằng thiết bị thật, iOS/Safari/Firefox, tải lớn hoặc tranh chấp nhiều tiến trình. Đã kiểm tra thao tác liên tiếp trên Chrome, nhưng đó không thay thế kiểm thử tải.
- Chưa chạy `scripts/setup.php` và `scripts/migrate.php` trên database người dùng vì schema đã đầy đủ. Các file này đã đọc/kiểm tra cú pháp; chưa xác nhận quy trình migration thực tế trên database cũ hoặc MySQL 8.
- Chức năng xóa món/danh mục cũ được giữ lại nhưng không chạy test xóa trên database này. Không có thay đổi UI quy mô lớn hay thay thế ảnh sản phẩm nhóm đã chuẩn bị.

## Chạy lại

Làm theo [README](../README.md) để bật server. `php scripts/smoke.php --write` tạo thêm dữ liệu kiểm thử có nhãn riêng và không dọn/xóa sau test; không nên chạy chỉ để xem website. Script mặc định dùng giá/dữ liệu seed và tài khoản demo. Có thể đặt `TEST_URL`, `TEST_ADMIN_EMAIL`, `TEST_ADMIN_PASSWORD` qua biến môi trường; không đưa mật khẩu thật vào source.

Các ca Chrome được thực hiện bằng Chrome headless cục bộ qua DevTools Protocol, không thêm npm package vào ứng dụng. Mã kiểm tra và kết quả tạm nằm trong `.runtime/`; để demo bằng tay, đi lần lượt: khách đặt COD → admin xác nhận/chuẩn bị/phân công → shipper nhận/giao/hoàn thành → khách làm mới chi tiết đơn.

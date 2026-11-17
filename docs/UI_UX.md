# Yumio: UI/UX, địa chỉ thông minh và checkout an toàn

Cập nhật ngày 17/11/2026. Đây là phần phát triển tiếp trên cùng PHP MVC, database và nghiệp vụ đặt COD/giao hàng hiện có. Không chạy migration hoặc import SQL trong lượt này.

## Cập nhật mới: checkout gọn và thực đơn cuộn liên tục

- Checkout dùng lại `delivery_map.php`, hiển thị theo thứ tự địa chỉ → bản đồ → ghi chú → xem lại. Chế độ `pick` tự tải, không có nút mở hoặc khu vực tiêu đề “Ghim vị trí nhận hàng”. Giữ click ghim, gợi ý, định vị, bỏ ghim và retry khi tải lỗi. Chiều cao vùng bản đồ được giữ khi lỗi để hạn chế dịch chuyển form; vẫn đặt COD bằng địa chỉ nhập tay.
- Các trang xem lại/chi tiết/admin/tài xế tiếp tục dùng chế độ `view` mở theo yêu cầu. Không viết component hoặc bộ nạp Leaflet thứ hai.
- `FoodController::index()` dùng lại `Food::where()` và `Category::ordered()`, nhóm món đang bán theo danh mục có thật, giữ thứ tự danh mục và bỏ nhóm rỗng. `food/index.php` render tất cả bằng `food_tile.php` hiện có; không query theo từng section và không tạo danh mục trong database.
- `/menu/{slug}` vẫn kiểm tra danh mục tồn tại rồi chuyển về `/menu#menu-category-{id}`. Danh mục không có món đang bán chuyển về `/menu`, không tạo anchor rỗng. View category cũ được giữ trong repository, không tạo một phiên bản giao diện mới trùng lặp.
- `initMenu()` được thêm vào `main.js`, dùng lại helper và media query sẵn có: tab sticky dưới header thực tế, click cuộn mượt, cuộn tay cập nhật `aria-current`, đưa tab active vào khung nhìn ngang trên mobile. ResizeObserver đo header/thanh tab, scroll listener passive chỉ lập lịch một rAF, ảnh món có sẵn khung aspect-ratio. Hỗ trợ hash/Back và giảm chuyển động; không gọi scrollIntoView trên tab để tránh kéo cả trang ngoài ý muốn.
- Giữ palette, thẻ món, AJAX cart, CSRF, chữ ký bước xem lại, giá/phí server và transaction đặt đơn. Không thêm dependency, không sửa schema hoặc Model nghiệp vụ.

**File sửa trong cập nhật này:**

| File | Nội dung |
| --- | --- |
| `app/Controllers/FoodController.php` | Nhóm món cho menu liên tục; URL danh mục cũ dẫn tới anchor |
| `app/Views/food/index.php` | Thanh tab và các section dùng lại thẻ món |
| `app/Views/order/checkout.php` | Đưa bản đồ sau địa chỉ, trước ghi chú; rút gọn hướng dẫn |
| `app/Views/partials/delivery_map.php` | Chế độ checkout tự mở và nút retry khi lỗi |
| `public/assets/js/delivery-map.js` | Dùng chung auto/view, giữ các thao tác bản đồ và fallback |
| `public/assets/js/address-search.js` | Rút gọn thông báo chọn địa chỉ |
| `public/assets/js/main.js` | Điều hướng anchor/sticky/scroll spy qua `initMenu()` |
| `public/assets/css/yumio-theme.css` | Bố cục tab/section, bản đồ inline gọn và trạng thái lỗi |
| `scripts/smoke.php` | Cập nhật kỳ vọng redirect của URL danh mục cũ |
| `README.md`, tài liệu này | Đồng bộ cách dùng và bằng chứng kiểm thử |

**Kiểm thử của cập nhật này:** 29 kiểm tra chính và 9 kiểm tra bổ sung trên Chrome đạt. Có 30 món đang bán trong 6 danh mục; 3 danh mục chỉ có món thử ngừng bán không xuất hiện trong tab/section. Đã đối chiếu danh sách ID món/danh mục giữa HTML và database, không chỉ đếm thẻ trên trang.

Đã kiểm tra sticky/click/cuộn tay/hash/Back, tab active tự cuộn ngang, reduced motion, giỏ AJAX, địa chỉ/map/ghi chú đúng thứ tự, bản đồ tự mở, ghim bằng click, chọn gợi ý Photon thật/cache, định vị với dữ liệu thiết bị mô phỏng, xóa ghim cũ khi sửa địa chỉ, lỗi tải thư viện và retry, lỗi geocoding, COD và bản đồ chi tiết đơn. Menu được kiểm tra ở 1440/768/390/320px; bản đồ checkout ở 1440/390/320px. Không có exception JS hoặc lỗi PHP trong log kiểm thử.

Bằng chứng local: `.runtime/menu-checks-result.json` (29 đạt), `.runtime/menu-extra-result.json` (9 đạt), ảnh `.runtime/continuous-menu-desktop.png`, `continuous-menu-mobile.png`, `inline-map-desktop.png`, `inline-map-mobile.png`. Kiểm tra cú pháp PHP/JS và `git diff --check` đạt. Không chạy lại toàn bộ CRUD smoke suite trong lượt chỉnh giao diện này.

Từ nền 23 đơn, đã tạo và giữ lại #24 (`YUMIO-CONTINUOUS-MENU-*`, 87.000₫, có tọa độ) và #25 (`YUMIO-INLINE-MAP-FALLBACK-*`, 87.000₫, địa chỉ nhập tay không có tọa độ). Không xóa dữ liệu, commit hoặc push. Geolocation thiết bị thật/Safari chưa được xác minh; các giới hạn Photon bên dưới vẫn áp dụng.

## Định hướng thiết kế

**Modern Playful Maximalism**, theo yêu cầu người dùng: trắng, đỏ, be, vàng và đen; xanh lá chỉ còn ở một số trạng thái/minh hoạ. Typography lớn, CTA nổi, ảnh món rõ, viền/shadow có chủ đích, nhãn món và texture chấm tĩnh. Không dùng ngôn ngữ thiết kế Apple.

`public/assets/css/yumio-theme.css` là lớp thiết kế chung nằm sau CSS cũ, để giữ component/markup/nghiệp vụ của nhóm. Màu nền thẻ dùng bảng màu giới hạn qua CSS, không đổi các giá trị `bg_color`/`color` trong database. Admin dùng cùng token, cỡ chữ dễ đọc hơn và tiết chế trang trí ở bảng.

Homepage giữ các dữ liệu món/danh mục/combo, đổi hero thành poster nhiều lớp và combo thành bố cục ảnh–nội dung. About có hero minh hoạ, các chương kể chuyện, quy trình giao hàng và các phần chính sách có thể mở bằng bàn phím. Giữ các neo `#giao-hang`, `#dinh-duong`, `#dieu-khoan`, `#bao-mat` để link cũ vẫn dùng được.

Motion trong `public/assets/js/motion.js` dùng IntersectionObserver, reveal một lần và parallax giới hạn 18–24px chỉ trên lớp minh hoạ đang nhìn thấy. Listener cuộn là passive; requestAnimationFrame chỉ được lập lịch khi cần, không chạy liên tục lúc đứng yên. Form không có reveal/parallax. `prefers-reduced-motion` làm nội dung hiện đầy đủ và tắt parallax.

Minh hoạ burger tại `public/assets/images/yumio-burger.svg` được vẽ riêng bằng SVG, dung lượng khoảng 1,7KB. Không thêm 3D, framework, npm package hay thư viện animation. Ảnh món tiếp tục dùng tài nguyên sẵn có của nhóm; không tải tài nguyên từ các website tham khảo.

## Checkout hai bước

1. GET `/checkout` tạo token và ảnh chụp giỏ như trước. Form nhập thông tin chỉ POST tới `/checkout/review`.
2. `OrderController::review()` kiểm tra CSRF, token, thông tin, tọa độ và giỏ. Chỉ render `app/Views/order/review.php`, không ghi order/items/events và không xoá giỏ. Session giữ chữ ký dữ liệu đã xem.
3. Trang xem lại hiện địa chỉ, người nhận, điện thoại, ghi chú, tất cả món/số lượng/giá, phí và tổng COD. Có link quay lại sửa.
4. Nút cuối có `name=checkout_action`, `value=confirm`, gửi POST `/checkout`. Server kiểm tra action và chữ ký khớp thông tin đã xem, sau đó mới gọi transaction đặt hàng cũ.

`public/assets/js/checkout.js` chặn Enter từ input/select, chặn submit không có nút chủ động và khóa gửi lặp ở bước cuối. Textarea vẫn có thể xuống dòng. Nút xem lại/xác nhận vẫn dùng được bằng Tab + Enter/Space. Combobox dùng ↑/↓/Enter để chọn địa chỉ; Enter này không gửi form.

Khi JavaScript bị tắt, Enter ở form nhập liệu có thể đi tới trang xem lại, nhưng không thể tạo đơn vì form đó không trỏ tới endpoint ghi đơn. Trang xem lại không có ô nhập hiển thị; xác nhận cuối vẫn phải kích hoạt nút. Giữ CSRF, UNIQUE checkout_token, snapshot giỏ, tính tiền server và transaction hiện có. Bấm hai lần/replay token không thêm đơn trùng.

## Gợi ý địa chỉ từ dữ liệu thật

- Route GET `/addresses/search?q=...` yêu cầu đăng nhập, dùng `app/Controllers/AddressController.php` và `app/Models/Geocoder.php`.
- Nhà cung cấp mặc định: Photon demo tại `https://photon.komoot.io/api`, hỗ trợ search-as-you-type theo [tài liệu chính thức](https://github.com/komoot/photon#features). [Chính sách demo](https://github.com/komoot/photon#demo-server) cho phép dùng ở mức hợp lý, có thể giới hạn/chặn và không bảo đảm khả dụng. Không giả định có SLA hay quota được cấp riêng.
- Không dùng Nominatim công cộng cho autocomplete vì [chính sách Nominatim](https://operations.osmfoundation.org/policies/nominatim/) cấm cách dùng đó.
- Query tối thiểu 4, tối đa 255 ký tự; `countrycode=VN`, tối đa 5 kết quả. Không yêu cầu khách thêm tên quốc gia. Giữ số nhà, tên đường, locality/district/county/city/state thực tế khi API trả về, không tự thay tên đơn vị hành chính cũ/mới.
- Frontend debounce 700ms, khoảng cách tối thiểu 1.400ms giữa các lần gọi, hủy request cũ và bỏ response đã lỗi thời. Cache bộ nhớ giới hạn 30 query; hỗ trợ loading, kết quả rỗng, timeout và lỗi dịch vụ.
- Backend tối đa 12 request/phút/session và giới hạn chung mọi session trên máy: một lần bắt đầu gọi upstream mỗi 1,3 giây. Request cache không gọi lại upstream. Khi bị giới hạn trả HTTP 429 và Retry-After.
- Cache server có TTL hiệu lực 6 giờ tại `storage/cache/geocoding/`, nằm ngoài document root và được Git bỏ qua. Kết nối HTTPS giữ kiểm tra TLS, connect timeout 2s, total timeout 5s; không tắt SSL verification.
- Người dùng chọn gợi ý thì text địa chỉ và tọa độ cập nhật cùng lúc; bản đồ tự mở, di chuyển và zoom tới marker. Không cần bấm bản đồ. Sửa text địa chỉ lập tức xóa tọa độ/marker cũ.
- Không có kết quả hoặc API lỗi thì không tự đặt tọa độ. Người dùng vẫn có thể gõ địa chỉ đầy đủ, chọn ghim thủ công hoặc đặt COD không có ghim.

Photon có thể chỉ tìm được đường hoặc địa danh, không biết chính xác số nhà. Kết quả không phải chứng nhận địa chỉ giao hàng; giao diện yêu cầu khách kiểm tra số nhà và cung cấp bước xem lại. Tên hành chính cũ chỉ tìm/hiển thị được nếu dữ liệu nguồn còn có; không hứa hỗ trợ mọi địa chỉ tại Việt Nam.

Ô địa chỉ có disclosure về gửi query tới dịch vụ, attribution Photon/OpenStreetMap, role combobox/listbox/option, active descendant, trạng thái live và điều khiển bằng bàn phím. Dữ liệu API được chèn bằng textContent, không innerHTML.

## Cấu hình

```powershell
# Mặc định bật Photon demo; không cần API key.
$env:GEOCODING_ENABLED = '1'
# Có thể thay bằng endpoint Photon tương thích do bạn quản lý.
$env:GEOCODING_URL = 'https://photon.komoot.io/api'
```

Để chỉ dùng nhập tay, đặt `GEOCODING_ENABLED=0` trước khi khởi động PHP. Endpoint thay thế phải là HTTPS và tương thích Photon, không phải URL tìm kiếm Nominatim. Khi dùng cho nhiều người hoặc triển khai thực tế, chọn nhà cung cấp/instance có chính sách và năng lực phù hợp; giới hạn file local không thay thế rate limiter phân tán.

PHP cURL cần CA bundle hợp lệ. XAMPP hiện tại đã cấu hình `curl.cainfo` và gọi Photon thành công; nếu máy khác lỗi certificate, cấu hình CA đúng trong php.ini, không tắt TLS verification. Thiếu cURL hoặc API lỗi chỉ làm gợi ý trả 503, không khóa COD nhập tay.

## Kiểm thử thực tế của lượt này

| Nhóm | Kết quả |
| --- | --- |
| HTTP/MySQL hồi quy | 64 kiểm tra đạt, bao gồm review không ghi đơn, COD, chống trùng, quyền, giao hàng và quản trị cũ |
| Chrome chức năng UI | 27 kiểm tra đạt: debounce, dữ liệu Photon thật, chọn bằng bàn phím, bản đồ tự căn giữa, Enter an toàn, double click, lỗi API/map, reduced motion |
| Chrome trường hợp biên | 20 kiểm tra đạt: 320/360/768/1024px, bỏ ghim cũ, query ngắn, 429, Enter không JavaScript, Admin desktop/mobile |
| Rà soát cuối Admin mobile | Đạt: 5 nhãn điều hướng đọc được, vùng chạm ≥44px, icon ≥20px, không tràn ngang |
| API trả HTML/JSON lỗi | Đạt: giữ địa chỉ nhập tay, không đoán tọa độ, hiển thị fallback tiếng Việt |
| Cú pháp | 86 file PHP và toàn bộ JS ứng dụng được kiểm tra |

Ba bộ kiểm tra có 111 assertion hành vi, cộng kiểm tra riêng cho vùng chạm Admin mobile và response API không phải JSON: 64 kiểm tra hồi quy, 49 kiểm tra UI/trường hợp biên. Không cộng lại các lần chạy trung gian. Chrome kiểm tra trên desktop 1440px và mobile 390px, cùng các chiều rộng ở bảng. Xem thêm lịch sử ở [TEST_REPORT.md](TEST_REPORT.md).

Dữ liệu trước lượt UI có 19 đơn. Lượt này tạo thêm #20 (Photon, 216.000₫), #21 (API/map lỗi, nhập tay 87.000₫), #22 (hồi quy giao thành công 87.000₫), #23 (hồi quy hủy khi pending, 216.000₫). Các đơn có nhãn `YUMIO-UI-*` hoặc `YUMIO-E2E-*`. Hồi quy còn tạo một tài khoản, danh mục #9 và món #33; món thử đã ngừng bán. Giữ lại toàn bộ, không xoá. Đơn đã có #19 vẫn giữ trạng thái đã hủy/tổng 90.000₫; không thay schema, dữ liệu cấu hình hay nghiệp vụ giá/phí.

Bằng chứng local nằm trong `.runtime/ui-functional-result.json`, `.runtime/ui-edge-result.json`, `.runtime/YUMIO-E2E-20261117-180710-008b/result.json`. Ảnh trước/sau: `before-*.png`, `after-*.png`, `review-mobile.png`. Đây là file tạm ngoài Git.

Khi cuộn About 45 frame bằng requestAnimationFrame trên Chrome headless của máy hiện tại, TaskDuration tăng khoảng 38ms. Đây là đo tham khảo tải CPU trong bài thử, không phải cam kết FPS trên mọi thiết bị. Chưa kiểm thử thiết bị thật, Safari/iOS, người dùng đồng thời lớn, geolocation phần cứng hoặc độ phủ mọi địa chỉ. Mất API được mô phỏng bằng Chrome DevTools; tìm kiếm thành công đã dùng response thật từ Photon/cache server của response thật.

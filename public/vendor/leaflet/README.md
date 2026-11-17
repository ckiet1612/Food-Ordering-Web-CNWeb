# Leaflet 1.9.4

Thư viện bản đồ duy nhất được bổ sung theo yêu cầu dự án. Lưu JS/CSS tại local để không phụ thuộc CDN khi demo; nền bản đồ vẫn cần mạng.

- Bản phát hành chính thức: https://github.com/Leaflet/Leaflet/releases/tag/v1.9.4
- JS: https://unpkg.com/leaflet@1.9.4/dist/leaflet.js
- CSS: https://unpkg.com/leaflet@1.9.4/dist/leaflet.css
- API sử dụng: https://leafletjs.com/reference-1.9.4.html
- Giấy phép BSD-2-Clause: `LICENSE` trong thư mục này.

Ứng dụng dùng circleMarker, không dùng icon ảnh, bộ điều khiển layers hoặc source map. Nếu bổ sung các chức năng đó, cần lấy thêm tài nguyên tương ứng từ đúng phiên bản Leaflet.

Nền OpenStreetMap: https://operations.osmfoundation.org/policies/tiles/. Giữ attribution trên bản đồ, không tải hàng loạt hay prefetch. Có thể đổi nhà cung cấp qua `MAP_TILE_URL`; cần điều chỉnh attribution nếu nhà cung cấp mới yêu cầu.

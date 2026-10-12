-- Dữ liệu mẫu cho Yumio (sinh tự động từ danh sách ảnh sản phẩm)
USE yumio;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE order_items;
TRUNCATE orders;
TRUNCATE foods;
TRUNCATE categories;
TRUNCATE users;
SET FOREIGN_KEY_CHECKS = 1;

-- Tài khoản demo: admin@yumio.vn / admin123 — khach@yumio.vn / 123456
INSERT INTO users (id, name, email, password, phone, address, role) VALUES
(1, 'Quản trị viên', 'admin@yumio.vn', '$2y$12$0JSBcHch5UsBNeSnxM4.lOmahHX6RCBnHYYnYmOSruhDwxfrfLPWW', '0901234567', '1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội', 'admin'),
(2, 'Nguyễn Văn An', 'khach@yumio.vn', '$2y$12$qx//rdpdYq8OOBWY9Ov75uux52itfmx/9bkd55N20p.ROJOr36biy', '0912345678', '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', 'customer');

INSERT INTO categories (id, name, slug, description, image, color, sort_order) VALUES
(1, 'Gà rán', 'ga-ran', 'Gà tẩm bột chiên giòn rụm, mọng nước bên trong.', 'assets/images/foods/ga-ran-truyen-thong.webp', '#f6893b', 1),
(2, 'Burger', 'burger', 'Bánh mềm, nhân dày, nướng nóng ngay khi bạn gọi.', 'assets/images/foods/burger-bo-hai-tang.webp', '#f9c94d', 2),
(3, 'Combo', 'combo', 'Món chính, món kèm và đồ uống — tiết kiệm hơn khi gọi lẻ.', 'assets/images/foods/combo-burger-bo-hai-tang.webp', '#e9573d', 3),
(4, 'Món ăn kèm', 'mon-an-kem', 'Khoai chiên, gà viên và những món nhỏ cho trọn bữa.', 'assets/images/foods/khoai-tay-chien.webp', '#9cc94a', 4),
(5, 'Tráng miệng', 'trang-mieng', 'Kem, bánh và những món ngọt cho cuối bữa.', 'assets/images/foods/kem-tuyet-oreo.webp', '#f8a46b', 5),
(6, 'Đồ uống', 'do-uong', 'Nước ngọt, cà phê và trà trái cây mát lạnh.', 'assets/images/foods/coca-cola.webp', '#2fb3d3', 6);

INSERT INTO foods (id, category_id, name, slug, description, price, image, bg_color, is_featured, is_available) VALUES
(1, 1, 'Gà Rán Truyền Thống (2 miếng)', 'ga-ran-truyen-thong', 'Hai miếng gà tẩm bột chiên vàng giòn rụm, thịt bên trong mọng nước đậm vị thảo mộc.', 72000, 'assets/images/foods/ga-ran-truyen-thong.webp', '#f9c94d', 1, 1),
(2, 1, 'Cánh Gà Cay Giòn', 'canh-ga-cay', 'Cánh gà chiên giòn phủ lớp ớt cay nồng, càng ăn càng cuốn.', 59000, 'assets/images/foods/canh-ga-cay.webp', '#f6893b', 1, 1),
(3, 1, 'Xô Gà Rán 6 Miếng', 'xo-ga-ran-6-mieng', 'Xô sáu miếng gà rán giòn tan, đầy đặn cho cả nhóm cùng thưởng thức.', 189000, 'assets/images/foods/xo-ga-ran-6-mieng.webp', '#9cc94a', 0, 1),
(4, 1, 'Gà Phi-lê Tender Giòn', 'ga-phi-le-tender', 'Thăn gà phi-lê không xương tẩm bột giòn rụm, mềm ngọt trong từng thớ thịt.', 45000, 'assets/images/foods/ga-phi-le-tender.webp', '#e9573d', 0, 1),
(5, 1, 'Gà Que Giòn Kèm Xốt (3 miếng)', 'ga-que-gion', 'Ba thanh gà phi-lê chiên giòn vàng ruộm, chấm cùng xốt mật ong mù tạt và xốt kem thơm béo.', 55000, 'assets/images/foods/ga-que-gion.webp', '#f8a46b', 0, 1),
(6, 2, 'Burger Bò Hai Tầng', 'burger-bo-hai-tang', 'Hai lớp bò nướng, xốt đặc biệt, xà lách, phô mai và dưa chuột muối kẹp trong bánh mì mè ba tầng.', 79000, 'assets/images/foods/burger-bo-hai-tang.webp', '#2fb3d3', 1, 1),
(7, 2, 'Burger Bò Phô Mai', 'burger-bo-pho-mai', 'Miếng bò dày nướng tươi, hai lát phô mai tan chảy cùng hành tây, dưa chuột và tương cà.', 75000, 'assets/images/foods/burger-bo-pho-mai.webp', '#f9c94d', 1, 1),
(8, 2, 'Burger Bò Thịt Xông Khói', 'burger-bo-xong-khoi', 'Bò nướng mọng nước kết hợp thịt xông khói giòn thơm và phô mai béo ngậy.', 89000, 'assets/images/foods/burger-bo-xong-khoi.webp', '#f6893b', 0, 1),
(9, 2, 'Burger Bò Phô Mai Đôi', 'burger-bo-pho-mai-doi', 'Hai lớp bò nướng xen kẽ phô mai cheddar, dưa chuột muối và mù tạt đậm đà.', 65000, 'assets/images/foods/burger-bo-pho-mai-doi.webp', '#9cc94a', 0, 1),
(10, 2, 'Burger Gà Giòn', 'burger-ga-gion', 'Phi-lê gà chiên giòn cùng xà lách tươi và xốt mayonnaise mềm mịn.', 49000, 'assets/images/foods/burger-ga-gion.webp', '#e9573d', 0, 1),
(11, 2, 'Burger Gà Giòn Cay', 'burger-ga-gion-cay', 'Đùi gà chiên giòn rụm phủ xốt cay nồng, kẹp bánh mì khoai tây mềm xốp.', 59000, 'assets/images/foods/burger-ga-gion-cay.webp', '#f8a46b', 0, 1),
(12, 3, 'Combo Burger Bò Hai Tầng', 'combo-burger-bo-hai-tang', 'Burger bò hai tầng, khoai tây chiên vừa và nước ngọt mát lạnh cho bữa ăn no nê.', 119000, 'assets/images/foods/combo-burger-bo-hai-tang.webp', '#2fb3d3', 1, 1),
(13, 3, 'Combo Burger Bò Phô Mai', 'combo-burger-bo-pho-mai', 'Burger bò phô mai đậm vị đi kèm khoai tây chiên giòn và ly nước ngọt sảng khoái.', 115000, 'assets/images/foods/combo-burger-bo-pho-mai.webp', '#f9c94d', 1, 1),
(14, 3, 'Combo Gà Viên 10 Miếng', 'combo-ga-vien-10-mieng', 'Mười miếng gà viên vàng giòn kèm xốt chấm, khoai tây chiên và nước ngọt.', 109000, 'assets/images/foods/combo-ga-vien-10-mieng.webp', '#f6893b', 0, 1),
(15, 3, 'Combo Gà Que Giòn', 'combo-ga-que-gion', 'Ba thanh gà que giòn rụm cùng khoai tây chiên, xốt chấm và ly soda chanh mát lạnh.', 99000, 'assets/images/foods/combo-ga-que-gion.webp', '#9cc94a', 0, 1),
(16, 4, 'Khoai Tây Chiên', 'khoai-tay-chien', 'Khoai tây cắt sợi chiên vàng giòn bên ngoài, bùi mềm bên trong, rắc chút muối vừa miệng.', 35000, 'assets/images/foods/khoai-tay-chien.webp', '#e9573d', 1, 1),
(17, 4, 'Gà Viên Chiên (4 miếng)', 'ga-vien-chien-4-mieng', 'Gà viên tẩm bột chiên giòn, chấm xốt chua ngọt cực bắt vị.', 39000, 'assets/images/foods/ga-vien-chien-4-mieng.webp', '#f8a46b', 1, 1),
(18, 4, 'Gà Viên Cay (10 miếng)', 'ga-vien-cay-10-mieng', 'Mười viên gà lớp vỏ ớt cay giòn tan, ăn kèm xốt cay ngọt đậm đà.', 69000, 'assets/images/foods/ga-vien-cay-10-mieng.webp', '#2fb3d3', 0, 1),
(19, 4, 'Bánh Khoai Tây Chiên', 'banh-khoai-tay-chien', 'Bánh khoai tây bào sợi chiên vàng, giòn rụm bên ngoài và mềm bùi bên trong.', 25000, 'assets/images/foods/banh-khoai-tay-chien.webp', '#f9c94d', 0, 1),
(20, 4, 'Bánh Cuốn Gà Giòn', 'banh-cuon-ga-gion', 'Bánh tortilla mềm cuộn gà giòn, xà lách, phô mai bào và xốt kem ranch thơm béo.', 45000, 'assets/images/foods/banh-cuon-ga-gion.webp', '#f6893b', 0, 1),
(21, 5, 'Kem Tuyết Bánh Quy Oreo', 'kem-tuyet-oreo', 'Kem vani mịn đánh tơi cùng vụn bánh quy sô-cô-la giòn tan.', 39000, 'assets/images/foods/kem-tuyet-oreo.webp', '#9cc94a', 1, 1),
(22, 5, 'Kem Sundae Sô-cô-la', 'sundae-socola', 'Kem vani mát lạnh phủ xốt sô-cô-la nóng sánh mịn, ngọt ngào khó cưỡng.', 29000, 'assets/images/foods/sundae-socola.webp', '#e9573d', 1, 1),
(23, 5, 'Kem Sundae Caramel', 'sundae-caramel', 'Kem vani béo mịn rưới xốt caramel bơ thơm lừng.', 29000, 'assets/images/foods/sundae-caramel.webp', '#f8a46b', 0, 1),
(24, 5, 'Bánh Táo Nướng', 'banh-tao-nuong', 'Vỏ bánh ngàn lớp giòn xốp, nhân táo quế nóng hổi ngọt dịu.', 25000, 'assets/images/foods/banh-tao-nuong.webp', '#2fb3d3', 0, 1),
(25, 5, 'Kem Ốc Quế Vani', 'kem-oc-que-vani', 'Kem vani tươi mát xoắn cao trên ốc quế giòn thơm.', 15000, 'assets/images/foods/kem-oc-que-vani.webp', '#f9c94d', 0, 1),
(26, 6, 'Coca-Cola', 'coca-cola', 'Ly Coca-Cola ướp đá mát lạnh, sảng khoái tức thì bên bữa ăn.', 25000, 'assets/images/foods/coca-cola.webp', '#f6893b', 1, 1),
(27, 6, 'Sprite Chanh', 'sprite', 'Soda vị chanh tươi mát với đá viên, giải nhiệt cực đã.', 25000, 'assets/images/foods/sprite.webp', '#9cc94a', 1, 1),
(28, 6, 'Sữa Lắc Sô-cô-la', 'sua-lac-socola', 'Sữa lắc sô-cô-la đậm đặc phủ kem tươi, béo mịn và mát lạnh.', 45000, 'assets/images/foods/sua-lac-socola.webp', '#e9573d', 0, 1),
(29, 6, 'Sữa Lắc Dâu', 'sua-lac-dau', 'Sữa lắc hương dâu ngọt ngào, phủ lớp kem tươi bồng bềnh.', 45000, 'assets/images/foods/sua-lac-dau.webp', '#f8a46b', 0, 1),
(30, 6, 'Latte Caramel Đá', 'latte-caramel-da', 'Espresso đậm đà hòa quyện sữa tươi và xốt caramel, thêm đá mát lạnh.', 49000, 'assets/images/foods/latte-caramel-da.webp', '#2fb3d3', 0, 1);

INSERT INTO orders (id, user_id, receiver_name, phone, address, note, subtotal, shipping_fee, total_amount, payment_method, status, created_at) VALUES
(1, 2, 'Nguyễn Văn An', '0912345678', '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', NULL, 157000, 15000, 172000, 'bank', 'completed', NOW() - INTERVAL 7 DAY - INTERVAL 2 HOUR),
(2, 2, 'Nguyễn Văn An', '0912345678', '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', NULL, 371000, 0, 371000, 'cod', 'completed', NOW() - INTERVAL 6 DAY - INTERVAL 7 HOUR),
(3, 2, 'Nguyễn Văn An', '0912345678', '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', NULL, 285000, 0, 285000, 'cod', 'completed', NOW() - INTERVAL 5 DAY - INTERVAL 2 HOUR),
(4, 2, 'Nguyễn Văn An', '0912345678', '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', NULL, 182000, 15000, 197000, 'bank', 'delivering', NOW() - INTERVAL 4 DAY - INTERVAL 7 HOUR),
(5, 2, 'Nguyễn Văn An', '0912345678', '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', NULL, 285000, 0, 285000, 'cod', 'confirmed', NOW() - INTERVAL 3 DAY - INTERVAL 8 HOUR),
(6, 2, 'Nguyễn Văn An', '0912345678', '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', NULL, 241000, 0, 241000, 'cod', 'pending', NOW() - INTERVAL 2 DAY - INTERVAL 3 HOUR),
(7, 2, 'Nguyễn Văn An', '0912345678', '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', NULL, 432000, 0, 432000, 'bank', 'cancelled', NOW() - INTERVAL 1 DAY - INTERVAL 9 HOUR),
(8, 2, 'Nguyễn Văn An', '0912345678', '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', NULL, 835000, 0, 835000, 'cod', 'completed', NOW() - INTERVAL 0 DAY - INTERVAL 4 HOUR);

INSERT INTO order_items (order_id, food_id, quantity, price) VALUES
(1, 16, 2, 35000),
(1, 23, 3, 29000),
(2, 18, 2, 69000),
(2, 28, 3, 45000),
(2, 30, 2, 49000),
(3, 9, 3, 65000),
(3, 28, 2, 45000),
(4, 4, 1, 45000),
(4, 19, 2, 25000),
(4, 23, 3, 29000),
(5, 9, 3, 65000),
(5, 29, 2, 45000),
(6, 9, 1, 65000),
(6, 11, 2, 59000),
(6, 22, 2, 29000),
(7, 7, 3, 75000),
(7, 18, 3, 69000),
(8, 3, 3, 189000),
(8, 12, 2, 119000),
(8, 25, 2, 15000);

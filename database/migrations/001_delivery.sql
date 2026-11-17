-- Chạy một lần trên schema Yumio gốc; không chứa lệnh xoá dữ liệu.
-- Khuyến nghị dùng php scripts/migrate.php: tự kiểm tra cột để chạy lại an toàn.
ALTER TABLE users MODIFY role ENUM('customer','admin','shipper') NOT NULL DEFAULT 'customer';
ALTER TABLE orders MODIFY status ENUM('pending','confirmed','preparing','delivering','completed','cancelled') NOT NULL DEFAULT 'pending';
ALTER TABLE orders ADD COLUMN shipper_id INT UNSIGNED NULL;
ALTER TABLE orders ADD CONSTRAINT fk_orders_shipper FOREIGN KEY (shipper_id) REFERENCES users(id) ON DELETE RESTRICT;
ALTER TABLE orders ADD COLUMN checkout_token CHAR(64) NULL UNIQUE;
ALTER TABLE orders ADD COLUMN delivery_lat DECIMAL(10,7) NULL;
ALTER TABLE orders ADD COLUMN delivery_lng DECIMAL(10,7) NULL;
ALTER TABLE orders ADD COLUMN distance_km DECIMAL(10,2) NULL;
ALTER TABLE order_items ADD COLUMN food_name VARCHAR(150) NULL;
CREATE TABLE IF NOT EXISTS order_events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    status VARCHAR(20) NOT NULL,
    actor_id INT UNSIGNED NULL,
    note VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_events_order (order_id, id)
) ENGINE=InnoDB;

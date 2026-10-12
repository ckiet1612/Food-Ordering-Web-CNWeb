-- Tạo CSDL cho website đặt đồ ăn nhanh Yumio
CREATE DATABASE IF NOT EXISTS yumio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE yumio;

CREATE TABLE users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    phone      VARCHAR(20)  NULL,
    address    VARCHAR(255) NULL,
    role       ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(120) NOT NULL UNIQUE,
    description TEXT NULL,
    image       VARCHAR(255) NULL COMMENT 'Đường dẫn tính từ public/',
    color       CHAR(7) NOT NULL DEFAULT '#f9c94d' COMMENT 'Màu nền thẻ danh mục',
    sort_order  INT NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE foods (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id  INT UNSIGNED NOT NULL,
    name         VARCHAR(150) NOT NULL,
    slug         VARCHAR(170) NOT NULL UNIQUE,
    description  TEXT NULL,
    price        DECIMAL(12, 0) NOT NULL,
    image        VARCHAR(255) NULL COMMENT 'Đường dẫn tính từ public/',
    bg_color     CHAR(7) NOT NULL DEFAULT '#f9c94d' COMMENT 'Màu nền thẻ món ăn',
    is_featured  TINYINT(1) NOT NULL DEFAULT 0,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE orders (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED NOT NULL,
    receiver_name  VARCHAR(100) NOT NULL,
    phone          VARCHAR(20)  NOT NULL,
    address        VARCHAR(255) NOT NULL,
    note           TEXT NULL,
    subtotal       DECIMAL(12, 0) NOT NULL,
    shipping_fee   DECIMAL(12, 0) NOT NULL DEFAULT 0,
    total_amount   DECIMAL(12, 0) NOT NULL,
    payment_method ENUM('cod', 'bank') NOT NULL DEFAULT 'cod',
    status         ENUM('pending', 'confirmed', 'delivering', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    food_id  INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    price    DECIMAL(12, 0) NOT NULL COMMENT 'Giá tại thời điểm đặt hàng',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id)  REFERENCES foods(id)  ON DELETE RESTRICT
) ENGINE=InnoDB;

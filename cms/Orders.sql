-- Requires: Customers table must exist first.
-- Import order: Users → Customers → Products → Orders → OrderItems

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS OrderItems;
DROP TABLE IF EXISTS Orders;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE Orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(50) NULL,
    source ENUM('website', 'facebook', 'instagram', 'tiktok', 'whatsapp', 'other') NOT NULL DEFAULT 'website',
    external_order_id VARCHAR(100) NULL,
    external_account_id VARCHAR(100) NULL,
    source_metadata JSON NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(30) NOT NULL DEFAULT 'cod',
    subtotal INT NOT NULL,
    shipping INT NOT NULL DEFAULT 0,
    total INT NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_orders_customer_id (customer_id),
    INDEX idx_orders_source (source),
    INDEX idx_orders_external_order_id (external_order_id),
    UNIQUE KEY uniq_orders_source_external (source, external_order_id),
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES Customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

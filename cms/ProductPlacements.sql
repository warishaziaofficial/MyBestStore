CREATE TABLE ProductPlacements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    placement ENUM('featured', 'new_arrival') NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_product_placement (product_id, placement),
    INDEX idx_product_placements_type (placement, sort_order),
    CONSTRAINT fk_product_placements_product FOREIGN KEY (product_id) REFERENCES Products(id) ON DELETE CASCADE
);

-- Run after Products.sql
CREATE TABLE ProductRelations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    related_product_id INT NOT NULL,
    relation_type ENUM('upsell', 'cross_sell', 'related', 'frequently_bought_together') NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY product_relation_unique (product_id, related_product_id, relation_type),
    INDEX idx_product_relations_product (product_id),
    INDEX idx_product_relations_type (relation_type)
);

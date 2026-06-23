-- Safe upgrade: allow social order line items without a catalog product_id.
-- Run once in phpMyAdmin (ignores if already nullable).

DELIMITER $$

DROP PROCEDURE IF EXISTS upgrade_orderitems_nullable_product$$

CREATE PROCEDURE upgrade_orderitems_nullable_product()
BEGIN
    IF EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'OrderItems'
          AND COLUMN_NAME = 'product_id'
          AND IS_NULLABLE = 'NO'
    ) THEN
        ALTER TABLE OrderItems MODIFY product_id INT NULL;
    END IF;
END$$

DELIMITER ;

CALL upgrade_orderitems_nullable_product();

DROP PROCEDURE IF EXISTS upgrade_orderitems_nullable_product;
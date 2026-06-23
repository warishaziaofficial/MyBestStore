-- Run after Products.sql — enables High Margin Products report
SET @exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'Products'
      AND COLUMN_NAME = 'cost_price'
);

SET @sql = IF(
    @exists = 0,
    'ALTER TABLE Products ADD COLUMN cost_price INT NULL AFTER price',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Run after Products.sql
SET @exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'Products'
      AND COLUMN_NAME = 'description'
);

SET @sql = IF(
    @exists = 0,
    'ALTER TABLE Products ADD COLUMN description LONGTEXT NULL AFTER sub_category',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

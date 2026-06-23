-- Add logo + display order for Shop By Brand section.

SET @db = DATABASE();

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'Brands' AND COLUMN_NAME = 'logo') = 0,
    'ALTER TABLE Brands ADD COLUMN logo VARCHAR(500) NULL AFTER name',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'Brands' AND COLUMN_NAME = 'sort_order') = 0,
    'ALTER TABLE Brands ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER logo',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

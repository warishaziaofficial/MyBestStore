-- Optional: dedicated dispatch / tracking columns on Orders.
-- Safe to run multiple times (checks information_schema).

SET @db = DATABASE();

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'Orders' AND COLUMN_NAME = 'courier_name') = 0,
    'ALTER TABLE Orders ADD COLUMN courier_name VARCHAR(80) NULL AFTER notes',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'Orders' AND COLUMN_NAME = 'tracking_number') = 0,
    'ALTER TABLE Orders ADD COLUMN tracking_number VARCHAR(120) NULL AFTER courier_name',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'Orders' AND COLUMN_NAME = 'dispatched_at') = 0,
    'ALTER TABLE Orders ADD COLUMN dispatched_at TIMESTAMP NULL AFTER tracking_number',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'Orders' AND COLUMN_NAME = 'dispatch_meta') = 0,
    'ALTER TABLE Orders ADD COLUMN dispatch_meta JSON NULL AFTER dispatched_at',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

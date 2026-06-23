-- Safe upgrade for existing Orders table (run once in phpMyAdmin).
-- Works whether you have an old Orders table (no source column) or a partially upgraded one.

DELIMITER $$

DROP PROCEDURE IF EXISTS upgrade_orders_social_fields$$

CREATE PROCEDURE upgrade_orders_social_fields()
BEGIN
    -- 1) payment_status (older installs)
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Orders'
          AND COLUMN_NAME = 'payment_status'
    ) THEN
        ALTER TABLE Orders
            ADD COLUMN payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending' AFTER status;
    END IF;

    -- 2) source column (your error: column did not exist yet)
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Orders'
          AND COLUMN_NAME = 'source'
    ) THEN
        ALTER TABLE Orders
            ADD COLUMN source ENUM('website', 'facebook', 'instagram', 'tiktok', 'whatsapp', 'other') NOT NULL DEFAULT 'website' AFTER customer_phone;
    ELSE
        ALTER TABLE Orders
            MODIFY COLUMN source ENUM('website', 'facebook', 'instagram', 'tiktok', 'whatsapp', 'other') NOT NULL DEFAULT 'website';
    END IF;

    -- 3) confirmed status in order workflow
    ALTER TABLE Orders
        MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending';

    -- 4) external social order references
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Orders'
          AND COLUMN_NAME = 'external_order_id'
    ) THEN
        ALTER TABLE Orders
            ADD COLUMN external_order_id VARCHAR(100) NULL AFTER source;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Orders'
          AND COLUMN_NAME = 'external_account_id'
    ) THEN
        ALTER TABLE Orders
            ADD COLUMN external_account_id VARCHAR(100) NULL AFTER external_order_id;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Orders'
          AND COLUMN_NAME = 'source_metadata'
    ) THEN
        ALTER TABLE Orders
            ADD COLUMN source_metadata JSON NULL AFTER external_account_id;
    END IF;

    -- 5) indexes (ignore if already present)
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Orders'
          AND INDEX_NAME = 'idx_orders_source'
    ) THEN
        ALTER TABLE Orders ADD INDEX idx_orders_source (source);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Orders'
          AND INDEX_NAME = 'idx_orders_external_order_id'
    ) THEN
        ALTER TABLE Orders ADD INDEX idx_orders_external_order_id (external_order_id);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Orders'
          AND INDEX_NAME = 'uniq_orders_source_external'
    ) THEN
        ALTER TABLE Orders ADD UNIQUE KEY uniq_orders_source_external (source, external_order_id);
    END IF;
END$$

DELIMITER ;

CALL upgrade_orders_social_fields();

DROP PROCEDURE IF EXISTS upgrade_orders_social_fields;

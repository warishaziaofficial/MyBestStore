-- Log every social order webhook / manual sync attempt.
-- Import after: SocialAccounts, Orders

CREATE TABLE SocialSyncLogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    social_account_id INT NULL,
    platform VARCHAR(50) NOT NULL,
    trigger_type ENUM('webhook', 'manual', 'test') NOT NULL DEFAULT 'webhook',
    status ENUM('success', 'failed', 'duplicate', 'partial') NOT NULL,
    orders_imported INT NOT NULL DEFAULT 0,
    external_order_id VARCHAR(100) NULL,
    order_id INT NULL,
    payload JSON NULL,
    message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_social_sync_logs_platform (platform),
    INDEX idx_social_sync_logs_account (social_account_id),
    INDEX idx_social_sync_logs_order (order_id),
    CONSTRAINT fk_social_sync_logs_account FOREIGN KEY (social_account_id) REFERENCES SocialAccounts(id) ON DELETE SET NULL,
    CONSTRAINT fk_social_sync_logs_order FOREIGN KEY (order_id) REFERENCES Orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

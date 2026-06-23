-- Connected social media accounts (structure for future API sync).
-- Import after: Orders

CREATE TABLE SocialAccounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform ENUM('instagram', 'facebook', 'tiktok', 'whatsapp', 'other') NOT NULL,
    account_name VARCHAR(150) NOT NULL,
    account_id VARCHAR(100) NOT NULL,
    status ENUM('connected', 'disconnected', 'error') NOT NULL DEFAULT 'connected',
    orders_synced_count INT NOT NULL DEFAULT 0,
    last_sync_at TIMESTAMP NULL,
    webhook_secret VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_social_platform_account (platform, account_id),
    INDEX idx_social_accounts_platform (platform),
    INDEX idx_social_accounts_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


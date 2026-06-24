CREATE TABLE ChatSettings (
    id INT NOT NULL PRIMARY KEY DEFAULT 1,
    is_enabled TINYINT(1) NOT NULL DEFAULT 0,
    base_url VARCHAR(500) NOT NULL DEFAULT 'http://localhost:3000',
    website_token VARCHAR(255) NOT NULL DEFAULT '',
    chatwoot_admin_url VARCHAR(500) NULL,
    crm_url VARCHAR(500) NULL,
    dify_url VARCHAR(500) NULL,
    chatdify_url VARCHAR(500) NULL,
    launcher_title VARCHAR(120) NOT NULL DEFAULT 'Chat with us',
    welcome_title VARCHAR(255) NOT NULL DEFAULT 'Store Support',
    welcome_description TEXT NULL,
    widget_position VARCHAR(10) NOT NULL DEFAULT 'right',
    fallback_enabled TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

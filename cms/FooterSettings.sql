CREATE TABLE FooterSettings (
    id INT PRIMARY KEY DEFAULT 1,
    tagline TEXT NOT NULL,
    website_url VARCHAR(500) NOT NULL DEFAULT 'https://mybeststore.pk/',
    instagram_url VARCHAR(500) NULL,
    facebook_url VARCHAR(500) NULL,
    copyright_text VARCHAR(255) NOT NULL DEFAULT 'MyBestStore.pk — All rights reserved.',
    newsletter_heading VARCHAR(255) NOT NULL DEFAULT 'Newsletter',
    newsletter_text TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


ALTER TABLE FeaturedCollections
    ADD COLUMN price INT NULL AFTER href,
    ADD COLUMN product_slug VARCHAR(255) NULL AFTER price;

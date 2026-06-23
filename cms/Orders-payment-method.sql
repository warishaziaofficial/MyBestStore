ALTER TABLE Orders
    ADD COLUMN payment_method VARCHAR(30) NOT NULL DEFAULT 'cod' AFTER payment_status;
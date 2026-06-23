-- Run in phpMyAdmin if Orders already exists without payment_status column.
ALTER TABLE Orders ADD COLUMN payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending' AFTER status;

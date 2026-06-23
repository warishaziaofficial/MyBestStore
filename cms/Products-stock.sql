-- Run in phpMyAdmin if Products already exists without stock column.
ALTER TABLE Products ADD COLUMN stock INT NOT NULL DEFAULT 0 AFTER brand;

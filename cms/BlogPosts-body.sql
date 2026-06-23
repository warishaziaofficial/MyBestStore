-- Run in phpMyAdmin if BlogPosts already exists without body column.
ALTER TABLE BlogPosts ADD COLUMN body LONGTEXT NULL AFTER excerpt;

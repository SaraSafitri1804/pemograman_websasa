-- Simple migration: Add foto column to reward table
USE loyaltypro;

ALTER TABLE reward ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER stok;

DESCRIBE reward;

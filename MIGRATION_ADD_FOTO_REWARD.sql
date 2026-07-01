-- =============================================
-- MIGRATION: Add foto column to reward table
-- Date: 2024
-- Description: Menambahkan kolom foto untuk reward
-- =============================================

USE loyaltypro;

-- Add foto column to reward table
-- Check if column exists first before adding
SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'loyaltypro'
    AND TABLE_NAME = 'reward'
    AND COLUMN_NAME = 'foto'
);

SET @query = IF(@column_exists = 0,
    'ALTER TABLE reward ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER stok',
    'SELECT "Column foto already exists" AS message'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Show table structure
DESCRIBE reward;

-- Update instruction:
-- 1. Backup database terlebih dahulu
-- 2. Jalankan script ini: mysql -u root -p loyaltypro < MIGRATION_ADD_FOTO_REWARD.sql
-- 3. Pastikan folder uploads/reward/ sudah dibuat dengan permission 0777

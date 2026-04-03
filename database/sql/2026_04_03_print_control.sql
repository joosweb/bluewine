-- Bluewine POS
-- Print control schema changes
-- Date: 2026-04-03
-- Target: MySQL 5.7+/8.x

START TRANSACTION;

-- 0) Helpers (idempotent DDL for MySQL 5.7+)
DELIMITER $$

DROP PROCEDURE IF EXISTS add_column_if_missing $$
CREATE PROCEDURE add_column_if_missing(
  IN p_table VARCHAR(64),
  IN p_column VARCHAR(64),
  IN p_definition TEXT
)
BEGIN
  DECLARE v_exists INT DEFAULT 0;

  SELECT COUNT(*) INTO v_exists
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = p_table
    AND COLUMN_NAME = p_column;

  IF v_exists = 0 THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN ', p_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END $$

DROP PROCEDURE IF EXISTS add_index_if_missing $$
CREATE PROCEDURE add_index_if_missing(
  IN p_table VARCHAR(64),
  IN p_index VARCHAR(64),
  IN p_definition TEXT
)
BEGIN
  DECLARE v_exists INT DEFAULT 0;

  SELECT COUNT(*) INTO v_exists
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = p_table
    AND INDEX_NAME = p_index;

  IF v_exists = 0 THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD INDEX `', p_index, '` ', p_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END $$

DELIMITER ;

-- 1) Add print-control columns to sales (safe if rerun)
CALL add_column_if_missing('sales', 'printed_original_at', '`printed_original_at` DATETIME NULL AFTER `created_at`');
CALL add_column_if_missing('sales', 'print_original_count', '`print_original_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `printed_original_at`');
CALL add_column_if_missing('sales', 'reprint_count', '`reprint_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `print_original_count`');
CALL add_column_if_missing('sales', 'print_locked', '`print_locked` TINYINT(1) NOT NULL DEFAULT 0 AFTER `reprint_count`');
CALL add_column_if_missing('sales', 'print_last_user_id', '`print_last_user_id` BIGINT UNSIGNED NULL AFTER `print_locked`');
CALL add_column_if_missing('sales', 'print_last_at', '`print_last_at` DATETIME NULL AFTER `print_last_user_id`');

CALL add_index_if_missing('sales', 'sales_printed_original_at_index', '(`printed_original_at`)');
CALL add_index_if_missing('sales', 'sales_print_last_user_id_index', '(`print_last_user_id`)');
CALL add_index_if_missing('sales', 'sales_reprint_count_index', '(`reprint_count`)');

-- 2) Create audit log table for voucher prints
CREATE TABLE IF NOT EXISTS `voucher_print_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` BIGINT UNSIGNED NOT NULL,
  `shop_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `approved_by_user_id` BIGINT UNSIGNED NULL,
  `action` ENUM('ORIGINAL','REPRINT','BLOCKED') NOT NULL,
  `reason` VARCHAR(255) NULL,
  `copy_number` INT UNSIGNED NOT NULL DEFAULT 0,
  `source` ENUM('POS_AUTO','POS_MANUAL','ADMIN_PANEL','API') NOT NULL DEFAULT 'API',
  `ip` VARCHAR(64) NULL,
  `user_agent` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `voucher_print_logs_sale_id_index` (`sale_id`),
  KEY `voucher_print_logs_shop_id_index` (`shop_id`),
  KEY `voucher_print_logs_user_id_index` (`user_id`),
  KEY `voucher_print_logs_approved_by_user_id_index` (`approved_by_user_id`),
  KEY `voucher_print_logs_action_index` (`action`),
  KEY `voucher_print_logs_sale_action_index` (`sale_id`, `action`),
  KEY `voucher_print_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Ensure log indexes also exist when table existed from a previous partial deploy
CALL add_index_if_missing('voucher_print_logs', 'voucher_print_logs_sale_id_index', '(`sale_id`)');
CALL add_index_if_missing('voucher_print_logs', 'voucher_print_logs_shop_id_index', '(`shop_id`)');
CALL add_index_if_missing('voucher_print_logs', 'voucher_print_logs_user_id_index', '(`user_id`)');
CALL add_index_if_missing('voucher_print_logs', 'voucher_print_logs_approved_by_user_id_index', '(`approved_by_user_id`)');
CALL add_index_if_missing('voucher_print_logs', 'voucher_print_logs_action_index', '(`action`)');
CALL add_index_if_missing('voucher_print_logs', 'voucher_print_logs_sale_action_index', '(`sale_id`, `action`)');
CALL add_index_if_missing('voucher_print_logs', 'voucher_print_logs_created_at_index', '(`created_at`)');

-- 4) Cleanup helpers
DROP PROCEDURE IF EXISTS add_column_if_missing;
DROP PROCEDURE IF EXISTS add_index_if_missing;

COMMIT;

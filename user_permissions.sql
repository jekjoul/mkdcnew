-- ==========================================
-- SQL MIGRATION FOR INDIVIDUAL USER PERMISSIONS
-- Run this on the production database
-- ==========================================

CREATE TABLE IF NOT EXISTS `user_permissions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `permission` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_permission` (`user_id`, `permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

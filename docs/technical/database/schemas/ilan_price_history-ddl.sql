CREATE TABLE `ilan_price_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ilan_id` bigint unsigned NOT NULL,
  `old_price` decimal(15,2) DEFAULT NULL COMMENT 'Eski fiyat',
  `new_price` decimal(15,2) NOT NULL COMMENT 'Yeni fiyat',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TRY' COMMENT 'Para birimi',
  `change_reason` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Değişiklik sebebi',
  `changed_by` bigint unsigned DEFAULT NULL,
  `additional_data` json DEFAULT NULL COMMENT 'Ek veriler',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma tarihi',
  PRIMARY KEY (`id`),
  KEY `idx_ilan_date` (`ilan_id`,`created_at`),
  KEY `idx_changed_by` (`changed_by`),
  KEY `idx_currency` (`currency`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `ilan_price_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ilan_price_history_ilan_id_foreign` FOREIGN KEY (`ilan_id`) REFERENCES `ilanlar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
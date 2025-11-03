CREATE TABLE `user_expertise_area` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `expertise_area_id` bigint unsigned NOT NULL,
  `level` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_expertise_area_user_id_expertise_area_id_unique` (`user_id`,`expertise_area_id`),
  KEY `user_expertise_area_expertise_area_id_foreign` (`expertise_area_id`),
  CONSTRAINT `user_expertise_area_expertise_area_id_foreign` FOREIGN KEY (`expertise_area_id`) REFERENCES `expertise_areas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_expertise_area_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
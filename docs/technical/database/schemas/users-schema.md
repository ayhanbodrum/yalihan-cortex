# Tablo Şeması: users

Veritabanı: `yalihanemlak_db`

## Kolonlar

| Kolon | Tip | Null | Varsayılan | Key | Extra | Açıklama |
|---|---|---|---|---|---|---|
| id | bigint unsigned | NO | NULL | PRI | auto_increment |  |
| name | varchar(255) | NO | NULL |  |  |  |
| email | varchar(255) | NO | NULL | UNI |  |  |
| email_verified_at | timestamp | YES | NULL |  |  |  |
| password | varchar(255) | NO | NULL |  |  |  |
| remember_token | varchar(100) | YES | NULL |  |  |  |
| role_id | bigint unsigned | YES | NULL | MUL |  |  |
| is_active | tinyint(1) | NO | 1 |  |  |  |
| last_login_at | timestamp | YES | NULL |  |  |  |
| last_activity_at | timestamp | YES | NULL |  |  |  |
| profile_photo_path | varchar(2048) | YES | NULL |  |  |  |
| phone_number | varchar(20) | YES | NULL |  |  |  |
| title | varchar(255) | YES | NULL |  |  |  |
| bio | text | YES | NULL |  |  |  |
| office_address | text | YES | NULL |  |  |  |
| whatsapp_number | varchar(20) | YES | NULL |  |  |  |
| instagram_profile | varchar(255) | YES | NULL |  |  |  |
| linkedin_profile | varchar(255) | YES | NULL |  |  |  |
| website | varchar(255) | YES | NULL |  |  |  |
| is_verified | tinyint(1) | NO | 0 |  |  |  |
| expertise_summary | text | YES | NULL |  |  |  |
| certificates_info | text | YES | NULL |  |  |  |
| deleted_at | timestamp | YES | NULL |  |  |  |
| created_at | timestamp | YES | NULL |  |  |  |
| updated_at | timestamp | YES | NULL |  |  |  |
| position | varchar(255) | YES | NULL |  |  | Kullanıcının pozisyonu (örn: Müdür, Danışman) |
| department | varchar(255) | YES | NULL |  |  | Kullanıcının departmanı |
| employee_id | varchar(255) | YES | NULL | UNI |  | Personel kimlik numarası |
| hire_date | date | YES | NULL |  |  | İşe başlama tarihi |
| termination_date | date | YES | NULL |  |  | İşten ayrılma tarihi |
| emergency_contact | varchar(255) | YES | NULL |  |  | Acil status iletişim bilgisi |
| notes | text | YES | NULL |  |  | Kullanıcı hakkında notlar |
| facebook_profile | varchar(255) | YES | NULL |  |  | Facebook profil linki |
| twitter_profile | varchar(255) | YES | NULL |  |  | Twitter profil linki |
| youtube_channel | varchar(255) | YES | NULL |  |  | YouTube kanal linki |
| tiktok_profile | varchar(255) | YES | NULL |  |  | TikTok profil linki |
| settings | json | YES | NULL |  |  | Kullanıcı ayarları (JSON) |

## Indexler

| Index | Kolonlar | Unique |
|---|---|---|
| PRIMARY | id | Yes |
| users_email_unique | email | Yes |
| users_employee_id_unique | employee_id | Yes |
| users_role_id_foreign | role_id | No |

## DDL

```sql
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `profile_photo_path` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `office_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `whatsapp_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `expertise_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `certificates_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kullanıcının pozisyonu (örn: Müdür, Danışman)',
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kullanıcının departmanı',
  `employee_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Personel kimlik numarası',
  `hire_date` date DEFAULT NULL COMMENT 'İşe başlama tarihi',
  `termination_date` date DEFAULT NULL COMMENT 'İşten ayrılma tarihi',
  `emergency_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Acil status iletişim bilgisi',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Kullanıcı hakkında notlar',
  `facebook_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Facebook profil linki',
  `twitter_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Twitter profil linki',
  `youtube_channel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'YouTube kanal linki',
  `tiktok_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'TikTok profil linki',
  `settings` json DEFAULT NULL COMMENT 'Kullanıcı ayarları (JSON)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

# Tablo Şeması: kisiler

Veritabanı: `yalihanemlak_db`

## Kolonlar

| Kolon       | Tip                                          | Null | Varsayılan | Key | Extra          | Açıklama |
| ----------- | -------------------------------------------- | ---- | ---------- | --- | -------------- | -------- |
| id          | bigint unsigned                              | NO   | NULL       | PRI | auto_increment |          |
| danisman_id | bigint unsigned                              | YES  | NULL       | MUL |                |          |
| ad          | varchar(100)                                 | NO   | NULL       |     |                |          |
| soyad       | varchar(100)                                 | NO   | NULL       |     |                |          |
| email       | varchar(255)                                 | YES  | NULL       |     |                |          |
| telefon     | varchar(20)                                  | YES  | NULL       |     |                |          |
| adres       | text                                         | YES  | NULL       |     |                |          |
| notlar      | text                                         | YES  | NULL       |     |                |          |
| kaynak      | varchar(100)                                 | YES  | NULL       |     |                |          |
| status      | enum('Aktif','Pasif','Potansiyel','Müşteri') | NO   | Potansiyel |     |                |          |
| created_at  | timestamp                                    | YES  | NULL       |     |                |          |
| updated_at  | timestamp                                    | YES  | NULL       |     |                |          |
| deleted_at  | timestamp                                    | YES  | NULL       |     |                |          |

## Indexler

| Index                       | Kolonlar    | Unique |
| --------------------------- | ----------- | ------ |
| kisiler_danisman_id_foreign | danisman_id | No     |
| PRIMARY                     | id          | Yes    |

## DDL

```sql
CREATE TABLE `kisiler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `danisman_id` bigint unsigned DEFAULT NULL,
  `ad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `soyad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adres` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notlar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kaynak` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Pasif','Potansiyel','Müşteri') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Potansiyel',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kisiler_danisman_id_foreign` (`danisman_id`),
  CONSTRAINT `kisiler_danisman_id_foreign` FOREIGN KEY (`danisman_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

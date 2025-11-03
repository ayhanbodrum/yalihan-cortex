# Tablo Şeması: ilanlar

Veritabanı: `yalihanemlak_db`

## Kolonlar

| Kolon | Tip | Null | Varsayılan | Key | Extra | Açıklama |
|---|---|---|---|---|---|---|
| id | bigint unsigned | NO | NULL | PRI | auto_increment |  |
| baslik | varchar(255) | NO | NULL |  |  |  |
| aciklama | text | YES | NULL |  |  |  |
| fiyat | decimal(15,2) | YES | NULL | MUL |  |  |
| para_birimi | varchar(3) | NO | TRY |  |  |  |
| kategori | varchar(255) | YES | NULL | MUL |  |  |
| alt_kategori | varchar(255) | YES | NULL |  |  |  |
| ilan_tipi | enum('satilik','kiralik') | NO | satilik |  |  |  |
| status | enum('active','pasif','satildi','kiralandi') | NO | active |  |  |  |
| il_id | bigint unsigned | YES | NULL | MUL |  |  |
| ilce_id | bigint unsigned | YES | NULL | MUL |  |  |
| mahalle_id | bigint unsigned | YES | NULL | MUL |  |  |
| adres | text | YES | NULL |  |  |  |
| latitude | decimal(10,8) | YES | NULL |  |  |  |
| longitude | decimal(11,8) | YES | NULL |  |  |  |
| danisman_id | bigint unsigned | YES | NULL | MUL |  |  |
| ilan_sahibi_id | bigint unsigned | YES | NULL | MUL |  |  |
| created_at | timestamp | YES | NULL |  |  |  |
| updated_at | timestamp | YES | NULL |  |  |  |
| deleted_at | timestamp | YES | NULL |  |  |  |

## Indexler

| Index | Kolonlar | Unique |
|---|---|---|
| ilanlar_fiyat_para_birimi_index | fiyat,para_birimi | No |
| ilanlar_ilan_sahibi_id_index | ilan_sahibi_id | No |
| ilanlar_ilce_id_foreign | ilce_id | No |
| ilanlar_kategori_durum_index | kategori,status | No |
| ilanlar_mahalle_id_foreign | mahalle_id | No |
| ilanlar_sehir_id_ilce_id_index | il_id,ilce_id | No |
| ilanlar_user_id_index | danisman_id | No |
| PRIMARY | id | Yes |

## DDL

```sql
CREATE TABLE `ilanlar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aciklama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fiyat` decimal(15,2) DEFAULT NULL,
  `para_birimi` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TRY',
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ilan_tipi` enum('satilik','kiralik') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'satilik',
  `status` enum('active','pasif','satildi','kiralandi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `il_id` bigint unsigned DEFAULT NULL,
  `ilce_id` bigint unsigned DEFAULT NULL,
  `mahalle_id` bigint unsigned DEFAULT NULL,
  `adres` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `danisman_id` bigint unsigned DEFAULT NULL,
  `ilan_sahibi_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ilanlar_ilce_id_foreign` (`ilce_id`),
  KEY `ilanlar_mahalle_id_foreign` (`mahalle_id`),
  KEY `ilanlar_kategori_durum_index` (`kategori`,`status`),
  KEY `ilanlar_sehir_id_ilce_id_index` (`il_id`,`ilce_id`),
  KEY `ilanlar_fiyat_para_birimi_index` (`fiyat`,`para_birimi`),
  KEY `ilanlar_user_id_index` (`danisman_id`),
  KEY `ilanlar_ilan_sahibi_id_index` (`ilan_sahibi_id`),
  CONSTRAINT `ilanlar_danisman_id_foreign` FOREIGN KEY (`danisman_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ilanlar_ilan_sahibi_id_foreign` FOREIGN KEY (`ilan_sahibi_id`) REFERENCES `kisiler` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ilanlar_ilce_id_foreign` FOREIGN KEY (`ilce_id`) REFERENCES `ilceler` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ilanlar_mahalle_id_foreign` FOREIGN KEY (`mahalle_id`) REFERENCES `mahalleler` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ilanlar_sehir_id_foreign` FOREIGN KEY (`il_id`) REFERENCES `iller` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

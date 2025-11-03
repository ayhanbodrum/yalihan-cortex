/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `ai_category_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_category_analytics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `suggestion_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `confidence_score` decimal(3,2) NOT NULL,
  `ai_response` json NOT NULL,
  `user_accepted` tinyint(1) NOT NULL,
  `suggested_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_category_analytics_category_id_suggested_at_index` (`category_id`,`suggested_at`),
  KEY `ai_category_analytics_suggestion_type_confidence_score_index` (`suggestion_type`,`confidence_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ai_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_id` bigint unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `response_time` int DEFAULT NULL,
  `cost` decimal(10,6) DEFAULT NULL,
  `tokens_used` int DEFAULT NULL,
  `request_data` text COLLATE utf8mb4_unicode_ci,
  `response_data` longtext COLLATE utf8mb4_unicode_ci,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_logs_created_at_index` (`created_at`),
  KEY `ai_logs_provider_status_index` (`provider`,`status`),
  KEY `ai_logs_content_type_content_id_index` (`content_type`,`content_id`),
  KEY `ai_logs_user_id_index` (`user_id`),
  KEY `ai_logs_provider_index` (`provider`),
  KEY `ai_logs_status_index` (`status`),
  CONSTRAINT `ai_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`),
  KEY `blog_categories_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blog_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Beklemede',
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blog_post_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_post_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_post_tags_post_id_tag_id_unique` (`post_id`,`tag_id`),
  KEY `blog_post_tags_tag_id_foreign` (`tag_id`),
  CONSTRAINT `blog_post_tags_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_post_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `blog_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `author_id` bigint unsigned NOT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `view_count` int NOT NULL DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blog_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_tags_slug_unique` (`slug`),
  KEY `blog_tags_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `design_token_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `design_token_usage` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `component_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokens_used` json NOT NULL,
  `token_count` int NOT NULL,
  `compliance_score` decimal(3,2) NOT NULL,
  `analyzed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `design_token_usage_page_name_analyzed_at_index` (`page_name`,`analyzed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `error_memory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `error_memory` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `eslesmeler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eslesmeler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ilan_id` bigint unsigned NOT NULL,
  `kisi_id` bigint unsigned NOT NULL,
  `talep_id` bigint unsigned DEFAULT NULL,
  `danisman_id` bigint unsigned DEFAULT NULL,
  `skor` int NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beklemede',
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `eslesme_detaylari` json DEFAULT NULL,
  `eslesme_tarihi` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eslesmeler_kisi_id_foreign` (`kisi_id`),
  KEY `eslesmeler_status_index` (`status`),
  KEY `eslesmeler_skor_index` (`skor`),
  KEY `eslesmeler_ilan_id_kisi_id_index` (`ilan_id`,`kisi_id`),
  KEY `eslesmeler_danisman_id_index` (`danisman_id`),
  KEY `eslesmeler_talep_id_index` (`talep_id`),
  CONSTRAINT `eslesmeler_danisman_id_foreign` FOREIGN KEY (`danisman_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `eslesmeler_ilan_id_foreign` FOREIGN KEY (`ilan_id`) REFERENCES `ilanlar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eslesmeler_kisi_id_foreign` FOREIGN KEY (`kisi_id`) REFERENCES `kisiler` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eslesmeler_talep_id_foreign` FOREIGN KEY (`talep_id`) REFERENCES `talepler` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `etiketler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `etiketler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `etiketler_slug_unique` (`slug`),
  KEY `etiketler_status_order_index` (`status`,`order`),
  KEY `etiketler_slug_index` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `feature_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feature_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `seo_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feature_categories_slug_unique` (`slug`),
  KEY `feature_categories_status_order_index` (`status`,`order`),
  KEY `feature_categories_slug_index` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `features` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'boolean',
  `options` json DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feature_category_id` bigint unsigned DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_filterable` tinyint(1) NOT NULL DEFAULT '1',
  `is_searchable` tinyint(1) NOT NULL DEFAULT '1',
  `order` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `features_slug_unique` (`slug`),
  KEY `features_status_order_index` (`status`,`order`),
  KEY `features_feature_category_id_status_index` (`feature_category_id`,`status`),
  KEY `features_slug_index` (`slug`),
  CONSTRAINT `features_feature_category_id_foreign` FOREIGN KEY (`feature_category_id`) REFERENCES `feature_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gorevler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gorevler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aciklama` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Beklemede',
  `oncelik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `atanan_user_id` bigint unsigned DEFAULT NULL,
  `olusturan_user_id` bigint unsigned DEFAULT NULL,
  `baslangic_tarihi` date DEFAULT NULL,
  `bitis_tarihi` date DEFAULT NULL,
  `tamamlanma_yuzdesi` int NOT NULL DEFAULT '0',
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gorevler_status_oncelik_index` (`status`,`oncelik`),
  KEY `gorevler_atanan_user_id_index` (`atanan_user_id`),
  KEY `gorevler_bitis_tarihi_index` (`bitis_tarihi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ilan_kategori_yayin_tipleri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ilan_kategori_yayin_tipleri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori_id` bigint unsigned NOT NULL,
  `yayin_tipi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ilan_kategori_yayin_tipleri_kategori_id_yayin_tipi_unique` (`kategori_id`,`yayin_tipi`),
  KEY `ilan_kategori_yayin_tipleri_status_index` (`status`),
  CONSTRAINT `ilan_kategori_yayin_tipleri_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `ilan_kategorileri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ilan_kategorileri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ilan_kategorileri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aciklama` text COLLATE utf8mb4_unicode_ci,
  `parent_id` bigint unsigned DEFAULT NULL,
  `seviye` int NOT NULL DEFAULT '1',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ilan_kategorileri_slug_unique` (`slug`),
  KEY `ilan_kategorileri_parent_id_status_index` (`parent_id`,`status`),
  KEY `ilan_kategorileri_order_index` (`order`),
  KEY `ilan_kategorileri_seviye_index` (`seviye`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ilanlar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ilanlar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aciklama` text COLLATE utf8mb4_unicode_ci,
  `fiyat` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `user_id` bigint unsigned DEFAULT NULL,
  `danisman_id` bigint unsigned DEFAULT NULL,
  `kategori_id` bigint unsigned DEFAULT NULL,
  `il_id` bigint unsigned DEFAULT NULL,
  `ilce_id` bigint unsigned DEFAULT NULL,
  `mahalle_id` bigint unsigned DEFAULT NULL,
  `adres` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oda_sayisi` int DEFAULT NULL,
  `salon_sayisi` int DEFAULT NULL,
  `banyo_sayisi` int DEFAULT NULL,
  `kat` int DEFAULT NULL,
  `toplam_kat` int DEFAULT NULL,
  `brut_m2` decimal(10,2) DEFAULT NULL,
  `net_m2` decimal(10,2) DEFAULT NULL,
  `bina_yasi` year DEFAULT NULL,
  `isitma` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aidat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `esyali` tinyint(1) NOT NULL DEFAULT '0',
  `ilan_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referans_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dosya_adi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sahibinden_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emlakjet_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hepsiemlak_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zingat_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hurriyetemlak_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portal_sync_status` json DEFAULT NULL,
  `portal_pricing` json DEFAULT NULL,
  `goruntulenme` int NOT NULL DEFAULT '0',
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `anahtar_kimde` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anahtar_turu` enum('mal_sahibi','danisman','kapici','emlakci','yonetici','diger') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anahtar_notlari` text COLLATE utf8mb4_unicode_ci,
  `anahtar_ulasilabilirlik` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anahtar_ek_bilgi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ilanlar_ilan_no_unique` (`ilan_no`),
  UNIQUE KEY `ilanlar_referans_no_unique` (`referans_no`),
  KEY `ilanlar_status_created_at_index` (`status`,`created_at`),
  KEY `ilanlar_il_id_ilce_id_index` (`il_id`,`ilce_id`),
  KEY `ilanlar_kategori_id_status_index` (`kategori_id`,`status`),
  KEY `ilanlar_user_id_index` (`user_id`),
  KEY `idx_referans_no` (`referans_no`),
  KEY `idx_sahibinden_id` (`sahibinden_id`),
  KEY `idx_emlakjet_id` (`emlakjet_id`),
  KEY `idx_hepsiemlak_id` (`hepsiemlak_id`),
  KEY `idx_zingat_id` (`zingat_id`),
  KEY `ilanlar_danisman_id_index` (`danisman_id`),
  CONSTRAINT `ilanlar_danisman_id_foreign` FOREIGN KEY (`danisman_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ilceler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ilceler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `il_id` bigint unsigned NOT NULL,
  `ilce_adi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ilceler_il_id_ilce_adi_index` (`il_id`,`ilce_adi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `iller`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `iller` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `il_adi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plaka_kodu` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefon_kodu` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iller_plaka_kodu_unique` (`plaka_kodu`),
  KEY `iller_il_adi_index` (`il_adi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kisiler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kisiler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `danisman_id` bigint unsigned DEFAULT NULL,
  `ad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `soyad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefon_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tc_kimlik` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adres` text COLLATE utf8mb4_unicode_ci,
  `il_id` bigint unsigned DEFAULT NULL,
  `ilce_id` bigint unsigned DEFAULT NULL,
  `meslek` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kisi_tipi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Müşteri',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kisiler_status_kisi_tipi_index` (`status`,`kisi_tipi`),
  KEY `kisiler_il_id_ilce_id_index` (`il_id`,`ilce_id`),
  KEY `kisiler_user_id_index` (`user_id`),
  KEY `kisiler_email_index` (`email`),
  KEY `kisiler_telefon_index` (`telefon`),
  KEY `kisiler_danisman_id_index` (`danisman_id`),
  CONSTRAINT `kisiler_danisman_id_foreign` FOREIGN KEY (`danisman_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mahalleler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahalleler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ilce_id` bigint unsigned NOT NULL,
  `mahalle_adi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mahalle_kodu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `posta_kodu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mahalleler_ilce_id_index` (`ilce_id`),
  KEY `mahalleler_mahalle_adi_index` (`mahalle_adi`),
  CONSTRAINT `mahalleler_ilce_id_foreign` FOREIGN KEY (`ilce_id`) REFERENCES `ilceler` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ozellik_kategorileri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ozellik_kategorileri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aciklama` text COLLATE utf8mb4_unicode_ci,
  `parent_id` bigint unsigned DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ozellik_kategorileri_slug_unique` (`slug`),
  KEY `ozellik_kategorileri_parent_id_status_index` (`parent_id`,`status`),
  KEY `ozellik_kategorileri_order_index` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ozellikler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ozellikler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_id` bigint unsigned DEFAULT NULL,
  `veri_tipi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `veri_secenekleri` json DEFAULT NULL,
  `birim` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `order` int NOT NULL DEFAULT '0',
  `zorunlu` tinyint(1) NOT NULL DEFAULT '0',
  `arama_filtresi` tinyint(1) NOT NULL DEFAULT '0',
  `ilan_kartinda_goster` tinyint(1) NOT NULL DEFAULT '0',
  `aciklama` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ozellikler_slug_unique` (`slug`),
  KEY `ozellikler_status_index` (`status`),
  KEY `ozellikler_kategori_id_index` (`kategori_id`),
  KEY `ozellikler_order_index` (`order`),
  CONSTRAINT `ozellikler_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `ozellik_kategorileri` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `projeler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projeler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aciklama` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `oncelik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Orta',
  `baslangic_tarihi` date DEFAULT NULL,
  `bitis_tarihi` date DEFAULT NULL,
  `takim_lideri_id` bigint unsigned DEFAULT NULL,
  `butce` decimal(15,2) DEFAULT NULL,
  `tamamlanma_yuzdesi` int NOT NULL DEFAULT '0',
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projeler_slug_unique` (`slug`),
  KEY `projeler_status_index` (`status`),
  KEY `projeler_oncelik_index` (`oncelik`),
  KEY `projeler_takim_lideri_id_index` (`takim_lideri_id`),
  CONSTRAINT `projeler_takim_lideri_id_foreign` FOREIGN KEY (`takim_lideri_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `search_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `search_analytics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unified',
  `filters` json DEFAULT NULL,
  `results_count` int NOT NULL,
  `response_time` double(8,2) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `searched_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `search_analytics_query_searched_at_index` (`query`,`searched_at`),
  KEY `search_analytics_type_success_index` (`type`,`success`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`),
  KEY `settings_group_key_index` (`group`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `site_apartmanlar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_apartmanlar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Site/Apartman adı',
  `adres` text COLLATE utf8mb4_unicode_ci,
  `il_id` bigint unsigned DEFAULT NULL,
  `ilce_id` bigint unsigned DEFAULT NULL,
  `mahalle_id` bigint unsigned DEFAULT NULL,
  `yonetici_adi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `yonetici_telefon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `yonetici_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kapici_telefon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `toplam_daire_sayisi` int DEFAULT NULL,
  `kat_sayisi` int DEFAULT NULL,
  `asansor_sayisi` int DEFAULT NULL,
  `otopark_statusu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sosyal_tesisler` json DEFAULT NULL,
  `guvenlik_sistemi` json DEFAULT NULL,
  `aidat_tutari` decimal(10,2) DEFAULT NULL,
  `aidat_para_birimi` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TRY',
  `aidat_periyodu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'aylık, 3 aylık, 6 aylık, yıllık',
  `yapim_yili` year DEFAULT NULL,
  `yapi_tarzi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isitma_sistemi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_apartmanlar_ilce_id_foreign` (`ilce_id`),
  KEY `site_apartmanlar_mahalle_id_foreign` (`mahalle_id`),
  KEY `site_apartmanlar_name_index` (`name`),
  KEY `site_apartmanlar_il_id_ilce_id_mahalle_id_index` (`il_id`,`ilce_id`,`mahalle_id`),
  CONSTRAINT `site_apartmanlar_il_id_foreign` FOREIGN KEY (`il_id`) REFERENCES `iller` (`id`) ON DELETE SET NULL,
  CONSTRAINT `site_apartmanlar_ilce_id_foreign` FOREIGN KEY (`ilce_id`) REFERENCES `ilceler` (`id`) ON DELETE SET NULL,
  CONSTRAINT `site_apartmanlar_mahalle_id_foreign` FOREIGN KEY (`mahalle_id`) REFERENCES `mahalleler` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`),
  KEY `site_settings_key_index` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blok_adi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adres` text COLLATE utf8mb4_unicode_ci,
  `il_id` bigint unsigned NOT NULL,
  `ilce_id` bigint unsigned NOT NULL,
  `mahalle_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','inactive','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sites_ilce_id_foreign` (`ilce_id`),
  KEY `sites_mahalle_id_foreign` (`mahalle_id`),
  KEY `sites_created_by_foreign` (`created_by`),
  KEY `sites_il_id_ilce_id_name_index` (`il_id`,`ilce_id`,`name`),
  KEY `sites_status_name_index` (`status`,`name`),
  KEY `sites_name_index` (`name`),
  CONSTRAINT `sites_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sites_il_id_foreign` FOREIGN KEY (`il_id`) REFERENCES `iller` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sites_ilce_id_foreign` FOREIGN KEY (`ilce_id`) REFERENCES `ilceler` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sites_mahalle_id_foreign` FOREIGN KEY (`mahalle_id`) REFERENCES `mahalleler` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `takim_uyeleri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `takim_uyeleri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `pozisyon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departman` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasyon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `performans_skoru` int NOT NULL DEFAULT '0',
  `ise_baslama_tarihi` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `takim_uyeleri_status_departman_index` (`status`,`departman`),
  KEY `takim_uyeleri_user_id_index` (`user_id`),
  KEY `takim_uyeleri_performans_skoru_index` (`performans_skoru`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `talepler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `talepler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kisi_id` bigint unsigned NOT NULL,
  `danisman_id` bigint unsigned DEFAULT NULL,
  `talep_tipi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emlak_tipi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_fiyat` decimal(15,2) DEFAULT NULL,
  `max_fiyat` decimal(15,2) DEFAULT NULL,
  `il_id` bigint unsigned DEFAULT NULL,
  `ilce_id` bigint unsigned DEFAULT NULL,
  `notlar` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `oncelik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Orta',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `talepler_il_id_foreign` (`il_id`),
  KEY `talepler_ilce_id_foreign` (`ilce_id`),
  KEY `talepler_status_index` (`status`),
  KEY `talepler_talep_tipi_index` (`talep_tipi`),
  KEY `talepler_kisi_id_index` (`kisi_id`),
  KEY `talepler_danisman_id_index` (`danisman_id`),
  CONSTRAINT `talepler_danisman_id_foreign` FOREIGN KEY (`danisman_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `talepler_il_id_foreign` FOREIGN KEY (`il_id`) REFERENCES `iller` (`id`) ON DELETE SET NULL,
  CONSTRAINT `talepler_ilce_id_foreign` FOREIGN KEY (`ilce_id`) REFERENCES `ilceler` (`id`) ON DELETE SET NULL,
  CONSTRAINT `talepler_kisi_id_foreign` FOREIGN KEY (`kisi_id`) REFERENCES `kisiler` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ulkeler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ulkeler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ulke_adi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ulke_kodu` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefon_kodu` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `para_birimi` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ulkeler_ulke_kodu_unique` (`ulke_kodu`),
  KEY `ulkeler_ulke_adi_index` (`ulke_adi`),
  KEY `ulkeler_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `telegram_chat_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2025_10_07_214858_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2025_10_07_214921_add_missing_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_10_07_220521_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_10_07_220551_create_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_10_08_132426_update_sites_status_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_10_08_133526_add_soft_deletes_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_10_10_071029_add_last_activity_at_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_10_10_073304_create_ilanlar_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_10_10_073503_create_ilan_kategorileri_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_10_10_073545_create_ilceler_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_10_10_073545_create_iller_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_10_10_073826_create_kisiler_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_10_10_160010_create_gorevler_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_10_10_160010_create_ozellik_kategorileri_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_10_10_160010_create_settings_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_10_10_160010_create_takim_uyeleri_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_10_10_160011_create_site_settings_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_10_10_160011_create_ulkeler_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_10_10_203000_create_talepler_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_10_10_174618_add_seviye_to_ilan_kategorileri_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_10_10_174859_create_blog_categories_and_tags_tables',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_10_10_174650_create_blog_posts_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_10_10_174808_create_ilan_kategori_yayin_tipleri_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_10_10_174600_create_blog_comments_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_10_10_175050_create_ozellikler_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_10_10_175050_create_projeler_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_10_10_180210_create_eslesmeler_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_10_10_184720_add_talep_id_to_eslesmeler_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_10_11_180000_add_referans_system_to_ilanlar_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_10_11_151632_create_error_memory_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_10_11_215700_add_anahtar_management_fields_to_ilanlar_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_10_12_174311_rename_site_adi_to_name_in_site_apartmanlar_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_10_12_180000_create_mahalleler_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_10_13_204351_create_site_apartmanlar_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_10_14_130126_create_ai_logs_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_10_15_160340_create_feature_categories_table',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_10_15_170751_create_etiketler_table',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_10_15_172758_create_features_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_10_15_213116_add_danisman_id_to_ilanlar_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_10_15_213509_add_danisman_id_to_kisiler_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_10_15_220340_create_search_analytics_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_10_15_220402_create_design_token_usage_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_10_15_220437_create_ai_category_analytics_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_10_16_220234_create_sites_table',30);

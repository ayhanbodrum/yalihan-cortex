-- MySQL dump 10.13  Distrib 9.3.0, for macos15.2 (arm64)
--
-- Host: localhost    Database: yalihanemlak_ultra
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`),
  KEY `blog_categories_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etiketler`
--

DROP TABLE IF EXISTS `etiketler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `etiketler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'feature',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bg_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `badge_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_badge` tinyint(1) NOT NULL DEFAULT '0',
  `target_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `etiketler_slug_unique` (`slug`),
  KEY `etiketler_status_order_index` (`status`,`order`),
  KEY `etiketler_slug_index` (`slug`),
  KEY `etiketler_type_index` (`type`),
  KEY `etiketler_is_badge_status_index` (`is_badge`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etiketler`
--

LOCK TABLES `etiketler` WRITE;
/*!40000 ALTER TABLE `etiketler` DISABLE KEYS */;
/*!40000 ALTER TABLE `etiketler` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ozellikler`
--

DROP TABLE IF EXISTS `ozellikler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ozellikler` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_id` bigint unsigned DEFAULT NULL,
  `veri_tipi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `veri_secenekleri` json DEFAULT NULL,
  `birim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `order` int NOT NULL DEFAULT '0',
  `zorunlu` tinyint(1) NOT NULL DEFAULT '0',
  `arama_filtresi` tinyint(1) NOT NULL DEFAULT '0',
  `ilan_kartinda_goster` tinyint(1) NOT NULL DEFAULT '0',
  `aciklama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ozellikler_slug_unique` (`slug`),
  KEY `ozellikler_status_index` (`status`),
  KEY `ozellikler_kategori_id_index` (`kategori_id`),
  KEY `ozellikler_order_index` (`order`),
  KEY `idx_ozellikler_kategori` (`kategori_id`),
  KEY `idx_ozellikler_status` (`status`),
  KEY `idx_ozellikler_order` (`order`),
  CONSTRAINT `ozellikler_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `ozellik_kategorileri` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ozellikler`
--

LOCK TABLES `ozellikler` WRITE;
/*!40000 ALTER TABLE `ozellikler` DISABLE KEYS */;
/*!40000 ALTER TABLE `ozellikler` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_ozellikleri`
--

DROP TABLE IF EXISTS `site_ozellikleri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_ozellikleri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Özellik adı (örn: Güvenlik, Otopark)',
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL-friendly slug',
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'amenity' COMMENT 'Özellik tipi: amenity, security, facility',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Özellik açıklaması',
  `order` int NOT NULL DEFAULT '0' COMMENT 'Sıralama',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Context7: Aktif/Pasif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_ozellikleri_slug_unique` (`slug`),
  KEY `idx_site_ozellikleri_type` (`type`),
  KEY `idx_site_ozellikleri_status` (`status`),
  KEY `idx_site_ozellikleri_order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_ozellikleri`
--

LOCK TABLES `site_ozellikleri` WRITE;
/*!40000 ALTER TABLE `site_ozellikleri` DISABLE KEYS */;
/*!40000 ALTER TABLE `site_ozellikleri` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori_ozellik_matrix`
--

DROP TABLE IF EXISTS `kategori_ozellik_matrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori_ozellik_matrix` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori_slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `yayin_tipi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ozellik_kategori_id` bigint unsigned NOT NULL,
  `ozellik_alt_kategori_id` bigint unsigned NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `zorunlu` tinyint(1) NOT NULL DEFAULT '0',
  `ai_suggestion` tinyint(1) NOT NULL DEFAULT '0',
  `ai_auto_fill` tinyint(1) NOT NULL DEFAULT '0',
  `sira` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_matrix` (`kategori_slug`,`yayin_tipi`,`ozellik_kategori_id`,`ozellik_alt_kategori_id`),
  KEY `kategori_ozellik_matrix_ozellik_kategori_id_foreign` (`ozellik_kategori_id`),
  KEY `kategori_ozellik_matrix_ozellik_alt_kategori_id_foreign` (`ozellik_alt_kategori_id`),
  CONSTRAINT `kategori_ozellik_matrix_ozellik_alt_kategori_id_foreign` FOREIGN KEY (`ozellik_alt_kategori_id`) REFERENCES `ozellik_alt_kategorileri` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kategori_ozellik_matrix_ozellik_kategori_id_foreign` FOREIGN KEY (`ozellik_kategori_id`) REFERENCES `ozellik_kategorileri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori_ozellik_matrix`
--

LOCK TABLES `kategori_ozellik_matrix` WRITE;
/*!40000 ALTER TABLE `kategori_ozellik_matrix` DISABLE KEYS */;
/*!40000 ALTER TABLE `kategori_ozellik_matrix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konut_ozellik_hibrit_siralama`
--

DROP TABLE IF EXISTS `konut_ozellik_hibrit_siralama`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konut_ozellik_hibrit_siralama` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ozellik_adi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ozellik_slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kullanim_sikligi` int NOT NULL DEFAULT '0',
  `ai_oneri_yuzdesi` decimal(5,2) NOT NULL DEFAULT '0.00',
  `kullanici_tercih_yuzdesi` decimal(5,2) NOT NULL DEFAULT '0.00',
  `hibrit_skor` decimal(8,2) NOT NULL DEFAULT '0.00',
  `onem_seviyesi` enum('cok_onemli','onemli','orta_onemli','dusuk_onemli') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'orta_onemli',
  `siralama` int NOT NULL DEFAULT '0',
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `aciklama` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `konut_ozellik_hibrit_siralama_ozellik_slug_unique` (`ozellik_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konut_ozellik_hibrit_siralama`
--

LOCK TABLES `konut_ozellik_hibrit_siralama` WRITE;
/*!40000 ALTER TABLE `konut_ozellik_hibrit_siralama` DISABLE KEYS */;
/*!40000 ALTER TABLE `konut_ozellik_hibrit_siralama` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ozellik_alt_kategorileri`
--

DROP TABLE IF EXISTS `ozellik_alt_kategorileri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ozellik_alt_kategorileri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ozellik_kategori_id` bigint unsigned NOT NULL,
  `alt_kategori_adi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_kategori_slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_kategori_icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_type` enum('text','number','boolean','select','textarea','date','price','location') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `field_options` json DEFAULT NULL,
  `field_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aciklama` text COLLATE utf8mb4_unicode_ci,
  `sira` int NOT NULL DEFAULT '0',
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ozellik_alt_kategorileri_ozellik_kategori_id_foreign` (`ozellik_kategori_id`),
  CONSTRAINT `ozellik_alt_kategorileri_ozellik_kategori_id_foreign` FOREIGN KEY (`ozellik_kategori_id`) REFERENCES `ozellik_kategorileri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ozellik_alt_kategorileri`
--

LOCK TABLES `ozellik_alt_kategorileri` WRITE;
/*!40000 ALTER TABLE `ozellik_alt_kategorileri` DISABLE KEYS */;
/*!40000 ALTER TABLE `ozellik_alt_kategorileri` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori_yayin_tipi_field_dependencies`
--

DROP TABLE IF EXISTS `kategori_yayin_tipi_field_dependencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori_yayin_tipi_field_dependencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori_slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kategori slug: konut, arsa, yazlik, isyeri',
  `yayin_tipi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Yayın tipi: Satılık, Kiralık, Sezonluk Kiralık, Devren Satış',
  `field_slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Field slug: ada_no, gunluk_fiyat, oda_sayisi',
  `field_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Field görünen adı: Ada No, Günlük Fiyat',
  `field_type` enum('text','number','boolean','select','textarea','date','price','location') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Field tipi',
  `field_category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Field kategorisi: fiyat, ozellik, dokuман, sezonluk, arsa',
  `field_options` json DEFAULT NULL COMMENT 'Select field için seçenekler: {"1+1":"1+1","2+1":"2+1"}',
  `field_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Birim: m², TL, Gün, %',
  `field_placeholder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Placeholder metin',
  `field_help` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Yardım metni',
  `field_icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Icon: ?, ?, ?',
  `enabled` tinyint NOT NULL DEFAULT '1' COMMENT '0=disabled, 1=enabled (Context7 boolean)',
  `required` tinyint NOT NULL DEFAULT '0' COMMENT '0=optional, 1=required (Context7 boolean)',
  `searchable` tinyint NOT NULL DEFAULT '0' COMMENT '0=not searchable, 1=searchable',
  `show_in_card` tinyint NOT NULL DEFAULT '0' COMMENT '0=hide in card, 1=show in card',
  `display_order` int NOT NULL DEFAULT '0',
  `ai_suggestion` tinyint NOT NULL DEFAULT '0' COMMENT 'AI ile öneri yapılsın mı?',
  `ai_auto_fill` tinyint NOT NULL DEFAULT '0' COMMENT 'AI ile otomatik doldurulsun mu?',
  `ai_prompt_key` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'AI prompt dosyası key: arsa-ada-no-suggest',
  `ai_context` json DEFAULT NULL COMMENT 'AI için context bilgileri',
  `validation_min` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Min değer/uzunluk',
  `validation_max` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Max değer/uzunluk',
  `validation_rules` json DEFAULT NULL COMMENT 'Laravel validation rules',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_kategori_yayin_field` (`kategori_slug`,`yayin_tipi`,`field_slug`),
  KEY `idx_kategori_yayin` (`kategori_slug`,`yayin_tipi`),
  KEY `idx_field_slug` (`field_slug`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_ai_suggestion` (`ai_suggestion`),
  KEY `idx_category_order` (`field_category`,`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori_yayin_tipi_field_dependencies`
--

LOCK TABLES `kategori_yayin_tipi_field_dependencies` WRITE;
/*!40000 ALTER TABLE `kategori_yayin_tipi_field_dependencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `kategori_yayin_tipi_field_dependencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yayin_tipleri`
--

DROP TABLE IF EXISTS `yayin_tipleri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `yayin_tipleri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yayin_tipleri`
--

LOCK TABLES `yayin_tipleri` WRITE;
/*!40000 ALTER TABLE `yayin_tipleri` DISABLE KEYS */;
/*!40000 ALTER TABLE `yayin_tipleri` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yazlik_details`
--

DROP TABLE IF EXISTS `yazlik_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `yazlik_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ilan_id` bigint unsigned NOT NULL,
  `min_konaklama` int NOT NULL DEFAULT '1' COMMENT 'Minimum konaklama günü',
  `max_misafir` int DEFAULT NULL COMMENT 'Maksimum misafir sayısı',
  `oda_sayisi` int DEFAULT NULL COMMENT 'Oda sayısı',
  `banyo_sayisi` int DEFAULT NULL COMMENT 'Banyo sayısı',
  `yatak_sayisi` int DEFAULT NULL COMMENT 'Yatak sayısı',
  `yatak_turleri` json DEFAULT NULL COMMENT 'Yatak türleri array',
  `carsaf_dahil` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Çarşaf dahil mi',
  `havlu_dahil` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Havlu dahil mi',
  `temizlik_ucreti` decimal(10,2) DEFAULT NULL,
  `havuz` tinyint(1) NOT NULL DEFAULT '0',
  `havuz_turu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `havuz_boyut` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `havuz_derinlik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `havuz_boyut_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Havuz genişlik (m)',
  `havuz_boyut_boy` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Havuz uzunluk (m)',
  `bahce_var` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Bahçe var mı',
  `tv_var` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'TV var mı',
  `barbeku_var` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Barbekü var mı',
  `sezlong_var` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Şezlong var mı',
  `bahce_masasi_var` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Bahçe masası var mı',
  `manzara` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Manzara türü',
  `ozel_isaretler` json DEFAULT NULL COMMENT 'Özel işaretler array',
  `ev_tipi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ev tipi (villa, bungalov, etc.)',
  `ev_konsepti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ev konsepti',
  `gunluk_fiyat` decimal(10,2) DEFAULT NULL,
  `haftalik_fiyat` decimal(10,2) DEFAULT NULL,
  `aylik_fiyat` decimal(10,2) DEFAULT NULL,
  `sezonluk_fiyat` decimal(10,2) DEFAULT NULL,
  `sezon_baslangic` date DEFAULT NULL,
  `sezon_bitis` date DEFAULT NULL,
  `elektrik_dahil` tinyint(1) NOT NULL DEFAULT '0',
  `internet_dahil` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'İnternet dahil mi',
  `klima_var` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Klima var mı',
  `restoran_mesafe` int DEFAULT NULL COMMENT 'Restoran mesafe (km)',
  `market_mesafe` int DEFAULT NULL COMMENT 'Market mesafe (km)',
  `deniz_mesafe` int DEFAULT NULL COMMENT 'Deniz mesafe (km)',
  `merkez_mesafe` int DEFAULT NULL COMMENT 'Merkez mesafe (km)',
  `su_dahil` tinyint(1) NOT NULL DEFAULT '0',
  `ozel_notlar` text COLLATE utf8mb4_unicode_ci,
  `musteri_notlari` text COLLATE utf8mb4_unicode_ci,
  `indirim_notlari` text COLLATE utf8mb4_unicode_ci,
  `indirimli_fiyat` decimal(10,2) DEFAULT NULL,
  `anahtar_kimde` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anahtar_notlari` text COLLATE utf8mb4_unicode_ci,
  `sahip_ozel_notlari` text COLLATE utf8mb4_unicode_ci,
  `sahip_iletisim_tercihi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eids_onayli` tinyint(1) NOT NULL DEFAULT '0',
  `eids_onay_tarihi` date DEFAULT NULL,
  `eids_belge_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `yazlik_details_ilan_id_unique` (`ilan_id`),
  KEY `yazlik_details_ilan_id_index` (`ilan_id`),
  KEY `yazlik_details_sezon_baslangic_index` (`sezon_baslangic`),
  KEY `yazlik_details_sezon_bitis_index` (`sezon_bitis`),
  CONSTRAINT `yazlik_details_ilan_id_foreign` FOREIGN KEY (`ilan_id`) REFERENCES `ilanlar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yazlik_details`
--

LOCK TABLES `yazlik_details` WRITE;
/*!40000 ALTER TABLE `yazlik_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `yazlik_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yazlik_rezervasyonlar`
--

DROP TABLE IF EXISTS `yazlik_rezervasyonlar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `yazlik_rezervasyonlar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ilan_id` bigint unsigned NOT NULL,
  `musteri_adi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Müşteri adı',
  `musteri_telefon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Müşteri telefonu',
  `musteri_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Müşteri e-posta',
  `check_in` date NOT NULL COMMENT 'Giriş tarihi',
  `check_out` date NOT NULL COMMENT 'Çıkış tarihi',
  `misafir_sayisi` int NOT NULL DEFAULT '1' COMMENT 'Misafir sayısı',
  `cocuk_sayisi` int NOT NULL DEFAULT '0' COMMENT 'Çocuk sayısı',
  `pet_sayisi` int NOT NULL DEFAULT '0' COMMENT 'Evcil hayvan sayısı',
  `ozel_istekler` text COLLATE utf8mb4_unicode_ci COMMENT 'Özel istekler',
  `toplam_fiyat` decimal(10,2) NOT NULL COMMENT 'Toplam fiyat',
  `kapora_tutari` decimal(10,2) DEFAULT NULL COMMENT 'Kapora tutarı',
  `status` enum('beklemede','onaylandi','iptal','tamamlandi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beklemede' COMMENT 'Rezervasyon durumu',
  `iptal_nedeni` text COLLATE utf8mb4_unicode_ci COMMENT 'İptal nedeni',
  `onay_tarihi` timestamp NULL DEFAULT NULL COMMENT 'Onay tarihi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_yazlik_rez_ilan` (`ilan_id`),
  KEY `idx_yazlik_rez_tarih` (`check_in`,`check_out`),
  KEY `idx_yazlik_rez_status` (`status`),
  KEY `idx_yazlik_rez_telefon` (`musteri_telefon`),
  KEY `idx_yazlik_rez_email` (`musteri_email`),
  CONSTRAINT `yazlik_rezervasyonlar_ilan_id_foreign` FOREIGN KEY (`ilan_id`) REFERENCES `ilanlar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yazlik_rezervasyonlar`
--

LOCK TABLES `yazlik_rezervasyonlar` WRITE;
/*!40000 ALTER TABLE `yazlik_rezervasyonlar` DISABLE KEYS */;
/*!40000 ALTER TABLE `yazlik_rezervasyonlar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_core_system`
--

DROP TABLE IF EXISTS `ai_core_system`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_core_system` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `context` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prompt_template` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `learned_patterns` json DEFAULT NULL,
  `success_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `usage_count` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_core_system_context_task_type_unique` (`context`,`task_type`),
  KEY `ai_core_system_context_success_rate_index` (`context`,`success_rate`),
  KEY `ai_core_system_context_index` (`context`),
  KEY `ai_core_system_task_type_index` (`task_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_core_system`
--

LOCK TABLES `ai_core_system` WRITE;
/*!40000 ALTER TABLE `ai_core_system` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_core_system` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-11 13:41:12

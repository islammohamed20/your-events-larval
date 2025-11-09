/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: your_events
-- ------------------------------------------------------
-- Server version	10.6.22-MariaDB-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `your_events`
--

/*!40000 DROP DATABASE IF EXISTS `your_events`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `your_events` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `your_events`;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `package_id` bigint(20) unsigned DEFAULT NULL,
  `service_id` bigint(20) unsigned DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_email` varchar(255) NOT NULL,
  `client_phone` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `guests_count` int(11) NOT NULL DEFAULT 1,
  `special_requests` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `booking_reference` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookings_booking_reference_unique` (`booking_reference`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_package_id_foreign` (`package_id`),
  KEY `bookings_service_id_foreign` (`service_id`),
  CONSTRAINT `bookings_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,NULL,NULL,2,'Eslam Mohamed','islammahamd@gmail.com','0122323223','2025-08-19','الدوحة',50,NULL,1500.00,'completed','YE-SVFXA6XNXKXL87M9','2025-08-16 06:15:32','2025-08-16 06:29:58'),(4,NULL,NULL,3,'Eslam Mohamed','islamaldjlaksjfkhlh@gmail.com','0128912873126','2025-08-21','الدوحة',50,NULL,0.00,'pending','YE-LSHONJTGDYK3CYQP','2025-08-17 23:57:25','2025-08-17 23:57:25');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('a2214ecf520b2eaba3e785a6546590ec7c69981f','i:1;',1755473623),('a2214ecf520b2eaba3e785a6546590ec7c69981f:timer','i:1755473623;',1755473623),('eacc47d86f1c80b9e52940a29553305673c0494b','i:1;',1755475399),('eacc47d86f1c80b9e52940a29553305673c0494b:timer','i:1755475399;',1755475399),('site_settings','a:71:{s:9:\"site_name\";s:11:\"Your Events\";s:12:\"site_tagline\";s:53:\"تجارب واقع افتراضي استثنائية\";s:16:\"site_description\";s:107:\"نقدم تجارب واقع افتراضي مبتكرة وفعاليات تفاعلية استثنائية\";s:13:\"contact_phone\";s:16:\"+966 50 123 4567\";s:13:\"contact_email\";s:19:\"info@yourevents.com\";s:15:\"contact_address\";s:61:\"الرياض، المملكة العربية السعودية\";s:13:\"working_hours\";s:44:\"الأحد - الخميس: 9:00 ص - 6:00 م\";s:15:\"whatsapp_number\";s:13:\"+966501234567\";s:12:\"facebook_url\";s:0:\"\";s:11:\"twitter_url\";s:0:\"\";s:13:\"instagram_url\";s:0:\"\";s:12:\"linkedin_url\";s:0:\"\";s:11:\"youtube_url\";s:0:\"\";s:10:\"tiktok_url\";s:0:\"\";s:13:\"primary_color\";s:7:\"#1f144a\";s:15:\"secondary_color\";s:7:\"#2dbcae\";s:12:\"accent_color\";s:7:\"#ef4870\";s:10:\"gold_color\";s:7:\"#f0c71d\";s:12:\"purple_light\";s:7:\"#7269b0\";s:8:\"bg_light\";s:7:\"#ffffff\";s:12:\"bg_secondary\";s:7:\"#f8f9fa\";s:10:\"text_color\";s:7:\"#222222\";s:11:\"hover_color\";s:7:\"#f56b8a\";s:10:\"sidebar_bg\";s:7:\"#1f144a\";s:13:\"sidebar_hover\";s:7:\"#2d1a5e\";s:19:\"font_family_primary\";s:7:\"Tajawal\";s:21:\"font_family_secondary\";s:5:\"Amiri\";s:19:\"font_family_english\";s:5:\"Inter\";s:8:\"logo_url\";s:0:\"\";s:11:\"favicon_url\";s:0:\"\";s:15:\"header_bg_color\";s:7:\"#1f144a\";s:15:\"footer_bg_color\";s:7:\"#1f144a\";s:13:\"button_radius\";s:3:\"8px\";s:11:\"card_radius\";s:4:\"12px\";s:12:\"shadow_color\";s:15:\"rgba(0,0,0,0.1)\";s:14:\"gradient_start\";s:7:\"#1f144a\";s:12:\"gradient_end\";s:7:\"#7269b0\";s:10:\"meta_title\";s:67:\"Your Events - تجارب واقع افتراضي استثنائية\";s:16:\"meta_description\";s:159:\"نقدم تجارب واقع افتراضي مبتكرة وفعاليات تفاعلية استثنائية في المملكة العربية السعودية\";s:13:\"meta_keywords\";s:88:\"واقع افتراضي, فعاليات, تجارب تفاعلية, VR, السعودية\";s:11:\"meta_author\";s:11:\"Your Events\";s:11:\"meta_robots\";s:13:\"index, follow\";s:13:\"meta_viewport\";s:35:\"width=device-width, initial-scale=1\";s:8:\"og_title\";s:67:\"Your Events - تجارب واقع افتراضي استثنائية\";s:14:\"og_description\";s:107:\"نقدم تجارب واقع افتراضي مبتكرة وفعاليات تفاعلية استثنائية\";s:8:\"og_image\";s:0:\"\";s:6:\"og_url\";s:0:\"\";s:12:\"twitter_card\";s:19:\"summary_large_image\";s:12:\"twitter_site\";s:0:\"\";s:15:\"twitter_creator\";s:0:\"\";s:13:\"canonical_url\";s:0:\"\";s:11:\"schema_type\";s:12:\"Organization\";s:11:\"schema_name\";s:11:\"Your Events\";s:18:\"schema_description\";s:109:\"شركة متخصصة في تجارب الواقع الافتراضي والفعاليات التفاعلية\";s:11:\"schema_logo\";s:0:\"\";s:14:\"schema_address\";s:61:\"الرياض، المملكة العربية السعودية\";s:12:\"schema_phone\";s:13:\"+966501234567\";s:12:\"schema_email\";s:19:\"info@yourevents.com\";s:19:\"google_analytics_id\";s:0:\"\";s:21:\"google_tag_manager_id\";s:0:\"\";s:17:\"facebook_pixel_id\";s:0:\"\";s:24:\"google_site_verification\";s:0:\"\";s:22:\"bing_site_verification\";s:0:\"\";s:19:\"yandex_verification\";s:0:\"\";s:9:\"smtp_host\";s:0:\"\";s:9:\"smtp_port\";i:587;s:13:\"smtp_username\";s:0:\"\";s:13:\"smtp_password\";s:0:\"\";s:15:\"smtp_encryption\";s:3:\"tls\";s:16:\"maintenance_mode\";b:0;s:19:\"maintenance_message\";s:61:\"الموقع تحت الصيانة، سنعود قريباً!\";}',1755334519);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `galleries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` enum('image','video') NOT NULL DEFAULT 'image',
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `alt_text` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
INSERT INTO `galleries` VALUES (5,'وش يعني Your Event','image','gallery/Rrxhq0HAcskDBjKm2ztCQwWas7srEbzYRFuVdmZD.jpg',NULL,0,'2025-08-15 09:30:20','2025-08-15 09:30:20',1,'وش يعني Your Event',346699,'image/jpeg','team'),(6,'أحلي لحظة مع احلي صورة','image','gallery/fonGFTOjOZJoFVYi94RJdEgfjgRfmUQjWNvg6TyO.jpg',NULL,0,'2025-08-15 09:34:28','2025-08-15 09:34:28',2,'أحلي لحظة مع احلي صورة',161033,'image/jpeg','team');
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `type` enum('image','video') NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `file_size` bigint(20) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gallery_type_is_featured_index` (`type`,`is_featured`),
  KEY `gallery_category_index` (`category`),
  KEY `gallery_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery`
--

LOCK TABLES `gallery` WRITE;
/*!40000 ALTER TABLE `gallery` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000001_create_users_table',1),(2,'2024_01_02_000001_create_packages_table',1),(3,'2024_01_03_000001_create_services_table',1),(4,'2024_01_04_000001_create_galleries_table',1),(5,'2024_01_05_000001_create_bookings_table',1),(6,'2024_01_06_000001_create_reviews_table',1),(7,'2025_08_09_184445_create_cache_table',2),(9,'2025_08_09_223452_create_gallery_table',3),(10,'2025_08_10_133121_add_is_admin_to_users_table',3),(11,'2025_08_14_074229_add_missing_fields_to_services_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `packages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
INSERT INTO `packages` VALUES (1,'الباقة الذهبية',15000.00,'الباقة الأكثر شمولية وفخامة لمناسباتكم الخاصة. تشمل جميع الخدمات المتميزة مع أعلى مستويات الجودة.','[\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0648\\u062a\\u0632\\u064a\\u064a\\u0646 \\u0627\\u0644\\u0645\\u0643\\u0627\\u0646 \\u0628\\u0627\\u0644\\u0643\\u0627\\u0645\\u0644\",\"\\u062e\\u062f\\u0645\\u0629 \\u0636\\u064a\\u0627\\u0641\\u0629 VIP \\u0645\\u0639 \\u0623\\u0641\\u0636\\u0644 \\u0623\\u0646\\u0648\\u0627\\u0639 \\u0627\\u0644\\u0637\\u0639\\u0627\\u0645\",\"\\u0646\\u0638\\u0627\\u0645 \\u0635\\u0648\\u062a\\u064a \\u0648\\u0645\\u0631\\u0626\\u064a \\u0645\\u062a\\u0637\\u0648\\u0631\",\"\\u062a\\u0635\\u0648\\u064a\\u0631 \\u0641\\u0648\\u062a\\u0648\\u063a\\u0631\\u0627\\u0641\\u064a \\u0648\\u0641\\u064a\\u062f\\u064a\\u0648\\u062c\\u0631\\u0627\\u0641\\u064a \\u0627\\u062d\\u062a\\u0631\\u0627\\u0641\\u064a\",\"\\u0645\\u0646\\u0633\\u0642 \\u0634\\u062e\\u0635\\u064a \\u0645\\u062e\\u0635\\u0635\",\"\\u062e\\u062f\\u0645\\u0629 \\u0627\\u0633\\u062a\\u0642\\u0628\\u0627\\u0644 \\u0648\\u062a\\u0648\\u062f\\u064a\\u0639 \\u0627\\u0644\\u0636\\u064a\\u0648\\u0641\",\"\\u062a\\u0646\\u0638\\u064a\\u0641 \\u0627\\u0644\\u0645\\u0643\\u0627\\u0646 \\u0628\\u0639\\u062f \\u0627\\u0644\\u062d\\u0641\\u0644\"]',NULL,1,'2025-08-09 18:41:21','2025-08-09 18:41:21'),(2,'الباقة الفضية',10000.00,'باقة متوسطة تجمع بين الجودة والسعر المناسب. مثالية للمناسبات المتوسطة التي تتطلب خدمات عالية الجودة.','[\"\\u062a\\u0632\\u064a\\u064a\\u0646 \\u0623\\u0633\\u0627\\u0633\\u064a \\u0644\\u0644\\u0645\\u0643\\u0627\\u0646\",\"\\u062e\\u062f\\u0645\\u0629 \\u0637\\u0639\\u0627\\u0645 \\u0648\\u0634\\u0631\\u0627\\u0628 \\u0645\\u062a\\u0646\\u0648\\u0639\\u0629\",\"\\u0646\\u0638\\u0627\\u0645 \\u0635\\u0648\\u062a\\u064a \\u0623\\u0633\\u0627\\u0633\\u064a\",\"\\u062a\\u0635\\u0648\\u064a\\u0631 \\u0641\\u0648\\u062a\\u0648\\u063a\\u0631\\u0627\\u0641\\u064a\",\"\\u0645\\u0646\\u0633\\u0642 \\u0644\\u0644\\u0641\\u0639\\u0627\\u0644\\u064a\\u0629\",\"\\u062e\\u062f\\u0645\\u0629 \\u062a\\u0646\\u0638\\u064a\\u0641 \\u0623\\u0633\\u0627\\u0633\\u064a\\u0629\"]',NULL,1,'2025-08-09 18:41:21','2025-08-09 18:41:21'),(3,'الباقة البرونزية',6000.00,'باقة اقتصادية مناسبة للمناسبات الصغيرة والمتوسطة. تقدم الخدمات الأساسية بجودة ممتازة.','[\"\\u062a\\u0632\\u064a\\u064a\\u0646 \\u0628\\u0633\\u064a\\u0637 \\u0648\\u0645\\u0646\\u0627\\u0633\\u0628\",\"\\u062e\\u062f\\u0645\\u0629 \\u0637\\u0639\\u0627\\u0645 \\u0623\\u0633\\u0627\\u0633\\u064a\\u0629\",\"\\u0646\\u0638\\u0627\\u0645 \\u0635\\u0648\\u062a\\u064a \\u0628\\u0633\\u064a\\u0637\",\"\\u0645\\u0646\\u0633\\u0642 \\u0644\\u0644\\u0625\\u0634\\u0631\\u0627\\u0641\",\"\\u062e\\u062f\\u0645\\u0629 \\u062a\\u0646\\u0638\\u064a\\u0641\"]',NULL,1,'2025-08-09 18:41:21','2025-08-09 18:41:21');
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `comment` text NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,'سارة العتيبي',5,'خدمة ممتازة وتنظيم رائع! فريق Your Events جعل حفل زفافي حلماً يتحقق. كل التفاصيل كانت مثالية والتنسيق كان في غاية الروعة.',1,'2025-08-09 18:41:21','2025-08-09 18:41:21'),(2,'محمد الأحمد',5,'تجربة مميزة جداً في تنظيم مؤتمر شركتنا السنوي. الفريق محترف ومتفهم لاحتياجاتنا. أنصح بهم بشدة.',1,'2025-08-09 18:41:21','2025-08-09 18:41:21'),(3,'نورا القحطاني',4,'نظموا حفل تخرج ابنتي بشكل رائع. الديكور كان جميل والتنظيم ممتاز. شكراً لكم على هذه التجربة الرائعة.',1,'2025-08-09 18:41:21','2025-08-09 18:41:21');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (2,'Food Truck Station','تنظيم جميع أنواع المناسبات الاجتماعية مثل حفلات التخرج، أعياد الميلاد، الذكريات السنوية، والتجمعات العائلية. نضمن لكم تجربة ممتعة ومميزة.',1500.00,'4 ساعات','ترفيه','[null]','services/KyFQ0ZEliucc94EDdQR32emlsDPgmkL0tvUac40N.jpg',1,'2025-08-09 18:41:21','2025-08-18 00:03:16'),(3,'المؤتمرات والفعاليات','خدمات تنظيم المؤتمرات والفعاليات المهنية بمعايير عالمية. نوفر كافة التجهيزات التقنية والإعلامية المطلوبة لضمان نجاح فعاليتكم.',NULL,NULL,NULL,NULL,NULL,1,'2025-08-09 18:41:21','2025-08-09 18:41:21'),(4,'حفلات الأطفال','تنظيم حفلات أطفال مبهجة وآمنة مع ألعاب وأنشطة ترفيهية متنوعة. نهتم بتوفير بيئة ممتعة وآمنة للأطفال مع برامج ترفيهية مناسبة لأعمارهم.',NULL,NULL,NULL,NULL,NULL,1,'2025-08-09 18:41:21','2025-08-09 18:41:21'),(5,'المعارض التجارية','تصميم وتنظيم المعارض التجارية والترويجية. نقدم حلول شاملة تشمل التصميم، البناء، والإدارة لضمان نجاح معرضكم التجاري.',NULL,NULL,NULL,NULL,NULL,1,'2025-08-09 18:41:21','2025-08-09 18:41:21'),(6,'الحفلات الخيرية','تنظيم الحفلات والفعاليات الخيرية بروح إنسانية عالية. نساعدكم في تحقيق أهدافكم الخيرية من خلال فعاليات منظمة ومؤثرة.',NULL,NULL,NULL,NULL,NULL,1,'2025-08-09 18:41:21','2025-08-09 18:41:21');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('15oY68F2ICX7iKc144yMMVNCpDMYpRQjixrUWfVx',NULL,'192.168.1.152','curl/7.81.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjhqVWlpTEt0bmZYekE5bU9WcGNZcVBmbGZ0YUZRaFFadHdvMndnSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xOTIuMTY4LjEuMTUyOjgwOTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1755533924),('8cCphFt0uMvB8BA6wqmS5gQqC1t2iAnxpGgf0ntr',1,'192.168.1.25','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaGt5amFTTUtFdGJNM0oxQXlacGFVdFdpNmNiVWY4Uk9xSHE5T2pQeiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xOTIuMTY4LjEuMTUyOjgwOTAvYWRtaW4vc2VydmljZXMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1755473724),('Bo0soA0twuEO0a8BCxMbaUldlm14Q2EQP6wyjxyW',NULL,'192.168.1.152','curl/7.81.0','YToyOntzOjY6Il90b2tlbiI7czo0MDoidThGUkVTc3ZKN05OaTBGWHhsc1JZSnRxOExqQUpWY1JCOUF4NGowcSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1755472592),('ifxvnXdqf80tr3Mjy0wxbb4dX8dgdQrrWSUmsSPP',NULL,'192.168.1.152','curl/7.81.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVU1LMzA4SUF2VUM5MkVJbGtzVFkxRXVSVHEwYnBBZm9HRkpBNEwyMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xOTIuMTY4LjEuMTUyOjgwOTAvcGFja2FnZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1755533924),('KiW7Ktu9DaowXjJIsK9gX71AxbSF5kxExBEXzJRl',NULL,'192.168.1.152','curl/7.81.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmxzZHY2YlVNTkJVcDlMNHNZdERibDB1dmhnMUgwcUxoOU5kb3pGQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly8xOTIuMTY4LjEuMTUyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1755472592),('oP6qCSvWYnyuelRz6zLfIrE2tuiaSDTwh9fGKcWD',NULL,'100.91.242.161','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoic2x5NHc2VHBOZGx5M3gxTlBvQk91U1dvSTlGcUNiRzRsaDA2eWVMcCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9lbXMtdGVjaC5mcmVlZGRucy5vcmc6ODA5MCI7fX0=',1755476056),('Tz8t8lyTvIqqGNftxBTnU1mfpHrrQzilNujVeOzt',NULL,'192.168.1.152','curl/7.81.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiREl6bXVlMWlVSGVTWXp6MVduYkdrSVNjTnBmdVIzOE5sOVhHN0lWdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xOTIuMTY4LjEuMTUyOjgwOTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1755534036),('xSKUmPtbbNbcKTY5D8jDngLgMHydEe3Oaz2erJuS',NULL,'192.168.1.152','curl/7.81.0','YToyOntzOjY6Il90b2tlbiI7czo0MDoiYms1UUJFckFydWRpN05mcWYxT2E3VzhpNHdkbUs1akNpWXFoY1BGVSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1755532380);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'مدير الموقع','admin@yourevents.com','2025-08-09 18:41:21','$2y$12$8vUOhkI3JeV206//vvURV.vfpAZdoEOC155RQypfP9dcgU2gz/P96','+966501234567','admin',0,'8zVMaDB0qIt9IJgTXb59yn3YCZxuJzeyKI63xjYI7bTxgxBmq3fIPgapqsFw','2025-08-09 18:41:21','2025-08-15 09:20:07'),(2,'أحمد محمد','user@example.com','2025-08-09 18:41:21','$2y$12$OMWsrAams5MM6Gvt08qdHeDJumpLPKV2kmYr/cNnUBbnfb1HwNBy6','+966509876543','user',0,NULL,'2025-08-09 18:41:21','2025-08-09 18:41:21');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'your_events'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-18 16:52:28

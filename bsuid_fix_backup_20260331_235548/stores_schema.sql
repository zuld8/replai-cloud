-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: whatsmail
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

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
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stores` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bsuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wa_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `history_chat_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `pemilik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `prospek` enum('pending','yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `scrapping_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_group_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_account_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int NOT NULL DEFAULT '0',
  `jid_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `stores_id_unique` (`id`),
  KEY `stores_category_id_index` (`category_id`),
  KEY `stores_district_id_index` (`district_id`),
  KEY `stores_scrapping_id_index` (`scrapping_id`),
  KEY `stores_history_chat_id_index` (`history_chat_id`),
  KEY `idx_scrapping_created` (`scrapping_id`,`created_at`),
  KEY `idx_store_phone_biz` (`phone`,`business_id`),
  KEY `idx_store_label` (`label_id`),
  KEY `idx_stores_bsuid` (`bsuid`),
  KEY `stores_meta_account_id_index` (`meta_account_id`),
  KEY `idx_stores_biz_name` (`business_id`,`name`(50)),
  KEY `idx_stores_biz_waba` (`business_id`,`meta_account_id`),
  FULLTEXT KEY `idx_stores_name_fulltext` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-31 18:55:50

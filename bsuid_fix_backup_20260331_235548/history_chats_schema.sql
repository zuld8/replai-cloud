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
-- Table structure for table `history_chats`
--

DROP TABLE IF EXISTS `history_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history_chats` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `livechat_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_waba_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `messanger_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('personal','group') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'personal',
  `from_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bsuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wa_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jid_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `from` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'whatsapp',
  `status` enum('resolved','open','block','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `label` text COLLATE utf8mb4_unicode_ci,
  `note` text COLLATE utf8mb4_unicode_ci,
  `handled_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collabolator` text COLLATE utf8mb4_unicode_ci,
  `resolved_by_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_data` longtext COLLATE utf8mb4_unicode_ci,
  `assigned_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assignment_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `takeover` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_message_at` datetime DEFAULT NULL,
  `unread_count` int unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `history_chats_id_unique` (`id`),
  KEY `history_chats_device_id_index` (`device_id`),
  KEY `history_chats_merchant_id_index` (`merchant_id`),
  KEY `history_chats_livechat_id_index` (`livechat_id`),
  KEY `history_chats_handled_by_index` (`handled_by`),
  KEY `history_chats_assigned_by_index` (`assigned_by`),
  KEY `idx_business_id` (`business_id`),
  KEY `idx_status` (`status`),
  KEY `idx_business_status` (`business_id`,`status`),
  KEY `idx_updated_at` (`updated_at`),
  KEY `idx_last_message_at` (`last_message_at`),
  KEY `idx_status_last_msg` (`status`,`last_message_at`),
  KEY `idx_unread_count` (`unread_count`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_crm_main` (`business_id`,`status`,`takeover`,`last_message_at` DESC),
  KEY `idx_hc_from` (`from`,`business_id`,`status`),
  KEY `idx_waba_id` (`whatsapp_waba_id`),
  KEY `idx_store_id` (`store_id`),
  KEY `idx_status_created` (`status`,`created_at`),
  KEY `idx_hc_bsuid` (`bsuid`),
  KEY `idx_hc_unread_status_lastmsg` (`unread_count`,`status`,`last_message_at`),
  FULLTEXT KEY `ft_name_phone` (`name`,`from_number`)
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

-- Dump completed on 2026-03-31 18:55:49

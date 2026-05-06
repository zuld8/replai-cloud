-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: whatsmail
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
-- Table structure for table `chat_bots`
--

DROP TABLE IF EXISTS `chat_bots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_bots` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_account_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `select_device` text COLLATE utf8mb4_unicode_ci,
  `select_telegram` text COLLATE utf8mb4_unicode_ci,
  `select_livechat` text COLLATE utf8mb4_unicode_ci,
  `reply_method` enum('text','template','image') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `merchant_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_waba_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `select_instagram` text COLLATE utf8mb4_unicode_ci,
  `select_messanger` text COLLATE utf8mb4_unicode_ci,
  `select_waba` text COLLATE utf8mb4_unicode_ci,
  `metadata` longtext COLLATE utf8mb4_unicode_ci,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `chat_bots_id_unique` (`id`),
  KEY `chat_bots_template_id_index` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_bots`
--

LOCK TABLES `chat_bots` WRITE;
/*!40000 ALTER TABLE `chat_bots` DISABLE KEYS */;
INSERT INTO `chat_bots` VALUES ('4661d03d-4071-428d-98d8-272f6bd5696d','Join Grup Nurul Ashri','a6a6ffea-0d4a-4b9c-a540-617ece17bf08','9e192a4c-c39a-4357-b536-5b53979a81b9',NULL,NULL,'text',NULL,'Untuk mendapatkan semua informasi kegiatan di Masjid Nurul Ashri, kamu bisa bergabung ke dalam grup berikut:\nhttps://chat.whatsapp.com/GaLNZqJ1AHq8Yu9v9ruJ3D','6b4707e9-75d2-4db9-ab86-724bd213fb3f','24c41c11-bcf9-48b3-9b71-3fc2041c1188',NULL,NULL,NULL,NULL,'{\"header\":{\"format\":\"TEXT\",\"text\":\"\"},\"body\":{\"text\":\"Untuk mendapatkan semua informasi kegiatan di Masjid Nurul Ashri, kamu bisa bergabung ke dalam grup berikut:\\nhttps://chat.whatsapp.com/GaLNZqJ1AHq8Yu9v9ruJ3D\",\"type\":\"\",\"parameters\":[]},\"footer\":[],\"buttons\":[],\"media\":null}','','2026-02-26 14:00:57','2026-02-26 15:00:58'),('95958e3a-d6ba-48be-9a1d-6b4bae65fc61','Ikut Sedekah Jum\'at','a6a6ffea-0d4a-4b9c-a540-617ece17bf08','9e192a4c-c39a-4357-b536-5b53979a81b9',NULL,NULL,'text',NULL,'Kamu bisa ikut sedekah mulai dari Rp 2.000 untuk semua program Ramadhan melalui rekening a.n Yayasan Nurul Ashri Deresan Yogyakarta:\n💳BSI 71331-80723\n💳BNI 6660-000562\n💳BRI 21640-1000459560\n📌 Kode Program Ramadhan : 333\nContoh : Rp2.333','6b4707e9-75d2-4db9-ab86-724bd213fb3f','24c41c11-bcf9-48b3-9b71-3fc2041c1188',NULL,NULL,NULL,NULL,'{\"header\":{\"format\":\"TEXT\",\"text\":\"\"},\"body\":{\"text\":\"Kamu bisa ikut sedekah mulai dari Rp 2.000 untuk semua program Ramadhan melalui rekening a.n Yayasan Nurul Ashri Deresan Yogyakarta:\\n💳BSI 71331-80723\\n💳BNI 6660-000562\\n💳BRI 21640-1000459560\\n📌 Kode Program Ramadhan : 333\\nContoh : Rp2.333\",\"type\":\"\",\"parameters\":[]},\"footer\":{},\"buttons\":[],\"media\":null}','','2026-02-26 13:59:23','2026-02-26 13:59:23'),('c9526f60-6e4e-461b-87b5-6db3a43420e0','Rekening','0fc856f3-e183-4b19-ad84-ef501dfc9f69','6f49d96c-19c9-440d-a427-41490b564d5d',NULL,NULL,'text',NULL,'(BSI) 8007557559 \nMandiri 1300023575833 \nBNI  2227771664 \nBCA 1569092777 \nBRI 040701001615561 \nan Yayasan Amal Produktif Indonesia','88dad5af-a281-4298-a43b-d4131cc13679','77c51fa3-9aee-4168-939b-0aa1d76dd585',NULL,NULL,NULL,NULL,'{\"header\":{\"format\":\"TEXT\",\"text\":\"\"},\"body\":{\"text\":\"(BSI) 8007557559 \\nMandiri 1300023575833 \\nBNI  2227771664 \\nBCA 1569092777 \\nBRI 040701001615561 \\nan Yayasan Amal Produktif Indonesia\",\"type\":\"\",\"parameters\":[]},\"footer\":{},\"buttons\":[],\"media\":null}','','2025-10-31 14:50:06','2025-10-31 14:50:06');
/*!40000 ALTER TABLE `chat_bots` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-06 19:32:44

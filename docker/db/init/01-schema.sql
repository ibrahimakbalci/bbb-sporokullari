-- Boş veritabanı şeması (veri içermez). Gerçek döküm yoksa dev ortamında ilk açılışta yüklenir.
-- Gerçek dökümü kullanmak için .env içinde DB_INIT_SQL=./dosya.sql verin.

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
DROP TABLE IF EXISTS `basvuru_basvuru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `basvuru_basvuru` (
  `basvuru_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `basvuru_tckimlik` varchar(15) NOT NULL DEFAULT '',
  `basvuru_dtarih` date DEFAULT NULL,
  `basvuru_adsoyad_sporcu` varchar(50) DEFAULT NULL,
  `basvuru_telefon_cep` varchar(30) DEFAULT NULL,
  `basvuru_odeme_turu` varchar(20) NOT NULL,
  `basvuru_odenen_tutar` varchar(50) NOT NULL,
  `basvuru_aciklama` text DEFAULT NULL,
  `basvuru_zamani` datetime DEFAULT NULL,
  `uye_id` int(11) DEFAULT NULL,
  `brans_id` int(11) DEFAULT NULL,
  `tesis_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`basvuru_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `basvuru_branslar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `basvuru_branslar` (
  `brans_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `brans_isim` varchar(50) DEFAULT NULL,
  `yas_id` int(11) DEFAULT NULL,
  `brans_ucret` decimal(10,2) DEFAULT NULL,
  `brans_kontenjan` int(11) DEFAULT NULL,
  `tesis_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`brans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `basvuru_tesisler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `basvuru_tesisler` (
  `tesis_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tesis_isim` varchar(50) DEFAULT NULL,
  `tesis_yetkili` text DEFAULT 'a:0:{}',
  PRIMARY KEY (`tesis_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `basvuru_uyeler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `basvuru_uyeler` (
  `uye_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uye_email` varchar(50) DEFAULT NULL,
  `uye_sifre` varchar(255) DEFAULT NULL,
  `uye_adsoyad` varchar(50) DEFAULT NULL,
  `uye_yetki` text DEFAULT NULL,
  `uye_admin` varchar(10) DEFAULT 'uye' COMMENT 'admin, uye',
  PRIMARY KEY (`uye_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `basvuru_yas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `basvuru_yas` (
  `yas_id` int(11) NOT NULL AUTO_INCREMENT,
  `yas_isim` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`yas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.17-0ubuntu0.16.04.1 - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             9.4.0.5159
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for zadanie_1
CREATE DATABASE IF NOT EXISTS `zadanie_1` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `zadanie_1`;

-- Dumping structure for table zadanie_1.absence
CREATE TABLE IF NOT EXISTS `absence` (
  `id_abs` int(11) NOT NULL AUTO_INCREMENT,
  `id_employee` int(11) NOT NULL DEFAULT '0',
  `id_type` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id_abs`),
  KEY `id_employee` (`id_employee`),
  KEY `id_type` (`id_type`),
  CONSTRAINT `id_employee` FOREIGN KEY (`id_employee`) REFERENCES `employees` (`id_employee`),
  CONSTRAINT `id_type` FOREIGN KEY (`id_type`) REFERENCES `absence_type` (`id_type`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- Dumping data for table zadanie_1.absence: ~0 rows (approximately)
/*!40000 ALTER TABLE `absence` DISABLE KEYS */;
REPLACE INTO `absence` (`id_abs`, `id_employee`, `id_type`, `date`) VALUES
	(20, 1, 3, '2017-02-16'),
	(21, 2, 1, '2017-03-13'),
	(22, 3, 3, '2017-02-20'),
	(23, 3, 3, '2017-02-16'),
	(24, 4, 3, '2017-02-08');
/*!40000 ALTER TABLE `absence` ENABLE KEYS */;

-- Dumping structure for table zadanie_1.absence_type
CREATE TABLE IF NOT EXISTS `absence_type` (
  `id_type` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table zadanie_1.absence_type: ~4 rows (approximately)
/*!40000 ALTER TABLE `absence_type` DISABLE KEYS */;
REPLACE INTO `absence_type` (`id_type`, `type`, `color`) VALUES
	(1, 'PN', 'red'),
	(2, 'OCR', 'orange'),
	(3, 'DOV', 'green'),
	(4, 'PLAN_DOV', 'yellow');
/*!40000 ALTER TABLE `absence_type` ENABLE KEYS */;

-- Dumping structure for table zadanie_1.employees
CREATE TABLE IF NOT EXISTS `employees` (
  `id_employee` int(11) NOT NULL AUTO_INCREMENT,
  `meno` tinytext,
  `priezvisko` tinytext,
  PRIMARY KEY (`id_employee`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table zadanie_1.employees: ~4 rows (approximately)
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
REPLACE INTO `employees` (`id_employee`, `meno`, `priezvisko`) VALUES
	(1, 'Juraj', 'K'),
	(2, 'Barbora', 'J'),
	(3, 'Jozef', 'G'),
	(4, 'Alena', 'A');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

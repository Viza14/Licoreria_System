-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: licoreria
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
-- Current Database: `licoreria`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `licoreria` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `licoreria`;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `id_tipo_categoria` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_tipo_categoria` (`id_tipo_categoria`),
  KEY `id_estatus` (`id_estatus`),
  CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`id_tipo_categoria`) REFERENCES `tipos_categoria` (`id`),
  CONSTRAINT `categorias_ibfk_2` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Ron Añejo',5,1),(2,'Ron Blanco',5,1),(3,'Whisky Escocés',4,1),(4,'Whisky Irlandés',4,1),(5,'Whisky Americano',4,1),(6,'Vodka Premium',6,1),(7,'Vodka Regular',6,1),(8,'Tequila Blanco',7,1),(9,'Tequila Reposado',7,1),(10,'Tequila Añejo',7,1),(11,'Cerveza Nacional',2,1),(12,'Cerveza Importada',2,1),(13,'Vino Tinto',3,1),(14,'Vino Blanco',3,1),(15,'Vino Rosado',3,1),(16,'Champagne',3,1),(17,'Licores de Frutas',1,1),(18,'Licores de Crema',1,1),(19,'Aguardiente',8,1),(20,'Sidra',8,1);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `cedula` varchar(15) NOT NULL,
  `id_simbolo_cedula` int(11) DEFAULT 1,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(255) DEFAULT 'Sin especificar',
  `id_estatus` int(11) NOT NULL,
  PRIMARY KEY (`cedula`),
  KEY `id_estatus` (`id_estatus`),
  KEY `clientes_ibfk_simbolo` (`id_simbolo_cedula`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`),
  CONSTRAINT `clientes_ibfk_simbolo` FOREIGN KEY (`id_simbolo_cedula`) REFERENCES `simbolos_cedula` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES ('1122334',1,'Pedro','García','04221234567','Av. 6, Urb. La Alegría, Valencia',1),('1234567',1,'María','González','04241234567','Av. 2, Res. El Bosque, Valencia',1),('1357924',1,'Ana','Martínez','04261234567','Av. 4, Urb. Santa Fe, Valencia',1),('2233445',1,'Laura','López','04131234567','Calle 7, Urb. Los Guayos, Valencia',1),('2468135',1,'Luis','Hernández','04121234567','Calle 5, Urb. Trigal Norte, Valencia',1),('3344556',1,'José','Sánchez','04231234567','Av. 8, Urb. La Isabelica, Valencia',1),('4455667',1,'Marta','Ramírez','04151234567','Calle 9, Urb. La Viña, Valencia',1),('5566778',1,'Ricardo','Torres','04251234567','Av. 10, Urb. Los Colorados, Valencia',1),('7654321',1,'Carlos','Rodríguez','04161234567','Calle 3, Urb. Prebo, Valencia',1),('9876543',1,'Juan','Pérez','04141234567','Calle 1, Urb. Las Acacias, Valencia',1);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_venta`
--

DROP TABLE IF EXISTS `detalle_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_venta` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_venta` int(255) NOT NULL,
  `id_producto` int(10) NOT NULL,
  `cantidad` int(10) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_venta` (`id_venta`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`),
  CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_venta`
--

LOCK TABLES `detalle_venta` WRITE;
/*!40000 ALTER TABLE `detalle_venta` DISABLE KEYS */;
INSERT INTO `detalle_venta` VALUES (1,1,1,2,45.00,90.00),(2,2,6,1,65.00,65.00),(3,3,2,1,30.00,30.00),(4,4,23,1,120.00,120.00),(5,5,25,1,80.00,80.00),(6,6,3,1,40.00,40.00),(7,6,14,2,2.50,5.00),(8,7,7,1,60.00,60.00),(9,8,12,1,35.00,35.00),(10,9,8,1,55.00,55.00),(11,10,13,1,70.00,70.00),(12,11,10,1,40.00,40.00),(13,12,9,1,50.00,50.00),(14,12,15,1,3.00,3.00),(15,12,16,1,4.50,4.50),(16,13,11,1,25.00,25.00),(17,14,5,1,18.00,18.00),(18,15,4,1,15.00,15.00),(19,16,14,1,2.50,2.50),(20,16,4,1,15.00,15.00),(21,17,14,1,2.50,2.50),(22,17,4,1,15.00,15.00);
/*!40000 ALTER TABLE `detalle_venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estatus`
--

DROP TABLE IF EXISTS `estatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estatus`
--

LOCK TABLES `estatus` WRITE;
/*!40000 ALTER TABLE `estatus` DISABLE KEYS */;
INSERT INTO `estatus` VALUES (1,'Activo'),(2,'Inactivo');
/*!40000 ALTER TABLE `estatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos_inventario`
--

DROP TABLE IF EXISTS `movimientos_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_transaccion` varchar(20) DEFAULT NULL,
  `id_producto` int(10) NOT NULL,
  `tipo_movimiento` enum('ENTRADA','SALIDA','AJUSTE') NOT NULL,
  `subtipo_movimiento` enum('COMPRA','VENTA','PERDIDA','AJUSTE','OTRO') DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `id_referencia` int(11) DEFAULT NULL COMMENT 'ID de la tabla referencia (venta, compra, etc)',
  `tipo_referencia` varchar(50) DEFAULT NULL COMMENT 'Tipo de referencia (VENTA, COMPRA, etc)',
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(10) NOT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `id_estatus` int(11) NOT NULL DEFAULT 1,
  `id_movimiento_original` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_producto` (`id_producto`),
  KEY `id_usuario` (`id_usuario`),
  KEY `fk_movimientos_estatus` (`id_estatus`),
  KEY `fk_movimientos_original` (`id_movimiento_original`),
  KEY `idx_numero_transaccion` (`numero_transaccion`),
  CONSTRAINT `fk_movimientos_estatus` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`),
  CONSTRAINT `fk_movimientos_original` FOREIGN KEY (`id_movimiento_original`) REFERENCES `movimientos_inventario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos_inventario`
--

LOCK TABLES `movimientos_inventario` WRITE;
/*!40000 ALTER TABLE `movimientos_inventario` DISABLE KEYS */;
INSERT INTO `movimientos_inventario` VALUES (1,'TXN00000001',1,'ENTRADA','COMPRA',50,35.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(2,'TXN00000002',2,'ENTRADA','COMPRA',75,22.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(3,'TXN00000003',3,'ENTRADA','COMPRA',60,30.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(4,'TXN00000004',4,'ENTRADA','COMPRA',120,10.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(5,'TXN00000005',5,'ENTRADA','COMPRA',90,12.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(6,'TXN00000006',6,'ENTRADA','COMPRA',40,50.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(7,'TXN00000007',7,'ENTRADA','COMPRA',35,45.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(8,'TXN00000008',8,'ENTRADA','COMPRA',30,40.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(9,'TXN00000009',9,'ENTRADA','COMPRA',45,35.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(10,'TXN00000010',10,'ENTRADA','COMPRA',55,30.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(11,'TXN00000011',11,'ENTRADA','COMPRA',70,18.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(12,'TXN00000012',12,'ENTRADA','COMPRA',65,25.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(13,'TXN00000013',13,'ENTRADA','COMPRA',25,50.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(14,'TXN00000014',14,'ENTRADA','COMPRA',200,1.80,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(15,'TXN00000015',15,'ENTRADA','COMPRA',150,2.20,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(16,'TXN00000016',16,'ENTRADA','COMPRA',100,3.50,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(17,'TXN00000017',17,'ENTRADA','COMPRA',90,3.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(18,'TXN00000018',18,'ENTRADA','COMPRA',80,5.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(19,'TXN00000019',19,'ENTRADA','COMPRA',70,6.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(20,'TXN00000020',20,'ENTRADA','COMPRA',30,25.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(21,'TXN00000021',21,'ENTRADA','COMPRA',40,15.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(22,'TXN00000022',22,'ENTRADA','COMPRA',50,8.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(23,'TXN00000023',23,'ENTRADA','COMPRA',15,90.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(24,'TXN00000024',24,'ENTRADA','COMPRA',10,85.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(25,'TXN00000025',25,'ENTRADA','COMPRA',20,60.00,NULL,NULL,'2025-07-26 23:53:24',1,'Compra inicial de stock',1,NULL,NULL),(26,'TXN00000026',1,'SALIDA','VENTA',2,45.00,1,'VENTA','2025-07-26 23:53:24',2,'Venta a cliente 9876543',1,NULL,NULL),(27,'TXN00000027',6,'SALIDA','VENTA',1,65.00,2,'VENTA','2025-07-26 23:53:24',2,'Venta a cliente 1234567',1,NULL,NULL),(28,'TXN00000028',2,'SALIDA','VENTA',1,30.00,3,'VENTA','2025-07-26 23:53:24',3,'Venta a cliente 7654321',1,NULL,NULL),(29,'TXN00000029',23,'SALIDA','VENTA',1,120.00,4,'VENTA','2025-07-26 23:53:24',3,'Venta a cliente 1357924',1,NULL,NULL),(30,'TXN00000030',25,'SALIDA','VENTA',1,80.00,5,'VENTA','2025-07-26 23:53:24',4,'Venta a cliente 2468135',1,NULL,NULL),(31,'TXN00000031',3,'SALIDA','VENTA',1,40.00,6,'VENTA','2025-07-26 23:53:24',4,'Venta a cliente 1122334',1,NULL,NULL),(32,'TXN00000032',14,'SALIDA','VENTA',2,2.50,6,'VENTA','2025-07-26 23:53:24',4,'Venta a cliente 1122334',1,NULL,NULL),(33,'TXN00000033',7,'SALIDA','VENTA',1,60.00,7,'VENTA','2025-07-26 23:53:24',2,'Venta a cliente 2233445',1,NULL,NULL),(34,'TXN00000034',12,'SALIDA','VENTA',1,35.00,8,'VENTA','2025-07-26 23:53:24',3,'Venta a cliente 3344556',1,NULL,NULL),(35,'TXN00000035',8,'SALIDA','VENTA',1,55.00,9,'VENTA','2025-07-26 23:53:24',4,'Venta a cliente 4455667',1,NULL,NULL),(36,'TXN00000036',13,'SALIDA','VENTA',1,70.00,10,'VENTA','2025-07-26 23:53:24',2,'Venta a cliente 5566778',1,NULL,NULL),(37,'TXN00000037',10,'SALIDA','VENTA',1,40.00,11,'VENTA','2025-07-26 23:53:24',3,'Venta a cliente 9876543',1,NULL,NULL),(38,'TXN00000038',9,'SALIDA','VENTA',1,50.00,12,'VENTA','2025-07-26 23:53:24',4,'Venta a cliente 1234567',1,NULL,NULL),(39,'TXN00000039',15,'SALIDA','VENTA',1,3.00,12,'VENTA','2025-07-26 23:53:24',4,'Venta a cliente 1234567',1,NULL,NULL),(40,'TXN00000040',16,'SALIDA','VENTA',1,4.50,12,'VENTA','2025-07-26 23:53:24',4,'Venta a cliente 1234567',1,NULL,NULL),(41,'TXN00000041',11,'SALIDA','VENTA',1,25.00,13,'VENTA','2025-07-26 23:53:24',2,'Venta a cliente 7654321',1,NULL,NULL),(42,'TXN00000042',5,'SALIDA','VENTA',1,18.00,14,'VENTA','2025-07-26 23:53:24',3,'Venta a cliente 1357924',1,NULL,NULL),(43,'TXN00000043',4,'SALIDA','VENTA',1,15.00,15,'VENTA','2025-07-26 23:53:24',4,'Venta a cliente 2468135',1,NULL,NULL),(44,'TXN00000044',14,'SALIDA','VENTA',1,2.50,16,'VENTA','2025-07-28 00:27:26',1,'Venta #16 - TXN00000044',1,NULL,NULL),(45,'TXN00000044',4,'SALIDA','VENTA',1,15.00,16,'VENTA','2025-07-28 00:27:26',1,'Venta #16 - TXN00000044',1,NULL,NULL),(46,'TXN00000045',14,'SALIDA','VENTA',1,2.50,17,'VENTA','2025-07-28 00:32:10',1,'Venta #17 - TXN00000045',1,NULL,NULL),(47,'TXN00000045',4,'SALIDA','VENTA',1,15.00,17,'VENTA','2025-07-28 00:32:10',1,'Venta #17 - TXN00000045',1,NULL,NULL);
/*!40000 ALTER TABLE `movimientos_inventario` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_movimiento_insert_numero` 

BEFORE INSERT ON `movimientos_inventario` 

FOR EACH ROW 

BEGIN

    IF NEW.numero_transaccion IS NULL THEN

        SET NEW.numero_transaccion = GenerarNumeroTransaccion();

    END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `pagos_venta`
--

DROP TABLE IF EXISTS `pagos_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos_venta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(255) NOT NULL,
  `forma_pago` enum('EFECTIVO','TARJETA','PAGO_MOVIL') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `referencia_pago` varchar(6) DEFAULT NULL,
  `fecha_pago` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pagos_venta_venta` (`id_venta`),
  CONSTRAINT `fk_pagos_venta_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_venta`
--

LOCK TABLES `pagos_venta` WRITE;
/*!40000 ALTER TABLE `pagos_venta` DISABLE KEYS */;
INSERT INTO `pagos_venta` VALUES (1,1,'EFECTIVO',90.00,NULL,'2025-07-01 10:15:00'),(2,2,'TARJETA',65.00,'123456','2025-07-01 11:30:00'),(3,3,'EFECTIVO',30.00,NULL,'2025-07-02 12:45:00'),(4,4,'TARJETA',120.00,'654321','2025-07-02 14:20:00'),(5,5,'PAGO_MOVIL',80.00,'789012','2025-07-03 16:10:00'),(6,6,'EFECTIVO',45.00,NULL,'2025-07-03 17:30:00'),(7,7,'TARJETA',60.00,'345678','2025-07-04 18:45:00'),(8,8,'EFECTIVO',35.00,NULL,'2025-07-05 10:00:00'),(9,9,'PAGO_MOVIL',50.00,'901234','2025-07-05 11:15:00'),(10,10,'TARJETA',70.00,'567890','2025-07-06 12:30:00'),(11,11,'EFECTIVO',40.00,NULL,'2025-07-06 14:45:00'),(12,12,'EFECTIVO',55.00,NULL,'2025-07-07 16:00:00'),(13,13,'PAGO_MOVIL',25.00,'234567','2025-07-08 17:15:00'),(14,14,'EFECTIVO',18.00,NULL,'2025-07-09 18:30:00'),(15,15,'TARJETA',15.00,'890123','2025-07-10 10:45:00'),(16,16,'EFECTIVO',17.50,NULL,'2025-07-28 00:27:26'),(17,17,'EFECTIVO',17.50,NULL,'2025-07-28 00:32:10');
/*!40000 ALTER TABLE `pagos_venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perdidas`
--

DROP TABLE IF EXISTS `perdidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perdidas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(10) NOT NULL,
  `cantidad` int(255) NOT NULL,
  `id_usuario` int(10) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `id_estatus` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_producto` (`id_producto`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_estatus` (`id_estatus`),
  CONSTRAINT `perdidas_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  CONSTRAINT `perdidas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `perdidas_ibfk_3` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perdidas`
--

LOCK TABLES `perdidas` WRITE;
/*!40000 ALTER TABLE `perdidas` DISABLE KEYS */;
/*!40000 ALTER TABLE `perdidas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producto` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` int(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_categoria` (`id_categoria`),
  KEY `id_estatus` (`id_estatus`),
  KEY `idx_producto_descripcion` (`descripcion`),
  CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`),
  CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
INSERT INTO `producto` VALUES (1,'Ron Santa Teresa 1796',50,45.00,1,1),(2,'Ron Pampero Aniversario',75,30.00,1,1),(3,'Ron Diplomatico Reserva',60,40.00,1,1),(4,'Ron Cacique 500',118,15.00,2,1),(5,'Ron Pampero Blanco',90,18.00,2,1),(6,'Johnnie Walker Black Label',40,65.00,3,1),(7,'Chivas Regal 12 años',35,60.00,3,1),(8,'Jameson Irish Whiskey',30,55.00,4,1),(9,'Jack Daniels',45,50.00,5,1),(10,'Absolut Vodka',55,40.00,6,1),(11,'Smirnoff Vodka',70,25.00,7,1),(12,'Jose Cuervo Especial',65,35.00,8,1),(13,'Don Julio Reposado',25,70.00,9,1),(14,'Polar Pilsen',198,2.50,11,1),(15,'Solera Verde',150,3.00,11,1),(16,'Heineken',100,4.50,12,1),(17,'Budweiser',90,4.00,12,1),(18,'Vino Tinto Don Simon',80,8.00,13,1),(19,'Vino Blanco Gato Negro',70,9.00,14,1),(20,'Baileys Irish Cream',30,35.00,18,1),(21,'Aguardiente Antioqueño',40,20.00,19,1),(22,'Sidra Pomar',50,12.00,20,1),(23,'Moët & Chandon Imperial',15,120.00,16,1),(24,'Veuve Clicquot',10,110.00,16,1),(25,'Ron Zacapa Centenario 23',20,80.00,1,1);
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_producto_delete` BEFORE DELETE ON `producto` FOR EACH ROW BEGIN


    INSERT INTO perdidas (id_producto, cantidad, descripcion, fecha_hora)


    VALUES (OLD.id, OLD.cantidad, 'Producto eliminado', NOW());


END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `proveedor_producto`
--

DROP TABLE IF EXISTS `proveedor_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_proveedor` varchar(15) NOT NULL,
  `id_producto` int(10) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp(),
  `id_estatus` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proveedor_producto_unico` (`cedula_proveedor`,`id_producto`),
  KEY `id_producto` (`id_producto`),
  KEY `fk_proveedor_producto_estatus` (`id_estatus`),
  CONSTRAINT `fk_proveedor_producto_estatus` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`),
  CONSTRAINT `proveedor_producto_ibfk_1` FOREIGN KEY (`cedula_proveedor`) REFERENCES `proveedores` (`cedula`) ON UPDATE CASCADE,
  CONSTRAINT `proveedor_producto_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedor_producto`
--

LOCK TABLES `proveedor_producto` WRITE;
/*!40000 ALTER TABLE `proveedor_producto` DISABLE KEYS */;
INSERT INTO `proveedor_producto` VALUES (1,'12345678',1,35.00,'2025-07-26 23:50:30',1),(2,'12345678',2,22.00,'2025-07-26 23:50:30',1),(3,'12345678',3,30.00,'2025-07-26 23:50:30',1),(4,'12345678',4,10.00,'2025-07-26 23:50:30',1),(5,'12345678',5,12.00,'2025-07-26 23:50:30',1),(6,'87654321',6,50.00,'2025-07-26 23:50:30',1),(7,'87654321',7,45.00,'2025-07-26 23:50:30',1),(8,'87654321',8,40.00,'2025-07-26 23:50:30',1),(9,'87654321',9,35.00,'2025-07-26 23:50:30',1),(10,'87654321',10,30.00,'2025-07-26 23:50:30',1),(11,'11223344',11,18.00,'2025-07-26 23:50:30',1),(12,'11223344',12,25.00,'2025-07-26 23:50:30',1),(13,'11223344',13,50.00,'2025-07-26 23:50:30',1),(14,'11223344',14,1.80,'2025-07-26 23:50:30',1),(15,'11223344',15,2.20,'2025-07-26 23:50:30',1),(16,'44332211',16,3.50,'2025-07-26 23:50:30',1),(17,'44332211',17,3.00,'2025-07-26 23:50:30',1),(18,'44332211',18,5.00,'2025-07-26 23:50:30',1),(19,'44332211',19,6.00,'2025-07-26 23:50:30',1),(20,'44332211',20,25.00,'2025-07-26 23:50:30',1),(21,'55667788',21,15.00,'2025-07-26 23:50:30',1),(22,'55667788',22,8.00,'2025-07-26 23:50:30',1),(23,'55667788',23,90.00,'2025-07-26 23:50:30',1),(24,'55667788',24,85.00,'2025-07-26 23:50:30',1),(25,'55667788',25,60.00,'2025-07-26 23:50:30',1);
/*!40000 ALTER TABLE `proveedor_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedores` (
  `cedula` varchar(15) NOT NULL,
  `id_simbolo_cedula` int(11) DEFAULT 1,
  `nombre` varchar(255) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(255) DEFAULT 'Sin especificar',
  `id_estatus` int(11) NOT NULL,
  PRIMARY KEY (`cedula`),
  KEY `id_estatus` (`id_estatus`),
  KEY `proveedores_ibfk_simbolo` (`id_simbolo_cedula`),
  CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`),
  CONSTRAINT `proveedores_ibfk_simbolo` FOREIGN KEY (`id_simbolo_cedula`) REFERENCES `simbolos_cedula` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES ('11223344',4,'Alimentos y Bebidas del Centro','04141234567','Av. Bolívar, Maracay',1),('12345678',4,'Distribuidora Licores Nacionales','04121234567','Av. Principal, Valencia',1),('44332211',4,'Distribuidora La Europea','04161234567','Calle Sucre, Puerto Cabello',1),('55667788',4,'Bebidas Internacionales C.A.','04261234567','Av. Miranda, Valencia',1),('87654321',4,'Importadora Bebidas Selectas','04241234567','Calle Comercio, Caracas',1);
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador'),(2,'Empleado');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simbolos_cedula`
--

DROP TABLE IF EXISTS `simbolos_cedula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simbolos_cedula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(2) NOT NULL COMMENT 'Símbolo (V, E, G, J)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simbolos_cedula`
--

LOCK TABLES `simbolos_cedula` WRITE;
/*!40000 ALTER TABLE `simbolos_cedula` DISABLE KEYS */;
INSERT INTO `simbolos_cedula` VALUES (1,'V'),(2,'E'),(3,'G'),(4,'J');
/*!40000 ALTER TABLE `simbolos_cedula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_limites`
--

DROP TABLE IF EXISTS `stock_limites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_limites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(10) NOT NULL,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `stock_maximo` int(11) NOT NULL DEFAULT 0,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_producto_unico` (`id_producto`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `stock_limites_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  CONSTRAINT `stock_limites_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_limites`
--

LOCK TABLES `stock_limites` WRITE;
/*!40000 ALTER TABLE `stock_limites` DISABLE KEYS */;
INSERT INTO `stock_limites` VALUES (1,1,10,100,'2025-07-26 23:53:32',1),(2,2,15,120,'2025-07-26 23:53:32',1),(3,3,10,100,'2025-07-26 23:53:32',1),(4,4,20,200,'2025-07-26 23:53:32',1),(5,5,15,150,'2025-07-26 23:53:32',1),(6,6,5,80,'2025-07-26 23:53:32',1),(7,7,5,70,'2025-07-26 23:53:32',1),(8,8,5,60,'2025-07-26 23:53:32',1),(9,9,5,80,'2025-07-26 23:53:32',1),(10,10,10,100,'2025-07-26 23:53:32',1),(11,11,15,120,'2025-07-26 23:53:32',1),(12,12,10,100,'2025-07-26 23:53:32',1),(13,13,3,50,'2025-07-26 23:53:32',1),(14,14,30,300,'2025-07-26 23:53:32',1),(15,15,20,200,'2025-07-26 23:53:32',1),(16,16,15,150,'2025-07-26 23:53:32',1),(17,17,15,150,'2025-07-26 23:53:32',1),(18,18,10,120,'2025-07-26 23:53:32',1),(19,19,10,100,'2025-07-26 23:53:32',1),(20,20,5,50,'2025-07-26 23:53:32',1),(21,21,5,60,'2025-07-26 23:53:32',1),(22,22,5,80,'2025-07-26 23:53:32',1),(23,23,2,30,'2025-07-26 23:53:32',1),(24,24,2,20,'2025-07-26 23:53:32',1),(25,25,3,40,'2025-07-26 23:53:32',1);
/*!40000 ALTER TABLE `stock_limites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_categoria`
--

DROP TABLE IF EXISTS `tipos_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `id_estatus` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_tipos_categoria_estatus` (`id_estatus`),
  CONSTRAINT `fk_tipos_categoria_estatus` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_categoria`
--

LOCK TABLES `tipos_categoria` WRITE;
/*!40000 ALTER TABLE `tipos_categoria` DISABLE KEYS */;
INSERT INTO `tipos_categoria` VALUES (1,'Licores',1),(2,'Cervezas',1),(3,'Vinos',1),(4,'Whiskies',1),(5,'Ron',1),(6,'Vodka',1),(7,'Tequila',1),(8,'Otros',1);
/*!40000 ALTER TABLE `tipos_categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(12) NOT NULL,
  `id_simbolo_cedula` int(11) DEFAULT 1,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(255) DEFAULT 'Sin especificar',
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL,
  `ultimo_inicio_sesion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_unico` (`user`),
  KEY `id_rol` (`id_rol`),
  KEY `id_estatus` (`id_estatus`),
  KEY `usuarios_ibfk_simbolo` (`id_simbolo_cedula`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`),
  CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`),
  CONSTRAINT `usuarios_ibfk_simbolo` FOREIGN KEY (`id_simbolo_cedula`) REFERENCES `simbolos_cedula` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'12345678',1,'Super','Admin','04127654321','Naguanagua','admin','$2y$10$.Dv.UCeKDYG3HIiK.4F7Jed5g2/1FZWq8j6zRHErVQNLYxUBhM4NG',1,1,'2025-06-26 14:46:42'),(2,'8765432',1,'Pedro','Gómez','04121234568','Av. Principal, Naguanagua','pgomez','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',2,1,'2025-07-20 09:15:22'),(3,'7654321',1,'Ana','López','04241234568','Calle Comercio, San Diego','alopez','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',2,1,'2025-07-21 10:30:45'),(4,'6543210',1,'Carlos','Martínez','04161234568','Av. Bolívar, Los Guayos','cmartinez','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',2,1,'2025-07-22 14:20:10'),(5,'5432109',1,'Luisa','Fernández','04261234568','Calle Sucre, La Isabelica','lfernandez','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2025-07-23 16:45:30');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_usuarios_update_ultimo_inicio` BEFORE UPDATE ON `usuarios` FOR EACH ROW BEGIN


    -- Si el campo 'ultimo_inicio_sesion' está siendo actualizado


    IF NEW.ultimo_inicio_sesion IS NOT NULL THEN


        SET NEW.ultimo_inicio_sesion = NOW();


    END IF;


END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ventas` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(15) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `id_venta_original` int(11) DEFAULT NULL,
  `id_estatus` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `ventas_ibfk_usuario` (`id_usuario`),
  KEY `idx_ventas_fecha` (`fecha`),
  KEY `fk_ventas_original` (`id_venta_original`),
  KEY `fk_ventas_estatus` (`id_estatus`),
  CONSTRAINT `fk_ventas_estatus` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`),
  CONSTRAINT `fk_ventas_original` FOREIGN KEY (`id_venta_original`) REFERENCES `ventas` (`id`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula`) ON UPDATE CASCADE,
  CONSTRAINT `ventas_ibfk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (1,'9876543',2,'2025-07-01 10:15:00',90.00,NULL,1),(2,'1234567',2,'2025-07-01 11:30:00',65.00,NULL,1),(3,'7654321',3,'2025-07-02 12:45:00',30.00,NULL,1),(4,'1357924',3,'2025-07-02 14:20:00',120.00,NULL,1),(5,'2468135',4,'2025-07-03 16:10:00',80.00,NULL,1),(6,'1122334',4,'2025-07-03 17:30:00',45.00,NULL,1),(7,'2233445',2,'2025-07-04 18:45:00',60.00,NULL,1),(8,'3344556',3,'2025-07-05 10:00:00',35.00,NULL,1),(9,'4455667',4,'2025-07-05 11:15:00',50.00,NULL,1),(10,'5566778',2,'2025-07-06 12:30:00',70.00,NULL,1),(11,'9876543',3,'2025-07-06 14:45:00',40.00,NULL,1),(12,'1234567',4,'2025-07-07 16:00:00',55.00,NULL,1),(13,'7654321',2,'2025-07-08 17:15:00',25.00,NULL,1),(14,'1357924',3,'2025-07-09 18:30:00',18.00,NULL,1),(15,'2468135',4,'2025-07-10 10:45:00',15.00,NULL,1),(16,'3344556',1,'2025-07-28 00:27:26',17.50,NULL,1),(17,'3344556',1,'2025-07-28 00:32:10',17.50,NULL,1);
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `vista_detalle_ventas`
--

DROP TABLE IF EXISTS `vista_detalle_ventas`;
/*!50001 DROP VIEW IF EXISTS `vista_detalle_ventas`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vista_detalle_ventas` AS SELECT
 1 AS `id_detalle`,
  1 AS `id_venta`,
  1 AS `cedula_cliente`,
  1 AS `nombre_usuario`,
  1 AS `fecha`,
  1 AS `id_producto`,
  1 AS `producto`,
  1 AS `cantidad`,
  1 AS `monto` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vista_movimientos_completa`
--

DROP TABLE IF EXISTS `vista_movimientos_completa`;
/*!50001 DROP VIEW IF EXISTS `vista_movimientos_completa`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vista_movimientos_completa` AS SELECT
 1 AS `id`,
  1 AS `numero_transaccion`,
  1 AS `id_producto`,
  1 AS `producto`,
  1 AS `tipo_movimiento`,
  1 AS `subtipo_movimiento`,
  1 AS `cantidad`,
  1 AS `precio_unitario`,
  1 AS `id_referencia`,
  1 AS `tipo_referencia`,
  1 AS `fecha_movimiento`,
  1 AS `usuario`,
  1 AS `observaciones`,
  1 AS `estado`,
  1 AS `id_movimiento_original`,
  1 AS `categoria` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vista_producto_mas_vendido_mes`
--

DROP TABLE IF EXISTS `vista_producto_mas_vendido_mes`;
/*!50001 DROP VIEW IF EXISTS `vista_producto_mas_vendido_mes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vista_producto_mas_vendido_mes` AS SELECT
 1 AS `Periodo`,
  1 AS `Producto`,
  1 AS `Total_Vendido`,
  1 AS `Semana`,
  1 AS `Año` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vista_producto_menos_vendido`
--

DROP TABLE IF EXISTS `vista_producto_menos_vendido`;
/*!50001 DROP VIEW IF EXISTS `vista_producto_menos_vendido`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vista_producto_menos_vendido` AS SELECT
 1 AS `Periodo`,
  1 AS `Producto`,
  1 AS `Total_Vendido`,
  1 AS `Semana`,
  1 AS `Año` */;
SET character_set_client = @saved_cs_client;

--
-- Current Database: `licoreria`
--

USE `licoreria`;

--
-- Final view structure for view `vista_detalle_ventas`
--

/*!50001 DROP VIEW IF EXISTS `vista_detalle_ventas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_detalle_ventas` AS select `dv`.`id` AS `id_detalle`,`v`.`id` AS `id_venta`,`v`.`cedula_cliente` AS `cedula_cliente`,`u`.`nombres` AS `nombre_usuario`,`v`.`fecha` AS `fecha`,`dv`.`id_producto` AS `id_producto`,`p`.`descripcion` AS `producto`,`dv`.`cantidad` AS `cantidad`,`dv`.`monto` AS `monto` from (((`detalle_venta` `dv` join `ventas` `v` on(`dv`.`id_venta` = `v`.`id`)) join `usuarios` `u` on(`v`.`id_usuario` = `u`.`id`)) join `producto` `p` on(`dv`.`id_producto` = `p`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_movimientos_completa`
--

/*!50001 DROP VIEW IF EXISTS `vista_movimientos_completa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_movimientos_completa` AS select `mi`.`id` AS `id`,`mi`.`numero_transaccion` AS `numero_transaccion`,`mi`.`id_producto` AS `id_producto`,`p`.`descripcion` AS `producto`,`mi`.`tipo_movimiento` AS `tipo_movimiento`,`mi`.`subtipo_movimiento` AS `subtipo_movimiento`,`mi`.`cantidad` AS `cantidad`,`mi`.`precio_unitario` AS `precio_unitario`,`mi`.`id_referencia` AS `id_referencia`,`mi`.`tipo_referencia` AS `tipo_referencia`,`mi`.`fecha_movimiento` AS `fecha_movimiento`,concat(`u`.`nombres`,' ',`u`.`apellidos`) AS `usuario`,`mi`.`observaciones` AS `observaciones`,`e`.`nombre` AS `estado`,`mi`.`id_movimiento_original` AS `id_movimiento_original`,`c`.`nombre` AS `categoria` from ((((`movimientos_inventario` `mi` join `producto` `p` on(`mi`.`id_producto` = `p`.`id`)) join `usuarios` `u` on(`mi`.`id_usuario` = `u`.`id`)) join `estatus` `e` on(`mi`.`id_estatus` = `e`.`id`)) left join `categorias` `c` on(`p`.`id_categoria` = `c`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_producto_mas_vendido_mes`
--

/*!50001 DROP VIEW IF EXISTS `vista_producto_mas_vendido_mes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_producto_mas_vendido_mes` AS select date_format(`v`.`fecha`,'%Y-%m') AS `Periodo`,`p`.`descripcion` AS `Producto`,sum(`dv`.`cantidad`) AS `Total_Vendido`,week(`v`.`fecha`) AS `Semana`,year(`v`.`fecha`) AS `Año` from ((`detalle_venta` `dv` join `ventas` `v` on(`dv`.`id_venta` = `v`.`id`)) join `producto` `p` on(`dv`.`id_producto` = `p`.`id`)) group by date_format(`v`.`fecha`,'%Y-%m'),`p`.`descripcion`,week(`v`.`fecha`),year(`v`.`fecha`) order by sum(`dv`.`cantidad`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_producto_menos_vendido`
--

/*!50001 DROP VIEW IF EXISTS `vista_producto_menos_vendido`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_producto_menos_vendido` AS select 'Semana' AS `Periodo`,`p`.`descripcion` AS `Producto`,sum(`dv`.`cantidad`) AS `Total_Vendido`,week(`v`.`fecha`) AS `Semana`,year(`v`.`fecha`) AS `Año` from ((`detalle_venta` `dv` join `producto` `p` on(`dv`.`id_producto` = `p`.`id`)) join `ventas` `v` on(`dv`.`id_venta` = `v`.`id`)) group by `p`.`id`,week(`v`.`fecha`),year(`v`.`fecha`) order by sum(`dv`.`cantidad`) limit 0,1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-28  3:09:10

-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for b55producciones
CREATE DATABASE IF NOT EXISTS `b55producciones` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `b55producciones`;

-- Dumping structure for table b55producciones.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `client_company` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `client_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table b55producciones.clientes: ~0 rows (approximately)
INSERT INTO `clientes` (`id`, `client_name`, `client_company`, `client_email`, `client_phone`, `created_at`) VALUES
	(1, 'Prueba 2', 'probando2', 'probando2@probando.com', '14567789', '2024-12-13 18:28:42');

-- Dumping structure for table b55producciones.comentarios
CREATE TABLE IF NOT EXISTS `comentarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `proyecto_id` int DEFAULT NULL,
  `tarea_id` int DEFAULT NULL,
  `contenido` text,
  `autor` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `proyecto_id` (`proyecto_id`),
  KEY `tarea_id` (`tarea_id`),
  CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table b55producciones.comentarios: ~0 rows (approximately)

-- Dumping structure for table b55producciones.facturas
CREATE TABLE IF NOT EXISTS `facturas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `presupuesto_id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `numero_factura` varchar(20) NOT NULL,
  `fecha_emision` date NOT NULL,
  `estado` enum('pagada','pendiente') DEFAULT 'pendiente',
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_factura` (`numero_factura`),
  KEY `presupuesto_id` (`presupuesto_id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuesto` (`id`) ON DELETE CASCADE,
  CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table b55producciones.facturas: ~0 rows (approximately)

-- Dumping structure for table b55producciones.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `factura_id` int NOT NULL,
  `fecha_pago` date NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_id` (`factura_id`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table b55producciones.pagos: ~0 rows (approximately)

-- Dumping structure for table b55producciones.presupuesto
CREATE TABLE IF NOT EXISTS `presupuesto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `project_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `creation_date` date NOT NULL,
  `global_total` decimal(25,0) NOT NULL,
  `imprevisto` decimal(11,2) DEFAULT NULL,
  `impuesto` decimal(11,2) DEFAULT NULL,
  `total_presupuestado` decimal(11,2) DEFAULT NULL,
  `due_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `presupuesto_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table b55producciones.presupuesto: ~5 rows (approximately)
INSERT INTO `presupuesto` (`id`, `client_id`, `project_name`, `creation_date`, `global_total`, `imprevisto`, `impuesto`, `total_presupuestado`, `due_date`) VALUES
	(1, 1, 'probando gbc', '2024-12-11', 0, NULL, NULL, NULL, '2024-12-25'),
	(2, 2, 'Grabaciones unicas de presentacion local', '2024-12-13', 175200, 8760.00, 33112.80, 217072.80, '2024-12-20'),
	(3, 3, 'probando', '2024-12-18', 33000, 1650.00, 6237.00, 40887.00, '2025-01-10'),
	(4, 4, 'probando2', '2024-12-29', 10982, 549.10, 2075.60, 13606.70, '2025-01-03'),
	(5, 1, 'probando2', '2024-12-05', 519520, 25976.00, 98189.28, 643685.28, '2024-12-30');

-- Dumping structure for table b55producciones.proyectos
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `presupuesto_asignado` decimal(10,2) DEFAULT NULL,
  `costo_real` decimal(10,2) DEFAULT '0.00',
  `estado` enum('pendiente','en progreso','completado') DEFAULT 'pendiente',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table b55producciones.proyectos: ~0 rows (approximately)
INSERT INTO `proyectos` (`id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `presupuesto_asignado`, `costo_real`, `estado`, `fecha_creacion`) VALUES
	(1, 'Grabaciones unicas de presentacion local', 'grabar comerciales de todas las localidades de la zona.', '2024-12-13', '2024-12-19', 25000.00, 0.00, 'pendiente', '2024-12-13 17:04:31'),
	(2, 'probando2', 'probando esto', '2024-12-25', '2024-12-24', 657291.98, 0.00, 'pendiente', '2024-12-13 18:39:39');

-- Dumping structure for table b55producciones.servicios_presupuesto
CREATE TABLE IF NOT EXISTS `servicios_presupuesto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `presupuesto_id` int NOT NULL,
  `partida` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `presupuesto_id` (`presupuesto_id`),
  CONSTRAINT `servicios_presupuesto_ibfk_1` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuesto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table b55producciones.servicios_presupuesto: ~0 rows (approximately)
INSERT INTO `servicios_presupuesto` (`id`, `presupuesto_id`, `partida`, `descripcion`, `cantidad`, `precio`, `subtotal`, `created_at`) VALUES
	(1, 1, 'preproduccion', 'ghghghg', 1, 12457.25, 12457.25, '2024-12-12 03:19:23'),
	(2, 2, 'preproduccion', 'Productor', 1, 175200.00, 175200.00, '2024-12-13 13:09:32'),
	(3, 3, 'preproduccion', 'Productor', 1, 25000.00, 25000.00, '2024-12-13 14:32:34'),
	(4, 3, 'preproduccion', 'asistente', 1, 8000.00, 8000.00, '2024-12-13 14:32:34'),
	(5, 4, 'preproduccion', 'Productor', 1, 7500.00, 7500.00, '2024-12-13 15:51:13'),
	(6, 4, 'preproduccion', 'asisis', 1, 25.00, 25.00, '2024-12-13 15:51:13'),
	(7, 4, 'produccion', 'prr', 1, 425.00, 425.00, '2024-12-13 15:51:13'),
	(8, 4, 'arte', 'dsasd', 1, 25.00, 25.00, '2024-12-13 15:51:13'),
	(9, 4, 'arte', 'sdfdsf', 1, 50.00, 50.00, '2024-12-13 15:51:13'),
	(10, 4, 'edicionypostproduccion', 'sdfdfd', 1, 1475.00, 1475.00, '2024-12-13 15:51:13'),
	(11, 4, 'edicionypostproduccion', 'gfgfgf', 1, 1482.00, 1482.00, '2024-12-13 15:51:13'),
	(12, 5, 'preproduccion', 'Productor', 1, 1520.00, 1520.00, '2024-12-13 18:28:42'),
	(13, 5, 'preproduccion', 'asisis', 1, 8000.00, 8000.00, '2024-12-13 18:28:42'),
	(14, 5, 'talento', 'actor', 1, 85000.00, 85000.00, '2024-12-13 18:28:42'),
	(15, 5, 'gastos_produccion', 'producto', 1, 425000.00, 425000.00, '2024-12-13 18:28:42');

-- Dumping structure for table b55producciones.tareas
CREATE TABLE IF NOT EXISTS `tareas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `proyecto_id` int NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `responsable` varchar(255) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('pendiente','en progreso','completada') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pendiente',
  `prioridad` enum('baja','media','alta') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'baja',
  PRIMARY KEY (`id`),
  KEY `proyecto_id` (`proyecto_id`),
  CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table b55producciones.tareas: ~0 rows (approximately)
INSERT INTO `tareas` (`id`, `proyecto_id`, `titulo`, `descripcion`, `responsable`, `fecha_inicio`, `fecha_fin`, `estado`, `prioridad`) VALUES
	(2, 1, 'grabaciones del barrio guachupita.', 'estas grabaciones se haran proximo a la escula del  lugar para captar la mayor cantidad de personas.', NULL, '2024-12-13', '2024-12-16', 'completada', 'baja');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

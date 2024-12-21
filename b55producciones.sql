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

-- Dumping structure for table b55producciones.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `client_company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `client_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table b55producciones.clientes: ~5 rows (approximately)
REPLACE INTO `clientes` (`id`, `client_name`, `client_company`, `client_email`, `client_phone`, `created_at`) VALUES
	(7, 'Wendin de Jesus stone', 'StoneCorp', 'StoneCorp@hotmail.com', '8295676666', '2024-12-19 15:10:33'),
	(8, 'Wendin de Jesus stone', 'StoneCorp', 'StoneCorp@hotmail.com', '8295676666', '2024-12-19 15:12:16'),
	(9, 'Wendin de Jesus stone', 'StoneCorp', 'StoneCorp@hotmail.com', '8295676666', '2024-12-19 15:18:17'),
	(10, 'Lewis Genao', 'Instituto Domadierch', 'institutodomadierch@hotmail.com', '8297853654', '2024-12-19 20:57:47'),
	(11, 'Wendin De Jesus', 'locomotion drag', 'locomotion@hotmail.com', '8092554545', '2024-12-21 15:51:47');

-- Dumping structure for table b55producciones.facturas
CREATE TABLE IF NOT EXISTS `facturas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `presupuesto_id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `numero_factura` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_emision` date NOT NULL,
  `estado` enum('pagada','pendiente') COLLATE utf8mb4_general_ci DEFAULT 'pendiente',
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_factura` (`numero_factura`),
  KEY `presupuesto_id` (`presupuesto_id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuesto` (`id`) ON DELETE CASCADE,
  CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table b55producciones.facturas: ~2 rows (approximately)
REPLACE INTO `facturas` (`id`, `presupuesto_id`, `cliente_id`, `numero_factura`, `fecha_emision`, `estado`, `total`) VALUES
	(8, 10, 10, 'B0100000065', '2024-12-19', 'pendiente', 1033016.25),
	(9, 11, 11, 'B01000000125', '2024-12-21', 'pagada', 92925.00);

-- Dumping structure for table b55producciones.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `factura_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `monto_real` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `a_quien_dirigida` varchar(255) NOT NULL,
  `beneficiario` varchar(255) NOT NULL,
  `cedula_beneficiario` varchar(20) NOT NULL,
  `concepto` varchar(255) NOT NULL,
  `condicion_comercial` enum('Persona Física','Simplificado','Informal') NOT NULL,
  `factura_beneficiario` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_id` (`factura_id`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table b55producciones.pagos: ~3 rows (approximately)
REPLACE INTO `pagos` (`id`, `factura_id`, `monto`, `monto_real`, `fecha_pago`, `metodo_pago`, `a_quien_dirigida`, `beneficiario`, `cedula_beneficiario`, `concepto`, `condicion_comercial`, `factura_beneficiario`) VALUES
	(16, 8, 100000.00, 90000.00, '2024-12-19', 'Transferencia', 'Lic. Luis Valdez', 'Wenjos Corp', '056-0125043-3', 'direccion', 'Simplificado', 'B0100000098'),
	(17, 8, 18000.00, 18000.00, '2024-12-20', 'transferencia', 'Lic. Luis Valdes', 'Paty Gil', '001-1521458-3', 'vestuarioymaquillaje', 'Informal', 'N/A'),
	(18, 9, 140000.00, 140000.00, '2024-12-22', 'Transferencia', 'Lic Luis Valdez', 'Franklyn Suriel', '056012458-1', 'preproduccion', 'Persona Física', 'B00100001');

-- Dumping structure for table b55producciones.presupuesto
CREATE TABLE IF NOT EXISTS `presupuesto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `project_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `creation_date` date NOT NULL,
  `global_total` decimal(25,0) NOT NULL,
  `imprevisto` decimal(11,2) DEFAULT NULL,
  `impuesto` decimal(11,2) DEFAULT NULL,
  `total_presupuestado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `due_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `presupuesto_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table b55producciones.presupuesto: ~2 rows (approximately)
REPLACE INTO `presupuesto` (`id`, `client_id`, `project_name`, `creation_date`, `global_total`, `imprevisto`, `impuesto`, `total_presupuestado`, `due_date`) VALUES
	(10, 10, 'A puro Pelo', '2024-12-20', 833750, 41687.50, 157578.75, 1125321.75, '2024-12-23'),
	(11, 11, 'Comercial casas de las viejas como Franklyn', '2024-12-21', 75000, 3750.00, 14175.00, 92925.00, '2024-12-22');

-- Dumping structure for table b55producciones.proyectos
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `presupuesto_asignado` decimal(10,2) DEFAULT NULL,
  `costo_real` decimal(10,2) DEFAULT '0.00',
  `estado` enum('pendiente','en progreso','completado') COLLATE utf8mb4_general_ci DEFAULT 'pendiente',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table b55producciones.proyectos: ~1 rows (approximately)
REPLACE INTO `proyectos` (`id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `presupuesto_asignado`, `costo_real`, `estado`, `fecha_creacion`) VALUES
	(6, 'Comercial casas de las viejas como Franklyn', 'esto sera un comercial basado en la vida de vieja Franklyn Suriel', '2024-12-21', '2024-12-22', 92925.00, 0.00, 'pendiente', '2024-12-21 15:53:35');

-- Dumping structure for table b55producciones.servicios_presupuesto
CREATE TABLE IF NOT EXISTS `servicios_presupuesto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `presupuesto_id` int NOT NULL,
  `partida` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS ((`cantidad` * `precio`)) STORED,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `presupuesto_id` (`presupuesto_id`),
  CONSTRAINT `servicios_presupuesto_ibfk_1` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuesto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table b55producciones.servicios_presupuesto: ~14 rows (approximately)
REPLACE INTO `servicios_presupuesto` (`id`, `presupuesto_id`, `partida`, `descripcion`, `cantidad`, `precio`, `created_at`) VALUES
	(191, 10, 'preproduccion', 'Productor', 4, 35000.00, '2024-12-21 13:37:18'),
	(192, 10, 'produccion', 'Productor', 1, 100000.00, '2024-12-21 13:37:18'),
	(193, 10, 'direccion', 'Director', 1, 100000.00, '2024-12-21 13:37:18'),
	(194, 10, 'fotografia', 'Director de fotografia', 1, 60000.00, '2024-12-21 13:37:18'),
	(195, 10, 'arte', 'Director de arte', 1, 65000.00, '2024-12-21 13:37:18'),
	(196, 10, 'ayb', 'Viaticos', 25, 1350.00, '2024-12-21 13:37:18'),
	(197, 10, 'miscelaneos', 'Disco duro', 1, 12500.00, '2024-12-21 13:37:18'),
	(198, 10, 'sonido', 'Sonidista', 1, 12000.00, '2024-12-21 13:37:18'),
	(199, 10, 'vestuarioymaquillaje', 'Maquillaje', 1, 20000.00, '2024-12-21 13:37:18'),
	(200, 10, 'vestuarioymaquillaje', 'Peluquera', 1, 20000.00, '2024-12-21 13:37:18'),
	(201, 10, 'talento', 'Actor', 1, 100000.00, '2024-12-21 13:37:18'),
	(202, 10, 'gastos_produccion', 'Compra de produccion', 1, 45000.00, '2024-12-21 13:37:18'),
	(203, 10, 'edicionypostproduccion', 'Edicion', 1, 200000.00, '2024-12-21 13:37:18'),
	(204, 11, 'preproduccion', 'Productor', 1, 75000.00, '2024-12-21 15:51:47');

-- Dumping structure for table b55producciones.tareas
CREATE TABLE IF NOT EXISTS `tareas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `proyecto_id` int NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `responsable` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('pendiente','en progreso','completada') COLLATE utf8mb4_general_ci DEFAULT 'pendiente',
  `prioridad` enum('baja','media','alta') COLLATE utf8mb4_general_ci DEFAULT 'baja',
  PRIMARY KEY (`id`),
  KEY `proyecto_id` (`proyecto_id`),
  CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table b55producciones.tareas: ~0 rows (approximately)

-- Dumping structure for table b55producciones.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rol` enum('Admin','Usuario') DEFAULT 'Usuario',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table b55producciones.usuarios: ~1 rows (approximately)
REPLACE INTO `usuarios` (`id`, `username`, `password`, `nombre`, `rol`, `creado_en`) VALUES
	(1, 'wendin', '$2y$10$vNWnnVZ2ArLOf3EKtO5QBOBTUPlkG.Jd25fKlGPpzhbxzuYFVxV9O', 'Wendin De Jesus', 'Admin', '2024-12-15 23:01:25');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

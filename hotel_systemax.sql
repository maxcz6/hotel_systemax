-- bd = hotel_systemax 
--phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-12-2025 a las 15:33:56
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hotel_systemax`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_habitaciones_disponibles` (IN `p_fecha_entrada` DATE, IN `p_fecha_salida` DATE, IN `p_tipo_habitacion_id` BIGINT)   BEGIN
  SELECT h.*
  FROM habitaciones h
  WHERE h.estado = 'disponible'
    AND (p_tipo_habitacion_id IS NULL OR h.tipo_habitacion_id = p_tipo_habitacion_id)
    AND h.id NOT IN (
      SELECT habitacion_id
      FROM reservas
      WHERE estado IN ('confirmada', 'checkin', 'pendiente')
        AND (
          (p_fecha_entrada BETWEEN fecha_entrada AND fecha_salida) OR
          (p_fecha_salida BETWEEN fecha_entrada AND fecha_salida) OR
          (fecha_entrada BETWEEN p_fecha_entrada AND p_fecha_salida)
        )
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_realizar_checkin` (IN `p_reserva_id` BIGINT, IN `p_usuario_id` BIGINT)   BEGIN
  DECLARE v_habitacion_id BIGINT;
  
  START TRANSACTION;
  
  -- Actualizar estado de reserva
  UPDATE reservas
  SET estado = 'checkin'
  WHERE id = p_reserva_id;
  
  -- Obtener habitación
  SELECT habitacion_id INTO v_habitacion_id
  FROM reservas
  WHERE id = p_reserva_id;
  
  -- Actualizar estado de habitación
  UPDATE habitaciones
  SET estado = 'ocupada'
  WHERE id = v_habitacion_id;
  
  -- Crear estancia
  INSERT INTO estancias (reserva_id, check_in_real, estado)
  VALUES (p_reserva_id, NOW(), 'activa');
  
  COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_realizar_checkout` (IN `p_reserva_id` BIGINT, IN `p_usuario_id` BIGINT)   BEGIN
  DECLARE v_habitacion_id BIGINT;
  
  START TRANSACTION;
  
  -- Actualizar estado de reserva
  UPDATE reservas
  SET estado = 'completada'
  WHERE id = p_reserva_id;
  
  -- Actualizar estancia
  UPDATE estancias
  SET check_out_real = NOW(), estado = 'finalizada'
  WHERE reserva_id = p_reserva_id;
  
  -- Obtener habitación
  SELECT habitacion_id INTO v_habitacion_id
  FROM reservas
  WHERE id = p_reserva_id;
  
  -- Actualizar estado de habitación
  UPDATE habitaciones
  SET estado = 'limpieza'
  WHERE id = v_habitacion_id;
  
  COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporte_ingresos` (IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE)   BEGIN
  SELECT 
    DATE(p.fecha_pago) as fecha,
    COUNT(*) as total_pagos,
    SUM(p.monto) as total_ingresos,
    p.metodo_pago,
    AVG(p.monto) as promedio_pago
  FROM pagos p
  WHERE p.estado = 'completado'
    AND DATE(p.fecha_pago) BETWEEN p_fecha_inicio AND p_fecha_fin
  GROUP BY DATE(p.fecha_pago), p.metodo_pago
  ORDER BY fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporte_ocupacion` (IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE)   BEGIN
  SELECT 
    DATE(r.fecha_entrada) as fecha,
    COUNT(DISTINCT r.habitacion_id) as habitaciones_ocupadas,
    (SELECT COUNT(*) FROM habitaciones WHERE estado != 'mantenimiento') as total_habitaciones,
    ROUND((COUNT(DISTINCT r.habitacion_id) * 100.0 / 
      (SELECT COUNT(*) FROM habitaciones WHERE estado != 'mantenimiento')), 2) as porcentaje_ocupacion
  FROM reservas r
  WHERE r.estado IN ('confirmada', 'checkin')
    AND r.fecha_entrada <= p_fecha_fin
    AND r.fecha_salida >= p_fecha_inicio
  GROUP BY DATE(r.fecha_entrada)
  ORDER BY fecha;
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_tiene_permiso` (`p_role` VARCHAR(50), `p_permiso` VARCHAR(100)) RETURNS TINYINT(1) DETERMINISTIC READS SQL DATA BEGIN
  DECLARE tiene_permiso BOOLEAN DEFAULT FALSE;
  
  SELECT COUNT(*) > 0 INTO tiene_permiso
  FROM permisos
  WHERE nombre = p_permiso
    AND FIND_IN_SET(p_role, REPLACE(roles_permitidos, ',', ',')) > 0;
  
  RETURN tiene_permiso;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tabla` varchar(50) NOT NULL,
  `operacion` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `registro_id` bigint(20) UNSIGNED NOT NULL,
  `datos_anteriores` longtext DEFAULT NULL,
  `datos_nuevos` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `dni` varchar(8) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `dni`, `email`, `telefono`, `direccion`, `created_at`, `updated_at`) VALUES
(4, 'max', 'paucar', '70707070', 'maxjpr8@gmail.com', '999999999', 'hghrtbbgehbv', '2025-11-26 21:03:17', '2025-11-26 21:03:17');

--
-- Disparadores `clientes`
--
DELIMITER $$
CREATE TRIGGER `trg_auditoria_clientes_delete` BEFORE DELETE ON `clientes` FOR EACH ROW BEGIN
  INSERT INTO auditoria (tabla, operacion, registro_id, datos_anteriores)
  VALUES ('clientes', 'DELETE', OLD.id, JSON_OBJECT(
    'nombre', OLD.nombre,
    'apellido', OLD.apellido,
    'dni', OLD.dni,
    'email', OLD.email,
    'telefono', OLD.telefono,
    'direccion', OLD.direccion
  ));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estancias`
--

CREATE TABLE `estancias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reserva_id` bigint(20) UNSIGNED NOT NULL,
  `check_in_real` datetime DEFAULT NULL,
  `check_out_real` datetime DEFAULT NULL,
  `estado` enum('activa','finalizada','cancelada') NOT NULL DEFAULT 'activa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `estancias`
--
DELIMITER $$
CREATE TRIGGER `trg_estancia_checkin` AFTER INSERT ON `estancias` FOR EACH ROW BEGIN
  IF NEW.check_in_real IS NOT NULL THEN
    UPDATE habitaciones h
    INNER JOIN reservas r ON h.id = r.habitacion_id
    SET h.estado = 'ocupada'
    WHERE r.id = NEW.reserva_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_estancia_checkout` AFTER UPDATE ON `estancias` FOR EACH ROW BEGIN
  IF OLD.check_out_real IS NULL AND NEW.check_out_real IS NOT NULL THEN
    UPDATE habitaciones h
    INNER JOIN reservas r ON h.id = r.habitacion_id
    SET h.estado = 'limpieza'
    WHERE r.id = NEW.reserva_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

CREATE TABLE `habitaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero` varchar(255) NOT NULL,
  `tipo_habitacion_id` bigint(20) UNSIGNED NOT NULL,
  `precio_por_noche` decimal(8,2) NOT NULL,
  `estado` enum('disponible','ocupada','mantenimiento','limpieza') NOT NULL DEFAULT 'disponible',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`id`, `numero`, `tipo_habitacion_id`, `precio_por_noche`, `estado`, `created_at`, `updated_at`) VALUES
(2, '5', 2, 50.11, 'disponible', '2025-11-26 20:48:41', '2025-11-26 20:55:13');

--
-- Disparadores `habitaciones`
--
DELIMITER $$
CREATE TRIGGER `trg_habitacion_cambio_estado` AFTER UPDATE ON `habitaciones` FOR EACH ROW BEGIN
  IF OLD.estado != NEW.estado THEN
    INSERT INTO historial_estados_habitacion (habitacion_id, estado_anterior, estado_nuevo)
    VALUES (NEW.id, OLD.estado, NEW.estado);
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_estados_habitacion`
--

CREATE TABLE `historial_estados_habitacion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `habitacion_id` bigint(20) UNSIGNED NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_25_200859_add_role_to_users_table', 1),
(5, '2025_11_25_200958_create_tipo_habitaciones_table', 1),
(6, '2025_11_25_201011_create_habitaciones_table', 1),
(7, '2025_11_25_201024_create_clientes_table', 1),
(8, '2025_11_25_201036_create_reservas_table', 1),
(9, '2025_11_25_201053_create_estancias_table', 1),
(10, '2025_11_25_201107_create_servicios_table', 1),
(11, '2025_11_25_201120_create_servicio_detalles_table', 1),
(12, '2025_11_25_201134_create_pagos_table', 1),
(13, '2025_11_26_144647_add_dni_to_clientes_table', 2),
(14, '2025_11_26_151014_add_capacidad_precio_to_tipo_habitaciones_table', 3),
(15, '2025_11_26_160724_add_descuento_notas_to_reservas_table', 4),
(16, '2025_11_26_161334_modify_estado_column_in_reservas_table', 5),
(17, '0001_01_01_000000_create_users_table', 6),
(18, '2025_11_26_162546_add_role_column_again_to_users_table', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reserva_id` bigint(20) UNSIGNED NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(255) NOT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `fecha_pago` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `estado` enum('pendiente','completado','anulado') NOT NULL DEFAULT 'completado',
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `numero_transaccion` varchar(100) DEFAULT NULL,
  `anulado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_anulacion` datetime DEFAULT NULL,
  `motivo_anulacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `pagos`
--
DELIMITER $$
CREATE TRIGGER `trg_auditoria_pagos_update` AFTER UPDATE ON `pagos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (tabla, operacion, registro_id, datos_anteriores, datos_nuevos)
  VALUES ('pagos', 'UPDATE', NEW.id, 
    JSON_OBJECT('monto', OLD.monto, 'estado', OLD.estado),
    JSON_OBJECT('monto', NEW.monto, 'estado', NEW.estado)
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `roles_permitidos` varchar(255) NOT NULL COMMENT 'Roles separados por coma: administrador,gerente,recepcion',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `modulo`, `descripcion`, `roles_permitidos`, `created_at`, `updated_at`) VALUES
(1, 'habitaciones.ver', 'habitaciones', 'Ver listado de habitaciones', 'administrador,gerente,recepcion,limpieza,mantenimiento', NULL, NULL),
(2, 'habitaciones.crear', 'habitaciones', 'Crear nuevas habitaciones', 'administrador,gerente', NULL, NULL),
(3, 'habitaciones.editar', 'habitaciones', 'Editar habitaciones existentes', 'administrador,gerente', NULL, NULL),
(4, 'habitaciones.eliminar', 'habitaciones', 'Eliminar habitaciones', 'administrador', NULL, NULL),
(5, 'habitaciones.cambiar_estado', 'habitaciones', 'Cambiar estado de habitaciones', 'administrador,gerente,recepcion,limpieza,mantenimiento', NULL, NULL),
(6, 'reservas.ver', 'reservas', 'Ver reservas', 'administrador,gerente,recepcion', NULL, NULL),
(7, 'reservas.crear', 'reservas', 'Crear reservas', 'administrador,gerente,recepcion', NULL, NULL),
(8, 'reservas.editar', 'reservas', 'Editar reservas', 'administrador,gerente,recepcion', NULL, NULL),
(9, 'reservas.cancelar', 'reservas', 'Cancelar reservas', 'administrador,gerente', NULL, NULL),
(10, 'reservas.checkin', 'reservas', 'Realizar check-in', 'administrador,gerente,recepcion', NULL, NULL),
(11, 'reservas.checkout', 'reservas', 'Realizar check-out', 'administrador,gerente,recepcion', NULL, NULL),
(12, 'clientes.ver', 'clientes', 'Ver clientes', 'administrador,gerente,recepcion', NULL, NULL),
(13, 'clientes.crear', 'clientes', 'Crear clientes', 'administrador,gerente,recepcion', NULL, NULL),
(14, 'clientes.editar', 'clientes', 'Editar clientes', 'administrador,gerente,recepcion', NULL, NULL),
(15, 'clientes.eliminar', 'clientes', 'Eliminar clientes', 'administrador', NULL, NULL),
(16, 'pagos.ver', 'pagos', 'Ver pagos', 'administrador,gerente,recepcion', NULL, NULL),
(17, 'pagos.registrar', 'pagos', 'Registrar pagos', 'administrador,gerente,recepcion', NULL, NULL),
(18, 'pagos.anular', 'pagos', 'Anular pagos', 'administrador,gerente', NULL, NULL),
(19, 'reportes.ocupacion', 'reportes', 'Ver reporte de ocupación', 'administrador,gerente', NULL, NULL),
(20, 'reportes.ingresos', 'reportes', 'Ver reporte de ingresos', 'administrador,gerente', NULL, NULL),
(21, 'reportes.clientes', 'reportes', 'Ver reporte de clientes', 'administrador,gerente', NULL, NULL),
(22, 'usuarios.ver', 'usuarios', 'Ver usuarios', 'administrador', NULL, NULL),
(23, 'usuarios.crear', 'usuarios', 'Crear usuarios', 'administrador', NULL, NULL),
(24, 'usuarios.editar', 'usuarios', 'Editar usuarios', 'administrador', NULL, NULL),
(25, 'usuarios.eliminar', 'usuarios', 'Eliminar usuarios', 'administrador', NULL, NULL),
(26, 'servicios.ver', 'servicios', 'Ver servicios', 'administrador,gerente,recepcion', NULL, NULL),
(27, 'servicios.crear', 'servicios', 'Crear servicios', 'administrador,gerente', NULL, NULL),
(28, 'servicios.editar', 'servicios', 'Editar servicios', 'administrador,gerente', NULL, NULL),
(29, 'servicios.eliminar', 'servicios', 'Eliminar servicios', 'administrador', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `habitacion_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_entrada` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `total_precio` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` varchar(50) NOT NULL DEFAULT 'pendiente',
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `num_adultos` int(11) DEFAULT 2,
  `num_ninos` int(11) DEFAULT 0,
  `origen_reserva` enum('web','telefono','presencial','agencia') DEFAULT 'presencial',
  `cancelado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_cancelacion` datetime DEFAULT NULL,
  `motivo_cancelacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `cliente_id`, `usuario_id`, `habitacion_id`, `fecha_entrada`, `fecha_salida`, `total_precio`, `descuento`, `estado`, `notas`, `created_at`, `updated_at`, `num_adultos`, `num_ninos`, `origen_reserva`, `cancelado_por`, `fecha_cancelacion`, `motivo_cancelacion`) VALUES
(3, 4, NULL, 2, '2025-11-26', '2025-11-28', -100.22, 0.00, 'checkin', NULL, '2025-11-26 21:03:38', '2025-11-26 21:14:28', 2, 0, 'presencial', NULL, NULL, NULL);

--
-- Disparadores `reservas`
--
DELIMITER $$
CREATE TRIGGER `trg_calcular_precio_reserva` BEFORE INSERT ON `reservas` FOR EACH ROW BEGIN
  DECLARE precio_noche DECIMAL(10,2);
  DECLARE num_noches INT;
  
  SELECT precio_por_noche INTO precio_noche
  FROM habitaciones
  WHERE id = NEW.habitacion_id;
  
  SET num_noches = DATEDIFF(NEW.fecha_salida, NEW.fecha_entrada);
  SET NEW.total_precio = (precio_noche * num_noches) - NEW.descuento;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_validar_disponibilidad` BEFORE INSERT ON `reservas` FOR EACH ROW BEGIN
  DECLARE reservas_conflicto INT;
  
  SELECT COUNT(*) INTO reservas_conflicto
  FROM reservas
  WHERE habitacion_id = NEW.habitacion_id
    AND estado IN ('confirmada', 'checkin', 'pendiente')
    AND (
      (NEW.fecha_entrada BETWEEN fecha_entrada AND fecha_salida) OR
      (NEW.fecha_salida BETWEEN fecha_entrada AND fecha_salida) OR
      (fecha_entrada BETWEEN NEW.fecha_entrada AND NEW.fecha_salida)
    );
  
  IF reservas_conflicto > 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'La habitación no está disponible en las fechas seleccionadas';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `descripcion`, `precio`, `created_at`, `updated_at`) VALUES
(2, 'max', 'djtn', 55.00, '2025-11-26 21:05:47', '2025-11-26 21:05:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio_detalles`
--

CREATE TABLE `servicio_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reserva_id` bigint(20) UNSIGNED NOT NULL,
  `servicio_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(8,2) NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('gE96FbYDkRDmoimMuGtZuQ7Ky8BTKfsihZP46eN3', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiblZzZHJUTHk4YUdseHBEenBjOUZaNUVQbG5iRnZXcDZvV0M0S25KUSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1764771906);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas_especiales`
--

CREATE TABLE `tarifas_especiales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo_habitacion_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `precio_por_noche` decimal(10,2) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tarifas_especiales`
--

INSERT INTO `tarifas_especiales` (`id`, `tipo_habitacion_id`, `fecha_inicio`, `fecha_fin`, `precio_por_noche`, `motivo`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-12-20', '2026-01-05', 150.00, 'Temporada Navideña', 1, NULL, NULL),
(2, 2, '2025-12-20', '2026-01-05', 100.00, 'Temporada Navideña', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_habitaciones`
--

CREATE TABLE `tipo_habitaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `capacidad` int(11) NOT NULL DEFAULT 2,
  `precio_por_noche` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_base` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_habitaciones`
--

INSERT INTO `tipo_habitaciones` (`id`, `nombre`, `descripcion`, `capacidad`, `precio_por_noche`, `precio_base`, `created_at`, `updated_at`) VALUES
(1, 'bdgb', 'muhhtyrj', 15, 11.87, 10.00, '2025-11-26 19:55:05', '2025-11-26 20:56:28'),
(2, 'yth', 'dh', 2, 0.00, 0.00, '2025-11-26 19:58:21', '2025-11-26 19:58:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('administrador','gerente','recepcion','limpieza','mantenimiento') NOT NULL DEFAULT 'recepcion',
  `telefono` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `telefono`, `avatar`, `activo`, `ultimo_acceso`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Gerente', 'gerente@hotel.com', 'administrador', NULL, NULL, 1, NULL, NULL, '$2y$12$bGKuLjrgrSNlVsHWznwg1eIsbhiNn0WeOxiIRBT4QY1Mh0GYWdsZq', NULL, '2025-11-26 21:26:22', '2025-11-26 21:26:22'),
(2, 'Recepcion', 'recepcion@hotel.com', 'recepcion', NULL, NULL, 1, NULL, NULL, '$2y$12$bGKuLjrgrSNlVsHWznwg1eIsbhiNn0WeOxiIRBT4QY1Mh0GYWdsZq', NULL, '2025-11-26 21:26:23', '2025-11-26 21:26:23'),
(3, 'maxxx', 'maxjpr7@gmail.com', 'administrador', NULL, NULL, 1, NULL, NULL, '$2y$12$bGKuLjrgrSNlVsHWznwg1eIsbhiNn0WeOxiIRBT4QY1Mh0GYWdsZq', NULL, '2025-12-03 19:23:40', '2025-12-03 19:23:40');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_dashboard_ocupacion`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_dashboard_ocupacion` (
`habitaciones_ocupadas` bigint(21)
,`habitaciones_disponibles` bigint(21)
,`habitaciones_mantenimiento` bigint(21)
,`habitaciones_limpieza` bigint(21)
,`checkins_hoy` bigint(21)
,`llegadas_hoy` bigint(21)
,`salidas_hoy` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_habitaciones_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_habitaciones_completa` (
`id` bigint(20) unsigned
,`numero` varchar(255)
,`precio_por_noche` decimal(8,2)
,`estado` enum('disponible','ocupada','mantenimiento','limpieza')
,`tipo_habitacion` varchar(255)
,`capacidad` int(11)
,`tipo_descripcion` text
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_ingresos_mes_actual`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_ingresos_mes_actual` (
`total_mes` decimal(32,2)
,`num_pagos` bigint(21)
,`promedio_pago` decimal(14,6)
,`metodo_pago` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_reservas_activas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_reservas_activas` (
`id` bigint(20) unsigned
,`fecha_entrada` date
,`fecha_salida` date
,`total_precio` decimal(10,2)
,`estado` varchar(50)
,`cliente_nombre` varchar(511)
,`cliente_telefono` varchar(255)
,`cliente_email` varchar(255)
,`habitacion_numero` varchar(255)
,`tipo_habitacion` varchar(255)
,`num_noches` int(7)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_roles_permisos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_roles_permisos` (
`rol` varchar(13)
,`permiso` varchar(100)
,`modulo` varchar(50)
,`descripcion` mediumtext
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_dashboard_ocupacion`
--
DROP TABLE IF EXISTS `v_dashboard_ocupacion`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_dashboard_ocupacion`  AS SELECT (select count(0) from `habitaciones` where `habitaciones`.`estado` = 'ocupada') AS `habitaciones_ocupadas`, (select count(0) from `habitaciones` where `habitaciones`.`estado` = 'disponible') AS `habitaciones_disponibles`, (select count(0) from `habitaciones` where `habitaciones`.`estado` = 'mantenimiento') AS `habitaciones_mantenimiento`, (select count(0) from `habitaciones` where `habitaciones`.`estado` = 'limpieza') AS `habitaciones_limpieza`, (select count(0) from `reservas` where `reservas`.`estado` = 'checkin') AS `checkins_hoy`, (select count(0) from `reservas` where `reservas`.`fecha_entrada` = curdate()) AS `llegadas_hoy`, (select count(0) from `reservas` where `reservas`.`fecha_salida` = curdate()) AS `salidas_hoy` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_habitaciones_completa`
--
DROP TABLE IF EXISTS `v_habitaciones_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_habitaciones_completa`  AS SELECT `h`.`id` AS `id`, `h`.`numero` AS `numero`, `h`.`precio_por_noche` AS `precio_por_noche`, `h`.`estado` AS `estado`, `th`.`nombre` AS `tipo_habitacion`, `th`.`capacidad` AS `capacidad`, `th`.`descripcion` AS `tipo_descripcion` FROM (`habitaciones` `h` join `tipo_habitaciones` `th` on(`h`.`tipo_habitacion_id` = `th`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_ingresos_mes_actual`
--
DROP TABLE IF EXISTS `v_ingresos_mes_actual`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_ingresos_mes_actual`  AS SELECT sum(`pagos`.`monto`) AS `total_mes`, count(0) AS `num_pagos`, avg(`pagos`.`monto`) AS `promedio_pago`, `pagos`.`metodo_pago` AS `metodo_pago` FROM `pagos` WHERE month(`pagos`.`fecha_pago`) = month(curdate()) AND year(`pagos`.`fecha_pago`) = year(curdate()) AND `pagos`.`estado` = 'completado' GROUP BY `pagos`.`metodo_pago` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_reservas_activas`
--
DROP TABLE IF EXISTS `v_reservas_activas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_reservas_activas`  AS SELECT `r`.`id` AS `id`, `r`.`fecha_entrada` AS `fecha_entrada`, `r`.`fecha_salida` AS `fecha_salida`, `r`.`total_precio` AS `total_precio`, `r`.`estado` AS `estado`, concat(`c`.`nombre`,' ',`c`.`apellido`) AS `cliente_nombre`, `c`.`telefono` AS `cliente_telefono`, `c`.`email` AS `cliente_email`, `h`.`numero` AS `habitacion_numero`, `th`.`nombre` AS `tipo_habitacion`, to_days(`r`.`fecha_salida`) - to_days(`r`.`fecha_entrada`) AS `num_noches` FROM (((`reservas` `r` join `clientes` `c` on(`r`.`cliente_id` = `c`.`id`)) join `habitaciones` `h` on(`r`.`habitacion_id` = `h`.`id`)) join `tipo_habitaciones` `th` on(`h`.`tipo_habitacion_id` = `th`.`id`)) WHERE `r`.`estado` in ('confirmada','checkin','pendiente') ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_roles_permisos`
--
DROP TABLE IF EXISTS `v_roles_permisos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_roles_permisos`  AS SELECT DISTINCT 'administrador' AS `rol`, `p`.`nombre` AS `permiso`, `p`.`modulo` AS `modulo`, `p`.`descripcion` AS `descripcion` FROM `permisos` AS `p` WHERE find_in_set('administrador',`p`.`roles_permitidos`) > 0union allselect distinct 'gerente' AS `rol`,`p`.`nombre` AS `permiso`,`p`.`modulo` AS `modulo`,`p`.`descripcion` AS `descripcion` from `permisos` `p` where find_in_set('gerente',`p`.`roles_permitidos`) > 0 union all select distinct 'recepcion' AS `rol`,`p`.`nombre` AS `permiso`,`p`.`modulo` AS `modulo`,`p`.`descripcion` AS `descripcion` from `permisos` `p` where find_in_set('recepcion',`p`.`roles_permitidos`) > 0 union all select distinct 'limpieza' AS `rol`,`p`.`nombre` AS `permiso`,`p`.`modulo` AS `modulo`,`p`.`descripcion` AS `descripcion` from `permisos` `p` where find_in_set('limpieza',`p`.`roles_permitidos`) > 0 union all select distinct 'mantenimiento' AS `rol`,`p`.`nombre` AS `permiso`,`p`.`modulo` AS `modulo`,`p`.`descripcion` AS `descripcion` from `permisos` `p` where find_in_set('mantenimiento',`p`.`roles_permitidos`) > 0  ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auditoria_user_id_foreign` (`user_id`),
  ADD KEY `idx_auditoria_tabla_registro` (`tabla`,`registro_id`),
  ADD KEY `idx_auditoria_fecha` (`created_at`),
  ADD KEY `idx_auditoria_user_fecha` (`user_id`,`created_at`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clientes_email_unique` (`email`),
  ADD UNIQUE KEY `clientes_dni_unique` (`dni`),
  ADD KEY `idx_clientes_nombre` (`nombre`,`apellido`);

--
-- Indices de la tabla `estancias`
--
ALTER TABLE `estancias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `estancias_reserva_id_unique` (`reserva_id`),
  ADD KEY `idx_estancias_estado` (`estado`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `habitaciones_numero_unique` (`numero`),
  ADD KEY `habitaciones_tipo_habitacion_id_foreign` (`tipo_habitacion_id`),
  ADD KEY `idx_habitaciones_estado` (`estado`);

--
-- Indices de la tabla `historial_estados_habitacion`
--
ALTER TABLE `historial_estados_habitacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historial_habitacion_id_foreign` (`habitacion_id`),
  ADD KEY `historial_usuario_id_foreign` (`usuario_id`),
  ADD KEY `idx_historial_fecha` (`created_at`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pagos_reserva_id_foreign` (`reserva_id`),
  ADD KEY `idx_pagos_fecha` (`fecha_pago`),
  ADD KEY `pagos_usuario_id_foreign` (`usuario_id`),
  ADD KEY `idx_pagos_estado` (`estado`),
  ADD KEY `idx_pagos_usuario` (`usuario_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permisos_nombre_unique` (`nombre`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservas_cliente_id_foreign` (`cliente_id`),
  ADD KEY `reservas_habitacion_id_foreign` (`habitacion_id`),
  ADD KEY `idx_reservas_estado` (`estado`),
  ADD KEY `idx_reservas_fechas` (`fecha_entrada`,`fecha_salida`),
  ADD KEY `reservas_usuario_id_foreign` (`usuario_id`),
  ADD KEY `idx_reservas_usuario` (`usuario_id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `servicios_nombre_unique` (`nombre`),
  ADD KEY `idx_servicios_precio` (`precio`);

--
-- Indices de la tabla `servicio_detalles`
--
ALTER TABLE `servicio_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servicio_detalles_reserva_id_foreign` (`reserva_id`),
  ADD KEY `servicio_detalles_servicio_id_foreign` (`servicio_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tarifas_especiales`
--
ALTER TABLE `tarifas_especiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarifas_tipo_habitacion_id_foreign` (`tipo_habitacion_id`),
  ADD KEY `idx_tarifas_fechas` (`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `tipo_habitaciones`
--
ALTER TABLE `tipo_habitaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_habitaciones_nombre_unique` (`nombre`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estancias`
--
ALTER TABLE `estancias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `historial_estados_habitacion`
--
ALTER TABLE `historial_estados_habitacion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `servicio_detalles`
--
ALTER TABLE `servicio_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tarifas_especiales`
--
ALTER TABLE `tarifas_especiales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipo_habitaciones`
--
ALTER TABLE `tipo_habitaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `estancias`
--
ALTER TABLE `estancias`
  ADD CONSTRAINT `estancias_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`);

--
-- Filtros para la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD CONSTRAINT `habitaciones_tipo_habitacion_id_foreign` FOREIGN KEY (`tipo_habitacion_id`) REFERENCES `tipo_habitaciones` (`id`);

--
-- Filtros para la tabla `historial_estados_habitacion`
--
ALTER TABLE `historial_estados_habitacion`
  ADD CONSTRAINT `historial_habitacion_id_foreign` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`id`),
  ADD CONSTRAINT `historial_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `reservas_habitacion_id_foreign` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`id`);

--
-- Filtros para la tabla `servicio_detalles`
--
ALTER TABLE `servicio_detalles`
  ADD CONSTRAINT `servicio_detalles_reserva_id_foreign` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`),
  ADD CONSTRAINT `servicio_detalles_servicio_id_foreign` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`);

--
-- Filtros para la tabla `tarifas_especiales`
--
ALTER TABLE `tarifas_especiales`
  ADD CONSTRAINT `tarifas_tipo_habitacion_id_foreign` FOREIGN KEY (`tipo_habitacion_id`) REFERENCES `tipo_habitaciones` (`id`);

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `evt_actualizar_reservas_vencidas` ON SCHEDULE EVERY 1 DAY STARTS '2025-12-03 09:22:19' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
  UPDATE reservas
  SET estado = 'cancelada', 
      motivo_cancelacion = 'Reserva vencida automáticamente'
  WHERE estado = 'pendiente'
    AND fecha_entrada < CURDATE();
END$$

CREATE DEFINER=`root`@`localhost` EVENT `evt_limpiar_auditoria` ON SCHEDULE EVERY 1 MONTH STARTS '2025-12-03 09:22:19' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
  DELETE FROM auditoria
  WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

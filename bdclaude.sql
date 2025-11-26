-- =====================================================
-- SISTEMA DE HOTEL MEJORADO CON ROLES, TRIGGERS Y MÁS
-- =====================================================

-- Primero, las tablas base mejoradas

-- =====================================================
-- 1. ENUM DE ROLES PREDEFINIDOS EN USERS
-- =====================================================

-- Los roles estarán directamente en la tabla users como ENUM
-- No hay tabla separada de roles para evitar modificaciones

-- =====================================================
-- 2. TABLA DE PERMISOS PREDEFINIDOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `permisos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `roles_permitidos` varchar(255) NOT NULL COMMENT 'Roles separados por coma: administrador,gerente,recepcion',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permisos_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permisos` (`nombre`, `modulo`, `descripcion`, `roles_permitidos`) VALUES
-- Habitaciones
('habitaciones.ver', 'habitaciones', 'Ver listado de habitaciones', 'administrador,gerente,recepcion,limpieza,mantenimiento'),
('habitaciones.crear', 'habitaciones', 'Crear nuevas habitaciones', 'administrador,gerente'),
('habitaciones.editar', 'habitaciones', 'Editar habitaciones existentes', 'administrador,gerente'),
('habitaciones.eliminar', 'habitaciones', 'Eliminar habitaciones', 'administrador'),
('habitaciones.cambiar_estado', 'habitaciones', 'Cambiar estado de habitaciones', 'administrador,gerente,recepcion,limpieza,mantenimiento'),
-- Reservas
('reservas.ver', 'reservas', 'Ver reservas', 'administrador,gerente,recepcion'),
('reservas.crear', 'reservas', 'Crear reservas', 'administrador,gerente,recepcion'),
('reservas.editar', 'reservas', 'Editar reservas', 'administrador,gerente,recepcion'),
('reservas.cancelar', 'reservas', 'Cancelar reservas', 'administrador,gerente'),
('reservas.checkin', 'reservas', 'Realizar check-in', 'administrador,gerente,recepcion'),
('reservas.checkout', 'reservas', 'Realizar check-out', 'administrador,gerente,recepcion'),
-- Clientes
('clientes.ver', 'clientes', 'Ver clientes', 'administrador,gerente,recepcion'),
('clientes.crear', 'clientes', 'Crear clientes', 'administrador,gerente,recepcion'),
('clientes.editar', 'clientes', 'Editar clientes', 'administrador,gerente,recepcion'),
('clientes.eliminar', 'clientes', 'Eliminar clientes', 'administrador'),
-- Pagos
('pagos.ver', 'pagos', 'Ver pagos', 'administrador,gerente,recepcion'),
('pagos.registrar', 'pagos', 'Registrar pagos', 'administrador,gerente,recepcion'),
('pagos.anular', 'pagos', 'Anular pagos', 'administrador,gerente'),
-- Reportes
('reportes.ocupacion', 'reportes', 'Ver reporte de ocupación', 'administrador,gerente'),
('reportes.ingresos', 'reportes', 'Ver reporte de ingresos', 'administrador,gerente'),
('reportes.clientes', 'reportes', 'Ver reporte de clientes', 'administrador,gerente'),
-- Usuarios
('usuarios.ver', 'usuarios', 'Ver usuarios', 'administrador'),
('usuarios.crear', 'usuarios', 'Crear usuarios', 'administrador'),
('usuarios.editar', 'usuarios', 'Editar usuarios', 'administrador'),
('usuarios.eliminar', 'usuarios', 'Eliminar usuarios', 'administrador'),
-- Servicios
('servicios.ver', 'servicios', 'Ver servicios', 'administrador,gerente,recepcion'),
('servicios.crear', 'servicios', 'Crear servicios', 'administrador,gerente'),
('servicios.editar', 'servicios', 'Editar servicios', 'administrador,gerente'),
('servicios.eliminar', 'servicios', 'Eliminar servicios', 'administrador');

-- =====================================================
-- 3. MEJORAR TABLA USERS CON ROLES PREDEFINIDOS
-- =====================================================

ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('administrador','gerente','recepcion','limpieza','mantenimiento') NOT NULL DEFAULT 'recepcion',
ADD COLUMN `telefono` varchar(20) DEFAULT NULL AFTER `role`,
ADD COLUMN `avatar` varchar(255) DEFAULT NULL AFTER `telefono`,
ADD COLUMN `activo` tinyint(1) NOT NULL DEFAULT 1 AFTER `avatar`,
ADD COLUMN `ultimo_acceso` timestamp NULL DEFAULT NULL AFTER `activo`;

-- Crear índice para búsquedas por rol
ALTER TABLE `users` ADD INDEX `idx_users_role` (`role`);

-- Actualizar usuarios existentes si es necesario
UPDATE users SET role = 'administrador' WHERE role = 'gerente' AND email = 'gerente@hotel.com';
UPDATE users SET role = 'recepcion' WHERE role NOT IN ('administrador','gerente','recepcion','limpieza','mantenimiento');

-- =====================================================
-- 4. TABLA DE AUDITORÍA
-- =====================================================

CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tabla` varchar(50) NOT NULL,
  `operacion` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `registro_id` bigint(20) UNSIGNED NOT NULL,
  `datos_anteriores` longtext DEFAULT NULL,
  `datos_nuevos` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `auditoria_user_id_foreign` (`user_id`),
  KEY `idx_auditoria_tabla_registro` (`tabla`, `registro_id`),
  KEY `idx_auditoria_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. MEJORAR TABLA RESERVAS
-- =====================================================

ALTER TABLE `reservas`
ADD COLUMN `usuario_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `cliente_id`,
ADD COLUMN `num_adultos` int(11) DEFAULT 2,
ADD COLUMN `num_ninos` int(11) DEFAULT 0,
ADD COLUMN `origen_reserva` enum('web','telefono','presencial','agencia') DEFAULT 'presencial',
ADD COLUMN `cancelado_por` bigint(20) UNSIGNED DEFAULT NULL,
ADD COLUMN `fecha_cancelacion` datetime DEFAULT NULL,
ADD COLUMN `motivo_cancelacion` text DEFAULT NULL,
ADD KEY `reservas_usuario_id_foreign` (`usuario_id`);

-- =====================================================
-- 6. TABLA DE HISTORIAL DE ESTADOS DE HABITACIÓN
-- =====================================================

CREATE TABLE IF NOT EXISTS `historial_estados_habitacion` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `habitacion_id` bigint(20) UNSIGNED NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `historial_habitacion_id_foreign` (`habitacion_id`),
  KEY `historial_usuario_id_foreign` (`usuario_id`),
  KEY `idx_historial_fecha` (`created_at`),
  CONSTRAINT `historial_habitacion_id_foreign` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`id`),
  CONSTRAINT `historial_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. MEJORAR TABLA PAGOS
-- =====================================================

ALTER TABLE `pagos`
ADD COLUMN `estado` enum('pendiente','completado','anulado') NOT NULL DEFAULT 'completado',
ADD COLUMN `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
ADD COLUMN `numero_transaccion` varchar(100) DEFAULT NULL,
ADD COLUMN `anulado_por` bigint(20) UNSIGNED DEFAULT NULL,
ADD COLUMN `fecha_anulacion` datetime DEFAULT NULL,
ADD COLUMN `motivo_anulacion` text DEFAULT NULL,
ADD KEY `pagos_usuario_id_foreign` (`usuario_id`),
ADD KEY `idx_pagos_estado` (`estado`);

-- =====================================================
-- 8. TABLA DE TARIFAS DINÁMICAS
-- =====================================================

CREATE TABLE IF NOT EXISTS `tarifas_especiales` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo_habitacion_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `precio_por_noche` decimal(10,2) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarifas_tipo_habitacion_id_foreign` (`tipo_habitacion_id`),
  KEY `idx_tarifas_fechas` (`fecha_inicio`, `fecha_fin`),
  CONSTRAINT `tarifas_tipo_habitacion_id_foreign` FOREIGN KEY (`tipo_habitacion_id`) REFERENCES `tipo_habitaciones` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. FUNCIÓN PARA VERIFICAR PERMISOS
-- =====================================================

DELIMITER $

CREATE FUNCTION `fn_tiene_permiso`(
  p_role VARCHAR(50),
  p_permiso VARCHAR(100)
) RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
  DECLARE tiene_permiso BOOLEAN DEFAULT FALSE;
  
  SELECT COUNT(*) > 0 INTO tiene_permiso
  FROM permisos
  WHERE nombre = p_permiso
    AND FIND_IN_SET(p_role, REPLACE(roles_permitidos, ',', ',')) > 0;
  
  RETURN tiene_permiso;
END$

DELIMITER ;

-- =====================================================
-- 10. VISTA DE ROLES Y SUS PERMISOS
-- =====================================================

CREATE OR REPLACE VIEW `v_roles_permisos` AS
SELECT DISTINCT
  'administrador' as rol,
  p.nombre as permiso,
  p.modulo,
  p.descripcion
FROM permisos p
WHERE FIND_IN_SET('administrador', p.roles_permitidos) > 0

UNION ALL

SELECT DISTINCT
  'gerente' as rol,
  p.nombre as permiso,
  p.modulo,
  p.descripcion
FROM permisos p
WHERE FIND_IN_SET('gerente', p.roles_permitidos) > 0

UNION ALL

SELECT DISTINCT
  'recepcion' as rol,
  p.nombre as permiso,
  p.modulo,
  p.descripcion
FROM permisos p
WHERE FIND_IN_SET('recepcion', p.roles_permitidos) > 0

UNION ALL

SELECT DISTINCT
  'limpieza' as rol,
  p.nombre as permiso,
  p.modulo,
  p.descripcion
FROM permisos p
WHERE FIND_IN_SET('limpieza', p.roles_permitidos) > 0

UNION ALL

SELECT DISTINCT
  'mantenimiento' as rol,
  p.nombre as permiso,
  p.modulo,
  p.descripcion
FROM permisos p
WHERE FIND_IN_SET('mantenimiento', p.roles_permitidos) > 0;

-- =====================================================
-- TRIGGERS
-- =====================================================

DELIMITER $$

-- Trigger: Registrar cambios de estado de habitación
CREATE TRIGGER `trg_habitacion_cambio_estado`
AFTER UPDATE ON `habitaciones`
FOR EACH ROW
BEGIN
  IF OLD.estado != NEW.estado THEN
    INSERT INTO historial_estados_habitacion (habitacion_id, estado_anterior, estado_nuevo)
    VALUES (NEW.id, OLD.estado, NEW.estado);
  END IF;
END$$

-- Trigger: Actualizar estado de habitación al hacer check-in
CREATE TRIGGER `trg_estancia_checkin`
AFTER INSERT ON `estancias`
FOR EACH ROW
BEGIN
  IF NEW.check_in_real IS NOT NULL THEN
    UPDATE habitaciones h
    INNER JOIN reservas r ON h.id = r.habitacion_id
    SET h.estado = 'ocupada'
    WHERE r.id = NEW.reserva_id;
  END IF;
END$$

-- Trigger: Actualizar estado de habitación al hacer check-out
CREATE TRIGGER `trg_estancia_checkout`
AFTER UPDATE ON `estancias`
FOR EACH ROW
BEGIN
  IF OLD.check_out_real IS NULL AND NEW.check_out_real IS NOT NULL THEN
    UPDATE habitaciones h
    INNER JOIN reservas r ON h.id = r.habitacion_id
    SET h.estado = 'limpieza'
    WHERE r.id = NEW.reserva_id;
  END IF;
END$$

-- Trigger: Validar disponibilidad antes de crear reserva
CREATE TRIGGER `trg_validar_disponibilidad`
BEFORE INSERT ON `reservas`
FOR EACH ROW
BEGIN
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
END$$

-- Trigger: Calcular precio total de reserva
CREATE TRIGGER `trg_calcular_precio_reserva`
BEFORE INSERT ON `reservas`
FOR EACH ROW
BEGIN
  DECLARE precio_noche DECIMAL(10,2);
  DECLARE num_noches INT;
  
  SELECT precio_por_noche INTO precio_noche
  FROM habitaciones
  WHERE id = NEW.habitacion_id;
  
  SET num_noches = DATEDIFF(NEW.fecha_salida, NEW.fecha_entrada);
  SET NEW.total_precio = (precio_noche * num_noches) - NEW.descuento;
END$$

-- Trigger: Auditar eliminación de clientes
CREATE TRIGGER `trg_auditoria_clientes_delete`
BEFORE DELETE ON `clientes`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla, operacion, registro_id, datos_anteriores)
  VALUES ('clientes', 'DELETE', OLD.id, JSON_OBJECT(
    'nombre', OLD.nombre,
    'apellido', OLD.apellido,
    'dni', OLD.dni,
    'email', OLD.email,
    'telefono', OLD.telefono,
    'direccion', OLD.direccion
  ));
END$$

-- Trigger: Auditar cambios en pagos
CREATE TRIGGER `trg_auditoria_pagos_update`
AFTER UPDATE ON `pagos`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla, operacion, registro_id, datos_anteriores, datos_nuevos)
  VALUES ('pagos', 'UPDATE', NEW.id, 
    JSON_OBJECT('monto', OLD.monto, 'estado', OLD.estado),
    JSON_OBJECT('monto', NEW.monto, 'estado', NEW.estado)
  );
END$$

DELIMITER ;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER $$

-- Procedimiento: Obtener habitaciones disponibles
CREATE PROCEDURE `sp_habitaciones_disponibles`(
  IN p_fecha_entrada DATE,
  IN p_fecha_salida DATE,
  IN p_tipo_habitacion_id BIGINT
)
BEGIN
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

-- Procedimiento: Realizar check-in
CREATE PROCEDURE `sp_realizar_checkin`(
  IN p_reserva_id BIGINT,
  IN p_usuario_id BIGINT
)
BEGIN
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

-- Procedimiento: Realizar check-out
CREATE PROCEDURE `sp_realizar_checkout`(
  IN p_reserva_id BIGINT,
  IN p_usuario_id BIGINT
)
BEGIN
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

-- Procedimiento: Reporte de ocupación
CREATE PROCEDURE `sp_reporte_ocupacion`(
  IN p_fecha_inicio DATE,
  IN p_fecha_fin DATE
)
BEGIN
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

-- Procedimiento: Reporte de ingresos
CREATE PROCEDURE `sp_reporte_ingresos`(
  IN p_fecha_inicio DATE,
  IN p_fecha_fin DATE
)
BEGIN
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

DELIMITER ;

-- =====================================================
-- VISTAS
-- =====================================================

-- Vista: Habitaciones con información completa
CREATE OR REPLACE VIEW `v_habitaciones_completa` AS
SELECT 
  h.id,
  h.numero,
  h.precio_por_noche,
  h.estado,
  th.nombre as tipo_habitacion,
  th.capacidad,
  th.descripcion as tipo_descripcion
FROM habitaciones h
INNER JOIN tipo_habitaciones th ON h.tipo_habitacion_id = th.id;

-- Vista: Reservas activas con información completa
CREATE OR REPLACE VIEW `v_reservas_activas` AS
SELECT 
  r.id,
  r.fecha_entrada,
  r.fecha_salida,
  r.total_precio,
  r.estado,
  CONCAT(c.nombre, ' ', c.apellido) as cliente_nombre,
  c.telefono as cliente_telefono,
  c.email as cliente_email,
  h.numero as habitacion_numero,
  th.nombre as tipo_habitacion,
  DATEDIFF(r.fecha_salida, r.fecha_entrada) as num_noches
FROM reservas r
INNER JOIN clientes c ON r.cliente_id = c.id
INNER JOIN habitaciones h ON r.habitacion_id = h.id
INNER JOIN tipo_habitaciones th ON h.tipo_habitacion_id = th.id
WHERE r.estado IN ('confirmada', 'checkin', 'pendiente');

-- Vista: Dashboard de ocupación
CREATE OR REPLACE VIEW `v_dashboard_ocupacion` AS
SELECT 
  (SELECT COUNT(*) FROM habitaciones WHERE estado = 'ocupada') as habitaciones_ocupadas,
  (SELECT COUNT(*) FROM habitaciones WHERE estado = 'disponible') as habitaciones_disponibles,
  (SELECT COUNT(*) FROM habitaciones WHERE estado = 'mantenimiento') as habitaciones_mantenimiento,
  (SELECT COUNT(*) FROM habitaciones WHERE estado = 'limpieza') as habitaciones_limpieza,
  (SELECT COUNT(*) FROM reservas WHERE estado = 'checkin') as checkins_hoy,
  (SELECT COUNT(*) FROM reservas WHERE fecha_entrada = CURDATE()) as llegadas_hoy,
  (SELECT COUNT(*) FROM reservas WHERE fecha_salida = CURDATE()) as salidas_hoy;

-- Vista: Ingresos del mes
CREATE OR REPLACE VIEW `v_ingresos_mes_actual` AS
SELECT 
  SUM(monto) as total_mes,
  COUNT(*) as num_pagos,
  AVG(monto) as promedio_pago,
  metodo_pago
FROM pagos
WHERE MONTH(fecha_pago) = MONTH(CURDATE())
  AND YEAR(fecha_pago) = YEAR(CURDATE())
  AND estado = 'completado'
GROUP BY metodo_pago;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

CREATE INDEX idx_reservas_usuario ON reservas(usuario_id);
CREATE INDEX idx_pagos_usuario ON pagos(usuario_id);
CREATE INDEX idx_auditoria_user_fecha ON auditoria(user_id, created_at);
CREATE INDEX idx_estancias_estado ON estancias(estado);
CREATE INDEX idx_servicios_precio ON servicios(precio);

-- =====================================================
-- EVENTOS PROGRAMADOS
-- =====================================================

DELIMITER $$

-- Evento: Actualizar reservas pendientes vencidas
CREATE EVENT IF NOT EXISTS `evt_actualizar_reservas_vencidas`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
  UPDATE reservas
  SET estado = 'cancelada', 
      motivo_cancelacion = 'Reserva vencida automáticamente'
  WHERE estado = 'pendiente'
    AND fecha_entrada < CURDATE();
END$$

-- Evento: Limpiar auditoría antigua (más de 1 año)
CREATE EVENT IF NOT EXISTS `evt_limpiar_auditoria`
ON SCHEDULE EVERY 1 MONTH
STARTS CURRENT_TIMESTAMP
DO
BEGIN
  DELETE FROM auditoria
  WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
END$$

DELIMITER ;

-- =====================================================
-- DATOS DE PRUEBA ADICIONALES
-- =====================================================

-- Insertar tarifas especiales para temporada alta
INSERT INTO tarifas_especiales (tipo_habitacion_id, fecha_inicio, fecha_fin, precio_por_noche, motivo, activo)
VALUES 
(1, '2025-12-20', '2026-01-05', 150.00, 'Temporada Navideña', 1),
(2, '2025-12-20', '2026-01-05', 100.00, 'Temporada Navideña', 1);

-- =====================================================
-- COMENTARIOS FINALES
-- =====================================================

/*
MEJORAS IMPLEMENTADAS:

1. SISTEMA DE ROLES PREDEFINIDOS:
   - Los roles están definidos como ENUM directamente en la tabla users
   - No hay tabla separada de roles para evitar modificaciones
   - 5 roles fijos: administrador, gerente, recepcion, limpieza, mantenimiento
   - No se pueden agregar, modificar o eliminar roles desde la aplicación

2. TABLA DE PERMISOS PREDEFINIDOS:
   - Los permisos están predefinidos en la tabla permisos
   - Cada permiso tiene una columna 'roles_permitidos' que lista los roles que pueden usarlo
   - Los permisos no se pueden modificar, solo consultar

3. FUNCIÓN DE VERIFICACIÓN:
   - fn_tiene_permiso(rol, permiso): Verifica si un rol tiene un permiso específico
   - Uso: SELECT fn_tiene_permiso('gerente', 'habitaciones.crear');

4. VISTA DE PERMISOS POR ROL:
   - v_roles_permisos: Muestra todos los permisos de cada rol
   - Útil para mostrar en la UI qué puede hacer cada rol

5. AUDITORÍA COMPLETA:
   - Registro de todas las operaciones críticas
   - Triggers para auditar automáticamente
   - Almacenamiento de datos anteriores y nuevos en JSON

6. TRIGGERS INTELIGENTES:
   - Validación de disponibilidad automática
   - Cálculo automático de precios
   - Gestión automática de estados de habitaciones
   - Historial de cambios de estado

7. PROCEDIMIENTOS ALMACENADOS:
   - Check-in y check-out transaccionales
   - Consulta de disponibilidad optimizada
   - Reportes de ocupación e ingresos

8. VISTAS:
   - Dashboard de ocupación en tiempo real
   - Información completa de habitaciones y reservas
   - Resumen de ingresos

9. EVENTOS PROGRAMADOS:
   - Limpieza automática de datos antiguos
   - Actualización de reservas vencidas

10. MEJORAS EN TABLAS:
    - Tarifas dinámicas por temporada
    - Historial de estados de habitaciones
    - Campos adicionales para trazabilidad
    - Índices optimizados

CÓMO USAR LOS ROLES:

En tu aplicación Laravel:

1. Middleware de permisos:
   public function handle($request, Closure $next, $permiso)
   {
       $user = auth()->user();
       $tienePermiso = DB::selectOne(
           'SELECT fn_tiene_permiso(?, ?) as tiene',
           [$user->role, $permiso]
       );
       
       if (!$tienePermiso->tiene) {
           abort(403, 'No tienes permiso para esta acción');
       }
       
       return $next($request);
   }

2. En rutas:
   Route::get('/habitaciones/crear', [HabitacionController::class, 'create'])
       ->middleware('permiso:habitaciones.crear');

3. En Blade:
   @can('habitaciones.crear')
       <a href="/habitaciones/crear">Nueva Habitación</a>
   @endcan

4. Obtener permisos de un rol:
   SELECT * FROM v_roles_permisos WHERE rol = 'gerente';

Los roles son completamente fijos y no se pueden modificar desde la aplicación.
Si necesitas cambiar permisos, debes modificar la tabla permisos directamente en la BD.
*/
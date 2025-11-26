# Sistema de Roles - Hotel System

## ✅ Roles Implementados

El sistema cuenta con **4 roles** distintos, cada uno con permisos específicos:

### 1. 👔 Gerente (gerente)
**Acceso Total al Sistema**

**Permisos:**
- ✅ Todas las funciones de Recepción
- ✅ Gestión de Tipos de Habitación (CRUD completo)
- ✅ Gestión de Servicios (CRUD completo)
- ✅ Acceso a todos los Reportes:
  - Reporte General
  - Reporte de Ingresos
  - Reporte de Ocupación
  - Reporte de Servicios

**Usuario de Prueba:**
- Email: `gerente@hotel.com`
- Password: `password123`

---

### 2. 🏨 Recepción (recepcion)
**Operaciones Diarias del Hotel**

**Permisos:**
- ✅ Gestión de Clientes (CRUD completo)
- ✅ Gestión de Habitaciones (CRUD sin eliminar)
- ✅ Gestión de Reservas (CRUD completo)
- ✅ Proceso de Check-In
- ✅ Proceso de Check-Out
- ✅ Registro de Servicios Adicionales
- ✅ Gestión de Pagos
- ❌ NO puede gestionar tipos de habitación
- ❌ NO puede ver reportes
- ❌ NO puede gestionar servicios del catálogo

**Usuario de Prueba:**
- Email: `recepcion@hotel.com`
- Password: `password123`

---

### 3. 🧹 Limpieza (limpieza)
**Gestión de Estado de Habitaciones**

**Permisos:**
- ✅ Ver lista de habitaciones
- ✅ Ver estado de habitaciones (disponible, ocupada, limpieza, mantenimiento)
- 📝 Rutas específicas disponibles para expandir funcionalidad

**Usuario de Prueba:**
- Email: `limpieza@hotel.com`
- Password: `password123`

**Rutas Disponibles:**
- `/limpieza/habitaciones` - Ver habitaciones

---

### 4. 🔧 Mantenimiento (mantenimiento)
**Gestión de Mantenimiento de Habitaciones**

**Permisos:**
- ✅ Ver lista de habitaciones
- ✅ Ver estado de habitaciones
- 📝 Rutas específicas disponibles para expandir funcionalidad

**Usuario de Prueba:**
- Email: `mantenimiento@hotel.com`
- Password: `password123`

**Rutas Disponibles:**
- `/mantenimiento/habitaciones` - Ver habitaciones

---

## 🗄️ Estructura de Base de Datos

### Migración de Roles

La migración `2025_11_25_200859_add_role_to_users_table.php` agrega el campo `role` a la tabla `users`:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('role')->default('recepcion');
});
```

**Valores Permitidos:**
- `gerente`
- `recepcion`
- `limpieza`
- `mantenimiento`

**Default:** `recepcion` (cuando se registra un nuevo usuario)

---

## 🔐 Middleware de Roles

El sistema utiliza `RoleMiddleware` para controlar el acceso:

**Características:**
- ✅ Verifica autenticación del usuario
- ✅ El rol "gerente" tiene acceso a TODAS las rutas
- ✅ Otros roles solo acceden a sus rutas asignadas
- ❌ Si un usuario intenta acceder a una ruta no autorizada, recibe error 403

**Archivo:** `app/Http/Middleware/RoleMiddleware.php`

---

## 🚀 Seeders

### RolesAndUsersSeeder

Crea automáticamente los 4 usuarios de prueba con sus roles.

**Ejecutar Seeder:**
```bash
php artisan db:seed --class=RolesAndUsersSeeder
```

**Archivo:** `database/seeders/RolesAndUsersSeeder.php`

---

## 📋 Navegación por Rol

El menú de navegación (`resources/views/layouts/navigation.blade.php`) se adapta automáticamente según el rol:

### Menú para Gerente:
- Dashboard
- Clientes
- Habitaciones
- Reservas
- Pagos
- **Tipos de Habitación** (exclusivo)
- **Servicios** (exclusivo)
- **Reportes** (exclusivo)

### Menú para Recepción:
- Dashboard
- Clientes
- Habitaciones
- Reservas
- Pagos

### Menú para Limpieza:
- Dashboard
- (Menú simplificado)

### Menú para Mantenimiento:
- Dashboard
- (Menú simplificado)

---

## 🛣️ Rutas Protegidas

### Rutas de Gerente
```php
Route::middleware('role:gerente')->group(function () {
    Route::resource('tipo_habitaciones', TipoHabitacionController::class);
    Route::resource('servicios', ServicioController::class);
    Route::get('/reportes', [ReportesController::class, 'index']);
    Route::get('/reportes/general', [ReportesController::class, 'general']);
    Route::get('/reportes/ingresos', [ReportesController::class, 'ingresos']);
    Route::get('/reportes/ocupacion', [ReportesController::class, 'ocupacion']);
    Route::get('/reportes/servicios', [ReportesController::class, 'servicios']);
});
```

### Rutas de Recepción
```php
Route::middleware('role:recepcion')->group(function () {
    Route::resource('clientes', ClienteController::class);
    Route::resource('habitaciones', HabitacionController::class);
    Route::resource('reservas', ReservaController::class);
    Route::get('/checkin/{reserva}', [CheckInController::class, 'show']);
    Route::post('/checkin/{reserva}', [CheckInController::class, 'store']);
    Route::get('/checkout/{reserva}', [CheckOutController::class, 'show']);
    Route::post('/checkout/{reserva}', [CheckOutController::class, 'store']);
    // ... pagos y servicios detalle
});
```

### Rutas de Limpieza
```php
Route::middleware('role:limpieza')->group(function () {
    Route::get('/limpieza/habitaciones', [HabitacionController::class, 'index']);
    // Expandible con más funcionalidades
});
```

### Rutas de Mantenimiento
```php
Route::middleware('role:mantenimiento')->group(function () {
    Route::get('/mantenimiento/habitaciones', [HabitacionController::class, 'index']);
    // Expandible con más funcionalidades
});
```

---

## 📝 Notas Importantes

1. **Jerarquía de Permisos:** El gerente tiene acceso a TODO el sistema, incluyendo todas las funciones de recepción.

2. **Seguridad:** Todas las rutas están protegidas por el middleware `auth` + `role`.

3. **Extensibilidad:** Los roles de Limpieza y Mantenimiento tienen rutas base definidas, listas para agregar más funcionalidades específicas.

4. **Default Role:** Cuando un usuario se registra sin especificar rol, se le asigna automáticamente el rol `recepcion`.

---

## 🔄 Migrar Base de Datos

Si necesitas ejecutar las migraciones:

```bash
# Ejecutar todas las migraciones
php artisan migrate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Solo ejecutar el seeder de roles
php artisan db:seed --class=RolesAndUsersSeeder

# Refrescar base de datos (CUIDADO: Elimina todos los datos)
php artisan migrate:fresh --seed
```

---

## 🧪 Probar los Roles

1. **Login con diferentes usuarios:**
   - Gerente: `gerente@hotel.com` / `password123`
   - Recepción: `recepcion@hotel.com` / `password123`
   - Limpieza: `limpieza@hotel.com` / `password123`
   - Mantenimiento: `mantenimiento@hotel.com` / `password123`

2. **Verificar menú de navegación:**
   - El menú debe cambiar según el rol

3. **Intentar acceder a rutas restringidas:**
   - Por ejemplo, si inicias sesión como "Recepción" e intentas acceder a `/reportes`, deberías recibir error 403

---

## 🎯 Próximas Mejoras para Limpieza y Mantenimiento

### Sugerencias para Rol Limpieza:
- ✨ Dashboard con habitaciones pendientes de limpiar
- ✨ Marcar habitación como "Limpia" o "En proceso"
- ✨ Historial de limpieza
- ✨ Reporte de habitaciones por estado

### Sugerencias para Rol Mantenimiento:
- ✨ Dashboard con habitaciones en mantenimiento
- ✨ Marcar habitación como "En mantenimiento" o "Reparada"
- ✨ Registro de trabajos de mantenimiento
- ✨ Reporte de incidencias

---

## ✅ Verificación Completa

- [x] 4 roles implementados (gerente, recepcion, limpieza, mantenimiento)
- [x] Migración `add_role_to_users_table` creada
- [x] Campo `role` agregado al modelo User (fillable)
- [x] Middleware RoleMiddleware funcionando
- [x] Seeder RolesAndUsersSeeder creado
- [x] 4 usuarios de prueba creados
- [x] Rutas protegidas por rol
- [x] Navegación dinámica por rol
- [x] Gerente con acceso total

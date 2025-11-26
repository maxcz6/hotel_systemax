# 🏨 Hotel Systemax - Documentación Completa del Sistema

> **Versión:** 1.0  
> **Framework:** Laravel 11  
> **PHP:** 8.2+  
> **Base de Datos:** MySQL/MariaDB  
> **Frontend:** CSS Nativo + JavaScript Vanilla (Sin Tailwind, Sin Alpine.js)

---

## 📋 Índice

1. [Características Principales](#características-principales)
2. [Sistema de Roles y Permisos](#sistema-de-roles-y-permisos)
3. [Estructura de Base de Datos](#estructura-de-base-de-datos)
4. [Módulos Implementados](#módulos-implementados)
5. [Instalación y Configuración](#instalación-y-configuración)
6. [Credenciales de Acceso](#credenciales-de-acceso)
7. [Arquitectura del Sistema](#arquitectura-del-sistema)
8. [Funcionalidades Especiales](#funcionalidades-especiales)
9. [Reportes Implementados](#reportes-implementados)
10. [Testing](#testing)
11. [Comandos Útiles](#comandos-útiles)

---

## 🚀 Características Principales

### ✅ Gestión Integral de Hotel
- **Clientes**: CRUD completo con validación de documentos (DNI 8 dígitos, RUC 11 dígitos para Perú)
- **Habitaciones**: Control de estados (Disponible, Ocupada, Limpieza, Mantenimiento)
- **Tipos de Habitación**: Simple, Doble, Suite con capacidad y precios configurables
- **Reservas**: Flujo completo (Pendiente → Confirmada → Check-in → Check-out)
- **Check-in/Check-out**: Procesos automatizados con cambio de estados
- **Servicios Adicionales**: Minibar, lavandería, room service, etc.
- **Pagos**: Múltiples métodos (Efectivo, Tarjeta, Transferencia)
- **Reportes**: 4 tipos de reportes con análisis detallado

### ✅ Seguridad y Control
- Autenticación con Laravel Breeze
- Sistema de roles y permisos con middleware
- Validación de formularios (Frontend + Backend)
- Protección CSRF en todas las peticiones

### ✅ UI/UX Moderna
- Diseño responsive 100% CSS nativo
- Navegación dinámica por roles
- Mensajes de éxito/error
- Sin dependencias de frontend (No Tailwind, No Alpine.js)

---

## 👥 Sistema de Roles y Permisos

### **4 ROLES IMPLEMENTADOS**

#### 1. 👔 **Gerente** (`gerente`)
**Acceso Total al Sistema**

**Permisos:**
- ✅ **Todas las funciones de Recepción** (hereda todos sus permisos)
- ✅ **Gestión de Tipos de Habitación** (CRUD completo)
- ✅ **Gestión de Servicios** (CRUD completo)
- ✅ **Acceso a TODOS los Reportes**:
  - Reporte General
  - Reporte de Ingresos
  - Reporte de Ocupación
  - Reporte de Servicios

**Funcionalidades Exclusivas:**
- ✅ Crear Tipo de Habitación inline al crear habitación
- ✅ Eliminar tipos de habitación (con validación de habitaciones asociadas)
- ✅ Ver todos los módulos de reportes

**Usuario de Prueba:**
```
Email: gerente@hotel.com
Password: password123
```

---

#### 2. 🏨 **Recepción** (`recepcion`)
**Operaciones Diarias del Hotel**

**Permisos:**
- ✅ **Gestión de Clientes** (CRUD completo)
- ✅ **Gestión de Habitaciones** (CRUD sin eliminar)
- ✅ **Gestión de Reservas** (CRUD completo)
- ✅ **Proceso de Check-In**
- ✅ **Proceso de Check-Out**
- ✅ **Registro de Servicios Adicionales**
- ✅ **Gestión de Pagos** (Crear y listar)

**Restricciones:**
- ❌ NO puede gestionar tipos de habitación
- ❌ NO puede ver reportes
- ❌ NO puede gestionar el catálogo de servicios
- ❌ NO puede eliminar habitaciones

**Usuario de Prueba:**
```
Email: recepcion@hotel.com
Password: password123
```

---

#### 3. 🧹 **Limpieza** (`limpieza`)
**Gestión de Estado de Habitaciones**

**Permisos:**
- ✅ Ver lista de habitaciones
- ✅ Ver estado de habitaciones (disponible, ocupada, limpieza, mantenimiento)

**Usuario de Prueba:**
```
Email: limpieza@hotel.com
Password: password123
```

**Rutas Disponibles:**
- `/limpieza/habitaciones` - Ver habitaciones

**Nota:** Funcionalidades específicas pendientes de implementación (marcar como limpia, historial, etc.)

---

#### 4. 🔧 **Mantenimiento** (`mantenimiento`)
**Gestión de Mantenimiento de Habitaciones**

**Permisos:**
- ✅ Ver lista de habitaciones
- ✅ Ver estado de habitaciones

**Usuario de Prueba:**
```
Email: mantenimiento@hotel.com
Password: password123
```

**Rutas Disponibles:**
- `/mantenimiento/habitaciones` - Ver habitaciones

**Nota:** Funcionalidades específicas pendientes de implementación (marcar en mantenimiento, registro de trabajos, etc.)

---

### 🔐 Middleware de Roles

**Archivo:** `app/Http/Middleware/RoleMiddleware.php`

**Características:**
- ✅ Verifica autenticación del usuario
- ✅ El rol "gerente" tiene acceso a TODAS las rutas
- ✅ Otros roles solo acceden a sus rutas asignadas
- ❌ Si un usuario intenta acceder a una ruta no autorizada, recibe error **403 Unauthorized**

**Registro en:** `bootstrap/app.php`

---

### 🗺️ Rutas Protegidas por Rol

#### **Rutas de Gerente**
```php
Route::middleware(['auth', 'role:gerente'])->group(function () {
    // Tipos de Habitación
    Route::resource('tipo_habitaciones', TipoHabitacionController::class);
    
    // Servicios
    Route::resource('servicios', ServicioController::class);
    
    // Reportes
    Route::get('/reportes', [ReportesController::class, 'index']);
    Route::get('/reportes/general', [ReportesController::class, 'general']);
    Route::get('/reportes/ingresos', [ReportesController::class, 'ingresos']);
    Route::get('/reportes/ocupacion', [ReportesController::class, 'ocupacion']);
    Route::get('/reportes/servicios', [ReportesController::class, 'servicios']);
    
    // + TODAS las rutas de recepción
});
```

#### **Rutas de Recepción**
```php
Route::middleware(['auth', 'role:recepcion'])->group(function () {
    // Clientes
    Route::resource('clientes', ClienteController::class);
    
    // Habitaciones (sin destroy)
    Route::resource('habitaciones', HabitacionController::class)->except(['destroy']);
    
    // Reservas
    Route::resource('reservas', ReservaController::class);
    
    // Check-in/Check-out
    Route::get('/checkin/{reserva}', [CheckInController::class, 'show']);
    Route::post('/checkin/{reserva}', [CheckInController::class, 'store']);
    Route::get('/checkout/{reserva}', [CheckOutController::class, 'show']);
    Route::post('/checkout/{reserva}', [CheckOutController::class, 'store']);
    
    // Servicios Detalle
    Route::resource('servicio_detalle', ServicioDetalleController::class);
    
    // Pagos
    Route::resource('pagos', PagoController::class);
});
```

#### **Rutas de Limpieza**
```php
Route::middleware(['auth', 'role:limpieza'])->group(function () {
    Route::get('/limpieza/habitaciones', [HabitacionController::class, 'index']);
});
```

#### **Rutas de Mantenimiento**
```php
Route::middleware(['auth', 'role:mantenimiento'])->group(function () {
    Route::get('/mantenimiento/habitaciones', [HabitacionController::class, 'index']);
});
```

---

### 🧭 Navegación Dinámica por Rol

**Archivo:** `resources/views/layouts/navigation.blade.php`

El menú cambia automáticamente según el rol del usuario:

#### **Menú para Gerente:**
- Dashboard
- Clientes
- Habitaciones
- Reservas
- Pagos
- **Tipos de Habitación** ⭐ (exclusivo)
- **Servicios** ⭐ (exclusivo)
- **Reportes** ⭐ (exclusivo)

#### **Menú para Recepción:**
- Dashboard
- Clientes
- Habitaciones
- Reservas
- Pagos

#### **Menú para Limpieza:**
- Dashboard
- Habitaciones (solo lectura)

#### **Menú para Mantenimiento:**
- Dashboard
- Habitaciones (solo lectura)

---

## 🗄️ Estructura de Base de Datos

### **10 Migraciones Principales**

1. ✅ `create_users_table` - Tabla de usuarios base
2. ✅ `add_role_to_users_table` - Campo role agregado
3. ✅ `create_tipo_habitaciones_table` - Tipos de habitación
4. ✅ `create_habitaciones_table` - Habitaciones
5. ✅ `create_clientes_table` - Clientes
6. ✅ `create_reservas_table` - Reservas
7. ✅ `create_estancias_table` - Estancias (check-in/out)
8. ✅ `create_servicios_table` - Servicios
9. ✅ `create_servicio_detalles_table` - Detalle de servicios
10. ✅ `create_pagos_table` - Pagos

**Total:** 13 migraciones (10 principales + 3 por defecto de Laravel: cache, jobs, sessions)

---

### **Migración de Roles**

**Archivo:** `database/migrations/2025_11_25_200859_add_role_to_users_table.php`

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

### **Modelo User Actualizado**

**Archivo:** `app/Models/User.php`

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role', // ✅ AGREGADO
];
```

---

### **Seeders Implementados**

#### **RolesAndUsersSeeder**

Crea automáticamente **4 usuarios de prueba**:

| Rol | Email | Password | Estado |
|-----|-------|----------|--------|
| Gerente | gerente@hotel.com | password123 | ✅ Creado |
| Recepción | recepcion@hotel.com | password123 | ✅ Creado |
| Limpieza | limpieza@hotel.com | password123 | ✅ Creado |
| Mantenimiento | mantenimiento@hotel.com | password123 | ✅ Creado |

**Ejecutar Seeder:**
```bash
php artisan db:seed --class=RolesAndUsersSeeder
```

**Archivo:** `database/seeders/RolesAndUsersSeeder.php`

---

## 📦 Módulos Implementados

### **1. Modelos (Models)**

Todos los modelos con relaciones Eloquent completas:

| Modelo | Descripción | Relaciones |
|--------|-------------|------------|
| **User** | Usuario del sistema | hasMany(Reserva) |
| **Cliente** | Clientes del hotel | hasMany(Reserva) |
| **TipoHabitacion** | Tipos de habitaciones | hasMany(Habitacion) |
| **Habitacion** | Habitaciones del hotel | belongsTo(TipoHabitacion), hasMany(Reserva) |
| **Reserva** | Reservas de habitaciones | belongsTo(Cliente), belongsTo(Habitacion), hasOne(Estancia), hasMany(Pago) |
| **Estancia** | Check-in/Check-out | belongsTo(Reserva), hasMany(ServicioDetalle) |
| **Servicio** | Servicios adicionales | hasMany(ServicioDetalle) |
| **ServicioDetalle** | Detalle de servicios usados | belongsTo(Servicio), belongsTo(Estancia) |
| **Pago** | Pagos realizados | belongsTo(Reserva) |

---

### **2. Controladores (Controllers)**

Todos los controladores resource completos:

#### **Gestión General**
- **DashboardController** - Panel principal con estadísticas
- **ClienteController** - CRUD completo de clientes
- **TipoHabitacionController** - CRUD tipos de habitación
- **HabitacionController** - CRUD de habitaciones
- **ReservaController** - CRUD de reservas con validación de disponibilidad

#### **Procesos Operativos**
- **CheckInController** - Proceso de check-in
- **CheckOutController** - Proceso de check-out
- **ServicioController** - CRUD de servicios
- **ServicioDetalleController** - Registro de servicios usados durante estancia
- **PagoController** - Registro y gestión de pagos

#### **Reportes**
- **ReportesController** - 4 tipos de reportes (general, ingresos, ocupación, servicios)

---

### **3. Vistas (Blade Templates)**

#### **Layouts**
- `layouts/app.blade.php` - Layout principal autenticado
- `layouts/guest.blade.php` - Layout para visitantes
- `layouts/navigation.blade.php` - Navegación con menú por roles

#### **Módulos Principales**
```
resources/views/
├── clientes/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── tipo_habitaciones/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── habitaciones/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── reservas/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── servicios/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── servicio_detalle/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── pagos/
│   ├── index.blade.php
│   └── create.blade.php
├── checkin/
│   └── form.blade.php
├── checkout/
│   └── form.blade.php
└── reportes/
    ├── index.blade.php
    ├── general.blade.php
    ├── ingresos.blade.php
    ├── ocupacion.blade.php
    └── servicios.blade.php
```

#### **Componentes Reutilizables**
- `components/text-input.blade.php`
- `components/primary-button.blade.php`
- `components/secondary-button.blade.php`
- `components/danger-button.blade.php`
- `components/input-label.blade.php`
- `components/input-error.blade.php`
- `components/dropdown.blade.php`
- `components/dropdown-link.blade.php`

---

## 🎨 Estilos y JavaScript

### **CSS Nativo**

**Archivo:** `public/css/style.css`

**Incluye:**
- Reset y estilos base
- Componentes: cards, botones, formularios, tablas
- Navegación y menús
- Alertas y mensajes
- Modales y dropdowns
- Grid para dashboard
- Utilidades responsivas
- **NO usa Tailwind CSS** - Todo CSS nativo

### **JavaScript Nativo**

**Archivo:** `public/js/script.js`

**Funcionalidades:**
- Control de dropdowns
- Interacciones de modales
- Toggle de campos dinámicos (crear tipo habitación inline)
- **NO usa Alpine.js** - Todo JavaScript vanilla

---

## ✨ Funcionalidades Especiales

### **1. Crear Tipo de Habitación Inline** ⭐

**Exclusivo para Gerentes**

Cuando un gerente está creando una habitación en `/habitaciones/create`, puede crear un nuevo tipo de habitación directamente desde el formulario.

#### **Cómo Funciona:**

1. **Seleccionar "+ Crear Nuevo Tipo de Habitación"** en el select
2. **Aparecen automáticamente los campos:**
   - Nombre del Tipo
   - Descripción
   - Capacidad
   - Precio por Noche Base
3. **Al enviar el formulario:**
   - ✅ Se crea primero el nuevo tipo de habitación
   - ✅ Se asigna automáticamente ese tipo a la habitación
   - ✅ Se guarda la habitación
   - ✅ Redirige al listado con mensaje de éxito

#### **Validaciones Implementadas:**

**Frontend (HTML5 + JavaScript):**
- Campos requeridos dinámicos
- Tipo numérico para capacidad y precio
- Min/max values apropiados

**Backend (Laravel):**
- Validación de tipo_habitacion_id
- Validación de campos del nuevo tipo (cuando aplica)
- Verificación de rol de gerente
- Validación que el ID existe si no es "nuevo"

**Archivos Modificados:**
- `resources/views/habitaciones/create.blade.php`
- `app/Http/Controllers/HabitacionController.php`
- `app/Http/Requests/StoreHabitacionRequest.php`

---

### **2. Validación de Eliminación de Tipos de Habitación** 🛡️

**Problema Resuelto:**

Antes, al intentar eliminar un tipo de habitación con habitaciones asociadas, el sistema arrojaba errores de integridad referencial.

**Solución Implementada:**

```php
public function destroy(TipoHabitacion $tipoHabitacion)
{
    try {
        // Verificar si tiene habitaciones asociadas
        $habitacionesCount = $tipoHabitacion->habitaciones()->count();
        
        if ($habitacionesCount > 0) {
            return redirect()->route('tipo_habitaciones.index')
                ->with('error', "No se puede eliminar este tipo porque tiene {$habitacionesCount} habitación(es) asociada(s).");
        }
        
        // Eliminar si no hay restricciones
        $tipoHabitacion->delete();
        return redirect()->route('tipo_habitaciones.index')
            ->with('success', 'Tipo de habitación eliminado con éxito.');
            
    } catch (\Exception $e) {
        return redirect()->route('tipo_habitaciones.index')
            ->with('error', 'Error al eliminar: ' . $e->getMessage());
    }
}
```

**Resultados:**
- ✅ Protege integridad de datos
- ✅ Mensajes claros al usuario
- ✅ No permite eliminar tipos con habitaciones asociadas
- ✅ Try-catch captura errores inesperados

**Archivos Modificados:**
- `app/Http/Controllers/TipoHabitacionController.php`
- `resources/views/tipo_habitaciones/index.blade.php`
- `resources/views/tipo_habitaciones/edit.blade.php`

---

### **3. Corrección de Rutas con Model Binding**

**Problema Resuelto:**

Error al editar tipos de habitación:
```
Missing required parameter for [Route: tipo_habitaciones.update] 
[URI: tipo_habitaciones/{tipo_habitacione}]
```

**Solución:**

Cambiar todas las rutas para pasar el ID explícitamente:

**Antes:**
```blade
<form action="{{ route('tipo_habitaciones.update', $tipoHabitacion) }}">
```

**Después:**
```blade
<form action="{{ route('tipo_habitaciones.update', $tipoHabitacion->id) }}">
```

**Comando Ejecutado:**
```bash
php artisan route:clear
```

---

## 💼 Lógica de Negocio Implementada

### **A) Reservas**
- ✅ Cálculo automático de precio según días
- ✅ Validación de disponibilidad de habitación
- ✅ Asignación de habitación a reserva
- ✅ Estados: Pendiente, Confirmada, Check-in, Check-out, Cancelada

### **B) Check-in**
- ✅ Cambio de estado de reserva a "Check-in"
- ✅ Cambio de estado de habitación a "Ocupada"
- ✅ Creación de registro en tabla estancias
- ✅ Registro de hora de entrada

### **C) Check-out**
- ✅ Suma de servicios adicionales
- ✅ Registro de pagos finales
- ✅ Cambio de habitación a estado "Limpieza"
- ✅ Cálculo de total incluyendo servicios
- ✅ Cierre de estancia

### **D) Servicios Adicionales**
- ✅ Registro de uso de servicios (minibar, lavandería, etc.)
- ✅ Cálculo de subtotales automático
- ✅ Vinculación con estancias

### **E) Pagos**
- ✅ Registro de pagos por reserva
- ✅ Soporte múltiples métodos (efectivo, tarjeta, transferencia)
- ✅ Actualización de total pagado
- ✅ Cálculo de saldo pendiente

---

## 📊 Reportes Implementados

**Ruta Base:** `/reportes`  
**Acceso:** Solo Gerente

### **1. Reporte General**

**Ruta:** `/reportes/general`

**Métricas:**
- Total de reservas por período
- Total de ingresos
- Distribución por estado de reservas
- Ocupación general del hotel

---

### **2. Reporte de Ingresos**

**Ruta:** `/reportes/ingresos`

**Métricas:**
- Ingresos totales por período
- Desglose por método de pago
- Detalle diario de ingresos
- Comparativa de períodos

---

### **3. Reporte de Ocupación**

**Ruta:** `/reportes/ocupacion`

**Métricas:**
- Porcentaje de ocupación del hotel
- Ocupación diaria
- Habitaciones más reservadas
- Tipos de habitación más solicitados

---

### **4. Reporte de Servicios**

**Ruta:** `/reportes/servicios`

**Métricas:**
- Servicios más utilizados
- Ingresos generados por servicios
- Cantidad total vendida por servicio
- Promedio de consumo por estancia

---

## 🚀 Instalación y Configuración

### **Requisitos del Sistema**
- PHP 8.2+
- Composer
- MySQL/MariaDB
- Apache/Nginx (XAMPP compatible)
- Node.js (opcional, solo si modificas assets)

### **Pasos de Instalación**

#### **1. Clonar el repositorio**
```bash
git clone <url-del-repositorio>
cd hotel_systemax
```

#### **2. Instalar dependencias de PHP**
```bash
composer install
```

#### **3. Configurar entorno**

Duplica el archivo `.env.example` y renómbralo a `.env`:
```bash
cp .env.example .env
```

Configura tus credenciales de base de datos en `.env`:
```env
APP_NAME="Hotel Systemax"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_systemax
DB_USERNAME=root
DB_PASSWORD=
```

#### **4. Generar clave de aplicación**
```bash
php artisan key:generate
```

#### **5. Ejecutar migraciones y seeders**

Esto creará las tablas y los 4 usuarios por defecto:
```bash
php artisan migrate --seed
```

**Nota:** Si necesitas reiniciar la base de datos:
```bash
php artisan migrate:fresh --seed
```

#### **6. Iniciar el servidor local**
```bash
php artisan serve
```

Accede a: **http://127.0.0.1:8000**

---

## 🔑 Credenciales de Acceso

### **Usuarios de Prueba**

| Rol | Email | Password | Descripción |
|-----|-------|----------|-------------|
| **Gerente** | gerente@hotel.com | password123 | Acceso total al sistema |
| **Recepción** | recepcion@hotel.com | password123 | Operaciones diarias |
| **Limpieza** | limpieza@hotel.com | password123 | Estado de habitaciones |
| **Mantenimiento** | mantenimiento@hotel.com | password123 | Mantenimiento de habitaciones |

---

## 🧪 Testing

### **Tests Implementados**

El sistema incluye todos los tests de Laravel Breeze:

```bash
php artisan test
```

**Resultado:**
```
Tests:    25 passed (61 assertions)
Duration: 4.53s
```

**Estado:** ✅ Todos los tests pasan correctamente

---

## 🛠️ Comandos Útiles

### **Desarrollo**
```bash
# Iniciar servidor
php artisan serve

# Ejecutar tests
php artisan test

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ver lista de rutas
php artisan route:list
```

### **Base de Datos**
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Refrescar base de datos (CUIDADO: Elimina todos los datos)
php artisan migrate:fresh --seed

# Ejecutar solo el seeder de roles
php artisan db:seed --class=RolesAndUsersSeeder
```

### **Debugging**
```bash
# Modo mantenimiento
php artisan down
php artisan up

# Ver logs
tail -f storage/logs/laravel.log
```

---

## 📂 Estructura del Proyecto

```
hotel_systemax/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CheckInController.php
│   │   │   ├── CheckOutController.php
│   │   │   ├── ClienteController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── HabitacionController.php
│   │   │   ├── PagoController.php
│   │   │   ├── ReportesController.php
│   │   │   ├── ReservaController.php
│   │   │   ├── ServicioController.php
│   │   │   ├── ServicioDetalleController.php
│   │   │   └── TipoHabitacionController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   │       ├── StoreHabitacionRequest.php
│   │       └── ...
│   └── Models/
│       ├── Cliente.php
│       ├── Estancia.php
│       ├── Habitacion.php
│       ├── Pago.php
│       ├── Reserva.php
│       ├── Servicio.php
│       ├── ServicioDetalle.php
│       ├── TipoHabitacion.php
│       └── User.php
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2025_11_25_200859_add_role_to_users_table.php
│   │   ├── create_tipo_habitaciones_table.php
│   │   ├── create_habitaciones_table.php
│   │   ├── create_clientes_table.php
│   │   ├── create_reservas_table.php
│   │   ├── create_estancias_table.php
│   │   ├── create_servicios_table.php
│   │   ├── create_servicio_detalles_table.php
│   │   └── create_pagos_table.php
│   └── seeders/
│       └── RolesAndUsersSeeder.php
├── public/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── resources/
│   └── views/
│       ├── checkin/
│       ├── checkout/
│       ├── clientes/
│       ├── components/
│       ├── dashboard.blade.php
│       ├── habitaciones/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── guest.blade.php
│       │   └── navigation.blade.php
│       ├── pagos/
│       ├── profile/
│       ├── reportes/
│       ├── reservas/
│       ├── servicios/
│       ├── tipo_habitaciones/
│       ├── users/
│       └── welcome.blade.php
├── routes/
│   ├── api.php
│   ├── channels.php
│   ├── console.php
│   ├── web.php
│   └── api.php
├── storage/
│   ├── app/
│   ├── app/public/
│   ├── logs/
│   └── views/
├── tests/
│   ├── Feature/
│   ├── Integration/
│   ├── Unit/
│   └── TestCase.php
├── vendor/
├── composer.json
├── composer.lock
├── artisan
├── package.json
├── phpunit.xml
├── README.md
└── webpack.mix.js
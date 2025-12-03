# Sistema de Roles - Hotel Systemax (Actualizado)

## ✅ 5 Roles Implementados

El sistema cuenta con **5 roles** distintos, cada uno con permisos y dashboards específicos:

---

### 1. 👑 Administrador (administrador)
**Acceso Total al Sistema + Gestión de Usuarios**

**Permisos:**
- ✅ **Gestión de Usuarios** (exclusivo del administrador)
- ✅ Todas las funciones de Gerente
- ✅ Todas las funciones de Recepción
- ✅ Acceso a todos los Reportes
- ✅ Gestión completa de Tipos de Habitación
- ✅ Gestión completa de Servicios
- ✅ Gestión completa de Habitaciones
- ✅ Vista de ingresos mensuales

**Usuario de Prueba:**
- Email: `admin@hotel.com`
- Password: `password123`

**Dashboard Personalizado:**
- Total de usuarios del sistema
- Total de clientes
- Habitaciones disponibles/ocupadas
- Reservas del día
- Check-ins y check-outs pendientes
- Ingresos del día
- Ingresos del mes

---

### 2. 👔 Gerente (gerente)
**Gestión Operativa y Reportes**

**Permisos:**
- ✅ Gestión de Tipos de Habitación (CRUD completo)
- ✅ Gestión de Habitaciones (CRUD completo)
- ✅ Gestión de Servicios (CRUD completo)
- ✅ Acceso a todos los Reportes:
  - Reporte General
  - Reporte de Ingresos
  - Reporte de Ocupación
  - Reporte de Servicios
- ✅ Todas las funciones de Recepción
- ❌ NO puede gestionar usuarios del sistema

**Usuario de Prueba:**
- Email: `gerente@hotel.com`
- Password: `password123`

**Dashboard Personalizado:**
- Habitaciones disponibles/ocupadas/limpieza/mantenimiento
- Reservas del día
- Check-ins y check-outs pendientes
- Ingresos del día
- Ingresos del mes

---

### 3. 🏨 Recepción (recepcion)
**Operaciones Diarias del Hotel**

**Permisos:**
- ✅ Gestión de Clientes (CRUD completo)
- ✅ Gestión de Habitaciones (solo ver)
- ✅ Gestión de Reservas (CRUD completo)
- ✅ Proceso de Check-In
- ✅ Proceso de Check-Out
- ✅ Registro de Servicios Adicionales
- ✅ Gestión de Pagos
- ❌ NO puede gestionar tipos de habitación
- ❌ NO puede ver reportes
- ❌ NO puede gestionar servicios del catálogo
- ❌ NO puede gestionar usuarios

**Usuario de Prueba:**
- Email: `recepcion@hotel.com`
- Password: `password123`

**Dashboard Personalizado:**
- Habitaciones disponibles/ocupadas
- Reservas del día
- Check-ins y check-outs pendientes
- Ingresos del día
- Acciones rápidas (Nueva Reserva, Nuevo Cliente, Registrar Pago)

---

### 4. 🧹 Limpieza (limpieza)
**Gestión de Estado de Habitaciones**

**Permisos:**
- ✅ Ver lista de habitaciones
- ✅ Ver estado de habitaciones (disponible, ocupada, limpieza, mantenimiento)
- ❌ NO puede acceder a otras funcionalidades

**Usuario de Prueba:**
- Email: `limpieza@hotel.com`
- Password: `password123`

**Dashboard Personalizado:**
- Habitaciones pendientes de limpieza (destacado)
- Habitaciones disponibles
- Habitaciones ocupadas
- Total de habitaciones
- Acceso rápido a ver todas las habitaciones

**Rutas Disponibles:**
- `/limpieza/habitaciones` - Ver habitaciones

---

### 5. 🔧 Mantenimiento (mantenimiento)
**Gestión de Mantenimiento de Habitaciones**

**Permisos:**
- ✅ Ver lista de habitaciones
- ✅ Ver estado de habitaciones
- ❌ NO puede acceder a otras funcionalidades

**Usuario de Prueba:**
- Email: `mantenimiento@hotel.com`
- Password: `password123`

**Dashboard Personalizado:**
- Habitaciones en mantenimiento (destacado)
- Habitaciones disponibles
- Habitaciones ocupadas
- Total de habitaciones
- Acceso rápido a ver todas las habitaciones

**Rutas Disponibles:**
- `/mantenimiento/habitaciones` - Ver habitaciones

---

## 🔐 Jerarquía de Roles

```
Administrador (Acceso Total)
    ↓
Gerente (Gestión Operativa + Reportes)
    ↓
Recepción (Operaciones Diarias)
    ↓
Limpieza / Mantenimiento (Funciones Específicas)
```

---

## 📋 Navegación por Rol

### Menú para Administrador:
- Dashboard
- **Usuarios** (exclusivo)
- Clientes
- Habitaciones
- Reservas
- Pagos
- Tipos de Habitación
- Servicios
- Reportes

### Menú para Gerente:
- Dashboard
- Clientes
- Habitaciones
- Reservas
- Pagos
- Tipos de Habitación
- Servicios
- Reportes

### Menú para Recepción:
- Dashboard
- Clientes
- Habitaciones
- Reservas
- Pagos

### Menú para Limpieza:
- Dashboard
- Habitaciones

### Menú para Mantenimiento:
- Dashboard
- Habitaciones

---

## 🧪 Probar los Roles

1. **Login con diferentes usuarios:**
   - Administrador: `admin@hotel.com` / `password123`
   - Gerente: `gerente@hotel.com` / `password123`
   - Recepción: `recepcion@hotel.com` / `password123`
   - Limpieza: `limpieza@hotel.com` / `password123`
   - Mantenimiento: `mantenimiento@hotel.com` / `password123`

2. **Verificar dashboard personalizado:**
   - Cada rol tiene su propio dashboard con métricas relevantes

3. **Verificar menú de navegación:**
   - El menú debe cambiar según el rol

4. **Intentar acceder a rutas restringidas:**
   - Por ejemplo, si inicias sesión como "Recepción" e intentas acceder a `/reportes`, deberías recibir error 403

---

## 🚀 Ejecutar Seeder

Para crear los 5 usuarios de prueba:

```bash
php artisan db:seed --class=RolesAndUsersSeeder
```

---

## ✅ Características Implementadas

- [x] 5 roles implementados (administrador, gerente, recepcion, limpieza, mantenimiento)
- [x] Middleware RoleMiddleware con jerarquía de acceso
- [x] Seeder RolesAndUsersSeeder actualizado
- [x] 5 usuarios de prueba creados
- [x] Rutas protegidas por rol
- [x] Navegación dinámica por rol
- [x] **Dashboards personalizados para cada rol**
- [x] **Vista de gestión de usuarios (solo administrador)**
- [x] Administrador con acceso total
- [x] Gerente con acceso a gestión y reportes
- [x] Recepción con operaciones diarias
- [x] Limpieza con vista de habitaciones
- [x] Mantenimiento con vista de habitaciones

---

## 📝 Diferencias Clave entre Roles

| Funcionalidad | Admin | Gerente | Recepción | Limpieza | Mantenimiento |
|--------------|-------|---------|-----------|----------|---------------|
| Gestión de Usuarios | ✅ | ❌ | ❌ | ❌ | ❌ |
| Reportes | ✅ | ✅ | ❌ | ❌ | ❌ |
| Tipos de Habitación | ✅ | ✅ | ❌ | ❌ | ❌ |
| Servicios | ✅ | ✅ | ❌ | ❌ | ❌ |
| Habitaciones (CRUD) | ✅ | ✅ | Ver | Ver | Ver |
| Clientes | ✅ | ✅ | ✅ | ❌ | ❌ |
| Reservas | ✅ | ✅ | ✅ | ❌ | ❌ |
| Check-in/out | ✅ | ✅ | ✅ | ❌ | ❌ |
| Pagos | ✅ | ✅ | ✅ | ❌ | ❌ |
| Dashboard Personalizado | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## 🎯 Sistema Completo y Funcional

El sistema ahora tiene una estructura completa de roles con:
- Jerarquía clara de permisos
- Dashboards personalizados para cada rol
- Navegación adaptativa
- Seguridad robusta con middleware
- 5 usuarios de prueba listos para usar

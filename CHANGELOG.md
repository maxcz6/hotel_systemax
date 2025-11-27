# 🔄 Changelog & Updates - November 26, 2025

## ✅ System Fixes and Improvements

### 🎯 **Navigation Menu Fix for Administrador Role**

**Problem**: Users with `administrador` role could not see navigation menu items.

**Root Cause**: The navigation was only checking for `gerente` and `recepcion` roles, but the database uses `administrador` as the highest privilege role.

**Solution**: 
- Updated `resources/views/layouts/navigation.blade.php` to support `administrador` role
- Updated `app/Http/Middleware/RoleMiddleware.php` to grant `administrador` full system access
- Both `administrador` and `gerente` now have identical permissions (full access)

**Files Modified**:
- `resources/views/layouts/navigation.blade.php`
- `app/Http/Middleware/RoleMiddleware.php`

---

### 💳 **Payment System Overhaul**

**Problem**: Payment controller and views were using outdated column names from previous database schema.

**Database Schema Changes**:
- `id_reserva` →`reserva_id`
- `fecha` → `fecha_pago`
- `referencia` → `numero_transaccion` + `comprobante`
- Added `estado` (pendiente | completado | anulado)
- Added `usuario_id` to track which user registered the payment
- Added `anulado_por`, `fecha_anulacion`, `motivo_anulacion` for soft deletion

**Solution**:

#### 1. Updated Pago Model (`app/Models/Pago.php`)
- Added all new fields to `$fillable` array
- Added date casts for `fecha_pago` and `fecha_anulacion`
- Supports payment status tracking (pendiente, completado, anulado)

#### 2. Updated PagoController (`app/Http/Controllers/PagoController.php`)
- Fixed column names throughout (reserva_id, fecha_pago, etc.)
- Added `usuario_id` tracking (records which user created the payment)
- Changed `destroy()` method to soft-delete (marks as `anulado` instead of deleting)
- Added proper payment status filtering when calculating totals
- Fixed total calculation to use `total_precio` instead of `precio_total`
- Fixed servicio total calculation to use `total` instead of `subtotal`

#### 3. Updated Payment Create View (`resources/views/pagos/create.blade.php`)
- Changed `id_reserva` to `reserva_id` in hidden input
- Replaced `referencia` field with two fields:
  - `numero_transaccion` - Transaction/reference number
  - `comprobante` - Receipt/voucher code
- Added `max` validation on amount input to prevent overpayment

#### 4. Updated Payment Index View (`resources/views/pagos/index.blade.php`)
- Fixed column references (reserva_id, fecha_pago)
- Added "Estado" column showing payment status with color-coded badges
- Changed "Referencia" column to "N° Transacción"
- Fixed table colspan for empty state

**Files Modified**:
- `app/Models/Pago.php`
- `app/Http/Controllers/PagoController.php`
- `resources/views/pagos/create.blade.php`
- `resources/views/pagos/index.blade.php`

---

### 📊 **Reserva Model Enhancement**

**Problem**: Reserva model was missing many fields from the database schema.

**Solution**: Added all missing fields and relationships

**New Fillable Fields**:
- `usuario_id` - User who created the reservation
- `num_adultos` - Number of adults
- `num_ninos` - Number of children
- `origen_reserva` - Source (web, telefono, presencial, agencia)
- `cancelado_por` - User who cancelled (if cancelled)
- `fecha_cancelacion` - Cancellation date
- `motivo_cancelacion` - Cancellation reason

**New Relationships**:
- `usuario()` - BelongsTo relationship with User model

**New Casts**:
- `fecha_entrada` → date
- `fecha_salida` → date
- `fecha_cancelacion` → datetime
- `total_precio` → decimal:2
- `descuento` → decimal:2

**Files Modified**:
- `app/Models/Reserva.php`

---

## 📝 Database Schema Summary

### User Roles (ENUM values):
- `administrador` - Full system access (equivalent to gerente)
- `gerente` - Full system access
- `recepcion` - Daily operations (clients, rooms, reservations, payments)
- `limpieza` - Room cleaning status
- `mantenimiento` - Room maintenance

### Payment States (ENUM values):
- `pendiente` - Payment pending
- `completado` - Payment completed
- `anulado` - Payment voided/cancelled

---

**Last Updated**: November 26, 2025

# 💳 Payment System - Quick Reference Guide

## How to Register a Payment

### From the Admin/Reception Interface:

1. **Navigate to Reservations**
   - Click "Reservations" in the navigation menu
   - Select the reservation you want to register a payment for

2. **Go to Reservation Details**
   - Click on the reservation to view full details
   - Look for the "Register Payment" button

3. **Fill Payment Form**
   - **Amount (Monto)**: Enter the payment amount  
     *The system pre-fills the pending balance*
   - **Payment Method (Método de Pago)**: Select one:
     - Efectivo (Cash)
     - Tarjeta (Card)
     - Transferencia (Bank Transfer)
   - **Transaction Number** *(Optional)*: Reference or transaction ID
   - **Receipt/Voucher** *(Optional)*: Receipt or voucher code

4. **Submit**
   - Click "Registrar Pago" (Register Payment)
   - The system will:
     - Create the payment record
     - Mark it as "completado" (completed)
     - Track which user registered it
     - Update reservation status if fully paid

## Payment Statuses

| Status | Badge Color | Description |
|--------|-------------|-------------|
| **Completado** | 🟢 Green | Payment successfully processed |
| **Pendiente** | 🟡 Yellow | Payment pending confirmation |
| **Anulado** | 🔴 Red | Payment voided/cancelled |

## Viewing Payments

### All Payments List
- Navigate to **Payments** in the main menu
- View table showing:
  - Payment ID
  - Related Reservation
  - Client Name
  - Payment Date
  - Amount
  - Payment Method
  - Status (with color badge)
  - Transaction Number

### Payments for Specific Reservation
- Go to reservation details page
- View "Payments" section showing all payments for that reservation
- See total paid vs. total owed

## Voiding a Payment

**Note**: Payments are never deleted, only voided for audit purposes.

1. Go to reservation details
2. Find the payment to void
3. Click "Delete" or "Anular" button
4. The payment will be marked as "anulado" with:
   - Who voided it (`anulado_por`)
   - When it was voided (`fecha_anulacion`)
   - Reason: "Anulado por usuario"

## Database Fields Reference

### Pagos Table Columns:

```
- id                    # Auto-increment primary key
- reserva_id           # Foreign key to reservas table
- monto                # Payment amount (decimal 10,2)
- metodo_pago          # efectivo | tarjeta | transferencia
- comprobante          # Receipt/voucher code (optional)
- fecha_pago           # Payment date (datetime)
- estado               # pendiente | completado | anulado
- usuario_id           # User who registered the payment
- numero_transaccion   # Transaction/reference number (optional)
- anulado_por          # User who voided (if anulado)
- fecha_anulacion      # Void date (if anulado)
- motivo_anulacion     # Void reason (if anulado)
- created_at           # Record creation timestamp
- updated_at           # Last update timestamp
```

## API/Controller Reference

### PagoController Methods:

#### `index()`
- Lists all payments with pagination
- Loads relationships: `reserva.cliente`
- Orders by `fecha_pago` descending

#### `create(?reserva_id)`
- Shows payment form for a specific reservation
- Calculates pending balance:
  ```php
  $saldoPendiente = ($total_precio + $servicios_total) - $total_pagado
  ```

#### `store(Request)`
- Validates: `reserva_id`, `monto`, `metodo_pago`, `numero_transaccion`, `comprobante`
- Creates payment with `estado='completado'` and `usuario_id=auth()->id()`
- Updates reservation status if fully paid

#### `show(Pago)`
- Displays single payment details
- Loads `reserva.cliente` relationship

#### `destroy(Pago)`
- Soft-deletes payment (marks as anulado)
- Records `anulado_por`, `fecha_anulacion`, `motivo_anulacion`

## Business Logic

### Automatic Reservation Status Update

When a payment is registered:

```php
$totalPagado = $reserva->pagos->where('estado', 'completado')->sum('monto');
$serviciosTotal = $reserva->estancia->serviciosDetalle->sum('total');
$totalGeneral = $reserva->total_precio + $serviciosTotal;

if ($totalPagado >= $totalGeneral) {
    // Mark reservation as confirmed (fully paid)
    $reserva->estado = 'confirmada';
}
```

### Payment Validation

- Amount must be > 0
- Amount cannot exceed pending balance (enforced in UI with `max` attribute)
- Reservation must exist
- Payment method must be one of: efectivo, tarjeta, transferencia

## Permissions

| Role | View Payments | Create Payments | Void Payments |
|------|--------------|----------------|---------------|
| **Administrador** | ✅ | ✅ | ✅ |
| **Gerente** | ✅ | ✅ | ✅ |
| **Recepcion** | ✅ | ✅ |❌ |
| **Limpieza** | ❌ | ❌ | ❌ |
| **Mantenimiento** | ❌ | ❌ | ❌ |

---

## Troubleshooting

### Payment Not Showing in List
- Check that payment `estado` is not filtered
- Verify payment was created successfully (check `pagos` table)

### Can't Register Payment
- Ensure you have proper role (administrador, gerente, or recepcion)
- Verify reservation exists and is not cancelled
- Check database connection

### Pending Balance Incorrect
- Verify `total_precio` in reservas table
- Check `servicio_detalles.total` sum
- Ensure only `completado` payments are counted

---

**For Developers**: See full implementation in:
- `app/Http/Controllers/PagoController.php`
- `app/Models/Pago.php`
- `resources/views/pagos/create.blade.php`
- `resources/views/pagos/index.blade.php`

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TipoHabitacionController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ServicioDetalleController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\MantenimientoController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes for Administrador (full access)
    Route::middleware('role:administrador')->group(function () {
        // Administrador tiene acceso completo a pagos (edit/update/delete)
        Route::get('/pagos/{pago}/edit', [PagoController::class, 'edit'])->name('pagos.edit');
        Route::put('/pagos/{pago}', [PagoController::class, 'update'])->name('pagos.update');
        
        // Habitaciones - CRUD completo para administrador
        Route::resource('habitaciones', HabitacionController::class)->parameters([
            'habitaciones' => 'habitacion'
        ]);
        
        // Tipos de Habitación - CRUD completo para administrador
        Route::resource('tipo_habitaciones', TipoHabitacionController::class)->parameters([
            'tipo_habitaciones' => 'tipoHabitacion'
        ]);
        
        // Reportes
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/general', [ReportesController::class, 'general'])->name('reportes.general');
        Route::get('/reportes/ingresos', [ReportesController::class, 'ingresos'])->name('reportes.ingresos');
        Route::get('/reportes/ocupacion', [ReportesController::class, 'ocupacion'])->name('reportes.ocupacion');
        Route::get('/reportes/servicios', [ReportesController::class, 'servicios'])->name('reportes.servicios');
        
        // Servicios
        Route::resource('servicios', ServicioController::class);
    });

    // Routes for Gerente
    Route::middleware('role:gerente')->group(function () {
        Route::resource('tipo_habitaciones', TipoHabitacionController::class)->parameters([
            'tipo_habitaciones' => 'tipoHabitacion'
        ]);
        
        // Habitaciones - CRUD completo para gerente
        Route::resource('habitaciones', HabitacionController::class)->parameters([
            'habitaciones' => 'habitacion'
        ]);
        
        // Reportes
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/general', [ReportesController::class, 'general'])->name('reportes.general');
        Route::get('/reportes/ingresos', [ReportesController::class, 'ingresos'])->name('reportes.ingresos');
        Route::get('/reportes/ocupacion', [ReportesController::class, 'ocupacion'])->name('reportes.ocupacion');
        Route::get('/reportes/servicios', [ReportesController::class, 'servicios'])->name('reportes.servicios');
        
        // Servicios
        Route::resource('servicios', ServicioController::class);
    });

    // Routes for Recepcion
    Route::middleware('role:recepcion')->group(function () {
        Route::resource('clientes', ClienteController::class);
        
        // Habitaciones - Solo ver, NO crear/editar
        Route::get('/habitaciones', [HabitacionController::class, 'index'])->name('habitaciones.index');
        Route::get('/habitaciones/{habitacion}', [HabitacionController::class, 'show'])->name('habitaciones.show');
        
        Route::resource('reservas', ReservaController::class);
        Route::put('/reservas/{reserva}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');
        
        // Check-in / Check-out
        Route::get('/checkin/{reserva}', [CheckInController::class, 'show'])->name('checkin.show');
        Route::post('/checkin/{reserva}', [CheckInController::class, 'store'])->name('checkin.store');
        Route::get('/checkout/{reserva}', [CheckOutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout/{reserva}', [CheckOutController::class, 'store'])->name('checkout.store');
        
        // Servicios Detalle
        Route::resource('servicio_detalle', ServicioDetalleController::class)->except(['show']);
        Route::put('/servicio_detalle/{servicioDetalle}/anular', [ServicioDetalleController::class, 'anular'])->name('servicio_detalle.anular');
        
        // Pagos (recepcion puede ver, crear, mostrar, eliminar - NO editar)
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/create', [PagoController::class, 'create'])->name('pagos.create');
        Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
        Route::get('/pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
        Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
        
        // Salidas de Clientes
        Route::get('/salidas', [SalidaController::class, 'index'])->name('salidas.index');
        Route::get('/salidas/create', [SalidaController::class, 'create'])->name('salidas.create');
        Route::post('/salidas', [SalidaController::class, 'store'])->name('salidas.store');
        Route::get('/salidas/{salida}', [SalidaController::class, 'show'])->name('salidas.show');
        Route::get('/salidas/{salida}/edit', [SalidaController::class, 'edit'])->name('salidas.edit');
        Route::put('/salidas/{salida}', [SalidaController::class, 'update'])->name('salidas.update');
    });

    // Routes for Administrador y Gerente (Mantenimiento)
    Route::middleware('role:administrador,gerente')->group(function () {
        Route::resource('mantenimientos', MantenimientoController::class);
    });

    // Routes for Limpieza
    Route::middleware('role:limpieza')->group(function () {
        Route::get('/limpieza/habitaciones', [HabitacionController::class, 'index'])->name('limpieza.habitaciones');
        // Aquí se pueden agregar más rutas específicas para limpieza
    });

    // Routes for Mantenimiento
    Route::middleware('role:mantenimiento')->group(function () {
        Route::get('/mantenimiento/habitaciones', [HabitacionController::class, 'index'])->name('mantenimiento.habitaciones');
        Route::resource('mantenimientos', MantenimientoController::class);
    });
});

require __DIR__.'/auth.php';


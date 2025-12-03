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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes for Administrador - Acceso total al sistema
    Route::middleware('role:administrador')->group(function () {
        // Gestión de Usuarios (solo administrador)
        Route::get('/usuarios', function() {
            $usuarios = \App\Models\User::all();
            return view('usuarios.index', compact('usuarios'));
        })->name('usuarios.index');
        
        // Todas las funcionalidades de gerente
        Route::resource('tipo_habitaciones', TipoHabitacionController::class)->parameters([
            'tipo_habitaciones' => 'tipoHabitacion'
        ]);
        Route::resource('servicios', ServicioController::class);
        Route::resource('habitaciones', HabitacionController::class)->parameters([
            'habitaciones' => 'habitacion'
        ]);
        
        // Reportes
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/general', [ReportesController::class, 'general'])->name('reportes.general');
        Route::get('/reportes/ingresos', [ReportesController::class, 'ingresos'])->name('reportes.ingresos');
        Route::get('/reportes/ocupacion', [ReportesController::class, 'ocupacion'])->name('reportes.ocupacion');
        Route::get('/reportes/servicios', [ReportesController::class, 'servicios'])->name('reportes.servicios');
        
        // Todas las funcionalidades de recepción
        Route::resource('clientes', ClienteController::class);
        Route::resource('reservas', ReservaController::class);
        Route::get('/checkin/{reserva}', [CheckInController::class, 'show'])->name('checkin.show');
        Route::post('/checkin/{reserva}', [CheckInController::class, 'store'])->name('checkin.store');
        Route::get('/checkout/{reserva}', [CheckOutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout/{reserva}', [CheckOutController::class, 'store'])->name('checkout.store');
        Route::resource('servicio_detalle', ServicioDetalleController::class)->except(['show']);
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/create', [PagoController::class, 'create'])->name('pagos.create');
        Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
        Route::get('/pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
        Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
    });

    // Routes for Gerente - Gestión operativa y reportes
    Route::middleware('role:gerente')->group(function () {
        Route::resource('tipo_habitaciones', TipoHabitacionController::class)->parameters([
            'tipo_habitaciones' => 'tipoHabitacion'
        ]);
        
        Route::resource('habitaciones', HabitacionController::class)->parameters([
            'habitaciones' => 'habitacion'
        ]);
        
        // Reportes
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/general', [ReportesController::class, 'general'])->name('reportes.general');
        Route::get('/reportes/ingresos', [ReportesController::class, 'ingresos'])->name('reportes.ingresos');
        Route::get('/reportes/ocupacion', [ReportesController::class, 'ocupacion'])->name('reportes.ocupacion');
        Route::get('/reportes/servicios', [ReportesController::class, 'servicios'])->name('reportes.servicios');
        
        Route::resource('servicios', ServicioController::class);
        
        // Acceso a funcionalidades de recepción
        Route::resource('clientes', ClienteController::class);
        Route::resource('reservas', ReservaController::class);
        Route::get('/checkin/{reserva}', [CheckInController::class, 'show'])->name('checkin.show');
        Route::post('/checkin/{reserva}', [CheckInController::class, 'store'])->name('checkin.store');
        Route::get('/checkout/{reserva}', [CheckOutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout/{reserva}', [CheckOutController::class, 'store'])->name('checkout.store');
        Route::resource('servicio_detalle', ServicioDetalleController::class)->except(['show']);
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/create', [PagoController::class, 'create'])->name('pagos.create');
        Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
        Route::get('/pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
        Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
    });

    // Routes for Recepcion - Operaciones diarias
    Route::middleware('role:recepcion')->group(function () {
        Route::resource('clientes', ClienteController::class);
        
        // Habitaciones - Solo ver
        Route::get('/habitaciones', [HabitacionController::class, 'index'])->name('habitaciones.index');
        Route::get('/habitaciones/{habitacion}', [HabitacionController::class, 'show'])->name('habitaciones.show');
        
        Route::resource('reservas', ReservaController::class);
        
        // Check-in / Check-out
        Route::get('/checkin/{reserva}', [CheckInController::class, 'show'])->name('checkin.show');
        Route::post('/checkin/{reserva}', [CheckInController::class, 'store'])->name('checkin.store');
        Route::get('/checkout/{reserva}', [CheckOutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout/{reserva}', [CheckOutController::class, 'store'])->name('checkout.store');
        
        // Servicios Detalle
        Route::resource('servicio_detalle', ServicioDetalleController::class)->except(['show']);
        
        // Pagos
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/create', [PagoController::class, 'create'])->name('pagos.create');
        Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
        Route::get('/pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
        Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
    });

    // Routes for Limpieza - Gestión de limpieza
    Route::middleware('role:limpieza')->group(function () {
        Route::get('/limpieza/habitaciones', [HabitacionController::class, 'index'])->name('limpieza.habitaciones');
    });

    // Routes for Mantenimiento - Gestión de mantenimiento
    Route::middleware('role:mantenimiento')->group(function () {
        Route::get('/mantenimiento/habitaciones', [HabitacionController::class, 'index'])->name('mantenimiento.habitaciones');
    });
});

require __DIR__.'/auth.php';


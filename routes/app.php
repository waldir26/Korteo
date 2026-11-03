<?php

use App\Http\Controllers\App\BarbershopSettingsController;
use App\Http\Controllers\App\ServiceController;
use App\Http\Controllers\App\ClientController;
use App\Http\Controllers\App\AppointmentController;
use App\Http\Controllers\App\BarberController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\CashController;
use App\Http\Controllers\App\ProductController;
use App\Http\Controllers\App\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'barbershop'])->prefix('app')->name('app.')->group(function () {

    // ==========================================
    // RUTAS PARA TODOS (Dueño y Barbero)
    // ==========================================

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Citas
    Route::get('/citas/agendar', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::get('/citas', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/citas', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::match(['get', 'post', 'put'], '/citas/{appointment}/update', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::post('/citas/{appointment}/estado', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::post('/citas/{appointment}/pagar', [AppointmentController::class, 'pay'])->name('appointments.pay');
    Route::post('/citas/verificar-disponibilidad', [AppointmentController::class, 'checkAvailability'])->name('appointments.check');
    Route::post('/citas/{appointment}/delete', [AppointmentController::class, 'destroy'])->name('appointments.delete');
    Route::get('/calendario', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/calendario/data', [AppointmentController::class, 'calendarData'])->name('appointments.calendar.data');

    // Clientes
    Route::get('/clientes', [ClientController::class, 'index'])->name('clientes.index');
    Route::post('/clientes', [ClientController::class, 'store'])->name('clientes.store');
    Route::post('/clientes/{client}/update', [ClientController::class, 'update'])->name('clientes.update');
    Route::post('/clientes/{client}/delete', [ClientController::class, 'destroy'])->name('clientes.delete');

    // Perfil
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');


    // ==========================================
    // RUTAS EXCLUSIVAS DEL DUEÑO (Rol: admin)
    // ==========================================
    Route::middleware(['role:admin'])->group(function () {

        // Configuración de la Barbería
        Route::get('/configuracion', [BarbershopSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/configuracion', [BarbershopSettingsController::class, 'update'])->name('settings.update');
        Route::delete('/configuracion/logo', [BarbershopSettingsController::class, 'deleteLogo'])->name('settings.delete-logo');

        // Servicios
        Route::get('/servicios', [ServiceController::class, 'index'])->name('servicios.index');
        Route::post('/servicios', [ServiceController::class, 'store'])->name('servicios.store');
        Route::match(['get', 'post', 'put'], '/servicios/{service}/update', [ServiceController::class, 'update'])->name('servicios.update');
        Route::post('/servicios/{service}/delete', [ServiceController::class, 'destroy'])->name('servicios.delete');

        // Barberos
        Route::get('/barberos', [BarberController::class, 'index'])->name('barberos.index');
        Route::post('/barberos', [BarberController::class, 'store'])->name('barberos.store');
        Route::match(['get', 'post', 'put'], '/barberos/{barber}/update', [BarberController::class, 'update'])->name('barberos.update');
        Route::post('/barberos/{barber}/delete', [BarberController::class, 'destroy'])->name('barberos.delete');

        // Inventario (Productos)
        Route::get('/productos', [ProductController::class, 'index'])->name('productos.index');
        Route::post('/productos', [ProductController::class, 'store'])->name('productos.store');
        Route::match(['get', 'post', 'put'], '/productos/{product}/update', [ProductController::class, 'update'])->name('productos.update');
        Route::post('/productos/{product}/delete', [ProductController::class, 'destroy'])->name('productos.delete');

        // Categorías de Productos
        Route::post('/categorias', [CategoryController::class, 'store'])->name('categorias.store');
        Route::post('/categorias/{category}/delete', [CategoryController::class, 'destroy'])->name('categorias.destroy');

        // Finanzas
        Route::get('/finanzas/ventas', [CashController::class, 'indexVentas'])->name('cash.ventas');
        Route::get('/finanzas/gastos', [CashController::class, 'indexGastos'])->name('cash.gastos');
        Route::get('/finanzas/pagos', [CashController::class, 'indexPagos'])->name('cash.pagos');
        Route::get('/finanzas/cierre', [CashController::class, 'indexCierre'])->name('cash.cierre');

        // Acciones de Caja
        Route::post('/finanzas/ingreso', [CashController::class, 'storeIngreso'])->name('cash.store-ingreso');
        Route::post('/finanzas/gasto', [CashController::class, 'storeGasto'])->name('cash.store-gasto');
        Route::post('/finanzas/{transaction}/delete', [CashController::class, 'destroy'])->name('cash.delete');
    });
});

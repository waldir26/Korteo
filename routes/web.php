<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use Illuminate\Support\Facades\Route;

// Redirige la raíz al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Login / Logout
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Cambio de contraseña obligatorio
Route::middleware('auth')->group(function () {
    Route::get('/cambiar-password', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/cambiar-password', [PasswordChangeController::class, 'update'])->name('password.change.post');
});

Route::view('/ayuda', 'app.ayuda')->name('app.ayuda');

// AQUÍ ESTÁ LA MAGIA: Cargar los archivos separados
require __DIR__ . '/superadmin.php';
require __DIR__ . '/app.php';

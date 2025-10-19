<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AscensorController;
use App\Http\Controllers\PublicoController;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Rutas protegidas
Route::middleware('auth')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('ascensores', AscensorController::class);
    
    // Rutas adicionales que crearemos después
    Route::get('/revisiones', function() {
        return inertia('Revisiones/Index');
    })->name('revisiones.index');
    
    Route::get('/reportes', function() {
        return inertia('Reportes/Index');
    })->name('reportes.index');
    
    Route::get('/perfil', function() {
        return inertia('Perfil/Edit');
    })->name('perfil.edit');
    
    Route::get('/configuracion', function() {
        return inertia('Configuracion/Index');
    })->name('configuracion.index');
});

// Vista pública del ascensor por QR
Route::get('/a/{slug}', [PublicoController::class, 'show'])->name('ascensor.publico');

// Redirección raíz
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

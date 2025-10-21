<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AscensorController;
use App\Http\Controllers\PublicoController;
use App\Http\Controllers\RevisionesController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ReportesController;
use Illuminate\Support\Facades\Route;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Pública (QR)
Route::get('/a/{slug}', [PublicoController::class, 'show'])->name('ascensor.publico');
Route::get('/a/{slug}/pdf', [PublicoController::class, 'pdf'])->name('ascensor.publico.pdf');
// Root
Route::get('/', fn() => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

// Protegidas
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ascensores
    Route::resource('ascensores', AscensorController::class)
        ->parameters(['ascensores' => 'ascensor']);

    // Revisiones
    Route::get('/revisiones', [RevisionesController::class, 'index'])->name('revisiones.index');
    Route::get('/revisiones/create', [RevisionesController::class, 'create'])->name('revisiones.create');
    Route::post('/revisiones', [RevisionesController::class, 'store'])->name('revisiones.store');
    Route::get('/revisiones/{revision}', [RevisionesController::class, 'show'])
        ->name('revisiones.show');
    Route::put('/revisiones/{revision}', [RevisionesController::class, 'update'])
        ->name('revisiones.update');
    // Perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'updateProfile'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');

    // Configuración (opcional: restringir a admin con Gate)
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::put('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');


    Route::get('/configuracion', [ConfiguracionController::class, 'index'])
        ->middleware('can:manage-settings')
        ->name('configuracion.index');
    Route::put('/configuracion', [ConfiguracionController::class, 'update'])
        ->middleware('can:manage-settings')
        ->name('configuracion.update');



    // Reportes
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/export', [ReportesController::class, 'export'])->name('reportes.export'); // ?tipo=csv
});

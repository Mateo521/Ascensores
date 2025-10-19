<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RevisionesController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/revisiones/sync', [RevisionesController::class, 'syncOffline'])
        ->name('api.revisiones.sync');
});

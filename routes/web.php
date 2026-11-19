<?php

use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PremioController;
use App\Http\Controllers\Admin\ResultadoController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\RuletaPublica;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página principal: la ruleta (reemplaza a welcome)
Route::get('/', RuletaPublica::class)->name('ruleta.index');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Jetstream)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:admin',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('premios', PremioController::class)->except(['show']);
        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'usuario'])
            ->except(['show']);

        Route::get('resultados', [ResultadoController::class, 'index'])->name('resultados.index');
        Route::get('resultados/export', [ResultadoController::class, 'export'])->name('resultados.export');
        Route::delete('resultados/{resultado}', [ResultadoController::class, 'destroy'])->name('resultados.destroy');

        Route::get('configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
        Route::put('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

        Route::get('aplicaciones', fn () => view('admin.aplicaciones.index'))
            ->name('aplicaciones.index');
    });
});

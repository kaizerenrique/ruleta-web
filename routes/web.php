<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuletaController;
use App\Http\Controllers\Admin\{
    PremioController,
    ResultadoController,
    ConfiguracionController,
    DashboardController,
    UserController,
};

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página principal: la ruleta (reemplaza a welcome)
Route::get('/', \App\Livewire\RuletaPublica::class)->name('ruleta.index');

// Procesar el giro de la ruleta
Route::post('/girar', [RuletaController::class, 'girar'])
    ->middleware('throttle:10,1')
    ->name('ruleta.girar');

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
        Route::resource('premios', PremioController::class);
        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'usuario'])
            ->except(['show']);

        Route::get('resultados', [ResultadoController::class, 'index'])->name('resultados.index');
        Route::get('resultados/export', [ResultadoController::class, 'export'])->name('resultados.export');
        Route::delete('resultados/{resultado}', [ResultadoController::class, 'destroy'])->name('resultados.destroy');

        Route::get('configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
        Route::put('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

        Route::get('aplicaciones', fn() => view('admin.aplicaciones.index'))
            ->name('aplicaciones.index');
    });
});

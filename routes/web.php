<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuletaController;
use App\Http\Controllers\Admin\PremioController;
use App\Http\Controllers\Admin\ResultadoController;
use App\Http\Controllers\Admin\ConfiguracionController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página principal: la ruleta (reemplaza a welcome)
Route::get('/', [RuletaController::class, 'index'])->name('ruleta.index');

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
])->group(function () {

    // Dashboard de Jetstream
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Panel de administración de la ruleta
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('premios', PremioController::class);

        Route::get('resultados', [ResultadoController::class, 'index'])->name('resultados.index');
        Route::get('resultados/export', [ResultadoController::class, 'export'])->name('resultados.export');
        Route::delete('resultados/{resultado}', [ResultadoController::class, 'destroy'])->name('resultados.destroy');

        Route::get('configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
        Route::put('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
    });
});

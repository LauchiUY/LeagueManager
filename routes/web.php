<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ClasificacionController;

Route::get('/', function () {
    return view('welcome');
});

// Equipos
Route::resource('equipos', EquipoController::class);

// Clasificación y estadísticas (consulta pública)
Route::get('/clasificacion', [ClasificacionController::class, 'competiciones'])->name('clasificacion.competiciones');
Route::get('/clasificacion/{competicion}', [ClasificacionController::class, 'index'])->name('clasificacion.index');
Route::get('/clasificacion/{competicion}/pdf', [ClasificacionController::class, 'exportPdf'])->name('clasificacion.pdf');
Route::get('/estadisticas/equipo/{equipo}', [ClasificacionController::class, 'estadisticasEquipo'])->name('estadisticas.equipo');

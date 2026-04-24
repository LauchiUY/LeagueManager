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

// Ruta protegida para el algoritmo del Calendario
Route::post('/admin/generar-calendario/{competicion}', function (\Illuminate\Http\Request $request, $competicion, \App\Services\CalendarioService $calendarioService) {
    // En producción, aquí se recibirían los IDs de los equipos por el Request o desde una tabla pivot
    $equiposIds = \App\Models\Equipo::pluck('id')->toArray();
    
    $fechaInicio = $request->input('fecha_inicio', now()->format('Y-m-d'));

    try {
        $resultado = $calendarioService->generar($competicion, $equiposIds, $fechaInicio);
        return response()->json($resultado);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
    }
})->middleware(['auth', 'role:admin'])->name('admin.calendario.generar');

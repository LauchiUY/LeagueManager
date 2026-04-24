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

// Ruta para el algoritmo del Calendario (Modificada temporalmente a GET sin middleware para que la pruebes fácilmente)
Route::get('/admin/generar-calendario/{competicion}', function (\Illuminate\Http\Request $request, $competicion, \App\Services\CalendarioService $calendarioService) {
    // Por simplicidad en la prueba, tomaremos todos los equipos de la base de datos
    $equiposIds = \App\Models\Equipo::pluck('id')->toArray();
    
    // Asignar la fecha de inicio recibida o la fecha actual por defecto
    $fechaInicio = $request->input('fecha_inicio', now()->format('Y-m-d'));

    try {
        $resultado = $calendarioService->generar($competicion, $equiposIds, $fechaInicio);
        return response()->json($resultado);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
    }
})->name('admin.calendario.generar');

// Ruta temporal para probar las sanciones automáticas
Route::get('/admin/test-sanciones', function () {
    // 1. Agarramos el primer partido que generaste antes y lo marcamos como finalizado
    $partido = \App\Models\Partido::first();
    if (!$partido) return "Primero debes generar el calendario.";
    
    $partido->update(['estado' => 'finalizado']);

    // 2. Creamos un jugador falso (Alineación Indebida)
    $jugadorIlegal = \App\Models\Usuario::create([
        'nombre' => 'Jugador Falso (No Inscrito)',
        'email' => 'falso' . rand(1, 1000) . '@test.com',
        'password' => bcrypt('password'),
        'rol' => 'jugador'
    ]);

    // 3. Añadimos un evento a este partido diciendo que este jugador jugó (ej. metió gol)
    \App\Models\EventoPartido::create([
        'id_partido' => $partido->id,
        'id_jugador' => $jugadorIlegal->id,
        'tipo_evento' => 'gol',
        'minuto' => 45
    ]);

    // 4. Ejecutamos el comando automático de sanciones
    \Illuminate\Support\Facades\Artisan::call('competicion:evaluar-sanciones');
    
    // 5. Mostramos el resultado
    return response()->json([
        'mensaje' => 'Simulación ejecutada con éxito.',
        'log_de_consola' => \Illuminate\Support\Facades\Artisan::output(),
        'sanciones_creadas' => \App\Models\Sancion::with('usuario')->get()
    ]);
});

// Ruta temporal simulando el Login
Route::get('/login', function () {
    return "¡Te hemos atrapado! El Middleware bloqueó tu acceso y te redirigió a esta pantalla de Login porque no eres Administrador.";
})->name('login');

// Ruta protegida por el Middleware que bloquea intrusos
Route::get('/solo-admins', function () {
    return "¡Hola Admin! Tienes acceso al panel secreto.";
})->middleware(['auth', 'role:admin']);

// Dashboard visual temporal para ver la Base de Datos
Route::get('/ver-resultados', function () {
    $partidos = \App\Models\Partido::with(['equipoLocal', 'equipoVisitante'])->orderBy('jornada')->get();
    $sanciones = \App\Models\Sancion::with('usuario')->get();
    
    $html = "<body style='font-family: sans-serif; padding: 20px; background: #f4f4f4;'>";
    $html .= "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>";
    $html .= "<h1 style='color: #FF2D20;'>🏆 Panel de Supervisión del Backend</h1>";
    
    $html .= "<h2>📅 Calendario Generado (Prueba de no solapamientos)</h2>";
    $html .= "<table style='width: 100%; border-collapse: collapse;' border='1' cellpadding='8'>";
    $html .= "<tr style='background: #eee;'><th>Jornada</th><th>Local</th><th>Visitante</th><th>Estado</th></tr>";
    foreach($partidos as $p) {
        $local = $p->equipoLocal ? $p->equipoLocal->nombre : 'Descansa (Impar)';
        $visit = $p->equipoVisitante ? $p->equipoVisitante->nombre : 'Descansa (Impar)';
        $html .= "<tr><td>Jornada {$p->jornada}</td><td>{$local}</td><td>{$visit}</td><td>{$p->estado}</td></tr>";
    }
    $html .= "</table>";
    
    $html .= "<h2 style='margin-top: 40px; color: red;'>🚨 Sanciones Automáticas</h2>";
    $html .= "<table style='width: 100%; border-collapse: collapse;' border='1' cellpadding='8'>";
    $html .= "<tr style='background: #fee;'><th>Jugador Infractor</th><th>Motivo (Robot Sanciones)</th><th>Castigo</th></tr>";
    foreach($sanciones as $s) {
        $nombre = $s->usuario ? $s->usuario->nombre : 'Desconocido';
        $html .= "<tr><td>{$nombre}</td><td>{$s->motivo}</td><td>{$s->partidos_suspension} Partidos</td></tr>";
    }
    if($sanciones->isEmpty()) {
        $html .= "<tr><td colspan='3'>Aún no se han ejecutado sanciones. Ve a /admin/test-sanciones primero.</td></tr>";
    }
    $html .= "</table>";
    
    $html .= "</div></body>";
    
    return response($html);
});

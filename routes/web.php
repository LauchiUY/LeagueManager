<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\CapitanController;
use App\Http\Controllers\ArbitroController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Equipos
    Route::get('/equipos', [EquipoController::class, 'index'])->name('equipos.index');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/equipos/create', [EquipoController::class, 'create'])->name('equipos.create');
        Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
        Route::get('/equipos/{id}/edit', [EquipoController::class, 'edit'])->name('equipos.edit');
        Route::put('/equipos/{id}', [EquipoController::class, 'update'])->name('equipos.update');
        Route::delete('/equipos/{id}', [EquipoController::class, 'destroy'])->name('equipos.destroy');
    });

    Route::get('/equipos/{id}', [EquipoController::class, 'show'])->name('equipos.show');

    // Clasificación y estadísticas
    Route::get('/clasificacion', [ClasificacionController::class, 'competiciones'])->name('clasificacion.competiciones');
    Route::get('/clasificacion/{competicion}', [ClasificacionController::class, 'index'])->name('clasificacion.index');
    Route::get('/clasificacion/{competicion}/pdf', [ClasificacionController::class, 'exportPdf'])->name('clasificacion.pdf');
    Route::get('/estadisticas/equipo/{equipo}', [ClasificacionController::class, 'estadisticasEquipo'])->name('estadisticas.equipo');
});

// Rutas del Árbitro (Panel de Actas)
Route::middleware(['auth', 'role:arbitro,admin'])->group(function () {
    Route::get('/partidos', [PartidoController::class, 'index'])->name('partidos.index');
    Route::get('/partidos/{id}', [PartidoController::class, 'show'])->name('partidos.show');
    Route::post('/partidos/{id}/evento', [PartidoController::class, 'registrarEvento'])->name('partidos.evento.store');
    Route::post('/partidos/{id}/validar', [PartidoController::class, 'validarActa'])->name('partidos.validar');
    Route::post('/partidos/{id}/acta', [PartidoController::class, 'subirFotoActa'])->name('partidos.acta.upload');
});

// Rutas del Capitán (Panel de Equipo y Convocatorias)
Route::middleware(['auth', 'role:capitan,admin'])->prefix('capitan')->name('capitan.')->group(function () {
    Route::get('/mi-equipo', [CapitanController::class, 'miEquipo'])->name('equipo');
    Route::post('/jugador/add', [CapitanController::class, 'addJugador'])->name('jugador.add');
    Route::delete('/jugador/{id}/remove', [CapitanController::class, 'removeJugador'])->name('jugador.remove');

    Route::get('/partido/{id}/convocatoria', [CapitanController::class, 'convocatoria'])->name('convocatoria');
    Route::post('/partido/{id}/convocatoria', [CapitanController::class, 'guardarConvocatoria'])->name('convocatoria.guardar');
    Route::post('/partido/{id}/aplazar', [CapitanController::class, 'solicitarAplazamiento'])->name('aplazar');
});

// Rutas del Administrador
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/competiciones', [AdminController::class, 'competiciones'])->name('competiciones');
    Route::post('/competiciones/crear', [AdminController::class, 'crearCompeticion'])->name('competiciones.crear');
    Route::put('/competiciones/{id}', [AdminController::class, 'actualizarCompeticion'])->name('competiciones.actualizar');
    Route::delete('/competiciones/{id}', [AdminController::class, 'eliminarCompeticion'])->name('competiciones.eliminar');
    Route::post('/competiciones/{id}/equipos', [AdminController::class, 'asignarEquipos'])->name('competiciones.equipos');
    Route::post('/competiciones/{id}/calendario', [AdminController::class, 'generarCalendario'])->name('competiciones.calendario');

    Route::get('/partidos', [AdminController::class, 'partidos'])->name('partidos');
    Route::post('/partidos/{id}/arbitro', [AdminController::class, 'asignarArbitro'])->name('partidos.arbitro');

    Route::get('/sanciones', [AdminController::class, 'sanciones'])->name('sanciones');
    Route::post('/sanciones/crear', [AdminController::class, 'crearSancion'])->name('sanciones.crear');
    Route::put('/sanciones/{id}', [AdminController::class, 'editarSancion'])->name('sanciones.editar');

    Route::get('/aplazamientos', [AdminController::class, 'aplazamientos'])->name('aplazamientos');
    Route::post('/aplazamientos/{id}/gestionar', [AdminController::class, 'gestionarAplazamiento'])->name('aplazamientos.gestionar');

    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
    Route::post('/usuarios/{id}/rol', [AdminController::class, 'cambiarRol'])->name('usuarios.rol');
});

// Perfil de usuario general
Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [App\Http\Controllers\PerfilController::class, 'index'])->name('perfil.index');
});

// Panel de Árbitro (Acta Digital) — rutas de Ayman
Route::middleware(['auth', 'role:arbitro'])->prefix('arbitro')->name('arbitro.')->group(function () {
    Route::get('/partidos', [ArbitroController::class, 'index'])->name('partidos');
    Route::get('/acta/{partido}', [ArbitroController::class, 'acta'])->name('acta');
    Route::post('/acta/{partido}/evento', [ArbitroController::class, 'registrarEvento'])->name('registrar_evento');
    Route::delete('/acta/{partido}/evento/{evento}', [ArbitroController::class, 'eliminarEvento'])->name('eliminar_evento');
    Route::post('/acta/{partido}/finalizar', [ArbitroController::class, 'finalizarPartido'])->name('finalizar_partido');
});

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Ruta protegida por el Middleware que bloquea intrusos
Route::get('/solo-admins', function () {
    return "¡Hola Admin! Tienes acceso al panel secreto.";
})->middleware(['auth', 'role:admin']);

// Dashboard visual temporal para ver la Base de Datos
Route::middleware(['auth', 'role:admin'])->get('/admin/ver-resultados', function () {
    $partidos = \App\Models\Partido::with(['equipoLocal', 'equipoVisitante'])->orderBy('jornada')->get();
    $sanciones = \App\Models\Sancion::with('usuario')->get();

    $html = "<body style='font-family: sans-serif; padding: 20px; background: #f4f4f4;'>";
    $html .= "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>";
    $html .= "<h1 style='color: #FF2D20;'>🏆 Panel de Supervisión del Backend</h1>";

    $html .= "<h2>📅 Calendario Generado (Prueba de no solapamientos)</h2>";
    $html .= "<table style='width: 100%; border-collapse: collapse;' border='1' cellpadding='8'>";
    $html .= "<tr style='background: #eee;'><th>Jornada</th><th>Local</th><th>Visitante</th><th>Estado</th></tr>";
    foreach ($partidos as $p) {
        $local = $p->equipoLocal ? $p->equipoLocal->nombre : 'Descansa (Impar)';
        $visit = $p->equipoVisitante ? $p->equipoVisitante->nombre : 'Descansa (Impar)';
        $html .= "<tr><td>Jornada {$p->jornada}</td><td>{$local}</td><td>{$visit}</td><td>{$p->estado}</td></tr>";
    }
    $html .= "</table>";

    $html .= "<h2 style='margin-top: 40px; color: red;'>🚨 Sanciones Automáticas</h2>";
    $html .= "<table style='width: 100%; border-collapse: collapse;' border='1' cellpadding='8'>";
    $html .= "<tr style='background: #fee;'><th>Jugador Infractor</th><th>Motivo (Robot Sanciones)</th><th>Castigo</th></tr>";
    foreach ($sanciones as $s) {
        $nombre = $s->usuario ? $s->usuario->nombre : 'Desconocido';
        $html .= "<tr><td>{$nombre}</td><td>{$s->motivo}</td><td>{$s->partidos_suspension} Partidos</td></tr>";
    }
    if ($sanciones->isEmpty()) {
        $html .= "<tr><td colspan='3'>Aún no se han ejecutado sanciones.</td></tr>";
    }
    $html .= "</table>";

    $html .= "</div></body>";

    return response($html);
})->name('admin.ver-resultados');

// Marcar notificación como leída
Route::post('/notificaciones/{id}/leer', function ($id) {
    auth()->user()->notifications()->findOrFail($id)->markAsRead();
    return back();
})->name('notificaciones.leer')->middleware('auth');

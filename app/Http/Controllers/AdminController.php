<?php

namespace App\Http\Controllers;

use App\Models\Competicion;
use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Sancion;
use App\Models\Usuario;
use App\Services\CalendarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Dashboard general de administración
     */
    public function dashboard()
    {
        $stats = [
            'equipos' => Equipo::count(),
            'partidos_pendientes' => Partido::where('estado', 'pendiente')->count(),
            'partidos_jugados' => Partido::where('estado', 'jugado')->count(),
            'sanciones_activas' => Sancion::where('estado', 'activa')->count(),
            'usuarios' => Usuario::count(),
            'competiciones' => Competicion::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Muestra las competiciones y permite gestionar sus equipos
     */
    public function competiciones()
    {
        $competiciones = Competicion::with('equipos')->withCount('partidos')->get();
        $equipos = Equipo::all();
        return view('admin.competiciones', compact('competiciones', 'equipos'));
    }

    /**
     * Crea una nueva competición
     */
    public function crearCompeticion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'deporte' => 'required|string|max:100',
            'puntos_victoria' => 'required|integer|min:0',
            'puntos_empate' => 'required|integer|min:0',
        ]);

        Competicion::create([
            'nombre' => $request->nombre,
            'deporte' => $request->deporte,
            'estado' => 'pendiente',
            'puntos_victoria' => $request->puntos_victoria,
            'puntos_empate' => $request->puntos_empate,
        ]);

        return back()->with('success', 'Competición creada correctamente.');
    }

    /**
     * Actualiza una competición existente
     */
    public function actualizarCompeticion(Request $request, $id)
    {
        $competicion = Competicion::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'deporte' => 'required|string|max:100',
            'estado' => 'required|in:pendiente,en_curso,finalizada,suspendida',
            'puntos_victoria' => 'required|integer|min:0',
            'puntos_empate' => 'required|integer|min:0',
        ]);

        $competicion->update($request->only(['nombre', 'deporte', 'estado', 'puntos_victoria', 'puntos_empate']));
        return back()->with('success', 'Competición actualizada correctamente.');
    }

    /**
     * Elimina una competición y todos sus partidos
     */
    public function eliminarCompeticion($id)
    {
        $competicion = Competicion::findOrFail($id);
        // Borrar partidos asociados primero
        $competicion->partidos()->delete();
        $competicion->equipos()->detach();
        $competicion->delete();
        return back()->with('success', 'Competición eliminada del sistema.');
    }

    /**
     * Asigna los equipos que participarán en una competición
     */
    public function asignarEquipos(Request $request, $id)
    {
        $competicion = Competicion::findOrFail($id);
        $request->validate([
            'equipos' => 'array',
            'equipos.*' => 'exists:equipos,id'
        ]);

        $competicion->equipos()->sync($request->equipos ?? []);
        return back()->with('success', 'Equipos asignados a la competición correctamente.');
    }

    /**
     * Llama al servicio del calendario para generarlo basado en los equipos asignados
     */
    public function generarCalendario(Request $request, $id, CalendarioService $calendarioService)
    {
        $competicion = Competicion::findOrFail($id);
        $equiposIds = $competicion->equipos()->pluck('equipos.id')->toArray();
        
        if (count($equiposIds) < 2) {
            return back()->with('error', 'Se necesitan al menos 2 equipos asignados para generar el calendario.');
        }

        // Si ya hay partidos para esta competición, no dejamos generar de nuevo a menos que se borren
        if ($competicion->partidos()->count() > 0) {
            return back()->with('error', 'Esta competición ya tiene un calendario generado.');
        }

        $fechaInicio = $request->input('fecha_inicio', now()->addDays(7)->format('Y-m-d'));

        try {
            $calendarioService->generarCalendario($competicion->id, $equiposIds, $fechaInicio);
            
            // Actualizar estado de la competición a 'en_curso' automáticamente
            $competicion->update(['estado' => 'en_curso']);
            
            return back()->with('success', 'Calendario generado con éxito (ida y vuelta) para los equipos seleccionados. La competición ha pasado a estar En Curso.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el calendario: ' . $e->getMessage());
        }
    }

    /**
     * Lista todos los partidos para su supervisión o para cambiar el árbitro
     */
    public function partidos()
    {
        $partidos = Partido::with(['equipoLocal', 'equipoVisitante', 'competicion', 'arbitro'])
            ->orderBy('fecha_hora', 'desc')
            ->paginate(15);
        $arbitros = Usuario::where('rol', 'arbitro')->get();
        return view('admin.partidos', compact('partidos', 'arbitros'));
    }

    /**
     * Asigna o cambia el árbitro de un partido
     */
    public function asignarArbitro(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);
        $request->validate(['id_arbitro' => 'required|exists:usuarios,id']);
        $partido->update(['id_arbitro' => $request->id_arbitro]);
        return back()->with('success', 'Árbitro asignado correctamente.');
    }

    /**
     * Muestra las sanciones activas e históricas
     */
    public function sanciones()
    {
        $sanciones = Sancion::with(['usuario', 'partidoOrigen'])
            ->orderBy('estado', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $usuarios = Usuario::whereIn('rol', ['jugador', 'capitan'])->get();
        return view('admin.sanciones', compact('sanciones', 'usuarios'));
    }

    /**
     * Crea una sanción manual (fuera del algoritmo)
     */
    public function crearSancion(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|exists:usuarios,id',
            'partidos_suspension' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
        ]);

        Sancion::create([
            'id_usuario' => $request->id_usuario,
            'id_partido_origen' => null, // Sanción disciplinaria general
            'partidos_suspension' => $request->partidos_suspension,
            'partidos_cumplidos' => 0,
            'motivo' => $request->motivo,
            'estado' => 'activa'
        ]);

        return back()->with('success', 'Sanción manual creada correctamente.');
    }

    /**
     * Edita una sanción existente
     */
    public function editarSancion(Request $request, $id)
    {
        $sancion = Sancion::findOrFail($id);

        $request->validate([
            'partidos_suspension' => 'required|integer|min:1',
            'partidos_cumplidos' => 'required|integer|min:0',
            'motivo' => 'required|string|max:255',
            'estado' => 'required|in:activa,cumplida',
        ]);

        $sancion->update([
            'partidos_suspension' => $request->partidos_suspension,
            'partidos_cumplidos' => $request->partidos_cumplidos,
            'motivo' => $request->motivo,
            'estado' => $request->estado,
        ]);

        return back()->with('success', 'Sanción actualizada correctamente.');
    }

    /**
     * Lista las solicitudes de aplazamiento enviadas por los capitanes
     */
    public function aplazamientos()
    {
        $aplazamientos = \App\Models\Aplazamiento::with(['partido.equipoLocal', 'partido.equipoVisitante', 'solicitante'])
            ->orderByRaw("FIELD(estado, 'pendiente', 'aprobado', 'rechazado')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.aplazamientos', compact('aplazamientos'));
    }

    /**
     * Aprueba o rechaza un aplazamiento
     */
    public function gestionarAplazamiento(Request $request, $id)
    {
        $aplazamiento = \App\Models\Aplazamiento::findOrFail($id);
        $request->validate(['accion' => 'required|in:aprobado,rechazado']);
        
        $aplazamiento->update(['estado' => $request->accion]);
        
        if ($request->accion === 'aprobado') {
            $aplazamiento->partido->update(['estado' => 'aplazado']);
        }

        // Enviar notificación al capitán que lo solicitó
        $aplazamiento->solicitante->notify(new \App\Notifications\AplazamientoResueltoNotification($aplazamiento));

        return back()->with('success', 'Solicitud de aplazamiento procesada: ' . ucfirst($request->accion));
    }
    /**
     * Lista todos los usuarios para gestionar roles
     */
    public function usuarios()
    {
        $usuarios = Usuario::orderBy('rol')->orderBy('nombre')->paginate(20);
        return view('admin.usuarios', compact('usuarios'));
    }

    /**
     * Cambia el rol de un usuario
     */
    public function cambiarRol(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $request->validate([
            'rol' => 'required|in:admin,arbitro,capitan,jugador'
        ]);

        // Evitar que el admin se quite a sí mismo su rol (si es el único o por error)
        if ($usuario->id === Auth::id() && $request->rol !== 'admin') {
            return back()->with('error', 'No puedes quitarte el rol de administrador a ti mismo.');
        }

        $usuario->update(['rol' => $request->rol]);

        return back()->with('success', 'Rol de ' . $usuario->nombre . ' cambiado a ' . ucfirst($request->rol) . ' correctamente.');
    }
}

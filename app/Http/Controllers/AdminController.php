<?php

namespace App\Http\Controllers;

use App\Models\Competicion;
use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Sancion;
use App\Models\Usuario;
use App\Services\CalendarioService;
use Illuminate\Http\Request;

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
            'usuarios' => Usuario::count()
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
            return back()->with('success', 'Calendario generado con éxito (ida y vuelta) para los equipos seleccionados.');
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

        return back()->with('success', 'Solicitud de aplazamiento procesada: ' . ucfirst($request->accion));
    }
}

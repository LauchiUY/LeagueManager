<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partido;
use App\Models\EventoPartido;
use App\Models\PlantillaJugador;
use Illuminate\Support\Facades\Artisan;

class ArbitroController extends Controller
{
    /**
     * Muestra la lista de partidos asignados al árbitro.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener los partidos asignados a este árbitro
        $partidos = Partido::with(['equipoLocal', 'equipoVisitante', 'competicion'])
            ->where('id_arbitro', $user->id)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        return view('arbitro.index', compact('partidos'));
    }

    /**
     * Muestra el acta digital de un partido concreto.
     */
    public function acta(Partido $partido)
    {
        // Verificar que el árbitro es el asignado
        if ($partido->id_arbitro !== Auth::id()) {
            return redirect()->route('arbitro.partidos')->with('error', 'No tienes permiso para gestionar el acta de este partido.');
        }

        // Cargar las relaciones necesarias (plantillas y eventos actuales)
        $partido->load([
            'equipoLocal.plantilla.usuario', 
            'equipoVisitante.plantilla.usuario',
            'eventoPartido.jugador'
        ]);

        return view('arbitro.acta', compact('partido'));
    }

    /**
     * Registra un evento (Gol, Amarilla, Roja) en el partido.
     */
    public function registrarEvento(Request $request, Partido $partido)
    {
        // Validar el estado del partido
        if ($partido->estado === 'finalizado') {
            return redirect()->back()->with('error', 'El partido ya está finalizado. No se pueden modificar eventos.');
        }

        $request->validate([
            'id_jugador' => 'required|exists:usuarios,id',
            'id_equipo' => 'required|exists:equipos,id',
            'tipo_evento' => 'required|in:gol,tarjeta_amarilla,tarjeta_roja',
            'minuto' => 'nullable|integer|min:1|max:120'
        ]);

        // Registrar el evento
        EventoPartido::create([
            'id_partido' => $partido->id,
            'id_jugador' => $request->id_jugador,
            'tipo_evento' => $request->tipo_evento,
            'minuto' => $request->minuto ?? rand(1, 90), // Si no se pone minuto, se simula
            'observaciones' => null
        ]);

        // Si es un gol, actualizar el marcador del partido
        if ($request->tipo_evento === 'gol') {
            if ($partido->id_local == $request->id_equipo) {
                // Inicializar a 0 si es null
                $golesActuales = $partido->goles_local ?? 0;
                $partido->update(['goles_local' => $golesActuales + 1]);
            } elseif ($partido->id_visitante == $request->id_equipo) {
                $golesActuales = $partido->goles_visitante ?? 0;
                $partido->update(['goles_visitante' => $golesActuales + 1]);
            }
        }

        return redirect()->back()->with('success', 'Evento registrado correctamente.');
    }

    /**
     * Cierra el acta del partido, disparando recalculo y sanciones.
     */
    public function finalizarPartido(Partido $partido)
    {
        // Verificar que el árbitro es el asignado
        if ($partido->id_arbitro !== Auth::id()) {
            return redirect()->route('arbitro.partidos')->with('error', 'No tienes permiso.');
        }

        if ($partido->estado === 'finalizado') {
            return redirect()->back()->with('error', 'El partido ya estaba finalizado.');
        }

        // Por seguridad, si el partido no tuvo goles, lo ponemos a 0-0 en vez de null
        $golesLocal = $partido->goles_local ?? 0;
        $golesVisitante = $partido->goles_visitante ?? 0;

        $partido->update([
            'estado' => 'finalizado',
            'goles_local' => $golesLocal,
            'goles_visitante' => $golesVisitante
        ]);

        // Recalcular la clasificación de esa competición
        app(ClasificacionController::class)->recalcular($partido->id_competicion);

        // Disparar la evaluación de sanciones automática
        Artisan::call('competicion:evaluar-sanciones');

        return redirect()->route('arbitro.partidos')->with('success', '¡Acta validada y cerrada correctamente! Sanciones y clasificación actualizadas.');
    }
}

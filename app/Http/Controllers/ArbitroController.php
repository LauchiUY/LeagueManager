<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partido;
use App\Models\EventoPartido;
use App\Models\PlantillaJugador;
use App\Models\Convocatoria;
use App\Services\SancionesService;

class ArbitroController extends Controller
{
    /**
     * Muestra la lista de partidos asignados al árbitro (pendientes + historial).
     */
    public function index()
    {
        $user = Auth::user();
        
        // Partidos pendientes (no finalizados)
        $partidosPendientes = Partido::with(['equipoLocal', 'equipoVisitante', 'competicion'])
            ->where('id_arbitro', $user->id)
            ->whereNotIn('estado', ['finalizado', 'jugado'])
            ->orderBy('fecha_hora', 'asc')
            ->get();

        // Historial (finalizados/jugados)
        $partidosHistorial = Partido::with(['equipoLocal', 'equipoVisitante', 'competicion'])
            ->where('id_arbitro', $user->id)
            ->whereIn('estado', ['finalizado', 'jugado'])
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return view('arbitro.index', compact('partidosPendientes', 'partidosHistorial'));
    }

    /**
     * Muestra el acta digital de un partido concreto.
     * Solo muestra jugadores CONVOCADOS, no toda la plantilla.
     */
    public function acta(Partido $partido)
    {
        // Verificar que el árbitro es el asignado
        if ($partido->id_arbitro !== Auth::id()) {
            return redirect()->route('arbitro.partidos')->with('error', 'No tienes permiso para gestionar el acta de este partido.');
        }

        $partido->load(['equipoLocal', 'equipoVisitante', 'eventoPartido.jugador']);

        // Obtener solo jugadores CONVOCADOS para este partido
        $convocadosLocalIds = Convocatoria::where('id_partido', $partido->id)
            ->where('id_equipo', $partido->id_local)
            ->pluck('id_usuario');
        $convocadosVisitanteIds = Convocatoria::where('id_partido', $partido->id)
            ->where('id_equipo', $partido->id_visitante)
            ->pluck('id_usuario');

        $jugadoresLocal = PlantillaJugador::with('usuario')
            ->where('id_equipo', $partido->id_local)
            ->where('estado', 'activo')
            ->when($convocadosLocalIds->isNotEmpty(), function ($q) use ($convocadosLocalIds) {
                $q->whereIn('id_usuario', $convocadosLocalIds);
            })
            ->get();

        $jugadoresVisitante = PlantillaJugador::with('usuario')
            ->where('id_equipo', $partido->id_visitante)
            ->where('estado', 'activo')
            ->when($convocadosVisitanteIds->isNotEmpty(), function ($q) use ($convocadosVisitanteIds) {
                $q->whereIn('id_usuario', $convocadosVisitanteIds);
            })
            ->get();

        // Contar amarillas por jugador en este partido (para indicar visualmente)
        $amarillasPorJugador = EventoPartido::where('id_partido', $partido->id)
            ->where('tipo_evento', 'Amarilla')
            ->selectRaw('id_jugador, COUNT(*) as total')
            ->groupBy('id_jugador')
            ->pluck('total', 'id_jugador');

        return view('arbitro.acta', compact('partido', 'jugadoresLocal', 'jugadoresVisitante', 'amarillasPorJugador'));
    }

    /**
     * Registra un evento (Gol, Autogol, Amarilla, Roja) en el partido.
     * Controla doble amarilla → roja automática.
     */
    public function registrarEvento(Request $request, Partido $partido)
    {
        // Validar el estado del partido
        if (in_array($partido->estado, ['finalizado', 'jugado'])) {
            return redirect()->back()->with('error', 'El partido ya está finalizado. No se pueden modificar eventos.');
        }

        $request->validate([
            'id_jugador' => 'required|exists:usuarios,id',
            'id_equipo' => 'required|exists:equipos,id',
            'tipo_evento' => 'required|in:Gol,Autogol,Amarilla,Roja',
            'minuto' => 'nullable|integer|min:1|max:120'
        ]);

        $minuto = $request->minuto ?? rand(1, 90);

        // Si estaba pendiente, al añadir el primer evento pasa a "en_curso"
        if ($partido->estado === 'pendiente') {
            $partido->update(['estado' => 'en_curso']);
        }

        // Registrar el evento
        EventoPartido::create([
            'id_partido' => $partido->id,
            'id_jugador' => $request->id_jugador,
            'tipo_evento' => $request->tipo_evento,
            'minuto' => $minuto,
            'observaciones' => $request->observaciones ?? null
        ]);

        // Lógica de goles
        if ($request->tipo_evento === 'Gol') {
            // Gol normal: suma al equipo del jugador
            if ($partido->id_local == $request->id_equipo) {
                $partido->update(['goles_local' => ($partido->goles_local ?? 0) + 1]);
            } elseif ($partido->id_visitante == $request->id_equipo) {
                $partido->update(['goles_visitante' => ($partido->goles_visitante ?? 0) + 1]);
            }
        } elseif ($request->tipo_evento === 'Autogol') {
            // Autogol: suma al equipo CONTRARIO
            if ($partido->id_local == $request->id_equipo) {
                $partido->update(['goles_visitante' => ($partido->goles_visitante ?? 0) + 1]);
            } elseif ($partido->id_visitante == $request->id_equipo) {
                $partido->update(['goles_local' => ($partido->goles_local ?? 0) + 1]);
            }
        }

        // Lógica de tarjeta roja → sanción automática
        if ($request->tipo_evento === 'Roja') {
            $sancionesService = new SancionesService();
            $sancionesService->aplicarSancionTarjetaRoja($request->id_jugador, $partido->id);
        }

        // Lógica de doble amarilla → roja automática + sanción
        if ($request->tipo_evento === 'Amarilla') {
            $amarillasEnPartido = EventoPartido::where('id_partido', $partido->id)
                ->where('id_jugador', $request->id_jugador)
                ->where('tipo_evento', 'Amarilla')
                ->count();

            if ($amarillasEnPartido >= 2) {
                // Crear evento de roja automática por doble amarilla
                EventoPartido::create([
                    'id_partido' => $partido->id,
                    'id_jugador' => $request->id_jugador,
                    'tipo_evento' => 'Roja',
                    'minuto' => $minuto,
                    'observaciones' => 'Roja automática por doble amarilla'
                ]);

                // Aplicar sanción
                $sancionesService = new SancionesService();
                $sancionesService->aplicarSancionTarjetaRoja($request->id_jugador, $partido->id);

                return redirect()->back()->with('success', '⚠️ Doble amarilla detectada → Tarjeta roja automática y sanción aplicada.');
            }
        }

        return redirect()->back()->with('success', 'Evento registrado correctamente.');
    }

    /**
     * Elimina un evento del acta y ajusta el marcador si era un gol/autogol.
     */
    public function eliminarEvento(Partido $partido, EventoPartido $evento)
    {
        // Verificar que el árbitro es el asignado
        if ($partido->id_arbitro !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        if (in_array($partido->estado, ['finalizado', 'jugado'])) {
            return redirect()->back()->with('error', 'No se pueden eliminar eventos de un partido finalizado.');
        }

        // Verificar que el evento pertenece a este partido
        if ($evento->id_partido !== $partido->id) {
            return redirect()->back()->with('error', 'El evento no pertenece a este partido.');
        }

        // Determinar equipo del jugador
        $equipoJugador = PlantillaJugador::where('id_usuario', $evento->id_jugador)
            ->whereIn('id_equipo', [$partido->id_local, $partido->id_visitante])
            ->value('id_equipo');

        // Ajustar marcador si era gol o autogol
        if ($evento->tipo_evento === 'Gol' && $equipoJugador) {
            if ($equipoJugador == $partido->id_local && $partido->goles_local > 0) {
                $partido->update(['goles_local' => $partido->goles_local - 1]);
            } elseif ($equipoJugador == $partido->id_visitante && $partido->goles_visitante > 0) {
                $partido->update(['goles_visitante' => $partido->goles_visitante - 1]);
            }
        } elseif ($evento->tipo_evento === 'Autogol' && $equipoJugador) {
            // Autogol sumaba al contrario, así que restamos del contrario
            if ($equipoJugador == $partido->id_local && $partido->goles_visitante > 0) {
                $partido->update(['goles_visitante' => $partido->goles_visitante - 1]);
            } elseif ($equipoJugador == $partido->id_visitante && $partido->goles_local > 0) {
                $partido->update(['goles_local' => $partido->goles_local - 1]);
            }
        }

        $evento->delete();

        return redirect()->back()->with('success', 'Evento eliminado y marcador actualizado.');
    }

    /**
     * Cierra el acta del partido, disparando recálculo y sanciones.
     */
    public function finalizarPartido(Partido $partido)
    {
        // Verificar que el árbitro es el asignado
        if ($partido->id_arbitro !== Auth::id()) {
            return redirect()->route('arbitro.partidos')->with('error', 'No tienes permiso.');
        }

        if (in_array($partido->estado, ['finalizado', 'jugado'])) {
            return redirect()->back()->with('error', 'El partido ya estaba finalizado.');
        }

        $partido->load('eventoPartido');

        // Obtener jugadores de cada equipo
        $jugadoresLocalIds = PlantillaJugador::where('id_equipo', $partido->id_local)
            ->where('estado', 'activo')
            ->pluck('id_usuario');
        $jugadoresVisitanteIds = PlantillaJugador::where('id_equipo', $partido->id_visitante)
            ->where('estado', 'activo')
            ->pluck('id_usuario');

        // Calcular goles desde los eventos registrados
        $golesLocal = $partido->eventoPartido
            ->where('tipo_evento', 'Gol')
            ->whereIn('id_jugador', $jugadoresLocalIds)
            ->count();
        $golesLocal += $partido->eventoPartido
            ->where('tipo_evento', 'Autogol')
            ->whereIn('id_jugador', $jugadoresVisitanteIds)
            ->count();

        $golesVisitante = $partido->eventoPartido
            ->where('tipo_evento', 'Gol')
            ->whereIn('id_jugador', $jugadoresVisitanteIds)
            ->count();
        $golesVisitante += $partido->eventoPartido
            ->where('tipo_evento', 'Autogol')
            ->whereIn('id_jugador', $jugadoresLocalIds)
            ->count();

        $partido->update([
            'estado' => 'finalizado',
            'goles_local' => $golesLocal,
            'goles_visitante' => $golesVisitante
        ]);

        // Disparar la evaluación de sanciones automática
        try {
            $sancionesService = new SancionesService();
            $sancionesService->evaluarAlineacionesIndebidas($partido->id);
            $sancionesService->avanzarSancionesCumplidas($partido->id);
        } catch (\Exception $e) {
            // Ignorar errores de sanciones para no bloquear la finalización
        }

        return redirect()->route('arbitro.partidos')->with('success', "¡Acta cerrada! Resultado oficial: {$golesLocal} - {$golesVisitante} (calculado desde eventos).");
    }
}

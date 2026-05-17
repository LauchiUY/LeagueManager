<?php

namespace App\Http\Controllers;

use App\Models\Competicion;
use App\Models\Equipo;
use App\Models\Partido;
use App\Models\EventoPartido;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ClasificacionController extends Controller
{
    /**
     * Muestra la tabla de clasificación de una competición.
     */
    public function index($competicionId)
    {
        $competicion = Competicion::findOrFail($competicionId);

        // Obtener todos los partidos jugados de la competición con Eager Loading
        $partidos = Partido::with(['equipoLocal', 'equipoVisitante'])
            ->where('id_competicion', $competicionId)
            ->where('estado', 'jugado')
            ->get();

        // Obtener todos los equipos que participan en esta competición
        $equipoIds = $partidos->pluck('id_local')
            ->merge($partidos->pluck('id_visitante'))
            ->unique();

        $equipos = Equipo::whereIn('id', $equipoIds)->get();

        // Calcular la tabla de clasificación
        $clasificacion = $this->calcularClasificacion($equipos, $partidos, $competicion);

        // Datos para Chart.js
        $chartData = [
            'labels' => $clasificacion->pluck('nombre')->toArray(),
            'puntos' => $clasificacion->pluck('puntos')->toArray(),
            'goles_favor' => $clasificacion->pluck('gf')->toArray(),
            'goles_contra' => $clasificacion->pluck('gc')->toArray(),
        ];

        return view('clasificacion.index', compact('competicion', 'clasificacion', 'chartData'));
    }

    /**
     * Muestra las estadísticas detalladas de un equipo.
     */
    public function estadisticasEquipo($equipoId)
    {
        $equipo = Equipo::with('plantilla.usuario')->findOrFail($equipoId);

        // Partidos del equipo (local y visitante)
        $partidos = Partido::where(function ($q) use ($equipoId) {
                $q->where('id_local', $equipoId)->orWhere('id_visitante', $equipoId);
            })
            ->where('estado', 'jugado')
            ->orderBy('jornada')
            ->get();

        // Datos por jornada para Chart.js
        $jornadas = [];
        $golesPorJornada = [];
        $golesRecibidosPorJornada = [];
        $puntosPorJornada = [];
        $puntosAcumulados = 0;
        $puntosAcumuladosPorJornada = [];

        foreach ($partidos as $partido) {
            $esLocal = $partido->id_local == $equipoId;
            $gf = $esLocal ? $partido->goles_local : $partido->goles_visitante;
            $gc = $esLocal ? $partido->goles_visitante : $partido->goles_local;

            $puntosPartido = 0;
            if ($gf > $gc) $puntosPartido = 3;
            elseif ($gf == $gc) $puntosPartido = 1;

            $puntosAcumulados += $puntosPartido;

            $jornadas[] = 'J' . $partido->jornada;
            $golesPorJornada[] = $gf;
            $golesRecibidosPorJornada[] = $gc;
            $puntosPorJornada[] = $puntosPartido;
            $puntosAcumuladosPorJornada[] = $puntosAcumulados;
        }

        // Eventos del equipo (goles, tarjetas)
        $jugadoresIds = $equipo->plantilla()->pluck('id_usuario');
        $eventos = EventoPartido::whereIn('id_jugador', $jugadoresIds)->get();

        $totalGoles = $eventos->where('tipo_evento', 'Gol')->count();
        $totalAmarillas = $eventos->where('tipo_evento', 'Amarilla')->count();
        $totalRojas = $eventos->where('tipo_evento', 'Roja')->count();

        // Goleadores del equipo
        $goleadores = $eventos->where('tipo_evento', 'Gol')
            ->groupBy('id_jugador')
            ->map(function ($group) {
                return [
                    'jugador' => $group->first()->jugador->nombre ?? 'Desconocido',
                    'goles' => $group->count(),
                ];
            })
            ->sortByDesc('goles')
            ->values();

        $chartData = [
            'jornadas' => $jornadas,
            'goles_favor' => $golesPorJornada,
            'goles_contra' => $golesRecibidosPorJornada,
            'puntos_acumulados' => $puntosAcumuladosPorJornada,
            'goleadores_labels' => $goleadores->pluck('jugador')->toArray(),
            'goleadores_data' => $goleadores->pluck('goles')->toArray(),
        ];

        return view('estadisticas.equipo', compact(
            'equipo', 'partidos', 'chartData',
            'totalGoles', 'totalAmarillas', 'totalRojas', 'goleadores'
        ));
    }

    /**
     * Exporta la clasificación a PDF.
     */
    public function exportPdf($competicionId)
    {
        $competicion = Competicion::findOrFail($competicionId);

        $partidos = Partido::with(['equipoLocal', 'equipoVisitante'])
            ->where('id_competicion', $competicionId)
            ->where('estado', 'jugado')
            ->get();

        $equipoIds = $partidos->pluck('id_local')
            ->merge($partidos->pluck('id_visitante'))
            ->unique();

        $equipos = Equipo::whereIn('id', $equipoIds)->get();

        $clasificacion = $this->calcularClasificacion($equipos, $partidos, $competicion);

        $pdf = Pdf::loadView('pdf.clasificacion', compact('competicion', 'clasificacion'));

        return $pdf->download('clasificacion_' . str_replace(' ', '_', $competicion->nombre) . '.pdf');
    }

    /**
     * Lista todas las competiciones disponibles.
     */
    public function competiciones()
    {
        $competiciones = Competicion::all();
        return view('clasificacion.competiciones', compact('competiciones'));
    }

    /**
     * Calcula la tabla de clasificación a partir de los partidos jugados.
     */
    private function calcularClasificacion($equipos, $partidos, $competicion)
    {
        $tabla = [];

        foreach ($equipos as $equipo) {
            $tabla[$equipo->id] = [
                'id' => $equipo->id,
                'nombre' => $equipo->nombre,
                'logo_url' => $equipo->logo_url,
                'pj' => 0, 'pg' => 0, 'pe' => 0, 'pp' => 0,
                'gf' => 0, 'gc' => 0, 'dg' => 0, 'puntos' => 0,
            ];
        }

        foreach ($partidos as $partido) {
            $local = $partido->id_local;
            $visitante = $partido->id_visitante;
            $gl = $partido->goles_local;
            $gv = $partido->goles_visitante;

            if (!isset($tabla[$local]) || !isset($tabla[$visitante])) continue;

            // Partidos jugados
            $tabla[$local]['pj']++;
            $tabla[$visitante]['pj']++;

            // Goles
            $tabla[$local]['gf'] += $gl;
            $tabla[$local]['gc'] += $gv;
            $tabla[$visitante]['gf'] += $gv;
            $tabla[$visitante]['gc'] += $gl;

            // Resultado
            if ($gl > $gv) {
                $tabla[$local]['pg']++;
                $tabla[$local]['puntos'] += $competicion->puntos_victoria;
                $tabla[$visitante]['pp']++;
            } elseif ($gl < $gv) {
                $tabla[$visitante]['pg']++;
                $tabla[$visitante]['puntos'] += $competicion->puntos_victoria;
                $tabla[$local]['pp']++;
            } else {
                $tabla[$local]['pe']++;
                $tabla[$local]['puntos'] += $competicion->puntos_empate;
                $tabla[$visitante]['pe']++;
                $tabla[$visitante]['puntos'] += $competicion->puntos_empate;
            }
        }

        // Diferencia de goles
        foreach ($tabla as &$fila) {
            $fila['dg'] = $fila['gf'] - $fila['gc'];
        }

        // Ordenar: puntos > diferencia goles > goles a favor
        $clasificacion = collect($tabla)->sortBy([
            ['puntos', 'desc'],
            ['dg', 'desc'],
            ['gf', 'desc'],
        ])->values();

        return $clasificacion;
    }
}

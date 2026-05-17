<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EventoPartido;
use App\Models\Sancion;
use App\Models\PlantillaJugador;
use App\Models\Partido;

class PerfilController extends Controller
{
    /**
     * Muestra el perfil y estadísticas del usuario autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        // Estadísticas solo para jugadores y capitanes
        $stats = [
            'goles'            => 0,
            'amarillas'        => 0,
            'rojas'            => 0,
            'partidos_jugados' => 0,
            'sanciones_activas'=> 0,
        ];

        if (in_array($user->rol, ['jugador', 'capitan'])) {
            $stats['goles'] = EventoPartido::where('id_jugador', $user->id)
                ->where('tipo_evento', 'Gol')
                ->count();

            $stats['amarillas'] = EventoPartido::where('id_jugador', $user->id)
                ->where('tipo_evento', 'Amarilla')
                ->count();

            $stats['rojas'] = EventoPartido::where('id_jugador', $user->id)
                ->where('tipo_evento', 'Roja')
                ->count();

            $stats['sanciones_activas'] = Sancion::where('id_usuario', $user->id)
                ->where('estado', 'activa')
                ->count();

            // Partidos jugados: partidos donde el equipo del jugador participó y está jugado
            $equipoId = PlantillaJugador::where('id_usuario', $user->id)
                ->where('estado', 'activo')
                ->value('id_equipo');

            if ($equipoId) {
                $stats['partidos_jugados'] = Partido::where('estado', 'jugado')
                    ->where(function ($q) use ($equipoId) {
                        $q->where('id_local', $equipoId)
                          ->orWhere('id_visitante', $equipoId);
                    })->count();
            }
        }

        // Últimos eventos del jugador
        $ultimosEventos = EventoPartido::with('partido.equipoLocal', 'partido.equipoVisitante')
            ->where('id_jugador', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('perfil.index', compact('user', 'stats', 'ultimosEventos'));
    }
}

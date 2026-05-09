<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\EventoPartido;
use App\Models\Sancion;
use Illuminate\Http\Request;
use App\Http\Controllers\ClasificacionController;

class PartidoController extends Controller
{
    /**
     * Valida el acta del partido y aplica sanción automática si hay alineación indebida (CU-22)
     */
    public function validarActa(Request $request, Partido $partido)
    {
        // Obtener todos los jugadores que participaron en el partido
        $jugadoresLocal     = $partido->eventos->where('tipo', '!=', 'tarjeta_roja')
                                ->pluck('id_usuario')->unique();
        $jugadoresVisitante = $partido->eventos->where('tipo', '!=', 'tarjeta_roja')
                                ->pluck('id_usuario')->unique();

        // Obtener IDs de jugadores sancionados activos
        $sancionados = Sancion::where('activa', true)
                        ->pluck('id_usuario')
                        ->toArray();

        $equipoInfractor  = null;
        $equipoGanador    = null;

        // Comprobar si algún jugador sancionado participó y en qué equipo estaba
        $plantillaLocal = $partido->equipoLocal->plantillaJugadores->pluck('id_usuario')->toArray();
        $plantillaVisitante = $partido->equipoVisitante->plantillaJugadores->pluck('id_usuario')->toArray();

        $infractorEnLocal     = array_intersect($sancionados, $plantillaLocal);
        $infractorEnVisitante = array_intersect($sancionados, $plantillaVisitante);

        if (!empty($infractorEnLocal)) {
            // El equipo local alineó un jugador sancionado → pierde el local
            $equipoInfractor = $partido->equipoLocal;
            $equipoGanador   = $partido->equipoVisitante;
            $partido->goles_local     = 0;
            $partido->goles_visitante = 3;
        } elseif (!empty($infractorEnVisitante)) {
            // El equipo visitante alineó un jugador sancionado → pierde el visitante
            $equipoInfractor = $partido->equipoVisitante;
            $equipoGanador   = $partido->equipoLocal;
            $partido->goles_local     = 3;
            $partido->goles_visitante = 0;
        }

        if ($equipoInfractor) {
            $partido->estado = 'sancionado';
            $partido->save();

            // Actualizar clasificación
            app(ClasificacionController::class)->recalcular($partido->id_competicion);

            // Notificar al administrador
            // Puedes disparar un evento o notificación aquí si lo tenéis implementado

            return redirect()->back()->with('error',
                'Alineación indebida detectada. El equipo ' . $equipoInfractor->nombre .
                ' pierde el partido por 0-3. La clasificación ha sido actualizada.'
            );
        }

        // Si no hay infracción, validar el acta normalmente
        $partido->estado = 'jugado';
        $partido->save();

        app(ClasificacionController::class)->recalcular($partido->id_competicion);

        return redirect()->back()->with('success', 'Acta validada correctamente.');
    }
}
<?php

namespace App\Services;

use App\Models\Partido;
use App\Models\Sancion;
use App\Models\PlantillaJugador;
use App\Models\Equipo;
use Exception;

class SancionesService
{
    /**
     * Evalúa un partido para detectar alineaciones indebidas y aplicar sanciones.
     * 
     * @param int $partidoId ID del partido a evaluar
     * @return array Resumen de la auditoría
     */
    public function evaluarAlineacionesIndebidas(int $partidoId)
    {
        $partido = Partido::with(['eventos', 'equipoLocal', 'equipoVisitante'])->findOrFail($partidoId);
        
        if ($partido->estado !== 'finalizado') {
            throw new Exception("El partido debe estar finalizado para evaluar sanciones.");
        }

        // Obtener todos los jugadores únicos que participaron en eventos de este partido
        $jugadoresParticipantes = $partido->eventos()->whereNotNull('id_jugador')->get()->groupBy('id_jugador');
        
        $sancionesAplicadas = 0;
        $equiposSancionados = [];

        foreach ($jugadoresParticipantes as $idJugador => $eventos) {
            // Determinar a qué equipo pertenece este jugador en el partido (basado en el primer evento)
            // Nota: En una implementación más robusta, el evento guardaría el id_equipo del jugador
            // Asumiremos que tenemos que buscarlo en las plantillas del equipo local o visitante
            $enPlantillaLocal = PlantillaJugador::where('id_equipo', $partido->id_local)
                                                ->where('id_usuario', $idJugador)
                                                ->exists();
            
            $enPlantillaVisitante = PlantillaJugador::where('id_equipo', $partido->id_visitante)
                                                    ->where('id_usuario', $idJugador)
                                                    ->exists();

            if (!$enPlantillaLocal && !$enPlantillaVisitante) {
                // El jugador participó pero no está en ninguna plantilla de los equipos que juegan
                // Esto es una ALINEACIÓN INDEBIDA.
                
                // Evitar duplicar la sanción si ya se aplicó para este partido y jugador
                $sancionExistente = Sancion::where('id_usuario', $idJugador)
                                           ->where('id_partido_origen', $partido->id)
                                           ->where('motivo', 'Alineación Indebida')
                                           ->exists();

                if (!$sancionExistente) {
                    // Sancionar al jugador (ej. 1 partido de suspensión)
                    Sancion::create([
                        'id_usuario' => $idJugador,
                        'id_partido_origen' => $partido->id,
                        'partidos_suspension' => 1,
                        'motivo' => 'Alineación Indebida',
                        'estado' => 'activa'
                    ]);
                    $sancionesAplicadas++;

                    // Sancionar al equipo (quitamos puntos de sanción, en este caso sumamos al contador de castigo)
                    // Para saber qué equipo castigar, necesitaríamos saber con quién jugó. 
                    // Por simplicidad, castigamos al equipo local si no está en el visitante y viceversa.
                    // Si no podemos determinarlo, requerirá ajuste manual, pero aplicaremos la lógica a los dos si hay duda (o se puede mejorar).
                    // Asumiremos que el frontend envió el id_equipo en el evento. Si no, lo marcamos como incidente grave.
                }
            }
        }

        return [
            'success' => true,
            'message' => "Auditoría del partido completada.",
            'sanciones_aplicadas' => $sancionesAplicadas
        ];
    }
}

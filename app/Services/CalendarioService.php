<?php

namespace App\Services;

use App\Models\Competicion;
use App\Models\Partido;
use Carbon\Carbon;
use Exception;

class CalendarioService
{
    /**
     * Genera un calendario automático para la competición usando el algoritmo Round-Robin.
     * 
     * @param int $competicionId ID de la competición
     * @param array $equiposIds Array con los IDs de los equipos participantes
     * @param string $fechaInicio Fecha del primer partido (Y-m-d)
     * @return array Resumen de la operación
     */
    public function generar(int $competicionId, array $equiposIds, string $fechaInicio)
    {
        $competicion = Competicion::findOrFail($competicionId);
        $equipos = $equiposIds;
        
        // Si no hay suficientes equipos, lanzar excepción
        if (count($equipos) < 2) {
            throw new Exception("Se necesitan al menos 2 equipos para generar un calendario.");
        }

        // Si el número de equipos es impar, añadimos un equipo "fantasma" (bye)
        $esImpar = count($equipos) % 2 !== 0;
        if ($esImpar) {
            $equipos[] = null; // null representará que el equipo descansa
        }

        $totalEquipos = count($equipos);
        $totalJornadas = $totalEquipos - 1;
        $mitad = $totalEquipos / 2;

        $fechaActual = Carbon::parse($fechaInicio);
        $partidosCreados = 0;

        for ($jornada = 1; $jornada <= $totalJornadas; $jornada++) {
            for ($i = 0; $i < $mitad; $i++) {
                $equipoLocal = $equipos[$i];
                $equipoVisitante = $equipos[$totalEquipos - 1 - $i];

                // Si ninguno de los dos es el equipo fantasma (null), creamos el partido
                if ($equipoLocal !== null && $equipoVisitante !== null) {
                    
                    // Alternar localía en jornadas pares para el primer equipo para que no juegue siempre en casa
                    if ($i === 0 && $jornada % 2 === 0) {
                        $temp = $equipoLocal;
                        $equipoLocal = $equipoVisitante;
                        $equipoVisitante = $temp;
                    }

                    Partido::create([
                        'id_competicion' => $competicion->id,
                        'id_local' => $equipoLocal,
                        'id_visitante' => $equipoVisitante,
                        'jornada' => $jornada,
                        'fecha_hora' => $fechaActual->copy()->addHours(18), // Ejemplo: Todos los partidos a las 18:00
                        'campo_pista' => 'Campo Central',
                        'estado' => 'pendiente',
                    ]);
                    $partidosCreados++;
                }
            }

            // Rotar el array de equipos para la siguiente jornada (Round-Robin)
            // El primer equipo se queda fijo, los demás rotan en el sentido de las agujas del reloj
            $fijo = $equipos[0];
            $ultimo = array_pop($equipos);
            array_splice($equipos, 1, 0, [$ultimo]);
            $equipos[0] = $fijo;

            // Añadir 7 días para la próxima jornada
            $fechaActual->addDays(7);
        }

        return [
            'success' => true,
            'message' => "Calendario generado exitosamente.",
            'jornadas' => $totalJornadas,
            'partidos_creados' => $partidosCreados
        ];
    }
}

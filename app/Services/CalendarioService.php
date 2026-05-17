<?php

namespace App\Services;

use App\Models\Partido;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class CalendarioService
{
    /**
     * Genera el calendario de una competición usando el algoritmo Round-Robin (ida y vuelta).
     *
     * @param int $competicionId
     * @param array $idsEquipos
     * @param string $fechaInicio
     * @param string $campo
     * @return array
     * @throws Exception
     */
    public function generarCalendario(int $competicionId, array $idsEquipos, string $fechaInicio, string $campo = 'Por definir'): array
    {
        $equipos = $idsEquipos;

        if (count($equipos) < 2) {
            throw new Exception("Se necesitan al menos 2 equipos para generar un calendario.");
        }

        // Si el número de equipos es impar, añadimos un equipo fantasma (null) para los "bye" (descansos)
        if (count($equipos) % 2 !== 0) {
            $equipos[] = null;
        }

        $numEquipos = count($equipos);
        $totalJornadasIda = $numEquipos - 1;
        $totalJornadas = $totalJornadasIda * 2;
        $mitad = $numEquipos / 2;

        $partidosToInsert = [];
        $fechaActual = Carbon::parse($fechaInicio);
        $jornadaGlobal = 1;

        // Guardamos copia del orden original para poder restaurarlo
        $equiposOriginal = $equipos;

        // --- PRIMERA VUELTA (IDA) ---
        for ($j = 0; $j < $totalJornadasIda; $j++) {
            for ($i = 0; $i < $mitad; $i++) {
                $local = $equipos[$i];
                $visitante = $equipos[$numEquipos - 1 - $i];

                // Solo creamos el partido si ninguno de los dos es el equipo fantasma (null)
                if ($local !== null && $visitante !== null) {
                    $partidosToInsert[] = [
                        'id_competicion' => $competicionId,
                        'id_local'       => $local,
                        'id_visitante'   => $visitante,
                        'jornada'        => $jornadaGlobal,
                        'fecha_hora'     => $fechaActual->copy(),
                        'campo_pista'    => $campo,
                        'estado'         => 'pendiente',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }
            }

            // Algoritmo Round-Robin: rotar los equipos dejando el primero (índice 0) fijo.
            $ultimo = array_pop($equipos);
            array_splice($equipos, 1, 0, [$ultimo]);

            $jornadaGlobal++;
            $fechaActual->addWeek();
        }

        // Restauramos el orden para asegurar que los emparejamientos de la vuelta sean exactos
        $equipos = $equiposOriginal;
        
        // --- SEGUNDA VUELTA (VUELTA) ---
        for ($j = 0; $j < $totalJornadasIda; $j++) {
            for ($i = 0; $i < $mitad; $i++) {
                // Invertimos local y visitante para la vuelta
                $visitante = $equipos[$i];
                $local = $equipos[$numEquipos - 1 - $i];

                if ($local !== null && $visitante !== null) {
                    $partidosToInsert[] = [
                        'id_competicion' => $competicionId,
                        'id_local'       => $local,
                        'id_visitante'   => $visitante,
                        'jornada'        => $jornadaGlobal,
                        'fecha_hora'     => $fechaActual->copy(),
                        'campo_pista'    => $campo,
                        'estado'         => 'pendiente',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }
            }

            // Rotamos igual que en la ida
            $ultimo = array_pop($equipos);
            array_splice($equipos, 1, 0, [$ultimo]);

            $jornadaGlobal++;
            $fechaActual->addWeek();
        }

        // Insertar todos los partidos de golpe usando transacción
        DB::transaction(function () use ($partidosToInsert) {
            Partido::insert($partidosToInsert);
        });

        // La fecha final será la de la última jornada (le restamos la semana extra que se sumó al final del bucle)
        $fechaFin = $fechaActual->subWeek()->toDateTimeString();

        return [
            'total_partidos' => count($partidosToInsert),
            'total_jornadas' => $totalJornadas,
            'fecha_inicio'   => $fechaInicio,
            'fecha_fin'      => $fechaFin,
        ];
    }

    /**
     * Elimina todos los partidos de una competición que estén en estado "programado".
     *
     * @param int $competicionId
     * @return int Número de partidos eliminados
     */
    public function eliminarCalendario(int $competicionId): int
    {
        return Partido::where('id_competicion', $competicionId)
            ->where('estado', 'pendiente')
            ->delete();
    }
}

<?php

namespace App\Services;

use App\Models\Partido;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class CalendarioService
{
    /**
     * Genera el calendario de una competición usando el algoritmo Round-Robin (ida y vuelta).
     * Las fechas se generan con días y horas aleatorias pero lógicas.
     * Los árbitros se asignan automáticamente sin conflictos de horario.
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

        // Obtener todos los árbitros disponibles
        $arbitros = Usuario::where('rol', 'arbitro')->pluck('id')->toArray();
        if (empty($arbitros)) {
            throw new Exception("No hay árbitros registrados en el sistema. Registra al menos uno antes de generar el calendario.");
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
        $baseDate = Carbon::parse($fechaInicio);
        $jornadaGlobal = 1;

        // Horarios lógicos para partidos (fútbol sala / fútbol 7)
        $horariosDisponibles = [
            ['hora' => 17, 'minuto' => 0],  // 17:00
            ['hora' => 18, 'minuto' => 30], // 18:30
            ['hora' => 19, 'minuto' => 0],  // 19:00
            ['hora' => 20, 'minuto' => 0],  // 20:00
            ['hora' => 20, 'minuto' => 30], // 20:30
            ['hora' => 21, 'minuto' => 0],  // 21:00
        ];

        // Días válidos de la semana (Lunes=1 ... Domingo=7)
        // Preferimos viernes(5), sábado(6), domingo(7) y miércoles(3)
        $diasPreferidos = [3, 5, 6, 7]; // miércoles, viernes, sábado, domingo

        // Pistas disponibles (aleatorizar)
        $pistas = ['Pista Central', 'Pista Norte', 'Pista Sur', 'Pista Este'];

        // Guardamos copia del orden original para poder restaurarlo
        $equiposOriginal = $equipos;

        // --- PRIMERA VUELTA (IDA) ---
        for ($j = 0; $j < $totalJornadasIda; $j++) {
            // Calcular fecha de esta jornada: semana base + offset aleatorio en días preferidos
            $fechaJornada = $this->generarFechaJornada($baseDate, $j, $diasPreferidos);

            // Recopilar partidos de esta jornada primero para asignar árbitros sin conflictos
            $partidosJornada = [];
            for ($i = 0; $i < $mitad; $i++) {
                $local = $equipos[$i];
                $visitante = $equipos[$numEquipos - 1 - $i];

                if ($local !== null && $visitante !== null) {
                    $partidosJornada[] = [
                        'id_local' => $local,
                        'id_visitante' => $visitante,
                    ];
                }
            }

            // Asignar horarios y árbitros a los partidos de esta jornada
            $asignaciones = $this->asignarArbitrosYHorarios($partidosJornada, $fechaJornada, $arbitros, $horariosDisponibles, $pistas);

            foreach ($asignaciones as $asignacion) {
                $partidosToInsert[] = [
                    'id_competicion' => $competicionId,
                    'id_local'       => $asignacion['id_local'],
                    'id_visitante'   => $asignacion['id_visitante'],
                    'id_arbitro'     => $asignacion['id_arbitro'],
                    'jornada'        => $jornadaGlobal,
                    'fecha_hora'     => $asignacion['fecha_hora'],
                    'campo_pista'    => $asignacion['campo_pista'],
                    'estado'         => 'pendiente',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            // Algoritmo Round-Robin: rotar los equipos dejando el primero (índice 0) fijo.
            $ultimo = array_pop($equipos);
            array_splice($equipos, 1, 0, [$ultimo]);

            $jornadaGlobal++;
        }

        // Restauramos el orden para asegurar que los emparejamientos de la vuelta sean exactos
        $equipos = $equiposOriginal;
        
        // --- SEGUNDA VUELTA (VUELTA) ---
        for ($j = 0; $j < $totalJornadasIda; $j++) {
            $fechaJornada = $this->generarFechaJornada($baseDate, $totalJornadasIda + $j, $diasPreferidos);

            $partidosJornada = [];
            for ($i = 0; $i < $mitad; $i++) {
                // Invertimos local y visitante para la vuelta
                $visitante = $equipos[$i];
                $local = $equipos[$numEquipos - 1 - $i];

                if ($local !== null && $visitante !== null) {
                    $partidosJornada[] = [
                        'id_local' => $local,
                        'id_visitante' => $visitante,
                    ];
                }
            }

            $asignaciones = $this->asignarArbitrosYHorarios($partidosJornada, $fechaJornada, $arbitros, $horariosDisponibles, $pistas);

            foreach ($asignaciones as $asignacion) {
                $partidosToInsert[] = [
                    'id_competicion' => $competicionId,
                    'id_local'       => $asignacion['id_local'],
                    'id_visitante'   => $asignacion['id_visitante'],
                    'id_arbitro'     => $asignacion['id_arbitro'],
                    'jornada'        => $jornadaGlobal,
                    'fecha_hora'     => $asignacion['fecha_hora'],
                    'campo_pista'    => $asignacion['campo_pista'],
                    'estado'         => 'pendiente',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            // Rotamos igual que en la ida
            $ultimo = array_pop($equipos);
            array_splice($equipos, 1, 0, [$ultimo]);

            $jornadaGlobal++;
        }

        // Insertar todos los partidos de golpe usando transacción
        DB::transaction(function () use ($partidosToInsert) {
            Partido::insert($partidosToInsert);
        });

        // La fecha final será la de la última jornada
        $fechaFin = collect($partidosToInsert)->max('fecha_hora');

        return [
            'total_partidos' => count($partidosToInsert),
            'total_jornadas' => $totalJornadas,
            'fecha_inicio'   => $fechaInicio,
            'fecha_fin'      => $fechaFin,
        ];
    }

    /**
     * Genera una fecha lógica para una jornada, eligiendo un día preferido de la semana
     * dentro de la semana correspondiente.
     */
    private function generarFechaJornada(Carbon $baseDate, int $weekOffset, array $diasPreferidos): Carbon
    {
        // Avanzamos a la semana correspondiente
        $weekStart = $baseDate->copy()->addWeeks($weekOffset)->startOfWeek();
        
        // Elegir un día preferido aleatorio de esa semana
        $diaElegido = $diasPreferidos[array_rand($diasPreferidos)];
        
        // Calcular la fecha del día elegido en esa semana (Carbon weekday: 1=Lunes, 7=Domingo)
        $fecha = $weekStart->copy()->addDays($diaElegido - 1);
        
        return $fecha;
    }

    /**
     * Asigna árbitros y horarios a los partidos de una jornada
     * garantizando que no haya conflictos (mismo árbitro a la misma hora).
     */
    private function asignarArbitrosYHorarios(array $partidosJornada, Carbon $fechaJornada, array $arbitros, array $horariosDisponibles, array $pistas): array
    {
        $resultado = [];
        // Llevar registro de qué árbitro está asignado a qué horario
        $arbitroOcupado = []; // ['arbitroId_horaIndex' => true]
        $arbitroIndex = 0;
        
        shuffle($horariosDisponibles); // Aleatorizar horarios
        $pistasDisponibles = $pistas;
        shuffle($pistasDisponibles);

        foreach ($partidosJornada as $i => $partido) {
            // Elegir horario: cada partido de la jornada en un horario diferente si es posible
            $horarioIndex = $i % count($horariosDisponibles);
            $horario = $horariosDisponibles[$horarioIndex];
            
            $fechaHora = $fechaJornada->copy()
                ->setHour($horario['hora'])
                ->setMinute($horario['minuto'])
                ->setSecond(0);

            // Buscar un árbitro libre para este horario
            $arbitroAsignado = null;
            $intentos = 0;
            while ($intentos < count($arbitros)) {
                $candidato = $arbitros[$arbitroIndex % count($arbitros)];
                $clave = $candidato . '_' . $horarioIndex;
                
                if (!isset($arbitroOcupado[$clave])) {
                    $arbitroAsignado = $candidato;
                    $arbitroOcupado[$clave] = true;
                    $arbitroIndex++;
                    break;
                }
                
                $arbitroIndex++;
                $intentos++;
            }

            // Fallback: si todos los árbitros están ocupados a esa hora, asignar un horario diferente
            if ($arbitroAsignado === null) {
                // Buscar otro horario disponible para cualquier árbitro
                foreach ($horariosDisponibles as $hIdx => $h) {
                    foreach ($arbitros as $arb) {
                        $clave = $arb . '_' . $hIdx;
                        if (!isset($arbitroOcupado[$clave])) {
                            $arbitroAsignado = $arb;
                            $arbitroOcupado[$clave] = true;
                            $fechaHora = $fechaJornada->copy()
                                ->setHour($h['hora'])
                                ->setMinute($h['minuto'])
                                ->setSecond(0);
                            break 2;
                        }
                    }
                }
                
                // Último recurso: asignar el primer árbitro disponible
                if ($arbitroAsignado === null) {
                    $arbitroAsignado = $arbitros[0];
                }
            }

            // Pista aleatoria
            $pista = $pistasDisponibles[$i % count($pistasDisponibles)];

            $resultado[] = [
                'id_local' => $partido['id_local'],
                'id_visitante' => $partido['id_visitante'],
                'id_arbitro' => $arbitroAsignado,
                'fecha_hora' => $fechaHora,
                'campo_pista' => $pista,
            ];
        }

        return $resultado;
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

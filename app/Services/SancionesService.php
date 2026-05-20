<?php

namespace App\Services;

use App\Models\Partido;
use App\Models\PlantillaJugador;
use App\Models\Sancion;
use App\Models\Equipo;
use App\Models\Usuario;
use App\Models\EventoPartido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SancionesService
{
    /**
     * Evalúa las alineaciones de un partido finalizado.
     */
    public function evaluarAlineacionesIndebidas(int $partidoId): array
    {
        $partido = Partido::with(['eventoPartido', 'equipoLocal', 'equipoVisitante'])->findOrFail($partidoId);

        if ($partido->estado !== 'jugado' && $partido->estado !== 'finalizado') {
            throw new Exception("El partido debe estar jugado para evaluar sanciones.");
        }

        // Obtener IDs de jugadores únicos que participaron en el partido (fueron convocados)
        $jugadoresParticipantes = \App\Models\Convocatoria::where('id_partido', $partidoId)
            ->pluck('id_usuario')
            ->unique();

        $infraccionesPorEquipo = [
            $partido->id_local => false,
            $partido->id_visitante => false,
        ];

        $sancionesAplicadas = 0;
        $infractores = [];

        DB::beginTransaction();

        try {
            foreach ($jugadoresParticipantes as $idJugador) {
                // Verificar si está inscrito en alguno de los dos equipos
                $equipoInscrito = PlantillaJugador::where('id_usuario', $idJugador)
                    ->whereIn('id_equipo', [$partido->id_local, $partido->id_visitante])
                    ->value('id_equipo');

                $esNoInscrito = is_null($equipoInscrito);

                // Verificar si tenía sanción activa al momento de jugar
                $tieneSancionActiva = Sancion::where('id_usuario', $idJugador)
                    ->where('estado', 'activa')
                    ->where('id_partido_origen', '!=', $partidoId)
                    ->exists();

                if (!$esNoInscrito && !$tieneSancionActiva) {
                    continue; // El jugador está en regla
                }

                if ($esNoInscrito) {
                    $motivo = 'Alineación Indebida: jugador no inscrito en ningún equipo';
                    $equipoInfractor = null; // No se puede saber con certeza el equipo exacto si no está en plantillas
                } else {
                    $motivo = 'Alineación Indebida: jugador con sanción activa';
                    $equipoInfractor = $equipoInscrito;
                }

                // Crear sanción
                Sancion::create([
                    'id_usuario' => $idJugador,
                    'id_partido_origen' => $partidoId,
                    'partidos_suspension' => 1,
                    'motivo' => $motivo,
                    'estado' => 'activa',
                ]);

                $sancionesAplicadas++;
                $infractores[] = [
                    'id_jugador' => $idJugador,
                    'equipo_infractor' => $equipoInfractor,
                    'motivo' => $motivo,
                ];

                if ($equipoInfractor) {
                    $infraccionesPorEquipo[$equipoInfractor] = true;
                }
            }

            // Aplicar castigos a los equipos si hubo infracciones
            $huboCastigoLocal = $infraccionesPorEquipo[$partido->id_local];
            $huboCastigoVisitante = $infraccionesPorEquipo[$partido->id_visitante];

            if ($huboCastigoLocal || $huboCastigoVisitante) {
                if ($huboCastigoLocal && !$huboCastigoVisitante) {
                    // Local pierde 3-0
                    $partido->goles_local = 0;
                    $partido->goles_visitante = 3;
                    Equipo::where('id', $partido->id_local)->increment('puntos_sancion', 1);
                } elseif ($huboCastigoVisitante && !$huboCastigoLocal) {
                    // Visitante pierde 3-0
                    $partido->goles_local = 3;
                    $partido->goles_visitante = 0;
                    Equipo::where('id', $partido->id_visitante)->increment('puntos_sancion', 1);
                } else {
                    // Ambos infractores
                    $partido->goles_local = 0;
                    $partido->goles_visitante = 0;
                    Equipo::where('id', $partido->id_local)->increment('puntos_sancion', 1);
                    Equipo::where('id', $partido->id_visitante)->increment('puntos_sancion', 1);
                }
                $partido->save();
            }

            if ($sancionesAplicadas > 0) {
                $this->notificarAdmin($partido, $infractores);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => $sancionesAplicadas > 0 ? "Infracciones detectadas. Resultado revertido y sanciones aplicadas." : "Auditoría completada sin infracciones.",
                'sanciones_aplicadas' => $sancionesAplicadas,
                'infractores' => $infractores,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error evaluando alineaciones para el partido {$partidoId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Aplica sanción automática por tarjeta roja (1 partido de suspensión).
     */
    public function aplicarSancionTarjetaRoja(int $idJugador, int $idPartido): Sancion
    {
        return Sancion::firstOrCreate(
            [
                'id_usuario' => $idJugador,
                'id_partido_origen' => $idPartido,
                'motivo' => 'Tarjeta roja',
            ],
            [
                'partidos_suspension' => 1,
                'estado' => 'activa',
            ]
        );
    }

    /**
     * Avanza el contador de partidos cumplidos de todas las sanciones activas
     * de los jugadores que participaron en un partido.
     */
    public function avanzarSancionesCumplidas(int $partidoId): void
    {
        $partido = Partido::findOrFail($partidoId);
        
        // Obtener todos los jugadores de los dos equipos
        $jugadoresEquipos = PlantillaJugador::whereIn('id_equipo', [$partido->id_local, $partido->id_visitante])
            ->pluck('id_usuario')
            ->unique();
            
        // Obtener los que jugaron (convocados)
        $jugadoresConvocados = \App\Models\Convocatoria::where('id_partido', $partidoId)
            ->pluck('id_usuario')
            ->toArray();
            
        // Los que cumplieron sanción son los que pertenecen a los equipos pero NO fueron convocados
        $jugadoresCumpliendo = $jugadoresEquipos->diff($jugadoresConvocados);

        foreach ($jugadoresCumpliendo as $idJugador) {
            $sanciones = Sancion::where('id_usuario', $idJugador)
                ->where('estado', 'activa')
                ->where(function ($q) use ($partidoId) {
                    $q->where('id_partido_origen', '!=', $partidoId)
                      ->orWhereNull('id_partido_origen');
                })
                ->get();

            foreach ($sanciones as $sancion) {
                $sancion->increment('partidos_cumplidos');
                $sancion->refresh();

                if ($sancion->partidos_cumplidos >= $sancion->partidos_suspension) {
                    $sancion->update(['estado' => 'cumplida']);
                }
            }
        }
    }

    /**
     * Notifica al admin con una alerta crítica.
     */
    private function notificarAdmin(Partido $partido, array $infractores): void
    {
        $admin = Usuario::where('rol', 'admin')->first();

        if ($admin) {
            $detalle = collect($infractores)
                ->map(fn($infractor) => "Jugador #{$infractor['id_jugador']}: {$infractor['motivo']}")
                ->join(' | ');

            Log::warning("⚠️ ALERTA CRÍTICA — Partido #{$partido->id}: {$detalle}");

            // TODO: Conectar sistema de notificaciones real
            // $admin->notify(new \App\Notifications\AlineacionIndebidaNotification($partido, $infractores));
        }
    }
}
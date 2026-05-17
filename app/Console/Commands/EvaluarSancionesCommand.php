<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Partido;
use App\Models\Sancion;
use App\Models\PlantillaJugador;
use App\Services\SancionesService;

class EvaluarSancionesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competicion:evaluar-sanciones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta el robot de sanciones para evaluar infracciones en los últimos partidos y avanzar los contadores de las sanciones activas.';

    /**
     * Execute the console command.
     */
    public function handle(SancionesService $sancionesService)
    {
        $this->info('🤖 Iniciando el Robot de Auditoría Disciplinaria...');

        // 1. Evaluar Alineaciones Indebidas (Por si algún partido se cerró sin pasar por la validación estándar o se forzó por BD)
        $partidosJugados = Partido::whereIn('estado', ['jugado', 'finalizado'])->get();
        $infraccionesTotales = 0;

        foreach ($partidosJugados as $partido) {
            try {
                $resultado = $sancionesService->evaluarAlineacionesIndebidas($partido->id);
                if (isset($resultado['sanciones_aplicadas']) && $resultado['sanciones_aplicadas'] > 0) {
                    $infraccionesTotales += $resultado['sanciones_aplicadas'];
                    $this->warn("⚠️  Infracciones detectadas en Partido #{$partido->id}: Se revirtió el resultado.");
                }
            } catch (\Exception $e) {
                // Si ya se evaluó u ocurre un error, ignoramos y seguimos
            }
        }

        // 2. Avanzar contadores de sanciones de los jugadores que NO jugaron (porque están cumpliendo su castigo)
        $sancionesActivas = Sancion::where('estado', 'activa')->get();
        $sancionesCumplidas = 0;

        foreach ($sancionesActivas as $sancion) {
            // Buscamos a qué equipo pertenece el jugador sancionado
            $equipo = PlantillaJugador::where('id_usuario', $sancion->id_usuario)
                ->where('estado', 'activo')
                ->first();

            if ($equipo) {
                // Verificamos si su equipo jugó un partido recientemente (en los últimos 7 días)
                // Esto es una simplificación asumiendo que el comando corre semanalmente.
                $equipoJugo = Partido::whereIn('estado', ['jugado', 'finalizado'])
                    ->where(function ($q) use ($equipo) {
                        $q->where('id_local', $equipo->id_equipo)
                          ->orWhere('id_visitante', $equipo->id_equipo);
                    })
                    ->where('fecha_hora', '>=', now()->subDays(7))
                    ->exists();

                if ($equipoJugo) {
                    $sancion->increment('partidos_cumplidos');
                    if ($sancion->partidos_cumplidos >= $sancion->partidos_suspension) {
                        $sancion->update(['estado' => 'cumplida']);
                        $sancionesCumplidas++;
                    }
                }
            }
        }

        $this->info("✅ Auditoría completada.");
        $this->line("- Nuevas sanciones aplicadas (Alineación Indebida): <fg=red>{$infraccionesTotales}</>");
        $this->line("- Sanciones que acaban de ser cumplidas totalmente: <fg=green>{$sancionesCumplidas}</>");
        
        return 0;
    }
}

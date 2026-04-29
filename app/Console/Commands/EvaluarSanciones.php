<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Partido;
use App\Services\SancionesService;

class EvaluarSanciones extends Command
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
    protected $description = 'Evalúa todos los partidos finalizados para detectar alineaciones indebidas y aplicar sanciones automáticamente.';

    /**
     * Execute the console command.
     */
    public function handle(SancionesService $sancionesService)
    {
        $this->info('Iniciando evaluación de sanciones automáticas...');

        // Obtener partidos finalizados que tengan eventos de jugadores
        $partidos = Partido::where('estado', 'finalizado')
                           ->whereHas('eventos')
                           ->get();

        if ($partidos->isEmpty()) {
            $this->info('No hay partidos finalizados pendientes de auditar.');
            return;
        }

        $totalSanciones = 0;

        foreach ($partidos as $partido) {
            try {
                $resultado = $sancionesService->evaluarAlineacionesIndebidas($partido->id);
                $totalSanciones += $resultado['sanciones_aplicadas'];
                $this->line("Partido #{$partido->id} auditado. Sanciones: " . $resultado['sanciones_aplicadas']);
            } catch (\Exception $e) {
                $this->error("Error al auditar partido #{$partido->id}: " . $e->getMessage());
            }
        }

        $this->info("Auditoría completada. Total de nuevas sanciones aplicadas: {$totalSanciones}");
    }
}

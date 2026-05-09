<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\EventoPartido;
use App\Models\Sancion;
use Illuminate\Http\Request;

class PartidoController extends Controller
{
    /**
     * Valida el acta del partido y aplica sanción automática si hay alineación indebida (CU-22)
     */
    public function validarActa(Request $request, $id_partido)
    {
        // 1. Recuperamos los IDs de los jugadores que participaron según el acta
        $alineados = EventoPartido::where('id_partido', $id_partido)->pluck('id_jugador');

        // 2. Comprobamos si alguno de esos jugadores tiene una sanción 'activa'
        $infractor = Sancion::whereIn('id_usuario', $alineados)
                            ->where('estado', 'activa')
                            ->exists();

        $partido = Partido::findOrFail($id_partido);

        if ($infractor) {
            // 3. Aplicación del CU-22: El equipo infractor pierde 0-3 automáticamente
            $partido->update([
                'goles_local' => 0,
                'goles_visitante' => 3,
                'estado' => 'jugado',
                'observaciones' => 'ALERTA CRÍTICA: Derrota administrativa por alineación indebida.'
            ]);
            
            return redirect()->back()->with('error', 'Sanción aplicada: Alineación indebida detectada.');
        } else {
            // Guardado normal si no hay infracción
            $partido->update([
                'goles_local' => $request->goles_local,
                'goles_visitante' => $request->goles_visitante,
                'estado' => 'jugado'
            ]);
            
            return redirect()->back()->with('success', 'Acta validada correctamente.');
        }
    }
}
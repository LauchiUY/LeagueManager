<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Usuario;
use App\Models\PlantillaJugador;
use App\Models\Convocatoria;
use App\Models\Aplazamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapitanController extends Controller
{
    /**
     * Muestra el panel principal del capitán con los datos de su equipo.
     */
    public function miEquipo()
    {
        $user = Auth::user();

        $equipo = Equipo::with(['plantilla.usuario.sanciones' => function($q) {
            $q->where('estado', 'activa');
        }])->whereHas('plantilla', function($q) use ($user) {
            $q->where('id_usuario', $user->id)->where('es_capitan', true);
        })->first();

        if (!$equipo) {
            return redirect('/')->with('error', 'No eres capitán de ningún equipo.');
        }

        $partidos = Partido::with(['equipoLocal', 'equipoVisitante', 'competicion'])
            ->where(function ($q) use ($equipo) {
                $q->where('id_local', $equipo->id)
                  ->orWhere('id_visitante', $equipo->id);
            })
            ->whereIn('estado', ['pendiente', 'en_curso'])
            ->orderBy('fecha_hora', 'asc')
            ->paginate(5);

        // Cargar TODAS las sanciones de los jugadores de este equipo (activas + historial)
        $jugadoresIds = $equipo->plantilla->pluck('id_usuario');
        $sancionesEquipo = \App\Models\Sancion::with(['usuario', 'partidoOrigen'])
            ->whereIn('id_usuario', $jugadoresIds)
            ->orderBy('estado', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('capitan.mi-equipo', compact('equipo', 'partidos', 'sancionesEquipo'));
    }

    /**
     * Añade un jugador a la plantilla del equipo mediante su email.
     */
    public function addJugador(Request $request)
    {
       $request->validate([
            'email' => 'required|email|exists:usuarios,email',
            'dorsal' => 'required|integer|min:1|max:99'
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes introducir un formato de correo válido.',
            'email.exists' => 'No hemos encontrado ningún jugador con ese correo en el sistema.',
            'dorsal.required' => 'El dorsal es obligatorio.',
            'dorsal.integer' => 'El dorsal debe ser un número.',
        ]);

        $equipo = Equipo::whereHas('plantilla', fn($q) => $q->where('id_usuario', Auth::id())->where('es_capitan', true))->firstOrFail();


        $jugador = Usuario::where('email', $request->email)->first();

        if ($jugador->rol !== 'jugador') {
            return back()->with('error', 'El usuario especificado no tiene el rol de jugador.');
        }
        // Verificar si el jugador ya está en otra plantilla activa de otro equipo
        $enOtraPlantilla = PlantillaJugador::where('id_usuario', $jugador->id)
            ->where('estado', 'activo')
            ->where('id_equipo', '!=', $equipo->id)
            ->exists();

        if ($enOtraPlantilla) {
            return back()->with('error', 'Este jugador ya está activo en otro equipo.');
        }

        PlantillaJugador::updateOrCreate(
            ['id_equipo' => $equipo->id, 'id_usuario' => $jugador->id],
            ['dorsal' => $request->dorsal, 'estado' => 'activo']
        );

        return back()->with('success', 'Jugador añadido a la plantilla correctamente.');
    }

    /**
     * Da de baja a un jugador (lo marca como inactivo).
     */
    public function removeJugador($idJugador)
    {
        $equipo = Equipo::whereHas('plantilla', fn($q) => $q->where('id_usuario', Auth::id())->where('es_capitan', true))->firstOrFail();

        // Evitar que el capitán se borre a sí mismo de la plantilla
        if ((int) $idJugador === Auth::id()) {
            return back()->with('error', 'No puedes darte de baja a ti mismo. Eres el capitán del equipo.');
        }

        PlantillaJugador::where('id_equipo', $equipo->id)
            ->where('id_usuario', $idJugador)
            ->update(['estado' => 'inactivo']);

        return back()->with('success', 'Jugador dado de baja del equipo.');
    }

    /**
     * Muestra el formulario para gestionar la convocatoria de un partido.
     */
    public function convocatoria($partidoId)
    {
        $equipo = Equipo::whereHas('plantilla', fn($q) => $q->where('id_usuario', Auth::id())->where('es_capitan', true))->firstOrFail();
        $partido = Partido::findOrFail($partidoId);

        if ($partido->id_local !== $equipo->id && $partido->id_visitante !== $equipo->id) {
            abort(403, 'Tu equipo no participa en este partido.');
        }

        $plantilla = PlantillaJugador::with(['usuario.sanciones' => function($q) {
            $q->where('estado', 'activa');
        }])->where('id_equipo', $equipo->id)
          ->where('estado', 'activo')
          ->get();

        $convocados = Convocatoria::where('id_partido', $partidoId)
            ->where('id_equipo', $equipo->id)
            ->pluck('id_usuario')
            ->toArray();

        return view('capitan.convocatoria', compact('equipo', 'partido', 'plantilla', 'convocados'));
    }

    /**
     * Guarda la selección de jugadores convocados.
     */
    public function guardarConvocatoria(Request $request, $partidoId)
    {
        $equipo = Equipo::whereHas('plantilla', fn($q) => $q->where('id_usuario', Auth::id())->where('es_capitan', true))->firstOrFail();
        $partido = Partido::findOrFail($partidoId);

        // Limpiar convocatorias anteriores para este partido/equipo
        Convocatoria::where('id_partido', $partidoId)
            ->where('id_equipo', $equipo->id)
            ->delete();

        if ($request->has('jugadores') && is_array($request->jugadores)) {
            // Nota: Se permite guardar jugadores sancionados si vulneran el frontend (F12)
            // para que el SancionesService actúe en la validación del acta (Alineación indebida automática).
            $nuevasConvocatorias = [];
            foreach ($request->jugadores as $jugadorId) {
                $nuevasConvocatorias[] = [
                    'id_partido' => $partidoId,
                    'id_equipo' => $equipo->id,
                    'id_usuario' => $jugadorId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            Convocatoria::insert($nuevasConvocatorias);
        }

        return redirect()->route('capitan.equipo')->with('success', 'Convocatoria guardada correctamente.');
    }

    /**
     * Solicita un aplazamiento para un partido programado.
     */
    public function solicitarAplazamiento(Request $request, $partidoId)
    {
        $request->validate(['motivo' => 'required|string|min:10|max:500']);
        $equipo = Equipo::whereHas('plantilla', fn($q) => $q->where('id_usuario', Auth::id())->where('es_capitan', true))->firstOrFail();
        $partido = Partido::findOrFail($partidoId);

        if ($partido->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes aplazar partidos pendientes.');
        }

        // Crear o actualizar solicitud pendiente
        Aplazamiento::updateOrCreate(
            ['id_partido' => $partidoId, 'id_solicitante' => Auth::id()],
            [
                'motivo' => $request->motivo,
                'estado' => 'pendiente',
                'fecha_limite' => now()->addHours(48)
            ]
        );

        return back()->with('success', 'Solicitud de aplazamiento enviada a la administración.');
    }
}

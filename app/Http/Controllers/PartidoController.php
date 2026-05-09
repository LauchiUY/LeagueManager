<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\EventoPartido;
use App\Models\PlantillaJugador;
use App\Services\SancionesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PartidoController extends Controller
{
    /**
     * Lista los partidos asignados al árbitro actual.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Si es admin puede ver todos (por si quiere auditar), si es árbitro solo los suyos
        $query = Partido::with(['equipoLocal', 'equipoVisitante', 'competicion'])
            ->orderBy('fecha_hora', 'asc');
            
        if ($user->rol === 'arbitro') {
            $query->where('id_arbitro', $user->id);
        }

        $partidos = $query->paginate(15);
        
        return view('partidos.index', compact('partidos'));
    }

    /**
     * Muestra el acta digital de un partido (solo accesible para su árbitro o un admin).
     */
    public function show($id)
    {
        $partido = Partido::with([
            'equipoLocal', 
            'equipoVisitante', 
            'competicion',
            'eventoPartido.jugador'
        ])->findOrFail($id);

        $user = Auth::user();

        // Validar permisos
        if ($user->rol === 'arbitro' && $partido->id_arbitro !== $user->id) {
            abort(403, 'No estás autorizado para gestionar este partido.');
        }

        // Obtener jugadores de ambas plantillas
        $jugadoresLocal = PlantillaJugador::with('usuario')
            ->where('id_equipo', $partido->id_local)
            ->where('estado', 'activo')
            ->get();
            
        $jugadoresVisitante = PlantillaJugador::with('usuario')
            ->where('id_equipo', $partido->id_visitante)
            ->where('estado', 'activo')
            ->get();

        return view('partidos.show', compact('partido', 'jugadoresLocal', 'jugadoresVisitante'));
    }

    /**
     * Registra un evento (Gol, Amarilla, Roja) en el acta.
     */
    public function registrarEvento(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);

        // Validar estado del partido
        if ($partido->estado === 'jugado') {
            return back()->with('error', 'No se pueden añadir eventos a un partido que ya está jugado.');
        }
        
        // Si estaba pendiente, al añadir el primer evento pasa a "en_curso"
        if ($partido->estado === 'pendiente') {
            $partido->update(['estado' => 'en_curso']);
        }

        $request->validate([
            'id_jugador' => 'required|exists:usuarios,id',
            'tipo_evento' => 'required|in:Gol,Amarilla,Roja',
            'minuto' => 'required|integer|min:1|max:120',
            'observaciones' => 'nullable|string|max:255'
        ]);

        EventoPartido::create([
            'id_partido' => $partido->id,
            'id_jugador' => $request->id_jugador,
            'tipo_evento' => $request->tipo_evento,
            'minuto' => $request->minuto,
            'observaciones' => $request->observaciones
        ]);

        // Si es roja, aplicar sanción automáticamente
        if ($request->tipo_evento === 'Roja') {
            $sancionesService = new SancionesService();
            $sancionesService->aplicarSancionTarjetaRoja($request->id_jugador, $partido->id);
        }

        return back()->with('success', 'Evento registrado correctamente.');
    }

    /**
     * Valida el acta del partido, evaluando el resultado final y activando el algoritmo de sanciones (CU-22).
     */
    public function validarActa(Request $request, $id, SancionesService $sancionesService)
    {
        $partido = Partido::with('eventoPartido')->findOrFail($id);

        if ($partido->estado === 'jugado') {
            return back()->with('error', 'El acta de este partido ya ha sido validada.');
        }

        $request->validate([
            'goles_local' => 'required|integer|min:0',
            'goles_visitante' => 'required|integer|min:0'
        ]);

        // Guardar resultado y marcar como jugado
        $partido->update([
            'goles_local' => $request->goles_local,
            'goles_visitante' => $request->goles_visitante,
            'estado' => 'jugado'
        ]);

        try {
            // Evaluar sanciones y acumulación de partidos
            $sancionesService->evaluarAlineacionesIndebidas($partido->id);
            $sancionesService->avanzarSancionesCumplidas($partido->id);
            
            return redirect()->route('partidos.index')
                ->with('success', 'Acta validada con éxito. Resultado guardado y sanciones evaluadas.');
                
        } catch (\Exception $e) {
            return redirect()->route('partidos.index')
                ->with('error', 'Acta validada, pero hubo un error evaluando sanciones: ' . $e->getMessage());
        }
    }

    /**
     * Permite subir una fotografía del acta física al servidor.
     */
    public function subirFotoActa(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);

        $request->validate([
            'foto_acta' => 'required|image|mimes:jpeg,png,jpg|max:5120' // 5MB max
        ]);

        if ($request->hasFile('foto_acta')) {
            // Eliminar acta anterior si existe
            if ($partido->url_foto_acta && Storage::disk('public')->exists($partido->url_foto_acta)) {
                Storage::disk('public')->delete($partido->url_foto_acta);
            }

            $path = $request->file('foto_acta')->store('actas', 'public');
            $partido->update(['url_foto_acta' => $path]);

            return back()->with('success', 'Foto del acta subida correctamente.');
        }

        return back()->with('error', 'No se ha podido subir la imagen.');
    }
}

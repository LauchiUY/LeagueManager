<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\PlantillaJugador;
use App\Models\Equipo;

class CapitanController extends Controller
{
    /**
     * Muestra el panel de gestión del equipo del capitán logueado.
     */
    public function index()
    {
        $user = Auth::user();
        
        // El equipo del que este usuario es capitán
        $equipo = Equipo::whereHas('plantilla', function($q) use ($user) {
            $q->where('id_usuario', $user->id)->where('es_capitan', true);
        })->first();
        
        if (!$equipo) {
            return redirect()->route('perfil.index')->with('error', 'No tienes ningún equipo asignado como capitán.');
        }

        // Cargar los jugadores de la plantilla
        $equipo->load('plantilla.usuario');

        return view('capitan.index', compact('equipo'));
    }

    /**
     * Añade un jugador al equipo por su email.
     */
    public function ficharJugador(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = Auth::user();
        $equipo = Equipo::whereHas('plantilla', function($q) use ($user) {
            $q->where('id_usuario', $user->id)->where('es_capitan', true);
        })->first();

        if (!$equipo) {
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        // Buscar al jugador
        $jugador = Usuario::where('email', $request->email)->first();

        if (!$jugador) {
            return redirect()->back()->with('error', 'No existe ningún usuario con ese correo.');
        }

        if ($jugador->rol !== 'jugador') {
            return redirect()->back()->with('error', 'El usuario no tiene el rol de Jugador (es un ' . $jugador->rol . ').');
        }

        // Verificar si ya está en ESTE equipo
        $yaEnEquipo = PlantillaJugador::where('id_equipo', $equipo->id)
            ->where('id_usuario', $jugador->id)
            ->first();

        if ($yaEnEquipo) {
            return redirect()->back()->with('error', 'Este jugador ya está en tu plantilla.');
        }

        // Verificar si ya está en OTRO equipo
        $yaEnOtroEquipo = PlantillaJugador::where('id_usuario', $jugador->id)->first();
        if ($yaEnOtroEquipo) {
            return redirect()->back()->with('error', 'Este jugador ya está fichado por otro equipo.');
        }

        // Inscribir jugador
        PlantillaJugador::create([
            'id_equipo' => $equipo->id,
            'id_usuario' => $jugador->id,
            'dorsal' => rand(2, 99), // Asignamos un dorsal aleatorio por ahora
            'estado' => 'activo'
        ]);

        return redirect()->back()->with('success', '¡Jugador fichado con éxito!');
    }

    /**
     * Expulsa a un jugador de la plantilla.
     */
    public function expulsarJugador($id)
    {
        $user = Auth::user();
        $equipo = Equipo::whereHas('plantilla', function($q) use ($user) {
            $q->where('id_usuario', $user->id)->where('es_capitan', true);
        })->first();

        if (!$equipo) {
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        $registroPlantilla = PlantillaJugador::where('id', $id)
            ->where('id_equipo', $equipo->id)
            ->first();

        if (!$registroPlantilla) {
            return redirect()->back()->with('error', 'Jugador no encontrado en tu plantilla.');
        }

        // No puedes expulsarte a ti mismo si eres el capitán
        if ($registroPlantilla->id_usuario == $user->id) {
            return redirect()->back()->with('error', 'No puedes expulsarte a ti mismo (eres el Capitán).');
        }

        $registroPlantilla->delete();

        return redirect()->back()->with('success', 'Jugador expulsado del equipo.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index()
    {
        $equipos = Equipo::with('plantilla.usuario')->get();
        return view('equipos.index', compact('equipos'));
    }

    public function create()
    {
        return view('equipos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_capitan' => 'required|integer', // Obligamos a que manden un ID de capitán
        ]);

        $equipo = Equipo::create([
            'nombre' => $request->nombre,
            'logo_url' => 'default.png',
            'puntos_sancion' => 0
        ]);

        if ($request->filled('id_capitan')) {
            \App\Models\PlantillaJugador::create([
                'id_equipo' => $equipo->id,
                'id_usuario' => $request->id_capitan,
                'estado' => 'activo',
                'es_capitan' => true
            ]);
        }

        return redirect()->route('equipos.index');
    }

    public function show(Equipo $equipo)
    {
        // Cargamos la relación de la plantilla (y los datos del usuario asociado)
        $equipo->load('plantilla.usuario');
        return view('equipos.show', compact('equipo'));
    }
}
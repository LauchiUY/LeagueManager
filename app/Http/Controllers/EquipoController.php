<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Usuario;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index()
    {
        // Cargamos los equipos con la cantidad de jugadores en su plantilla
        $equipos = Equipo::with('capitan')->withCount('plantilla')->get();
        return view('equipos.index', compact('equipos'));
    }

    public function create()
    {
        $capitanes = Usuario::where('rol', 'capitan')->get();
        return view('equipos.create', compact('capitanes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_capitan' => 'required|exists:usuarios,id',
        ]);

        Equipo::create([
            'nombre' => $request->nombre,
            'logo_url' => 'default.png',
            'id_capitan' => $request->id_capitan,
            'puntos_sancion' => 0
        ]);

        return redirect()->route('equipos.index')->with('success', 'Equipo creado correctamente.');
    }

    public function show($id)
    {
        $equipo = Equipo::with(['capitan', 'plantilla.usuario'])->findOrFail($id);
        
        $partidos = \App\Models\Partido::with(['equipoLocal', 'equipoVisitante', 'competicion'])
            ->where(function ($q) use ($equipo) {
                $q->where('id_local', $equipo->id)
                  ->orWhere('id_visitante', $equipo->id);
            })
            ->orderBy('fecha_hora', 'desc')
            ->take(10)
            ->get();
            
        return view('equipos.show', compact('equipo', 'partidos'));
    }

    public function edit($id)
    {
        $equipo = Equipo::findOrFail($id);
        $capitanes = Usuario::where('rol', 'capitan')->get();
        return view('equipos.edit', compact('equipo', 'capitanes'));
    }

    public function update(Request $request, $id)
    {
        $equipo = Equipo::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_capitan' => 'required|exists:usuarios,id',
            'puntos_sancion' => 'required|integer|min:0'
        ]);

        $equipo->update([
            'nombre' => $request->nombre,
            'id_capitan' => $request->id_capitan,
            'puntos_sancion' => $request->puntos_sancion
        ]);

        return redirect()->route('equipos.show', $equipo->id)->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $equipo = Equipo::findOrFail($id);
        $equipo->delete();
        return redirect()->route('equipos.index')->with('success', 'Equipo eliminado del sistema.');
    }
}
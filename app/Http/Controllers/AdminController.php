<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Competicion;

class AdminController extends Controller
{
    /**
     * Muestra el panel de control del Administrador.
     */
    public function index()
    {
        // Estadísticas rápidas para el dashboard
        $stats = [
            'usuarios' => Usuario::count(),
            'equipos' => Equipo::count(),
            'partidos' => Partido::count(),
            'partidos_pendientes' => Partido::where('estado', 'programado')->count(),
            'competiciones' => Competicion::count(),
        ];

        return view('admin.index', compact('stats'));
    }
}

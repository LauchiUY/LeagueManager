<?php

use App\Models\Usuario;
use App\Models\Equipo;
use App\Models\Competicion;
use App\Models\Partido;
use App\Models\PlantillaJugador;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

// 1. Create user Ayman
$ayman = Usuario::firstOrCreate(
    ['email' => 'ayman@leaguemanager.com'],
    [
        'nombre' => 'Ayman',
        'password' => Hash::make('password'),
        'telefono' => '600000000',
        'rol' => 'jugador'
    ]
);
echo "Ayman creado/encontrado (ID: {$ayman->id})\n";

// 2. Get Carlos
$carlos = Usuario::where('email', 'carlos@leaguemanager.com')->first();
if (!$carlos) {
    echo "Carlos no encontrado\n";
    exit;
}
echo "Carlos encontrado (ID: {$carlos->id})\n";

// 3. Get or Create a Team for Carlos
$carlosPlantilla = PlantillaJugador::where('id_usuario', $carlos->id)->where('es_capitan', true)->first();

if (!$carlosPlantilla) {
    // Create a new team
    $equipo = Equipo::firstOrCreate(['nombre' => 'Los Galácticos de Carlos'], [
        'puntos_sancion' => 0,
        'logo_url' => 'default.png'
    ]);
    
    PlantillaJugador::create([
        'id_equipo' => $equipo->id,
        'id_usuario' => $carlos->id,
        'dorsal' => 10,
        'estado' => 'activo',
        'es_capitan' => true
    ]);
    echo "Equipo creado para Carlos (ID: {$equipo->id})\n";
} else {
    $equipo = Equipo::find($carlosPlantilla->id_equipo);
    echo "Carlos ya tiene equipo (ID: {$equipo->id})\n";
}

// 4. Ensure Team is in a Competition
$competicion = Competicion::firstOrCreate(
    ['nombre' => 'Liga de Presentación'],
    [
        'tipo' => 'liga',
        'estado' => 'activa',
        'deporte' => 'Fútbol',
        'fecha_inicio' => Carbon::now()->subDays(10),
        'fecha_fin' => Carbon::now()->addMonths(2)
    ]
);

$equipo->competiciones()->syncWithoutDetaching([$competicion->id]);
echo "Equipo inscrito en competición '{$competicion->nombre}'\n";

// 5. Create an opponent team
$oponente = Equipo::firstOrCreate(['nombre' => 'Rival de Prueba'], [
    'puntos_sancion' => 0,
    'logo_url' => 'default.png'
]);
$oponente->competiciones()->syncWithoutDetaching([$competicion->id]);

// 6. Create upcoming matches for Carlos' team
$partido1 = Partido::firstOrCreate(
    [
        'id_competicion' => $competicion->id,
        'id_local' => $equipo->id,
        'id_visitante' => $oponente->id,
        'jornada' => 1,
    ],
    [
        'fecha_hora' => Carbon::now()->addDays(2),
        'estado' => 'pendiente',
        'campo_pista' => 'Campo Central'
    ]
);
// Ensure date is in the future
$partido1->fecha_hora = Carbon::now()->addDays(2);
$partido1->estado = 'pendiente';
$partido1->campo_pista = 'Campo Central';
$partido1->save();

$partido2 = Partido::firstOrCreate(
    [
        'id_competicion' => $competicion->id,
        'id_local' => $oponente->id,
        'id_visitante' => $equipo->id,
        'jornada' => 2,
    ],
    [
        'fecha_hora' => Carbon::now()->addDays(9),
        'estado' => 'pendiente',
        'campo_pista' => 'Campo Anexo'
    ]
);
$partido2->fecha_hora = Carbon::now()->addDays(9);
$partido2->estado = 'pendiente';
$partido2->campo_pista = 'Campo Anexo';
$partido2->save();

echo "Partidos creados/actualizados para el equipo de Carlos\n";
echo "Hecho.\n";

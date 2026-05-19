<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuarios ──────────────────────────────────────────────────

        $password = Hash::make('1234');

        $usuarios = [
            // Admin
            ['id' => 1,  'nombre' => 'Administrador',       'email' => 'admin@admin.com',              'password' => Hash::make('admin123'), 'telefono' => '600000000', 'rol' => 'admin'],

            // Capitanes (uno por equipo)
            ['id' => 2,  'nombre' => 'Carlos García',       'email' => 'carlos@leaguemanager.com',     'password' => $password, 'telefono' => '601000001', 'rol' => 'capitan'],
            ['id' => 3,  'nombre' => 'Miguel Torres',       'email' => 'miguel@leaguemanager.com',     'password' => $password, 'telefono' => '601000002', 'rol' => 'capitan'],
            ['id' => 4,  'nombre' => 'Pablo Ruiz',          'email' => 'pablo@leaguemanager.com',      'password' => $password, 'telefono' => '601000003', 'rol' => 'capitan'],
            ['id' => 5,  'nombre' => 'Andrés López',        'email' => 'andres@leaguemanager.com',     'password' => $password, 'telefono' => '601000004', 'rol' => 'capitan'],
            ['id' => 6,  'nombre' => 'David Fernández',     'email' => 'david@leaguemanager.com',      'password' => $password, 'telefono' => '601000005', 'rol' => 'capitan'],
            ['id' => 7,  'nombre' => 'Jorge Martín',        'email' => 'jorge@leaguemanager.com',      'password' => $password, 'telefono' => '601000006', 'rol' => 'capitan'],

            // Árbitros
            ['id' => 8,  'nombre' => 'Sergio Navarro',      'email' => 'sergio@leaguemanager.com',     'password' => $password, 'telefono' => '602000001', 'rol' => 'arbitro'],
            ['id' => 9,  'nombre' => 'Raúl Jiménez',        'email' => 'raul@leaguemanager.com',       'password' => $password, 'telefono' => '602000002', 'rol' => 'arbitro'],
            ['id' => 10, 'nombre' => 'Fernando Vega',       'email' => 'fernando@leaguemanager.com',   'password' => $password, 'telefono' => '602000003', 'rol' => 'arbitro'],
            ['id' => 11, 'nombre' => 'Alberto Campos',      'email' => 'alberto@leaguemanager.com',    'password' => $password, 'telefono' => '602000004', 'rol' => 'arbitro'],

            // Jugadores — Tigres FC (equipo 1)
            ['id' => 12, 'nombre' => 'Iván Sánchez',        'email' => 'ivan@leaguemanager.com',       'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 13, 'nombre' => 'Mario Díaz',          'email' => 'mario@leaguemanager.com',      'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 14, 'nombre' => 'Álex Moreno',         'email' => 'alex@leaguemanager.com',       'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 15, 'nombre' => 'Daniel Romero',       'email' => 'daniel@leaguemanager.com',     'password' => $password, 'telefono' => null, 'rol' => 'jugador'],

            // Jugadores — Halcones United (equipo 2)
            ['id' => 16, 'nombre' => 'Hugo Alonso',         'email' => 'hugo@leaguemanager.com',       'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 17, 'nombre' => 'Óscar Peña',          'email' => 'oscar@leaguemanager.com',      'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 18, 'nombre' => 'Rubén Castro',        'email' => 'ruben@leaguemanager.com',      'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 19, 'nombre' => 'Adrián Molina',       'email' => 'adrian@leaguemanager.com',     'password' => $password, 'telefono' => null, 'rol' => 'jugador'],

            // Jugadores — Lobos Azules (equipo 3)
            ['id' => 20, 'nombre' => 'Marcos Serrano',      'email' => 'marcos@leaguemanager.com',     'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 21, 'nombre' => 'Javier Ortega',       'email' => 'javier@leaguemanager.com',     'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 22, 'nombre' => 'Lucas Herrera',       'email' => 'lucas@leaguemanager.com',      'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 23, 'nombre' => 'Diego Ramos',         'email' => 'diego@leaguemanager.com',      'password' => $password, 'telefono' => null, 'rol' => 'jugador'],

            // Jugadores — Dragones Rojos (equipo 4)
            ['id' => 24, 'nombre' => 'Samuel Gil',          'email' => 'samuel@leaguemanager.com',     'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 25, 'nombre' => 'Víctor Blanco',       'email' => 'victor@leaguemanager.com',     'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 26, 'nombre' => 'Raúl Guerrero',       'email' => 'raulguerrero@leaguemanager.com', 'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 27, 'nombre' => 'Manuel Crespo',       'email' => 'manuel@leaguemanager.com',     'password' => $password, 'telefono' => null, 'rol' => 'jugador'],

            // Jugadores — Águilas Doradas (equipo 5)
            ['id' => 28, 'nombre' => 'Pedro Nieto',         'email' => 'pedro@leaguemanager.com',      'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 29, 'nombre' => 'Tomás Ibáñez',        'email' => 'tomas@leaguemanager.com',      'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 30, 'nombre' => 'Nicolás Pardo',       'email' => 'nicolas@leaguemanager.com',    'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 31, 'nombre' => 'Gabriel Reyes',       'email' => 'gabriel@leaguemanager.com',    'password' => $password, 'telefono' => null, 'rol' => 'jugador'],

            // Jugadores — Panteras Negras (equipo 6)
            ['id' => 32, 'nombre' => 'Enrique Soto',        'email' => 'enrique@leaguemanager.com',    'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 33, 'nombre' => 'Felipe Vargas',       'email' => 'felipe@leaguemanager.com',     'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 34, 'nombre' => 'Gonzalo Medina',      'email' => 'gonzalo@leaguemanager.com',    'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 35, 'nombre' => 'Emilio Delgado',      'email' => 'emilio@leaguemanager.com',     'password' => $password, 'telefono' => null, 'rol' => 'jugador'],
        ];
        DB::table('usuarios')->insert($usuarios);

        // ── Competición ───────────────────────────────────────────────

        DB::table('competiciones')->insert([
            'id'               => 1,
            'nombre'           => 'Liga Primavera 2026',
            'deporte'          => 'Fútbol Sala',
            'estado'           => 'pendiente',
            'puntos_victoria'  => 3,
            'puntos_empate'    => 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // ── Equipos ───────────────────────────────────────────────────

        $equipos = [
            ['id' => 1, 'nombre' => 'Tigres FC',        'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Halcones United',  'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Lobos Azules',     'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'Dragones Rojos',   'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nombre' => 'Águilas Doradas',  'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nombre' => 'Panteras Negras',  'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('equipos')->insert($equipos);

        // ── Inscripción de equipos en la competición ──────────────────

        $inscripciones = [];
        for ($i = 1; $i <= 6; $i++) {
            $inscripciones[] = ['id_competicion' => 1, 'id_equipo' => $i, 'created_at' => now(), 'updated_at' => now()];
        }
        DB::table('competicion_equipo')->insert($inscripciones);

        // ── Plantilla jugadores (5 por equipo: capitán + 4 jugadores) ─

        // Mapeo: equipo_id => [capitan_id, jugador1, jugador2, jugador3, jugador4]
        $equiposJugadores = [
            1 => ['capitan' => 2,  'jugadores' => [12, 13, 14, 15]],
            2 => ['capitan' => 3,  'jugadores' => [16, 17, 18, 19]],
            3 => ['capitan' => 4,  'jugadores' => [20, 21, 22, 23]],
            4 => ['capitan' => 5,  'jugadores' => [24, 25, 26, 27]],
            5 => ['capitan' => 6,  'jugadores' => [28, 29, 30, 31]],
            6 => ['capitan' => 7,  'jugadores' => [32, 33, 34, 35]],
        ];

        $plantilla = [];
        $dorsal = 1;

        foreach ($equiposJugadores as $equipoId => $miembros) {
            // Capitán
            $plantilla[] = [
                'id_equipo'   => $equipoId,
                'id_usuario'  => $miembros['capitan'],
                'dorsal'      => $dorsal++,
                'estado'      => 'activo',
                'es_capitan'  => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            // Jugadores
            foreach ($miembros['jugadores'] as $jugadorId) {
                $plantilla[] = [
                    'id_equipo'   => $equipoId,
                    'id_usuario'  => $jugadorId,
                    'dorsal'      => $dorsal++,
                    'estado'      => 'activo',
                    'es_capitan'  => false,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        DB::table('plantilla_jugadores')->insert($plantilla);
    }
}
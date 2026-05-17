<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuarios ──────────────────────────────────────────────
        $usuarios = [
            ['id' => 1,  'nombre' => 'Administrador',   'email' => 'admin@admin.com',    'password' => Hash::make('admin123'), 'telefono' => '600111111', 'rol' => 'admin'],
            ['id' => 2,  'nombre' => 'Carlos García',     'email' => 'carlos@leaguemanager.com',   'password' => Hash::make('1234'), 'telefono' => '600222222', 'rol' => 'capitan'],
            ['id' => 3,  'nombre' => 'Miguel Torres',     'email' => 'miguel@leaguemanager.com',   'password' => Hash::make('1234'), 'telefono' => '600333333', 'rol' => 'capitan'],
            ['id' => 4,  'nombre' => 'Pablo Ruiz',        'email' => 'pablo@leaguemanager.com',    'password' => Hash::make('1234'), 'telefono' => '600444444', 'rol' => 'capitan'],
            ['id' => 5,  'nombre' => 'Andrés López',      'email' => 'andres@leaguemanager.com',   'password' => Hash::make('1234'), 'telefono' => '600555555', 'rol' => 'capitan'],
            ['id' => 6,  'nombre' => 'David Fernández',   'email' => 'david@leaguemanager.com',    'password' => Hash::make('1234'), 'telefono' => '600666666', 'rol' => 'capitan'],
            ['id' => 7,  'nombre' => 'Jorge Martín',      'email' => 'jorge@leaguemanager.com',    'password' => Hash::make('1234'), 'telefono' => '600777777', 'rol' => 'capitan'],
            ['id' => 8,  'nombre' => 'Sergio Navarro',    'email' => 'sergio@leaguemanager.com',   'password' => Hash::make('1234'), 'telefono' => '600888888', 'rol' => 'arbitro'],
            ['id' => 9,  'nombre' => 'Raúl Jiménez',      'email' => 'raul@leaguemanager.com',     'password' => Hash::make('1234'), 'telefono' => '600999999', 'rol' => 'arbitro'],
            // Jugadores
            ['id' => 10, 'nombre' => 'Usuario Normal',    'email' => 'user@user.com',              'password' => Hash::make('user123'), 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 11, 'nombre' => 'Iván Sánchez',      'email' => 'ivan@leaguemanager.com',     'password' => Hash::make('1234'), 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 12, 'nombre' => 'Mario Díaz',        'email' => 'mario@leaguemanager.com',    'password' => Hash::make('1234'), 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 13, 'nombre' => 'Álex Moreno',       'email' => 'alex@leaguemanager.com',     'password' => Hash::make('1234'), 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 14, 'nombre' => 'Daniel Romero',     'email' => 'daniel@leaguemanager.com',   'password' => Hash::make('1234'), 'telefono' => null, 'rol' => 'jugador'],
            ['id' => 15, 'nombre' => 'Hugo Alonso',       'email' => 'hugo@leaguemanager.com',     'password' => Hash::make('1234'), 'telefono' => null, 'rol' => 'jugador'],
        ];
        DB::table('usuarios')->insert($usuarios);

        // ── Competición ───────────────────────────────────────────
        DB::table('competiciones')->insert([
            ['id' => 1, 'nombre' => 'Liga Primavera 2026',   'deporte' => 'Fútbol Sala', 'estado' => 'en_curso', 'puntos_victoria' => 3, 'puntos_empate' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Copa Verano 2026',      'deporte' => 'Fútbol 7',    'estado' => 'pendiente', 'puntos_victoria' => 3, 'puntos_empate' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Equipos ───────────────────────────────────────────────
        $equipos = [
            ['id' => 1, 'nombre' => 'Tigres FC',        'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Halcones United',  'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Lobos Azules',     'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'Dragones Rojos',   'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nombre' => 'Águilas Doradas',  'logo_url' => 'default.png', 'puntos_sancion' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nombre' => 'Panteras Negras',  'logo_url' => 'default.png', 'puntos_sancion' => 0, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('equipos')->insert($equipos);

        // ── Plantilla jugadores ───────────────────────────────────
        $plantilla = [
            // Tigres FC
            ['id_equipo' => 1, 'id_usuario' => 2,  'dorsal' => 10, 'estado' => 'activo', 'es_capitan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_equipo' => 1, 'id_usuario' => 10, 'dorsal' => 7,  'estado' => 'activo', 'es_capitan' => false, 'created_at' => now(), 'updated_at' => now()],
            // Halcones United
            ['id_equipo' => 2, 'id_usuario' => 3,  'dorsal' => 9,  'estado' => 'activo', 'es_capitan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_equipo' => 2, 'id_usuario' => 11, 'dorsal' => 5,  'estado' => 'activo', 'es_capitan' => false, 'created_at' => now(), 'updated_at' => now()],
            // Lobos Azules
            ['id_equipo' => 3, 'id_usuario' => 4,  'dorsal' => 8,  'estado' => 'activo', 'es_capitan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_equipo' => 3, 'id_usuario' => 12, 'dorsal' => 3,  'estado' => 'activo', 'es_capitan' => false, 'created_at' => now(), 'updated_at' => now()],
            // Dragones Rojos
            ['id_equipo' => 4, 'id_usuario' => 5,  'dorsal' => 11, 'estado' => 'activo', 'es_capitan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_equipo' => 4, 'id_usuario' => 13, 'dorsal' => 4,  'estado' => 'activo', 'es_capitan' => false, 'created_at' => now(), 'updated_at' => now()],
            // Águilas Doradas
            ['id_equipo' => 5, 'id_usuario' => 6,  'dorsal' => 1,  'estado' => 'activo', 'es_capitan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_equipo' => 5, 'id_usuario' => 14, 'dorsal' => 6,  'estado' => 'activo', 'es_capitan' => false, 'created_at' => now(), 'updated_at' => now()],
            // Panteras Negras
            ['id_equipo' => 6, 'id_usuario' => 7,  'dorsal' => 2,  'estado' => 'activo', 'es_capitan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_equipo' => 6, 'id_usuario' => 15, 'dorsal' => 14, 'estado' => 'activo', 'es_capitan' => false, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('plantilla_jugadores')->insert($plantilla);

        // ── Partidos (Jornadas 1-5 de la Liga Primavera) ──────────
        $baseDate = Carbon::create(2026, 3, 1, 18, 0);
        $partidos = [
            // Jornada 1
            ['id' => 1,  'id_competicion' => 1, 'id_local' => 1, 'id_visitante' => 2, 'id_arbitro' => 8, 'jornada' => 1, 'fecha_hora' => $baseDate->copy(),                    'campo_pista' => 'Pista Central',  'goles_local' => 3, 'goles_visitante' => 1, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2,  'id_competicion' => 1, 'id_local' => 3, 'id_visitante' => 4, 'id_arbitro' => 9, 'jornada' => 1, 'fecha_hora' => $baseDate->copy()->addHours(2),         'campo_pista' => 'Pista Norte',    'goles_local' => 2, 'goles_visitante' => 2, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3,  'id_competicion' => 1, 'id_local' => 5, 'id_visitante' => 6, 'id_arbitro' => 8, 'jornada' => 1, 'fecha_hora' => $baseDate->copy()->addHours(4),         'campo_pista' => 'Pista Sur',      'goles_local' => 0, 'goles_visitante' => 1, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            // Jornada 2
            ['id' => 4,  'id_competicion' => 1, 'id_local' => 2, 'id_visitante' => 3, 'id_arbitro' => 9, 'jornada' => 2, 'fecha_hora' => $baseDate->copy()->addWeek(),           'campo_pista' => 'Pista Central',  'goles_local' => 1, 'goles_visitante' => 0, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5,  'id_competicion' => 1, 'id_local' => 4, 'id_visitante' => 5, 'id_arbitro' => 8, 'jornada' => 2, 'fecha_hora' => $baseDate->copy()->addWeek()->addHours(2), 'campo_pista' => 'Pista Norte', 'goles_local' => 4, 'goles_visitante' => 0, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6,  'id_competicion' => 1, 'id_local' => 6, 'id_visitante' => 1, 'id_arbitro' => 9, 'jornada' => 2, 'fecha_hora' => $baseDate->copy()->addWeek()->addHours(4), 'campo_pista' => 'Pista Sur',   'goles_local' => 1, 'goles_visitante' => 2, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            // Jornada 3
            ['id' => 7,  'id_competicion' => 1, 'id_local' => 1, 'id_visitante' => 3, 'id_arbitro' => 8, 'jornada' => 3, 'fecha_hora' => $baseDate->copy()->addWeeks(2),         'campo_pista' => 'Pista Central',  'goles_local' => 2, 'goles_visitante' => 1, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8,  'id_competicion' => 1, 'id_local' => 5, 'id_visitante' => 2, 'id_arbitro' => 9, 'jornada' => 3, 'fecha_hora' => $baseDate->copy()->addWeeks(2)->addHours(2), 'campo_pista' => 'Pista Norte', 'goles_local' => 1, 'goles_visitante' => 3, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9,  'id_competicion' => 1, 'id_local' => 4, 'id_visitante' => 6, 'id_arbitro' => 8, 'jornada' => 3, 'fecha_hora' => $baseDate->copy()->addWeeks(2)->addHours(4), 'campo_pista' => 'Pista Sur',  'goles_local' => 2, 'goles_visitante' => 0, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            // Jornada 4
            ['id' => 10, 'id_competicion' => 1, 'id_local' => 3, 'id_visitante' => 5, 'id_arbitro' => 9, 'jornada' => 4, 'fecha_hora' => $baseDate->copy()->addWeeks(3),         'campo_pista' => 'Pista Central',  'goles_local' => 3, 'goles_visitante' => 0, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'id_competicion' => 1, 'id_local' => 6, 'id_visitante' => 4, 'id_arbitro' => 8, 'jornada' => 4, 'fecha_hora' => $baseDate->copy()->addWeeks(3)->addHours(2), 'campo_pista' => 'Pista Norte', 'goles_local' => 1, 'goles_visitante' => 1, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'id_competicion' => 1, 'id_local' => 2, 'id_visitante' => 1, 'id_arbitro' => 9, 'jornada' => 4, 'fecha_hora' => $baseDate->copy()->addWeeks(3)->addHours(4), 'campo_pista' => 'Pista Sur',  'goles_local' => 0, 'goles_visitante' => 1, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            // Jornada 5
            ['id' => 13, 'id_competicion' => 1, 'id_local' => 1, 'id_visitante' => 4, 'id_arbitro' => 8, 'jornada' => 5, 'fecha_hora' => $baseDate->copy()->addWeeks(4),         'campo_pista' => 'Pista Central',  'goles_local' => 2, 'goles_visitante' => 1, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'id_competicion' => 1, 'id_local' => 3, 'id_visitante' => 6, 'id_arbitro' => 9, 'jornada' => 5, 'fecha_hora' => $baseDate->copy()->addWeeks(4)->addHours(2), 'campo_pista' => 'Pista Norte', 'goles_local' => 0, 'goles_visitante' => 2, 'url_foto_acta' => null, 'estado' => 'jugado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'id_competicion' => 1, 'id_local' => 5, 'id_visitante' => 6, 'id_arbitro' => 8, 'jornada' => 5, 'fecha_hora' => $baseDate->copy()->addWeeks(5),         'campo_pista' => 'Pista Sur',      'goles_local' => null, 'goles_visitante' => null, 'url_foto_acta' => null, 'estado' => 'pendiente', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('partidos')->insert($partidos);

        // ── Eventos de partido (goles y tarjetas) ─────────────────
        $eventos = [
            // Jornada 1: Tigres 3-1 Halcones
            ['id_jugador' => 2,  'id_partido' => 1, 'tipo_evento' => 'Gol',      'minuto' => 12, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 10, 'id_partido' => 1, 'tipo_evento' => 'Gol',      'minuto' => 34, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 2,  'id_partido' => 1, 'tipo_evento' => 'Gol',      'minuto' => 55, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 3,  'id_partido' => 1, 'tipo_evento' => 'Gol',      'minuto' => 70, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 11, 'id_partido' => 1, 'tipo_evento' => 'Amarilla', 'minuto' => 40, 'observaciones' => 'Juego brusco', 'created_at' => now(), 'updated_at' => now()],
            // Jornada 1: Lobos 2-2 Dragones
            ['id_jugador' => 4,  'id_partido' => 2, 'tipo_evento' => 'Gol',      'minuto' => 22, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 12, 'id_partido' => 2, 'tipo_evento' => 'Gol',      'minuto' => 60, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 5,  'id_partido' => 2, 'tipo_evento' => 'Gol',      'minuto' => 15, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 13, 'id_partido' => 2, 'tipo_evento' => 'Gol',      'minuto' => 78, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            // Jornada 1: Águilas 0-1 Panteras
            ['id_jugador' => 7,  'id_partido' => 3, 'tipo_evento' => 'Gol',      'minuto' => 88, 'observaciones' => 'Gol en el último minuto', 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 6,  'id_partido' => 3, 'tipo_evento' => 'Roja',     'minuto' => 75, 'observaciones' => 'Doble amarilla', 'created_at' => now(), 'updated_at' => now()],
            // Jornada 3: Tigres 2-1 Lobos
            ['id_jugador' => 2,  'id_partido' => 7, 'tipo_evento' => 'Gol',      'minuto' => 10, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 10, 'id_partido' => 7, 'tipo_evento' => 'Gol',      'minuto' => 50, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 4,  'id_partido' => 7, 'tipo_evento' => 'Gol',      'minuto' => 85, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            // Jornada 5: Tigres 2-1 Dragones
            ['id_jugador' => 2,  'id_partido' => 13, 'tipo_evento' => 'Gol',     'minuto' => 30, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 10, 'id_partido' => 13, 'tipo_evento' => 'Gol',     'minuto' => 67, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id_jugador' => 5,  'id_partido' => 13, 'tipo_evento' => 'Gol',     'minuto' => 80, 'observaciones' => null, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('eventos_partido')->insert($eventos);

        // ── Sanciones ─────────────────────────────────────────────
        DB::table('sanciones')->insert([
            ['id_usuario' => 6, 'id_partido_origen' => 3, 'partidos_suspension' => 1, 'motivo' => 'Expulsión por doble amarilla', 'estado' => 'cumplida', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
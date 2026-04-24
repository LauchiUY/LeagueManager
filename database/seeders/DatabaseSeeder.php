<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            'id' => 1,
            'nombre' => 'prueba',
            'email' => 'prueba@prueba.com',
            'password' => Hash::make('1234'),
            'rol' => 'capitan'
        ]);
    }
}
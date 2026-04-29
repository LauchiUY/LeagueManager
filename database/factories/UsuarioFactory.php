<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'telefono' => fake()->phoneNumber(),
            'rol' => 'jugador',
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'admin',
        ]);
    }

    public function arbitro(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'arbitro',
        ]);
    }

    public function capitan(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'capitan',
        ]);
    }

    public function jugador(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'jugador',
        ]);
    }
}

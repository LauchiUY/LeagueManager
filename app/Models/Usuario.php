<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Database\Factories\UsuarioFactory;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'email', 'password', 'telefono', 'rol'];
    
    protected $hidden = ['password', 'remember_token'];

    protected static function newFactory()
    {
        return UsuarioFactory::new();
    }

    public function plantillas() {
        return $this->hasMany(PlantillaJugador::class, 'id_usuario');
    }

    public function eventos() {
        return $this->hasMany(EventoPartido::class, 'id_jugador');
    }

    public function sanciones() {
        return $this->hasMany(Sancion::class, 'id_usuario');
    }

    public function partidosArbitrados() {
        return $this->hasMany(Partido::class, 'id_arbitro');
    }
}

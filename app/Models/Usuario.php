<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['nombre', 'email', 'password', 'telefono', 'rol'];

    protected $hidden = ['password'];

    public function equipoCapitan()
    {
        return $this->hasOne(Equipo::class, 'id_capitan');
    }

    public function plantillas()
    {
        return $this->hasMany(PlantillaJugador::class, 'id_usuario');
    }

    public function eventos()
    {
        return $this->hasMany(EventoPartido::class, 'id_jugador');
    }

    public function sanciones()
    {
        return $this->hasMany(Sancion::class, 'id_usuario');
    }
}

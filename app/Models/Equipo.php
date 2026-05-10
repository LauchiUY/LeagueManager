<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'logo_url', 'puntos_sancion'];



    public function partidosLocal()
    {
        return $this->hasMany(Partido::class, 'id_local');
    }

    public function partidosVisitante()
    {
        return $this->hasMany(Partido::class, 'id_visitante');
    }

    public function plantilla()
    {
        return $this->hasMany(PlantillaJugador::class, 'id_equipo');
    }
}

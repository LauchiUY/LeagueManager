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

    public function competiciones()
    {
        return $this->belongsToMany(Competicion::class, 'competicion_equipo', 'id_equipo', 'id_competicion')->withTimestamps();
    }

    /**
     * Obtiene el capitán del equipo a través de la plantilla.
     */
    public function getCapitanAttribute()
    {
        $registro = $this->plantilla->firstWhere('es_capitan', true);
        return $registro ? $registro->usuario : null;
    }
}

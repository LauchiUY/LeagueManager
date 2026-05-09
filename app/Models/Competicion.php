<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competicion extends Model
{
    use HasFactory;

    protected $table = 'competiciones';

    protected $fillable = ['nombre', 'deporte', 'estado', 'puntos_victoria', 'puntos_empate'];

    public function partidos()
    {
        return $this->hasMany(Partido::class, 'id_competicion');
    }

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'competicion_equipo', 'id_competicion', 'id_equipo')->withTimestamps();
    }
}

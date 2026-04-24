<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_competicion', 'id_local', 'id_visitante', 'id_arbitro',
        'jornada', 'fecha_hora', 'campo_pista',
        'goles_local', 'goles_visitante', 'url_foto_acta', 'estado'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function competicion()
    {
        return $this->belongsTo(Competicion::class, 'id_competicion');
    }

    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'id_local');
    }

    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'id_visitante');
    }

    public function arbitro()
    {
        return $this->belongsTo(Usuario::class, 'id_arbitro');
    }

    public function eventos()
    {
        return $this->hasMany(EventoPartido::class, 'id_partido');
    }
}

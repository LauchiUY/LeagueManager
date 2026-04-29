<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Competicion;
use App\Models\Equipo;
use App\Models\Usuario;
use App\Models\EventoPartido;
use App\Models\Sancion;

class Partido extends Model
{
    use HasFactory;

    protected $table = 'partidos';

    protected $fillable = [
        'id_competicion',
        'id_local',
        'id_visitante',
        'id_arbitro',
        'jornada',
        'fecha_hora',
        'campo_pista',
        'goles_local',
        'goles_visitante',
        'url_foto_acta',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'goles_local' => 'integer',
            'goles_visitante' => 'integer',
        ];
    }

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

    public function eventoPartido()
    {
        return $this->hasMany(EventoPartido::class, 'id_partido');
    }

    public function sanciones()
    {
        return $this->hasMany(Sancion::class, 'id_partido_origen');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('estado', 'finalizado');
    }

    public function scopeProgramados($query)
    {
        return $query->where('estado', 'programado');
    }

    public function esLocal(int $idEquipo): bool
    {
        return $this->id_local === $idEquipo;
    }

    public function ganador(): ?Equipo
    {
        if ($this->estado !== 'finalizado') {
            return null;
        }

        if ($this->goles_local > $this->goles_visitante) {
            return $this->equipoLocal;
        }

        if ($this->goles_visitante > $this->goles_local) {
            return $this->equipoVisitante;
        }

        return null; // En caso de empate
    }
}

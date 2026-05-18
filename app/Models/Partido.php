<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Competicion;
use App\Models\Equipo;
use App\Models\Usuario;
use App\Models\EventoPartido;
use App\Models\Sancion;
use App\Models\PlantillaJugador;

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
        return $query->where('estado', 'jugado');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function esLocal(int $idEquipo): bool
    {
        return $this->id_local === $idEquipo;
    }

    public function ganador(): ?Equipo
    {
        if ($this->estado !== 'jugado') {
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

    /**
     * Comprueba si el resultado oficial difiere del resultado calculado
     * a partir de los eventos (Goles/Autogoles) en la cancha.
     * Esto ocurre cuando el sistema aplica una sanción automática de 0-3.
     */
    public function tieneResolucionAdministrativa(): bool
    {
        if ($this->estado !== 'jugado') {
            return false;
        }

        $jugadoresLocalIds = PlantillaJugador::where('id_equipo', $this->id_local)
            ->pluck('id_usuario');
        $jugadoresVisitanteIds = PlantillaJugador::where('id_equipo', $this->id_visitante)
            ->pluck('id_usuario');

        $golesLocalCalc = $this->eventoPartido()->where('tipo_evento', 'Gol')->whereIn('id_jugador', $jugadoresLocalIds)->count()
            + $this->eventoPartido()->where('tipo_evento', 'Autogol')->whereIn('id_jugador', $jugadoresVisitanteIds)->count();

        $golesVisitanteCalc = $this->eventoPartido()->where('tipo_evento', 'Gol')->whereIn('id_jugador', $jugadoresVisitanteIds)->count()
            + $this->eventoPartido()->where('tipo_evento', 'Autogol')->whereIn('id_jugador', $jugadoresLocalIds)->count();

        return ($this->goles_local !== $golesLocalCalc || $this->goles_visitante !== $golesVisitanteCalc);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoPartido extends Model
{
    use HasFactory;

    protected $table = 'eventos_partido';

    protected $fillable = ['id_jugador', 'id_partido', 'tipo_evento', 'minuto', 'observaciones'];

    public function jugador()
    {
        return $this->belongsTo(Usuario::class, 'id_jugador');
    }

    public function partido()
    {
        return $this->belongsTo(Partido::class, 'id_partido');
    }
}

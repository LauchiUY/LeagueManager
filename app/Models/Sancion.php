<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sancion extends Model
{
    use HasFactory;

    protected $table = 'sanciones';

    protected $fillable = ['id_usuario', 'id_partido_origen', 'partidos_suspension', 'motivo', 'estado'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function partidoOrigen()
    {
        return $this->belongsTo(Partido::class, 'id_partido_origen');
    }
}

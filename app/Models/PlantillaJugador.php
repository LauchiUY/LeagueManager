<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaJugador extends Model
{
    use HasFactory;

    protected $table = 'plantilla_jugadores';

    protected $fillable = ['id_equipo', 'id_usuario', 'dorsal', 'estado'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'id_equipo');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}

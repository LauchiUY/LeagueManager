<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aplazamiento extends Model
{
    use HasFactory;

    protected $table = 'aplazamientos';

    protected $fillable = ['id_partido', 'id_solicitante', 'motivo', 'estado', 'fecha_limite'];

    public function partido()
    {
        return $this->belongsTo(Partido::class, 'id_partido');
    }

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'id_solicitante');
    }
}

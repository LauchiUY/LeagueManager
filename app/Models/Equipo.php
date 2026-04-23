<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    // Con esto le decimos a Laravel qué columnas de la base de datos de tu amigo podemos rellenar
    protected $fillable = ['nombre', 'logo_url', 'id_capitan', 'puntos_sancion']; 
}
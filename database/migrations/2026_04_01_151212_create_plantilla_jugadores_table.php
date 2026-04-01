<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::create('plantilla_jugadores', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_equipo')->constrained('equipos')->onDelete('cascade');
        $table->foreignId('id_usuario')->constrained('usuarios')->onDelete('cascade');
        $table->integer('dorsal')->nullable();
        $table->string('estado')->default('activo');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantilla_jugadores');
    }
};

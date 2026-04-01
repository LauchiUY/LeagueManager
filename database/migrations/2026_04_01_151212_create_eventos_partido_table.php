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
    Schema::create('eventos_partido', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_jugador')->constrained('usuarios');
        $table->foreignId('id_partido')->constrained('partidos')->onDelete('cascade');
        $table->string('tipo_evento'); // Gol, Amarilla, Roja
        $table->integer('minuto');
        $table->text('observaciones')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_partido');
    }
};

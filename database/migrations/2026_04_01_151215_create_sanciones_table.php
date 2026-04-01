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
    Schema::create('sanciones', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_usuario')->constrained('usuarios');
        $table->foreignId('id_partido_origen')->constrained('partidos');
        $table->integer('partidos_suspension');
        $table->string('motivo');
        $table->string('estado')->default('pendiente');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanciones');
    }
};

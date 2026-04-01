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
    Schema::create('partidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_competicion')->constrained('competiciones');
        $table->foreignId('id_local')->constrained('equipos');
        $table->foreignId('id_visitante')->constrained('equipos');
        $table->foreignId('id_arbitro')->nullable()->constrained('usuarios');
        $table->integer('jornada');
        $table->dateTime('fecha_hora');
        $table->string('campo_pista');
        $table->integer('goles_local')->nullable();
        $table->integer('goles_visitante')->nullable();
        $table->string('url_foto_acta')->nullable();
        $table->string('estado')->default('pendiente');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};

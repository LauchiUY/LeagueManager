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
        Schema::create('competicion_equipo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_competicion')->constrained('competiciones')->onDelete('cascade');
            $table->foreignId('id_equipo')->constrained('equipos')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['id_competicion', 'id_equipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competicion_equipo');
    }
};

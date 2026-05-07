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
        Schema::create('asistencia_faenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fecha_faena_id')->constrained('fechas_faenas')->cascadeOnDelete();
            $table->foreignId('comunero_id')->constrained('comuneros');
            $table->enum('estado_asistencia', ['Asistio', 'Falto', 'Justificado'])->default('Falto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_faenas');
    }
};

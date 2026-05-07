<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciudadano_cargo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestion_id')->constrained('gestiones');
            $table->foreignId('cargo_id')->constrained('cargos');
            $table->foreignId('ciudadano_id')->constrained('ciudadanos');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('estado_asignacion', ['Vigente', 'Cesado', 'Renuncia'])->default('Vigente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciudadano_cargo');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_lote', 20);
            $table->string('manzana', 10)->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->text('referencia_ubicacion')->nullable();
            $table->enum('estado', ['Habitado', 'Desocupado', 'En Litigio'])->default('Habitado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};

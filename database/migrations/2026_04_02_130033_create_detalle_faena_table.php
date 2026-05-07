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
        Schema::create('fechas_faenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faena_id')->constrained('faenas')->cascadeOnDelete();
            $table->date('fecha_realizacion');
            $table->unique(['faena_id', 'fecha_realizacion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fechas_faenas');
    }
};

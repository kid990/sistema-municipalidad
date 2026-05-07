<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('familias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_familia', 100);
            $table->foreignId('lote_id')->nullable()->constrained('lotes');
            $table->foreignId('jefe_familia_id')->constrained('ciudadanos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('familias');
    }
};
